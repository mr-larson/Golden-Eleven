<?php

namespace App\Services\WorldCup;

use App\Enums\Nationality;
use App\Models\GameSaves\GameSave;
use App\Models\GameSaves\GameTeam;
use App\Models\Nation;

/**
 * Emblème (emoji + image) de chaque participant d'un tournoi, par nom d'équipe : drapeau pour une
 * Coupe Nation, logo du club pour une Coupe Club. Les écrans de tournoi lisent `flag` / `flag_url`
 * sans savoir de quelle coupe il s'agit.
 */
final class TournamentEmblems
{
    /** @var array<string, string> nom d'équipe => URL d'image */
    private array $urls;

    private function __construct(private readonly bool $clubs, array $urls)
    {
        $this->urls = $urls;
    }

    /** Emblèmes des participants de la partie. */
    public static function for(GameSave $gameSave): self
    {
        if (! $gameSave->isClubCup()) {
            return new self(false, Nation::flagUrls());
        }

        $urls = GameTeam::where('game_save_id', $gameSave->id)->whereNotNull('logo_path')->pluck('logo_path', 'name')
            ->map(fn (string $path) => str_starts_with($path, 'http') || str_starts_with($path, '/') ? $path : asset('storage/'.$path))
            ->all();

        return new self(true, $urls);
    }

    /** @return array{flag: string, flag_url: string|null} */
    public function get(?string $name): array
    {
        return [
            'flag'     => $this->clubs ? '🏟️' : Nationality::flag($name),
            'flag_url' => $this->urls[$name ?? ''] ?? null,
        ];
    }
}
