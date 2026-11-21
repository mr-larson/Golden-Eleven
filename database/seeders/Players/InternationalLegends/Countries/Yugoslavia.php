<?php

namespace Database\Seeders\Players\InternationalLegends\Countries;

/**
 * Onze historique de Yougoslavie (toutes époques : RFS Yougoslavie, RF Yougoslavie, Serbie-et-Monténégro) :
 * sélection volontairement modeste, avec quelques stars (Stojković, Džajić, Savićević, Mijatović) dans un onze moyen.
 */
final class Yugoslavia
{
    public const NATIONALITY = 'Yougoslavie';
    public const ROSTER = [];
    public const ADDITIONS = [];

    public const HISTORICAL_ADDITIONS = [
        ['firstname' => 'Dejan', 'lastname' => 'Savićević', 'profile' => 'playmaker', 'stats' => [], 'note' => 'Référence de la période des années 1990.'],
        ['firstname' => 'Predrag', 'lastname' => 'Mijatović', 'profile' => 'striker', 'stats' => [], 'note' => 'Référence de la période des années 1990.'],
    ];

    public const EXTRA_ADDITIONS = [
        ['firstname' => 'Tomislav', 'lastname' => 'Ivković', 'profile' => 'keeper', 'position' => 'GB', 'secondary' => null, 'stats' => ['speed' => 56, 'stamina' => 68, 'attack' => 12, 'defense' => 25, 'shot' => 9, 'pass' => 16, 'dribble' => 11, 'block' => 18, 'intercept' => 18, 'tackle' => 16, 'heading' => 14, 'hand_save' => 26, 'punch_save' => 23], 'note' => 'Gardien de la Yougoslavie à Italia 90, quart de finaliste : il arrête le tir au but de Maradona lors de la séance contre l\'Argentine ; gardien sûr sur sa ligne.'],
        ['firstname' => 'Nemanja', 'lastname' => 'Vidić', 'profile' => 'stopper', 'position' => 'DC', 'secondary' => null, 'stats' => ['speed' => 58, 'stamina' => 70, 'attack' => 12, 'defense' => 31, 'shot' => 9, 'pass' => 15, 'dribble' => 11, 'block' => 27, 'intercept' => 27, 'tackle' => 29, 'heading' => 29, 'hand_save' => 10, 'punch_save' => 10], 'note' => 'Manchester United (quatre Premier League, Ligue des champions 2008) et Inter, défenseur central de Serbie-et-Monténégro au Mondial 2006 ; roc des duels, dominant de la tête.'],
        ['firstname' => 'Siniša', 'lastname' => 'Mihajlović', 'profile' => 'fullback', 'position' => 'DG', 'secondary' => 'DC', 'stats' => ['speed' => 55, 'stamina' => 67, 'attack' => 17, 'defense' => 25, 'shot' => 27, 'pass' => 23, 'dribble' => 15, 'block' => 19, 'intercept' => 21, 'tackle' => 20, 'heading' => 16, 'hand_save' => 10, 'punch_save' => 10], 'note' => 'Étoile Rouge, Sampdoria, Lazio (champion d\'Italie 2000) et Inter ; défenseur gaucher à la frappe de coup franc légendaire.'],
        ['firstname' => 'Faruk', 'lastname' => 'Hadžibegić', 'profile' => 'libero', 'position' => 'DC', 'secondary' => null, 'stats' => ['speed' => 54, 'stamina' => 64, 'attack' => 13, 'defense' => 27, 'shot' => 10, 'pass' => 22, 'dribble' => 14, 'block' => 21, 'intercept' => 24, 'tackle' => 22, 'heading' => 21, 'hand_save' => 10, 'punch_save' => 10], 'note' => 'Capitaine de la Yougoslavie à Italia 90, passé par Sochaux ; libéro élégant et fiable.'],
        ['firstname' => 'Velibor', 'lastname' => 'Vasović', 'profile' => 'libero', 'position' => 'DC', 'secondary' => null, 'stats' => ['speed' => 56, 'stamina' => 66, 'attack' => 14, 'defense' => 26, 'shot' => 11, 'pass' => 19, 'dribble' => 12, 'block' => 21, 'intercept' => 23, 'tackle' => 23, 'heading' => 21, 'hand_save' => 10, 'punch_save' => 10], 'note' => 'Capitaine de l\'Ajax vainqueur de la Coupe des clubs champions 1971, finaliste de l\'Euro 1968 avec la Yougoslavie ; libéro et meneur de défense.'],
        ['firstname' => 'Dejan', 'lastname' => 'Stanković', 'profile' => 'box_to_box', 'position' => 'MC', 'secondary' => null, 'stats' => ['speed' => 63, 'stamina' => 71, 'attack' => 21, 'defense' => 21, 'shot' => 21, 'pass' => 25, 'dribble' => 21, 'block' => 16, 'intercept' => 21, 'tackle' => 21, 'heading' => 17, 'hand_save' => 10, 'punch_save' => 10], 'note' => 'Étoile Rouge, Lazio, puis Inter (triplé 2010), Mondiaux 1998 et 2006 ; milieu complet, puissant et infatigable.'],
        ['firstname' => 'Safet', 'lastname' => 'Sušić', 'profile' => 'playmaker', 'position' => 'MOC', 'secondary' => null, 'stats' => ['speed' => 52, 'stamina' => 62, 'attack' => 30, 'defense' => 18, 'shot' => 26, 'pass' => 34, 'dribble' => 32, 'block' => 12, 'intercept' => 20, 'tackle' => 17, 'heading' => 15, 'hand_save' => 10, 'punch_save' => 10], 'note' => 'Idole du Paris Saint-Germain, Mondial 1982 avec la Yougoslavie ; meneur de jeu élégant, passe d\'orfèvre mais peu de vitesse.'],
        ['firstname' => 'Dragan', 'lastname' => 'Stojković', 'profile' => 'playmaker', 'position' => 'MOC', 'secondary' => 'AG', 'stats' => ['speed' => 64, 'stamina' => 68, 'attack' => 33, 'defense' => 17, 'shot' => 27, 'pass' => 31, 'dribble' => 34, 'block' => 11, 'intercept' => 18, 'tackle' => 14, 'heading' => 14, 'hand_save' => 10, 'punch_save' => 10], 'note' => '« Piksi », Marseille (finaliste de la Coupe d\'Europe 1991) puis Nagoya Grampus au Japon ; meneur-dribbleur de la Yougoslavie d\'Italia 90.'],
        ['firstname' => 'Dragan', 'lastname' => 'Džajić', 'profile' => 'winger_forward', 'position' => 'AG', 'secondary' => null, 'stats' => ['speed' => 74, 'stamina' => 70, 'attack' => 33, 'defense' => 15, 'shot' => 25, 'pass' => 29, 'dribble' => 34, 'block' => 10, 'intercept' => 15, 'tackle' => 13, 'heading' => 13, 'hand_save' => 10, 'punch_save' => 10], 'note' => 'Légende de l\'Étoile Rouge et finaliste de l\'Euro 1968 ; ailier gauche rapide et dribbleur, considéré comme l\'un des plus grands joueurs yougoslaves.'],
    ];
}
