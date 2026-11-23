<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Retire Esperion de la période Collège (incohérent avec l'époque) : contrats supprimés, club archivé
 * (suppression logique). World Youth et Professionnel gardent le club ; les joueurs restent au catalogue.
 */
return new class extends Migration
{
    public function up(): void
    {
        $ids = DB::table('teams')->where('period', 'college')->where('name', 'Esperion')->whereNull('deleted_at')->pluck('id');
        DB::table('contracts')->whereIn('team_id', $ids)->delete();
        DB::table('teams')->whereIn('id', $ids)->update(['deleted_at' => now()]);
    }

    public function down(): void
    {
        // Conserver l'archive : une partie peut référencer ce club.
    }
};
