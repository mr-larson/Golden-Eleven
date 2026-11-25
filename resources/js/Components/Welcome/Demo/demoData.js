/**
 * Données figées de la visite guidée de la page d'accueil.
 * Construites à partir des vraies équipes/joueurs reçus par Welcome (logos et
 * portraits réels), mais avec des chiffres fixes : aucune logique de jeu ici.
 * Retourne null si l'échantillon est trop pauvre (la page affiche alors la capture).
 */
const num = (value, fallback) => (Number.isFinite(Number(value)) ? Number(value) : fallback);

// Résultats figés : [V, N, D] après 7 journées, du 1er au 8e.
const RECORDS = [[5, 1, 1], [4, 2, 1], [3, 3, 1], [3, 1, 3], [2, 3, 2], [2, 1, 4], [1, 2, 4], [0, 2, 5]];
const ME_RANK = 4;                  // l'équipe du joueur est 4e dans la démo
const START_BUDGET = 2440;

const ME_NAME = 'Nankatsu';         // le club « du joueur » dans la démo (comme sur la capture d'origine)

const toPlayer = (player, index) => {
    const stats = player.stats ?? {};
    const attack = num(stats.attack, 18 + (index % 5) * 2);
    const defense = num(stats.defense, 16 + ((index + 2) % 5) * 2);
    return {
        id: player.id,
        teamId: player.team_id,
        name: `${player.firstname} ${player.lastname}`,
        lastname: player.lastname,
        position: player.position,
        photo: `/storage/${player.photo_path}`,
        attack,
        defense,
        stamina: num(stats.stamina, 55 + ((index * 17) % 41)),
        price: Math.round(60 + (attack + defense) * 4),
    };
};

const FORMATION = '3-2-3-2';        // 1 GB, 3 DEF, 5 MIL, 2 ATT : un effectif Collège (2/5/5/2) le remplit sans reste

// Effectif complet pour MatchReplayPitch : 11 titulaires (meilleurs de chaque ligne) + remplaçants, numérotés.
function squadOf(pool, teamId) {
    const all = pool.filter(player => player.teamId === teamId);
    const line = (position, count, key) => all.filter(player => player.position === position).sort((a, b) => b[key] - a[key]).slice(0, count);
    const starters = [...line('Goalkeeper', 1, 'defense'), ...line('Defender', 3, 'defense'), ...line('Midfielder', 5, 'attack'), ...line('Forward', 2, 'attack')];
    const ids = new Set(starters.map(player => player.id));
    const ordered = [...starters, ...all.filter(player => !ids.has(player.id))];
    return {
        formation: FORMATION,
        players: ordered.map((player, index) => ({ id: player.id, name: player.name, lastname: player.lastname, position: player.position, number: index + 1, is_starter: ids.has(player.id), attack: player.attack, defense: player.defense, photo: player.photo })),
    };
}

export function buildDemo(teams = [], players = []) {
    const all = teams.map(team => ({ id: team.id, name: team.name, logo: team.logo_path ? `/storage/${team.logo_path}` : null }));
    const pool = players.filter(player => player.photo_path).map(toPlayer);   // déjà triés par attaque décroissante
    const meClub = all.find(club => club.name === ME_NAME) ?? all[0];
    if (!meClub || all.length < 8) return null;

    const squad = pool.filter(player => player.teamId === meClub.id).slice(0, 9);
    // Marché : les meilleurs joueurs des autres clubs, un seul par club pour varier les maillots.
    const seen = new Set();
    const market = pool.filter(player => {
        if (player.teamId === meClub.id || seen.has(player.teamId)) return false;
        seen.add(player.teamId);
        return true;
    }).slice(0, 5);
    if (squad.length < 9 || market.length < 5) return null;

    // 8 clubs au classement, l'équipe du joueur y figure à la 4e place.
    const clubs = all.filter(club => club.id !== meClub.id).slice(0, 7);
    clubs.splice(ME_RANK - 1, 0, meClub);
    const standings = clubs.map((club, index) => {
        const [w, d, l] = RECORDS[index];
        return { ...club, rank: index + 1, played: w + d + l, w, d, l, pts: w * 3 + d, gd: (4 - index) * 2 + (index > 4 ? -1 : 1) };
    });
    const me = standings[ME_RANK - 1];
    const opponent = standings[5];

    // Match de la démo : effectifs complets, puis le duel (meilleur attaquant adverse face à notre meilleur défenseur).
    const home = squadOf(pool, meClub.id);
    const away = squadOf(pool, opponent.id);
    const outfield = (squadPlayers, key) => squadPlayers.players.filter(player => player.is_starter && player.position !== 'Goalkeeper').sort((a, b) => b[key] - a[key]);
    const [striker, secondStriker] = outfield(away, 'attack');
    const [keeper] = outfield(home, 'defense');
    const [scorer, playmaker] = outfield(home, 'attack');
    if (!striker || !secondStriker || !keeper || !scorer || !playmaker) return null;

    return {
        standings, me, opponent,
        squads: { home, away },
        duel: { attacker: striker, defender: keeper, scorer, playmaker, secondStriker },
        record: { w: me.w, d: me.d, l: me.l, pts: me.pts },
        budget: START_BUDGET,
        rosterSize: pool.filter(player => player.teamId === meClub.id).length,
        squad,
        market,
        cards: [
            { icon: '🏋️', name: 'Séance intensive', kind: 'Bonus',  desc: "Boost d'entraînement pour toute l'équipe cette semaine.", price: 120 },
            { icon: '🩹', name: 'Soigneur miracle',  kind: 'Bonus',  desc: 'Remet un blessé sur pied immédiatement.',               price: 200 },
            { icon: '😈', name: 'Pression adverse',  kind: 'Malus',  desc: "Fatigue légèrement l'adversaire avant le prochain match.", price: 160 },
        ],
    };
}
