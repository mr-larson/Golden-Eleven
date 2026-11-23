<script setup>
import EditorFilterSelect from '@/Components/EditorFilterSelect.vue';
import EditorPeriodSelect from '@/Components/EditorPeriodSelect.vue';

// Barre de filtres commune à TOUS les écrans d'édition. L'ordre est fixé ici, une seule fois,
// du plus général au plus précis :
//   période → univers → nation → club → poste → autres filtres → tri → ordre → réinitialiser.
// Chaque écran ne remplit que les emplacements (slots) dont il a besoin :
//   origin, nation, club, position, extra  (menus : <EditorFilterSelect> ou composants dédiés)
//   sort                                   (options du tri : le menu « Ordre » suit tout seul)
// Pour changer l'ordre ou l'apparence, modifier ce fichier suffit.
defineProps({
    label: { type: String, default: 'Filtres' },
    // Période de la base affichée ; null = pas de sélecteur (parties, pays…).
    period: { type: String, default: null },
    // Tri : clé courante et sens (v-model:sort / v-model:direction) ; sans `sort`, pas de menu de tri.
    sort: { type: String, default: null },
    direction: { type: String, default: 'desc' },
    // Clés de tri sans sens (ordre alphabétique) : le menu « Ordre » est alors masqué.
    sortNoDirection: { type: Array, default: () => ['name'] },
});
defineEmits(['reset', 'update:sort', 'update:direction']);
</script>

<template>
    <div class="flex flex-wrap xl:flex-nowrap justify-end items-center gap-2" :aria-label="label">
        <EditorPeriodSelect v-if="period" :period="period" />
        <slot name="origin" />
        <slot name="nation" />
        <slot name="club" />
        <slot name="position" />
        <slot name="extra" />
        <EditorFilterSelect v-if="sort !== null" :model-value="sort" label="Trier par" :highlight="false"
                            @update:model-value="$emit('update:sort', $event)">
            <slot name="sort" />
        </EditorFilterSelect>
        <!-- Sens du tri : un seul bouton qui bascule (flèche vers le bas = décroissant). -->
        <button v-if="sort !== null && !sortNoDirection.includes(sort)" type="button"
                class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-slate-300 bg-white text-slate-600 hover:bg-slate-50"
                :title="direction === 'desc' ? 'Tri décroissant (cliquer pour inverser)' : 'Tri croissant (cliquer pour inverser)'"
                :aria-label="direction === 'desc' ? 'Tri décroissant' : 'Tri croissant'"
                @click="$emit('update:direction', direction === 'desc' ? 'asc' : 'desc')">
            <svg class="h-4 w-4 transition-transform" :class="direction === 'asc' ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M12 5v14" /><path d="m6 13 6 6 6-6" />
            </svg>
        </button>
        <button type="button" class="inline-flex items-center justify-center p-2 text-teal-700 hover:text-teal-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-teal-600"
                title="Réinitialiser les filtres" aria-label="Réinitialiser les filtres" @click="$emit('reset')">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M3 10a9 9 0 1 1 2.6 8.4" />
                <path d="M3 4v6h6" />
            </svg>
        </button>
    </div>
</template>
