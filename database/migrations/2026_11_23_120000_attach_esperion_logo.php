<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Attache l'écusson d'Esperion (public/storage/teams/esperion.webp) aux clubs Esperion des trois périodes
 * qui n'ont pas encore de logo. Les seeders le retrouvent seuls sur une base neuve.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (is_file(public_path('storage/teams/esperion.webp'))) {
            DB::table('teams')->where('name', 'Esperion')
                ->where(fn ($query) => $query->whereNull('logo_path')->orWhere('logo_path', ''))
                ->update(['logo_path' => 'teams/esperion.webp', 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        // Conserver la référence du logo.
    }
};
