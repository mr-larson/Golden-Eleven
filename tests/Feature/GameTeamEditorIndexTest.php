<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Concerns\BuildsGameWorld;
use Tests\TestCase;

/** L'éditeur d'équipes d'une partie expose les mêmes données que celui de la base (onze, effectif, joueurs libres). */
class GameTeamEditorIndexTest extends TestCase
{
    use RefreshDatabase;
    use BuildsGameWorld;

    public function test_index_exposes_eleven_roster_and_free_players(): void
    {
        $save = $this->makeSave();
        $team = $this->makeTeam($save);
        // Positions réelles du jeu (enum PlayerPosition) : 1 GK, 5 DEF, 5 MID, 5 FW.
        foreach (array_merge(['Goalkeeper'], array_fill(0, 5, 'Defender'), array_fill(0, 5, 'Midfielder'), array_fill(0, 5, 'Forward')) as $position) {
            $this->makeContract($save, $team, $this->makePlayer($save, ['position' => $position]));
        }
        $free = $this->makePlayer($save, ['position' => 'Forward']);

        $this->actingAs(User::find($save->user_id))
            ->get(route('game-saves.teams.index', $save))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('GameSaves/GameTeams/Index')
                ->has('teams', 1, fn (Assert $t) => $t
                    ->where('id', $team->id)
                    ->has('roster', 16)
                    ->has('roster.0.player.stats')
                    ->has('eleven')
                    ->etc())
                ->missing('freePlayers')
            );

        // Joueurs libres : rechargement partiel (chargés à l'ouverture de « Ajouter un joueur »).
        $this->actingAs(User::find($save->user_id))
            ->get(route('game-saves.teams.index', $save), [
                'X-Inertia'                   => 'true',
                'X-Inertia-Version'           => (string) app(\App\Http\Middleware\HandleInertiaRequests::class)->version(request()),
                'X-Inertia-Partial-Component' => 'GameSaves/GameTeams/Index',
                'X-Inertia-Partial-Data'      => 'freePlayers',
            ])
            ->assertOk()
            ->assertJsonCount(1, 'props.freePlayers')
            ->assertJsonPath('props.freePlayers.0.id', $free->id)
            ->assertJsonStructure(['props' => ['freePlayers' => [['salary', 'stats']]]]);
    }

    public function test_players_index_exposes_labels_for_the_shared_editor(): void
    {
        $save = $this->makeSave();
        $this->makePlayer($save);

        $this->actingAs(User::find($save->user_id))
            ->get(route('game-saves.players.index', $save))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('GameSaves/GamePlayers/Index')
                ->has('positions')->has('positionLabels')->has('origins'));
    }
}
