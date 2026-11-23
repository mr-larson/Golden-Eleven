<?php

namespace Tests\Feature;

use App\Models\Player;
use App\Models\Team;
use Database\Seeders\PlayerSeeder;
use Database\Seeders\Players\AoAshi\Roster;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\BuildsGameWorld;
use Tests\TestCase;

class AoAshiRosterTest extends TestCase
{
    use RefreshDatabase;
    use BuildsGameWorld;

    private const NEW_A_TEAM = ['Akutsu', 'Kuribayashi', 'Kiriki', 'Takasugi', 'Yoshitsune'];

    public function test_fresh_catalog_has_the_a_team_and_no_coach_as_player(): void
    {
        $this->seed(PlayerSeeder::class);

        $this->assertSame(17, Player::where('origin', 'ao_ashi')->count());
        $this->assertSame(0, Player::where('origin', 'ao_ashi')->where('lastname', 'Nakamura')->count(), 'Taira Nakamura est un entraîneur');
        $this->assertSame(self::NEW_A_TEAM, Player::where('origin', 'ao_ashi')->whereIn('lastname', self::NEW_A_TEAM)->orderBy('id')->pluck('lastname')->all());
        $this->assertSame('Forward', Player::where('lastname', 'Yoshitsune')->sole()->position->value);
        $this->assertSame('Goalkeeper', Player::where('lastname', 'Akiyama')->sole()->position->value);
        $this->assertSame('Japon', Player::where('lastname', 'Akutsu')->sole()->nationality);
    }

    public function test_migration_adds_the_a_team_archives_nakamura_and_preserves_the_rest(): void
    {
        $seeder = new PlayerSeeder();
        $moves  = Roster::specialMoves();
        // Catalogue « existant » : les 8 joueurs historiques + Nakamura, sans l'équipe A.
        foreach (Roster::players() as $profile) {
            if (! in_array($profile[1], self::NEW_A_TEAM, true)) {
                DB::table('players')->insert($seeder->row('ao_ashi', $profile, [], $moves));
            }
        }
        $nakamura = Player::create(['firstname' => 'Taira', 'lastname' => 'Nakamura', 'age' => 13, 'position' => 'Midfielder',
            'origin' => 'ao_ashi', 'nationality' => 'Japon', 'cost' => 46, 'stats' => ['speed' => 60], 'heading' => 18]);
        $reference = Player::where('firstname', 'Eisaku')->sole();
        $reference->update(['cost' => 123]);
        $saved = $this->makePlayer($this->makeSave(), ['base_player_id' => $reference->id, 'origin' => 'ao_ashi', 'shot' => 37]);
        $referenceBefore = $reference->fresh()->getAttributes();
        $savedBefore     = $saved->fresh()->getAttributes();

        $migration = require database_path('migrations/2026_11_22_120000_add_esperion_a_team_remove_nakamura.php');
        $migration->up();
        $migration->up();   // rejouable

        $this->assertSame(17, Player::where('origin', 'ao_ashi')->count());
        $this->assertSoftDeleted('players', ['id' => $nakamura->id]);
        $this->assertSame(5, Player::whereIn('lastname', self::NEW_A_TEAM)->count());
        $this->assertSame($referenceBefore, $reference->fresh()->getAttributes());
        $this->assertSame($savedBefore, $saved->fresh()->getAttributes());
    }

    public function test_esperion_migration_creates_the_club_in_the_three_periods_and_fixes_positions(): void
    {
        $seeder = new PlayerSeeder();
        $moves  = Roster::specialMoves();
        // Catalogue « existant » : sans les quatre nouveaux joueurs, Yoshitsune encore gardien.
        foreach (Roster::players() as $profile) {
            if (! in_array($profile[1], ['Akiyama', 'Yamada', 'Nikaido', 'Shimura'], true)) {
                DB::table('players')->insert($seeder->row('ao_ashi', $profile, [], $moves));
            }
        }
        Player::where('lastname', 'Yoshitsune')->update(['position' => 'Goalkeeper']);

        $migration = require database_path('migrations/2026_11_23_100000_add_esperion_club_to_all_periods.php');
        $migration->up();
        $migration->up();   // rejouable

        $this->assertSame(17, Player::where('origin', 'ao_ashi')->count());
        $this->assertSame('Forward', Player::where('lastname', 'Yoshitsune')->sole()->position->value);
        foreach (['world_youth', 'pro'] as $period) {
            $team = Team::where('period', $period)->where('name', 'Esperion')->sole();
            $this->assertSame(15, DB::table('contracts')->where('team_id', $team->id)->count(), $period);
            $this->assertSame(1, DB::table('contracts')->where('team_id', $team->id)->where('is_captain', 1)->count(), $period);
        }
    }
}
