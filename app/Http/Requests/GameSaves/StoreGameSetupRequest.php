<?php

namespace App\Http\Requests\GameSaves;

use App\Http\Requests\GameSaves\Concerns\ChecksTrainingGains;
use App\Models\GameSaves\GameSave;
use App\Support\GameConfig;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Étape 1 de la création : réglages (label, mode, configuration) gardés en
 * session avant le choix de l'équipe.
 */
class StoreGameSetupRequest extends FormRequest
{
    use ChecksTrainingGains;

    /** Tout utilisateur connecté qui peut créer une sauvegarde. */
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('create', GameSave::class);
    }

    /** Libellé, période, mode de jeu, type de compétition, package de période et configuration. */
    public function rules(): array
    {
        return [
            'label'             => ['nullable', 'string', 'max:255'],
            'period'            => ['required', 'string', \Illuminate\Validation\Rule::in(array_keys(GameSave::PERIOD_COMPETITIONS))],
            'game_mode'         => ['nullable', 'string', 'in:prebuilt,draft'],
            'competition_type'  => ['nullable', 'string', 'in:college_league,world_cup,club_cup'],
            'game_config'       => ['nullable', 'array'],
            'period_package_id' => ['nullable', 'integer', 'exists:period_packages,id'],
            // Nombre de participants (clubs d'une ligue, nations d'une coupe) et façon de les choisir.
            'participant_count' => ['nullable', 'integer', in_array($this->input('competition_type'), ['world_cup', 'club_cup'], true)
                ? \Illuminate\Validation\Rule::in(\App\Services\WorldCup\NewWorldCupBuilder::TOURNAMENT_SIZES)
                : 'between:4,64'],
            ...GameConfig::rules('game_config.'),
        ];
    }

    /** Vérifie que le gain d'entraînement minimum n'excède pas le maximum. */
    public function after(): array
    {
        return [
            function ($validator) {
                $competition = $this->input('competition_type') ?? GameSave::COMPETITION_LEAGUE;
                if (! in_array($competition, GameSave::PERIOD_COMPETITIONS[$this->input('period')] ?? [], true)) {
                    $validator->errors()->add('competition_type', 'Ce format de compétition n\'est pas disponible pour cette période.');
                }

                // Coupe Club : un tournoi a toujours un nombre de clubs, jamais plus que la période n'en compte.
                if ($this->input('competition_type') === 'club_cup') {
                    $clubs = \App\Models\Team::where('period', $this->input('period'))->count();
                    $count = (int) $this->input('participant_count');
                    if ($count < 4 || $count > $clubs) {
                        $validator->errors()->add('participant_count', "Cette période compte {$clubs} clubs : choisis 4 participants ou plus, sans dépasser ce nombre.");
                    }
                }

                $config = $this->input('game_config');
                if (is_array($config)) {
                    $this->checkTrainingGains($validator, GameConfig::sanitize($config), 'game_config');
                }
            },
        ];
    }

    /** Données validées, configuration réduite aux clés connues (null si vide). */
    public function setupData(): array
    {
        $data = $this->validated();
        $data['game_config'] = GameConfig::sanitize($data['game_config'] ?? null) ?: null;

        // Un tournoi n'a ni draft ni période importée : effectifs pré-faits de la période.
        if (in_array($data['competition_type'] ?? null, ['world_cup', 'club_cup'], true)) {
            $data['game_mode'] = 'prebuilt';
            $data['period_package_id'] = null;
        }

        return $data;
    }
}
