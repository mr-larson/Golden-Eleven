<?php

use App\Enums\TeamStyle;
use App\Models\Team;
use App\Models\Traits\CalculatesWeeklyCost;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Ajoute le club Esperion (Ao Ashi) aux périodes World Youth et Professionnel (pas au Collège, incohérent avec l'époque) et
 * met à jour les joueurs de l'univers : Yoshitsune (avant-centre capitaine) et Takasugi (ailier) changent
 * de poste, quatre joueurs sont créés (Akiyama, Yamada, Nikaido, Shimura) et les descriptions sont revues.
 * Instantané dans `database/data/esperion-roster-2026-11-23.json` ; une base neuve est peuplée par les
 * seeders (Roster, ClubRosters, ProClubs, TeamSeeder), cette migration ne vise que les bases existantes.
 * Portraits non touchés ; les ids existants ne bougent pas.
 */
return new class extends Migration
{
    /** Effectif : 11 titulaires puis 4 remplaçants. */
    private const SQUAD = [
        'Enshin Akiyama', 'Ashito Aoi', 'Nagisa Akutsu', 'Keiji Togashi', 'Yutaka Yamada',
        'Eisaku Ohtomo', 'Yoichi Kiriki', 'Haruhisa Kuribayashi',
        'Eita Takasugi', 'Kenta Yoshitsune', 'Yuma Motoki',
        'Kanji Nikaido', 'Kyo Shimura', 'Jun Asari', 'Soichiro Tachibana',
    ];

    private const CAPTAIN = 'Kenta Yoshitsune';

    public function up(): void
    {
        // Base neuve (ou sans l'univers Ao Ashi) : les seeders s'en chargent.
        if (! DB::table('players')->where('origin', 'ao_ashi')->where('lastname', 'Aoi')->exists()) {
            return;
        }

        $rows = json_decode(file_get_contents(database_path('data/esperion-roster-2026-11-23.json')), true, 512, JSON_THROW_ON_ERROR);

        DB::transaction(function () use ($rows): void {
            $this->syncPlayers($rows);
            foreach (['world_youth', 'pro'] as $period) {
                $this->syncClub($period);
            }
        });
    }

    /** Met à jour les joueurs existants (stats, poste, description, techniques) et crée les absents. */
    private function syncPlayers(array $rows): void
    {
        foreach ($rows as $row) {
            $query = DB::table('players')->where('origin', 'ao_ashi')
                ->where('firstname', $row['firstname'])->where('lastname', $row['lastname']);

            if ($query->exists()) {
                $query->update([
                    'position' => $row['position'], 'secondary_positions' => $row['secondary_positions'],
                    'stats' => $row['stats'], 'heading' => $row['heading'], 'cost' => $row['cost'],
                    'special_moves' => $row['special_moves'], 'description' => $row['description'],
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('players')->insert($row + ['created_at' => now(), 'updated_at' => now()]);
            }
        }
    }

    /** Crée (ou réactive) Esperion dans la période et y remplace les contrats de ses joueurs. */
    private function syncClub(string $period): void
    {
        $team = Team::withTrashed()->updateOrCreate(['period' => $period, 'name' => 'Esperion'], [
            'description' => 'Le club formateur d\'Ao Ashi : jeu technique, lecture tactique et jeunes talents.',
            'budget' => 5000,
            'tactical_style' => TeamStyle::TACTICAL_POSSESSION,
            'management_philosophy' => TeamStyle::PHILOSOPHY_COLLECTIVE,
            'default_formation' => '4-3-3',
            'deleted_at' => null,
        ]);

        $players = DB::table('players')->where('origin', 'ao_ashi')->whereNull('deleted_at')->get()
            ->keyBy(fn ($p) => "{$p->firstname} {$p->lastname}");
        $periodTeams = Team::withTrashed()->where('period', $period)->pluck('id');
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
        // Les joueurs et le club peuvent être référencés par une partie existante.
    }
};
