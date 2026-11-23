<template>
    <Head :title="adapter.headTitle" />

    <AuthenticatedLayout>
        <template #header>
            <H2>{{ adapter.heading }}</H2>
        </template>

        <div class="p-4 sm:p-6 max-w-8xl mx-auto flex flex-col gap-4">
            <div class="flex flex-wrap xl:flex-nowrap items-center justify-between gap-4">
                <EditorSubNav class="shrink-0" :back="adapter.back" :links="adapter.links" />

                <EditorFilterBar label="Filtres des équipes" class="ml-auto xl:min-w-0" :period="adapter.period ?? null"
                                 v-model:sort="sort.key" v-model:direction="sort.direction" :sort-no-direction="['name', 'country']"
                                 @reset="resetFilters">
                    <template #nation><EditorNationFilter v-model="country" :nations="countries" /></template>
                    <template #sort>
                        <option value="name">Nom (A → Z)</option>
                        <option value="country">Pays (A → Z)</option>
                        <option value="ovr">Note globale du onze</option>
                        <option v-for="stat in PLAYER_STATS" :key="stat.key" :value="stat.key">{{ stat.label }}</option>
                    </template>
                </EditorFilterBar>
            </div>

            <EntityEditor
                title="Équipes"
                :items="filteredTeams"
                :total="teams.length"
                :selected-id="editor.selectedId.value"
                :creating="editor.creating.value"
                :search-text="t => t.name || ''"
                search-placeholder="Rechercher une équipe"
                empty-label="Aucune équipe trouvée."
                add-title="Créer une équipe"
                :tabs="panelTabs"
                v-model:active-tab="activePanel"
                @select="editor.select"
                @create="editor.startCreating"
            >
                <template #item="{ item }">
                    <TeamLogo :team="item" />
                    <span class="flex-1 truncate text-sm font-medium">{{ item.name }}</span>
                    <span v-if="item.eleven" class="text-2xs font-semibold tabular-nums text-slate-500 shrink-0">{{ Number(item.eleven.ovr).toFixed(1) }}</span>
                </template>

                <template #badges>
                    <Badge v-if="eleven">OVR {{ Number(eleven.ovr).toFixed(1) }}</Badge>
                    <Badge v-if="isGame">
                        Budget : {{ Number(form.budget || 0).toLocaleString('fr-FR') }} <GameBanknote />
                    </Badge>
                </template>

                <Panel v-if="activePanel !== 'team' && !editor.creating.value" as="div" class="sm:p-6">
 
                    <div v-if="activePanel === 'players'" class="mb-4">
                        <button type="button" class="rounded-full bg-teal-500 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-teal-600" @click="addingPlayer = !addingPlayer">
                            {{ addingPlayer ? 'Fermer' : '+ Ajouter un joueur' }}
                        </button>
                    </div>

                    <!-- Effectif à gauche, joueurs libres à droite : deux colonnes quand « Ajouter un joueur » est ouvert -->
                    <div :class="activePanel === 'players' && addingPlayer ? 'grid items-start gap-4 lg:grid-cols-2' : ''">
                        <SelectionRoster v-if="activePanel === 'players'" :players="rosterPlayers" empty-text="Aucun joueur sous contrat" />
                        <TeamRosterPanel v-else :roster="roster" :row-href="adapter.contractHref" />
                        <FreePlayerPicker v-if="activePanel === 'players' && addingPlayer" :team-id="form.id" :players="freePlayers"
                                          :sign-url="adapter.signUrl" :sign-payload="adapter.signPayload" />
                    </div>
                </Panel>

                <Panel v-show="activePanel === 'team' || editor.creating.value" as="div" class="sm:p-6">
                    <template v-if="!editor.creating.value">
                        <PlayerStatsPreview v-if="eleven" :stats="eleven.stats" :player-id="form.id" />
                        <p v-if="eleven" class="-mt-2 mb-4 text-[11px] text-slate-400">
                            Moyenne par stat des {{ eleven.size }} titulaires que l'IA aligne (OVR {{ Number(eleven.ovr).toFixed(1) }}).
                        </p>
                        <p v-else class="mb-4 text-sm text-slate-400">
                            Pas assez de joueurs sous contrat pour aligner un onze : pas de statistiques à afficher.
                        </p>
                    </template>

                    <TeamRecordBars v-if="isGame && !editor.creating.value" :wins="form.wins" :draws="form.draws" :losses="form.losses" />

                    <form @submit.prevent="submit">
                        <TeamFormFields
                            :form="form"
                            layout="grid"
                            :show-record="isGame"
                            :show-budget="isGame"
                            :logo-url="logoPath ? `/storage/${logoPath}` : null"
                            @remove-logo="removeLogo"
                        />

                        <ButtonGroup>
                            <ButtonPrimary :disabled="form.processing">
                                {{ editor.creating.value ? "Créer l'équipe" : 'Mettre à jour' }}
                            </ButtonPrimary>
                            <ButtonDanger v-if="!editor.creating.value" class="w-36" type="button"
                                          :disabled="form.processing || !form.id" @click="deleteTeam">
                                Supprimer
                            </ButtonDanger>
                        </ButtonGroup>
                    </form>
                </Panel>
            </EntityEditor>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import Badge from '@/Components/Badge.vue';
import Panel from '@/Components/Panel.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { confirmDialog } from '@/Composables/useDialog.js';
import { computed, reactive, ref, watch } from 'vue';
import { PLAYER_STATS } from '@/Enums/playerStats.js';
import EditorFilterBar from '@/Components/EditorFilterBar.vue';
import EditorNationFilter from '@/Components/EditorNationFilter.vue';

import H2 from '@/Components/H2.vue';
import EntityEditor from '@/Components/EntityEditor.vue';
import EditorSubNav from '@/Components/EditorSubNav.vue';
import PlayerStatsPreview from '@/Components/PlayerStatsPreview.vue';
import TeamLogo from '@/Components/TeamLogo.vue';
import FreePlayerPicker from '@/Components/FreePlayerPicker.vue';
import SelectionRoster from '@/Components/Selection/SelectionRoster.vue';
import TeamRosterPanel from '@/Components/TeamRosterPanel.vue';
import ButtonGroup from '@/Components/ButtonGroup.vue';
import ButtonPrimary from '@/Components/ButtonPrimary.vue';
import ButtonDanger from '@/Components/ButtonDanger.vue';
import GameBanknote from '@/Components/GameBanknote.vue';
import TeamRecordBars from '@/Components/TeamRecordBars.vue';
import TeamFormFields from '@/Components/TeamFormFields.vue';
import { useEntityEditor, clone } from '@/Composables/useEntityEditor.js';

// Écran d'édition des équipes, commun à la base de données (admin) et à une partie.
// Seul l'adaptateur change : libellés, navigation, routes et champs propres à la partie
// (budget, bilan), le reste est identique.
//
// adapter = {
//   kind: 'base' | 'game', headTitle, heading,
//   back: { href, label }, links: [{ href, label, active }], period?,
//   routes: { store(), update(id), destroy(id) },
//   deleteMessage,                         // texte de confirmation de suppression
//   contractHref(row),                     // lien d'une ligne de l'onglet Contrats
//   signUrl(player), signPayload(player, teamId),  // signature d'un joueur libre (contrat)
// }
const props = defineProps({
    adapter: { type: Object, required: true },
    teams: { type: Array, required: true },
    // Équipe à sélectionner à l'ouverture (ex. juste après sa création).
    selectedId: { type: Number, default: null },
    // Joueurs sans contrat, fournis à la demande (ouverture de « Ajouter un joueur »).
    freePlayers: { type: Array, default: null },
    // Ouvre directement une fiche vide (ancienne URL /teams/create).
    startCreate: { type: Boolean, default: false },
});

const isGame = computed(() => props.adapter.kind === 'game');


// Filtre par pays (nationalité majoritaire des joueurs sous contrat).
const country = ref('ALL');
const countries = computed(() => [...new Set(props.teams.map(t => t.country).filter(Boolean))].sort((a, b) => a.localeCompare(b, 'fr')));
const sort = reactive({ key: 'name', direction: 'desc' });
const teamValue = t => sort.key === 'ovr' ? Number(t.eleven?.ovr ?? -1) : Number(t.eleven?.stats?.[sort.key] ?? -1);
const filteredTeams = computed(() => props.teams.filter(t => country.value === 'ALL' || (t.country || 'NONE') === country.value)
    .sort((a, b) => {
        if (sort.key === 'name') return (a.name ?? '').localeCompare(b.name ?? '', 'fr');
        if (sort.key === 'country') return (a.country ?? '~').localeCompare(b.country ?? '~', 'fr') || (a.name ?? '').localeCompare(b.name ?? '', 'fr');
        const diff = teamValue(b) - teamValue(a);
        return sort.direction === 'asc' ? -diff : diff;
    }));
const resetFilters = () => { country.value = 'ALL'; Object.assign(sort, { key: 'name', direction: 'desc' }); };

const BLANK = {
    id: null,
    name: '',
    description: '',
    ...(isGame.value ? { budget: '', wins: 0, draws: 0, losses: 0 } : {}),
    logo: null,
    remove_logo: false,
    tactical_style: 'balanced',
    management_philosophy: 'collective',
};

const form = useForm(clone(BLANK));

// Profil du onze type du club sélectionné (null : pas de onze possible).
const eleven = computed(() => props.teams.find(t => t.id === form.id)?.eleven ?? null);

// Contrats de l'équipe sélectionnée (onglets Joueurs et Contrats : seuls ceux liés à cette équipe).
const roster = computed(() => props.teams.find(t => t.id === form.id)?.roster ?? []);

// Joueurs de l'équipe sous la forme attendue par l'effectif partagé (cartes + fiche détaillée au clic).
const rosterPlayers = computed(() => roster.value.map(r => r.player));

// Onglets de la fiche : l'équipe, puis ses joueurs et ses contrats (pas en création, l'équipe n'existe pas encore).
const activePanel = ref('team');
const addingPlayer = ref(false);   // panneau « Ajouter un joueur » ouvert à côté de l'effectif
const panelTabs = computed(() => editor.creating.value
    ? [{ key: 'team', label: 'Fiche équipe' }]
    : [
        { key: 'team', label: 'Fiche équipe' },
        { key: 'players', label: `Joueurs (${roster.value.length})` },
        { key: 'contracts', label: `Contrats (${roster.value.length})` },
    ]);

// Logo enregistré : affiché seulement, jamais renvoyé au serveur (il gère seul logo_path).
const logoPath = ref(null);

const editor = useEntityEditor({
    form,
    items: () => props.teams,
    blank: BLANK,
    entityLabel: 'La nouvelle équipe',
    initial: () => ({ selectedId: props.selectedId, startCreate: props.startCreate }),
    apply(team) {
        Object.assign(form, {
            id: team.id,
            name: team.name,
            description: team.description ?? '',
            ...(isGame.value ? { budget: team.budget ?? 0, wins: team.wins ?? 0, draws: team.draws ?? 0, losses: team.losses ?? 0 } : {}),
            tactical_style: team.tactical_style ?? 'balanced',
            management_philosophy: team.management_philosophy ?? 'collective',
            logo: null,
            remove_logo: false,
        });
        logoPath.value = team.logo_path ?? null;
    },
    onCreate() {
        logoPath.value = null;
    },
});

// Une équipe en création n'a pas encore de joueurs ni de contrats : retour à la fiche.
watch(() => editor.creating.value, creating => { if (creating) activePanel.value = 'team'; });

function removeLogo() {
    form.remove_logo = true;
    form.logo = null;
}

function submit() {
    const creating = editor.creating.value;
    if (!creating && !form.id) return;

    form.transform(({ id: _id, ...data }) => data).post(
        creating ? props.adapter.routes.store() : props.adapter.routes.update(form.id),
        {
            preserveScroll: true,
            forceFormData: true,
            // Création : le serveur renvoie l'id de l'équipe créée. Mise à jour : équipe rechargée
            // (logo à jour, fichier et « supprimer le logo » remis à zéro).
            onSuccess: () => editor.reload(creating ? props.selectedId : form.id),
        },
    );
}

async function deleteTeam() {
    if (!form.id) return;
    if (!(await confirmDialog({ title: "Supprimer l'équipe", message: props.adapter.deleteMessage, confirmLabel: 'Supprimer', danger: true }))) return;

    form.delete(props.adapter.routes.destroy(form.id), {
        preserveScroll: true,
        onSuccess: () => editor.afterDelete(),
    });
}
</script>
