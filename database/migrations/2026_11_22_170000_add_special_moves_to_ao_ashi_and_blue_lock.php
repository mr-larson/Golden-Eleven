<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Techniques spéciales manquantes : cinq joueurs de l'équipe A d'Esperion (Ao Ashi) et trois rivaux
 * de Blue Lock (Sae Itoshi, Noel Noa, Michael Kaiser). Instantané dans `database/data/universe-special-moves-2026-11-22.json`
 * (origine → slug « prenom-nom » → techniques). Un joueur qui a déjà des techniques n'est pas modifié.
 */
return new class extends Migration
{
    /** Écrit les techniques sur les joueurs qui n'en ont pas encore. */
    public function up(): void
    {
        $catalogue = json_decode(file_get_contents(database_path('data/universe-special-moves-2026-11-22.json')), true, 512, JSON_THROW_ON_ERROR);
        DB::transaction(function () use ($catalogue): void {
            foreach ($catalogue as $origin => $bySlug) {
                foreach (DB::table('players')->where('origin', $origin)->get(['id', 'firstname', 'lastname', 'special_moves']) as $player) {
                    $slug = Str::slug($player->firstname.' '.$player->lastname);
                    if (isset($bySlug[$slug]) && empty(json_decode($player->special_moves ?? '[]', true))) {
                        DB::table('players')->where('id', $player->id)->update([
                            'special_moves' => json_encode($bySlug[$slug], JSON_UNESCAPED_UNICODE),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        });
    }

    public function down(): void
    {
        // Les joueurs peuvent être référencés par une partie existante.
    }
};
