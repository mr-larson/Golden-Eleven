<script setup>
import { computed, onMounted, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import PlayerDetailModal from '@/Components/PlayerDetailModal.vue';
import { positionBadgeClass, positionShort } from '@/Enums/positionBadges.js';

/**
 * Panneau « Ajouter un joueur » d'une équipe (affiché à côté de son effectif) : choisit parmi les joueurs sans contrat dans la période et crée le contrat
 * en un clic (salaire proposé d'après les stats, modifiable ensuite depuis l'onglet Contrats).
 *
 *  - teamId  : équipe qui signe le joueur ;
 *  - players : joueurs libres (prop `freePlayers`, chargée à l'ouverture) ou null tant qu'elle n'est pas arrivée ;
 *  - signUrl / signPayload : route et données du contrat créé (la partie a ses propres contrats).
 */
const props = defineProps({
    teamId:  { type: Number, required: true },
    players: { type: Array, default: null },
    // Signature : adresse et charge utile d'un contrat (défaut : contrats de la base).
    signUrl:     { type: Function, default: () => route('contracts.store') },
    signPayload: { type: Function, default: null },
});

const query = ref('');
const position = ref('ALL');
const signing = ref(null);
const detailPlayer = ref(null);   // joueur dont la fiche détaillée est ouverte
const error = ref('');

// Durée par défaut d'un nouveau contrat, la même que le formulaire de l'écran Contrats.
const MATCHES_TOTAL = 10;
const SHOWN = 60;

// Le panneau n'existe que quand il est ouvert : les joueurs libres se chargent à l'affichage.
onMounted(() => router.reload({ only: ['freePlayers'] }));

const filtered = computed(() => {
    const q = query.value.trim().toLowerCase();

    return (props.players ?? [])
        .filter(p => position.value === 'ALL' || p.position === position.value)
        .filter(p => !q || `${p.name} ${p.nationality ?? ''}`.toLowerCase().includes(q));
});

function sign(player) {
    signing.value = player.id;
    error.value = '';
    const payload = props.signPayload
        ? props.signPayload(player, props.teamId)
        : { team_id: props.teamId, player_id: player.id, salary: player.salary, matches_total: MATCHES_TOTAL };
    router.post(props.signUrl(player), payload, {
        preserveScroll: true,
        onSuccess: () => router.reload({ only: ['freePlayers'] }),
        onError: (errors) => { error.value = Object.values(errors)[0] ?? 'Signature impossible.'; },
        onFinish: () => { signing.value = null; },
    });
}
</script>

<template>
    <section class="min-w-0 rounded-xl border border-slate-200 bg-slate-50 p-3 sm:p-4" aria-label="Ajouter un joueur">
        <h3 class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-500">Joueurs libres</h3>
        <div class="mb-3 flex flex-wrap items-center gap-2">
            <input v-model="query" type="search" placeholder="Rechercher un joueur libre…" aria-label="Rechercher un joueur libre"
                   class="min-w-0 flex-1 rounded-full border border-slate-300 bg-white px-4 py-1.5 text-sm" />
            <select v-model="position" aria-label="Poste" class="rounded-full border border-slate-300 bg-white py-1.5 pl-3 pr-7 text-sm">
                <option value="ALL">Tous les postes</option>
                <option value="Goalkeeper">Gardiens</option>
                <option value="Defender">Défenseurs</option>
                <option value="Midfielder">Milieux</option>
                <option value="Forward">Attaquants</option>
            </select>
        </div>
        <p class="mb-2 text-xs text-slate-500">{{ players === null ? 'Chargement…' : `${filtered.length} joueur(s) libre(s)` }}</p>
        <p v-if="error" class="mb-2 text-xs text-rose-600" role="alert">{{ error }}</p>

        <ul v-if="players !== null" class="max-h-[32rem] divide-y divide-slate-200 overflow-y-auto rounded-lg border border-slate-200 bg-white">
            <li v-for="p in filtered.slice(0, SHOWN)" :key="p.id" class="flex items-center gap-2 px-3 py-2 text-sm">
                <span v-if="p.position" :class="positionBadgeClass(p.position)" class="w-11 shrink-0 rounded-full px-2 py-0.5 text-center text-2xs font-bold">{{ positionShort(p.position) }}</span>
                <button type="button" class="min-w-0 flex-1 text-left" :title="`Voir la fiche de ${p.name}`" @click="detailPlayer = p">
                    <span class="block truncate font-medium text-slate-700 hover:text-teal-700">{{ p.name }}</span>
                    <span class="block truncate text-2xs text-slate-500">{{ p.nationality ?? '—' }} · {{ p.salary }} / sem.</span>
                </button>
                <span v-if="p.ovr" class="w-10 shrink-0 text-right text-xs font-semibold tabular-nums text-slate-600">{{ Number(p.ovr).toFixed(1) }}</span>
                <button type="button" :disabled="signing !== null" class="shrink-0 rounded-full border border-teal-500 px-3 py-1 text-xs font-bold text-teal-700 hover:bg-teal-50 disabled:opacity-50" @click="sign(p)">
                    {{ signing === p.id ? '…' : 'Signer' }}
                </button>
            </li>
            <li v-if="!filtered.length" class="px-3 py-6 text-center text-sm italic text-slate-500">Aucun joueur libre ne correspond.</li>
            <li v-else-if="filtered.length > SHOWN" class="px-3 py-2 text-center text-xs text-slate-500">Affine la recherche pour voir les {{ filtered.length - SHOWN }} autres.</li>
        </ul>
    <PlayerDetailModal :player="detailPlayer" @close="detailPlayer = null" />
    </section>
</template>
