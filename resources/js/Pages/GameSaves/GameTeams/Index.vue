<template>
    <TeamsEditor :adapter="adapter" v-bind="$props" />
</template>

<script setup>
import { computed } from 'vue';
import TeamsEditor from '@/Components/TeamsEditor.vue';

// Équipes d'une partie : même écran que la base de données (TeamsEditor),
// cette page ne fournit que l'adaptateur (navigation, routes, contrats de la partie).
const props = defineProps({
    gameSave: { type: Object, required: true },
    teams: { type: Array, required: true }, // GameTeam[]
    selectedId: { type: Number, default: null },
    freePlayers: { type: Array, default: null },
    startCreate: { type: Boolean, default: false },
});

const adapter = computed(() => {
    const gameSave = props.gameSave.id;
    return {
        kind: 'game',
        headTitle: `Équipes — Partie #${gameSave}`,
        heading: 'Équipes de la partie',
        back: { href: route('game-saves.Play', { gameSave }), label: 'Retour à la partie' },
        links: [
            { href: route('game-saves.players.index', { gameSave }), label: 'Joueurs' },
            { href: route('game-saves.teams.index', { gameSave }), label: 'Équipes', active: true },
        ],
        routes: {
            store: () => route('game-saves.teams.store', { gameSave }),
            update: id => route('game-saves.teams.update', { gameSave, team: id }),
            destroy: id => route('game-saves.teams.destroy', { gameSave, team: id }),
        },
        deleteMessage: 'Voulez-vous vraiment supprimer cette équipe de la partie ?',
        // Un contrat de partie se modifie depuis la fiche du joueur (onglet Contrat).
        contractHref: row => route('game-saves.players.index', { gameSave, selected: row.player_id }),
        signUrl: player => route('game-saves.contracts.store', { gameSave, player: player.id }),
        signPayload: (player, teamId) => ({
            game_team_id: teamId,
            salary: player.salary,
            start_week: props.gameSave.week ?? 1,
            end_week: null,
            is_starter: false,
        }),
    };
});
</script>
