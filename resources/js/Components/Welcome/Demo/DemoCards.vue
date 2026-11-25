<script setup>
import { ref } from 'vue';
import Panel from '@/Components/Panel.vue';
import Badge from '@/Components/Badge.vue';
import SectionTitle from '@/Components/SectionTitle.vue';

// Maquette des Cartes bonus : un clic « joue » la carte (état local uniquement).
defineProps({ demo: { type: Object, required: true } });
const played = ref(null);
</script>

<template>
    <div>
        <SectionTitle class="mb-2">Boutique de cartes · 3 cartes cette semaine</SectionTitle>
        <div class="grid gap-3 md:grid-cols-3">
            <button v-for="(c, i) in demo.cards" :key="c.name" type="button" :data-demo="`card-${i}`" @click="played = played === i ? null : i"
                    class="text-left transition-transform duration-200 hover:-translate-y-0.5" :class="played === i ? 'scale-[1.02]' : ''">
                <Panel tone="white" padding="md" class="h-full transition-colors" :class="played === i ? '!border-teal-400 !bg-teal-50' : ''">
                    <div class="flex items-start justify-between">
                        <span class="text-2xl">{{ c.icon }}</span>
                        <Badge :tone="c.kind === 'Malus' ? 'solid-rose' : 'solid-teal'" size="xs">{{ c.kind }}</Badge>
                    </div>
                    <p class="mt-2 text-sm font-bold text-slate-800">{{ c.name }}</p>
                    <p class="mt-1 text-2xs leading-4 text-slate-500">{{ c.desc }}</p>
                    <p class="mt-3 text-2xs font-bold" :class="played === i ? 'text-teal-700' : 'text-slate-500'">
                        {{ played === i ? 'Carte jouée ✓' : `Prix : ${c.price}` }}
                    </p>
                </Panel>
            </button>
        </div>
    </div>
</template>
