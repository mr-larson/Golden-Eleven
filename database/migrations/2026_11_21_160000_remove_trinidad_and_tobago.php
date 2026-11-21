<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Retire Trinité-et-Tobago (un seul joueur, Dwight Yorke) du catalogue des nations. Les parties existantes gardent
 * leurs copies (`game_players.base_player_id` remis à null). Irréversible.
 */
return new class extends Migration
{
    private const NATION = 'Trinité-et-Tobago';

    public function up(): void
    {
        $playerIds = DB::table('players')->where('origin', 'world_legends')->where('nationality', self::NATION)->pluck('id');

        DB::table('game_players')->whereIn('base_player_id', $playerIds)->update(['base_player_id' => null]);
        DB::table('contracts')->whereIn('player_id', $playerIds)->delete();
        DB::table('players')->whereIn('id', $playerIds)->delete();

        // On ne retire la nation que si plus aucun joueur ne la porte.
        if (! DB::table('players')->where('nationality', self::NATION)->exists()) {
            DB::table('nations')->where('name', self::NATION)->delete();
        }
    }

    public function down(): void
    {
        // Irréversible : le joueur et la nation supprimés ne sont pas recréés.
    }
};
