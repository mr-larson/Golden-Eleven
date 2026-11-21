<template>
    <Head title="Pays" />

    <AuthenticatedLayout>
        <template #header>
            <H2>Pays de la base</H2>
        </template>

        <div class="p-4 sm:p-6 max-w-8xl mx-auto flex flex-col gap-4">
            <div class="flex flex-wrap xl:flex-nowrap items-center justify-between gap-4">
                <EditorSubNav class="shrink-0"
                    :back="{ href: route('dataBaseMenu'), label: 'Base de données' }"
                    :links="[
                        { href: route('players.index'), label: 'Joueurs' },
                        { href: route('teams.index'), label: 'Équipes' },
                        { href: route('contracts.index'), label: 'Contrats' },
                        { href: route('nations.index'), label: 'Pays', active: true },
                        { href: route('users.index'), label: 'Utilisateurs' },
                    ]"
                />

                <EditorFilterBar label="Filtres des pays" unit="pays" class="ml-auto xl:min-w-0"
                    :count="filteredNations.length" :total="nations.length" @reset="resetFilters">
                    <EditorFilterSelect v-model="filters.style" label="Style tactique">
                        <option value="ALL">Tous les styles</option>
                        <option v-for="style in TACTICAL_STYLES" :key="style" :value="style">{{ tacticalLabel(style) }}</option>
                    </EditorFilterSelect>
                    <EditorFilterSelect v-model="filters.philosophy" label="Philosophie de gestion">
                        <option value="ALL">Toutes les philosophies</option>
                        <option v-for="philosophy in PHILOSOPHIES" :key="philosophy" :value="philosophy">{{ philosophyLabel(philosophy) }}</option>
                    </EditorFilterSelect>
                    <EditorFilterSelect v-model="filters.sort" label="Trier par">
                        <option value="name">Nom (A → Z)</option>
                        <option value="ovr">Note globale du onze</option>
                        <option v-for="stat in PLAYER_STATS" :key="stat.key" :value="stat.key">{{ stat.label }}</option>
                    </EditorFilterSelect>
                    <EditorFilterSelect v-if="filters.sort !== 'name'" v-model="filters.direction" label="Ordre">
                        <option value="desc">Décroissant</option>
                        <option value="asc">Croissant</option>
                    </EditorFilterSelect>
                </EditorFilterBar>
            </div>

            <EntityEditor
                title="Pays"
                :items="filteredNations"
                :selected-id="editor.selectedId.value"
                :creating="editor.creating.value"
                :can-create="false"
                :search-text="n => n.name || ''"
                search-placeholder="Rechercher un pays"
                empty-label="Aucun pays trouvé."
                :tabs="[{ key: 'nation', label: 'Fiche pays' }]"
                active-tab="nation"
                @select="editor.select"
            >
                <template #item="{ item }">
                    <NationFlag :url="item.flag_url" :name="item.name" />
                    <span class="flex-1 truncate text-sm font-medium">{{ item.name }}</span>
                    <span v-if="item.eleven" class="text-2xs font-semibold tabular-nums text-slate-500 shrink-0">{{ Number(item.eleven.ovr).toFixed(1) }}</span>
                </template>

                <template #badges>
                    <Badge v-if="eleven">OVR {{ Number(eleven.ovr).toFixed(1) }}</Badge>
                    <Badge v-else>Pas de onze possible</Badge>
                </template>

                <Panel as="div" class="sm:p-6">
                    <PlayerStatsPreview v-if="eleven" :stats="eleven.stats" :player-id="form.id" />
                    <p v-if="eleven" class="-mt-2 mb-4 text-[11px] text-slate-400">
                        Moyenne par stat des {{ eleven.size }} titulaires que l'IA aligne (OVR {{ Number(eleven.ovr).toFixed(1) }}).
                    </p>
                    <p v-else class="mb-4 text-sm text-slate-400">
                        Pas assez de joueurs éligibles pour aligner un onze : pas de statistiques à afficher.
                    </p>

                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4 text-slate-700">
                            <FormField layout="grid" label="Style tactique" for="tactical_style" :error="form.errors.tactical_style">
                                <select id="tactical_style" v-model="form.tactical_style" :class="selectClass">
                                    <option v-for="style in TACTICAL_STYLES" :key="style" :value="style">
                                        {{ tacticalLabel(style) }} {{ tacticalIcon(style) }}
                                    </option>
                                </select>
                            </FormField>

                            <FormField layout="grid" label="Philosophie de gestion" for="management_philosophy" :error="form.errors.management_philosophy">
                                <select id="management_philosophy" v-model="form.management_philosophy" :class="selectClass">
                                    <option v-for="philosophy in PHILOSOPHIES" :key="philosophy" :value="philosophy">
                                        {{ philosophyLabel(philosophy) }} {{ philosophyIcon(philosophy) }}
                                    </option>
                                </select>
                            </FormField>

                            <FormField layout="grid" class="sm:col-span-2" label="Description" for="description" :error="form.errors.description">
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="4"
                                    placeholder="Description du pays (optionnel)"
                                    class="mt-1 w-full" :class="TEXTAREA_CLASS"
                                ></textarea>
                            </FormField>
                        </div>

                        <ButtonGroup>
                            <ButtonPrimary :disabled="form.processing || !form.id">Mettre à jour</ButtonPrimary>
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
import { computed, reactive } from 'vue';
import EditorFilterBar from '@/Components/EditorFilterBar.vue';
import EditorFilterSelect from '@/Components/EditorFilterSelect.vue';
import { PLAYER_STATS } from '@/Enums/playerStats.js';

import H2 from '@/Components/H2.vue';
import EntityEditor from '@/Components/EntityEditor.vue';
import EditorSubNav from '@/Components/EditorSubNav.vue';
import FormField from '@/Components/FormField.vue';
import PlayerStatsPreview from '@/Components/PlayerStatsPreview.vue';
import NationFlag from '@/Components/NationFlag.vue';
import ButtonGroup from '@/Components/ButtonGroup.vue';
import ButtonPrimary from '@/Components/ButtonPrimary.vue';
import { fieldClass, TEXTAREA_CLASS } from '@/Components/fieldStyles';
import { useTeamStyles } from '@/Pages/GameSaves/Play/useTeamStyles.js';
import { TACTICAL_STYLES, PHILOSOPHIES } from '@/Enums/teamStyle.js';
import { useEntityEditor, clone } from '@/Composables/useEntityEditor.js';

const props = defineProps({
    nations: { type: Array, required: true },
    // Nation à sélectionner à l'ouverture.
    selectedId: { type: Number, default: null },
});

const { tacticalLabel, tacticalIcon, philosophyLabel, philosophyIcon } = useTeamStyles();
const selectClass = ['mt-1 w-full', fieldClass()];

// Nom : affiché seulement (verrouillé, c'est aussi la valeur de `players.nationality`).
// Filtres (style, philosophie) et tri de la liste.
const filters = reactive({ style: 'ALL', philosophy: 'ALL', sort: 'name', direction: 'desc' });
const nationValue = n => filters.sort === 'ovr' ? Number(n.eleven?.ovr ?? -1) : Number(n.eleven?.stats?.[filters.sort] ?? -1);
const filteredNations = computed(() => props.nations
    .filter(n => (filters.style === 'ALL' || (n.tactical_style ?? 'balanced') === filters.style)
        && (filters.philosophy === 'ALL' || (n.management_philosophy ?? 'collective') === filters.philosophy))
    .sort((a, b) => {
        if (filters.sort === 'name') return (a.name ?? '').localeCompare(b.name ?? '', 'fr');
        const diff = nationValue(b) - nationValue(a);
        return filters.direction === 'asc' ? -diff : diff;
    }));
const resetFilters = () => Object.assign(filters, { style: 'ALL', philosophy: 'ALL', sort: 'name', direction: 'desc' });

const BLANK = { id: null, name: '', tactical_style: 'balanced', management_philosophy: 'collective', description: '' };

const form = useForm(clone(BLANK));

// Profil du onze type de la nation sélectionnée (null : pas de onze possible).
const eleven = computed(() => props.nations.find(n => n.id === form.id)?.eleven ?? null);

const editor = useEntityEditor({
    form,
    items: () => props.nations,
    blank: BLANK,
    entityLabel: 'Le pays',
    initial: () => ({ selectedId: props.selectedId }),
    apply(nation) {
        Object.assign(form, {
            id: nation.id,
            name: nation.name,
            tactical_style: nation.tactical_style ?? 'balanced',
            management_philosophy: nation.management_philosophy ?? 'collective',
            description: nation.description ?? '',
        });
    },
});

function submit() {
    if (!form.id) return;

    form.transform(({ id: _id, name: _name, ...data }) => data).post(route('nations.update', form.id), {
        preserveScroll: true,
        onSuccess: () => editor.reload(form.id),
    });
}
</script>
