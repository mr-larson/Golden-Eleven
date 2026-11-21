<template>
    <Head title="Contrats" />

    <AuthenticatedLayout>
        <template #header>
            <H2>Contrats de la base</H2>
        </template>

        <div class="p-4 sm:p-6 max-w-8xl mx-auto flex flex-col gap-4">
            <div class="flex flex-wrap xl:flex-nowrap items-center justify-between gap-4">
                <EditorSubNav class="shrink-0"
                    :back="{ href: route('dataBaseMenu'), label: 'Base de données' }"
                    :links="[
                        { href: route('players.index'), label: 'Joueurs' },
                        { href: route('teams.index'), label: 'Équipes' },
                        { href: route('contracts.index'), label: 'Contrats', active: true },
                        { href: route('nations.index'), label: 'Pays' },
                        { href: route('users.index'), label: 'Utilisateurs' },
                    ]"
                />

                <EditorFilterBar label="Filtres des contrats" unit="contrats" class="ml-auto xl:min-w-0"
                    :count="filteredContracts.length" :total="contracts.length" @reset="resetFilters">
                    <EditorFilterSelect v-model="filters.nationality" label="Nationalité">
                        <option value="ALL">Toutes les nationalités</option>
                        <option value="NONE">Non renseignée</option>
                        <option v-for="nation in nationalities" :key="nation" :value="nation">{{ nation }}</option>
                    </EditorFilterSelect>
                    <EditorFilterSelect v-model="filters.team" label="Équipe">
                        <option value="ALL">Toutes les équipes</option>
                        <option v-for="team in teams" :key="team.id" :value="String(team.id)">{{ team.name }}</option>
                    </EditorFilterSelect>
                    <EditorFilterSelect v-model="filters.sort" label="Trier par">
                        <option value="name">Nom (A → Z)</option>
                        <option value="team">Équipe (A → Z)</option>
                        <option value="salary">Salaire</option>
                        <option value="remaining">Matchs restants</option>
                        <option value="played">Matchs joués</option>
                    </EditorFilterSelect>
                    <EditorFilterSelect v-if="!['name', 'team'].includes(filters.sort)" v-model="filters.direction" label="Ordre">
                        <option value="desc">Décroissant</option>
                        <option value="asc">Croissant</option>
                    </EditorFilterSelect>
                </EditorFilterBar>
            </div>

            <EntityEditor
                title="Contrats"
                :items="filteredContracts"
                :selected-id="editor.selectedId.value"
                :creating="editor.creating.value"
                :search-text="c => `${c.player_name ?? ''} ${c.team_name ?? ''}`"
                search-placeholder="Joueur ou équipe"
                empty-label="Aucun contrat trouvé."
                add-title="Créer un contrat"
                :tabs="[{ key: 'contract', label: 'Fiche contrat' }]"
                active-tab="contract"
                @select="editor.select"
                @create="editor.startCreating"
            >
                <template #item="{ item, selected }">
                    <TeamLogo :team="{ name: item.team_name, logo_path: item.team_logo_path }" />
                    <span class="flex-1 min-w-0">
                        <span class="block truncate text-sm font-medium">{{ item.player_name ?? 'Joueur supprimé' }}</span>
                        <span class="block truncate text-[11px]" :class="selected ? 'text-white/70' : 'text-slate-400'">
                            {{ item.team_name ?? 'Équipe supprimée' }}
                        </span>
                    </span>
                </template>

                <template #badges>
                    <span v-if="!editor.creating.value" class="text-2xs font-bold px-2 py-0.5 rounded-full"
                          :class="remaining > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700'">
                        {{ remaining }} match{{ remaining > 1 ? 's' : '' }} restant{{ remaining > 1 ? 's' : '' }}
                    </span>
                </template>

                <Panel as="div" class="sm:p-6">
                    <form @submit.prevent="submit" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="player_id" value="Joueur" />
                                <InputSelect id="player_id" v-model="form.player_id" required placeholder="Choisir un joueur…" class="mt-1">
                                    <option v-for="p in players" :key="p.id" :value="p.id">{{ p.firstname }} {{ p.lastname }}</option>
                                </InputSelect>
                                <InputError class="mt-1" :message="form.errors.player_id" />
                            </div>
                            <div>
                                <InputLabel for="team_id" value="Équipe" />
                                <InputSelect id="team_id" v-model="form.team_id" required placeholder="Choisir une équipe…" class="mt-1">
                                    <option v-for="t in teams" :key="t.id" :value="t.id">{{ t.name }}</option>
                                </InputSelect>
                                <InputError class="mt-1" :message="form.errors.team_id" />
                            </div>
                            <div>
                                <InputLabel for="salary" value="Salaire (billets / match)" />
                                <InputText id="salary" type="number" min="0" v-model.number="form.salary" class="mt-1" />
                                <InputError class="mt-1" :message="form.errors.salary" />
                            </div>
                            <div>
                                <InputLabel for="matches_total" value="Durée (matchs)" />
                                <InputText id="matches_total" type="number" min="1" v-model.number="form.matches_total" class="mt-1" />
                                <InputError class="mt-1" :message="form.errors.matches_total" />
                            </div>
                            <div>
                                <InputLabel for="matches_played" value="Matchs joués" />
                                <!-- Un nouveau contrat démarre toujours à 0 match joué (forcé côté serveur). -->
                                <InputText id="matches_played" type="number" min="0" v-model.number="form.matches_played"
                                       :disabled="editor.creating.value" class="mt-1" />
                                <InputError class="mt-1" :message="form.errors.matches_played" />
                            </div>
                            <div>
                                <InputLabel value="Matchs restants" />
                                <InputText type="number" :model-value="remaining" disabled class="mt-1" />
                            </div>
                        </div>

                        <ButtonGroup>
                            <ButtonPrimary :disabled="form.processing">
                                {{ editor.creating.value ? 'Créer le contrat' : 'Mettre à jour' }}
                            </ButtonPrimary>
                            <ButtonDanger v-if="!editor.creating.value" type="button" class="w-36"
                                          :disabled="form.processing || !form.id" @click="deleteContract">
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
import Panel from '@/Components/Panel.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { confirmDialog } from '@/Composables/useDialog.js';
import { computed, reactive } from 'vue';
import EditorFilterBar from '@/Components/EditorFilterBar.vue';
import EditorFilterSelect from '@/Components/EditorFilterSelect.vue';

import H2 from '@/Components/H2.vue';
import EntityEditor from '@/Components/EntityEditor.vue';
import EditorSubNav from '@/Components/EditorSubNav.vue';
import TeamLogo from '@/Components/TeamLogo.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputSelect from '@/Components/InputSelect.vue';
import InputText from '@/Components/InputText.vue';
import InputError from '@/Components/InputError.vue';
import ButtonGroup from '@/Components/ButtonGroup.vue';
import ButtonPrimary from '@/Components/ButtonPrimary.vue';
import ButtonDanger from '@/Components/ButtonDanger.vue';
import { useEntityEditor, clone } from '@/Composables/useEntityEditor.js';

const props = defineProps({
    contracts: { type: Array, required: true },
    teams: { type: Array, required: true },
    players: { type: Array, required: true },
    // Contrat à sélectionner à l'ouverture (ex. juste après sa création).
    selectedId: { type: Number, default: null },
    // Ouvre directement une fiche vide (ancienne URL /contracts/create).
    startCreate: { type: Boolean, default: false },
});

// Filtres de la liste : nationalité du joueur et équipe du contrat.
const filters = reactive({ nationality: 'ALL', team: 'ALL', sort: 'name', direction: 'desc' });
const nationalities = computed(() => [...new Set(props.contracts.map(c => c.player_nationality).filter(Boolean))].sort((a, b) => a.localeCompare(b, 'fr')));
const SORT_VALUES = { salary: c => Number(c.salary ?? 0), remaining: c => Number(c.matches_remaining ?? 0), played: c => Number(c.matches_played ?? 0) };
const filteredContracts = computed(() => props.contracts.filter(c =>
    (filters.nationality === 'ALL' || (c.player_nationality || 'NONE') === filters.nationality)
    && (filters.team === 'ALL' || String(c.team_id) === filters.team))
    .sort((a, b) => {
        if (filters.sort === 'name') return (a.player_name ?? '').localeCompare(b.player_name ?? '', 'fr');
        if (filters.sort === 'team') return (a.team_name ?? '').localeCompare(b.team_name ?? '', 'fr');
        const diff = SORT_VALUES[filters.sort](b) - SORT_VALUES[filters.sort](a);
        return filters.direction === 'asc' ? -diff : diff;
    }));
const resetFilters = () => Object.assign(filters, { nationality: 'ALL', team: 'ALL', sort: 'name', direction: 'desc' });

const BLANK = {
    id: null,
    player_id: '',
    team_id: '',
    salary: '',
    matches_total: 10,
    matches_played: 0,
};

const form = useForm(clone(BLANK));

const remaining = computed(() => Math.max(0, Number(form.matches_total || 0) - Number(form.matches_played || 0)));

const editor = useEntityEditor({
    form,
    items: () => props.contracts,
    blank: BLANK,
    entityLabel: 'Le nouveau contrat',
    initial: () => ({ selectedId: props.selectedId, startCreate: props.startCreate }),
    apply(contract) {
        Object.assign(form, {
            id: contract.id,
            player_id: contract.player_id ?? '',
            team_id: contract.team_id ?? '',
            salary: contract.salary ?? '',
            matches_total: contract.matches_total ?? 1,
            matches_played: contract.matches_played ?? 0,
        });
    },
});

function submit() {
    const creating = editor.creating.value;
    if (!creating && !form.id) return;

    // open_created : le serveur renvoie sur cet écran avec le contrat créé sélectionné
    // (depuis la fiche joueur, il revient simplement à la page d'origine).
    form.transform(({ id: _id, ...data }) => (creating ? { ...data, open_created: true } : data)).post(
        creating ? route('contracts.store') : route('contracts.update', form.id),
        {
            preserveScroll: true,
            onSuccess: () => editor.reload(creating ? props.selectedId : form.id),
        },
    );
}

async function deleteContract() {
    if (!form.id) return;
    if (!(await confirmDialog({ title: 'Supprimer le contrat', message: 'Voulez-vous vraiment supprimer ce contrat ?', confirmLabel: 'Supprimer', danger: true }))) return;

    form.delete(route('contracts.destroy', form.id), {
        preserveScroll: true,
        onSuccess: () => editor.afterDelete(),
    });
}
</script>
