<script setup>
import { Link } from '@inertiajs/vue3';
import { positionBadgeClass, positionShort } from '@/Enums/positionBadges.js';

/**
 * Onglet « Contrats » de la fiche équipe (base de données) : seuls les contrats de l'équipe sélectionnée,
 * avec un lien vers chaque contrat. (L'onglet « Joueurs » utilise l'effectif partagé SelectionRoster.)
 *
 *  - roster : lignes { contract_id, name, nationality, salary, is_captain, player: { position… } }.
 */
defineProps({
    roster: { type: Array, required: true },
});
</script>

<template>
    <div v-if="!roster.length" class="py-10 text-center text-sm italic text-slate-500">
        Aucun contrat pour cette équipe.
    </div>
    <ul v-else class="divide-y divide-slate-200 rounded-xl border border-slate-200 bg-white">
        <li v-for="row in roster" :key="row.contract_id">
            <Link :href="route('contracts.index', { selected: row.contract_id })"
                  class="flex items-center gap-3 px-4 py-2.5 text-sm transition-colors hover:bg-slate-50">
                <span v-if="row.player.position" :class="positionBadgeClass(row.player.position)" class="w-12 shrink-0 rounded-full px-2 py-0.5 text-center text-2xs font-bold">
                    {{ positionShort(row.player.position) }}
                </span>
                <span class="min-w-0 flex-1 truncate font-medium text-slate-700">
                    {{ row.name }}
                    <span v-if="row.is_captain" class="ml-1 rounded bg-amber-100 px-1.5 py-0.5 text-2xs font-bold text-amber-700" title="Capitaine">C</span>
                </span>
                <span v-if="row.nationality" class="hidden shrink-0 text-xs text-slate-500 sm:inline">{{ row.nationality }}</span>
                <span class="w-24 shrink-0 text-right text-xs font-semibold tabular-nums text-slate-600">{{ row.salary }} / sem.</span>
            </Link>
        </li>
    </ul>
</template>
