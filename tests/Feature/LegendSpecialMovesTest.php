<?php

namespace Tests\Feature;

use App\Models\Player;
use Database\Seeders\PlayerSeeder;
use Database\Seeders\Players\InternationalLegends\Support\LegendSpecialMoves;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/** Techniques spéciales des légendes réelles : cohérence du catalogue et application au seed. */
class LegendSpecialMovesTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalogue_rules_are_consistent(): void
    {
        $defense = ['tackle', 'block', 'intercept', 'hand_save', 'punch_save'];
        $keys = [];
        foreach (LegendSpecialMoves::specialMoves() as $slug => $moves) {
            $this->assertLessThanOrEqual(2, count($moves), $slug);
            $this->assertSame(count($moves), count(array_unique(array_column($moves, 'base_action'))), "$slug : deux techniques sur la même action");
            foreach ($moves as $move) {
                $this->assertArrayNotHasKey($move['key'], $keys, "clé en double {$move['key']}");
                $keys[$move['key']] = true;
                $this->assertSame(in_array($move['base_action'], $defense, true) ? 'defense' : 'attack', $move['mode'], $move['key']);
                $this->assertNotEmpty($move['label']);
                $this->assertNotEmpty($move['short_label']);
                $this->assertNotEmpty($move['description']);
            }
        }
    }

    public function test_seed_applies_catalogue_to_real_legends_only(): void
    {
        $this->seed(PlayerSeeder::class);
        $catalogue = LegendSpecialMoves::specialMoves();
        $legends = Player::where('origin', 'world_legends')->get()
            ->keyBy(fn (Player $p) => Str::slug($p->firstname.' '.$p->lastname));

        foreach ($catalogue as $slug => $moves) {
            $this->assertTrue($legends->has($slug), "légende inconnue : $slug");
            $this->assertSame(array_column($moves, 'key'), array_column($legends[$slug]->special_moves, 'key'), $slug);
        }
        // Le Pepe réel n'hérite plus du tir Skylab du personnage Tsubasa.
        $this->assertSame(['pepe_hard_tackle'], array_column($legends['pepe']->special_moves, 'key'));
        // Un joueur hors catalogue reste sans technique.
        $this->assertEmpty($legends->first(fn (Player $p) => ! isset($catalogue[Str::slug($p->firstname.' '.$p->lastname)]))->special_moves ?? []);
    }
}
