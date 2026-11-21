<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Complète la Yougoslavie jusqu'à onze joueurs jouables (toutes époques) : neuf légendes en plus de Savićević et Mijatović,
 * pour une sélection modeste (OVR du onze sous 26). Sans portrait pour l'instant.
 * Instantané figé dans `database/data/yugoslav-legends-2026-11-21.json`. Un joueur déjà présent n'est pas dupliqué.
 */
return new class extends Migration
{
    /** Insère les joueurs absents du catalogue. */
    public function up(): void
    {
        $players = json_decode(file_get_contents(database_path('data/yugoslav-legends-2026-11-21.json')), true, 512, JSON_THROW_ON_ERROR);
        DB::transaction(function () use ($players): void {
            foreach ($players as $row) {
                if (DB::table('players')->where('origin', $row['origin'])->where('nationality', $row['nationality'])
                    ->where('firstname', $row['firstname'])->where('lastname', $row['lastname'])->exists()) {
                    continue;
                }
                DB::table('players')->insert($row + ['created_at' => now(), 'updated_at' => now()]);
            }
        });
    }

    public function down(): void
    {
        // Les joueurs peuvent être référencés par une partie existante.
    }
};
