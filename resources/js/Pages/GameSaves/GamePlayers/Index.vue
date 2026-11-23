<template>
    <PlayersEditor :adapter="adapter" v-bind="$props" />
</template>

<script setup>
import { computed } from 'vue';
import PlayersEditor from '@/Components/PlayersEditor.vue';

// Joueurs d'une partie : même écran que la base de données (PlayersEditor),
// cette page ne fournit que l'adaptateur (navigation, routes, forme des données).
const props = defineProps({
    gameSave: { type: Object, required: true },
    players: { type: Array, required: true },
    nationalities: { type: Array, default: () => [] },
    origins: { type: Object, default: () => ({}) },
    teams: { type: Array, required: true },
    positions: { type: Array, default: undefined },
    positionLabels: { type: Object, default: () => ({}) },
    selectedId: { type: Number, default: null },
    startCreate: { type: Boolean, default: false },
});

const adapter = computed(() => {
    const gameSave = props.gameSave.id;
    return {
        kind: 'game',
        headTitle: `Joueurs — Partie #${gameSave}`,
        heading: 'Joueurs de la partie',
        gameSave: props.gameSave,
        back: { href: route('game-saves.Play', { gameSave }), label: 'Retour à la partie' },
        links: [
            { href: route('game-saves.players.index', { gameSave }), label: 'Joueurs', active: true },
            { href: route('game-saves.teams.index', { gameSave }), label: 'Équipes' },
        ],
        routes: {
            store: () => route('game-saves.players.store', { gameSave }),
            update: id => route('game-saves.players.update', { gameSave, player: id }),
            destroy: id => route('game-saves.players.destroy', { gameSave, player: id }),
            contractStore: playerId => route('game-saves.contracts.store', { gameSave, player: playerId }),
            contractUpdate: id => route('game-saves.contracts.update', { gameSave, contract: id }),
            contractDestroy: id => route('game-saves.contracts.destroy', { gameSave, contract: id }),
        },
    };
});
</script>
