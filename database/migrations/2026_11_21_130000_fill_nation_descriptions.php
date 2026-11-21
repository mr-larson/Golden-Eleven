<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Renseigne la description des nations : un court historique du profil footballistique du pays.
 * Ne touche pas aux nations qui ont déjà une description (retouche faite à la main dans l'éditeur).
 */
return new class extends Migration
{
    /** Nom de la nation => description. */
    private const DESCRIPTIONS = [
        'Japon' => 'Longtemps en retrait, le Japon a bâti son football sur la technique, la vitesse et le collectif, de l\'essor de la J.League à des Mondiaux réguliers. Il forme des milieux créatifs et des ailiers rapides, avec une rigueur tactique reconnue.',
        'Allemagne' => 'Quadruple championne du monde (1954, 1974, 1990, 2014), l\'Allemagne est une machine à gagner bâtie sur la discipline, la puissance et un mental de tournoi. Elle enchaîne les générations d\'attaquants et de milieux de classe mondiale.',
        'France' => 'Championne du monde en 1998 et 2018, la France s\'appuie sur un vivier immense, athlétique et technique, formé à Clairefontaine. Elle brille en contre-attaque, portée par des attaquants explosifs et un milieu très complet.',
        'Brésil' => 'Cinq fois championne du monde, le Brésil est le royaume du football-spectacle : dribbles, une-deux et improvisation. Il produit des numéros 10 et des attaquants de génie, et reste moins réputé pour sa défense.',
        'Argentine' => 'Championne du monde en 1978, 1986 et 2022, l\'Argentine joue avec la garra : combativité, caractère et dribble de rue. Elle vit autour de meneurs de jeu de légende, de Maradona à Messi.',
        'Italie' => 'Quatre fois championne du monde, l\'Italie a fait de la défense une école : gardiens et défenseurs d\'élite, discipline tactique, efficacité. Elle gagne en gardant les matchs serrés et en frappant au bon moment.',
        'Pays-Bas' => 'Inventeurs du football total avec Cruyff dans les années 70, les Pays-Bas ont perdu trois finales de Coupe du monde sans jamais l\'emporter. Possession, circulation et ailiers percutants, avec une formation de joueurs très techniques.',
        'Uruguay' => 'Championne du monde en 1930 et 1950 et multiple lauréate de la Copa América, l\'Uruguay est une petite nation à la grande garra. Défenseurs durs, attaquants prolifiques, jeu direct et caractère de fer.',
        'Angleterre' => 'Championne du monde en 1966, l\'Angleterre a inventé le football et joue un jeu direct, physique et rapide, nourri par la Premier League. Elle compte de grands attaquants et ailiers, mais peine à décrocher un titre.',
        'Suède' => 'Finaliste de la Coupe du monde 1958 et troisième en 1950 et 1994, la Suède joue un football rigoureux et solide, avec des attaquants costauds. Larsson puis Ibrahimović ont porté ses dernières belles générations.',
        'Espagne' => 'Championne du monde en 2010 et d\'Europe en 2008, 2012 et 2024, l\'Espagne a imposé le tiki-taka : possession, passes courtes et pressing haut. Ses milieux techniques dictent le rythme du jeu.',
        'Mexique' => 'Habitué des phases finales mais souvent bloqué en huitièmes, le Mexique joue un football technique, vif et ancré dans la possession. Il forme des milieux et des ailiers habiles et domine la zone CONCACAF.',
        'États-Unis' => 'En progression constante depuis 1994, les États-Unis misent sur l\'athlétisme, l\'énergie et la polyvalence. Leur football moderne, formé en université et en MLS, produit des joueurs durs à l\'effort et très collectifs.',
        'Arabie saoudite' => 'Remarquée dès 1994 avec un huitième de finale, l\'Arabie saoudite joue un football rapide et technique, porté par des ailiers vifs. Elle a battu l\'Argentine au Mondial 2022, un exploit resté célèbre.',
        'Corée du Sud' => 'Demi-finaliste du Mondial 2002, la Corée du Sud est connue pour son énergie inépuisable, sa discipline et son pressing constant. Elle mise sur la vitesse et la contre-attaque, avec des ailiers en vue en Europe.',
        'Chine' => 'Géant démographique encore en construction, la Chine s\'est qualifiée une fois pour un Mondial (2002) et a beaucoup investi dans son championnat. Elle joue un football prudent et discipliné, sans grande vedette.',
        'Ouzbékistan' => 'Référence de l\'Asie centrale, l\'Ouzbékistan joue un football discipliné, structuré et défensif. Il compte sur la rigueur collective et une défense dense plus que sur une star.',
        'Nigéria' => 'Champion olympique en 1996 et plusieurs fois vainqueur de la CAN, le Nigéria dispose d\'un physique et d\'une explosivité rares. Les Super Eagles produisent des attaquants puissants et des ailiers vifs, dans un jeu audacieux.',
        'Australie' => 'Habituée des Mondiaux depuis 2006, l\'Australie joue un football physique et direct, entre défense solide et jeu long. Ses Socceroos misent sur la combativité et le collectif plutôt que sur la star.',
        'Ghana' => 'Quart de finaliste en 2010, à un penalty de la demi-finale, le Ghana joue un football athlétique et offensif. Les Black Stars comptent sur la vitesse, la puissance et des attaquants vifs.',
        'Trinité-et-Tobago' => 'Qualifiée pour son seul Mondial en 2006, Trinité-et-Tobago joue un football rapide en contre-attaque. Dwight Yorke en a été la figure, avec des ailiers vifs et une défense compacte.',
        'Thaïlande' => 'Référence du football d\'Asie du Sud-Est et multiple lauréate de la Coupe d\'ASEAN, la Thaïlande joue un football technique et rapide. Elle s\'appuie sur des milieux malins et une défense compacte.',
        'Portugal' => 'Championne d\'Europe en 2016, le Portugal est une école d\'ailiers et de techniciens, d\'Eusébio à Cristiano Ronaldo. Possession et dribbles, avec un jeu plus brillant sur les côtés qu\'en pointe.',
        'Croatie' => 'Finaliste en 2018 et troisième en 2022, la Croatie s\'appuie sur un milieu de classe mondiale autour de Modrić. Équipe technique, rusée et dure à battre malgré un petit vivier.',
        'Belgique' => 'Troisième du Mondial 2018 grâce à une génération dorée (De Bruyne, Hazard, Lukaku), la Belgique est offensive et spectaculaire. Une attaque de premier rang et des milieux créatifs, mais une défense moins constante.',
        'Danemark' => 'Champion d\'Europe en 1992 par surprise, le Danemark joue un football collectif, solide et organisé. Il forme des milieux et des défenseurs polyvalents et fait confiance au groupe plutôt qu\'aux stars.',
        'Paraguay' => 'Huitième de finaliste régulier et quart en 2010, le Paraguay a une défense rugueuse et un jeu direct. Des gardiens de légende comme Chilavert, des attaquants de force et une grande discipline défensive.',
        'République tchèque' => 'Héritière de la Tchécoslovaquie et finaliste de l\'Euro 1996, la Tchéquie joue un football technique et physique, avec un jeu aérien notable. Elle a offert des milieux créatifs, de Nedvěd à Rosický.',
        'Irlande' => 'Quart de finaliste en 1990 sous Jack Charlton, l\'Irlande joue un football direct, physique et très combatif. Ses défenseurs durs et ses milieux infatigables en font un adversaire coriace.',
        'Roumanie' => 'Portée par la génération dorée des années 90 autour de Hagi, quart de finaliste en 1994, la Roumanie allie un meneur technique à une contre-attaque tranchante. Elle produit des milieux et des attaquants vifs.',
        'Ukraine' => 'Quart de finaliste en 2006 avec Shevchenko, l\'Ukraine joue un football rapide en contre-attaque. Héritière de l\'école soviétique du Dynamo Kiev, elle compte sur des attaquants et ailiers vifs.',
        'Bulgarie' => 'Demi-finaliste du Mondial 1994 avec Stoïtchkov, la Bulgarie a connu son âge d\'or à cette époque. Contre-attaque, vitesse et un attaquant de génie en pointe.',
        'Liberia' => 'Nation de George Weah, Ballon d\'Or 1995, le Liberia a une histoire footballistique tenue par un seul phénomène. Jeu rapide en contre-attaque et physique athlétique.',
        'Maroc' => 'Demi-finaliste du Mondial 2022, une première pour une équipe africaine, le Maroc joue une défense très compacte et une contre-attaque efficace. Gardiens solides, ailiers techniques et jeu collectif.',
        'Finlande' => 'Nation de hockey avant tout, la Finlande a trouvé son meilleur football avec Jari Litmanen. Elle joue prudent et défensif, avec un collectif solide plutôt qu\'une grande qualité individuelle.',
        'Pays de Galles' => 'Demi-finaliste de l\'Euro 2016 avec Bale et Ramsey, le Pays de Galles joue un football pragmatique et en contre. Une star (Giggs hier, Bale ensuite) portée par un collectif discipliné.',
        'Yougoslavie' => 'Surnommée le « Brésil de l\'Europe », la Yougoslavie a produit des joueurs techniques et créatifs jusqu\'à son éclatement. Demi-finaliste en 1930 et 1962, elle jouait un football de possession élégant.',
        'Sénégal' => 'Quart de finaliste en 2002 dès sa première participation et vainqueur de la CAN 2021, le Sénégal joue un football athlétique et équilibré. Défense solide, milieux infatigables et ailiers explosifs.',
        'Cameroun' => 'Quart de finaliste en 1990, premier pays africain à ce stade, le Cameroun joue avec puissance et explosivité. Les Lions indomptables produisent des attaquants de force, de Roger Milla à Eto\'o.',
        'Colombie' => 'Quart de finaliste en 2014 avec James Rodríguez, la Colombie pratique un football technique, joueur et chaleureux, héritier de Valderrama. Des milieux créatifs et des attaquants vifs.',
        'Norvège' => 'Longtemps spécialiste du jeu direct et physique, la Norvège a battu le Brésil au Mondial 1998. Elle compte sur un jeu long et des attaquants costauds, dont Haaland aujourd\'hui.',
    ];

    public function up(): void
    {
        foreach (self::DESCRIPTIONS as $name => $description) {
            DB::table('nations')->where('name', $name)
                ->where(fn ($query) => $query->whereNull('description')->orWhere('description', ''))
                ->update(['description' => $description]);
        }
    }

    public function down(): void
    {
        foreach (self::DESCRIPTIONS as $name => $description) {
            DB::table('nations')->where('name', $name)->where('description', $description)->update(['description' => null]);
        }
    }
};
