<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import TabBar from '@/Components/TabBar.vue';
import DemoDashboard from './DemoDashboard.vue';
import DemoTeam from './DemoTeam.vue';
import DemoStandings from './DemoStandings.vue';
import DemoTransfers from './DemoTransfers.vue';
import DemoCards from './DemoCards.vue';
import DemoCursor from './DemoCursor.vue';
import DemoMatch from './DemoMatch.vue';
import { buildDemo } from './demoData.js';

/**
 * Visite guidée du jeu pour la page d'accueil : fausse fenêtre de partie dont les
 * écrans sont de vrais composants alimentés par des données figées.
 *
 * - Visible à l'écran : un faux curseur parcourt les onglets et joue quelques gestes.
 * - Dès que le visiteur clique ou touche la fenêtre : la visite s'arrête et il navigue lui-même.
 * - « Revoir la visite » relance le scénario. Pas de curseur sur mobile ni avec
 *   prefers-reduced-motion (onglets seulement / aucune animation).
 */
const props = defineProps({
    teams:   { type: Array, default: () => [] },
    players: { type: Array, default: () => [] },
});

const demo = computed(() => buildDemo(props.teams, props.players));

const TABS = [
    { key: 'dashboard', label: 'Dashboard' },
    { key: 'team',      label: 'Mon équipe' },
    { key: 'standings', label: 'Classement' },
    { key: 'transfers', label: 'Transferts' },
    { key: 'cards',     label: 'Cartes bonus' },
];
const SCREENS = { dashboard: DemoDashboard, team: DemoTeam, standings: DemoStandings, transfers: DemoTransfers, cards: DemoCards };
// Gestes joués par le curseur une fois sur l'onglet : cibles repérées par data-demo.
const GESTURES = {
    dashboard: [{ click: 'play' }, { click: 'duel-intercept', then: 2600 }, { click: 'duel-back' }],
    team:      [{ click: 'player-3' }, { click: 'player-5' }],
    standings: [{ click: 'row-me' }],
    transfers: [{ click: 'recruit-1' }, { click: 'recruit-3' }],
    cards:     [{ click: 'card-2' }],
};
const CAPTIONS = {
    dashboard: "Entre deux matchs : classement, budget, état de l'effectif et prochain adversaire, sur un seul écran.",
    team:      'Consulte chaque joueur : poste, stats et endurance.',
    standings: 'Suis ta progression dans le classement de la compétition.',
    transfers: 'Recrute sur le marché tant que ton budget le permet.',
    cards:     'Joue des cartes bonus (ou malus pour ton adversaire) au bon moment.',
};

const active = ref('dashboard');
const inMatch = ref(false);        // écran de match ouvert depuis le Dashboard (« Jouer »)
const root = ref(null);
const cursor = ref({ x: 0, y: 0, visible: false, pressed: false });
const playing = ref(false);
const stoppedByUser = ref(false);
let runId = 0;
let observer;

const reduced = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const compact = () => window.matchMedia('(max-width: 767px)').matches;

// Attente annulable : renvoie false si la visite a été arrêtée entre-temps.
const wait = (ms, id) => new Promise(resolve => setTimeout(() => resolve(id === runId), ms));

function moveTo(element) {
    const base = root.value.getBoundingClientRect();
    const rect = element.getBoundingClientRect();
    cursor.value = { ...cursor.value, visible: true, x: rect.left - base.left + rect.width * 0.55, y: rect.top - base.top + rect.height * 0.6 };
}

// Cherche un élément qui peut apparaître avec un léger retard (changement d'écran animé).
async function findTarget(selector, id) {
    for (let attempt = 0; attempt < 60; attempt++) {
        const element = root.value?.querySelector(selector);
        if (element) return element;
        if (!(await wait(150, id))) return null;
    }
    return null;
}

async function pressOn(element, id) {
    cursor.value = { ...cursor.value, pressed: true };
    if (!(await wait(140, id))) return false;
    element.click();
    cursor.value = { ...cursor.value, pressed: false };
    return true;
}

async function run() {
    const id = ++runId;
    playing.value = true;
    const desktop = !compact();
    if (desktop) cursor.value = { x: 40, y: 60, visible: true, pressed: false };

    while (id === runId) {
        for (const tab of TABS) {
            if (id !== runId) return;
            if (!desktop) {
                active.value = tab.key;
                if (!(await wait(3500, id))) return;
                continue;
            }
            const button = root.value?.querySelector(`[data-tab="${tab.key}"]`);
            if (button) {
                moveTo(button);
                if (!(await wait(900, id))) return;
                if (active.value !== tab.key && !(await pressOn(button, id))) return;
            }
            if (!(await wait(800, id))) return;
            for (const gesture of GESTURES[tab.key]) {
                const target = await findTarget(`[data-demo="${gesture.hover ?? gesture.click}"]:not(:disabled)`, id);
                if (id !== runId) return;
                if (!target) continue;
                moveTo(target);
                if (!(await wait(950, id))) return;
                if (gesture.click && !(await pressOn(target, id))) return;
                if (!(await wait(900 + (gesture.then ?? 0), id))) return;   // `then` : laisse le temps de voir l'effet du geste
            }
            if (!(await wait(500, id))) return;
        }
    }
}

function stop() {
    runId++;
    playing.value = false;
    cursor.value = { ...cursor.value, visible: false, pressed: false };
}

// Le visiteur prend la main : plus de curseur, il navigue seul.
function takeControl() {
    if (!playing.value && stoppedByUser.value) return;
    stoppedByUser.value = true;
    stop();
}

function replay() {
    stoppedByUser.value = false;
    active.value = 'dashboard';
    inMatch.value = false;
    run();
}

onMounted(() => {
    if (!demo.value) return;
    if (reduced()) { stoppedByUser.value = true; return; }
    observer = new IntersectionObserver(([entry]) => {
        if (entry.isIntersecting && !stoppedByUser.value && !playing.value) run();
        else if (!entry.isIntersecting && playing.value) stop();
    }, { threshold: 0.6 });
    observer.observe(root.value);
});
onBeforeUnmount(() => { runId++; observer?.disconnect(); });
</script>

<template>
    <figure v-if="demo" class="neon-screen overflow-hidden rounded-2xl border border-white/10 bg-slate-700 shadow-2xl">
        <div class="flex items-center justify-between gap-3 bg-slate-600/60 px-4 py-2.5">
            <span class="text-sm font-semibold text-slate-100">Session de jeu — {{ demo.me.name }}</span>
            <button v-if="!playing" type="button" class="rounded-full border border-white/15 px-3 py-1 text-xs font-medium text-slate-100 transition-colors hover:bg-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-300" @click="replay">
                ▶ Revoir la visite
            </button>
            <span v-else class="text-xs text-teal-300">Visite guidée · clique pour prendre la main</span>
        </div>

        <div ref="root" class="relative bg-slate-100 p-3 md:p-4" role="group" aria-label="Aperçu interactif du jeu"
             @pointerdown="takeControl" @keydown="takeControl">
            <TabBar v-model="active" :tabs="TABS" size="sm" label="Onglets de la démo" @update:model-value="inMatch = false" />
            <div class="mt-3 min-h-[440px] md:h-[470px] md:overflow-y-auto">
                <Transition name="demo-fade" mode="out-in">
                    <DemoMatch v-if="inMatch" key="match" :demo="demo" :rigged="playing" @exit="inMatch = false" />
                    <component :is="SCREENS[active]" v-else :key="active" :demo="demo" @play-match="inMatch = true" />
                </Transition>
            </div>

            <DemoCursor v-bind="cursor" />
        </div>

        <figcaption class="px-4 py-3 text-sm text-slate-300">{{ inMatch ? 'Pendant le match : choisis ta défense, le dé décide du duel.' : CAPTIONS[active] }}</figcaption>
    </figure>
</template>

<style scoped>
.demo-fade-enter-active, .demo-fade-leave-active { transition: opacity 0.18s ease; }
.demo-fade-enter-from, .demo-fade-leave-to { opacity: 0; }
@media (prefers-reduced-motion: reduce) {
    .demo-fade-enter-active, .demo-fade-leave-active { transition: none; }
}
</style>
