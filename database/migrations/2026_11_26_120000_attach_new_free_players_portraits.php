<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Attache les portraits (public/storage/players/slug.webp) des sept agents libres ajoutés le 26 novembre.
 * Crédits dans `resources/data/player-illustration-credits.json`. Un portrait déjà choisi n'est jamais remplacé.
 */
return new class extends Migration
{
    private const NAMES = [
        'Mei Nagai', 'Maria Moreno', 'Aya Fuchise', 'Rinka Nanasaki',
        'Eiji Tsukiori', 'Ayane Miyazaki', 'Kotone Miyazaki',
    ];

    public function up(): void
    {
        foreach (self::NAMES as $fullName) {
            [$first, $last] = explode(' ', $fullName);
            $path = 'players/'.Str::slug($fullName).'.webp';
            if (is_file(public_path('storage/'.$path))) {
                DB::table('players')->where('origin', 'captain_tsubasa')->where('firstname', $first)->where('lastname', $last)
                    ->where(fn ($q) => $q->whereNull('photo_path')->orWhere('photo_path', ''))
                    ->update(['photo_path' => $path, 'updated_at' => now()]);
            }
        }
    }

    public function down(): void
    {
        // Conserver les références des portraits.
    }
};
