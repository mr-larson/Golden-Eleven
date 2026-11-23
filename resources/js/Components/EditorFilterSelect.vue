<script setup>
import { computed } from 'vue';
import { fieldClass } from '@/Components/fieldStyles';

// Menu déroulant d'une barre de filtres d'édition (options passées en slot).
// Un filtre qui n'est plus sur « ALL » (valeur neutre) est surligné en teal : on voit d'un coup d'œil
// lesquels réduisent la liste. `highlight=false` pour les menus de tri (pas de valeur neutre).
const props = defineProps({
    modelValue: { type: String, required: true },
    label: { type: String, required: true },
    highlight: { type: Boolean, default: true },
});
const active = computed(() => props.highlight && props.modelValue !== 'ALL');
defineEmits(['update:modelValue']);
const field = fieldClass({ size: 'sm', border: 'border-slate-300' });
</script>

<template>
    <label class="text-xs text-slate-500 xl:min-w-0 xl:shrink">
        <span class="sr-only">{{ label }}</span>
        <select :value="modelValue" class="block w-full max-w-[9.5rem]" :class="[field, active ? '!border-teal-500 !bg-teal-50 !text-teal-800 font-semibold' : '']"
                @change="$emit('update:modelValue', $event.target.value)">
            <slot />
        </select>
    </label>
</template>
