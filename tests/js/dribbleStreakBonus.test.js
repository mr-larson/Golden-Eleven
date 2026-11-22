import { describe, it, expect } from 'vitest';
import { dribbleStreakBonus } from '../../resources/js/Pages/Match/engine/resolvers/duelRules.js';
import { DUEL_RULES } from '../../resources/js/Pages/Match/engine/constants.js';

describe('dribbleStreakBonus', () => {
    it('aucun avantage sans série ou hors dribble', () => {
        expect(dribbleStreakBonus({ attackType: 'dribble', streak: 0 })).toBeNull();
        expect(dribbleStreakBonus({ attackType: 'pass', streak: 2 })).toBeNull();
        expect(dribbleStreakBonus({ attackType: 'shot', streak: 3 })).toBeNull();
    });

    it('+5 % par dribble réussi d\'affilée', () => {
        expect(dribbleStreakBonus({ attackType: 'dribble', streak: 1 }).factor).toBeCloseTo(1.05);
        expect(dribbleStreakBonus({ attackType: 'dribble', streak: 2 }).factor).toBeCloseTo(1.10);
    });

    it('plafonné à 3 paliers', () => {
        const max = 1 + DUEL_RULES.DRIBBLE_STREAK_STEP * DUEL_RULES.DRIBBLE_STREAK_MAX_STEPS;
        expect(dribbleStreakBonus({ attackType: 'dribble', streak: 3 }).factor).toBeCloseTo(max);
        expect(dribbleStreakBonus({ attackType: 'dribble', streak: 9 }).factor).toBeCloseTo(max);
    });
});
