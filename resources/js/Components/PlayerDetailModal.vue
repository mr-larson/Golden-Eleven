<script setup>
import Modal from '@/Components/Modal.vue';
import PlayerPhoto from '@/Components/PlayerPhoto.vue';
import { positionLabel } from '@/Enums/positionBadges.js';
import RadarChart from '@/Pages/GameSaves/Play/components/RadarChart.vue';
import PlayerSpecialMoves from '@/Pages/GameSaves/Play/components/PlayerSpecialMoves.vue';
import StatBars from '@/Pages/GameSaves/Play/components/StatBars.vue';
import SecondaryPositions from '@/Pages/GameSaves/Play/components/SecondaryPositions.vue';
import { usePlayerUtils } from '@/Pages/GameSaves/Play/usePlayerUtils.js';
import { overallBadgeClass, posBadgeClass, staminaBar, staminaText, statValue } from '@/Components/Selection/playerDisplay.js';

/**
 * Fiche détaillée d'un joueur (identité, note, moral, endurance, radar, stats, coups spéciaux) dans une modale.
 * Partagée par l'effectif d'une équipe ou d'une sélection (choix d'équipe, base de données, joueurs libres).
 *
 *  - player : joueur à afficher (même forme que dans SelectionRoster), null = modale fermée ;
 *  - close  : demande de fermeture.
 */
defineProps({
    player: { type: Object, default: null },
});
const emit = defineEmits(['close']);

const { overallOf, playerPhotoUrl, nationalityFlag, moraleState, moraleMatchEffect } = usePlayerUtils();
</script>

<template>
    <!-- Fiche détaillée d'un joueur (clic sur sa carte) -->
    <Modal :show="!!player" max-width="2xl" @close="emit('close')">
        <div v-if="player" class="p-5 space-y-4">
            <div class="flex items-start gap-4">
                <div class="relative shrink-0">
                    <div class="w-20 h-20 rounded-xl overflow-hidden bg-slate-200 border border-slate-200">
                        <PlayerPhoto :src="playerPhotoUrl(player)" class="w-full h-full object-cover" alt="" />
                        <div v-if="!playerPhotoUrl(player)" class="w-full h-full flex items-center justify-center text-slate-400 text-2xl">👤</div>
                    </div>
                    <span v-if="nationalityFlag(player)"
                          class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-white border border-slate-200 flex items-center justify-center text-xs shadow-sm"
                          :title="player.nationality">{{ nationalityFlag(player) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-bold text-slate-700">{{ player.firstname }} {{ player.lastname }}</h3>
                    <div class="flex flex-wrap items-center gap-1.5 mt-1">
                        <span class="px-1.5 py-0.5 rounded text-2xs font-black uppercase tracking-wide" :class="posBadgeClass(player.position)">
                            {{ positionLabel(player.position) }}
                        </span>
                        <SecondaryPositions :player="player" />
                    </div>
                    <p v-if="player.description" class="mt-2 text-xs text-slate-500 italic">{{ player.description }}</p>
                </div>
                <div class="flex flex-col items-center gap-0.5 shrink-0">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-sm font-black border-2"
                         :class="overallBadgeClass(overallOf(player))">{{ overallOf(player) }}</div>
                    <span class="text-[8px] font-bold uppercase text-slate-400 tracking-wider">Note</span>
                </div>
                <button type="button" @click="emit('close')" aria-label="Fermer"
                        class="shrink-0 -mt-1 -mr-1 w-8 h-8 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100">✕</button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- Moral -->
                <div v-if="player.morale_preview != null" class="rounded-xl border border-slate-200 bg-slate-50 p-3 shadow-card">
                    <div class="flex items-center justify-between text-xs mb-1.5">
                        <span class="font-bold text-slate-500 uppercase tracking-wider text-2xs">Moral</span>
                        <span class="font-black" :class="moraleState(player.morale_preview).text">
                            {{ moraleState(player.morale_preview).emoji }} {{ moraleState(player.morale_preview).label }} · {{ player.morale_preview }}
                        </span>
                    </div>
                    <div class="h-2 bg-slate-200 rounded-full overflow-hidden">
                        <div class="h-full rounded-full" :class="moraleState(player.morale_preview).bar"
                             :style="{ width: Math.min(player.morale_preview, 100) + '%' }"></div>
                    </div>
                    <p class="mt-1.5 text-2xs text-slate-500">
                        Effet en match : {{ moraleMatchEffect(player.morale_preview)?.pct ?? 'aucun' }}
                    </p>
                </div>
                <!-- Endurance -->
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 shadow-card">
                    <div class="flex items-center justify-between text-xs mb-1.5">
                        <span class="font-bold text-slate-500 uppercase tracking-wider text-2xs">Endurance</span>
                        <span class="font-black" :class="staminaText(statValue(player, 'stamina'))">{{ statValue(player, 'stamina') }}</span>
                    </div>
                    <div class="h-2 bg-slate-200 rounded-full overflow-hidden">
                        <div class="h-full rounded-full" :class="staminaBar(statValue(player, 'stamina'))"
                             :style="{ width: Math.min(statValue(player, 'stamina'), 100) + '%' }"></div>
                    </div>
                    <p class="mt-1.5 text-2xs text-slate-500">Plus elle est haute, plus il tient le match et enchaîne les actions.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <RadarChart :player="player" accent="teal" />
                <StatBars :player="player" />
            </div>

            <PlayerSpecialMoves :moves="player.special_moves ?? []" />
        </div>
    </Modal>
</template>
