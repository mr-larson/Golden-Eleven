<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Complète Esperion (Ao Ashi) avec cinq joueurs de l'équipe A et retire Taira Nakamura,
     * qui est l'entraîneur de l'équipe B et non un joueur. Instantané des stats calculées par
     * PlayerSeeder : une base neuve est peuplée par le seeder (Roster à jour), cette migration
     * ne vise que les bases existantes (les ids ne bougent pas, aucun reseed).
     * Les postes de Kiriki, Takasugi et Yoshitsune sont à confirmer.
     */
    public function up(): void
    {
        // Base neuve (ou sans l'univers Ao Ashi) : le seeder s'en charge.
        if (! DB::table('players')->where('origin', 'ao_ashi')->where('lastname', 'Aoi')->exists()) {
            return;
        }

        $players = [
            ['Nagisa', 'Akutsu', 'Defender', 46, [62, 72, 36, 20, 15, 22, 16, 32, 28, 33, 10, 10], 30,
                'Défenseur central de l\'équipe A d\'Esperion. Autoritaire et solide dans les duels, il organise la ligne arrière.'],
            ['Haruhisa', 'Kuribayashi', 'Midfielder', 48, [66, 72, 26, 30, 24, 36, 30, 17, 26, 22, 10, 10], 17,
                'Meneur de jeu de l\'équipe A d\'Esperion. Technique et lucide, il dicte le tempo et distribue le jeu.'],
            ['Yoichi', 'Kiriki', 'Midfielder', 46, [66, 70, 27, 28, 22, 30, 26, 19, 26, 23, 10, 10], 18,
                'Milieu de l\'équipe A d\'Esperion, au profil complet entre récupération et construction.'],
            ['Eita', 'Takasugi', 'Defender', 44, [62, 68, 33, 20, 14, 21, 16, 28, 26, 30, 10, 10], 29,
                'Défenseur de l\'équipe A d\'Esperion, rigoureux dans le marquage et les tacles.'],
            ['Kenta', 'Yoshitsune', 'Goalkeeper', 38, [50, 62, 14, 8, 8, 20, 8, 10, 12, 8, 33, 30], 6,
                'Gardien de l\'équipe A d\'Esperion, fiable sur sa ligne et dans les sorties aériennes.'],
        ];
        $keys = ['speed', 'stamina', 'defense', 'attack', 'shot', 'pass', 'dribble', 'block', 'intercept', 'tackle', 'hand_save', 'punch_save'];

        foreach ($players as [$first, $last, $position, $cost, $values, $heading, $description]) {
            // Archives incluses : on ne ressuscite pas un joueur supprimé à la main.
            if (DB::table('players')->where('origin', 'ao_ashi')->where('firstname', $first)->where('lastname', $last)->exists()) {
                continue;
            }

            DB::table('players')->insert([
                'firstname' => $first, 'lastname' => $last, 'age' => 15,
                'position' => $position, 'origin' => 'ao_ashi', 'nationality' => 'Japon',
                'national_eligible' => true,
                'secondary_positions' => json_encode([]),
                'cost' => $cost,
                'stats' => json_encode(array_combine($keys, $values) + ['heading' => $heading]),
                'heading' => $heading,
                'description' => $description,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // Archive (suppression logique) : des parties peuvent référencer ce joueur.
        DB::table('players')->where('origin', 'ao_ashi')->where('firstname', 'Taira')->where('lastname', 'Nakamura')
            ->whereNull('deleted_at')->update(['deleted_at' => now()]);
    }

    public function down(): void
    {
        // Conserver la référence : une partie peut déjà utiliser ces joueurs.
    }
};
