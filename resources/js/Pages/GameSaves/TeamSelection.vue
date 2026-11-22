<!-- resources/js/Pages/GameSaves/TeamSelection.vue -->
<template>
    <Head title="Choix de l'équipe" />

    <AuthenticatedLayout>
        <template #header>
            <H2>Choix de l'équipe</H2>
        </template>

        <div class="min-h-full bg-slate-50 p-3 sm:p-6">

            <!-- Header -->
            <div class="max-w-7xl mx-auto mb-6">
                <p v-if="startForm.errors.setup_token" role="alert" class="mb-3 text-sm text-rose-600">
                    {{ startForm.errors.setup_token }}
                </p>
                <p v-if="startForm.errors.game_save" role="alert" class="mb-3 text-sm text-rose-600">
                    {{ startForm.errors.game_save }}
                </p>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <p class="text-sm text-slate-400">
                            Partie : <span class="font-semibold text-slate-600">{{ label || 'Sans nom' }}</span>
                            &nbsp;•&nbsp;
                            <span v-if="periodPackage" class="font-semibold text-teal-600">📦 {{ periodPackage.name }}</span>
                            <span v-else>Période : <span class="font-semibold text-slate-600">{{ periodLabel(period) }}</span></span>
                            &nbsp;•&nbsp;
                            <span v-if="gameMode === 'draft'" class="font-semibold text-amber-600">🎯 Mode Draft</span>
                            <span v-else class="font-semibold text-teal-600">🏟️ Effectifs pré-faits</span>
                        </p>
                    </div>
                    <Link :href="route('mainMenu')"
                          class="px-4 py-2 rounded-full text-sm font-semibold border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-all">
                        ← Retour
                    </Link>
                </div>
            </div>

            <div class="max-w-9xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

                    <!-- Colonne gauche : liste équipes -->
                    <SelectionList :items="teams" :selected-key="selectedTeam?.id ?? null" :is-active="isHumanTeam"
                                   unit="équipe(s)" empty-text="Aucune équipe" @select="selectTeam">
                        <template #item="{ item: team }">
                            <span v-if="isPicked(team)" title="Adversaire imposé" aria-label="Adversaire imposé"
                                  class="shrink-0 w-4 h-4 rounded bg-amber-500 text-white flex items-center justify-center text-[10px] font-black leading-none">✓</span>
                            <div class="w-7 h-7 rounded-lg overflow-hidden shrink-0 bg-slate-100 border border-slate-200 flex items-center justify-center">
                                <TeamLogoImage loading="lazy" decoding="async" v-if="teamLogoUrl(team)" :src="teamLogoUrl(team)" class="w-full h-full" alt=""/>
                                <span v-else class="text-[8px] text-slate-400">—</span>
                            </div>
                            <span class="truncate">{{ team.name }}</span>
                            <span v-if="teamNote(team) != null"
                                  class="ml-auto shrink-0 px-1.5 py-0.5 rounded-md text-2xs font-black tabular-nums"
                                  :class="isHumanTeam(team) ? 'bg-white/90 text-teal-700' : 'bg-slate-100 text-slate-600'"
                                  title="Note d'équipe (niveau du onze, moral, endurance)">{{ teamNote(team) }}</span>
                            <span v-if="isHumanTeam(team)"
                                  class="shrink-0 w-5 h-5 rounded-full bg-white/90 text-teal-700 text-2xs font-black flex items-center justify-center"
                                  :class="teamNote(team) == null ? 'ml-auto' : ''">
                                {{ seatOf(team) }}
                            </span>
                        </template>
                    </SelectionList>

                    <!-- Panneau principal -->
                    <div v-if="selectedTeam" class="col-span-1 lg:col-span-10 grid grid-cols-1 lg:grid-cols-12 gap-4">

                        <!-- Profil équipe -->
                        <div class="col-span-1 lg:col-span-3 flex flex-col gap-3">

                            <!-- Profil de l'équipe (composant partagé avec la Coupe Nation) -->
                            <SelectionProfileCard :title="selectedTeam.name" :subtitle="selectedTeam.description ?? '—'"
                                                  stat-label="Budget de départ"
                                                  :stat-note="`${roster.length} joueur${roster.length > 1 ? 's' : ''} sous contrat`">
                                <template #visual>
                                    <TeamLogoImage loading="lazy" decoding="async" v-if="teamLogoUrl(selectedTeam)" :src="teamLogoUrl(selectedTeam)" class="w-full h-full" alt=""/>
                                    <span v-else class="text-4xl">🏟️</span>
                                </template>
                                <TeamStyleBadges :team="selectedTeam" size="sm" />
                                <template #stat>{{ startBudget.toLocaleString('fr-FR') }} <GameBanknote /></template>
                            </SelectionProfileCard>

                            <!-- Stats moyennes (prebuilt uniquement) -->
                            <TeamStrengthCard v-if="gameMode !== 'draft'" :players="roster" />

                            <!-- Info draft (draft uniquement) -->
                            <div v-if="gameMode === 'draft'" class="border border-amber-200 rounded-xl bg-amber-50 p-4 shadow-card">
                                <h4 class="text-2xs font-bold text-amber-600 uppercase tracking-wider mb-2">🎯 Mode Draft</h4>
                                <div class="space-y-2 text-xs text-amber-700">
                                    <p>L'équipe démarre <span class="font-bold">sans joueur</span>. Tu construiras ton effectif lors du draft.</p>
                                    <p>Chaque équipe humaine reçoit <span class="font-bold">{{ draftBudget }} <GameBanknote /></span> pour la draft. Chaque équipe IA reçoit <span class="font-bold">{{ aiDraftBudget }} <GameBanknote /></span>.</p>
                                    <p>Ordre de pioche : <span class="font-bold">aléatoire</span> (tirage au sort).</p>
                                    <p>Objectif : constituer un effectif de <span class="font-bold">14 à 18 joueurs</span>.</p>
                                </div>
                            </div>

                            <!-- Actions : mêmes boutons que la Coupe Nation (composant partagé) -->
                            <SelectionActions :name="selectedTeam.name" :seat="seatOf(selectedTeam) || null" :human-count="humanTeams.length"
                                              :can-impose="!!participantCount" :imposed="isPicked(selectedTeam)"
                                              owned-label="✓ Ton équipe" :processing="startForm.processing"
                                              :next-label="(gameMode === 'draft' ? '🎯 Lancer le draft' : '▶ Lancer la partie') + ' →'"
                                              @main="setMainTeam(selectedTeam)" @toggle-human="toggleHumanTeam(selectedTeam)"
                                              @toggle-imposed="toggleParticipant(selectedTeam)" @next="startWithTeam" />
                            <p class="text-[11px] text-slate-500 text-center leading-snug">
                                Chaque équipe pilotée est confiée à un manager humain ; les autres sont gérées par l'IA. Les joueurs jouent à tour de rôle sur cet écran.
                            </p>

                            <!-- Récap hot-seat -->
                            <HumanManagersPanel :managers="humanTeams.map(t => ({ key: t.id, name: t.name, team: t }))" removable
                                                empty-text="Aucune équipe pilotée pour l'instant : choisis une équipe à gauche puis clique sur « Jouer avec »."
                                                @remove="removeHumanTeam($event.team)">
                                <template #icon="{ manager }">
                                    <div class="w-6 h-6 rounded-md overflow-hidden bg-white border border-slate-200 shrink-0 flex items-center justify-center">
                                        <TeamLogoImage loading="lazy" decoding="async" v-if="teamLogoUrl(manager.team)" :src="teamLogoUrl(manager.team)" class="w-full h-full" alt=""/>
                                        <span v-else class="text-[8px] text-slate-400">—</span>
                                    </div>
                                </template>
                                <p v-if="humanTeams.length" class="mb-2 text-center text-xs font-semibold text-slate-600">
                                    {{ launchRecap }}
                                </p>
                                <p v-if="participantCount" class="mb-2 text-center text-xs font-semibold text-teal-600">
                                    🎲 {{ participantTotal }} club(s) choisi(s) · les {{ participantCount - participantTotal }} autres sont tirés au sort (impose des clubs avec le bouton orange)
                                </p>
                            </HumanManagersPanel>
                        </div>

                        <!-- Colonne droite : effectif (prebuilt) ou aperçu style (draft) -->
                        <Panel as="div" tone="white" class="col-span-1 lg:col-span-9 flex flex-col">

                            <!-- Mode prebuilt : effectif complet -->
                            <template v-if="gameMode !== 'draft'">
                                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 shrink-0">
                                    Effectif — {{ roster.length }} joueur(s)
                                </h3>

                                <SelectionRoster :players="roster" empty-text="Aucun joueur sous contrat" />
                            </template>

                            <!-- Mode draft : explications + identité -->
                            <template v-else>
                                <h3 class="text-xs font-bold text-amber-500 uppercase tracking-wider mb-4">
                                    🎯 Identité de {{ selectedTeam.name }}
                                </h3>

                                <div class="space-y-4">
                                    <!-- Style tactique -->
                                    <div class="border border-slate-200 rounded-xl bg-slate-50 p-4">
                                        <SectionTitle class="mb-2">Style tactique</SectionTitle>
                                        <p class="text-sm text-slate-700">
                                            Cette équipe jouera avec un style
                                            <span class="font-bold">{{ styleLabel(selectedTeam.tactical_style) }}</span>.
                                            L'IA adaptera ses actions en match, son entraînement et sa stratégie selon ce profil.
                                        </p>
                                    </div>

                                    <!-- Philosophie -->
                                    <div class="border border-slate-200 rounded-xl bg-slate-50 p-4">
                                        <SectionTitle class="mb-2">Philosophie de gestion</SectionTitle>
                                        <p class="text-sm text-slate-700">
                                            Approche
                                            <span class="font-bold">{{ philosophyLabel(selectedTeam.management_philosophy) }}</span> :
                                            {{ philosophyDescription(selectedTeam.management_philosophy) }}
                                        </p>
                                    </div>

                                    <!-- Règles du draft -->
                                    <div class="border border-amber-100 rounded-xl bg-amber-50/50 p-4">
                                        <h4 class="text-2xs font-bold text-amber-500 uppercase tracking-wider mb-3">Comment ça marche</h4>
                                        <div class="space-y-2 text-xs text-slate-600">
                                            <div class="flex items-start gap-2">
                                                <span class="text-amber-500 font-bold shrink-0">1.</span>
                                                <p>L'ordre de pioche est tiré au sort entre les 15 équipes.</p>
                                            </div>
                                            <div class="flex items-start gap-2">
                                                <span class="text-amber-500 font-bold shrink-0">2.</span>
                                                <p>Chaque équipe pioche <span class="font-bold">1 joueur par tour</span>, puis on passe à la suivante.</p>
                                            </div>
                                            <div class="flex items-start gap-2">
                                                <span class="text-amber-500 font-bold shrink-0">3.</span>
                                                <p>Le draft continue jusqu'à ce que chaque équipe ait au moins <span class="font-bold">14 joueurs</span> (max 18).</p>
                                            </div>
                                            <div class="flex items-start gap-2">
                                                <span class="text-amber-500 font-bold shrink-0">4.</span>
                                                <p>Budget de draft par équipe humaine : <span class="font-bold">{{ draftBudget }} <GameBanknote /></span>. Budget de départ en saison : <span class="font-bold">{{ startBudget }} <GameBanknote /></span>.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </Panel>
                    </div>

                    <!-- Placeholder si aucune équipe sélectionnée -->
                    <div v-else class="col-span-1 lg:col-span-10 min-h-[400px] border border-dashed border-slate-200 rounded-xl flex flex-col items-center justify-center text-slate-400">
                        <div class="text-4xl mb-3">⚽</div>
                        <p class="font-semibold">Sélectionne une équipe pour voir son profil</p>
                    </div>

                </div><!-- fin grid 12 -->
            </div><!-- fin max-w -->
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import GameBanknote from '@/Components/GameBanknote.vue';
import TeamLogoImage from '@/Components/TeamLogoImage.vue';
import { periodLabel } from '@/Pages/GameSaves/periods.js';
import SelectionList from '@/Components/Selection/SelectionList.vue';
import SelectionRoster from '@/Components/Selection/SelectionRoster.vue';
import SectionTitle from '@/Components/SectionTitle.vue';
import Panel from '@/Components/Panel.vue';
import { competitionLabel } from '@/Pages/GameSaves/competitions.js';
import SelectionActions from '@/Components/Selection/SelectionActions.vue';
import SelectionProfileCard from '@/Components/Selection/SelectionProfileCard.vue';
import HumanManagersPanel from '@/Components/Selection/HumanManagersPanel.vue';
import TeamStrengthCard from '@/Components/Selection/TeamStrengthCard.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import H2 from '@/Components/H2.vue';
import TeamStyleBadges from '@/Pages/GameSaves/Play/components/TeamStyleBadges.vue';
import { useTeamStyles } from '@/Pages/GameSaves/Play/useTeamStyles.js';
import { usePlayerUtils } from '@/Pages/GameSaves/Play/usePlayerUtils.js';
import { useTeamStrength } from '@/Composables/useTeamStrength.js';

const props = defineProps({
    label:    { type: String, default: null },
    period:   { type: String, required: true },
    // Équipes à afficher, façonnées comme des Team canon (contracts[].player) :
    // celles du package choisi (id synthétique = team_key) si periodPackage
    // est renseigné, sinon l'effectif standard — décidé en amont, à l'écran
    // de création (cf. GameSaveController::store()).
    teams:    { type: Array,  required: true },
    gameMode: { type: String, default: 'prebuilt' },
    competitionType: { type: String, default: 'college_league' },
    gameConfig:      { type: Object, default: null },
    draftBudget:     { type: Number, required: true },
    aiDraftBudget:   { type: Number, required: true },
    setupToken:      { type: String, default: null },
    // Budget de départ réel d'une équipe pilotée (selon la difficulté) ; null = budget du club.
    humanStartBudget: { type: Number, default: null },
    // Package de période choisi à la création (ou null pour l'effectif standard) :
    // remplace entièrement le roster, figé pour cet écran (pas de bascule ici).
    periodPackage:   { type: Object, default: null },
    // Championnat réduit : nombre de clubs voulu (null = tous) et façon de les choisir.
    participantCount:       { type: Number, default: null },
});

const selectedTeam = ref(null);

// Helpers d'affichage partagés (cohérence avec le reste de l'app, cf. dashboard / effectif).
const {
    keyStatsFor, statLabel, statColor,
    positionGroup, playerPhotoUrl, teamLogoUrl, nationalityFlag,
} = usePlayerUtils();

const startForm = useForm({
    label:     props.label || '',
    period:    props.period,
    team_ids:  [],
    game_mode: props.gameMode,
    competition_type: props.competitionType,
    game_config: props.gameConfig,
    period_package_id: props.periodPackage?.id ?? null,
});

// Hot-seat multi-manager : équipes humaines dans l'ordre des sièges (prebuilt ET draft).
const humanTeams  = ref([]);

// Récapitulatif avant lancement (« Ligue · Solo · Effectifs pré-faits · Nankatsu »),
// pour vérifier ses choix après être passé par la configuration.
const launchRecap = computed(() => [
    competitionLabel(props.competitionType),
    humanTeams.value.length > 1 ? `Hot-seat ${humanTeams.value.length} joueurs` : 'Solo',
    props.gameMode === 'draft' ? 'Draft' : 'Effectifs pré-faits',
    (humanTeams.value.length ? humanTeams.value : [selectedTeam.value]).filter(Boolean).map(t => t.name).join(', '),
].filter(Boolean).join(' · '));

// Championnat réduit, clubs choisis à la main : participants cochés (les équipes pilotées comptent d'office).
const manualPicking = computed(() => !!props.participantCount);
const pickedIds     = ref([]);
const isParticipant = (team) => isHumanTeam(team) || pickedIds.value.includes(team.id);
const participantTotal = computed(() => props.teams.filter(isParticipant).length);

const isPicked = (team) => pickedIds.value.includes(team.id);

/** « Jouer avec » : l'équipe devient le siège 1 (elle ne peut plus être adversaire imposé). */
function setMainTeam(team) {
    pickedIds.value = pickedIds.value.filter(id => id !== team.id);
    const others = humanTeams.value.filter(t => t.id !== team.id).slice(1);
    humanTeams.value = [team, ...others];
}

/** « Imposer comme adversaire » : retient ou retire le club, tant qu'il reste de la place. */
function toggleParticipant(team) {
    if (isHumanTeam(team)) return;
    const i = pickedIds.value.indexOf(team.id);
    if (i === -1) {
        if (participantTotal.value < props.participantCount) pickedIds.value.push(team.id);
    } else {
        pickedIds.value.splice(i, 1);
    }
}

const isHumanTeam = (team) => humanTeams.value.some(t => t.id === team.id);
const seatOf      = (team) => humanTeams.value.findIndex(t => t.id === team.id) + 1;

function toggleHumanTeam(team) {
    pickedIds.value = pickedIds.value.filter(id => id !== team.id);
    const i = humanTeams.value.findIndex(t => t.id === team.id);
    if (i === -1) humanTeams.value.push(team);
    else humanTeams.value.splice(i, 1);
}

function removeHumanTeam(team) {
    const i = humanTeams.value.findIndex(t => t.id === team.id);
    if (i !== -1) humanTeams.value.splice(i, 1);
}

const roster = computed(() => {
    if (!selectedTeam.value?.contracts) return [];
    return selectedTeam.value.contracts.map(c => c.player).filter(Boolean);
});

const { tacticalLabel, tacticalIcon, philosophyLabel: philosophyName, philosophyIcon } = useTeamStyles();

// Libellés et icônes viennent de l'enum TeamStyle (props Inertia partagées)
const styleLabel = (key) => `${tacticalIcon(key)} ${tacticalLabel(key)}`.trim();
const philosophyLabel = (key) => `${philosophyIcon(key)} ${philosophyName(key)}`.trim();

const philosophyDescription = (key) => ({
    stars:      'mise sur 2-3 joueurs vedettes et complète avec des profils modestes.',
    collective: 'vise un effectif homogène, sans écarts de niveau entre les joueurs.',
    balanced:   'cherche un mix raisonnable entre quelques bons joueurs et une base solide.',
    economist:  'privilégie les bonnes affaires et garde du budget en réserve pour la saison.',
}[key] ?? '');

function selectTeam(team) {
    selectedTeam.value = team;
}

function startWithTeam() {
    // 1 à N équipes humaines (hot-seat), dans l'ordre des sièges.
    // En mode package, t.id est le team_key (string) du package sélectionné.
    if (!humanTeams.value.length) return;
    startForm.team_ids = humanTeams.value.map(t => t.id);
    startForm
        .transform(d => ({
            label: d.label, period: d.period, game_mode: d.game_mode, competition_type: d.competition_type,
            team_ids: d.team_ids, game_config: d.game_config,
            period_package_id: d.period_package_id,
            // Championnat réduit : nombre de clubs ; liste explicite seulement si choisie à la main (sinon tirage serveur).
            participant_count: props.participantCount,
            participant_ids: manualPicking.value && pickedIds.value.length ? props.teams.filter(isParticipant).map(t => t.id) : undefined,
            setup_token: props.setupToken,
        }))
        .post(route('game-saves.start'), { preserveScroll: true });
}

// Note de chaque équipe (liste de gauche) ; la carte « Forces » calcule la sienne avec le même composable.
const { teamRatingOf } = useTeamStrength();
// Note de chaque équipe, calculée une fois par jeu de données (le template la lit 3 fois par équipe).
const teamNotes = computed(() => new Map(props.teams.map(team => [
    team.id,
    teamRatingOf((team.contracts ?? []).map(c => c.player).filter(Boolean))?.note ?? null,
])));
const teamNote   = (team) => teamNotes.value.get(team.id) ?? null;

// overallOf / keyStatsFor / statLabel / statColor / positionGroup proviennent du
// composable partagé usePlayerUtils (déstructuré plus haut) — on n'ajoute ici que
// les helpers de mise en forme propres à cet écran.

// Budget affiché = budget de départ réel en partie (difficulté), sinon celui du club.
const startBudget = computed(() => props.humanStartBudget ?? selectedTeam.value?.budget ?? 0);

const label = props.label;
</script>
