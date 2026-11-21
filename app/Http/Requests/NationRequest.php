<?php

namespace App\Http\Requests;

use App\Enums\TeamStyle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Modification d'une nation du catalogue : style tactique, philosophie de gestion et description (le nom reste verrouillé). */
class NationRequest extends FormRequest
{
    /** Modification autorisée par NationPolicy (réservée aux administrateurs). */
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('update', $this->route('nation'));
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'tactical_style'        => ['required', Rule::in(TeamStyle::TACTICAL_STYLES)],
            'management_philosophy' => ['required', Rule::in(TeamStyle::PHILOSOPHIES)],
            'description'           => ['nullable', 'string', 'max:1000'],
        ];
    }
}
