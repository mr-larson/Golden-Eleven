<?php

namespace Tests\Unit;

use Database\Seeders\Players\CaptainTsubasa\Clubs\ClubRosters;
use Database\Seeders\Players\CaptainTsubasa\Clubs\ProClubs;
use PHPUnit\Framework\TestCase;

/** Cohérence des données de la période Professionnel (clubs, effectifs, capitaines). */
class ProClubsDataTest extends TestCase
{
    public function test_every_club_has_a_full_roster_and_a_captain_from_it(): void
    {
        $teams = array_column(ProClubs::teams(), 0);
        $this->assertCount(21, $teams);
        $this->assertSame($teams, array_keys(ProClubs::players()));

        foreach (ProClubs::players() as $club => $names) {
            // 14 joueurs, 15 pour Esperion (4 remplaçants dans le groupe d'Ao Ashi).
            $this->assertCount($club === 'Esperion' ? 15 : 14, $names, $club);
            $this->assertContains(ProClubs::captains()[$club], $names, "capitaine de {$club}");
        }
    }

    public function test_a_player_has_a_single_pro_contract_and_names_do_not_clash_with_college_clubs(): void
    {
        $all = array_merge(...array_values(ProClubs::players()));
        $this->assertSame($all, array_values(array_unique($all)), 'joueur présent dans deux clubs pro');
        // Esperion n'existe pas au Collège (période incohérente avec le club).
        $this->assertSame([], array_values(array_intersect(array_column(ProClubs::teams(), 0), array_keys(ClubRosters::players()))));
    }
}
