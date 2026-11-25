<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Attache l'écusson de Nishigaoka (public/storage/teams/nishigaoka.webp) au club s'il n'a pas encore de logo.
 * Les seeders le retrouvent seuls sur une base neuve.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (is_file(public_path('storage/teams/nishigaoka.webp'))) {
            DB::table('teams')->where('name', 'Nishigaoka')
                ->where(fn ($query) => $query->whereNull('logo_path')->orWhere('logo_path', ''))
                ->update(['logo_path' => 'teams/nishigaoka.webp', 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        // Conserver la référence du logo.
    }
};
