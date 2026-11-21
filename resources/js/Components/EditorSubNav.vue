<script setup>
import { Link, router } from '@inertiajs/vue3';

// Sous-navigation des écrans d'édition : lien retour + pastilles de section,
// et (si `period` est fourni) le sélecteur de période de la base (Collège / Professionnel).
defineProps({
    back: { type: Object, required: true }, // { href, label }
    links: { type: Array, required: true }, // [{ href, label, active }]
    period: { type: String, default: null }, // période affichée ; null = pas de sélecteur
});

const PERIODS = [{ key: 'college', label: 'Collège' }, { key: 'pro', label: 'Professionnel' }];

// Recharge l'écran courant sur la période choisie (le choix est mémorisé en session côté serveur).
function changePeriod(event) {
    router.get(window.location.pathname, { period: event.target.value }, { preserveScroll: true });
}
</script>

<template>
    <div class="flex flex-wrap items-center gap-2">
        <Link :href="back.href"
              class="text-xs font-semibold px-3 py-1.5 rounded-full border border-slate-300 bg-white text-slate-500 hover:bg-slate-50 transition-all">
            ← {{ back.label }}
        </Link>
        <span class="w-px h-5 bg-slate-200"></span>
        <Link v-for="l in links" :key="l.href" :href="l.href"
              class="text-xs font-bold px-3 py-1.5 rounded-full transition-all"
              :class="l.active
                  ? 'bg-teal-500 text-white shadow-sm'
                  : 'border border-slate-300 bg-white text-slate-600 hover:bg-slate-50'">
            {{ l.label }}
        </Link>
        <template v-if="period">
            <span class="w-px h-5 bg-slate-200"></span>
            <label class="flex items-center gap-1.5 text-xs font-bold text-slate-500">
                Période
                <select :value="period" @change="changePeriod" aria-label="Période de la base"
                        class="rounded-full border border-slate-300 bg-white py-1 pl-3 pr-7 text-xs font-bold text-slate-600">
                    <option v-for="p in PERIODS" :key="p.key" :value="p.key">{{ p.label }}</option>
                </select>
            </label>
        </template>
    </div>
</template>
