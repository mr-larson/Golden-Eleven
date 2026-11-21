// Types de compétition d'une partie : métadonnées et libellé uniques, réutilisés par tous les écrans
// (création, choix d'équipe, jeu, fin de tournoi).

export const COMPETITIONS = {
    college_league: {
        label: 'Ligue', tournament: false, icon: '🏫', tone: 'teal', start: '⚽ Démarrer la partie',
        text: 'Championnat des collèges en matchs aller-retour. Le mode classique de Captain Tsubasa.',
    },
    world_cup: {
        label: 'Coupe Nation', tournament: true, icon: '🌍', tone: 'indigo', start: '🌍 Choisir ma sélection',
        text: 'Tournoi des sélections nationales : poules puis élimination directe. Mène ta nation au titre.',
    },
    club_cup: {
        label: 'Coupe Club', tournament: true, icon: '🏆', tone: 'amber', start: '🏆 Choisir mon club',
        text: 'Tournoi de clubs : poules puis élimination directe. Mène ton club au titre.',
    },
};

/** Libellé d'une compétition (« Ligue » par défaut). */
export const competitionLabel = (type) => (COMPETITIONS[type] ?? COMPETITIONS.college_league).label;

/** Vrai pour les compétitions au format tournoi (poules puis élimination directe). */
export const isTournamentType = (type) => !!COMPETITIONS[type]?.tournament;
