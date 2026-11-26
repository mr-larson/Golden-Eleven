<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Remplace les descriptions provisoires des sept agents libres ajoutés le 26 novembre par des textes
 * fidèles à leur fiche du wiki Captain Tsubasa (club, rôle, technique signature).
 */
return new class extends Migration
{
    private const DESCRIPTIONS = [
        "Mei Nagai" => "Gardienne et capitaine du Nikko SC, Nagai pousse son équipe à s'entraîner dès l'aube. Ces séances, avec Maria Moreno, lui ont fait acquérir son arrêt « Mist Catch ». Elle poursuit ensuite au collège de Nikko.",
        "Maria Moreno" => "Attaquante argentine arrivée à Nikko comme élève d'échange, Moreno marque déjà des hat-tricks mais exige un meilleur niveau collectif. Séduite par l'entraînement matinal de l'équipe, elle rejoint le Nikko SC et ouvre le score dès le premier match du tournoi.",
        "Aya Fuchise" => "Défenseuse de Hidaka, douce et passionnée, Fuchise veut faire aimer le football à tous. Elle forme un duo avec Rinka Nanasaki, à qui elle transmet ses techniques.",
        "Rinka Nanasaki" => "Milieu de Hidaka, brillante en classe comme au sport, Nanasaki a un sens du football naturel. D'abord attirée par Eiji Tsukiori, elle se met sérieusement au jeu et devient un maillon clé de l'équipe, en duo avec Fuchise.",
        "Eiji Tsukiori" => "Attaquant et capitaine de Hidaka, Tsukiori ne pense qu'au football, au point d'ignorer les avances de ses admiratrices. Il développe sa frappe signature, la « Crescent Moon Shot ».",
        "Ayane Miyazaki" => "Milieu de Shimabara FC et cadette des jumelles Miyazaki, joyeuse et énergique. Avec sa sœur Kotone, elle met au point leur tir en duo, le « Twin Bound Shot ».",
        "Kotone Miyazaki" => "Milieu de Shimabara FC et aînée des jumelles Miyazaki, réservée et exigeante. Avec sa sœur Ayane, elle perfectionne leur tir en duo, le « Twin Bound Shot ».",
    ];

    public function up(): void
    {
        foreach (self::DESCRIPTIONS as $fullName => $description) {
            [$first, $last] = explode(' ', $fullName);
            DB::table('players')->where('origin', 'captain_tsubasa')->where('firstname', $first)->where('lastname', $last)
                ->update(['description' => $description, 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        // Retouche de texte volontaire : non réversible automatiquement.
    }
};
