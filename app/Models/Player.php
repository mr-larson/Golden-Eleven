<?php

namespace App\Models;

use App\Enums\PlayerPosition;
use App\Helpers\HeadingRating;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\CalculatesWeeklyCost;
use App\Models\Traits\HasFullName;
use App\Models\Traits\HasPhotoUrl;
use App\Models\Traits\HasSoccerStats;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Joueur du CATALOGUE (référence du jeu) : identité, poste, origine (œuvre), stats
 * de base en JSON, coups spéciaux. Chaque partie en fait une copie (GamePlayer).
 * Les stats sont toujours complétées par DEFAULT_STATS.
 */
class Player extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasSoccerStats;
    use HasFullName;
    use HasPhotoUrl;
    use CalculatesWeeklyCost;

    protected $table = 'players';

    /**
     * Stats par défaut pour tous les joueurs.
     * Servent de filet de sécurité si une clé manque dans le JSON.
     */
    public const DEFAULT_STATS = [
        'speed'      => 50,
        'stamina'    => 50,
        'attack'     => 50,
        'defense'    => 50,

        'shot'       => 50,
        'pass'       => 50,
        'dribble'    => 50,
        'block'      => 50,
        'intercept'  => 50,
        'tackle'     => 50,
        'heading'    => HeadingRating::DEFAULT_FIELD_RATING,

        'hand_save'  => 0,
        'punch_save' => 0,
    ];

    protected $fillable = [
        'firstname',
        'lastname',
        'age',
        'position',
        'origin',
        'nationality',
        'national_eligible',
        'secondary_positions',
        'cost',
        'stats',
        'special_moves',
        'description',
        'photo_path',
    ];

    protected $casts = [
        'position' => PlayerPosition::class,
        'national_eligible' => 'boolean',
        'secondary_positions' => 'array',
        'special_moves' => 'array'
    ];

    // ==========================
    //  RELATIONS
    // ==========================

    /** Équipes du catalogue où le joueur a un contrat (via la table `contracts`). */
    public function teams(): BelongsToMany
    {
        return $this
            ->belongsToMany(Team::class, 'contracts')
            ->withPivot(['salary', 'start_date', 'end_date'])
            ->withTimestamps();
    }

    /** Contrats de référence du joueur. */
    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    /** Joueurs convocables en sélection nationale (les figurants des effectifs de clubs en sont exclus). */
    /**
     * Joueur du catalogue mis en forme pour les écrans d'effectif et la fiche détaillée (PlayerDetailModal) :
     * identité, poste, photo, stats complétées par DEFAULT_STATS, coups spéciaux, description.
     *
     * @return array<string, mixed>
     */
    public function toDetailCard(): array
    {
        return [
            'id'                  => $this->id,
            'name'                => trim("{$this->firstname} {$this->lastname}"),
            'firstname'           => $this->firstname,
            'lastname'            => $this->lastname,
            'age'                 => $this->age,
            'photo_path'          => $this->photo_path,
            'nationality'         => $this->nationality instanceof \BackedEnum ? $this->nationality->value : $this->nationality,
            'position'            => $this->position?->value,
            'secondary_positions' => $this->secondary_positions ?? [],
            'special_moves'       => $this->special_moves ?? [],
            'description'         => $this->description,
            'stats'               => array_merge(self::DEFAULT_STATS, $this->stats ?? []),
            'ovr'                 => self::statsAverage($this->stats ?? []),
        ];
    }

    /** Salaire hebdomadaire proposé pour un nouveau contrat : calculé d'après les stats du catalogue (comme les contrats de référence). */
    public function suggestedSalary(): int
    {
        return $this->calculateWeeklyCost($this->stats ?? [], $this->position?->value);
    }

    /** OVR d'un joueur : moyenne de ses stats renseignées (> 0), à une décimale ; null sans stats. */
    public static function statsAverage(array $stats): ?float
    {
        $keys = ['speed', 'attack', 'defense', 'stamina', 'shot', 'pass', 'dribble', 'block', 'intercept', 'tackle', 'heading', 'hand_save', 'punch_save'];
        $values = collect($keys)->map(fn ($k) => (float) ($stats[$k] ?? 0))->filter(fn ($v) => $v > 0);

        return $values->isEmpty() ? null : round($values->avg(), 1);
    }

    public function scopeNationalEligible($query)
    {
        return $query->where('national_eligible', true);
    }

    // ==========================
    //  ACCESSORS
    // ==========================

    /**
     * Stats stockées en JSON, toujours fusionnées avec DEFAULT_STATS
     * pour garantir la présence de toutes les clés (lecture comme écriture).
     * Source unique : pas de cast 'array' en plus, sinon double encodage.
     */
    protected function stats(): Attribute
    {
        $normalize = function ($value): array {
            $stored = is_array($value) ? $value : (json_decode((string) $value, true) ?: []);
            $stats = array_merge(self::DEFAULT_STATS, $stored);
            $column = $this->attributes['heading'] ?? null;
            // Source : JSON, puis colonne (toutes deux alignées par les migrations
            // d'équilibrage), puis estimation d'après le profil. Plus de valeur
            // sentinelle : un 15 légitime n'est pas « non renseigné ».
            $stats['heading'] = $stored['heading'] ?? ($column !== null
                ? (int) $column
                : HeadingRating::forProfile($this->attributes['position'] ?? null, $stats));

            return $stats;
        };

        return Attribute::make(
            get: fn ($value) => $normalize($value),
            set: fn ($value) => json_encode($normalize($value)),
        );
    }


    // ==========================
    //  HELPERS POUR LE MOTEUR
    // ==========================

    /** Valeur brute d'une stat (clé JSON), pour les accesseurs de HasSoccerStats. */
    protected function getBaseStat(string $key): int
    {
        $stats = $this->stats;

        return (int) ($stats[$key] ?? self::DEFAULT_STATS[$key] ?? 0);
    }

}
