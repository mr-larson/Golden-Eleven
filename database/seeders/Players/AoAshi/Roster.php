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
                'Latéral gauche d\'Esperion, d\'abord attaquant reconverti par le coach Fukuda. Cœur tactique de l\'équipe, il orchestre le jeu depuis la défense grâce à sa vision panoramique et à sa détermination sans faille.',
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
                'Sentinelle d\'Esperion et premier ami d\'Ashito. Toujours calme sous pression, il fluidifie les transitions et compense les montées d\'Ashito : un régulateur précieux.'
            ],

            [
                'Soichiro', 'Tachibana', 13, 'Forward', 360,
                [
                    'speed' => 66, 'stamina' => 72, 'defense' => 16, 'attack' => 33,
                    'shot' => 27, 'pass' => 22, 'dribble' => 22,
                    'block' => 14, 'intercept' => 15, 'tackle' => 15,
                    'hand_save' => 10, 'punch_save' => 10,
                ],
                'Attaquant d\'Esperion, ancien joueur de Tokyo Musashino et ami d\'Ashito. Super-remplaçant par excellence : une énergie débordante et un don pour marquer des buts improbables en fin de match.'
            ],

            [
                'Keiji', 'Togashi', 13, 'Defender', 340,
                [
                    'speed' => 57, 'stamina' => 66, 'defense' => 31, 'attack' => 23,
                    'shot' => 22, 'pass' => 22, 'dribble' => 15,
                    'block' => 25, 'intercept' => 21, 'tackle' => 27,
                    'hand_save' => 10, 'punch_save' => 10,
                ],
                'Défenseur central d\'Esperion et colocataire d\'Ashito. Solide dans les duels et bon relanceur, il forme avec Akutsu un duo complémentaire (et explosif).'
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
                'Latéral et milieu formé à l\'académie Esperion. La polyvalence technique incarnée : il verrouille un couloir ou renforce le milieu et participe aux phases offensives.',
                ['Midfielder']
            ],

            [
                'Yuma', 'Motoki', 13, 'Forward', 395,
                [
                    'speed' => 68, 'stamina' => 68, 'defense' => 17, 'attack' => 33,
                    'shot' => 28, 'pass' => 25, 'dribble' => 26,
                    'block' => 14, 'intercept' => 16, 'tackle' => 15,
                    'hand_save' => 10, 'punch_save' => 10,
                ],
                'Ailier droit d\'Esperion, électron libre au flair instinctif. Promu en équipe A, il combine à la perfection avec Kuribayashi et Ashito.'
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
            // Équipe A d'Esperion
            //-------------------------------
            [
                'Nagisa', 'Akutsu', 15, 'Defender', 0,
                [
                    'speed' => 62, 'stamina' => 72, 'defense' => 36, 'attack' => 20,
                    'shot' => 15, 'pass' => 22, 'dribble' => 16,
                    'block' => 32, 'intercept' => 28, 'tackle' => 33,
                    'hand_save' => 10, 'punch_save' => 10,
                ],
                'Défenseur central d\'Esperion, patron de la défense. Monstrueux physiquement, doté d\'une lecture défensive hors norme et d\'un mental d\'acier.'
            ],

            [
                'Haruhisa', 'Kuribayashi', 15, 'Midfielder', 0,
                [
                    'speed' => 66, 'stamina' => 72, 'defense' => 26, 'attack' => 32,
                    'shot' => 26, 'pass' => 37, 'dribble' => 31,
                    'block' => 17, 'intercept' => 26, 'tackle' => 22,
                    'hand_save' => 10, 'punch_save' => 10,
                ],
                'Meneur offensif d\'Esperion, génie absolu déjà au niveau professionnel. Capable de débloquer n\'importe quelle défense.',
                ['Forward']
            ],

            [
                'Yoichi', 'Kiriki', 15, 'Midfielder', 0,
                [
                    'speed' => 66, 'stamina' => 70, 'defense' => 27, 'attack' => 28,
                    'shot' => 22, 'pass' => 32, 'dribble' => 27,
                    'block' => 19, 'intercept' => 26, 'tackle' => 23,
                    'hand_save' => 10, 'punch_save' => 10,
                ],
                'Milieu relayeur gauche d\'Esperion, maestro technique capable de dicter le tempo et de trouver des angles de passe impossibles.'
            ],

            [
                'Eita', 'Takasugi', 15, 'Forward', 0,
                [
                    'speed' => 72, 'stamina' => 70, 'defense' => 17, 'attack' => 31,
                    'shot' => 27, 'pass' => 24, 'dribble' => 29,
                    'block' => 13, 'intercept' => 16, 'tackle' => 14,
                    'hand_save' => 10, 'punch_save' => 10,
                ],
                'Ailier gauche d\'Esperion et international U18. Redoutable dans les déplacements sans ballon et dans la finition.',
                ['Midfielder']
            ],

            [
                'Kenta', 'Yoshitsune', 15, 'Forward', 0,
                [
                    'speed' => 66, 'stamina' => 72, 'defense' => 18, 'attack' => 33,
                    'shot' => 32, 'pass' => 28, 'dribble' => 28,
                    'block' => 14, 'intercept' => 18, 'tackle' => 16,
                    'hand_save' => 10, 'punch_save' => 10,
                ],
                'Avant-centre et capitaine d\'Esperion. Leader naturel doté d\'un QI footballistique immense et d\'une finition chirurgicale.'
            ],

            [
                'Enshin', 'Akiyama', 15, 'Goalkeeper', 0,
                [
                    'speed' => 50, 'stamina' => 62, 'defense' => 14, 'attack' => 8,
                    'shot' => 8, 'pass' => 22, 'dribble' => 8,
                    'block' => 10, 'intercept' => 12, 'tackle' => 8,
                    'hand_save' => 34, 'punch_save' => 31,
                ],
                'Gardien titulaire d\'Esperion. Mur décisif et relanceur précieux lors des grands rendez-vous.'
            ],

            [
                'Yutaka', 'Yamada', 15, 'Defender', 0,
                [
                    'speed' => 68, 'stamina' => 72, 'defense' => 30, 'attack' => 26,
                    'shot' => 17, 'pass' => 27, 'dribble' => 24,
                    'block' => 22, 'intercept' => 25, 'tackle' => 27,
                    'hand_save' => 10, 'punch_save' => 10,
                ],
                'Latéral droit d\'Esperion, l\'un des meilleurs latéraux du pays chez les jeunes. Ultra-complet, offensivement comme défensivement.',
                ['Midfielder']
            ],

            [
                'Kanji', 'Nikaido', 15, 'Goalkeeper', 0,
                [
                    'speed' => 48, 'stamina' => 60, 'defense' => 13, 'attack' => 8,
                    'shot' => 8, 'pass' => 19, 'dribble' => 8,
                    'block' => 10, 'intercept' => 11, 'tackle' => 8,
                    'hand_save' => 31, 'punch_save' => 29,
                ],
                'Gardien d\'Esperion, numéro 2 du poste. Son expérience et son leadership vocal sécurisent la défense.'
            ],

            [
                'Kyo', 'Shimura', 15, 'Defender', 0,
                [
                    'speed' => 66, 'stamina' => 68, 'defense' => 33, 'attack' => 18,
                    'shot' => 13, 'pass' => 21, 'dribble' => 15,
                    'block' => 29, 'intercept' => 27, 'tackle' => 30,
                    'hand_save' => 10, 'punch_save' => 10,
                ],
                'Défenseur central d\'Esperion, titulaire indiscutable en équipe A. Rigueur tactique et vitesse précieuses en défense.'
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

            'nagisa-akutsu' => [[
                'key'         => 'akutsu_back_line_boss',
                'mode'        => 'defense',
                'label'       => 'Autorité d’Akutsu',
                'short_label' => 'Autorité',
                'cooldown'    => 2,
                'base_action' => 'block',
                'description' => 'Défenseur central autoritaire de l’équipe A d’Esperion, il ferme l’axe et bloque les frappes.',
            ]],

            'haruhisa-kuribayashi' => [[
                'key'         => 'kuribayashi_tempo',
                'mode'        => 'attack',
                'label'       => 'Tempo de meneur',
                'short_label' => 'Tempo',
                'cooldown'    => 2,
                'base_action' => 'pass',
                'description' => 'Meneur lucide de l’équipe A d’Esperion : il dicte le rythme et délivre la passe qui lance l’attaque.',
            ]],

            'yoichi-kiriki' => [[
                'key'         => 'kiriki_all_round_recovery',
                'mode'        => 'defense',
                'label'       => 'Récupération complète',
                'short_label' => 'Récupération',
                'cooldown'    => 2,
                'base_action' => 'intercept',
                'description' => 'Milieu complet entre récupération et construction, il coupe la passe puis relance.',
            ]],

            'eita-takasugi' => [[
                'key'         => 'takasugi_off_ball_run',
                'mode'        => 'attack',
                'label'       => 'Appel dans le dos',
                'short_label' => 'Appel',
                'cooldown'    => 2,
                'base_action' => 'shot',
                'description' => 'International U18 redoutable sans ballon : il se démarque dans le dos de la défense et conclut.',
            ]],

            'kenta-yoshitsune' => [[
                'key'         => 'yoshitsune_captain_finish',
                'mode'        => 'attack',
                'label'       => 'Finition du capitaine',
                'short_label' => 'Capitaine',
                'cooldown'    => 3,
                'base_action' => 'shot',
                'description' => 'Un QI football immense et une finition chirurgicale : le capitaine d’Esperion ne laisse rien au hasard devant le but.',
            ]],

            'enshin-akiyama' => [[
                'key'         => 'akiyama_decisive_wall',
                'mode'        => 'defense',
                'label'       => 'Mur décisif',
                'short_label' => 'Mur',
                'cooldown'    => 3,
                'base_action' => 'hand_save',
                'description' => 'Gardien d’Esperion, il devient un mur sur les grands rendez-vous et relance aussitôt.',
            ]],

            'yutaka-yamada' => [[
                'key'         => 'yamada_complete_fullback',
                'mode'        => 'attack',
                'label'       => 'Latéral complet',
                'short_label' => 'Complet',
                'cooldown'    => 2,
                'base_action' => 'cross',
                'description' => 'Ultra-complet sur son couloir droit, il déborde puis centre avec précision.',
            ]],

            'kanji-nikaido' => [[
                'key'         => 'nikaido_vocal_leader',
                'mode'        => 'defense',
                'label'       => 'Leader vocal',
                'short_label' => 'Leader',
                'cooldown'    => 3,
                'base_action' => 'hand_save',
                'description' => 'Son expérience et sa voix organisent la défense et sécurisent le but.',
            ]],

            'kyo-shimura' => [[
                'key'         => 'shimura_tactical_rigor',
                'mode'        => 'defense',
                'label'       => 'Rigueur tactique',
                'short_label' => 'Rigueur',
                'cooldown'    => 2,
                'base_action' => 'intercept',
                'description' => 'Titulaire en équipe A, il anticipe et coupe les trajectoires grâce à son placement et sa vitesse.',
            ]],

            // =======================
        ];
    }
}
