<?php

namespace App\Services\Players;

use App\Models\GameSaves\GameContract;
use App\Models\GameSaves\GameMatch;
use App\Models\GameSaves\GamePlayer;
use App\Models\GameSaves\GameSave;
use App\Models\GameSaves\GameTeam;
use Illuminate\Support\Facades\DB;

/** Stamina des joueurs en fin de semaine : ceux qui ont joué en perdent (au prorata de leur temps de jeu), les autres en récupèrent ; les agents libres (hors Coupe Nation) récupèrent un peu aussi. Coût et récupération sont réglables dans la configuration de la partie. */
class StaminaService
{
    /** Stamina perdue par un joueur ayant joué (valeur par défaut). */
    const MATCH_STAMINA_COST    = 5;
    /** Stamina récupérée par un joueur au repos (valeur par défaut). */
    const REST_STAMINA_RECOVERY = 10;
    /** Stamina récupérée chaque semaine par un joueur sans contrat. */
    private const FREE_AGENT_RECOVERY = 3;

    /**
     * Applique stamina d'après-match pour UN match spécifique.
     * Conservé pour compatibilité — préférer applyAfterWeek().
     */
    public static function applyAfterMatch(GameSave $gameSave, ?GameMatch $match = null): void
    {
        if (!$match) return;

        self::applyStaminaChanges($gameSave, self::resolvePlaytimeShares($match));
    }

    /**
     * Applique stamina d'après-semaine en agrégeant TOUS les matchs joués
     * de la semaine donnée (source : match_stats, fallback : is_starter).
     */
    public static function applyAfterWeek(GameSave $gameSave, int $week): void
    {
        $matches = GameMatch::where('game_save_id', $gameSave->id)
            ->where('week', $week)
            ->where('status', 'played')
            ->with('playerStats')
            ->get();

        // Part de match jouée par joueur sur la semaine (plusieurs matchs : on additionne, plafonné à un match plein).
        $shares = [];
        foreach ($matches as $match) {
            foreach (self::resolvePlaytimeShares($match) as $pid => $share) {
                $shares[$pid] = min(1.0, ($shares[$pid] ?? 0.0) + $share);
            }
        }

        self::applyStaminaChanges($gameSave, $shares);

        if (! $gameSave->isTournament()) {
            $recovery = self::FREE_AGENT_RECOVERY;
            GamePlayer::where('game_save_id', $gameSave->id)
                ->excludingFictional()
                ->whereDoesntHave('contracts', fn ($q) => $q->activeAt($week))
                ->where('stamina', '<', 100)
                ->update([
                    'stamina' => DB::raw("CASE WHEN stamina + {$recovery} > 100 THEN 100 ELSE stamina + {$recovery} END"),
                ]);
        }
    }

    /**
     * Part du match jouée par chaque joueur ayant joué (0 à 1, id en chaîne), d'après match_stats.playtime :
     * un titulaire remplacé à la mi-temps vaut 0,5, comme le remplaçant qui le relaie. Sans temps de jeu
     * enregistré (anciens matchs), tout joueur ayant joué compte pour un match plein.
     *
     * @return array<string, float>
     */
    public static function resolvePlaytimeShares(GameMatch $match): array
    {
        $playtime = $match->match_stats['playtime'] ?? null;
        if (is_array($playtime) && $playtime !== []) {
            $total = max(1, (int) max($playtime));

            $shares = [];
            foreach ($playtime as $pid => $turns) {
                if ($turns > 0) {
                    $shares[(string) $pid] = min(1.0, $turns / $total);
                }
            }

            return $shares;
        }

        return array_fill_keys(self::resolvePlayedPlayerIds($match), 1.0);
    }

    /**
     * Détermine les IDs joueurs ayant joué un match.
     * Le temps de jeu distingue un joueur réellement au repos d'un titulaire
     * qui a disputé le match sans action enregistrée.
     */
    public static function resolvePlayedPlayerIds(GameMatch $match): array
    {
        if (isset($match->match_stats['playtime'])) {
            return array_map('strval', array_keys(array_filter($match->match_stats['playtime'])));
        }

        if ($players = $match->playerStatsMap()) {
            return array_map('strval', array_keys($players));
        }

        // Fallback : titulaires des deux équipes
        $teamIds = array_filter([$match->home_team_id, $match->away_team_id]);
        if (empty($teamIds)) return [];

        return \App\Models\GameSaves\GameContract::query()
            ->whereIn('game_team_id', $teamIds)
            ->where('is_starter', true)
            ->pluck('game_player_id')
            ->map(fn($id) => (string) $id)
            ->all();
    }

    /**
     * Retire le coût de match (au prorata du temps de jeu) aux joueurs ayant joué et fait
     * récupérer les autres, sur toutes les équipes de la save.
     *
     * @param array<string, float> $playShares part de match jouée par joueur (id en chaîne => 0..1)
     */
    protected static function applyStaminaChanges(GameSave $gameSave, array $playShares): void
    {
        $playedSet = $playShares;

        $matchCost = (int) $gameSave->getConfig('match_stamina_cost', self::MATCH_STAMINA_COST);
        $restRecov = (int) $gameSave->getConfig('rest_stamina_recovery', self::REST_STAMINA_RECOVERY);

        $week = $gameSave->week ?? 1;

        // Joueurs sous contrat actif dans la save (un joueur n'est compté
        // qu'une fois), puis deux UPDATE en lot au lieu d'un save() par joueur.
        // stamina est UNSIGNED : on compare avant de soustraire (pas de
        // « out of range » MySQL). Les coûts sont des entiers validés (cast int).
        $teamIds   = GameTeam::where('game_save_id', $gameSave->id)->pluck('id');
        $playerIds = GameContract::whereIn('game_team_id', $teamIds)
            ->activeAt($week)
            ->distinct()
            ->pluck('game_player_id')
            ->map(fn($id) => (int) $id);

        [$played, $rested] = $playerIds->partition(fn($id) => isset($playedSet[(string) $id]));

        if ($played->isNotEmpty() && $matchCost !== 0) {
            // Coût propre à chaque joueur (match plein = coût de la config) : un UPDATE par valeur de coût distincte.
            $byCost = $played->groupBy(fn ($id) => (int) round($matchCost * $playedSet[(string) $id]));
            foreach ($byCost as $cost => $ids) {
                if ((int) $cost === 0) {
                    continue;
                }
                GamePlayer::whereIn('id', $ids->values())->update([
                    'stamina' => DB::raw('CASE WHEN stamina < ' . (int) $cost . ' THEN 0 ELSE stamina - ' . (int) $cost . ' END'),
                ]);
            }
        }
        if ($rested->isNotEmpty() && $restRecov !== 0) {
            GamePlayer::whereIn('id', $rested->values())->update([
                'stamina' => DB::raw('CASE WHEN stamina + ' . $restRecov . ' > 100 THEN 100 ELSE stamina + ' . $restRecov . ' END'),
            ]);
        }
    }
}
