<script setup>
// Barre de filtres commune des écrans d'édition : menus (slot) + réinitialisation + compteur.
defineProps({
    label: { type: String, default: 'Filtres' },
    count: { type: Number, required: true },
    total: { type: Number, required: true },
    unit: { type: String, default: 'éléments' },
});
defineEmits(['reset']);
</script>

<template>
    <div class="flex flex-wrap xl:flex-nowrap justify-end items-center gap-2" :aria-label="label">
        <slot />
        <button type="button" class="inline-flex items-center justify-center p-2 text-teal-700 hover:text-teal-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-teal-600"
                title="Réinitialiser les filtres" aria-label="Réinitialiser les filtres" @click="$emit('reset')">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M3 10a9 9 0 1 1 2.6 8.4" />
                <path d="M3 4v6h6" />
            </svg>
        </button>
        <span class="py-2 text-xs text-slate-500 whitespace-nowrap shrink-0" aria-live="polite">{{ count }} / {{ total }} {{ unit }}</span>
    </div>
</template>
