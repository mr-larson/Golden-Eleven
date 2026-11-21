<?php

namespace Database\Seeders\Players\CaptainTsubasa\Clubs;

use App\Enums\TeamStyle;

/**
 * Clubs de la période « Professionnel » : un nouveau catalogue d'équipes et de
 * effectifs qui réutilise les MÊMES joueurs que le Collège (table `players`),
 * mais répartis autrement. Lu par TeamSeeder (équipes) et ContractSeeder
 * (contrats, capitaines).
 *
 * Tant que `teams()` est vide, la période n'est pas proposée à la création.
 *
 * Format de `teams()` : [nom, description, style tactique, philosophie, formation].
 * Format de `players()` : nom du club => titulaires (11) puis remplaçants (3),
 * en « Prénom Nom » comme dans le catalogue. `captains()` : nom du club => capitaine.
 * Le nom d'un club pro doit être différent de tous les noms de clubs du Collège
 * (la colonne `teams.name` est unique).
 */
final class ProClubs
{
    /** @return array<int, array{0: string, 1: string, 2: string, 3: string, 4: string}> */
    public static function teams(): array
    {
        return [
            // ['Nom du club', 'Description.', TeamStyle::TACTICAL_BALANCED, TeamStyle::PHILOSOPHY_BALANCED, '4-4-2'],
        ];
    }

    /** @return array<string, array<int, string>> */
    public static function players(): array
    {
        return [
            // 'Nom du club' => ['Gardien', …11 titulaires…, …3 remplaçants…],
        ];
    }

    /** @return array<string, string> */
    public static function captains(): array
    {
        return [
            // 'Nom du club' => 'Prénom Nom',
        ];
    }
}
