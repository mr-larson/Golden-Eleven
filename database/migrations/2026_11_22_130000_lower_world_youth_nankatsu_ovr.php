<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** Les 12 stats dont la moyenne donne l'OVR affiché (la tête n'y compte pas). */
    private const OVR_STATS = ['speed', 'stamina', 'attack', 'defense', 'shot', 'pass', 'dribble', 'block', 'intercept', 'tackle', 'hand_save', 'punch_save'];

    /** Effectif de Nankatsu en World Youth (renforts d'Otomo et Shutetsu compris), hors Ishizaki et Misaki ; Tsubasa a quitté le club. */
    private const PLAYERS = [
        ['Yuzo', 'Morisaki'], ['Masato', 'Nakazato'], ['Hiroshi', 'Nagano'], ['Susumu', 'Sakurai'],
        ['Tsuyoshi', 'Oda'], ['Shota', 'Minowa'], ['Kenichi', 'Iwami'], ['Yutaka', 'Murashige'],
        ['Akira', 'Tsuboi'], ['Manabu', 'Okawa'], ['Kenji', 'Tomo'],
        ['Koji', 'Nishio'], ['Masao', 'Nakayama'], ['Takeshi', 'Kishida'], ['Hanji', 'Urabe'], ['Shun', 'Nitta'],
        ['Shingo', 'Takasugi'], ['Mamoru', 'Izawa'], ['Hajime', 'Taki'], ['Teppei', 'Kisugi'],
    ];

    /**
     * Baisse l'OVR de ces joueurs d'exactement 1 point (−1 sur chacune des 12 stats de l'OVR).
     * ATTENTION : les stats d'un joueur sont communes à toutes les périodes, le Collège est donc
     * touché aussi. Migration unique (pas de garde d'idempotence propre : elle ne tourne qu'une fois).
     */
    public function up(): void
    {
        foreach (self::PLAYERS as [$first, $last]) {
            $row = DB::table('players')->where('firstname', $first)->where('lastname', $last)
                ->where('origin', 'captain_tsubasa')->first();
            if (! $row) {
                continue;
            }

            $stats = json_decode($row->stats, true);
            foreach (self::OVR_STATS as $key) {
                if (isset($stats[$key])) {
                    $stats[$key] = max(1, (int) $stats[$key] - 1);
                }
            }
            DB::table('players')->where('id', $row->id)->update(['stats' => json_encode($stats, JSON_UNESCAPED_UNICODE), 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        // Retouche d'équilibrage volontaire : non réversible automatiquement.
    }
};
