<?php

namespace App\Http\Requests;

use App\Enums\TeamStyle;
use App\Models\Team;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Requête de création ou d'édition d'une équipe du roster de référence (hors sauvegarde). */
class TeamRequest extends FormRequest
{
    /** Création ou modification autorisée par TeamPolicy (réservée aux administrateurs). */
    public function authorize(): bool
    {
        $team = $this->route('team');

        return $team
            ? (bool) $this->user()?->can('update', $team)
            : (bool) $this->user()?->can('create', Team::class);
    }

    /** Nom unique, description, logo (upload ou suppression) et styles tactique / philosophie. */
    public function rules(): array
    {
        $teamId = $this->route('team')?->id;

        return [
            'name'        => ['required', 'string', 'max:255', Rule::unique('teams', 'name')->ignore($teamId)],
            'description' => ['nullable', 'string', 'max:1000'],

            'logo'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_logo' => ['nullable', 'boolean'],

            'tactical_style'         => ['nullable', Rule::in(TeamStyle::TACTICAL_STYLES)],
            'management_philosophy'  => ['nullable', Rule::in(TeamStyle::PHILOSOPHIES)],
        ];
    }
}
