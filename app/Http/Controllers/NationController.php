<?php

namespace App\Http\Controllers;

use App\Http\Requests\NationRequest;
use App\Models\Nation;
use App\Services\Support\EditorProfileCache;
use App\Services\WorldCup\NationalTeamAssembler;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/** Édition des nations du catalogue (mode Coupe du monde) : style tactique, philosophie de gestion et description. */
class NationController extends Controller
{
    /** Écran unique des nations : liste + fiche. */
    public function index(Request $request, NationalTeamAssembler $assembler): Response
    {
        $this->authorize('viewAny', Nation::class);

        // Profil du onze type (OVR + moyenne par stat), calculé avec la composition que l'IA aligne ; mis en cache
        // tant que les joueurs, nations, équipes et contrats ne changent pas (le calcul prend environ une seconde).
        $elevens = EditorProfileCache::remember('nations', fn () => Nation::all()
            ->mapWithKeys(fn (Nation $nation) => [$nation->name => $assembler->startingElevenProfile($nation->name)])->all());

        return Inertia::render('Nations/Index', [
            'nations'    => Nation::orderBy('name')->get()->append('flag_url')
                ->each(fn (Nation $nation) => $nation->setAttribute('eleven', $elevens[$nation->name] ?? null)),
            'selectedId' => $request->integer('selected') ?: null,
        ]);
    }

    /** Met à jour la fiche d'une nation (nom, code et drapeau ne sont pas modifiables ici). */
    public function update(NationRequest $request, Nation $nation): RedirectResponse
    {
        $nation->update($request->validated());

        return back()->with('success', 'Nation mise à jour.');
    }
}
