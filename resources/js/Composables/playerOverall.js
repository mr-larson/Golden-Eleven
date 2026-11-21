import { PLAYER_STATS } from '@/Enums/playerStats.js';
import { playerStat } from '@/Pages/GameSaves/Play/recruitment.js';

/**
 * OVR d'un joueur : moyenne de ses stats renseignées (> 0). `stats` (JSON de la fiche) fait foi,
 * sinon les stats à plat d'un joueur de partie. Valeur exacte (non arrondie) ; l'affichage la formate.
 */
export function playerOverall(player) {
    const values = PLAYER_STATS
        .map(({ key }) => Number(player?.stats?.[key] ?? playerStat(player, key)))
        .filter(v => Number.isFinite(v) && v > 0);
    return values.length ? values.reduce((a, b) => a + b, 0) / values.length : 0;
}
