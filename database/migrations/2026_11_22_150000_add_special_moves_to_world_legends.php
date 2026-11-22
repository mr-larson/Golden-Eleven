<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Attribue des techniques spéciales aux légendes réelles (origin `world_legends`), à partir de
 * l'instantané `database/data/legend-special-moves-2026-11-22.json` (slug « prenom-nom » → techniques).
 * Remplace aussi les techniques que Pepe et Shilton avaient héritées de personnages homonymes d'autres univers.
 * Les parties existantes ne sont pas modifiées : elles gardent la copie faite à leur création.
 */
return new class extends Migration
{
    /** Écrit les techniques sur chaque légende correspondante. */
    public function up(): void
    {
        $catalogue = json_decode(file_get_contents(database_path('data/legend-special-moves-2026-11-22.json')), true, 512, JSON_THROW_ON_ERROR);
        DB::transaction(function () use ($catalogue): void {
            foreach (DB::table('players')->where('origin', 'world_legends')->get(['id', 'firstname', 'lastname']) as $player) {
                $slug = Str::slug($player->firstname.' '.$player->lastname);
                if (isset($catalogue[$slug])) {
                    DB::table('players')->where('id', $player->id)->update([
                        'special_moves' => json_encode($catalogue[$slug], JSON_UNESCAPED_UNICODE),
                        'updated_at' => now(),
                    ]);
                }
            }
        });
    }

    public function down(): void
    {
        // Les joueurs peuvent être référencés par une partie existante.
    }
};
