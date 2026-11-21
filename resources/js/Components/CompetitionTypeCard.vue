<script setup>
/**
 * Carte de choix d'un format de compétition (Ligue, Coupe Nation, Coupe Club) à la création d'une partie.
 *
 *  - v-model     : type de compétition sélectionné ;
 *  - value       : type que représente cette carte ;
 *  - icon, title, text : contenu de la carte ;
 *  - tone        : 'teal' | 'indigo' | 'amber' (couleur quand elle est choisie) ;
 *  - disabled    : format indisponible (ex. pas assez de clubs) ; `disabledText` en donne la raison.
 */
const model = defineModel({ type: String, required: true });

defineProps({
    value:        { type: String, required: true },
    icon:         { type: String, required: true },
    title:        { type: String, required: true },
    text:         { type: String, required: true },
    tone:         { type: String, default: 'teal' },
    disabled:     { type: Boolean, default: false },
    disabledText: { type: String, default: '' },
});

// Classes écrites en toutes lettres pour que Tailwind les détecte.
const TONES = {
    teal:   { box: 'border-teal-500 bg-teal-50',     title: 'text-teal-700',   text: 'text-teal-600',   check: 'bg-teal-500' },
    indigo: { box: 'border-indigo-500 bg-indigo-50', title: 'text-indigo-700', text: 'text-indigo-600', check: 'bg-indigo-500' },
    amber:  { box: 'border-amber-500 bg-amber-50',   title: 'text-amber-700',  text: 'text-amber-600',  check: 'bg-amber-500' },
};
</script>

<template>
    <button type="button" :disabled="disabled" @click="model = value"
            class="relative flex flex-col gap-2 p-4 rounded-xl border-2 transition-all text-left disabled:opacity-50 disabled:cursor-not-allowed"
            :class="model === value ? `${TONES[tone].box} shadow-sm` : 'border-slate-300 bg-white hover:border-slate-300'">
        <div class="flex items-center gap-2">
            <span class="text-xl">{{ icon }}</span>
            <span class="text-sm font-bold" :class="model === value ? TONES[tone].title : 'text-slate-700'">{{ title }}</span>
        </div>
        <p class="text-[11px] leading-relaxed" :class="model === value ? TONES[tone].text : 'text-slate-500'">
            {{ disabled && disabledText ? disabledText : text }}
        </p>
        <div v-if="model === value" class="absolute top-2 right-2 w-5 h-5 rounded-full flex items-center justify-center" :class="TONES[tone].check">
            <span class="text-white text-2xs font-bold">✓</span>
        </div>
    </button>
</template>
