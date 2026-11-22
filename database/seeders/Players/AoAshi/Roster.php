<?php

namespace Database\Seeders\Players\AoAshi;

/**
 * Données joueurs (et special moves) — groupe : Roster.
 * Extrait de PlayerSeeder pour alléger et organiser par source.
 */
class Roster
{
    /** @return array<int, array<int, mixed>> */
    public static function players(): array
    {
        return [
            //-------------------------------
            // Ao Ashi – Not contract
            //-------------------------------
            [
                'Ashito', 'Aoi', 13, 'Midfielder', 390,
                [
                    'speed' => 72, 'stamina' => 74, 'defense' => 28, 'attack' => 28,
                    'shot' => 22, 'pass' => 29, 'dribble' => 17,
                    'block' => 17, 'intercept' => 32, 'tackle' => 25,
                    'hand_save' => 10, 'punch_save' => 10,
                ],
                'Milieu d\'Esperion, d\'abord latéral gauche puis reconverti d\'attaquant par le coach Fukuda : il joue aussi bien en défense qu\'en attaque. Sa vision exceptionnelle du jeu et sa détermination sans faille en font un meneur polyvalent.',
                ['Defender', 'Forward']
            ],

            [
                'Eisaku', 'Ohtomo', 13, 'Midfielder', 355,
                [
                    'speed' => 60, 'stamina' => 70, 'defense' => 28, 'attack' => 24,
                    'shot' => 18, 'pass' => 29, 'dribble' => 23,
                    'block' => 20, 'intercept' => 23, 'tackle' => 22,
                    'hand_save' => 10, 'punch_save' => 10,
                ],
                'Milieu défensif de l\'équipe B d\'Esperion et premier ami d\'Ashito. Derrière une apparence de couard se cache un meneur composé, salué pour sa vision du jeu et sa capacité à faire le lien.'
            ],

            [
                'Soichiro', 'Tachibana', 13, 'Forward', 360,
                [
                    'speed' => 66, 'stamina' => 72, 'defense' => 16, 'attack' => 33,
                    'shot' => 27, 'pass' => 22, 'dribble' => 22,
                    'block' => 14, 'intercept' => 15, 'tackle' => 15,
                    'hand_save' => 10, 'punch_save' => 10,
                ],
                'Avant-centre de l\'équipe B d\'Esperion, ancien joueur de Tokyo Musashino. Ami d\'Ashito, il apporte de la présence dans la surface et un bon sens du placement.'
            ],

            [
                'Keiji', 'Togashi', 13, 'Defender', 340,
                [
                    'speed' => 57, 'stamina' => 66, 'defense' => 31, 'attack' => 23,
                    'shot' => 22, 'pass' => 22, 'dribble' => 15,
                    'block' => 25, 'intercept' => 21, 'tackle' => 27,
                    'hand_save' => 10, 'punch_save' => 10,
                ],
                'Défenseur central de l\'équipe B d\'Esperion et colocataire d\'Ashito. Physique et offensif, il combine impact dans les duels, bonnes bases techniques et frappe puissante pour soutenir les attaques.'
            ],

            [
                'Kanpei', 'Kuroda', 13, 'Midfielder', 345,
                [
                    'speed' => 62, 'stamina' => 68, 'defense' => 24, 'attack' => 25,
                    'shot' => 18, 'pass' => 28, 'dribble' => 25,
                    'block' => 18, 'intercept' => 24, 'tackle' => 20,
                    'hand_save' => 10, 'punch_save' => 10,
                ],
                'Milieu central formé à l\'académie Esperion. Technique et bien formé tactiquement, il apporte de la qualité dans la conservation du ballon et la construction du jeu.'
            ],

            [
                'Jun', 'Asari', 13, 'Defender', 330,
                [
                    'speed' => 71, 'stamina' => 66, 'defense' => 25, 'attack' => 24,
                    'shot' => 15, 'pass' => 25, 'dribble' => 23,
                    'block' => 23, 'intercept' => 21, 'tackle' => 23,
                    'hand_save' => 10, 'punch_save' => 10,
                ],
                'Latéral formé à l\'académie Esperion. Polyvalent et appliqué, il peut évoluer des deux côtés de la défense et participe activement aux phases offensives.'
            ],

            [
                'Yuma', 'Motoki', 13, 'Forward', 395,
                [
                    'speed' => 68, 'stamina' => 68, 'defense' => 17, 'attack' => 33,
                    'shot' => 28, 'pass' => 25, 'dribble' => 26,
                    'block' => 14, 'intercept' => 16, 'tackle' => 15,
                    'hand_save' => 10, 'punch_save' => 10,
                ],
                'Attaquant promu en équipe A d\'Esperion. Formé à l\'académie, il a su s\'imposer par sa technique et son efficacité offensive pour franchir le cap vers l\'équipe première.'
            ],

            [
                'Ryuichi', 'Takeshima', 13, 'Defender', 350,
                [
                    'speed' => 60, 'stamina' => 70, 'defense' => 32, 'attack' => 17,
                    'shot' => 13, 'pass' => 17, 'dribble' => 15,
                    'block' => 29, 'intercept' => 23, 'tackle' => 28,
                    'hand_save' => 10, 'punch_save' => 10,
                ],
                'Défenseur central formé à l\'académie Esperion. Solide dans les duels et bien organisé, il est l\'un des piliers de la défense de l\'équipe B.'
            ],

            //-------------------------------
            // Équipe A d'Esperion (postes de Kiriki, Takasugi et Yoshitsune à confirmer)
            //-------------------------------
            [
                'Nagisa', 'Akutsu', 15, 'Defender', 0,
                [
                    'speed' => 62, 'stamina' => 72, 'defense' => 36, 'attack' => 20,
                    'shot' => 15, 'pass' => 22, 'dribble' => 16,
                    'block' => 32, 'intercept' => 28, 'tackle' => 33,
                    'hand_save' => 10, 'punch_save' => 10,
                ],
                'Défenseur central de l\'équipe A d\'Esperion. Autoritaire et solide dans les duels, il organise la ligne arrière.'
            ],

            [
                'Haruhisa', 'Kuribayashi', 15, 'Midfielder', 0,
                [
                    'speed' => 66, 'stamina' => 72, 'defense' => 26, 'attack' => 30,
                    'shot' => 24, 'pass' => 36, 'dribble' => 30,
                    'block' => 17, 'intercept' => 26, 'tackle' => 22,
                    'hand_save' => 10, 'punch_save' => 10,
                ],
                'Meneur de jeu de l\'équipe A d\'Esperion. Technique et lucide, il dicte le tempo et distribue le jeu.'
            ],

            [
                'Yoichi', 'Kiriki', 15, 'Midfielder', 0,
                [
                    'speed' => 66, 'stamina' => 70, 'defense' => 27, 'attack' => 28,
                    'shot' => 22, 'pass' => 30, 'dribble' => 26,
                    'block' => 19, 'intercept' => 26, 'tackle' => 23,
                    'hand_save' => 10, 'punch_save' => 10,
                ],
                'Milieu de l\'équipe A d\'Esperion, au profil complet entre récupération et construction.'
            ],

            [
                'Eita', 'Takasugi', 15, 'Defender', 0,
                [
                    'speed' => 62, 'stamina' => 68, 'defense' => 33, 'attack' => 20,
                    'shot' => 14, 'pass' => 21, 'dribble' => 16,
                    'block' => 28, 'intercept' => 26, 'tackle' => 30,
                    'hand_save' => 10, 'punch_save' => 10,
                ],
                'Défenseur de l\'équipe A d\'Esperion, rigoureux dans le marquage et les tacles.'
            ],

            [
                'Kenta', 'Yoshitsune', 15, 'Goalkeeper', 0,
                [
                    'speed' => 50, 'stamina' => 62, 'defense' => 14, 'attack' => 8,
                    'shot' => 8, 'pass' => 20, 'dribble' => 8,
                    'block' => 10, 'intercept' => 12, 'tackle' => 8,
                    'hand_save' => 33, 'punch_save' => 30,
                ],
                'Gardien de l\'équipe A d\'Esperion, fiable sur sa ligne et dans les sorties aériennes.'
            ],

        ];
    }

    /** @return array<string, array<int, array<string, mixed>>> */
    public static function specialMoves(): array
    {
        return [
            // AO ASHI
            // =======================

            'ashito-aoi' => [[
                'key'         => 'aoi_bird_eye_view',
                'mode'        => 'attack',
                'label'       => 'Vision panoramique',
                'short_label' => 'Vision',
                'cooldown'    => 2,
                'base_action' => 'pass',
                'description' => 'Sa vue d’ensemble du terrain, « l’œil d’oiseau », lui permet de lancer une passe décisive depuis l’arrière.',
            ]],

            'eisaku-ohtomo' => [[
                'key'         => 'ohtomo_link_play',
                'mode'        => 'attack',
                'label'       => 'Jeu de liaison',
                'short_label' => 'Liaison',
                'cooldown'    => 2,
                'base_action' => 'pass',
                'description' => 'Une relance calme et bien orientée qui fait le lien entre la défense et l’attaque.',
            ]],

            'soichiro-tachibana' => [[
                'key'         => 'soichiro_box_finish',
                'mode'        => 'attack',
                'label'       => 'Renard des surfaces',
                'short_label' => 'Renard',
                'cooldown'    => 2,
                'base_action' => 'shot',
                'description' => 'Un excellent sens du placement dans la surface pour conclure au bon moment.',
            ]],

            'keiji-togashi' => [[
                'key'         => 'togashi_iron_marking',
                'mode'        => 'defense',
                'label'       => 'Marquage de fer',
                'short_label' => 'Marquage',
                'cooldown'    => 2,
                'base_action' => 'tackle',
                'description' => 'Défenseur central discipliné, solide dans le timing du tacle et le sens du placement.',
            ]],

            'kanpei-kuroda' => [[
                'key'         => 'kuroda_ball_control',
                'mode'        => 'attack',
                'label'       => 'Conservation technique',
                'short_label' => 'Conservation',
                'cooldown'    => 2,
                'base_action' => 'pass',
                'description' => 'Technique et bien formé tactiquement, il garde le ballon sous pression et construit le jeu.',
            ]],

            'jun-asari' => [[
                'key'         => 'asari_overlap_run',
                'mode'        => 'attack',
                'label'       => 'Montée du latéral',
                'short_label' => 'Montée',
                'cooldown'    => 2,
                'base_action' => 'dribble',
                'description' => 'Latéral polyvalent qui se projette des deux côtés pour soutenir l’attaque.',
            ]],

            'yuma-motoki' => [[
                'key'         => 'motoki_clinical_finish',
                'mode'        => 'attack',
                'label'       => 'Finition d’académie',
                'short_label' => 'Finition',
                'cooldown'    => 2,
                'base_action' => 'shot',
                'description' => 'Attaquant techniquement formé à l’académie, efficace et précis devant le but.',
            ]],

            'ryuichi-takeshima' => [[
                'key'         => 'takeshima_wall_block',
                'mode'        => 'defense',
                'label'       => 'Mur central',
                'short_label' => 'Mur',
                'cooldown'    => 2,
                'base_action' => 'block',
                'description' => 'Solide dans les duels et bien organisé, il ferme l’axe et bloque les frappes.',
            ]],

            // =======================
        ];
    }
}
