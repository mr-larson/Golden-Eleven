<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Renseigne `logo_path` des équipes qui n'en ont pas (clubs Pro et World Youth créés sans logo),
     * quand public/storage/teams/{slug}.{ext} existe, puis recopie ce logo dans les équipes des parties
     * déjà créées (game_teams les a copiées vides). Ne touche jamais un logo déjà renseigné.
     */
    public function up(): void
    {
        foreach (DB::table('teams')->whereNull('logo_path')->get(['id', 'name']) as $team) {
            foreach (['webp', 'png', 'jpg', 'jpeg'] as $ext) {
                $path = 'teams/'.Str::slug($team->name).'.'.$ext;
                if (is_file(public_path('storage/'.$path))) {
                    DB::table('teams')->where('id', $team->id)->update(['logo_path' => $path]);
                    break;
                }
            }
        }

        foreach (DB::table('game_teams')->whereNull('logo_path')->whereNotNull('base_team_id')->get(['id', 'base_team_id']) as $gameTeam) {
            $logo = DB::table('teams')->where('id', $gameTeam->base_team_id)->value('logo_path');
            if ($logo) {
                DB::table('game_teams')->where('id', $gameTeam->id)->update(['logo_path' => $logo]);
            }
        }
    }

    public function down(): void
    {
        // Données de référence : rien à défaire.
    }
};
