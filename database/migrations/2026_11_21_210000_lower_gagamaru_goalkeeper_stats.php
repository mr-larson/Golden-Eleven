<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Gin Gagamaru n'est pas un bon gardien : on abaisse ses stats de gardien (arrêts main/poing, blocage, interception).
 */
return new class extends Migration
{
    /** Met à jour les stats de Gagamaru s'il existe. */
    public function up(): void
    {
        $query = DB::table('players')->where('origin', 'blue_lock')
            ->where('firstname', 'Gin')->where('lastname', 'Gagamaru');
        $player = $query->first();
        if (! $player) {
            return;
        }
        $stats = json_decode($player->stats, true) ?: [];
        $stats = array_merge($stats, ['hand_save' => 21, 'punch_save' => 19, 'block' => 20, 'intercept' => 20]);
        $query->update(['stats' => json_encode($stats), 'updated_at' => now()]);
    }

    public function down(): void
    {
        // Les joueurs peuvent être référencés par une partie existante.
    }
};
