<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Période « World Youth » : point de départ identique au Collège (mêmes clubs, mêmes effectifs),
 * à adapter ensuite depuis la base de données (sélecteur de période).
 *
 * Additif et rejouable, comme ProClubSeeder : les clubs World Youth sont retrouvés par leur nom
 * (ids stables) et leurs contrats sont recopiés depuis le Collège à chaque exécution, donc ce seeder
 * ÉCRASE les contrats World Youth. À ne relancer que tant que la période n'a pas été retouchée à la main.
 * Doit passer APRÈS TeamSeeder et ContractSeeder.
 */
class WorldYouthSeeder extends Seeder
{
    private const PERIOD = 'world_youth';

    /** Copie les clubs et les contrats du Collège dans la période World Youth. */
    public function run(): void
    {
        $copies = [];   // id du club Collège => id du club World Youth
        foreach (Team::where('period', 'college')->get() as $club) {
            $copy = Team::withTrashed()->updateOrCreate(
                ['period' => self::PERIOD, 'name' => $club->name],
                $club->only(['description', 'budget', 'logo_path', 'tactical_style', 'management_philosophy', 'default_formation']) + ['deleted_at' => null],
            );
            $copies[$club->id] = $copy->id;
        }

        DB::table('contracts')->whereIn('team_id', array_values($copies))->delete();

        $rows = [];
        foreach (Contract::whereIn('team_id', array_keys($copies))->get() as $contract) {
            $rows[] = [
                'player_id'  => $contract->player_id,
                'team_id'    => $copies[$contract->team_id],
                'salary'     => $contract->salary,
                'start_date' => $contract->start_date,
                'end_date'   => $contract->end_date,
                'is_captain' => $contract->is_captain,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        foreach (array_chunk($rows, 200) as $chunk) {
            DB::table('contracts')->insert($chunk);
        }
    }
}
