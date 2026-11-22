<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Le Pepe de Captain Tsubasa devient « Pepe Santos » : son slug (« pepe ») était celui du Pepe portugais
 * des légendes réelles, ce qui mélangeait nationalité, portrait et techniques entre les deux joueurs.
 * Les parties existantes gardent leur copie du joueur.
 */
return new class extends Migration
{
    /** Renomme le Pepe brésilien du catalogue Captain Tsubasa. */
    public function up(): void
    {
        DB::table('players')->where('origin', 'captain_tsubasa_international')
            ->where('firstname', '')->where('lastname', 'Pepe')
            ->update(['firstname' => 'Pepe', 'lastname' => 'Santos', 'updated_at' => now()]);
    }

    public function down(): void
    {
        // Les joueurs peuvent être référencés par une partie existante.
    }
};
