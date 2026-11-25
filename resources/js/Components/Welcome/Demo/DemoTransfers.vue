<script setup>
import { computed, reactive } from 'vue';
import Panel from '@/Components/Panel.vue';
import PlayerPhoto from '@/Components/PlayerPhoto.vue';
import PositionBadge from '@/Components/PositionBadge.vue';
import SectionTitle from '@/Components/SectionTitle.vue';

// Maquette du Marché des transferts : recruter débite le budget affiché (état local, rien n'est enregistré).
const props = defineProps({ demo: { type: Object, required: true } });
const signed = reactive(new Set());
const budget = computed(() => props.demo.budget - props.demo.market.filter(p => signed.has(p.id)).reduce((sum, p) => sum + p.price, 0));
const canAfford = (p) => signed.has(p.id) || p.price <= budget.value;
const toggle = (p) => { if (signed.has(p.id)) signed.delete(p.id); else if (canAfford(p)) signed.add(p.id); };
</script>

<template>
    <Panel tone="white" padding="sm">
        <div class="flex items-center justify-between">
            <SectionTitle>Marché des transferts</SectionTitle>
            <span class="rounded-full bg-teal-50 px-2.5 py-0.5 text-2xs font-bold text-teal-700">Budget : {{ budget }}</span>
        </div>
        <ul class="mt-2 divide-y divide-slate-100">
            <li v-for="(p, i) in demo.market" :key="p.id" class="flex items-center gap-3 py-2">
                <PlayerPhoto :src="p.photo" :zoomable="false" class="h-10 w-10 rounded-lg object-cover object-top" />
                <div class="min-w-0 flex-1">
                    <p class="truncate text-xs font-bold text-slate-800">{{ p.name }}</p>
                    <div class="mt-0.5 flex items-center gap-2 text-3xs text-slate-400">
                        <PositionBadge :position="p.position" /> ATT {{ p.attack }} · DÉF {{ p.defense }}
                    </div>
                </div>
                <span class="text-xs font-bold text-slate-600">{{ p.price }}</span>
                <button type="button" :data-demo="`recruit-${i}`" :disabled="!canAfford(p)"
                        class="w-24 rounded-full px-3 py-1 text-2xs font-bold transition-colors disabled:cursor-not-allowed disabled:opacity-40"
                        :class="signed.has(p.id) ? 'bg-emerald-100 text-emerald-700' : 'bg-teal-500 text-white hover:bg-teal-400'" @click="toggle(p)">
                    {{ signed.has(p.id) ? 'Recruté ✓' : 'Recruter' }}
                </button>
            </li>
        </ul>
    </Panel>
</template>
