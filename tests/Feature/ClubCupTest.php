<?php

namespace Tests\Feature;

use App\Enums\GamePhase;
use App\Models\GameSaves\GameMatch;
use App\Models\GameSaves\GameSave;
use App\Models\GameSaves\GameTeam;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Coupe Club : tournoi (poules puis élimination directe) entre clubs de la période. */
class ClubCupTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_club_cup_starts_as_a_tournament_with_the_requested_clubs(): void
    {
        $user  = User::factory()->create();
        $teams = Team::factory()->count(10)->create();

        $this->actingAs($user)->post(route('game-saves.start'), [
            'period' => 'college', 'game_mode' => 'prebuilt', 'competition_type' => 'club_cup',
            'team_id' => $teams[3]->id, 'participant_count' => 8,
        ])->assertRedirect();

        $save = GameSave::where('user_id', $user->id)->firstOrFail();
        $this->assertTrue($save->isClubCup());
        $this->assertTrue($save->isTournament());
        $this->assertFalse($save->isWorldCup());
        $this->assertSame(GamePhase::Group, $save->currentPhase());
        $this->assertSame(8, GameTeam::where('game_save_id', $save->id)->count());
        $this->assertTrue(GameMatch::where('game_save_id', $save->id)->where('round', 'like', 'group_%')->exists());
        $this->assertNotNull($save->gameState()->tournament());
    }

    public function test_a_club_cup_cannot_exceed_the_clubs_of_the_period(): void
    {
        $user = User::factory()->create();
        Team::factory()->count(6)->create();

        $this->actingAs($user)->post(route('game-saves.store'), [
            'period' => 'college', 'competition_type' => 'club_cup', 'participant_count' => 8,
        ])->assertSessionHasErrors('participant_count');
    }

    public function test_a_club_cup_refuses_the_draft_mode(): void
    {
        $user  = User::factory()->create();
        $teams = Team::factory()->count(8)->create();

        $this->actingAs($user)->post(route('game-saves.start'), [
            'period' => 'college', 'game_mode' => 'draft', 'competition_type' => 'club_cup',
            'team_id' => $teams[0]->id, 'participant_count' => 8,
        ])->assertSessionHasErrors('competition_type');
        $this->assertSame(0, GameSave::count());
    }

    public function test_tournament_presentation_uses_club_logos_as_emblems(): void
    {
        $user  = User::factory()->create();
        $teams = Team::factory()->count(8)->create(['logo_path' => 'teams/logo.png']);

        $this->actingAs($user)->post(route('game-saves.start'), [
            'period' => 'college', 'game_mode' => 'prebuilt', 'competition_type' => 'club_cup',
            'team_id' => $teams[0]->id, 'participant_count' => 8,
        ])->assertRedirect();

        $save = GameSave::where('user_id', $user->id)->firstOrFail();
        $presentation = app(\App\Services\WorldCup\TournamentService::class)->presentation($save);
        $row = $presentation['groups'][0]['rows'][0];

        $this->assertStringEndsWith('storage/teams/logo.png', $row['flag_url']);
        $this->assertSame('🏟️', $row['flag']);
    }
}
