<script setup>
import { fieldClass } from '@/Components/fieldStyles';

// Menu déroulant d'une barre de filtres d'édition (options passées en slot).
defineProps({
    modelValue: { type: String, required: true },
    label: { type: String, required: true },
});
defineEmits(['update:modelValue']);
const field = fieldClass({ size: 'sm', border: 'border-slate-300' });
</script>

<template>
    <label class="text-xs text-slate-500 xl:min-w-0 xl:shrink">
        <span class="sr-only">{{ label }}</span>
        <select :value="modelValue" class="block w-full max-w-[220px]" :class="field"
                @change="$emit('update:modelValue', $event.target.value)">
            <slot />
        </select>
    </label>
</template>
