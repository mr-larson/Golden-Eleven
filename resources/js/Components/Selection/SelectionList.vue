<script setup>
import { computed, ref } from 'vue';
import { fieldClass } from '@/Components/fieldStyles';

/**
 * Colonne de gauche des écrans de choix (équipes d'une Ligue, nations d'une Coupe) : recherche par nom,
 * compteur et liste défilante. Le contenu de chaque ligne vient du slot « item » (logo/drapeau, note, pastilles).
 *
 *  - items       : éléments de la liste (objets avec `name`) ;
 *  - itemKey     : fonction qui renvoie la clé d'un élément ;
 *  - selectedKey : clé de l'élément affiché au centre ;
 *  - isActive    : vrai pour un élément déjà retenu (équipe pilotée, nation choisie…), mis en avant en plein ;
 *  - disabled    : élément non cliquable (ex. nation incomplète).
 */
const props = defineProps({
    items:       { type: Array, required: true },
    itemKey:     { type: Function, default: (item) => item.id },
    selectedKey: { type: [String, Number], default: null },
    isActive:    { type: Function, default: () => false },
    disabled:    { type: Function, default: () => false },
    unit:        { type: String, default: 'équipe(s)' },
    emptyText:   { type: String, default: 'Aucun résultat' },
});
const emit = defineEmits(['select']);

const query = ref('');
const filtered = computed(() => {
    const q = query.value.trim().toLowerCase();
    return q ? props.items.filter(i => i.name?.toLowerCase().includes(q)) : props.items;
});
</script>

<template>
    <!-- overflow-hidden + min-h-0 : la carte ne pilote pas la hauteur de la rangée — elle s'aligne
         sur la colonne du milieu. La liste défile à l'intérieur. -->
    <div class="col-span-1 lg:col-span-2 border border-slate-200 rounded-xl bg-white p-3 flex flex-col gap-2 min-h-0 max-h-64 lg:max-h-none overflow-hidden shadow-card">
        <div class="relative shrink-0">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 20 20">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
            </svg>
            <input type="search" v-model="query" class="w-full pl-8 pr-3 py-1.5" :class="fieldClass({ size: 'sm' })" placeholder="Rechercher..."/>
        </div>

        <div class="text-2xs text-slate-400 font-semibold px-1 shrink-0">{{ filtered.length }} {{ unit }}</div>

        <div class="flex-1 min-h-0 overflow-y-auto space-y-1 pr-0.5">
            <button v-for="item in filtered" :key="itemKey(item)" type="button"
                    :disabled="disabled(item)" @click="emit('select', item)"
                    class="w-full flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-xs font-semibold border transition-all text-left disabled:opacity-50 disabled:cursor-not-allowed"
                    :class="isActive(item)
                        ? 'bg-teal-500 text-white border-teal-600 shadow-sm'
                        : (selectedKey === itemKey(item)
                            ? 'bg-teal-50 text-slate-700 border-teal-300 ring-1 ring-teal-200'
                            : 'bg-white text-slate-600 border-slate-300 hover:border-teal-200 hover:bg-teal-50')">
                <slot name="item" :item="item" :active="isActive(item)" />
            </button>
            <div v-if="!filtered.length" class="text-xs text-slate-500 italic text-center py-4">{{ emptyText }}</div>
        </div>
    </div>
</template>
