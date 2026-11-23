<script setup>
import EditorFilterSelect from '@/Components/EditorFilterSelect.vue';

// Filtre « club » des écrans d'édition (joueurs, contrats) ; `withFree` ajoute « Joueurs libres ».
defineProps({
    modelValue: { type: String, required: true },
    teams: { type: Array, default: () => [] },
    withFree: { type: Boolean, default: false },
});
defineEmits(['update:modelValue']);
</script>

<template>
    <EditorFilterSelect :model-value="modelValue" label="Équipe" @update:model-value="$emit('update:modelValue', $event)">
        <option value="ALL">Équipe</option>
        <option v-if="withFree" value="FREE">Joueurs libres</option>
        <option v-for="team in teams" :key="team.id" :value="String(team.id)">{{ team.name }}</option>
    </EditorFilterSelect>
</template>
