<script setup>
import { Link } from '@inertiajs/vue3';

// Sous-navigation des écrans d'édition : lien retour + une icône par section.
// (Le sélecteur de période est dans la barre de filtres : EditorFilterBar.)
// Les sections sont des icônes (plus compactes, plus de place pour les filtres) ; le libellé reste
// en infobulle et pour les lecteurs d'écran. L'icône d'une section se choisit ici, par son libellé :
// un libellé inconnu s'affiche en texte.
defineProps({
    back: { type: Object, required: true }, // { href, label }
    links: { type: Array, required: true }, // [{ href, label, active }]
});

// Tracés SVG (24×24, trait) par libellé de section.
const ICONS = {
    Joueurs: ['M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z', 'M4 21a8 8 0 0 1 16 0'],
    Équipes: ['M9 11a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z', 'M2.5 20a6.5 6.5 0 0 1 13 0', 'M16 4.3a3.5 3.5 0 0 1 0 6.4', 'M17.5 14a6.5 6.5 0 0 1 4 6'],
    Contrats: ['M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8Z', 'M14 3v5h5', 'M9 13h6', 'M9 17h6'],
    Pays: ['M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z', 'M3 12h18', 'M12 3a14 14 0 0 1 0 18', 'M12 3a14 14 0 0 0 0 18'],
    Utilisateurs: ['M12 11a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z', 'M5 20a7 7 0 0 1 14 0', 'M12 14v3', 'M9.5 16l2.5 2 2.5-2'],
};
</script>

<template>
    <div class="flex flex-wrap items-center gap-2">
        <Link :href="back.href"
              class="text-xs font-semibold px-3 py-1.5 rounded-full border border-slate-300 bg-white text-slate-500 hover:bg-slate-50 transition-all">
            ← {{ back.label }}
        </Link>
        <span class="w-px h-5 bg-slate-200"></span>
        <Link v-for="l in links" :key="l.href" :href="l.href" :title="l.label" :aria-label="l.label"
              :aria-current="l.active ? 'page' : undefined"
              class="inline-flex items-center justify-center rounded-full transition-all"
              :class="[
                  ICONS[l.label] ? 'h-9 w-9' : 'text-xs font-bold px-3 py-1.5',
                  l.active
                      ? 'bg-teal-500 text-white shadow-sm'
                      : 'border border-slate-300 bg-white text-slate-600 hover:bg-slate-50',
              ]">
            <svg v-if="ICONS[l.label]" class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path v-for="d in ICONS[l.label]" :key="d" :d="d" />
            </svg>
            <template v-else>{{ l.label }}</template>
        </Link>
    </div>
</template>
