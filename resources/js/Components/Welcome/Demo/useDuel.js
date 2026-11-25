import { computed, onBeforeUnmount, ref } from 'vue';

/**
 * Logique du duel de la vitrine, partagée par « Un tour de match » (DemoDuel) et l'écran
 * de match de la démo (DemoMatch). Règles reprises du moteur réel (Match/engine) :
 * score = stat + dé d20 / 2 ; le bon contre donne +2 à la défense, un mauvais choix +2 à
 * l'attaque ; égalité = duel équilibré. Les dés aléatoires évitent les critiques (1 et 20).
 */
export const GOOD_COUNTER_BONUS = 2;
export const DEFENSE_OPTIONS = [
    { key: 'intercept', label: 'Interception', icon: '✋', good: true },   // bon contre face à une passe
    { key: 'tackle',    label: 'Tacle',        icon: '⚔️', good: false },
    { key: 'block',     label: 'Contre',       icon: '🧱', good: false },
];
export const SCRIPTED_ROLLS = { att: 7, def: 14 };
export const fmt = (n) => n.toLocaleString('fr-FR', { maximumFractionDigits: 1 });

// `stats.attacker` / `stats.defender` : fonctions renvoyant la stat en jeu (réactives si besoin).
export function useDuel(stats, initialPhase = 'choose') {
    const phase = ref(initialPhase);     // intro → choose → rolling → result
    const choice = ref(null);
    const dice = ref({ att: 0, def: 0 });
    let timer = 0;

    const good = computed(() => !!DEFENSE_OPTIONS.find(o => o.key === choice.value)?.good);
    const bad = computed(() => !!choice.value && !good.value);
    const attackTotal = computed(() => stats.attacker() + dice.value.att / 2 + (bad.value ? GOOD_COUNTER_BONUS : 0));
    const defenseTotal = computed(() => stats.defender() + dice.value.def / 2 + (good.value ? GOOD_COUNTER_BONUS : 0));
    const outcome = computed(() => attackTotal.value > defenseTotal.value ? 'attack' : attackTotal.value < defenseTotal.value ? 'defense' : 'tie');
    const revealed = computed(() => phase.value === 'result');

    // Lancer : les dés « défilent » ~0,9 s puis se figent (valeurs `forced` pour un scénario truqué).
    function play(key, forced = null, onDone = null) {
        if (phase.value !== 'choose') return;
        choice.value = key;
        phase.value = 'rolling';
        const finals = forced ?? { att: 2 + Math.floor(Math.random() * 18), def: 2 + Math.floor(Math.random() * 18) };
        let ticks = 0;
        clearInterval(timer);
        timer = setInterval(() => {
            ticks++;
            if (ticks >= 11) {
                clearInterval(timer);
                dice.value = finals;
                phase.value = 'result';
                onDone?.();
                return;
            }
            dice.value = { att: 1 + Math.floor(Math.random() * 20), def: 1 + Math.floor(Math.random() * 20) };
        }, 80);
    }

    function reset(nextPhase = 'choose') {
        clearInterval(timer);
        choice.value = null;
        dice.value = { att: 0, def: 0 };
        phase.value = nextPhase;
    }

    onBeforeUnmount(() => clearInterval(timer));

    return { phase, choice, dice, good, bad, attackTotal, defenseTotal, outcome, revealed, play, reset };
}
