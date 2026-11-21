<?php

namespace Tests\Feature;

use App\Models\Nation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Écran des pays de la base : style tactique, philosophie et description, réservés aux admins. */
class NationEditorTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->is_admin = true; // hors $fillable
        $user->save();

        return $user;
    }

    public function test_admin_edits_style_philosophy_and_description_but_not_the_name(): void
    {
        $nation = Nation::create(['name' => 'Testland', 'code' => 'TL']);

        $this->actingAs($this->admin())->get(route('nations.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Nations/Index'));

        $this->actingAs($this->admin())->post(route('nations.update', $nation), [
            'name' => 'Renommé', 'tactical_style' => 'balanced', 'management_philosophy' => 'collective', 'description' => 'Une sélection solide.',
        ])->assertRedirect();

        $nation->refresh();
        $this->assertSame('Testland', $nation->name);
        $this->assertSame('Une sélection solide.', $nation->description);
    }

    public function test_invalid_style_is_rejected_and_non_admin_is_forbidden(): void
    {
        $nation = Nation::create(['name' => 'Testland', 'code' => 'TL']);

        $this->actingAs($this->admin())->post(route('nations.update', $nation), [
            'tactical_style' => 'nimporte', 'management_philosophy' => 'collective',
        ])->assertSessionHasErrors('tactical_style');

        $this->actingAs(User::factory()->create())->get(route('nations.index'))->assertForbidden();
    }
}
