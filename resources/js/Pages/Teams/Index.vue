<template>
    <Head title="Équipes" />

    <AuthenticatedLayout>
        <template #header>
            <H2>Équipes de la base</H2>
        </template>

        <div class="p-4 sm:p-6 max-w-8xl mx-auto flex flex-col gap-4">
            <EditorSubNav
                :back="{ href: route('dataBaseMenu'), label: 'Base de données' }"
                :links="[
                    { href: route('players.index'), label: 'Joueurs' },
                    { href: route('teams.index'), label: 'Équipes', active: true },
                    { href: route('contracts.index'), label: 'Contrats' },
                    { href: route('nations.index'), label: 'Pays' },
                    { href: route('users.index'), label: 'Utilisateurs' },
                ]"
            />

            <EntityEditor
                title="Équipes"
                :items="teams"
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
                    <span class="truncate text-sm font-medium">{{ item.name }}</span>
                </template>

                <template #badges>
                    <Badge v-if="eleven">OVR {{ eleven.ovr }}</Badge>
                </template>

                <Panel as="div" class="sm:p-6">
                    <template v-if="!editor.creating.value">
                        <PlayerStatsPreview v-if="eleven" :stats="eleven.stats" :player-id="form.id" />
                        <p v-if="eleven" class="-mt-2 mb-4 text-[11px] text-slate-400">
                            Moyenne par stat des {{ eleven.size }} titulaires que l'IA aligne (OVR {{ eleven.ovr }}).
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
import { computed, ref } from 'vue';

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
