<?php

namespace App\Policies;

use App\Models\Nation;
use App\Models\User;

/** Nations du catalogue (données de référence) : consultation et modification réservées aux administrateurs. */
class NationPolicy
{
    /** Liste des nations réservée aux administrateurs. */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /** Modification du style, de la philosophie et de la description réservée aux administrateurs. */
    public function update(User $user, Nation $nation): bool
    {
        return $user->isAdmin();
    }
}
