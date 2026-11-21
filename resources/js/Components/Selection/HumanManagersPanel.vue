<script setup>
import Panel from '@/Components/Panel.vue';
import SectionTitle from '@/Components/SectionTitle.vue';

/**
 * Récap « Managers humains » des écrans de choix (équipes d'une Ligue, nations d'une Coupe) :
 * liste numérotée des sièges, puis le contenu du slot par défaut (recaps, erreurs…).
 *
 *  - managers    : [{ key, name }] dans l'ordre des sièges ;
 *  - removable   : affiche une croix pour retirer un manager (émet « remove » avec le manager) ;
 *  - emptyText   : phrase affichée tant qu'aucun manager n'est retenu.
 * Slot « icon » (facultatif) : logo ou drapeau du manager, reçoit { manager }.
 */
defineProps({
    managers:  { type: Array, default: () => [] },
    removable: { type: Boolean, default: false },
    emptyText: { type: String, default: 'Aucun manager pour l\'instant.' },
});
defineEmits(['remove']);
</script>

<template>
    <Panel as="div" tone="white">
        <SectionTitle class="mb-2">Managers humains — {{ managers.length }}</SectionTitle>

        <div v-if="managers.length" class="space-y-1.5 mb-3">
            <div v-for="(manager, i) in managers" :key="manager.key"
                 class="flex items-center gap-2 px-2 py-1.5 rounded-lg bg-slate-50 border border-slate-200">
                <span class="w-5 h-5 rounded-full bg-teal-500 text-white text-2xs font-black flex items-center justify-center shrink-0">{{ i + 1 }}</span>
                <slot name="icon" :manager="manager" />
                <span class="flex-1 truncate text-xs font-semibold text-slate-700">{{ manager.name }}</span>
                <button v-if="removable" type="button" :aria-label="`Retirer ${manager.name}`" @click="$emit('remove', manager)"
                        class="shrink-0 text-slate-300 hover:text-rose-500 text-base leading-none">×</button>
            </div>
        </div>
        <p v-else class="text-xs text-slate-500 italic mb-3">{{ emptyText }}</p>

        <slot />
    </Panel>
</template>
