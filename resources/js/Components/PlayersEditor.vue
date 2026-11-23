<template>
    <Head :title="adapter.headTitle" />

    <AuthenticatedLayout>
        <template #header>
            <H2>{{ adapter.heading }}</H2>
        </template>

        <div class="p-4 sm:p-6 max-w-8xl mx-auto flex flex-col gap-4">
            <div class="flex flex-wrap xl:flex-nowrap items-center justify-between gap-4">
                <EditorSubNav class="shrink-0" :back="adapter.back" :links="adapter.links" />

                <PlayerEditorFilters class="ml-auto xl:min-w-0"
                    :filters="filters" :nationalities="filterNationalities" :origins="filterOrigins" :teams="teams" :period="adapter.period ?? null" @reset="resetFilters"
                />
            </div>

            <EntityEditor
                title="Joueurs"
                :items="filteredPlayers"
                :total="players.length"
                :selected-id="editor.selectedId.value"
                :creating="editor.creating.value"
                :search-text="p => `${p.firstname ?? ''} ${p.lastname ?? ''} ${p.nationality ?? ''} ${origins[p.origin] ?? p.origin ?? ''}`"
                search-placeholder="Rechercher un joueur"
                empty-label="Aucun joueur trouvé."
                add-title="Créer un joueur"
                :tabs="panelTabs"
                v-model:active-tab="activePanel"
                @select="editor.select"
                @create="editor.startCreating"
            >
                <template #item="{ item }">
                    <span class="flex-1 min-w-0 text-left">
                        <span class="block truncate text-sm font-medium">{{ item.firstname }} {{ item.lastname }}</span>
                        <span v-if="homonymNames.has(playerName(item))" class="block text-2xs text-slate-500">
                            {{ [origins[item.origin] ?? item.origin, item.nationality || 'Nationalité non renseignée'].filter(Boolean).join(' · ') }}
                        </span>
                    </span>
                    <span v-if="playerOverall(item) > 0" class="text-2xs font-semibold tabular-nums text-slate-500 shrink-0">
                        {{ playerOverall(item).toFixed(1) }}
                    </span>
                    <span v-if="item.position"
                          :class="positionBadgeClass(item.position)"
                          class="text-2xs font-bold px-2 py-0.5 rounded-full shrink-0">
                        {{ positionShort(item.position) }}
                    </span>
                </template>

                <template #badges>
                    <span v-if="form.position" :class="positionBadgeClass(form.position)"
                          class="text-2xs font-bold px-2 py-0.5 rounded-full">
                        {{ positionLabels[form.position] ?? form.position }}
                    </span>
                    <Badge v-if="overall">
                        OVR {{ overall.toFixed(1) }}
                    </Badge>
                </template>

                <!-- FICHE JOUEUR -->
                <div v-show="activePanel === 'player'" class="bg-slate-50 rounded-xl p-4 sm:p-6 shadow-card">
                    <form @submit.prevent="submit">

                        <PlayerFormFields
                            :form="form"
                            :nationalities="nationalities" :origins="origins"
                            :show-age="isBase"
                            layout="grid"
                            :stats-key="isBase ? 'stats' : null"
                            :positions="positions"
                            :position-labels="positionLabels"
                            :photo-url="photoPath ? `/storage/${photoPath}` : null"
                        />
                    </form>
                </div>

                <!-- COUPS SPÉCIAUX (enregistrés avec la fiche du joueur) -->
                <div v-show="activePanel === 'moves'" class="bg-slate-50 rounded-xl p-4 sm:p-6 shadow-card">
                    <form @submit.prevent="submit">
                        <SpecialMovesEditor v-model="form.special_moves" :errors="form.errors" />
                    </form>
                </div>

                <!-- CONTRAT -->
                <div v-show="activePanel === 'contract'" class="bg-slate-50 rounded-xl p-4 sm:p-6 shadow-card">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-slate-700">Contrat</h2>
                        <button v-if="!contract" type="button" @click="startNewContract"
                                class="flex items-center justify-center h-9 px-3 rounded-full bg-teal-500 text-white text-sm font-medium hover:bg-teal-600 shadow-md">
                            + Ajouter
                        </button>
                    </div>

                    <form v-if="contract" @submit.prevent="saveContract" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <InputLabel value="Équipe" />
                                <InputSelect v-model="contract[teamField]" required placeholder="Choisir..." class="mt-1">
                                    <option v-for="team in teams" :key="team.id" :value="team.id">{{ team.name }}</option>
                                </InputSelect>
                            </div>
                            <div>
                                <InputLabel value="Salaire (billets / match)" />
                                <InputText type="number" min="0" v-model.number="contract.salary" class="mt-1" />
                            </div>
                            <template v-if="isBase">
                                <div>
                                    <InputLabel value="Durée (matchs)" />
                                    <InputText type="number" min="1" v-model.number="contract.matches_total" class="mt-1" />
                                </div>
                                <div>
                                    <InputLabel value="Matchs joués" />
                                    <InputText type="number" min="0" v-model.number="contract.matches_played" class="mt-1" />
                                </div>
                            </template>
                            <template v-else>
                                <div>
                                    <InputLabel value="Début (semaine)" />
                                    <InputText type="number" v-model.number="contract.start_week" class="mt-1" />
                                </div>
                                <div>
                                    <InputLabel value="Fin (semaine)" />
                                    <InputText type="number" v-model.number="contract.end_week" class="mt-1" />
                                </div>
                                <div>
                                    <InputLabel value="Titulaire ?" />
                                    <InputSelect v-model="contract.is_starter" placeholder="" class="mt-1">
                                        <option :value="true">Oui</option>
                                        <option :value="false">Non</option>
                                    </InputSelect>
                                </div>
                            </template>
                        </div>
                    </form>

                    <p v-else class="text-sm text-slate-500 italic">
                        Aucun contrat défini pour ce joueur. Cliquez sur « + Ajouter » pour en créer un.
                    </p>
                </div>
                <template #footer>
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex flex-wrap items-center gap-3">
                            <ButtonDanger v-if="activePanel === 'contract' ? !!contract?.id : !editor.creating.value && !!form.id"
                                          type="button" size="sm" :disabled="form.processing || contractProcessing"
                                          @click="activePanel === 'contract' ? deleteContract() : deletePlayer()">
                                {{ activePanel === 'contract' ? 'Supprimer le contrat' : 'Supprimer le joueur' }}
                            </ButtonDanger>
                        <span class="text-xs" aria-live="polite" :class="pendingChanges ? 'text-amber-700' : 'text-slate-500'">
                            {{ pendingChanges ? '● Modifications non enregistrées' : 'Aucune modification en attente' }}
                        </span>
                        </div>
                        <ButtonPrimary type="button" size="sm"
                                       :disabled="form.processing || contractProcessing || (activePanel === 'contract' && !contract)"
                                       @click="activePanel === 'contract' ? saveContract() : submit()">
                            {{ form.processing || contractProcessing ? 'Enregistrement…' : activePanel === 'contract' ? 'Enregistrer le contrat' : editor.creating.value ? 'Créer le joueur' : 'Enregistrer le joueur' }}
                        </ButtonPrimary>
                    </div>
                </template>
            </EntityEditor>
        </div>
    </AuthenticatedLayout>
</template>


<script setup>
import Badge from '@/Components/Badge.vue';
import PlayerEditorFilters from '@/Components/PlayerEditorFilters.vue';
import { playerOverall } from '@/Composables/playerOverall.js';
import { usePlayerEditorFilters } from '@/Composables/usePlayerEditorFilters.js';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { confirmDialog } from '@/Composables/useDialog.js';
import { ref, computed } from 'vue';

import H2 from '@/Components/H2.vue';
import EntityEditor from '@/Components/EntityEditor.vue';
import EditorSubNav from '@/Components/EditorSubNav.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputSelect from '@/Components/InputSelect.vue';
import InputText from '@/Components/InputText.vue';
import ButtonPrimary from '@/Components/ButtonPrimary.vue';
import ButtonDanger from '@/Components/ButtonDanger.vue';
import PlayerFormFields from '@/Components/PlayerFormFields.vue';
import SpecialMovesEditor from '@/Components/SpecialMovesEditor.vue';
import { PLAYER_STATS, defaultPlayerStats } from '@/Enums/playerStats.js';
import { positionBadgeClass, positionShort } from '@/Enums/positionBadges.js';
import { useEntityEditor, clone } from '@/Composables/useEntityEditor.js';

// Écran d'édition des joueurs, commun à la base de données (admin) et à une partie.
// Seul l'adaptateur change : il porte les libellés, la navigation, les routes et la
// forme des données.
//
// adapter = {
//   kind: 'base' | 'game',        // base : stats imbriquées, âge/origine, contrat en matchs
//                                 // game : stats à plat, contrat en semaines + titulaire
//   headTitle, heading,           // <title> et titre de l'en-tête
//   back: { href, label }, links: [{ href, label, active }], period?,
//   gameSave?,                    // partie (filtres par contrat actif, semaine du contrat)
//   routes: { store(), update(id), destroy(id), contractStore(playerId), contractUpdate(id), contractDestroy(id) },
// }
const props = defineProps({
    adapter: { type: Object, required: true },
    players: { type: Array, required: true },
    nationalities: { type: Array, default: () => [] },
    origins: { type: Object, default: () => ({}) },
    teams: { type: Array, required: true },
    positions: { type: Array, default: undefined },
    positionLabels: { type: Object, default: () => ({}) },
    // Joueur à sélectionner à l'ouverture (ex. juste après sa création).
    selectedId: { type: Number, default: null },
    // Ouvre directement une fiche vide (ancienne URL /players/create).
    startCreate: { type: Boolean, default: false },
});

const isBase = computed(() => props.adapter.kind === 'base');
const teamField = computed(() => (isBase.value ? 'team_id' : 'game_team_id'));

const { filters, origins: filterOrigins, nationalities: filterNationalities, filtered: filteredPlayers, reset: resetFilters } = usePlayerEditorFilters(
    () => props.players, { gameSave: () => props.adapter.gameSave ?? null },
);

const playerName = player => `${player.firstname ?? ''} ${player.lastname ?? ''}`.trim().toLocaleLowerCase();
const homonymNames = computed(() => {
    const counts = new Map();
    for (const player of props.players) {
        const name = playerName(player);
        counts.set(name, (counts.get(name) ?? 0) + 1);
    }
    return new Set([...counts].filter(([, count]) => count > 1).map(([name]) => name));
});

// Stats d'un joueur : imbriquées (base) ou à plat (partie).
const statsOf = player => (isBase.value ? (player.stats || {}) : player);
const defaultStat = key => (isBase.value && key === 'heading' ? 18 : 0);
const flatStats = () => Object.fromEntries(PLAYER_STATS.map(({ key }) => [key, 0]));

const BLANK = isBase.value
    ? {
        id: null, firstname: '', lastname: '', age: '', nationality: '', origin: '', position: '',
        secondary_positions: [], cost: '', description: '', photo: null,
        stats: defaultPlayerStats({ heading: null }), // défaut du modèle Player
        special_moves: [],
    }
    : {
        id: null, firstname: '', lastname: '', position: '', secondary_positions: [],
        nationality: '', cost: 0, description: '', photo: null, remove_photo: false,
        ...flatStats(), special_moves: [],
    };

const form = useForm(clone(BLANK));
// Photo enregistrée : affichée seulement, jamais renvoyée au serveur.
const photoPath = ref(null);
// Contrat édité : copie locale, enregistrée par les routes des contrats.
const contract = ref(null);
const contractProcessing = ref(false);

const activePanel = ref('player');

const editor = useEntityEditor({
    form,
    items: () => props.players,
    blank: BLANK,
    entityLabel: 'Le nouveau joueur',
    initial: () => ({ selectedId: props.selectedId, startCreate: props.startCreate }),
    apply(player) {
        const stats = statsOf(player);
        const values = Object.fromEntries(PLAYER_STATS.map(({ key }) => [key, Number(stats[key] ?? defaultStat(key))]));
        Object.assign(form, {
            id: player.id,
            firstname: player.firstname ?? '',
            lastname: player.lastname ?? '',
            nationality: player.nationality ?? '',
            position: player.position ?? '',
            secondary_positions: clone(player.secondary_positions ?? []),
            cost: player.cost ?? (isBase.value ? '' : 0),
            description: player.description ?? '',
            photo: null, // efface aussi l'aperçu du fichier choisi
            special_moves: clone(player.special_moves ?? []),
            ...(isBase.value
                ? { age: player.age ?? '', origin: player.origin ?? '', stats: values }
                : { remove_photo: false, ...values }),
        });
        photoPath.value = player.photo_path ?? null;
        contract.value = clone(player.contracts?.[0] ?? null);
    },
    onCreate() {
        photoPath.value = null;
        contract.value = null;
        if (activePanel.value === 'contract') activePanel.value = 'player';
    },
});

const contractDirty = computed(() => {
    const saved = props.players.find(player => player.id === form.id)?.contracts?.[0] ?? null;
    return JSON.stringify(contract.value) !== JSON.stringify(saved);
});
const pendingChanges = computed(() => form.isDirty || contractDirty.value);

const panelTabs = computed(() => [
    { key: 'player',   label: 'Fiche joueur' },
    { key: 'moves',    label: 'Coups spéciaux', badge: form.special_moves?.length || null },
    // Un contrat se rattache à un joueur existant : disponible après la création.
    { key: 'contract', label: 'Contrat', badge: contract.value?.id ? '✓' : null,
      disabled: editor.creating.value, disabledHint: "Crée d'abord le joueur" },
]);

const overall = computed(() => {
    const stats = isBase.value ? form.stats : form;
    const values = PLAYER_STATS.map(def => Number(stats?.[def.key] ?? 0)).filter(v => Number.isFinite(v) && v > 0);
    return values.length ? Math.round(values.reduce((a, b) => a + b, 0) / values.length * 10) / 10 : 0;
});

// ---------- Joueur ----------
function submit() {
    const creating = editor.creating.value;
    if (!creating && !form.id) return;

    // Côté partie, la mise à jour passe par une route PUT (multipart : méthode simulée).
    const spoof = !creating && !isBase.value ? { _method: 'PUT' } : {};
    form.transform(({ id: _id, ...data }) => ({ ...data, ...spoof })).post(
        creating ? props.adapter.routes.store() : props.adapter.routes.update(form.id),
        {
            preserveScroll: true,
            forceFormData: true, // nécessaire pour envoyer form.photo (File)
            // Création : le serveur renvoie l'id du joueur créé. Mise à jour : joueur rechargé.
            onSuccess: () => editor.reload(creating ? props.selectedId : form.id),
        },
    );
}

async function deletePlayer() {
    if (!form.id) return;
    if (!(await confirmDialog({ title: 'Supprimer le joueur', message: 'Voulez-vous vraiment supprimer ce joueur ?', confirmLabel: 'Supprimer', danger: true }))) return;

    form.delete(props.adapter.routes.destroy(form.id), {
        preserveScroll: true,
        onSuccess: () => editor.afterDelete(),
    });
}

// ---------- Contrat ----------
function startNewContract() {
    if (!form.id) return;
    contract.value = isBase.value
        ? { id: null, team_id: '', salary: Number(form.cost) || 0, matches_total: 10, matches_played: 0 }
        : {
            id: null, game_team_id: '', salary: 0,
            start_week: props.adapter.gameSave?.week ?? 1, // requis côté serveur
            end_week: null, is_starter: false,
        };
}

function reloadContract() {
    const fresh = props.players.find(p => p.id === form.id);
    contract.value = clone(fresh?.contracts?.[0] ?? null);
}

// Charge utile d'un contrat selon le mode.
function contractPayload() {
    const c = contract.value;
    return isBase.value
        ? { player_id: form.id, team_id: c.team_id, salary: c.salary, matches_total: c.matches_total, matches_played: c.matches_played }
        : { game_team_id: c.game_team_id, salary: c.salary, start_week: c.start_week, end_week: c.end_week, is_starter: c.is_starter };
}

function saveContract() {
    if (!contract.value) return;
    contractProcessing.value = true;

    // Succès et erreurs s'affichent dans le toast global.
    const options = {
        preserveScroll: true,
        // Recharger seulement le contrat (une saisie en cours sur la fiche est conservée).
        onSuccess: reloadContract,
        onFinish: () => { contractProcessing.value = false; },
    };

    if (contract.value.id) {
        // La base enregistre les mises à jour par POST, la partie par PUT.
        router[isBase.value ? 'post' : 'put'](props.adapter.routes.contractUpdate(contract.value.id), contractPayload(), options);
    } else {
        router.post(props.adapter.routes.contractStore(form.id), contractPayload(), options);
    }
}

async function deleteContract() {
    if (!contract.value?.id) return;
    if (!(await confirmDialog({ title: 'Supprimer le contrat', message: 'Supprimer ce contrat ?', confirmLabel: 'Supprimer', danger: true }))) return;

    contractProcessing.value = true;
    router.delete(props.adapter.routes.contractDestroy(contract.value.id), {
        preserveScroll: true,
        onSuccess: reloadContract,
        onFinish: () => { contractProcessing.value = false; },
    });
}
</script>
