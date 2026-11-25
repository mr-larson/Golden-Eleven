<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Fixe l'OVR de Takeshi Kishida à 27,5 (moyenne des 13 stats, tête comprise : total 358 → 27,54).
 * Même valeurs que la retouche de Clubs/stats/otomo.json ; la tête (18) n'est pas modifiée.
 */
return new class extends Migration
{
    public function up(): void
    {
        $row = DB::table('players')->where('firstname', 'Takeshi')->where('lastname', 'Kishida')
            ->where('origin', 'captain_tsubasa')->first();
        if (! $row) {
            return;
        }

        $stats = array_merge(json_decode($row->stats, true) ?? [], [
            'speed' => 55, 'stamina' => 70, 'attack' => 26, 'defense' => 26, 'shot' => 22, 'pass' => 25,
            'dribble' => 23, 'block' => 20, 'intercept' => 23, 'tackle' => 22, 'hand_save' => 14, 'punch_save' => 14,
        ]);
        DB::table('players')->where('id', $row->id)->update(['stats' => json_encode($stats, JSON_UNESCAPED_UNICODE), 'updated_at' => now()]);
    }

    public function down(): void
    {
        // Retouche d'équilibrage volontaire : non réversible automatiquement.
    }
};
