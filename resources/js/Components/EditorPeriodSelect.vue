<script setup>
import { router } from '@inertiajs/vue3';
import { PERIODS as PERIOD_META } from '@/Pages/GameSaves/periods.js';
import { fieldClass } from '@/Components/fieldStyles';

// Sélecteur de période de la base (Collège / Professionnel) des écrans d'édition.
defineProps({ period: { type: String, required: true } });

const PERIODS = Object.entries(PERIOD_META).map(([key, p]) => ({ key, label: p.label }));
const field = fieldClass({ size: 'sm', border: 'border-slate-300' });

// Recharge l'écran courant sur la période choisie (le choix est mémorisé en session côté serveur).
function changePeriod(event) {
    router.get(window.location.pathname, { period: event.target.value }, { preserveScroll: true });
}
</script>

<template>
    <label class="text-xs text-slate-500 xl:min-w-0 xl:shrink">
        <span class="sr-only">Période de la base</span>
        <select :value="period" class="block w-full max-w-[9.5rem]" :class="field" @change="changePeriod">
            <option v-for="p in PERIODS" :key="p.key" :value="p.key">{{ p.label }}</option>
        </select>
    </label>
</template>
