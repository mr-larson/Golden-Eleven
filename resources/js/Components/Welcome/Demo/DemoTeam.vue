<script setup>
import { computed, ref } from 'vue';
import Panel from '@/Components/Panel.vue';
import PlayerPhoto from '@/Components/PlayerPhoto.vue';
import PositionBadge from '@/Components/PositionBadge.vue';
import StaminaBar from '@/Components/StaminaBar.vue';
import SectionTitle from '@/Components/SectionTitle.vue';

// Maquette de « Mon équipe » : liste du onze à gauche, fiche du joueur sélectionné à droite.
const props = defineProps({ demo: { type: Object, required: true } });

const selectedId = ref(props.demo.squad[1].id);
const selected = computed(() => props.demo.squad.find(p => p.id === selectedId.value) ?? props.demo.squad[0]);
const bars = computed(() => [
    { label: 'Attaque', value: selected.value.attack, bar: 'bg-orange-400' },
    { label: 'Défense', value: selected.value.defense, bar: 'bg-blue-400' },
]);
</script>

<template>
    <div class="grid gap-3 md:grid-cols-[minmax(0,1.3fr)_minmax(0,1fr)]">
        <Panel tone="white" padding="sm">
            <SectionTitle>Effectif</SectionTitle>
            <ul class="mt-2 divide-y divide-slate-100">
                <li v-for="(p, i) in demo.squad" :key="p.id">
                    <button type="button" :data-demo="`player-${i}`" class="flex w-full items-center gap-2 rounded-md px-1.5 py-1 text-left transition-colors hover:bg-slate-50"
                            :class="p.id === selectedId ? 'bg-teal-50 ring-1 ring-teal-300' : ''" @click="selectedId = p.id">
                        <PlayerPhoto :src="p.photo" :zoomable="false" class="h-7 w-7 rounded-full object-cover object-top" />
                        <span class="min-w-0 flex-1 truncate text-xs font-semibold text-slate-700">{{ p.name }}</span>
                        <PositionBadge :position="p.position" />
                        <StaminaBar :value="p.stamina" class="h-[3px] w-10 bg-slate-200" />
                    </button>
                </li>
            </ul>
        </Panel>

        <Panel tone="white" padding="md" class="self-start">
            <div class="flex items-center gap-3">
                <PlayerPhoto :src="selected.photo" :zoomable="false" class="h-16 w-16 rounded-xl object-cover object-top" />
                <div>
                    <p class="text-sm font-bold text-slate-800">{{ selected.name }}</p>
                    <PositionBadge :position="selected.position" label="full" size="sm" />
                </div>
            </div>
            <ul class="mt-3 space-y-1.5">
                <li v-for="b in bars" :key="b.label" class="flex items-center gap-2 text-2xs text-slate-500">
                    <span class="w-14">{{ b.label }}</span>
                    <span class="h-1.5 flex-1 rounded-full bg-slate-100"><span class="block h-full rounded-full transition-all duration-500" :class="b.bar" :style="{ width: `${Math.min(100, b.value * 2.6)}%` }"></span></span>
                    <span class="w-6 text-right font-bold text-slate-700">{{ b.value }}</span>
                </li>
                <li class="flex items-center gap-2 text-2xs text-slate-500">
                    <span class="w-14">Endurance</span>
                    <StaminaBar :value="selected.stamina" class="h-1.5 flex-1 bg-slate-100" />
                    <span class="w-6 text-right font-bold text-slate-700">{{ selected.stamina }}</span>
                </li>
            </ul>
        </Panel>
    </div>
</template>
