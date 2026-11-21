<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * Période affichée dans les écrans d'édition de la base (joueurs, équipes, contrats) : `college` ou `pro`.
 *
 * Le choix vient de `?period=` puis reste en session : les redirections après création ou
 * modification (qui n'ont pas ce paramètre) retombent donc sur la même période.
 */
final class EditorPeriod
{
    /** Périodes proposées, avec leur libellé. */
    public const LABELS = ['college' => 'Collège', 'pro' => 'Professionnel'];

    /** Période courante : celle demandée dans l'URL (mémorisée), sinon celle de la session, sinon le Collège. */
    public static function current(Request $request): string
    {
        $asked = $request->query('period');
        if (is_string($asked) && isset(self::LABELS[$asked])) {
            $request->session()->put('editor_period', $asked);
        }

        $period = $request->session()->get('editor_period', 'college');

        return isset(self::LABELS[$period]) ? $period : 'college';
    }
}
