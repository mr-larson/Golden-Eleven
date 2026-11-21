<?php

namespace Tests\Feature;

use App\Models\Contract;
use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use Database\Seeders\WorldYouthSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Période World Youth : reprise du Collège, ouverte à la Coupe Nation. */
class WorldYouthPeriodTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_copies_college_clubs_and_contracts_and_is_rerunnable(): void
    {
        $club = Team::factory()->create(['name' => 'Nankatsu', 'period' => 'college']);
        foreach (Player::factory()->count(3)->create() as $player) {
            Contract::create(['team_id' => $club->id, 'player_id' => $player->id, 'salary' => 10, 'start_date' => now(), 'end_date' => now()->addYear(), 'is_captain' => false]);
        }

        $this->seed(WorldYouthSeeder::class);
        $this->seed(WorldYouthSeeder::class);

        $copy = Team::where('period', 'world_youth')->where('name', 'Nankatsu')->sole();
        $this->assertSame(3, Contract::where('team_id', $copy->id)->count());
        $this->assertSame(3, Contract::where('team_id', $club->id)->count(), 'le Collège reste intact');
        $this->assertSame(1, Team::where('name', 'Nankatsu')->where('period', 'world_youth')->count());
    }

    public function test_world_youth_accepts_the_nation_cup_at_setup(): void
    {
        $this->actingAs(User::factory()->create())->post(route('game-saves.store'), [
            'period' => 'world_youth', 'competition_type' => 'world_cup', 'participant_count' => 8,
        ])->assertSessionDoesntHaveErrors(['period', 'competition_type']);
    }
}
