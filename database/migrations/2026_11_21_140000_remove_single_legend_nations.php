<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Retire onze nations qui n'avaient qu'un seul joueur peu connu (légendes `world_legends`) : le joueur et la nation
 * du catalogue. Les parties existantes gardent leurs copies (`game_players.base_player_id` est remis à null,
 * `game_teams.base_nation_id` passe à null de lui-même). Irréversible : un retour arrière ne recrée rien.
 */
return new class extends Migration
{
    /** @var array<int, string> */
    private const NATIONS = [
        'Hongrie', 'Pologne', 'Slovénie', 'Costa Rica', "Côte d'Ivoire", 'Serbie',
        'RD Congo', 'Canada', 'Pérou', 'Albanie', 'Guinée',
    ];

    public function up(): void
    {
        $playerIds = DB::table('players')->where('origin', 'world_legends')->whereIn('nationality', self::NATIONS)->pluck('id');

        // Les liens d'alchimie et les contrats de base suivent le joueur ; les copies de partie sont détachées.
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
