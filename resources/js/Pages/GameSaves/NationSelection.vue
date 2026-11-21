<script setup>
// Choix de la sélection (et des co-managers hot-seat, adversaires imposés, taille du tournoi), puis
// convocation des joueurs de chaque manager. Domaine : Coupe Nation.
// Rendu par GameSaveCreationController::setup ; poste vers GameSaveCreationController::startWorldCup.
import NationFlag from '@/Components/NationFlag.vue';
import { isValidSelectionLineup } from '@/Components/WorldCup/selectionLineup.js';
import SquadPicker, { isValidSquad } from '@/Components/WorldCup/SquadPicker.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ButtonPrimary from '@/Components/ButtonPrimary.vue';
import SelectionActions from '@/Components/Selection/SelectionActions.vue';
import TeamStrengthCard from '@/Components/Selection/TeamStrengthCard.vue';
import SelectionProfileCard from '@/Components/Selection/SelectionProfileCard.vue';
import TeamStyleBadges from '@/Pages/GameSaves/Play/components/TeamStyleBadges.vue';
import HumanManagersPanel from '@/Components/Selection/HumanManagersPanel.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import H2 from '@/Components/H2.vue';
import { computed, onMounted, ref, watch } from 'vue';
import Panel from '@/Components/Panel.vue';
import SelectionList from '@/Components/Selection/SelectionList.vue';
import SelectionRoster from '@/Components/Selection/SelectionRoster.vue';

const props = defineProps({
    label: { type: String, default: null },
    period: { type: String, default: 'college' },
    competitionType: { type: String, default: 'world_cup' },
    nations: { type: Array, default: () => [] },
    squad: { type: Object, default: null },          // aperçu de la sélection du siège 1 (étape convocation)
    coSquads: { type: Array, default: () => [] },    // aperçus des co-managers, dans l'ordre des sièges
    gameConfig: { type: Object, default: null },
    tournamentSizes: { type: Array, default: () => [8] },
    participantCount: { type: Number, default: 8 },
    maxHumans: { type: Number, default: 2 },
    coNations: { type: Array, default: () => [] },
    opponents: { type: Array, default: () => [] },
    nationPreview: { type: Array, default: () => [] },   // effectif de la nation affichée (chargé à la demande)
});

const form = useForm({
    label: props.label || '',
    period: props.period,
    competition_type: props.competitionType,
    nation: props.squad?.name ?? null,
    participant_count: props.participantCount,
    co_nations: [...props.coNations],
    opponents: [...props.opponents],
    player_ids: [...(props.squad?.selectedIds ?? [])],
    formation: props.squad?.formation ?? null,
    lineup_slots: null,
    co_lineup_slots: {},
    co_formations: Object.fromEntries(props.coSquads.filter(Boolean).map(s => [s.name, s.formation])),
    co_player_ids: Object.fromEntries(props.coSquads.filter(Boolean).map(s => [s.name, [...s.selectedIds]])),
    game_config: props.gameConfig,
});

// ── Étape 1 : nations ─────────────────────────────────────────
// Format de chaque taille de tournoi, affiché à côté du bouton.
const SIZE_HINTS = { 8: '2 poules, demies', 16: '4 poules, quarts', 24: '6 poules, 8es (+ 4 meilleurs 3es)', 32: '8 poules, 8es' };

const playableCount = computed(() => props.nations.filter(n => n.playable).length);
const humanSeatLimit = computed(() => Math.max(1, Math.min(4, Math.floor(form.participant_count / 4))));

const humans = computed(() => 1 + form.co_nations.length);
const seatOf = (name) => {
    if (form.nation === name) return 1;
    const index = form.co_nations.indexOf(name);
    return index === -1 ? null : index + 2;
};
const isOpponent = (name) => form.opponents.includes(name);

// Nation affichée au centre (profil + effectif), indépendante du rôle qu'on lui donne.
const selected = ref(props.nations.find(n => n.name === form.nation) ?? props.nations.find(n => n.playable) ?? null);
const preview = computed(() => props.nationPreview ?? []);
const previewLoading = ref(false);

// L'effectif de la nation affichée est chargé à la demande (rechargement partiel), pas pour les 30+ nations d'un coup.
function selectNation(nation) {
    selected.value = nation;
    loadPreview();
}
function loadPreview() {
    if (!selected.value?.playable) return;
    previewLoading.value = true;
    router.get(route('game-saves.setup'), { ...setupQuery.value, preview: selected.value.name }, {
        only: ['nationPreview'], preserveState: true, preserveScroll: true, replace: true,
        onFinish: () => { previewLoading.value = false; },
    });
}
onMounted(loadPreview);

const without = (list, name) => list.filter(n => n !== name);

/** « Jouer avec » : la nation devient la sélection principale (siège 1). */
function setMain(name) {
    form.nation = name;
    form.co_nations = without(form.co_nations, name);
    form.opponents = without(form.opponents, name);
}
/** Co-manager hot-seat : ajoute ou retire la nation d'un siège supplémentaire. */
function toggleCo(name) {
    if (name === form.nation) return;
    form.opponents = without(form.opponents, name);
    if (form.co_nations.includes(name)) form.co_nations = without(form.co_nations, name);
    else if (humans.value < humanSeatLimit.value) form.co_nations.push(name);
}
/** Adversaire imposé : ajoute ou retire la nation des adversaires, tant qu'il reste de la place. */
function toggleOpponent(name) {
    if (name === form.nation) return;
    form.co_nations = without(form.co_nations, name);
    if (isOpponent(name)) form.opponents = without(form.opponents, name);
    else if (humans.value + form.opponents.length < form.participant_count) form.opponents.push(name);
}

const setupQuery = computed(() => ({
    nation: form.nation, participant_count: form.participant_count, co_nations: form.co_nations, opponents: form.opponents,
}));

// ── Étape 2 : convocation de chaque manager ───────────────────
const squads = computed(() => [props.squad, ...props.coSquads].filter(Boolean));
const active = ref(0);
const idsOf = (squad) => squad.name === props.squad?.name ? form.player_ids : (form.co_player_ids[squad.name] ?? []);
function setIds(squad, ids) {
    if (squad.name === props.squad?.name) form.player_ids = ids;
    else form.co_player_ids[squad.name] = ids;
}
const slotsOf = squad => squad.name === props.squad?.name ? form.lineup_slots : (form.co_lineup_slots[squad.name] ?? null);
function setSlots(squad, slots) {
    if (squad.name === props.squad?.name) form.lineup_slots = slots;
    else form.co_lineup_slots[squad.name] = slots;
}
const formationOf = squad => squad.name === props.squad?.name ? form.formation : form.co_formations[squad.name];
function setFormation(squad, value) {
    setSlots(squad, null);
    if (squad.name === props.squad?.name) form.formation = value;
    else form.co_formations[squad.name] = value;
}
const validSquads = computed(() => squads.value.map(s => isValidSquad(s, idsOf(s)) && isValidSelectionLineup(slotsOf(s), idsOf(s))));
// Les adversaires non imposés sont tirés au sort : aucune place à pourvoir à la main.
const canLaunch = computed(() => squads.value.length > 0 && validSquads.value.every(Boolean));
const errors = computed(() => [
    form.errors.lineup_slots, form.errors.co_lineup_slots, form.errors.formation, form.errors.co_formations, form.errors.player_ids, form.errors.co_player_ids, form.errors.co_nations, form.errors.opponents,
    form.errors.participant_count, form.errors.nation, form.errors.game_save,
    ...Object.entries(form.errors).filter(([k]) => k.startsWith('player_ids.') || k.startsWith('co_player_ids.') || k.startsWith('co_formations.') || k.startsWith('lineup_slots.') || k.startsWith('co_lineup_slots.')).map(([, v]) => v),
].filter(Boolean));

watch(() => props.squad?.name, () => {
    form.nation = props.squad?.name ?? null;
    form.player_ids = [...(props.squad?.selectedIds ?? [])];
    form.formation = props.squad?.formation ?? null;
    form.lineup_slots = null;
    form.clearErrors();
});

function launch() {
    if (!canLaunch.value) return;
    form.post(route('game-saves.start-world-cup'), { preserveScroll: true });
}
</script>

<template>
    <Head :title="squad ? 'Convoquer les joueurs' : 'Choisir ta sélection'" />

    <AuthenticatedLayout>
        <template #header>
            <H2>🌍 Coupe Nation — {{ squad ? 'Convoque tes joueurs' : 'Choisis ta sélection' }}</H2>
        </template>

        <div class="p-2 sm:p-4">
            <div v-if="!squad" class="max-w-9xl mx-auto">
                <div class="mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <p class="text-sm text-slate-400">
                        Partie : <span class="font-semibold text-slate-600">{{ label || 'Nouvelle Coupe Nation' }}</span>
                        &nbsp;•&nbsp; <span class="font-semibold text-teal-600">🌍 {{ form.participant_count }} sélections</span>
                        <span class="text-slate-400"> · {{ SIZE_HINTS[form.participant_count] }}</span>
                    </p>
                    <Link :href="route('game-saves.create')"
                          class="px-4 py-2 rounded-full text-sm font-semibold border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-all">
                        ← Retour
                    </Link>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                    <!-- Colonne gauche : nations (même liste que le choix d'équipe) -->
                    <SelectionList :items="nations" :item-key="n => n.name" :selected-key="selected?.name ?? null"
                                   :is-active="n => !!seatOf(n.name)" :disabled="n => !n.playable"
                                   unit="nation(s)" empty-text="Aucune nation" @select="selectNation">
                        <template #item="{ item: nation, active }">
                            <span class="text-xl leading-none shrink-0"><NationFlag :flag="nation.flag" :url="nation.flag_url" :name="nation.name" /></span>
                            <span class="truncate">{{ nation.name }}</span>
                            <span v-if="nation.rating" class="ml-auto shrink-0 px-1.5 py-0.5 rounded-md text-2xs font-black tabular-nums"
                                  :class="active ? 'bg-white/90 text-teal-700' : 'bg-slate-100 text-slate-600'"
                                  title="Note moyenne du onze de départ">{{ Math.round(nation.rating) }}</span>
                            <span v-else-if="!nation.playable" class="ml-auto shrink-0 text-2xs text-rose-400">incomplet</span>
                            <span v-if="seatOf(nation.name)"
                                  class="shrink-0 w-5 h-5 rounded-full bg-white/90 text-teal-700 text-2xs font-black flex items-center justify-center">{{ seatOf(nation.name) }}</span>
                            <span v-else-if="isOpponent(nation.name)"
                                  class="shrink-0 px-1.5 py-0.5 rounded-full bg-amber-500 text-white text-2xs font-black">VS</span>
                        </template>
                    </SelectionList>

                    <div v-if="selected" class="col-span-1 lg:col-span-10 grid grid-cols-1 lg:grid-cols-12 gap-4">
                        <!-- Profil + actions -->
                        <div class="col-span-1 lg:col-span-3 flex flex-col gap-3">
                            <SelectionProfileCard :title="selected.name"
                                                  :subtitle="selected.playable ? (selected.description ?? `${selected.total} joueurs disponibles`) : 'Incomplet : pas assez de joueurs avec un gardien'">
                                <template #visual><NationFlag :flag="selected.flag" :url="selected.flag_url" :name="selected.name" /></template>
                                <TeamStyleBadges :team="selected" size="sm" />
                            </SelectionProfileCard>

                            <template v-if="selected.playable">
                                <SelectionActions :name="selected.name" :seat="seatOf(selected.name)" :human-count="form.nation ? humans : 0"
                                                  :can-add-human="humanSeatLimit >= 2" :imposed="isOpponent(selected.name)"
                                                  @main="setMain(selected.name)" @toggle-human="toggleCo(selected.name)"
                                                  @toggle-imposed="toggleOpponent(selected.name)"
                                                  @next="router.get(route('game-saves.setup', setupQuery))" />
                            </template>

                            <!-- Forces de la nation affichée (mêmes calculs que pour une équipe de Ligue) -->
                            <TeamStrengthCard v-if="selected.playable" :players="preview" />

                            <!-- Récap : managers, adversaires imposés -->
                            <HumanManagersPanel :managers="(form.nation ? [form.nation, ...form.co_nations] : []).map(name => ({ key: name, name }))"
                                                empty-text="Choisis une nation puis clique sur « Jouer avec ».">
                                <p class="mb-2 text-center text-xs font-semibold text-slate-600">
                                    {{ form.opponents.length }} adversaire(s) imposé(s) · les {{ Math.max(0, form.participant_count - humans - form.opponents.length) }} autres sont tirés au sort
                                </p>
                                <p v-for="error in errors" :key="error" class="mb-2 text-xs text-rose-500">{{ error }}</p>
                            </HumanManagersPanel>
                        </div>

                        <!-- Effectif de la nation affichée (même composant que pour une équipe de Ligue) -->
                        <Panel as="div" tone="white" class="col-span-1 lg:col-span-9 flex flex-col">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 shrink-0">
                                Joueurs de {{ selected.name }} ({{ preview.length }})
                            </h3>
                            <p v-if="!selected.playable" class="text-sm italic text-slate-500">Cette nation n'a pas encore assez de joueurs pour concourir.</p>
                            <SelectionRoster v-else :players="preview" :empty-text="previewLoading ? 'Chargement…' : 'Aucun joueur'" />
                        </Panel>
                    </div>
                    <div v-else class="col-span-1 lg:col-span-10 min-h-[400px] border border-dashed border-slate-200 rounded-xl flex flex-col items-center justify-center text-slate-400">
                        <div class="text-4xl mb-3">🌍</div>
                        <p class="font-semibold">Sélectionne une nation pour voir son effectif</p>
                    </div>
                </div>
            </div>

            <form v-else class="min-w-0" @submit.prevent="launch">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <!-- Un onglet par manager humain -->
                    <div v-if="squads.length > 1" class="flex flex-wrap gap-2 rounded-xl bg-slate-200 p-1">
                        <button v-for="(s, i) in squads" :key="s.name" type="button" @click="active = i"
                                class="px-3 py-2 rounded-lg text-xs font-semibold"
                                :class="active === i ? 'bg-white text-teal-700 shadow-sm' : 'text-slate-500 hover:text-slate-700'">
                            <span v-if="squads.length > 1">Siège {{ i + 1 }} · </span>{{ s.name }}
                            <span :class="validSquads[i] ? 'text-emerald-600' : 'text-rose-500'">{{ validSquads[i] ? '✓' : '!' }}</span>
                        </button>
                    </div>
                    <Link :href="route('game-saves.setup')"
                          class="rounded-full border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                        ← Choix du pays
                    </Link>
                </div>

                <SquadPicker v-if="squads[active]" :key="squads[active].name" class="mt-4" :squad="squads[active]"
                             :model-value="idsOf(squads[active])" :formation="formationOf(squads[active])" :lineup-slots="slotsOf(squads[active])"
                             @update:lineup-slots="setSlots(squads[active], $event)" :disabled="form.processing"
                             @update:formation="setFormation(squads[active], $event)"
                             @update:model-value="setIds(squads[active], $event)" />

                <div class="sticky bottom-2 z-10 mt-4 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-card">
                    <div aria-live="polite">
                        <p class="text-sm font-semibold text-slate-700">
                            {{ idsOf(squads[active]).length }} / {{ squads[active].maxPlayers }} joueurs convoqués
                        </p>
                        <p v-if="!canLaunch" class="text-sm text-rose-700">
                            Chaque manager doit convoquer {{ squads[0].minPlayers }} à {{ squads[0].maxPlayers }} joueurs, dont un gardien.
                        </p>
                        <p v-for="error in errors" :key="error" class="text-sm text-rose-700">{{ error }}</p>
                    </div>
                    <ButtonPrimary type="submit" tone="teal" :disabled="!canLaunch || form.processing">
                        {{ form.processing ? 'Création…' : 'Lancer la Coupe Nation' }}
                    </ButtonPrimary>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
