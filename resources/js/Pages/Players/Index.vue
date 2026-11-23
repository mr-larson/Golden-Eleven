<template>
    <PlayersEditor :adapter="adapter" v-bind="$props" />
</template>

<script setup>
import PlayersEditor from '@/Components/PlayersEditor.vue';

// Joueurs de la base de données (admin) : l'écran est partagé avec les joueurs d'une partie
// (PlayersEditor) ; cette page ne fournit que l'adaptateur (navigation, routes, forme des données).
const props = defineProps({
    // Période de la base affichée (college / pro), choisie dans la sous-navigation.
    period: { type: String, default: 'college' },
    players: { type: Array, required: true },
    nationalities: { type: Array, default: () => [] },
    origins: { type: Object, default: () => ({}) },
    teams: { type: Array, required: true },
    positions: { type: Array, required: true },
    positionLabels: { type: Object, required: true },
    selectedId: { type: Number, default: null },
    startCreate: { type: Boolean, default: false },
});

const adapter = {
    kind: 'base',
    headTitle: 'Joueurs',
    heading: 'Joueurs de la base',
    period: props.period,
    back: { href: route('dataBaseMenu'), label: 'Base de données' },
    links: [
        { href: route('players.index'), label: 'Joueurs', active: true },
        { href: route('teams.index'), label: 'Équipes' },
        { href: route('contracts.index'), label: 'Contrats' },
        { href: route('nations.index'), label: 'Pays' },
        { href: route('users.index'), label: 'Utilisateurs' },
    ],
    routes: {
        store: () => route('players.store'),
        update: id => route('players.update', id),
        destroy: id => route('players.destroy', id),
        contractStore: () => route('contracts.store'),
        contractUpdate: id => route('contracts.update', id),
        contractDestroy: id => route('contracts.destroy', id),
    },
};
</script>
