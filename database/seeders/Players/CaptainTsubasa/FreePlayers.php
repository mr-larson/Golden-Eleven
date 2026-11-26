<?php

namespace Database\Seeders\Players\CaptainTsubasa;

/**
 * Données joueurs (et special moves) — groupe : FreePlayers.
 * Extrait de PlayerSeeder pour alléger et organiser par source.
 */
class FreePlayers
{
    /** @return array<int, array<int, mixed>> */
    public static function players(): array
    {
        return [
            // Real Seven – Not contract
            [
                'Michel', 'Yamada', 13, 'Goalkeeper', 300,
                [
                    'speed' => 60, 'stamina' => 70, 'defense' => 32, 'attack' => 18,
                    'shot' => 16, 'pass' => 19, 'dribble' => 17,
                    'block' => 22, 'intercept' => 23, 'tackle' => 18,
                    'hand_save' => 26, 'punch_save' => 24
                ],
                'Gardien de Real Seven, Yamada est un portier athlétique et fiable. Plus mobile que la moyenne, il combine réflexes et lecture du jeu.'
            ],

            [
                'Yuji', 'Sakaki', 13, 'Defender', 285,
                [
                    'speed' => 58, 'stamina' => 67, 'defense' => 30, 'attack' => 22,
                    'shot' => 18, 'pass' => 20, 'dribble' => 18,
                    'block' => 24, 'intercept' => 23, 'tackle' => 25,
                    'hand_save' => 15, 'punch_save' => 15
                ],
                'Défenseur solide, Sakaki se distingue par son sens du placement et sa régularité dans les duels.'
            ],

            [
                'Takashi', 'Sugimoto', 13, 'Midfielder', 305,
                [
                    'speed' => 65, 'stamina' => 70, 'defense' => 30, 'attack' => 27,
                    'shot' => 25, 'pass' => 26, 'dribble' => 25,
                    'block' => 22, 'intercept' => 23, 'tackle' => 22,
                    'hand_save' => 15, 'punch_save' => 15
                ],
                'Milieu complet, Sugimoto est un relais essentiel entre la défense et l’attaque de Real Seven.'
            ],

            [
                'Nobuyuki', 'Yumikura', 13, 'Midfielder', 310,
                [
                    'speed' => 66, 'stamina' => 71, 'defense' => 29, 'attack' => 28,
                    'shot' => 25, 'pass' => 27, 'dribble' => 26,
                    'block' => 22, 'intercept' => 23, 'tackle' => 22,
                    'hand_save' => 15, 'punch_save' => 15
                ],
                'Milieu leader, Yumikura impose le tempo et apporte une grande constance dans le jeu.'
            ],

            [
                'Toshiya', 'Okano', 13, 'Midfielder', 300,
                [
                    'speed' => 64, 'stamina' => 69, 'defense' => 29, 'attack' => 27,
                    'shot' => 25, 'pass' => 26, 'dribble' => 25,
                    'block' => 22, 'intercept' => 23, 'tackle' => 22,
                    'hand_save' => 15, 'punch_save' => 15
                ],
                'Milieu offensif régulier, Okano soutient les attaques et se projette intelligemment.'
            ],

            [
                'Ryoma', 'Hino', 12, 'Forward', 445,
                [
                    'speed' => 70, 'stamina' => 80, 'defense' => 20, 'attack' => 38,
                    'shot' => 30, 'pass' => 20, 'dribble' => 27,
                    'block' => 18, 'intercept' => 18, 'tackle' => 18,
                    'hand_save' => 15, 'punch_save' => 15
                ],
                'Star offensive de Real Seven, Hino est un buteur puissant et agressif. Son tir dévastateur et son mental font de lui une menace constante.'
            ],


            // Nouveaux joueurs libres (sans club)
            ['Mei', 'Nagai', 13, 'Goalkeeper', 220, ['speed' => 54, 'stamina' => 60, 'defense' => 27, 'attack' => 15, 'shot' => 15, 'pass' => 17, 'dribble' => 15, 'block' => 22, 'intercept' => 22, 'tackle' => 18, 'hand_save' => 25, 'punch_save' => 23], "Gardienne et capitaine du Nikko SC, Nagai pousse son équipe à s'entraîner dès l'aube. Ces séances, avec Maria Moreno, lui ont fait acquérir son arrêt « Mist Catch ». Elle poursuit ensuite au collège de Nikko."],
            ['Maria', 'Moreno', 13, 'Forward', 220, ['speed' => 60, 'stamina' => 55, 'defense' => 18, 'attack' => 26, 'shot' => 24, 'pass' => 19, 'dribble' => 23, 'block' => 16, 'intercept' => 16, 'tackle' => 16, 'hand_save' => 15, 'punch_save' => 15], "Attaquante argentine arrivée à Nikko comme élève d'échange, Moreno marque déjà des hat-tricks mais exige un meilleur niveau collectif. Séduite par l'entraînement matinal de l'équipe, elle rejoint le Nikko SC et ouvre le score dès le premier match du tournoi."],
            ['Aya', 'Fuchise', 13, 'Defender', 220, ['speed' => 55, 'stamina' => 62, 'defense' => 27, 'attack' => 19, 'shot' => 16, 'pass' => 18, 'dribble' => 17, 'block' => 25, 'intercept' => 24, 'tackle' => 26, 'hand_save' => 15, 'punch_save' => 15], "Défenseuse de Hidaka, douce et passionnée, Fuchise veut faire aimer le football à tous. Elle forme un duo avec Rinka Nanasaki, à qui elle transmet ses techniques."],
            ['Rinka', 'Nanasaki', 13, 'Midfielder', 220, ['speed' => 58, 'stamina' => 60, 'defense' => 22, 'attack' => 24, 'shot' => 20, 'pass' => 26, 'dribble' => 23, 'block' => 19, 'intercept' => 22, 'tackle' => 21, 'hand_save' => 15, 'punch_save' => 15], "Milieu de Hidaka, brillante en classe comme au sport, Nanasaki a un sens du football naturel. D'abord attirée par Eiji Tsukiori, elle se met sérieusement au jeu et devient un maillon clé de l'équipe, en duo avec Fuchise."],
            ['Eiji', 'Tsukiori', 13, 'Forward', 220, ['speed' => 62, 'stamina' => 60, 'defense' => 19, 'attack' => 29, 'shot' => 28, 'pass' => 22, 'dribble' => 25, 'block' => 17, 'intercept' => 17, 'tackle' => 17, 'hand_save' => 15, 'punch_save' => 15], "Attaquant et capitaine de Hidaka, Tsukiori ne pense qu'au football, au point d'ignorer les avances de ses admiratrices. Il développe sa frappe signature, la « Crescent Moon Shot »."],
            ['Ayane', 'Miyazaki', 13, 'Midfielder', 220, ['speed' => 57, 'stamina' => 58, 'defense' => 22, 'attack' => 23, 'shot' => 20, 'pass' => 24, 'dribble' => 22, 'block' => 19, 'intercept' => 21, 'tackle' => 20, 'hand_save' => 15, 'punch_save' => 15], "Milieu de Shimabara FC et cadette des jumelles Miyazaki, joyeuse et énergique. Avec sa sœur Kotone, elle met au point leur tir en duo, le « Twin Bound Shot »."],
            ['Kotone', 'Miyazaki', 13, 'Midfielder', 220, ['speed' => 57, 'stamina' => 58, 'defense' => 22, 'attack' => 23, 'shot' => 20, 'pass' => 24, 'dribble' => 22, 'block' => 19, 'intercept' => 21, 'tackle' => 20, 'hand_save' => 15, 'punch_save' => 15], "Milieu de Shimabara FC et aînée des jumelles Miyazaki, réservée et exigeante. Avec sa sœur Ayane, elle perfectionne leur tir en duo, le « Twin Bound Shot »."],
        ];
    }

    /** @return array<string, array<int, array<string, mixed>>> */
    public static function specialMoves(): array
    {
        return [
            // REAL SEVEN
            // =======================

            'ryoma-hino' => [[
                'key'         => 'hino_fire_shot',
                'mode'        => 'attack',
                'label'       => 'Tir de Feu',
                'short_label' => 'Feu',
                'cooldown'    => 3,
                'base_action' => 'shot',
                'description' => 'Un tir surpuissant et brûlant qui semble enflammer le ballon.',
            ]],

            // =======================
            // INTERNATIONAUX — REAL SEVEN
            // =======================

            'michel-yamada' => [[
                'key'         => 'yamada_reflex_save',
                'mode'        => 'defense',
                'label'       => 'Arrêt réflexe',
                'short_label' => 'Réflexe',
                'cooldown'    => 2,
                'base_action' => 'hand_save',
                'description' => 'Gardien athlétique et mobile, il combine réflexes explosifs et bonne lecture du jeu.',
            ]],

            'shinnosuke-kazami' => [[
                'key'         => 'kazami_quick_strike',
                'mode'        => 'attack',
                'label'       => 'Frappe éclair',
                'short_label' => 'Éclair',
                'cooldown'    => 2,
                'base_action' => 'shot',
                'description' => 'Attaquant rapide et opportuniste, il multiplie les appels et conclut vite les actions.',
            ]],

            // =======================
        ];
    }
}
