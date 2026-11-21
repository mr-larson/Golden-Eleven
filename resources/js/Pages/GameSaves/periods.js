// Périodes de jeu : formats de compétition qu'elles autorisent et univers de joueurs actifs par défaut.
// Source unique côté interface (création de partie) ; le serveur applique la même règle
// (GameSave::PERIOD_COMPETITIONS). Une nouvelle période (ex. « World Youth ») s'ajoute ici.

const ALL_UNIVERSES = {
    visible_origins: {
        captain_tsubasa: true, captain_tsubasa_international: true, world_legends: true,
        ecole_des_champions: true, hungry_heart: true, blue_lock: true, ao_ashi: true, original: true,
    },
    internationals_visible: true,
    legends_countries: null,
};

export const PERIODS = {
    // Collège : clubs et joueurs japonais + joueurs de base ; les autres univers restent activables à la main.
    college: {
        label: 'Collège',
        competitions: ['college_league', 'club_cup'],
        universes: () => ({
            visible_origins: {
                captain_tsubasa: true, captain_tsubasa_international: true, world_legends: false,
                ecole_des_champions: false, hungry_heart: false, blue_lock: false, ao_ashi: false, original: true,
            },
            internationals_visible: false,
            legends_countries: null,
        }),
    },
    // Professionnel : clubs historiques bâtis avec tous les univers, tous les formats.
    pro: {
        label: 'Professionnel',
        competitions: ['college_league', 'club_cup', 'world_cup'],
        universes: () => structuredClone(ALL_UNIVERSES),
    },
};

/** Vrai si la période autorise ce format de compétition. */
export const periodAllows = (period, competitionType) => !!PERIODS[period]?.competitions.includes(competitionType);

/** Univers de joueurs actifs par défaut pour la période. */
export const universePreset = (period) => (PERIODS[period] ?? PERIODS.college).universes();
