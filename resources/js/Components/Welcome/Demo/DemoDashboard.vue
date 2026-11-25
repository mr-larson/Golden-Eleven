<script setup>
import Panel from '@/Components/Panel.vue';
import SectionTitle from '@/Components/SectionTitle.vue';
import TeamLogoImage from '@/Components/TeamLogoImage.vue';

// Maquette du Dashboard : classement, bilan, budget, prochain match et forces en présence.
// « Jouer » demande à la visite d'ouvrir l'écran de match (DemoMatch).
defineProps({ demo: { type: Object, required: true } });
defineEmits(['play-match']);

const forces = [
    { label: 'Attaque', me: 26, opp: 26, bar: 'bg-orange-400' },
    { label: 'Défense', me: 25, opp: 28, bar: 'bg-blue-400' },
    { label: 'Stamina', me: 74, opp: 75, bar: 'bg-emerald-400' },
];
const width = (value) => `${Math.min(100, value * (value > 40 ? 1 : 2.6))}%`;
</script>

<template>
    <div class="grid gap-3">
        <Panel tone="white" padding="sm" class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <TeamLogoImage v-if="demo.me.logo" :src="demo.me.logo" class="h-9 w-9" />
                <div>
                    <p class="text-sm font-bold text-slate-800">{{ demo.me.name }}</p>
                    <p class="text-2xs text-slate-400">Ligue · Équilibré</p>
                </div>
            </div>
            <dl class="flex gap-6 text-center">
                <div><dd class="text-sm font-bold text-slate-800">1</dd><dt class="text-3xs text-slate-400">Saison</dt></div>
                <div><dd class="text-sm font-bold text-teal-600">8</dd><dt class="text-3xs text-slate-400">Semaine</dt></div>
                <div><dd class="text-sm font-bold text-slate-800">7</dd><dt class="text-3xs text-slate-400">Matchs joués</dt></div>
            </dl>
        </Panel>

        <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
            <Panel tone="white" padding="sm" class="text-center">
                <SectionTitle>Classement</SectionTitle>
                <p class="mt-1 text-2xl font-black text-teal-600">{{ demo.me.rank }}<sup class="text-xs">e</sup></p>
                <p class="text-3xs text-slate-400">/ {{ demo.standings.length }} équipes</p>
            </Panel>
            <Panel tone="white" padding="sm" class="text-center">
                <SectionTitle>Bilan</SectionTitle>
                <p class="mt-1 text-lg font-black text-slate-800">
                    <span class="text-emerald-600">{{ demo.record.w }}V</span>
                    <span class="mx-1 text-slate-400">{{ demo.record.d }}N</span>
                    <span class="text-rose-500">{{ demo.record.l }}D</span>
                </p>
                <p class="text-3xs text-slate-400">{{ demo.record.pts }} pts</p>
            </Panel>
            <Panel tone="white" padding="sm" class="text-center">
                <SectionTitle>Budget</SectionTitle>
                <p class="mt-1 text-2xl font-black text-slate-800">{{ demo.budget }}</p>
                <p class="text-3xs text-slate-400">{{ demo.rosterSize }} joueur(s)</p>
            </Panel>
            <Panel tone="white" padding="sm">
                <SectionTitle>Effectif</SectionTitle>
                <dl class="mt-1 space-y-0.5 text-2xs text-slate-500">
                    <div class="flex justify-between"><dt>Blessés</dt><dd class="font-bold text-rose-500">2</dd></div>
                    <div class="flex justify-between"><dt>Suspensions</dt><dd class="font-bold">0</dd></div>
                    <div class="flex justify-between"><dt>Cartons</dt><dd class="font-bold text-amber-500">3</dd></div>
                </dl>
            </Panel>
        </div>

        <Panel tone="white" padding="sm" class="!border-teal-400">
            <p class="text-center text-3xs font-bold uppercase tracking-wider text-teal-600">Domicile · Semaine 8</p>
            <div class="mt-2 flex items-center justify-around">
                <div class="flex flex-col items-center gap-1">
                    <TeamLogoImage v-if="demo.me.logo" :src="demo.me.logo" class="h-12 w-12" />
                    <span class="text-xs font-bold text-slate-800">{{ demo.me.name }}</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                    <span class="text-xl font-black text-slate-300">VS</span>
                    <button type="button" data-demo="play" class="rounded-full bg-teal-500 px-4 py-1 text-xs font-bold text-white transition-transform hover:bg-teal-400 active:scale-95" @click="$emit('play-match')">
                        ▶ Jouer
                    </button>
                </div>
                <div class="flex flex-col items-center gap-1">
                    <TeamLogoImage v-if="demo.opponent.logo" :src="demo.opponent.logo" class="h-12 w-12" />
                    <span class="text-xs font-bold text-slate-800">{{ demo.opponent.name }}</span>
                </div>
            </div>
        </Panel>

        <div class="grid gap-3 md:grid-cols-2">
            <Panel tone="white" padding="sm">
                <SectionTitle>Forces de l'équipe</SectionTitle>
                <ul class="mt-2 space-y-1.5">
                    <li v-for="f in forces" :key="f.label" class="flex items-center gap-2 text-2xs text-slate-500">
                        <span class="w-14">{{ f.label }}</span>
                        <span class="h-1.5 flex-1 rounded-full bg-slate-100"><span class="block h-full rounded-full" :class="f.bar" :style="{ width: width(f.me) }"></span></span>
                        <span class="w-6 text-right font-bold text-slate-700">{{ f.me }}</span>
                    </li>
                </ul>
            </Panel>
            <Panel tone="white" padding="sm">
                <SectionTitle>Adversaire · {{ demo.opponent.name }}</SectionTitle>
                <ul class="mt-2 space-y-1.5">
                    <li v-for="f in forces" :key="f.label" class="flex items-center gap-2 text-2xs text-slate-500">
                        <span class="w-14">{{ f.label }}</span>
                        <span class="h-1.5 flex-1 rounded-full bg-slate-100"><span class="block h-full rounded-full" :class="f.bar" :style="{ width: width(f.opp) }"></span></span>
                        <span class="w-6 text-right font-bold text-rose-500">{{ f.opp }}</span>
                    </li>
                </ul>
            </Panel>
        </div>
    </div>
</template>
