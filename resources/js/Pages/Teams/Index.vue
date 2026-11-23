<template>
    <TeamsEditor :adapter="adapter" v-bind="$props" />
</template>

<script setup>
import TeamsEditor from '@/Components/TeamsEditor.vue';

// Équipes de la base de données (admin) : l'écran est partagé avec les équipes d'une partie
// (TeamsEditor) ; cette page ne fournit que l'adaptateur (navigation, routes).
const props = defineProps({
    // Période de la base affichée (college / pro), choisie dans la sous-navigation.
    period: { type: String, default: 'college' },
    teams: { type: Array, required: true },
    selectedId: { type: Number, default: null },
    freePlayers: { type: Array, default: null },
    startCreate: { type: Boolean, default: false },
});

const adapter = {
    kind: 'base',
    headTitle: 'Équipes',
    heading: 'Équipes de la base',
    period: props.period,
    back: { href: route('dataBaseMenu'), label: 'Base de données' },
    links: [
        { href: route('players.index'), label: 'Joueurs' },
        { href: route('teams.index'), label: 'Équipes', active: true },
        { href: route('contracts.index'), label: 'Contrats' },
        { href: route('nations.index'), label: 'Pays' },
        { href: route('users.index'), label: 'Utilisateurs' },
    ],
    routes: {
        store: () => route('teams.store'),
        update: id => route('teams.update', id),
        destroy: id => route('teams.destroy', id),
    },
    deleteMessage: 'Voulez-vous vraiment supprimer cette équipe ?',
    contractHref: row => route('contracts.index', { selected: row.contract_id }),
    signUrl: () => route('contracts.store'),
    signPayload: null, // charge utile par défaut du sélecteur (contrat de la base)
};
</script>
