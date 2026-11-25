<?php

use App\Enums\TeamStyle;
use App\Models\Team;
use App\Models\Traits\CalculatesWeeklyCost;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Ajoute le club Nishigaoka à la période Collège (16e équipe, nombre pair pour le calendrier) :
 * crée Hikaru Tamura (joueur « original ») s'il manque, puis signe l'effectif — 11 titulaires
 * et Sakura Ikoma en remplaçante — et nomme Ayato Nakatsuji capitaine. Une base neuve est peuplée
 * par les seeders (Original/Roster, TeamSeeder, ClubRosters) ; cette migration ne vise que les bases existantes.
 */
return new class extends Migration
{
    /** Effectif : 11 titulaires puis 1 remplaçante. */
    private const SQUAD = [
        'Kenji Nakashima', 'Ayumu Fukazawa', 'Ayato Nakatsuji', 'Daichi Inafune',
        'Ao Sakuma', 'Keita Hara', 'Ayano Uchiyama', 'Ryota Kondo',
        'Kai Kaga', 'Aito Katayama', 'Hikaru Tamura',
        'Sakura Ikoma',
    ];

    private const CAPTAIN = 'Ayato Nakatsuji';

    public function up(): void
    {
        // Base neuve (ou sans les joueurs originaux) : les seeders s'en chargent.
        if (! DB::table('players')->where('origin', 'original')->exists()) {
            return;
        }

        DB::transaction(function (): void {
            $this->ensureTamura();
            $this->syncClub();
        });
    }

    /** Crée Hikaru Tamura (attaquant) s'il n'existe pas encore. */
    private function ensureTamura(): void
    {
        if (DB::table('players')->where('origin', 'original')->where('firstname', 'Hikaru')->where('lastname', 'Tamura')->exists()) {
            return;
        }

        // Même gabarit que ses coéquipiers du club (Katayama, Kaga) : on emprunte leurs colonnes.
        $template = (array) DB::table('players')->where('origin', 'original')->where('lastname', 'Katayama')->first();
        if (! $template) {
            return;
        }

        unset($template['id']);
        $stats = ['speed' => 55, 'stamina' => 54, 'defense' => 17, 'attack' => 25, 'shot' => 24, 'pass' => 18,
            'dribble' => 22, 'block' => 15, 'intercept' => 15, 'tackle' => 15, 'hand_save' => 15, 'punch_save' => 15];

        DB::table('players')->insert(array_merge($template, [
            'firstname' => 'Hikaru', 'lastname' => 'Tamura', 'cost' => 205,
            'stats' => json_encode($stats), 'photo_path' => null, 'description' => null,
            'special_moves' => json_encode([]), 'created_at' => now(), 'updated_at' => now(),
        ]));
    }

    /** Crée (ou réactive) Nishigaoka et lui attribue les contrats de son effectif. */
    private function syncClub(): void
    {
        $team = Team::withTrashed()->updateOrCreate(['period' => 'college', 'name' => 'Nishigaoka'], [
            'description' => 'Petit club de quartier à l\'effectif réduit : jeunesse, énergie et jeu en triangles.',
            'budget' => 5000,
            'tactical_style' => TeamStyle::TACTICAL_BALANCED,
            'management_philosophy' => TeamStyle::PHILOSOPHY_COLLECTIVE,
            'default_formation' => '3-4-3',
            'deleted_at' => null,
        ]);

        $players = DB::table('players')->where('origin', 'original')->whereNull('deleted_at')->get()
            ->keyBy(fn ($p) => "{$p->firstname} {$p->lastname}");
        $periodTeams = Team::withTrashed()->where('period', 'college')->pluck('id');
        $costing = new class {
            use CalculatesWeeklyCost;

            public function cost(array $stats, ?string $position): int
            {
                return $this->calculateWeeklyCost($stats, $position);
            }
        };

        DB::table('contracts')->where('team_id', $team->id)->delete();

        foreach (self::SQUAD as $fullName) {
            $player = $players->get($fullName);
            if (! $player) {
                continue;
            }

            // Un joueur n'a qu'un contrat par période : on libère l'éventuel précédent.
            DB::table('contracts')->where('player_id', $player->id)->whereIn('team_id', $periodTeams)->delete();
            DB::table('contracts')->insert([
                'player_id' => $player->id,
                'team_id' => $team->id,
                'salary' => $costing->cost(json_decode($player->stats, true) ?? [], $player->position),
                'start_date' => now()->subMonths(rand(1, 12)),
                'end_date' => now()->addMonths(rand(1, 12)),
                'is_captain' => $fullName === self::CAPTAIN,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Le club et ses joueurs peuvent être référencés par une partie existante.
    }
};
