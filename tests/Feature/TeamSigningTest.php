<?php

namespace Tests\Feature;

use App\Models\Contract;
use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Inertia;
use Tests\TestCase;

/** « Ajouter un joueur » depuis la fiche équipe : joueurs libres de la période, un contrat par joueur et par période. */
class TeamSigningTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->is_admin = true; // hors $fillable
        $user->save();

        return $user;
    }

    private function sign(User $admin, Team $team, Player $player)
    {
        return $this->actingAs($admin)->post(route('contracts.store'), [
            'team_id' => $team->id, 'player_id' => $player->id, 'salary' => $player->suggestedSalary(), 'matches_total' => 10,
        ]);
    }

    public function test_a_player_has_one_contract_per_period_only(): void
    {
        $admin  = $this->admin();
        $player = Player::factory()->create();
        $college = Team::factory()->create(['period' => 'college']);
        $otherCollege = Team::factory()->create(['period' => 'college']);
        $pro = Team::factory()->create(['period' => 'pro']);

        $this->sign($admin, $college, $player)->assertSessionDoesntHaveErrors();
        $this->sign($admin, $otherCollege, $player)->assertSessionHasErrors('player_id');
        $this->sign($admin, $pro, $player)->assertSessionDoesntHaveErrors();

        $this->assertSame(2, Contract::where('player_id', $player->id)->count());
    }

    public function test_free_players_are_those_without_a_contract_in_the_period(): void
    {
        $admin  = $this->admin();
        $team   = Team::factory()->create(['period' => 'pro']);
        $signed = Player::factory()->create();
        $free   = Player::factory()->create();
        $this->sign($admin, $team, $signed);

        // Rechargement partiel d'Inertia (comme à l'ouverture du panneau) : seule la prop `freePlayers` est demandée.
        $ids = $this->actingAs($admin)->withSession(['editor_period' => 'pro'])
            ->get(route('teams.index'), [
                'X-Inertia' => 'true', 'X-Inertia-Version' => Inertia::getVersion(),
                'X-Inertia-Partial-Component' => 'Teams/Index', 'X-Inertia-Partial-Data' => 'freePlayers',
            ])->json('props.freePlayers.*.id');

        $this->assertContains($free->id, $ids);
        $this->assertNotContains($signed->id, $ids);
    }
}
