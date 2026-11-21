<script setup>
/**
 * Carte de présentation de l'élément affiché au centre des écrans de choix (équipe d'une Ligue,
 * nation d'une Coupe) : visuel, nom, sous-titre, contenu libre, puis un encart chiffré.
 *
 *  - title      : nom de l'élément ;
 *  - subtitle   : phrase sous le nom (description, nombre de joueurs…) ;
 *  - statValue  : valeur de l'encart chiffré (laisser vide pour le masquer) ;
 *  - statLabel  : légende de l'encart ;
 *  - statNote   : précision sous la légende (facultatif) ;
 *  - statTone   : couleur de la valeur (classe Tailwind).
 * Slots : « visual » (logo ou drapeau, dans un cadre 96 px), « default » (badges…), « stat » (remplace la valeur,
 * ex. montant avec icône).
 */
defineProps({
    title:     { type: String, required: true },
    subtitle:  { type: String, default: '' },
    statValue: { type: [String, Number], default: null },
    statLabel: { type: String, default: '' },
    statNote:  { type: String, default: '' },
    statTone:  { type: String, default: 'text-teal-600' },
});
</script>

<template>
    <div class="border border-slate-200 rounded-xl bg-white p-5 shadow-card">
        <div class="flex flex-col items-center gap-3 mb-4">
            <div class="w-24 h-24 rounded-2xl overflow-hidden bg-slate-50 border border-slate-200 flex items-center justify-center text-5xl">
                <slot name="visual" />
            </div>
            <div class="text-center">
                <h2 class="text-lg font-black text-slate-700">{{ title }}</h2>
                <p v-if="subtitle" class="text-xs text-slate-400 mt-0.5">{{ subtitle }}</p>
                <div v-if="$slots.default" class="mt-2 flex justify-center"><slot /></div>
            </div>
        </div>
        <div v-if="statValue !== null || $slots.stat" class="rounded-lg bg-slate-50 p-2.5 text-center text-xs">
            <div class="text-lg font-black" :class="statTone"><slot name="stat">{{ statValue }}</slot></div>
            <div class="font-semibold text-slate-400">{{ statLabel }}</div>
            <div v-if="statNote" class="mt-0.5 text-[10px] leading-snug text-slate-500">{{ statNote }}</div>
        </div>
    </div>
</template>
