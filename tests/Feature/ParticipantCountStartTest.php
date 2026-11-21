<?php

namespace Tests\Feature;

use App\Models\GameSaves\GameSave;
use App\Models\GameSaves\GameTeam;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Championnat réduit : nombre de clubs choisi, tirage au sort ou sélection manuelle. */
class ClubCountStartTest extends TestCase
{
    use RefreshDatabase;

    private function baseIdsOf(GameSave $save): array
    {
        return GameTeam::where('game_save_id', $save->id)->pluck('base_team_id')->sort()->values()->all();
    }

    public function test_random_draw_keeps_the_human_team_and_the_requested_count(): void
    {
        $user  = User::factory()->create();
        $teams = Team::factory()->count(10)->create();

        $this->actingAs($user)->post(route('game-saves.start'), [
            'period' => 'college', 'game_mode' => 'prebuilt',
            'team_id' => $teams[3]->id, 'participant_count' => 4,
        ])->assertRedirect();

        $ids = $this->baseIdsOf(GameSave::where('user_id', $user->id)->firstOrFail());
        $this->assertCount(4, $ids);
        $this->assertContains($teams[3]->id, $ids);
    }

    public function test_manual_selection_uses_exactly_the_chosen_clubs(): void
    {
        $user  = User::factory()->create();
        $teams = Team::factory()->count(8)->create();
        $picked = [$teams[0]->id, $teams[2]->id, $teams[4]->id, $teams[6]->id];

        $this->actingAs($user)->post(route('game-saves.start'), [
            'period' => 'college', 'game_mode' => 'prebuilt',
            'team_ids' => [$teams[2]->id], 'participant_count' => 4, 'participant_ids' => $picked,
        ])->assertRedirect();

        $this->assertEqualsCanonicalizing($picked, $this->baseIdsOf(GameSave::where('user_id', $user->id)->firstOrFail()));
    }

    public function test_partial_selection_is_completed_at_random(): void
    {
        $user  = User::factory()->create();
        $teams = Team::factory()->count(8)->create();
        $picked = [$teams[2]->id, $teams[4]->id];

        $this->actingAs($user)->post(route('game-saves.start'), [
            'period' => 'college', 'game_mode' => 'prebuilt',
            'team_ids' => [$teams[2]->id], 'participant_count' => 5, 'participant_ids' => $picked,
        ])->assertRedirect();

        $ids = $this->baseIdsOf(GameSave::where('user_id', $user->id)->firstOrFail());
        $this->assertCount(5, $ids);
        $this->assertEmpty(array_diff($picked, $ids));
    }

    public function test_manual_selection_must_include_the_human_team(): void
    {
        $user  = User::factory()->create();
        $teams = Team::factory()->count(8)->create();

        $this->actingAs($user)->post(route('game-saves.start'), [
            'period' => 'college', 'game_mode' => 'prebuilt',
            'team_ids' => [$teams[7]->id], 'participant_count' => 4,
            'participant_ids' => [$teams[0]->id, $teams[1]->id, $teams[2]->id, $teams[3]->id],
        ])->assertSessionHasErrors('participant_ids');

        $this->assertSame(0, GameSave::count());
    }

    public function test_without_participant_count_all_clubs_of_the_period_play(): void
    {
        $user = User::factory()->create();
        Team::factory()->count(6)->create();
        $team = Team::factory()->create(['period' => 'pro']);

        $this->actingAs($user)->post(route('game-saves.start'), [
            'period' => 'college', 'game_mode' => 'prebuilt', 'team_id' => Team::where('period', 'college')->first()->id,
        ])->assertRedirect();

        $ids = $this->baseIdsOf(GameSave::where('user_id', $user->id)->firstOrFail());
        $this->assertCount(6, $ids);
        $this->assertNotContains($team->id, $ids);
    }

    public function test_college_period_refuses_the_nation_cup_but_pro_accepts_it(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('game-saves.store'), [
            'period' => 'college', 'competition_type' => 'world_cup', 'participant_count' => 8,
        ])->assertSessionHasErrors('competition_type');

        $this->actingAs($user)->post(route('game-saves.store'), [
            'period' => 'pro', 'competition_type' => 'world_cup', 'participant_count' => 8,
        ])->assertSessionDoesntHaveErrors('competition_type');
    }
}
