import { STAT_COLORS, usePlayerUtils } from '@/Pages/GameSaves/Play/usePlayerUtils.js';
import { averageTeamStat, TEAM_STATS } from '@/Pages/GameSaves/Play/teamStats.js';

/**
 * Force d'une équipe à partir de la liste de ses joueurs (Ligue comme sélection nationale) :
 *  - teamStatsOf(players)  : barres de statistiques moyennes (+ moral s'il est connu) ;
 *  - teamRatingOf(players) : note globale { note, level, morale, stamina } ou null sans joueur.
 *
 * La note part du niveau du onze probable (moyenne des 11 meilleures notes), corrigé par le moral
 * (effet en match moyen, -10 % à +5 %) et l'endurance moyenne (-10 % à +10 % autour de 60).
 */
export function useTeamStrength() {
    const { overallOf, moraleState, moraleMatchEffect } = usePlayerUtils();

    const statValue = (p, k) => Number(p?.[k] ?? p?.stats?.[k] ?? 0);
    const moralePct = (m) => parseFloat(moraleMatchEffect(m)?.pct ?? '0') || 0;

    function teamStatsOf(players) {
        const rows = TEAM_STATS.map(({ key, label, color }) => ({
            key, label, color: color ?? STAT_COLORS[key], value: averageTeamStat(players, key),
        }));
        const morales = players.map(p => p.morale_preview).filter(v => v != null);
        if (morales.length) {
            const avg = Math.round(morales.reduce((a, v) => a + Number(v), 0) / morales.length);
            const state = moraleState(avg);
            const afterStamina = rows.findIndex(stat => stat.key === 'stamina') + 1;
            rows.splice(afterStamina, 0, { key: 'morale', label: 'Moral', value: avg, color: state.bar, text: state.text });
        }
        return rows;
    }

    function teamRatingOf(players) {
        if (!players.length) return null;
        const best  = [...players].sort((a, b) => overallOf(b) - overallOf(a)).slice(0, 11);
        const level = best.reduce((sum, p) => sum + overallOf(p), 0) / best.length;
        const morales = players.map(p => p.morale_preview).filter(v => v != null);
        const morale  = morales.length ? morales.reduce((sum, m) => sum + moralePct(m), 0) / morales.length : 0;
        const stamina = players.reduce((sum, p) => sum + statValue(p, 'stamina'), 0) / players.length;
        const staminaAdj = Math.max(-10, Math.min(10, (stamina - 60) / 4));
        return {
            note:    Math.round(level * (1 + morale / 100) * (1 + staminaAdj / 100)),
            level:   Math.round(level),
            morale:  Math.round(morale * 10) / 10,
            stamina: Math.round(staminaAdj * 10) / 10,
        };
    }

    return { teamStatsOf, teamRatingOf };
}
