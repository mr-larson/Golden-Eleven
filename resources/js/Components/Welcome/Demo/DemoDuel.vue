<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import DemoCursor from './DemoCursor.vue';
import { DEFENSE_OPTIONS as OPTIONS, GOOD_COUNTER_BONUS as BONUS, SCRIPTED_ROLLS, fmt, useDuel } from './useDuel.js';

/**
 * « Un tour de match » : l'adversaire tente une passe, on choisit sa défense, le duel se joue
 * aux dés. Un scénario automatique joue d'abord le bon choix (dés truqués), puis le visiteur
 * peut rejouer lui-même avec des dés aléatoires. Règles et calculs : voir useDuel.js.
 */
const attacker = { name: 'Taro Misaki',  photo: '/images/Taro_Misaki.webp',  action: 'Passe', stat: 24 };
const defender = { name: 'Ryo Ishizaki', photo: '/images/Ryo_Ishizaki.webp', stat: 21 };

const { phase, choice, dice, good, bad, attackTotal, defenseTotal, outcome, revealed, play, reset } =
    useDuel({ attacker: () => attacker.stat, defender: () => defender.stat }, 'intro');

const scripted = ref(false);         // vrai pendant le scénario automatique
const stoppedByUser = ref(false);
const root = ref(null);
const cursor = ref({ x: 0, y: 0, visible: false, pressed: false });
let runId = 0;
let observer;

const reduced = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const wait = (ms, id) => new Promise(resolve => setTimeout(() => resolve(id === runId), ms));
const step = computed(() => ({ intro: 1, choose: 2, rolling: 3, result: 3 })[phase.value]);

function moveTo(element) {
    const base = root.value.getBoundingClientRect();
    const rect = element.getBoundingClientRect();
    cursor.value = { ...cursor.value, visible: true, x: rect.left - base.left + rect.width * 0.55, y: rect.top - base.top + rect.height * 0.6 };
}

async function runScript() {
    const id = ++runId;
    scripted.value = true;
    reset('intro');
    cursor.value = { x: 20, y: 20, visible: false, pressed: false };
    if (!(await wait(1400, id))) return;
    phase.value = 'choose';
    if (!(await wait(1300, id))) return;
    const target = root.value?.querySelector('[data-duel="intercept"]');
    if (target) {
        moveTo(target);
        if (!(await wait(1000, id))) return;
        cursor.value = { ...cursor.value, pressed: true };
        if (!(await wait(160, id))) return;
        cursor.value = { ...cursor.value, pressed: false };
    }
    play('intercept', SCRIPTED_ROLLS);
    if (!(await wait(900, id))) return;
    cursor.value = { ...cursor.value, visible: false };
    scripted.value = false;     // fin du scénario : à toi de rejouer
}

function takeControl() {
    if (!scripted.value) return;
    runId++;
    scripted.value = false;
    stoppedByUser.value = true;
    cursor.value = { ...cursor.value, visible: false };
    if (phase.value === 'intro') phase.value = 'choose';
}

onMounted(() => {
    if (reduced()) { phase.value = 'choose'; stoppedByUser.value = true; return; }
    observer = new IntersectionObserver(([entry]) => {
        if (entry.isIntersecting && !stoppedByUser.value) { observer.disconnect(); runScript(); }
    }, { threshold: 0.6 });
    observer.observe(root.value);
});
onBeforeUnmount(() => { runId++; observer?.disconnect(); });

const cardState = (n) => step.value >= n ? 'opacity-100' : 'opacity-35';
</script>

<template>
    <div ref="root" class="relative" @pointerdown="takeControl">
        <ol class="grid gap-4 lg:grid-cols-3">
            <!-- Étape 1 : l'action adverse -->
            <li class="flex flex-col rounded-xl border border-white/10 bg-white/5 p-5 transition-opacity duration-500" :class="cardState(1)">
                <div class="mb-3 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-400">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-teal-500 text-[11px] font-black text-slate-700">1</span>
                    L'adversaire attaque
                </div>
                <div class="flex items-center gap-3">
                    <img :src="attacker.photo" alt="" width="56" height="56" loading="lazy" decoding="async"
                         class="h-14 w-14 shrink-0 rounded-full border border-orange-300/40 bg-slate-800 object-cover object-top" />
                    <div>
                        <div class="font-semibold text-slate-100">{{ attacker.name }}</div>
                        <div class="text-sm text-orange-300">tente une passe <span aria-hidden="true">➡️</span></div>
                    </div>
                </div>
                <div class="mt-4 rounded-lg border border-orange-300/20 bg-orange-400/10 px-3 py-2 text-sm text-slate-300">
                    Sa stat de <strong class="text-slate-100">{{ attacker.action }}</strong> : <span class="font-mono text-orange-200">{{ attacker.stat }}</span>
                </div>
            </li>

            <!-- Étape 2 : ton choix défensif -->
            <li class="flex flex-col rounded-xl border border-white/10 bg-white/5 p-5 transition-opacity duration-500" :class="cardState(2)">
                <div class="mb-3 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-400">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-teal-500 text-[11px] font-black text-slate-700">2</span>
                    Tu choisis ta défense
                </div>
                <ul class="grid grid-cols-3 gap-2" aria-label="Défenses possibles">
                    <li v-for="o in OPTIONS" :key="o.key">
                        <button type="button" :data-duel="o.key" :disabled="phase !== 'choose'" @click="play(o.key)"
                                class="w-full rounded-lg border px-2 py-2.5 text-center text-xs transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-300"
                                :class="[
                                    choice === o.key ? 'border-teal-300/70 bg-teal-500/20 text-teal-100 shadow-[0_0_14px_rgb(45_212_191/0.3)]' : 'border-white/10 bg-white/5 text-slate-300',
                                    phase === 'choose' ? 'cursor-pointer hover:border-teal-300/50 hover:bg-white/10' : 'cursor-default',
                                    phase !== 'choose' && choice !== o.key ? 'opacity-50' : '',
                                ]">
                            <div class="mb-1 text-lg" aria-hidden="true">{{ o.icon }}</div>
                            {{ o.label }}
                        </button>
                    </li>
                </ul>
                <p class="mt-4 text-sm leading-6 text-slate-300">
                    <strong class="text-teal-200">Intercepter</strong> est le bon contre face à une passe :
                    <span class="text-teal-200">+{{ BONUS }}</span> pour ta défense.
                    Un mauvais choix donne ce bonus à l'attaque.
                </p>
            </li>

            <!-- Étape 3 : le duel -->
            <li class="flex flex-col rounded-xl border border-teal-300/20 bg-white/5 p-5 transition-opacity duration-500" :class="cardState(3)">
                <div class="mb-3 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-400">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-teal-500 text-[11px] font-black text-slate-700">3</span>
                    Le duel se joue aux dés
                </div>
                <dl class="space-y-3 text-sm">
                    <div class="rounded-lg bg-slate-950/40 px-3 py-2">
                        <dt class="mb-1 flex justify-between text-slate-400">
                            <span>{{ attacker.name }}</span>
                            <span class="font-mono text-slate-100">{{ revealed ? fmt(attackTotal) : '–' }}</span>
                        </dt>
                        <dd class="font-mono text-xs text-slate-400">
                            {{ attacker.stat }} + dé <span class="duel-die">{{ dice.att || '?' }}</span> ÷ 2
                            <span v-if="revealed && choice && !good" class="text-orange-300">+ {{ BONUS }} mauvais contre</span>
                        </dd>
                    </div>
                    <div class="rounded-lg bg-slate-950/40 px-3 py-2">
                        <dt class="mb-1 flex justify-between text-slate-400">
                            <span>{{ defender.name }} <span class="text-slate-500">(toi)</span></span>
                            <span class="font-mono text-teal-200">{{ revealed ? fmt(defenseTotal) : '–' }}</span>
                        </dt>
                        <dd class="font-mono text-xs text-slate-400">
                            {{ defender.stat }} + dé <span class="duel-die">{{ dice.def || '?' }}</span> ÷ 2
                            <span v-if="revealed && good" class="text-teal-300">+ {{ BONUS }} bon contre</span>
                        </dd>
                    </div>
                </dl>
                <div class="mt-4 min-h-[44px]" aria-live="polite">
                    <p v-if="revealed" class="rounded-lg border px-3 py-2 text-sm font-semibold"
                       :class="outcome === 'defense' ? 'border-teal-300/30 bg-teal-500/10 text-teal-100' : outcome === 'attack' ? 'border-orange-300/30 bg-orange-400/10 text-orange-100' : 'border-white/20 bg-white/5 text-slate-200'">
                        <template v-if="outcome === 'defense'"><span aria-hidden="true">✅ </span>Passe interceptée : le ballon est à toi.</template>
                        <template v-else-if="outcome === 'attack'"><span aria-hidden="true">❌ </span>La passe passe : l'adversaire garde le ballon.</template>
                        <template v-else><span aria-hidden="true">⚖️ </span>Duel équilibré.</template>
                    </p>
                </div>
                <button v-if="revealed && !scripted" type="button" class="mt-3 self-start rounded-full border border-white/15 px-3 py-1 text-xs font-medium text-slate-100 transition-colors hover:bg-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-300"
                        @click="reset()">
                    ↻ Rejouer en choisissant toi-même
                </button>
            </li>
        </ol>

        <DemoCursor v-bind="cursor" />
    </div>
</template>

<style scoped>
/* Dé d20 : petit carré façon LED. */
.duel-die {
    display: inline-flex;
    min-width: 1.6em;
    justify-content: center;
    padding: 0 0.25em;
    border: 1px solid rgb(94 234 212 / 0.45);
    border-radius: 3px;
    background: rgb(15 23 42 / 0.8);
    color: rgb(204 251 241);
    box-shadow: 0 0 8px rgb(45 212 191 / 0.25);
}
</style>
