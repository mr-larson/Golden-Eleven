<?php

namespace App\Http\Requests\GameSaves;

use App\Models\GameSaves\GameSave;
use App\Support\GameConfig;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Requête de création d'une sauvegarde (équipes, mode, période, configuration). Le renommage a sa propre requête : RenameGameSaveRequest. */
class GameSaveRequest extends FormRequest
{
    /** Tout utilisateur connecté peut créer une sauvegarde (délégué à GameSavePolicy). */
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('create', GameSave::class);
    }

    /**
     * Règles de validation pour créer une sauvegarde.
     */
    public function rules(): array
    {
        // Périodes jouables côté backend : celles de GameSave::PERIOD_COMPETITIONS.
        $allowedPeriods = array_keys(GameSave::PERIOD_COMPETITIONS);

        $rules = [
            'label' => [
                'nullable',
                'string',
                'max:255',
            ],

            'team_id' => [
                'nullable',
                'integer',
                'exists:teams,id',
            ],

            // Hot-seat multi-manager : équipes humaines, dans l'ordre des sièges.
            'team_ids' => [
                'nullable',
                'array',
                'min:1',
            ],
            // En mode package de période, les valeurs sont des team_key (string)
            // internes au package et non des id de la table globale `teams`.
            // `distinct` : une même équipe ne peut pas occuper deux sièges.
            'team_ids.*' => $this->filled('period_package_id')
                ? ['string', 'distinct']
                : ['integer', 'distinct', 'exists:teams,id'],

            'period_package_id' => [
                'nullable',
                'integer',
                'exists:period_packages,id',
            ],

            // Championnat réduit : nombre de clubs et, si le joueur les a choisis, leurs id
            // (sans liste, le tirage au sort se fait à la création, humains toujours inclus).
            'participant_count'       => ['nullable', 'integer', 'min:4', 'max:64'],
            'participant_ids'  => ['nullable', 'array'],
            'participant_ids.*' => ['integer', 'distinct', 'exists:teams,id'],

            'game_mode' => ['nullable', 'string', 'in:prebuilt,draft'],

            'competition_type' => ['nullable', 'string', 'in:college_league,world_cup,club_cup'],

            'game_config'      => ['nullable', 'array'],
            ...GameConfig::rules('game_config.'),
            'setup_token'      => ['nullable', 'string', 'size:64'],
        ];

        $rules['period'] = [
            'required',
            'string',
            Rule::in($allowedPeriods),
        ];

        // Au moins une équipe humaine (mono via team_id, ou liste).
        $rules['team_id'][] = 'required_without:team_ids';

        return $rules;
    }

    /**
     * Gain d'entraînement min ≤ max (sinon random_int lève une ValueError).
     */
    public function after(): array
    {
        return [
            function ($validator) {
                // Coupe Club : tournoi de 8, 16, 24 ou 32 clubs, effectifs pré-faits, sans période importée.
                if ($this->input('competition_type') === 'club_cup') {
                    if (! in_array((int) $this->input('participant_count'), \App\Services\WorldCup\NewWorldCupBuilder::TOURNAMENT_SIZES, true)) {
                        $validator->errors()->add('participant_count', 'Une Coupe Club réunit 8, 16, 24 ou 32 clubs.');
                    }
                    if ($this->input('game_mode') === 'draft' || $this->filled('period_package_id')) {
                        $validator->errors()->add('competition_type', 'La Coupe Club se joue avec les effectifs pré-faits de la période.');
                    }
                }

                $participants = $this->input('participant_ids');
                if (is_array($participants) && $participants !== []) {
                    $humans = array_map('intval', $this->input('team_ids') ?? array_filter([$this->input('team_id')]));
                    if (array_diff($humans, array_map('intval', $participants)) !== []) {
                        $validator->errors()->add('participant_ids', 'Les équipes pilotées doivent faire partie des clubs du championnat.');
                    }
                    if ((int) $this->input('participant_count') < count($participants)) {
                        $validator->errors()->add('participant_ids', 'Trop de clubs choisis par rapport au nombre demandé.');
                    }
                }

                $config = $this->input('game_config');
                if (is_array($config) && ! (new GameConfig)->hasValidTrainingGains($config)) {
                    $validator->errors()->add('game_config', 'Le gain max doit être supérieur ou égal au gain min.');
                }
            },
        ];
    }
}
