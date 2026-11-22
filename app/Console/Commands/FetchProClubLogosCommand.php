<?php

namespace App\Console\Commands;

use Database\Seeders\WorldYouthSeeder;
use Database\Seeders\Players\CaptainTsubasa\Clubs\ProClubs;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Commande `logos:fetch-pro` : télécharge les écussons des clubs de la période Pro via l'API
 * TheSportsDB (clé de test publique « 123 ») et les dépose dans public/images/teams/{slug}.png.
 *
 * Enchaîner ensuite : `php artisan images:publish` (WebP redimensionné vers public/storage),
 * puis `php artisan db:seed --class=ProClubSeeder` pour renseigner `logo_path`.
 */
class FetchProClubLogosCommand extends Command
{
    protected $signature = 'logos:fetch-pro
                            {--force : Re-télécharge même si un logo existe déjà}
                            {--dry-run : Affiche les correspondances sans rien télécharger}';

    protected $description = 'Récupère les logos des clubs de la période Pro depuis TheSportsDB';

    private const API = 'https://www.thesportsdb.com/api/v1/json/123/searchteams.php';

    /** Nom de recherche anglais quand il diffère du nom affiché dans le jeu. */
    private const ALIASES = [
        'FC Barcelone'       => 'Barcelona',
        'Paris Saint-Germain' => 'Paris SG',
        'Bayern Munich'      => 'Bayern Munich',
        'SL Benfica'         => 'Benfica',
        'Atlético de Madrid' => 'Atletico Madrid',
        'RSC Anderlecht'     => 'Anderlecht',
        'Werder Brême'       => 'Werder Bremen',
        'Hamburger SV'       => 'Hamburg',
    ];

    /**
     * Pour chaque club pro : cherche l'équipe de football correspondante, télécharge son badge
     * et signale ceux introuvables (à déposer à la main). Respecte le quota de l'API (pause entre appels).
     */
    public function handle(): int
    {
        $dir = public_path('images/teams');
        File::ensureDirectoryExists($dir);

        $missing = [];

        $names = array_merge(array_column(ProClubs::teams(), 0), array_keys(WorldYouthSeeder::extraClubs()));

        foreach (array_unique($names) as $name) {
            $slug = Str::slug($name);

            if (! $this->option('force') && glob("{$dir}/{$slug}.*")) {
                $this->line("= {$name} : déjà présent");
                continue;
            }

            $badge = $this->findBadge(self::ALIASES[$name] ?? $name);
            if (! $badge) {
                $this->warn("? {$name} : introuvable");
                $missing[] = $name;
                continue;
            }

            if ($this->option('dry-run')) {
                $this->info("✓ {$name} → {$badge}");
                continue;
            }

            $image = Http::timeout(30)->get($badge);
            if (! $image->successful()) {
                $this->warn("! {$name} : téléchargement échoué ({$image->status()})");
                $missing[] = $name;
                continue;
            }

            File::put("{$dir}/{$slug}.png", $image->body());
            $this->info("✓ {$name}");
        }

        if ($missing) {
            $this->newLine();
            $this->warn('À traiter à la main : '.implode(', ', $missing));
        }

        return self::SUCCESS;
    }

    /** URL du badge de la première équipe de football trouvée pour ce nom, ou null. */
    private function findBadge(string $search): ?string
    {
        // Quota gratuit serré : pause systématique, et on patiente en cas de 429 avant de réessayer.
        sleep(3);
        $response = Http::timeout(15)->get(self::API, ['t' => $search]);
        for ($try = 0; $response->status() === 429 && $try < 3; $try++) {
            $this->line('  quota atteint, pause 30 s…');
            sleep(30);
            $response = Http::timeout(15)->get(self::API, ['t' => $search]);
        }
        if (! $response->successful()) {
            return null;
        }

        foreach ($response->json('teams') ?? [] as $team) {
            if (($team['strSport'] ?? null) === 'Soccer' && ! empty($team['strBadge'])) {
                return $team['strBadge'];
            }
        }

        return null;
    }
}
