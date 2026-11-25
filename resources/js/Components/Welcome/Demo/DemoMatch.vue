<script setup>
import { computed, onBeforeUnmount, onMounted, reactive, ref, watch } from 'vue';
import Panel from '@/Components/Panel.vue';
import PlayerPhoto from '@/Components/PlayerPhoto.vue';
import SectionTitle from '@/Components/SectionTitle.vue';
import MatchReplayPitch from '@/Pages/GameSaves/Play/MatchReplayPitch.vue';
import { DEFENSE_OPTIONS, GOOD_COUNTER_BONUS, SCRIPTED_ROLLS, fmt, useDuel } from './useDuel.js';

/**
 * Écran de match de la démo (ouvert par « Jouer » du Dashboard). Le terrain est le vrai
 * composant du jeu (MatchReplayPitch : 22 joueurs placés par formation, ballon, score), piloté
 * par une courte suite d'actions fictives : deux buts, puis une passe adverse à contrer.
 * Le duel qui suit se joue comme en match (règles : useDuel.js) et son verdict déplace le ballon.
 * `rigged` = visite automatique : dés truqués pour un bon contre réussi ; sinon dés aléatoires.
 */
const props = defineProps({
    demo:   { type: Object,  required: true },
    rigged: { type: Boolean, default: false },
});
defineEmits(['exit']);

const { squads, duel, me, opponent } = props.demo;
const { attacker, defender, scorer, playmaker, secondStriker } = duel;

// --- Suite d'actions (format des évènements du replay) : internal = notre équipe, à domicile.
const actor = (player) => ({ id: player.id, name: player.name, number: player.number });
const action = (turn, team, actionType, result, text, by = null, zone = 2) =>
    ({ turn, team, actionType, result, text, attacker: by ? actor(by) : undefined, zone, details: [] });
const DUEL_INDEX = 8;               // 9e action : la passe adverse à contrer ; la 10e en est la suite (selon le verdict)
const events = reactive([
    action(1, 'internal', 'kickoff', 'neutral', "Coup d'envoi"),
    action(2, 'internal', 'pass-success', 'success', `${playmaker.lastname} lance ${scorer.lastname} dans l'axe.`, playmaker, 3),
    action(3, 'internal', 'shot-goal', 'success', `But de ${scorer.name} !`, scorer, 4),
    action(4, 'external', 'kickoff', 'neutral', 'Engagement adverse'),
    action(5, 'external', 'dribble-success', 'success', `${attacker.lastname} efface la défense.`, attacker, 3),
    action(6, 'external', 'shot-goal', 'success', `But de ${secondStriker.name} : égalisation !`, secondStriker, 4),
    action(7, 'internal', 'kickoff', 'neutral', 'Remise en jeu'),
    action(8, 'external', 'dribble-success', 'success', `${attacker.lastname} remonte le terrain.`, attacker, 3),
    { ...action(9, 'external', 'pass', 'neutral', `${attacker.lastname} tente une passe…`, attacker, 3), defender: actor(defender) },
    action(10, 'internal', 'kickoff', 'neutral', '…'),   // suite du duel, remplie par le verdict
    action(11, 'internal', 'kickoff', 'neutral', '…'),   // action future : le replay n'arrive jamais « en fin de match »
]);
const DUEL_TEXT = events[DUEL_INDEX].text;
const FOLLOW_UP = events[DUEL_INDEX + 1];

const pitch = ref(null);
const { phase, choice, dice, good, bad, attackTotal, defenseTotal, outcome, revealed, play, reset } =
    useDuel({ attacker: () => attacker.attack, defender: () => defender.defense }, 'intro');

let intro = 0;
const reduced = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// Déroule les 7 premières actions puis place le terrain sur la passe adverse ; le choix de défense s'ouvre ensuite.
onMounted(() => {
    const target = DUEL_INDEX + 1;
    if (reduced()) { pitch.value?.seek(target); phase.value = 'choose'; return; }
    let step = 0;
    intro = setInterval(() => {
        step++;
        pitch.value?.seek(step);
        if (step >= target) { clearInterval(intro); setTimeout(() => { phase.value = 'choose'; }, 600); }
    }, 600);
});
onBeforeUnmount(() => clearInterval(intro));

// Le verdict du duel change le résultat de l'action, puis une action de suite déplace le ballon.
const VERDICTS = {
    defense: {
        result: 'failed', text: `Passe interceptée par ${defender.lastname} !`,
        banner: '✅ Passe interceptée : le ballon est à toi.', tone: 'bg-emerald-50 text-emerald-700',
        followUp: () => Object.assign(FOLLOW_UP, action(10, 'internal', 'pass-success', 'success', `${defender.lastname} relance proprement vers l'avant.`, defender, 3)),
    },
    attack: {
        result: 'success', text: `La passe de ${attacker.lastname} passe, l'attaque se poursuit.`,
        banner: "❌ La passe passe : l'adversaire garde le ballon.", tone: 'bg-rose-50 text-rose-600',
        followUp: () => Object.assign(FOLLOW_UP, action(10, 'external', 'dribble-success', 'success', `${attacker.lastname} se présente devant ton but !`, attacker, 4)),
    },
    tie: { result: 'neutral', text: 'Duel équilibré, le ballon reste au milieu.', banner: '⚖️ Duel équilibré.', tone: 'bg-slate-100 text-slate-600', followUp: null },
};
const verdict = computed(() => VERDICTS[outcome.value]);
let followTimer = 0;
watch(revealed, (isRevealed) => {
    if (!isRevealed) return;
    events[DUEL_INDEX].result = verdict.value.result;
    events[DUEL_INDEX].text = verdict.value.text;
    if (!verdict.value.followUp) return;
    followTimer = setTimeout(() => {
        verdict.value.followUp();
        pitch.value?.seek(DUEL_INDEX + 2);
    }, 1100);
});
onBeforeUnmount(() => clearTimeout(followTimer));

const choose = (key) => play(key, props.rigged ? SCRIPTED_ROLLS : null);
function retry() {
    clearTimeout(followTimer);
    events[DUEL_INDEX].result = 'neutral';
    events[DUEL_INDEX].text = DUEL_TEXT;
    Object.assign(FOLLOW_UP, action(10, 'internal', 'kickoff', 'neutral', '…'));
    pitch.value?.seek(DUEL_INDEX + 1);
    reset('choose');
}
</script>

<template>
    <div class="grid gap-3 md:grid-cols-[minmax(0,1.3fr)_minmax(0,1fr)]">
        <Panel tone="white" padding="sm">
            <MatchReplayPitch ref="pitch" :events="events" :controls="false"
                              :home-name="me.name" :away-name="opponent.name" :home-logo="me.logo" :away-logo="opponent.logo"
                              :final-home-score="1" :final-away-score="1"
                              :home-squad="squads.home" :away-squad="squads.away" />
        </Panel>

        <Panel tone="white" padding="md" class="flex flex-col">
            <SectionTitle>L'adversaire attaque</SectionTitle>
            <div class="mt-2 flex items-center gap-3">
                <PlayerPhoto :src="attacker.photo" :zoomable="false" class="h-12 w-12 rounded-full object-cover object-top ring-2 ring-orange-300" />
                <div>
                    <p class="text-sm font-bold text-slate-800">{{ attacker.name }}</p>
                    <p class="text-2xs text-slate-500"><span class="font-semibold text-orange-500">tente une passe ➡️</span> · stat {{ attacker.attack }}</p>
                </div>
            </div>

            <SectionTitle class="mt-3">Ta défense · {{ defender.lastname }} ({{ defender.defense }})</SectionTitle>
            <ul class="mt-2 grid grid-cols-3 gap-2" aria-label="Défenses possibles">
                <li v-for="o in DEFENSE_OPTIONS" :key="o.key">
                    <button type="button" :data-demo="`duel-${o.key}`" :disabled="phase !== 'choose'" @click="choose(o.key)"
                            class="w-full rounded-lg border px-2 py-1.5 text-center text-2xs font-semibold transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-400"
                            :class="[
                                choice === o.key ? 'border-teal-400 bg-teal-50 text-teal-700' : 'border-slate-200 bg-white text-slate-600',
                                phase === 'choose' ? 'cursor-pointer hover:border-teal-300 hover:bg-slate-50' : 'cursor-default',
                                phase !== 'choose' && choice !== o.key ? 'opacity-50' : '',
                            ]">
                        <span class="block text-base" aria-hidden="true">{{ o.icon }}</span>{{ o.label }}
                    </button>
                </li>
            </ul>
            <p class="mt-2 text-3xs leading-4 text-slate-500">
                <strong class="text-teal-700">Intercepter</strong> est le bon contre face à une passe : +{{ GOOD_COUNTER_BONUS }} pour ta défense. Un mauvais choix donne ce bonus à l'attaque.
            </p>

            <dl class="mt-3 space-y-1.5 text-xs">
                <div class="rounded-lg bg-slate-50 px-3 py-1.5">
                    <dt class="flex justify-between font-semibold text-slate-600"><span>{{ attacker.lastname }}</span><span class="font-mono text-slate-800">{{ revealed ? fmt(attackTotal) : '–' }}</span></dt>
                    <dd class="font-mono text-2xs text-slate-400">
                        {{ attacker.attack }} + dé <span class="duel-die">{{ dice.att || '?' }}</span> ÷ 2
                        <span v-if="revealed && bad" class="text-orange-500">+ {{ GOOD_COUNTER_BONUS }} mauvais contre</span>
                    </dd>
                </div>
                <div class="rounded-lg bg-slate-50 px-3 py-1.5">
                    <dt class="flex justify-between font-semibold text-slate-600"><span>{{ defender.lastname }} <span class="font-normal text-slate-400">(toi)</span></span><span class="font-mono text-teal-700">{{ revealed ? fmt(defenseTotal) : '–' }}</span></dt>
                    <dd class="font-mono text-2xs text-slate-400">
                        {{ defender.defense }} + dé <span class="duel-die">{{ dice.def || '?' }}</span> ÷ 2
                        <span v-if="revealed && good" class="text-teal-600">+ {{ GOOD_COUNTER_BONUS }} bon contre</span>
                    </dd>
                </div>
            </dl>
            <p v-if="revealed" class="mt-2 rounded-lg px-3 py-1.5 text-xs font-bold" :class="verdict.tone" aria-live="polite">{{ verdict.banner }}</p>
            <div v-if="revealed" class="mt-auto flex flex-wrap gap-2 pt-3">
                <button type="button" class="rounded-full border border-slate-200 px-3 py-1 text-2xs font-semibold text-slate-600 hover:bg-slate-50" @click="retry">↻ Rejouer le duel</button>
                <button type="button" data-demo="duel-back" class="rounded-full bg-teal-500 px-3 py-1 text-2xs font-bold text-white hover:bg-teal-400" @click="$emit('exit')">Retour au tableau de bord</button>
            </div>
        </Panel>
    </div>
</template>

<style scoped>
/* Dé d20 : petit carré. */
.duel-die {
    display: inline-flex;
    min-width: 1.6em;
    justify-content: center;
    padding: 0 0.25em;
    border: 1px solid rgb(45 212 191 / 0.6);
    border-radius: 3px;
    background: white;
    color: rgb(15 118 110);
}
</style>
