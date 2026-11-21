<script setup>
import { computed, ref } from 'vue';
import { useTeamStrength } from '@/Composables/useTeamStrength.js';
import Panel from '@/Components/Panel.vue';
import SectionTitle from '@/Components/SectionTitle.vue';

/**
 * Carte « Forces » d'une équipe : note globale toujours visible, détail des statistiques moyennes
 * repliable (fermé par défaut).
 *
 *  - players     : joueurs de l'équipe ou de la sélection (statistiques moyennes et note calculées ici,
 *                  via useTeamStrength) ;
 *  - open        : état initial du détail.
 */
const props = defineProps({
    players:   { type: Array, default: () => [] },
    open:      { type: Boolean, default: false },
});

const expanded = ref(props.open);
const { teamStatsOf, teamRatingOf } = useTeamStrength();
const stats     = computed(() => teamStatsOf(props.players));
const rating    = computed(() => teamRatingOf(props.players));
const hasRoster = computed(() => props.players.length > 0);
const signedPct = (v) => `${v > 0 ? '+' : ''}${v.toLocaleString('fr-FR')} %`;
</script>

<template>
    <Panel as="div" tone="white">
        <div class="flex items-center justify-between gap-2">
            <SectionTitle>Forces</SectionTitle>
            <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-full text-slate-500 hover:bg-slate-100 focus-visible:ring-2 focus-visible:ring-teal-500"
                    :aria-expanded="expanded" aria-controls="team-selection-stats"
                    :aria-label="expanded ? 'Masquer les statistiques' : 'Afficher les statistiques'"
                    :title="expanded ? 'Masquer les statistiques' : 'Afficher les statistiques'"
                    @click="expanded = !expanded">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     class="h-4 w-4" :class="{ 'rotate-180': expanded }">
                    <path d="m6 9 6 6 6-6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                </svg>
            </button>
        </div>
        <div v-show="expanded" id="team-selection-stats" class="mt-2">
            <div class="space-y-2">
                <div v-for="stat in stats" :key="stat.key" class="flex items-center gap-2 text-xs">
                    <span class="w-16 text-slate-500 shrink-0">{{ stat.label }}</span>
                    <div class="flex-1 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full" :class="stat.color" :style="{ width: Math.min(stat.value ?? 0, 100) + '%' }"></div>
                    </div>
                    <span class="w-9 shrink-0 whitespace-nowrap text-right font-black" :class="stat.text ?? 'text-slate-600'">
                        {{ stat.value ?? (stat.key === 'luck' && hasRoster ? 'À tirer' : '—') }}
                    </span>
                </div>
            </div>
            <p v-if="hasRoster" class="mt-3 text-2xs text-slate-500">
                Arrêt main et Poings sont calculés sur les gardiens.
                La vitesse, le moral, l’endurance et la chance affichés sont ceux du début de partie.
            </p>
        </div>
        <div v-if="rating" class="mt-3 flex items-center justify-between gap-2 border-t border-slate-200 pt-3"
             :title="`Niveau du onze probable ${rating.level} · moral ${signedPct(rating.morale)} · endurance ${signedPct(rating.stamina)}`">
            <div>
                <div class="text-xs font-semibold text-slate-700">Note globale</div>
                <div class="mt-0.5 text-2xs text-slate-500">
                    Niveau {{ rating.level }} · moral {{ signedPct(rating.morale) }} · endur. {{ signedPct(rating.stamina) }}
                </div>
            </div>
            <strong class="text-lg text-violet-600">{{ rating.note }}</strong>
        </div>
    </Panel>
</template>
