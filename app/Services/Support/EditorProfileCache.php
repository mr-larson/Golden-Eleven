<?php

namespace App\Services\Support;

use App\Models\Contract;
use App\Models\Nation;
use App\Models\Player;
use App\Models\Team;
use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Cache des profils du « onze type » affichés dans les éditeurs de la base (pays, équipes).
 *
 * Calculer le onze de chaque nation ou club (sélection par l'IA) coûte environ une seconde : on garde le
 * résultat en mémoire. La clé contient une « version » des données (dernière modification et nombre de lignes des
 * joueurs, nations, équipes et contrats, plus la dernière migration jouée) : dès qu'une de ces données change,
 * la clé change et le profil est recalculé, donc jamais d'OVR périmé.
 */
class EditorProfileCache
{
    /** Empreinte des données dont dépendent les profils. */
    public static function version(): string
    {
        return md5(json_encode([
            Player::count(), (string) Player::max('updated_at'),
            Nation::count(), (string) Nation::max('updated_at'),
            Team::count(), (string) Team::max('updated_at'),
            Contract::count(), (string) Contract::max('updated_at'),
            DB::table('migrations')->max('id'),
        ]));
    }

    /**
     * Résultat de `$compute` pour la version courante des données (recalculé si les données ont changé).
     *
     * @return array<int|string, mixed>
     */
    public static function remember(string $name, Closure $compute): array
    {
        return Cache::remember("editor-profiles:{$name}:".self::version(), now()->addHours(12), $compute);
    }
}
