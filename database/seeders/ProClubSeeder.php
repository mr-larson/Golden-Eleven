<?php

namespace Database\Seeders;

use App\Models\Player;
use App\Models\Team;
use App\Models\Traits\CalculatesWeeklyCost;
use Database\Seeders\Players\CaptainTsubasa\Clubs\ProClubs;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Clubs et contrats de la période « Professionnel » (voir ProClubs).
 *
 * Additif et rejouable : il ne touche jamais aux clubs ni aux contrats du Collège, et ne
 * vide que les contrats des clubs pro avant de les recréer. Les clubs pro existants sont mis
 * à jour par leur nom (leur id reste stable), donc ce seeder peut tourner en production
 * sans fausser les parties en cours. Doit passer APRÈS TeamSeeder, PlayerSeeder et ContractSeeder.
 */
class ProClubSeeder extends Seeder
{
    use CalculatesWeeklyCost;

    /**
     * Crée ou met à jour les clubs pro, puis remplace leurs contrats par ceux de ProClubs.
     * Signale (sans s'arrêter) un joueur introuvable, et marque le capitaine de chaque club.
     */
    public function run(): void
    {
        $disk = Storage::disk('public');

        foreach (ProClubs::teams() as [$name, $description, $tacticalStyle, $philosophy, $formation]) {
            $logo = null;
            foreach (['webp', 'png', 'jpg', 'jpeg'] as $ext) {
                if ($disk->exists($candidate = 'teams/'.Str::slug($name).'.'.$ext)) {
                    $logo = $candidate;
                    break;
                }
            }

            Team::withTrashed()->updateOrCreate(['period' => 'pro', 'name' => $name], [
                'description'           => $description,
                'budget'                => 5000,
                'logo_path'             => $logo,
                'tactical_style'        => $tacticalStyle,
                'management_philosophy' => $philosophy,
                'default_formation'     => $formation,
                'deleted_at'            => null,
            ]);
        }

        $teamsByName       = Team::where('period', 'pro')->pluck('id', 'name');
        $playersByFullName = Player::all()->keyBy(fn (Player $p) => "{$p->firstname} {$p->lastname}");
        $captains          = ProClubs::captains();

        DB::table('contracts')->whereIn('team_id', $teamsByName->values())->delete();

        $contracts = [];
        foreach (ProClubs::players() as $teamName => $playerNames) {
            $teamId = $teamsByName[$teamName] ?? null;
            if (! $teamId) {
                $this->command?->warn("Club pro introuvable : {$teamName}");
                continue;
            }

            foreach ($playerNames as $fullName) {
                $player = $playersByFullName->get($fullName);
                if (! $player) {
                    $this->command?->warn("Joueur introuvable : {$fullName}");
                    continue;
                }

                $contracts[] = [
                    'player_id'  => $player->id,
                    'team_id'    => $teamId,
                    'salary'     => $this->calculateWeeklyCost($player->stats ?? [], $player->position?->value),
                    'start_date' => now()->subMonths(rand(1, 12))->subDays(rand(1, 30)),
                    'end_date'   => now()->addMonths(rand(1, 12))->addDays(rand(1, 30)),
                    'is_captain' => ($captains[$teamName] ?? null) === $fullName ? 1 : 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if ($contracts !== []) {
            DB::table('contracts')->insert($contracts);
        }
    }
}
