<script setup>
import EditorFilterSelect from '@/Components/EditorFilterSelect.vue';

// Filtre « nation » des écrans d'édition (joueurs, équipes, contrats) : mêmes libellés partout.
defineProps({
    modelValue: { type: String, required: true },
    nations: { type: Array, default: () => [] },
});
defineEmits(['update:modelValue']);
</script>

<template>
    <EditorFilterSelect :model-value="modelValue" label="Nationalité" @update:model-value="$emit('update:modelValue', $event)">
        <option value="ALL">Nation</option>
        <option value="NONE">Non renseignée</option>
        <option v-for="nation in nations" :key="nation" :value="nation">{{ nation }}</option>
    </EditorFilterSelect>
</template>
