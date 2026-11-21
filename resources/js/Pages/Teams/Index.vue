<template>
    <Head title="Équipes" />

    <AuthenticatedLayout>
        <template #header>
            <H2>Équipes de la base</H2>
        </template>

        <div class="p-4 sm:p-6 max-w-8xl mx-auto flex flex-col gap-4">
            <div class="flex flex-wrap xl:flex-nowrap items-center justify-between gap-4">
                <EditorSubNav class="shrink-0"
                    :back="{ href: route('dataBaseMenu'), label: 'Base de données' }"
                    :links="[
                        { href: route('players.index'), label: 'Joueurs' },
                        { href: route('teams.index'), label: 'Équipes', active: true },
                        { href: route('contracts.index'), label: 'Contrats' },
                        { href: route('nations.index'), label: 'Pays' },
                        { href: route('users.index'), label: 'Utilisateurs' },
                    ]"
                />

                <EditorFilterBar label="Filtres des équipes" unit="équipes" class="ml-auto xl:min-w-0"
                    :count="filteredTeams.length" :total="teams.length" @reset="resetFilters">
                    <EditorFilterSelect v-model="country" label="Pays">
                        <option value="ALL">Tous les pays</option>
                        <option value="NONE">Non renseigné</option>
                        <option v-for="c in countries" :key="c" :value="c">{{ c }}</option>
                    </EditorFilterSelect>
                    <EditorFilterSelect v-model="sort.key" label="Trier par">
                        <option value="name">Nom (A → Z)</option>
                        <option value="country">Pays (A → Z)</option>
                        <option value="ovr">Note globale du onze</option>
                        <option v-for="stat in PLAYER_STATS" :key="stat.key" :value="stat.key">{{ stat.label }}</option>
                    </EditorFilterSelect>
                    <EditorFilterSelect v-if="!['name', 'country'].includes(sort.key)" v-model="sort.direction" label="Ordre">
                        <option value="desc">Décroissant</option>
                        <option value="asc">Croissant</option>
                    </EditorFilterSelect>
                </EditorFilterBar>
            </div>

            <EntityEditor
                title="Équipes"
                :items="filteredTeams"
                :selected-id="editor.selectedId.value"
                :creating="editor.creating.value"
                :search-text="t => t.name || ''"
                search-placeholder="Rechercher une équipe"
                empty-label="Aucune équipe trouvée."
                add-title="Créer une équipe"
                :tabs="[{ key: 'team', label: 'Fiche équipe' }]"
                active-tab="team"
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
                </template>

                <Panel as="div" class="sm:p-6">
                    <template v-if="!editor.creating.value">
                        <PlayerStatsPreview v-if="eleven" :stats="eleven.stats" :player-id="form.id" />
                        <p v-if="eleven" class="-mt-2 mb-4 text-[11px] text-slate-400">
                            Moyenne par stat des {{ eleven.size }} titulaires que l'IA aligne (OVR {{ Number(eleven.ovr).toFixed(1) }}).
                        </p>
                        <p v-else class="mb-4 text-sm text-slate-400">
                            Pas assez de joueurs sous contrat pour aligner un onze : pas de statistiques à afficher.
                        </p>
                    </template>

                    <form @submit.prevent="submit">
                        <TeamFormFields
                            :form="form"
                            layout="grid"
                            :show-record="false"
                            :show-budget="false"
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
import { computed, reactive, ref } from 'vue';
import { PLAYER_STATS } from '@/Enums/playerStats.js';
import EditorFilterBar from '@/Components/EditorFilterBar.vue';
import EditorFilterSelect from '@/Components/EditorFilterSelect.vue';

import H2 from '@/Components/H2.vue';
import EntityEditor from '@/Components/EntityEditor.vue';
import EditorSubNav from '@/Components/EditorSubNav.vue';
import PlayerStatsPreview from '@/Components/PlayerStatsPreview.vue';
import TeamLogo from '@/Components/TeamLogo.vue';
import ButtonGroup from '@/Components/ButtonGroup.vue';
import ButtonPrimary from '@/Components/ButtonPrimary.vue';
import ButtonDanger from '@/Components/ButtonDanger.vue';
import TeamFormFields from '@/Components/TeamFormFields.vue';
import { useEntityEditor, clone } from '@/Composables/useEntityEditor.js';

const props = defineProps({
    teams: { type: Array, required: true },
    // Équipe à sélectionner à l'ouverture (ex. juste après sa création).
    selectedId: { type: Number, default: null },
    // Ouvre directement une fiche vide (ancienne URL /teams/create).
    startCreate: { type: Boolean, default: false },
});

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
    logo: null,
    remove_logo: false,
    tactical_style: 'balanced',
    management_philosophy: 'collective',
};

const form = useForm(clone(BLANK));

// Profil du onze type du club sélectionné (null : pas de onze possible).
const eleven = computed(() => props.teams.find(t => t.id === form.id)?.eleven ?? null);

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

function removeLogo() {
    form.remove_logo = true;
    form.logo = null;
}

function submit() {
    const creating = editor.creating.value;
    if (!creating && !form.id) return;

    form.transform(({ id: _id, ...data }) => data).post(
        creating ? route('teams.store') : route('teams.update', form.id),
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
    if (!(await confirmDialog({ title: "Supprimer l'équipe", message: 'Voulez-vous vraiment supprimer cette équipe ?', confirmLabel: 'Supprimer', danger: true }))) return;

    form.delete(route('teams.destroy', form.id), {
        preserveScroll: true,
        onSuccess: () => editor.afterDelete(),
    });
}
</script>
