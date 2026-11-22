<?php

namespace App\Http\Requests;

use App\Models\Contract;
use App\Models\Team;
use Illuminate\Foundation\Http\FormRequest;

/** Requête de création ou d'édition d'un contrat du roster de référence (hors sauvegarde). */
class ContractRequest extends FormRequest
{
    /** Création ou modification autorisée par ContractPolicy (réservée aux administrateurs). */
    public function authorize(): bool
    {
        $contract = $this->route('contract');

        return $contract
            ? (bool) $this->user()?->can('update', $contract)
            : (bool) $this->user()?->can('create', Contract::class);
    }

    /** Équipe, joueur, salaire par match, nombre de matchs du contrat et dates facultatives. */
    public function rules(): array
    {
        return [
            'team_id'   => ['required', 'exists:teams,id'],
            'player_id' => ['required', 'exists:players,id'],

            // coût par match
            'salary'        => ['required', 'integer', 'min:0'],

            // nombre total de matchs du contrat
            'matches_total'  => ['required', 'integer', 'min:1'],

            // matches_played sera en général géré côté backend (0 à la création)
            'matches_played' => ['nullable', 'integer', 'min:0'],

            // Dates → optionnelles / pas utilisées pour le gameplay
            'start_date' => ['nullable', 'date'],
            'end_date'   => ['nullable', 'date'],
        ];
    }

    /** À la création : un joueur n'a qu'un contrat par période (celle du club). */
    public function after(): array
    {
        return [
            function ($validator) {
                if ($this->route('contract') || $validator->errors()->isNotEmpty()) {
                    return;
                }

                $period = Team::whereKey($this->input('team_id'))->value('period');
                $already = Contract::where('player_id', $this->input('player_id'))
                    ->whereHas('team', fn ($q) => $q->where('period', $period))
                    ->exists();

                if ($already) {
                    $validator->errors()->add('player_id', 'Ce joueur a déjà un contrat dans cette période.');
                }
            },
        ];
    }
}
