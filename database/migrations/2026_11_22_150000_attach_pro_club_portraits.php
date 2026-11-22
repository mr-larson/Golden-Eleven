<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Attache les portraits libres (Wikimedia Commons, 22 novembre 2026) aux joueurs des clubs Pro qui n'en avaient pas.
 * Instantané figé dans `database/data/pro-club-portraits-2026-11-22.json` ; auteurs et licences dans
 * `resources/data/player-photo-credits.json`. Seuls les joueurs sans portrait sont mis à jour, et seulement
 * si le fichier existe : un portrait déjà choisi n'est jamais remplacé.
 */
return new class extends Migration
{
    /** Attache chaque portrait au joueur correspondant (par prénom et nom). */
    public function up(): void
    {
        $portraits = json_decode(file_get_contents(database_path('data/pro-club-portraits-2026-11-22.json')), true, 512, JSON_THROW_ON_ERROR);
        foreach ($portraits as $portrait) {
            if (! is_file(public_path('storage/'.$portrait['photo_path']))) {
                continue;
            }

            DB::table('players')->whereNull('deleted_at')
                ->where('firstname', $portrait['firstname'])->where('lastname', $portrait['lastname'])
                ->whereIn('origin', ['world_legends', 'captain_tsubasa_international'])
                ->where(fn ($query) => $query->whereNull('photo_path')->orWhere('photo_path', ''))
                ->update(['photo_path' => $portrait['photo_path']]);
        }
    }

    public function down(): void
    {
        // Conserver les références des portraits.
    }
};
