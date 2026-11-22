// Petits helpers d'affichage d'un joueur, partagés par l'effectif (SelectionRoster) et sa fiche détaillée (PlayerDetailModal).
import { positionGroupClass } from '@/Enums/positionBadges.js';
import { usePlayerUtils } from '@/Pages/GameSaves/Play/usePlayerUtils.js';

/** Valeur d'une stat, lue à plat ou dans `stats` (0 si absente). */
export const statValue = (p, k) => Number(p?.[k] ?? p?.stats?.[k] ?? 0);

/** Teinte du chiffre selon la valeur (lecture rapide des forces / faiblesses). */
export const statTone = (v) => Number(v) >= 70 ? 'text-emerald-600' : Number(v) >= 50 ? 'text-amber-600' : 'text-slate-500';

/** Pastille de note globale (mêmes seuils que les chiffres). */
export const overallBadgeClass = (o) => o >= 70 ? 'bg-emerald-50 border-emerald-300 text-emerald-700'
    : o >= 50 ? 'bg-amber-50 border-amber-300 text-amber-700'
    : 'bg-slate-100 border-slate-200 text-slate-500';

/** Endurance : du rouge (épuisé) au vert (en pleine forme). */
export const staminaBar  = (v) => v >= 85 ? 'bg-emerald-500' : v >= 70 ? 'bg-lime-500' : v >= 50 ? 'bg-amber-400' : v >= 30 ? 'bg-orange-500' : 'bg-rose-500';
export const staminaText = (v) => v >= 85 ? 'text-emerald-600' : v >= 70 ? 'text-lime-600' : v >= 50 ? 'text-amber-600' : v >= 30 ? 'text-orange-600' : 'text-rose-600';

/** Badge de poste coloré (couleurs communes, libellé brut conservé). */
export const posBadgeClass = (pos) => positionGroupClass(usePlayerUtils().positionGroup(pos));
