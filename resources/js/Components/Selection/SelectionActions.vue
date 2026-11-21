<script setup>
import { computed } from 'vue';
import ButtonPrimary from '@/Components/ButtonPrimary.vue';

/**
 * Boutons d'action des écrans de choix (équipe d'une Ligue, nation d'une Coupe) :
 *  1. « Jouer avec X » — l'élément affiché devient le siège 1 ;
 *  2. « Ajouter en joueur N (hot-seat) » / « Retirer le joueur N » ;
 *  3. « Imposer comme adversaire » — les places non imposées sont tirées au sort ;
 *  4. bouton d'étape suivante (« Suivant → », « Lancer la partie → »).
 *
 *  - name        : nom de l'élément affiché ;
 *  - seat        : siège humain de l'élément (1 = principal), null s'il n'est pas piloté ;
 *  - humanCount  : nombre de managers humains déjà retenus ;
 *  - canAddHuman : le hot-seat est possible sur cet écran ;
 *  - canImpose   : l'imposition d'adversaires a un sens (nombre de participants réduit) ;
 *  - imposed     : l'élément est déjà imposé comme adversaire ;
 *  - ownedLabel  : libellé du bouton 1 quand l'élément est déjà le siège 1 ;
 *  - nextLabel   : libellé du bouton d'étape suivante ;
 *  - processing  : envoi en cours (désactive le dernier bouton).
 * Émet : main, toggle-human, toggle-imposed, next.
 */
const props = defineProps({
    name:        { type: String, required: true },
    seat:        { type: Number, default: null },
    humanCount:  { type: Number, default: 0 },
    canAddHuman: { type: Boolean, default: true },
    canImpose:   { type: Boolean, default: true },
    imposed:     { type: Boolean, default: false },
    ownedLabel:  { type: String, default: '✓ Ta sélection' },
    nextLabel:   { type: String, default: 'Suivant →' },
    processing:  { type: Boolean, default: false },
});
defineEmits(['main', 'toggle-human', 'toggle-imposed', 'next']);

const hasMain  = computed(() => props.humanCount > 0);
const isMain   = computed(() => props.seat === 1);
const isHuman  = computed(() => props.seat !== null);
const others   = computed(() => hasMain.value && !isMain.value);
</script>

<template>
    <button type="button" class="w-full min-h-11 px-4 rounded-full font-bold text-sm transition-colors text-white"
            :class="isMain ? 'bg-teal-700' : 'bg-teal-500 hover:bg-teal-600 shadow-md'"
            @click="$emit('main')">
        {{ isMain ? ownedLabel : `Jouer avec ${name}` }}
    </button>
    <button v-if="others && canAddHuman" type="button"
            class="w-full min-h-11 px-4 rounded-full font-bold text-sm border transition-colors"
            :class="isHuman ? 'bg-white text-rose-600 border-rose-300 hover:bg-rose-50' : 'bg-[oklch(64.5%_0.246_16.439)] hover:bg-[oklch(58.6%_0.253_17.585)] text-white border-transparent shadow-md'"
            @click="$emit('toggle-human')">
        {{ isHuman ? `Retirer le joueur ${seat}` : `Ajouter en joueur ${humanCount + 1} (hot-seat)` }}
    </button>
    <button v-if="others && canImpose && !isHuman" type="button"
            class="w-full min-h-11 px-4 rounded-full font-bold text-sm border transition-colors"
            :class="imposed ? 'bg-amber-500 text-white border-amber-600' : 'bg-white text-amber-700 border-amber-300 hover:bg-amber-50'"
            @click="$emit('toggle-imposed')">
        {{ imposed ? '✓ Adversaire imposé' : 'Imposer comme adversaire' }}
    </button>
    <!-- Les places non imposées sont complétées au hasard jusqu'au nombre de participants choisi -->
    <ButtonPrimary v-if="hasMain" type="button" tone="teal" size="lg" class="w-full" :disabled="processing" @click="$emit('next')">
        {{ processing ? 'Chargement...' : nextLabel }}
    </ButtonPrimary>
</template>
