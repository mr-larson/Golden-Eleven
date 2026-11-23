<?php

namespace App\Models\GameSaves;

use App\Helpers\HeadingRating;
use App\Helpers\PlayerLuck;
use App\Models\GameSaves\Concerns\BelongsToGameSave;
use App\Models\Player;
use App\Models\Traits\CalculatesWeeklyCost;
use App\Models\Traits\HasFullName;
use App\Models\Traits\HasPhotoUrl;
use App\Models\Traits\HasSoccerStats;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * Joueur d'une partie : copie du joueur du catalogue (base_player_id) avec ses
 * stats propres, qui évoluent (entraînement, progression), son moral, sa relation
 * avec le coach et sa chance. Les stats sont des colonnes, contrairement au
 * catalogue (JSON). Peut aussi être un amateur ou un joueur fictif (Coupe Nation).
 */
class GamePlayer extends Model
{
    use HasFactory;
    use CalculatesWeeklyCost;
    use HasSoccerStats;
    use HasFullName;
    use HasPhotoUrl;
    use BelongsToGameSave;

    protected $fillable = [
        'game_save_id',
        'base_player_id',
        'firstname',
        'lastname',
        'position',
        'origin',
        'nationality',
        'secondary_positions',
        'speed',
        'stamina',
        'luck',
        'attack',
        'defense',
        'shot',
        'pass',
        'dribble',
        'block',
        'intercept',
        'tackle',
        'heading',
        'hand_save',
        'punch_save',
        'cost',
        'morale',
        'coach_affinity',
        'description',
        'photo_path',
        'special_moves'
    ];


    protected $casts = [
        'luck' => 'integer',
        'special_moves' => 'array',
        'secondary_positions' => 'array',
    ];

    /**
     * À la création : tire la chance du joueur et estime sa note de tête si elles ne
     * sont pas fournies.
     */
    protected static function booted(): void
    {
        static::creating(function (GamePlayer $player) {
            $player->luck ??= PlayerLuck::roll();
            $player->heading ??= HeadingRating::forProfile($player->position, $player->getAttributes());
        });
    }

    /** Origine des joueurs fictifs (générés pour compléter les sélections en Coupe Nation). */
    public const ORIGIN_FICTIONAL = 'fictional';

    /** Joueurs amateurs générés pour garnir le marché (cf. AmateurMarketService). */
    public const ORIGIN_AMATEUR = 'amateur';

    /** Expose le coût avec majoration de polyvalence côté front. */
    protected $appends = ['adjusted_cost'];

    /** Joueur du catalogue dont celui-ci est la copie (absent pour amateurs et fictifs). */
    public function basePlayer()
    {
        return $this->belongsTo(Player::class, 'base_player_id');
    }

    /** Clés des stats d'un joueur (colonnes à plat de la partie). */
    private const STAT_KEYS = ['speed', 'attack', 'defense', 'stamina', 'shot', 'pass', 'dribble', 'block', 'intercept', 'tackle', 'heading', 'hand_save', 'punch_save'];

    /** Stats à plat sous forme de tableau clé → valeur. */
    public function statsArray(): array
    {
        return collect(self::STAT_KEYS)->mapWithKeys(fn ($k) => [$k => (int) ($this->{$k} ?? 0)])->all();
    }

    /**
     * Joueur de la partie mis en forme pour les écrans d'effectif et la fiche détaillée
     * (même forme que Player::toDetailCard : l'éditeur d'équipes est partagé avec la base).
     *
     * @return array<string, mixed>
     */
    public function toDetailCard(): array
    {
        $stats = $this->statsArray();

        return [
            'id'                  => $this->id,
            'name'                => trim("{$this->firstname} {$this->lastname}"),
            'firstname'           => $this->firstname,
            'lastname'            => $this->lastname,
            'age'                 => null,
            'photo_path'          => $this->photo_path,
            'nationality'         => $this->nationality instanceof \BackedEnum ? $this->nationality->value : $this->nationality,
            'position'            => $this->position instanceof \BackedEnum ? $this->position->value : $this->position,
            'secondary_positions' => $this->secondary_positions ?? [],
            'special_moves'       => $this->special_moves ?? [],
            'description'         => $this->description,
            'stats'               => $stats,
            'ovr'                 => Player::statsAverage($stats),
        ];
    }

    /** Salaire hebdomadaire proposé pour un nouveau contrat, calculé d'après les stats du joueur. */
    public function suggestedSalary(): int
    {
        return $this->calculateWeeklyCost($this->statsArray(), $this->position instanceof \BackedEnum ? $this->position->value : $this->position);
    }

    /**
     * Copie transitoire sous forme de joueur du catalogue (non enregistrée) : permet de réutiliser
     * les services de composition de la base (onze type de l'IA) sur un effectif de partie.
     * L'id du catalogue est repris quand il existe (liens entre joueurs), sinon un id négatif.
     */
    public function toCatalogPlayer(): Player
    {
        return (new Player())->forceFill([
            'id'                  => $this->base_player_id ?? -$this->id,
            'firstname'           => $this->firstname,
            'lastname'            => $this->lastname,
            'position'            => $this->position instanceof \BackedEnum ? $this->position->value : $this->position,
            'secondary_positions' => $this->secondary_positions ?? [],
            'nationality'         => $this->nationality instanceof \BackedEnum ? $this->nationality->value : $this->nationality,
            'stats'               => $this->statsArray(),
        ]);
    }

    // (optionnel mais conseillé) : contrats dans la partie
    public function contracts()
    {
        return $this->hasMany(GameContract::class, 'game_player_id');
    }

    /** Historique des variations de moral du joueur. */
    public function moraleLogs()
    {
        return $this->hasMany(GamePlayerMoraleLog::class, 'game_player_id');
    }

    /** Promesses faites au joueur. */
    public function promises()
    {
        return $this->hasMany(GamePromise::class, 'game_player_id');
    }

    /**
     * Filtre les joueurs libres selon la config de visibilité.
     */
    public function scopeVisibleForConfig($query, \App\Models\GameSaves\GameSave $gameSave): void
    {
        $config = $gameSave->getConfig();
        $origins = $config['visible_origins'] ?? [];

        $hidden = array_keys(array_filter($origins, fn($v) => !$v));
        if (!empty($hidden)) {
            $query->where(fn ($q) => $q->whereNull('origin')->orWhereNotIn('origin', $hidden));
        }

        if (is_array($config['legends_countries'] ?? null)) {
            $query->where(fn ($q) => $q->whereNull('origin')->orWhere('origin', '!=', 'world_legends')
                ->orWhere(fn ($legends) => $legends->where('origin', 'world_legends')
                    ->whereIn('nationality', $config['legends_countries'])));
        }

        if (empty($config['internationals_visible'])) {
            $query->where(function ($q) {
                $q->where(fn ($origins) => $origins->whereNull('origin')
                    ->orWhereNotIn('origin', ['captain_tsubasa_international', 'world_legends']))
                  ->where(fn ($legacy) => $legacy->where('origin', '!=', 'captain_tsubasa')
                  ->orWhereNull('nationality')
                  ->orWhere('nationality', \App\Enums\Nationality::JAPON));
            });
        }
    }

    /**
     * Exclut les joueurs fictifs (origin = 'fictional').
     * Ils ne servent qu'à compléter les sélections nationales en Coupe
     * du Monde et ne doivent jamais apparaître dans le pool de draft.
     */
    public function scopeExcludingFictional($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('origin')
              ->orWhere('origin', '!=', self::ORIGIN_FICTIONAL);
        });
    }

    /**
     * Implémentation pour HasSoccerStats :
     * les stats sont des colonnes directement sur la table game_players.
     */
    protected function getBaseStat(string $key): int
    {
        return (int) ($this->{$key} ?? 0);
    }

    /**
     * Facteur de polyvalence : majoration salariale
     * (+3 % par poste secondaire, plafonnée à +10 %).
     */
    public function versatilityFactor(): float
    {
        $count = count((array) ($this->secondary_positions ?? []));

        return 1.0 + min($count * 0.03, 0.10);
    }

    /** Coût hebdomadaire incluant la majoration de polyvalence. */
    public function getAdjustedCostAttribute(): int
    {
        return (int) round(($this->cost ?? 0) * $this->versatilityFactor());
    }

    /**
     * Coût hebdomadaire de renouvellement : calculé d'après les stats actuelles du
     * joueur (qui ont pu progresser), avec la majoration de polyvalence.
     */
    public function getRenewalCostAttribute(): int
    {
        $cost = $this->calculateWeeklyCost($this->getAttributes(), $this->position);

        return (int) round($cost * $this->versatilityFactor());
    }

}
