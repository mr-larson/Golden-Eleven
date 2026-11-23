<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Attache les portraits des joueurs d'Esperion (Ao Ashi, 23 novembre 2026) : instantané dans
 * `database/data/esperion-portraits-2026-11-23.json` (slug → chemin), crédits dans
 * `resources/data/player-illustration-credits.json`. Seuls les joueurs sans portrait sont mis à jour,
 * et seulement si le fichier existe : un portrait déjà choisi n'est jamais remplacé.
 */
return new class extends Migration
{
    public function up(): void
    {
        $portraits = json_decode(file_get_contents(database_path('data/esperion-portraits-2026-11-23.json')), true, 512, JSON_THROW_ON_ERROR);

        foreach (DB::table('players')->where('origin', 'ao_ashi')->whereNull('deleted_at')->get(['id', 'firstname', 'lastname', 'photo_path']) as $player) {
            $path = $portraits[Str::slug($player->firstname.' '.$player->lastname)] ?? null;
            if ($path && empty($player->photo_path) && is_file(public_path('storage/'.$path))) {
                DB::table('players')->where('id', $player->id)->update(['photo_path' => $path, 'updated_at' => now()]);
            }
        }
    }

    public function down(): void
    {
        // Conserver les références des portraits.
    }
};
