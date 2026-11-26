<?php

use Database\Seeders\PlayerSeeder;
use Database\Seeders\Players\CaptainTsubasa\FreePlayers;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Ajoute sept agents libres à l'univers Captain Tsubasa (Nagai, Moreno, Fuchise, Nanasaki,
 * Tsukiori, Ayane et Kotone Miyazaki). Les lignes sont composées par PlayerSeeder depuis
 * FreePlayers, comme sur une base neuve ; les joueurs déjà présents sont ignorés.
 */
return new class extends Migration
{
    private const NAMES = [
        'Mei Nagai', 'Maria Moreno', 'Aya Fuchise', 'Rinka Nanasaki',
        'Eiji Tsukiori', 'Ayane Miyazaki', 'Kotone Miyazaki',
    ];

    public function up(): void
    {
        // Base neuve (catalogue vide) : les seeders s'en chargent.
        if (! DB::table('players')->exists()) {
            return;
        }

        $seeder = new PlayerSeeder;
        foreach (FreePlayers::players() as $player) {
            if (! in_array($player[0].' '.$player[1], self::NAMES, true)) {
                continue;
            }
            $exists = DB::table('players')->where('firstname', $player[0])->where('lastname', $player[1])->exists();
            if (! $exists) {
                DB::table('players')->insert($seeder->row('captain_tsubasa', $player, [], []) + ['created_at' => now(), 'updated_at' => now()]);
            }
        }
    }

    public function down(): void
    {
        // Les joueurs peuvent être référencés par une partie existante.
    }
};
