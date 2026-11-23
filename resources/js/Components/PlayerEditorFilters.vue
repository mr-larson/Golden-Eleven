<script setup>
import EditorFilterBar from '@/Components/EditorFilterBar.vue';
import EditorFilterSelect from '@/Components/EditorFilterSelect.vue';
import EditorNationFilter from '@/Components/EditorNationFilter.vue';
import EditorClubFilter from '@/Components/EditorClubFilter.vue';
import { POSITION_GROUPS } from '@/Enums/positionBadges.js';
import { PLAYER_STATS } from '@/Enums/playerStats.js';

defineProps({
    filters: { type: Object, required: true },
    origins: { type: Array, required: true },
    nationalities: { type: Array, default: () => [] },
    teams: { type: Array, required: true },
    period: { type: String, default: null },
});
defineEmits(['reset']);
</script>

<template>
    <EditorFilterBar label="Filtres des joueurs" :period="period"
                     v-model:sort="filters.sort" v-model:direction="filters.direction" @reset="$emit('reset')">
        <template #origin>
            <EditorFilterSelect v-model="filters.origin" label="Univers">
                <option value="ALL">Univers</option>
                <option v-for="origin in origins" :key="origin.key" :value="origin.key">{{ origin.label }}</option>
            </EditorFilterSelect>
        </template>
        <template #nation><EditorNationFilter v-model="filters.nationality" :nations="nationalities" /></template>
        <template #club><EditorClubFilter v-model="filters.team" :teams="teams" with-free /></template>
        <template #position>
            <EditorFilterSelect v-model="filters.position" label="Poste">
                <option value="ALL">Poste</option>
                <option v-for="group in POSITION_GROUPS" :key="group.key" :value="group.key">{{ group.plural }}</option>
            </EditorFilterSelect>
        </template>
        <template #sort>
            <option value="name">Nom (A → Z)</option>
            <option value="overall">Note globale</option>
            <option value="profile">Profil au poste</option>
            <option value="cost">Coût</option>
            <option v-for="stat in PLAYER_STATS" :key="stat.key" :value="stat.key">{{ stat.label }}</option>
        </template>
    </EditorFilterBar>
</template>
