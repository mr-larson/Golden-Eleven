<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamRequest;
use App\Models\Team;
use App\Services\Support\EditorProfileCache;
use App\Services\WorldCup\NationalTeamAssembler;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Support\EditorPeriod;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Str;

/** Gestion des équipes de la base de données (équipes « canon ») : liste, création, modification, suppression et logo. */
class TeamController extends Controller
{
    /**
     * Écran unique des équipes de la base : liste + fiche, création comprise
     * (fiche vide sur la même page).
     */
    public function index(Request $request, NationalTeamAssembler $assembler): Response
    {
        $this->authorize('viewAny', Team::class);

        // Profil du onze type du club (OVR + moyenne par stat) : titulaires choisis par l'IA parmi ses joueurs sous contrat,
        // et pays dominant. Mis en cache tant que les joueurs, équipes et contrats ne changent pas.
        $profiles = EditorProfileCache::remember('teams', fn () => Team::with('players')->get()
            ->mapWithKeys(fn (Team $team) => [$team->id => [
                'eleven'  => $assembler->elevenProfile(
                    $team->players,
                    $team->default_formation,
                    $team->tactical_style ?? 'balanced',
                    $team->management_philosophy ?? 'collective',
                ),
                'country' => self::dominantNationality($team->players),
            ]])->all());

        $period = EditorPeriod::current($request);

        // Contrats de chaque club de la période (onglets Joueurs et Contrats de la fiche), joueur compris.
        $teams = Team::where('period', $period)->orderBy('name')->get();
        $contractsByTeam = \App\Models\Contract::with('player')->whereIn('team_id', $teams->modelKeys())->get()->groupBy('team_id');

        return Inertia::render('Teams/Index', [
            'period'      => $period,
            // Joueurs sans contrat dans cette période, chargés seulement quand on ouvre « Ajouter un joueur ».
            'freePlayers' => Inertia::lazy(fn () => \App\Models\Player::whereDoesntHave('contracts', fn ($q) => $q->whereHas('team', fn ($t) => $t->where('period', $period)))
                ->orderBy('firstname')->orderBy('lastname')->get()
                ->map(fn ($p) => $p->toDetailCard() + ['salary' => $p->suggestedSalary()])->values()->all()),
            'teams'       => $teams->each(function (Team $team) use ($profiles, $contractsByTeam) {
                $team->setAttribute('roster', ($contractsByTeam[$team->id] ?? collect())->filter(fn ($c) => $c->player)->map(fn ($c) => [
                    'contract_id' => $c->id,
                    'player_id'   => $c->player_id,
                    'name'        => trim("{$c->player->firstname} {$c->player->lastname}"),
                    'nationality' => $c->player->toDetailCard()['nationality'],
                    'salary'      => $c->salary,
                    'is_captain'  => (bool) $c->is_captain,
                    'player'      => $c->player->toDetailCard(),
                ])->sortBy(fn ($r) => mb_strtolower($r['name']))->values()->all());
                $team->setAttribute('eleven', $profiles[$team->id]['eleven'] ?? null);
                $team->setAttribute('country', $profiles[$team->id]['country'] ?? null);
            }),
            // Équipe à ouvrir (ex. juste après sa création) / fiche vide demandée.
            'selectedId'  => $request->integer('selected') ?: null,
            'startCreate' => $request->boolean('create'),
        ]);
    }


    /** Pays d'une équipe : nationalité la plus représentée parmi ses joueurs sous contrat (null s'il n'y en a pas). */
    private static function dominantNationality($players): ?string
    {
        $counts = $players->map(fn ($p) => $p->nationality instanceof \BackedEnum ? $p->nationality->value : $p->nationality)
            ->filter()->countBy()->sortDesc();

        return $counts->keys()->first();
    }

    /** Ancienne URL de création : ouvre l'écran en mode création. */
    public function create(): RedirectResponse
    {
        return redirect()->route('teams.index', ['create' => 1]);
    }

    /**
     * Enregistrement d’une nouvelle équipe.
     */
    public function store(TeamRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $team = new Team();
        $team->period = EditorPeriod::current($request); // le club naît dans la période affichée

        // On ne "fill" pas logo/remove_logo
        unset($data['remove_logo'], $data['logo']);
        $team->fill($data);

        if ($request->hasFile('logo')) {
            $team->logo_path = $this->storeTeamLogo(
                $request->file('logo'),
                $team->name
            );
        }

        $team->save();

        return redirect()
            ->route('teams.index', ['selected' => $team->id])
            ->with('success', 'Équipe créée.');
    }

    /** Ancienne URL d'édition : même écran que la liste. */
    public function edit(Request $request): Response
    {
        return $this->index($request);
    }

    /**
     * Mise à jour d’une équipe.
     */
    public function update(TeamRequest $request, Team $team)
    {
        $data = $request->validated();

        // ✅ IMPORTANT : on lit remove_logo AVANT de unset
        $removeLogo = $request->boolean('remove_logo');

        unset($data['remove_logo'], $data['logo']);
        $team->fill($data);

        // Retrait explicite du logo (le fichier est conservé, cf. storeTeamLogo).
        if ($removeLogo) {
            $team->logo_path = null;
        }

        // remplacement logo
        if ($request->hasFile('logo')) {
            $team->logo_path = $this->storeTeamLogo(
                $request->file('logo'),
                $team->name
            );
        }

        $team->save();

        return back()->with('success', 'Équipe mise à jour.');
    }

    /**
     * Suppression d’une équipe.
     */
    public function destroy(Team $team): RedirectResponse
    {
        $this->authorize('delete', $team);

        $team->delete();

        return redirect()
            ->route('teams.index')
            ->with('success', 'Équipe supprimée.');
    }

    /**
     * Stocke le logo sur le disque public (servi sous /storage, comme les logos
     * canon « teams/… » affichés par le front). Un logo remplacé n'est jamais
     * supprimé : les saves en copient le chemin (game_teams.logo_path).
     */
    private function storeTeamLogo(\Illuminate\Http\UploadedFile $file, string $teamName): string
    {
        // Extension déduite du contenu (pas du nom envoyé par le client).
        $filename = Str::slug($teamName).'-'.Str::random(6).'.'.$file->guessExtension();

        return $file->storeAs('teams', $filename, 'public');
    }
}
