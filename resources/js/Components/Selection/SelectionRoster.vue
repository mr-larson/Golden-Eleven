<script setup>
import { computed, ref } from 'vue';
import Modal from '@/Components/Modal.vue';
import PlayerPhoto from '@/Components/PlayerPhoto.vue';
import { positionLabel, positionGroupClass } from '@/Enums/positionBadges.js';
import RadarChart from '@/Pages/GameSaves/Play/components/RadarChart.vue';
import PlayerSpecialMoves from '@/Pages/GameSaves/Play/components/PlayerSpecialMoves.vue';
import StatBars from '@/Pages/GameSaves/Play/components/StatBars.vue';
import SecondaryPositions from '@/Pages/GameSaves/Play/components/SecondaryPositions.vue';
import { usePlayerUtils } from '@/Pages/GameSaves/Play/usePlayerUtils.js';

/**
 * Effectif d'une équipe ou d'une sélection : joueurs groupés par poste (GK → DEF → MID → ATT),
 * triés par note, avec une fiche détaillée au clic. Partagé par le choix d'équipe (Ligue)
 * et le choix de sélection (Coupe Nation).
 */
const props = defineProps({
    players:   { type: Array, required: true },   // joueurs façonnés comme dans TeamSelection (stats, morale_preview…)
    emptyText: { type: String, default: 'Aucun joueur' },
});

const {
    overallOf, keyStatsFor, statLabel, statColor,
    positionGroup, playerPhotoUrl, nationalityFlag, moraleState, moraleMatchEffect,
} = usePlayerUtils();

const POSITION_GROUPS = [
    { key: 'GK',    label: 'Gardien',   text: 'text-violet-600' },
    { key: 'DEF',   label: 'Défense',   text: 'text-blue-600'   },
    { key: 'MID',   label: 'Milieu',    text: 'text-teal-600'   },
    { key: 'ATT',   label: 'Attaque',   text: 'text-orange-600' },
    { key: 'OTHER', label: 'Autres',    text: 'text-slate-500'  },
];

const groupedRoster = computed(() =>
    POSITION_GROUPS
        .map(g => ({
            ...g,
            players: props.players
                .filter(p => positionGroup(p.position) === g.key)
                .sort((a, b) => overallOf(b) - overallOf(a)),
        }))
        .filter(g => g.players.length)
);

const detailPlayer = ref(null);
const statValue = (p, k) => Number(p?.[k] ?? p?.stats?.[k] ?? 0);
const statTone = (v) => Number(v) >= 70 ? 'text-emerald-600' : Number(v) >= 50 ? 'text-amber-600' : 'text-slate-500';
const overallBadgeClass = (o) => o >= 70 ? 'bg-emerald-50 border-emerald-300 text-emerald-700'
    : o >= 50 ? 'bg-amber-50 border-amber-300 text-amber-700'
    : 'bg-slate-100 border-slate-200 text-slate-500';
const staminaBar  = (v) => v >= 85 ? 'bg-emerald-500' : v >= 70 ? 'bg-lime-500' : v >= 50 ? 'bg-amber-400' : v >= 30 ? 'bg-orange-500' : 'bg-rose-500';
const staminaText = (v) => v >= 85 ? 'text-emerald-600' : v >= 70 ? 'text-lime-600' : v >= 50 ? 'text-amber-600' : v >= 30 ? 'text-orange-600' : 'text-rose-600';
const posBadgeClass = (pos) => positionGroupClass(positionGroup(pos));
</script>

<template>
    <div class="flex flex-col min-h-0 flex-1">
                                <div v-if="players.length" class="flex-1 min-h-0 overflow-y-auto pr-1 space-y-3">
                                    <!-- Groupes de postes : GK → DEF → MID → ATT, triés par note -->
                                    <div v-for="group in groupedRoster" :key="group.key">
                                        <div class="flex items-center gap-2 mb-1.5 px-0.5">
                                            <span class="text-2xs font-black uppercase tracking-wider" :class="group.text">{{ group.label }}</span>
                                            <span class="text-2xs font-bold text-slate-300 tabular-nums">{{ group.players.length }}</span>
                                            <div class="flex-1 h-px bg-slate-100"></div>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                            <button v-for="player in group.players" :key="player.id" type="button"
                                                 @click="detailPlayer = player"
                                                 :title="`Voir la fiche de ${player.firstname} ${player.lastname}`"
                                                 class="w-full text-left flex items-center gap-3 p-3 rounded-xl border border-slate-300 bg-slate-50 hover:bg-white hover:border-teal-200 hover:shadow-sm transition-all cursor-pointer">
                                                <div class="relative shrink-0">
                                                    <div class="w-12 h-12 rounded-xl overflow-hidden bg-slate-200 border border-slate-200">
                                                        <PlayerPhoto :src="playerPhotoUrl(player)" class="w-full h-full object-cover" alt="" />
                                                        <div v-if="!playerPhotoUrl(player)" class="w-full h-full flex items-center justify-center text-slate-400 text-lg">👤</div>
                                                    </div>
                                                    <span v-if="nationalityFlag(player)"
                                                          class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-white border border-slate-200 flex items-center justify-center text-[11px] leading-none shadow-sm"
                                                          :title="player.nationality">
                                                        {{ nationalityFlag(player) }}
                                                    </span>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="text-xs font-bold text-slate-700 truncate">
                                                            {{ player.firstname }} {{ player.lastname }}
                                                        </span>
                                                        <span class="shrink-0 px-1.5 py-0.5 rounded text-[8px] font-black uppercase tracking-wide"
                                                              :class="posBadgeClass(player.position)">
                                                            {{ positionLabel(player.position) }}
                                                        </span>
                                                    </div>
                                                    <!-- Stats clés (adaptées au poste) : libellé, valeur teintée, mini-barre colorée -->
                                                    <div class="flex gap-2 mt-1.5">
                                                        <div v-for="k in keyStatsFor(player.position)" :key="k" class="flex-1 min-w-0">
                                                            <div class="flex items-center justify-between gap-1">
                                                                <span class="text-[8px] font-semibold uppercase text-slate-400 truncate">{{ statLabel(k) }}</span>
                                                                <span class="text-2xs font-black tabular-nums" :class="statTone(statValue(player, k))">
                                                                    {{ statValue(player, k) }}
                                                                </span>
                                                            </div>
                                                            <div class="h-1 bg-slate-200 rounded-full overflow-hidden mt-0.5">
                                                                <div class="h-full rounded-full" :class="statColor(k)"
                                                                     :style="{ width: Math.min(statValue(player, k), 100) + '%' }"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Endurance : du rouge (épuisé) au vert (en forme) -->
                                                    <div class="flex items-center gap-1.5 mt-1.5" :title="`Endurance ${statValue(player, 'stamina')}`">
                                                        <span class="text-[8px] font-semibold uppercase text-slate-400 shrink-0">Endurance</span>
                                                        <div class="flex-1 h-1.5 bg-slate-200 rounded-full overflow-hidden">
                                                            <div class="h-full rounded-full" :class="staminaBar(statValue(player, 'stamina'))"
                                                                 :style="{ width: Math.min(statValue(player, 'stamina'), 100) + '%' }"></div>
                                                        </div>
                                                        <span class="text-2xs font-black tabular-nums shrink-0" :class="staminaText(statValue(player, 'stamina'))">
                                                            {{ statValue(player, 'stamina') }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="flex flex-col items-center gap-0.5 shrink-0">
                                                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-[11px] font-black border-2"
                                                         :class="overallBadgeClass(overallOf(player))">
                                                        {{ overallOf(player) }}
                                                    </div>
                                                    <span class="text-[7px] font-bold uppercase text-slate-300 tracking-wider">Note</span>
                                                    <span v-if="player.morale_preview != null"
                                                          class="text-2xs font-black tabular-nums"
                                                          :class="moraleState(player.morale_preview).text"
                                                          :title="`Moral de départ : ${moraleState(player.morale_preview).label}`">
                                                        {{ moraleState(player.morale_preview).emoji }} {{ player.morale_preview }}
                                                    </span>
                                                </div>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="flex items-center justify-center h-48 text-slate-500 text-sm italic">
                                    {{ emptyText }}
                                </div>

    <!-- Fiche détaillée d'un joueur (clic sur sa carte) -->
    <Modal :show="!!detailPlayer" max-width="2xl" @close="detailPlayer = null">
        <div v-if="detailPlayer" class="p-5 space-y-4">
            <div class="flex items-start gap-4">
                <div class="relative shrink-0">
                    <div class="w-20 h-20 rounded-xl overflow-hidden bg-slate-200 border border-slate-200">
                        <PlayerPhoto :src="playerPhotoUrl(detailPlayer)" class="w-full h-full object-cover" alt="" />
                        <div v-if="!playerPhotoUrl(detailPlayer)" class="w-full h-full flex items-center justify-center text-slate-400 text-2xl">👤</div>
                    </div>
                    <span v-if="nationalityFlag(detailPlayer)"
                          class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-white border border-slate-200 flex items-center justify-center text-xs shadow-sm"
                          :title="detailPlayer.nationality">{{ nationalityFlag(detailPlayer) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-bold text-slate-700">{{ detailPlayer.firstname }} {{ detailPlayer.lastname }}</h3>
                    <div class="flex flex-wrap items-center gap-1.5 mt-1">
                        <span class="px-1.5 py-0.5 rounded text-2xs font-black uppercase tracking-wide" :class="posBadgeClass(detailPlayer.position)">
                            {{ positionLabel(detailPlayer.position) }}
                        </span>
                        <SecondaryPositions :player="detailPlayer" />
                    </div>
                    <p v-if="detailPlayer.description" class="mt-2 text-xs text-slate-500 italic">{{ detailPlayer.description }}</p>
                </div>
                <div class="flex flex-col items-center gap-0.5 shrink-0">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-sm font-black border-2"
                         :class="overallBadgeClass(overallOf(detailPlayer))">{{ overallOf(detailPlayer) }}</div>
                    <span class="text-[8px] font-bold uppercase text-slate-400 tracking-wider">Note</span>
                </div>
                <button type="button" @click="detailPlayer = null" aria-label="Fermer"
                        class="shrink-0 -mt-1 -mr-1 w-8 h-8 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100">✕</button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- Moral -->
                <div v-if="detailPlayer.morale_preview != null" class="rounded-xl border border-slate-200 bg-slate-50 p-3 shadow-card">
                    <div class="flex items-center justify-between text-xs mb-1.5">
                        <span class="font-bold text-slate-500 uppercase tracking-wider text-2xs">Moral</span>
                        <span class="font-black" :class="moraleState(detailPlayer.morale_preview).text">
                            {{ moraleState(detailPlayer.morale_preview).emoji }} {{ moraleState(detailPlayer.morale_preview).label }} · {{ detailPlayer.morale_preview }}
                        </span>
                    </div>
                    <div class="h-2 bg-slate-200 rounded-full overflow-hidden">
                        <div class="h-full rounded-full" :class="moraleState(detailPlayer.morale_preview).bar"
                             :style="{ width: Math.min(detailPlayer.morale_preview, 100) + '%' }"></div>
                    </div>
                    <p class="mt-1.5 text-2xs text-slate-500">
                        Effet en match : {{ moraleMatchEffect(detailPlayer.morale_preview)?.pct ?? 'aucun' }}
                    </p>
                </div>
                <!-- Endurance -->
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 shadow-card">
                    <div class="flex items-center justify-between text-xs mb-1.5">
                        <span class="font-bold text-slate-500 uppercase tracking-wider text-2xs">Endurance</span>
                        <span class="font-black" :class="staminaText(statValue(detailPlayer, 'stamina'))">{{ statValue(detailPlayer, 'stamina') }}</span>
                    </div>
                    <div class="h-2 bg-slate-200 rounded-full overflow-hidden">
                        <div class="h-full rounded-full" :class="staminaBar(statValue(detailPlayer, 'stamina'))"
                             :style="{ width: Math.min(statValue(detailPlayer, 'stamina'), 100) + '%' }"></div>
                    </div>
                    <p class="mt-1.5 text-2xs text-slate-500">Plus elle est haute, plus il tient le match et enchaîne les actions.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <RadarChart :player="detailPlayer" accent="teal" />
                <StatBars :player="detailPlayer" />
            </div>

            <PlayerSpecialMoves :moves="detailPlayer.special_moves ?? []" />
        </div>
    </Modal>
    </div>
</template>
