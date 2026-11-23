<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\Team;
use Illuminate\Database\Seeder;
use App\Enums\TeamStyle;
use App\Models\Player;
use App\Models\Traits\CalculatesWeeklyCost;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Période « World Youth » : point de départ identique au Collège (mêmes clubs, mêmes effectifs),
 * à adapter ensuite depuis la base de données (sélecteur de période).
 *
 * Additif et rejouable, comme ProClubSeeder : les clubs World Youth sont retrouvés par leur nom
 * (ids stables) et leurs contrats sont recopiés depuis le Collège à chaque exécution, donc ce seeder
 * ÉCRASE les contrats World Youth. À ne relancer que tant que la période n'a pas été retouchée à la main.
 * Doit passer APRÈS TeamSeeder et ContractSeeder.
 */
class WorldYouthSeeder extends Seeder
{
    use CalculatesWeeklyCost;

    private const PERIOD = 'world_youth';

    /** Clubs du Collège absents de la période World Youth (leurs joueurs deviennent agents libres). */
    private const REMOVED_CLUBS = ['Shimada', 'Minawi', 'Nakahara', 'Shimizu', 'Naniwa', 'Otomo', 'Shutetsu'];

    /** Transferts « Prénom Nom » => club d'arrivée (ces clubs étant supprimés, les autres joueurs sont libérés). */
    private const MOVES = [
        'Taichi Nakanishi' => 'Azumaichi',   // gardien de Naniwa
        'Koji Nishio'      => 'Nankatsu',    // Otomo
        'Masao Nakayama'   => 'Nankatsu',
        'Takeshi Kishida'  => 'Nankatsu',
        'Hanji Urabe'      => 'Nankatsu',
        'Shun Nitta'       => 'Nankatsu',
        'Shingo Takasugi'  => 'Nankatsu',    // Shutetsu (Wakabayashi reste)
        'Mamoru Izawa'     => 'Nankatsu',
        'Hajime Taki'      => 'Nankatsu',
        'Teppei Kisugi'    => 'Nankatsu',
        'Tetsuo Ishida'    => 'Azumaichi',   // Minawi
        'Kazuto Takei'     => 'Azumaichi',   // Minawi
        'Morimichi Kawakami' => 'Hirado',    // gardien de Shimizu
        'Junichi Nagasaki' => 'Hanawa',      // Shimada
    ];

    /** Joueurs sans club en World Youth (Tsubasa quitte Nankatsu). */
    private const RELEASED = ['Tsubasa Ozora'];

    /**
     * Clubs réels ajoutés à la période : nom => [description, style, philosophie, formation, capitaine, effectif].
     * L'effectif liste 11 titulaires puis 3 remplaçants en « Prénom Nom » (« Nom|Nationalité » pour lever
     * un doublon). Logos : voir la commande `logos:fetch-pro`.
     */
    public static function extraClubs(): array
    {
        return [
            'São Paulo' => ['Le club brésilien de Tsubasa, aux côtés de Pepe.', TeamStyle::TACTICAL_OFFENSIVE, TeamStyle::PHILOSOPHY_STARS, '4-3-3', 'Tsubasa Ozora', [
                ' Salinas', ' Giorgi', ' Senardo', 'Marcos Cafu', 'Ash Rodrigues',
                ' Branco', ' Rivaldo', ' Sócrates', 'Tsubasa Ozora', 'Pepe Santos', ' Romário',
                ' Jairzinho', ' Kaká', ' Casemiro',
            ]],
            'Flamengo' => ['Le club de Santana et Leo, rivaux brésiliens de Tsubasa.', TeamStyle::TACTICAL_COUNTER, TeamStyle::PHILOSOPHY_BALANCED, '4-3-3', 'Zico', [
                'Cláudio Taffarel', 'Carlos Alberto Torres', ' Alberto', ' Casa Grande', ' Dante',
                ' Zico', ' Silva', ' Dugo', 'Carlos Santana', 'Luciano Leo', ' Leônidas',
                ' Bebeto', ' Ronaldinho', ' Marcio',
            ]],
            'Hamburger SV' => ['Le club de Wakabayashi, avec Schneider et Kaltz.', TeamStyle::TACTICAL_BALANCED, TeamStyle::PHILOSOPHY_COLLECTIVE, '4-3-3', 'Genzo Wakabayashi', [
                'Genzo Wakabayashi', 'Hermann Kaltz', 'Franz Beckenbauer', 'Guido Buchwald', 'Paul Breitner',
                'Schweil Teigerbran', 'Kevin Schmidt', 'Eric Schmidt', 'Karl-Heinz Schneider', 'Uwe Seeler', 'Karl-Heinz Rummenigge',
                'Woltz Hoffmann', 'Pierre Littbarski', 'Mario Goethe',
            ]],
            'Werder Brême' => ['Le rival de Hambourg, mené par Schester et Margus.', TeamStyle::TACTICAL_OFFENSIVE, TeamStyle::PHILOSOPHY_BALANCED, '4-3-3', 'Franz Schester', [
                'Deuter Müller', 'Matthias Sammer', 'Andreas Brehme', 'Jürgen Kohler', 'Philipp Lahm',
                'Franz Schester', 'Andreas Möller', 'Thomas Hässler', 'Manfred Margus', 'Rudi Völler', 'Miroslav Klose',
                'Karl-Heinz Riedle', 'Michael Kaiser', 'Günter Netzer',
            ]],
        ];
    }

    /** Nouveaux capitaines là où l'ancien est parti : club => joueur. */
    private const CAPTAINS = ['Nankatsu' => 'Taro Misaki'];

    /** Copie les clubs et les contrats du Collège dans la période World Youth. */
    public function run(): void
    {
        $copies = [];   // id du club Collège => id du club World Youth
        foreach (Team::where('period', 'college')->get() as $club) {
            $copy = Team::withTrashed()->updateOrCreate(
                ['period' => self::PERIOD, 'name' => $club->name],
                $club->only(['description', 'budget', 'logo_path', 'tactical_style', 'management_philosophy', 'default_formation']) + ['deleted_at' => null],
            );
            $copies[$club->id] = $copy->id;
        }

        DB::table('contracts')->whereIn('team_id', array_values($copies))->delete();

        $rows = [];
        foreach (Contract::whereIn('team_id', array_keys($copies))->get() as $contract) {
            $rows[] = [
                'player_id'  => $contract->player_id,
                'team_id'    => $copies[$contract->team_id],
                'salary'     => $contract->salary,
                'start_date' => $contract->start_date,
                'end_date'   => $contract->end_date,
                'is_captain' => $contract->is_captain,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        foreach (array_chunk($rows, 200) as $chunk) {
            DB::table('contracts')->insert($chunk);
        }

        $this->adaptRosters();
        $this->addExtraClubs();
    }

    /** Crée (ou met à jour) les clubs réels de extraClubs() avec leur logo, leurs contrats et leur capitaine. */
    private function addExtraClubs(): void
    {
        $disk    = Storage::disk('public');
        $players = Player::all();
        $byName  = [];   // « Prénom Nom » (et « Prénom Nom|Nationalité ») => joueur
        foreach ($players as $p) {
            $full = "{$p->firstname} {$p->lastname}";
            $byName[$full] = $p;
            $byName["{$full}|{$p->nationality}"] = $p;
        }

        foreach (self::extraClubs() as $name => [$description, $style, $philosophy, $formation, $captain, $squad]) {
            $logo = null;
            foreach (['webp', 'png', 'jpg', 'jpeg'] as $ext) {
                if ($disk->exists($candidate = 'teams/'.Str::slug($name).'.'.$ext)) {
                    $logo = $candidate;
                    break;
                }
            }

            $team = Team::withTrashed()->updateOrCreate(['period' => self::PERIOD, 'name' => $name], [
                'description' => $description, 'budget' => 5000, 'logo_path' => $logo,
                'tactical_style' => $style, 'management_philosophy' => $philosophy,
                'default_formation' => $formation, 'deleted_at' => null,
            ]);
            DB::table('contracts')->where('team_id', $team->id)->delete();

            foreach ($squad as $key) {
                $player = $byName[$key] ?? null;
                if (! $player) {
                    $this->command?->warn("Joueur introuvable : {$key}");
                    continue;
                }
                // Un joueur n'a qu'un contrat par période : on libère l'éventuel précédent.
                $worldYouthTeams = Team::where('period', self::PERIOD)->pluck('id');
                DB::table('contracts')->where('player_id', $player->id)->whereIn('team_id', $worldYouthTeams)->delete();

                Contract::create([
                    'player_id'  => $player->id,
                    'team_id'    => $team->id,
                    'salary'     => $this->calculateWeeklyCost($player->stats ?? [], $player->position?->value),
                    'start_date' => now()->subMonths(rand(1, 12)),
                    'end_date'   => now()->addMonths(rand(1, 12)),
                    'is_captain' => trim(explode('|', $key)[0]) === $captain,
                ]);
            }
        }
    }

    /**
     * Retire les clubs supprimés (suppression logique, contrats effacés), applique les transferts,
     * libère les joueurs concernés et désigne les nouveaux capitaines.
     */
    private function adaptRosters(): void
    {
        $removedIds = Team::where('period', self::PERIOD)->whereIn('name', self::REMOVED_CLUBS)->pluck('id');
        $teamIds    = Team::where('period', self::PERIOD)->whereNotIn('id', $removedIds)->pluck('id', 'name');
        // (la copie ci-dessus recrée tous les clubs du Collège : les supprimés sont retirés ici, à chaque exécution)

        // Contrats indexés par « Prénom Nom » AVANT la purge : un joueur d'un club supprimé peut être transféré.
        $contracts = Contract::whereIn('team_id', $teamIds->values()->merge($removedIds))->with('player')->get()
            ->keyBy(fn (Contract $c) => "{$c->player->firstname} {$c->player->lastname}");

        // `->get()` : un joueur ou un club absent du catalogue est simplement ignoré.
        foreach (self::MOVES as $player => $club) {
            if ($teamIds->has($club)) {
                $contracts->get($player)?->update(['team_id' => $teamIds[$club], 'is_captain' => false]);
            }
        }
        foreach (self::RELEASED as $player) {
            $contracts->get($player)?->delete();
        }
        foreach (self::CAPTAINS as $club => $player) {
            $contracts->get($player)?->update(['is_captain' => true]);
        }

        // Les joueurs restés dans un club supprimé deviennent agents libres.
        DB::table('contracts')->whereIn('team_id', $removedIds)->delete();
        Team::whereIn('id', $removedIds)->delete();
    }
}
