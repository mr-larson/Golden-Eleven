<script setup>
import { ref } from 'vue';
import Panel from '@/Components/Panel.vue';
import TeamLogoImage from '@/Components/TeamLogoImage.vue';

// Maquette du Classement : tableau figé ; un clic sur une ligne la met en évidence.
const props = defineProps({ demo: { type: Object, required: true } });
const focusId = ref(props.demo.me.id);
const cols = [['played', 'J'], ['w', 'V'], ['d', 'N'], ['l', 'D'], ['gd', '+/-'], ['pts', 'Pts']];
</script>

<template>
    <Panel tone="white" padding="sm">
        <table class="w-full text-xs">
            <thead>
                <tr class="text-3xs uppercase tracking-wider text-slate-400">
                    <th class="w-8 py-1 text-left">#</th>
                    <th class="py-1 text-left">Équipe</th>
                    <th v-for="[key, label] in cols" :key="key" class="w-9 py-1 text-center">{{ label }}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="t in demo.standings" :key="t.id" :data-demo="t.id === demo.me.id ? 'row-me' : null"
                    class="cursor-pointer border-t border-slate-100 transition-colors hover:bg-slate-50"
                    :class="t.id === focusId ? 'bg-teal-50 font-bold' : ''" @click="focusId = t.id">
                    <td class="py-1.5 text-slate-400">{{ t.rank }}</td>
                    <td class="py-1.5">
                        <span class="flex items-center gap-2 text-slate-800">
                            <TeamLogoImage v-if="t.logo" :src="t.logo" class="h-5 w-5" />{{ t.name }}
                        </span>
                    </td>
                    <td v-for="[key] in cols" :key="key" class="text-center" :class="key === 'pts' ? 'font-black text-slate-800' : 'text-slate-500'">
                        {{ key === 'gd' && t.gd > 0 ? '+' : '' }}{{ t[key] }}
                    </td>
                </tr>
            </tbody>
        </table>
    </Panel>
</template>
