<template>
    <Head title="Pays" />

    <AuthenticatedLayout>
        <template #header>
            <H2>Pays de la base</H2>
        </template>

        <div class="p-4 sm:p-6 max-w-8xl mx-auto flex flex-col gap-4">
            <EditorSubNav
                :back="{ href: route('dataBaseMenu'), label: 'Base de données' }"
                :links="[
                    { href: route('players.index'), label: 'Joueurs' },
                    { href: route('teams.index'), label: 'Équipes' },
                    { href: route('contracts.index'), label: 'Contrats' },
                    { href: route('nations.index'), label: 'Pays', active: true },
                    { href: route('users.index'), label: 'Utilisateurs' },
                ]"
            />

            <EntityEditor
                title="Pays"
                :items="nations"
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
                    <span class="truncate text-sm font-medium">{{ item.name }}</span>
                </template>

                <template #badges>
                    <Badge v-if="eleven">OVR {{ eleven.ovr }}</Badge>
                    <Badge v-else>Pas de onze possible</Badge>
                </template>

                <Panel as="div" class="sm:p-6">
                    <PlayerStatsPreview v-if="eleven" :stats="eleven.stats" :player-id="form.id" />
                    <p v-if="eleven" class="-mt-2 mb-4 text-[11px] text-slate-400">
                        Moyenne par stat des {{ eleven.size }} titulaires que l'IA aligne (OVR {{ eleven.ovr }}).
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
import { computed } from 'vue';

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
