<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Retire l'Australie (deux légendes peu connues, Okon et Vidmar) et le Ghana (aucun joueur) du catalogue.
 * Les parties existantes gardent leurs copies (`game_players.base_player_id` remis à null). Irréversible.
 */
return new class extends Migration
{
    /** @var array<int, string> */
    private const NATIONS = ['Australie', 'Ghana'];

    public function up(): void
    {
        $playerIds = DB::table('players')->where('origin', 'world_legends')->whereIn('nationality', self::NATIONS)->pluck('id');

        DB::table('game_players')->whereIn('base_player_id', $playerIds)->update(['base_player_id' => null]);
        DB::table('contracts')->whereIn('player_id', $playerIds)->delete();
        DB::table('players')->whereIn('id', $playerIds)->delete();

        // On ne retire la nation que si plus aucun joueur ne la porte.
        foreach (self::NATIONS as $nation) {
            if (! DB::table('players')->where('nationality', $nation)->exists()) {
                DB::table('nations')->where('name', $nation)->delete();
            }
        }
    }

    public function down(): void
    {
        // Irréversible : les joueurs et nations supprimés ne sont pas recréés.
    }
};
