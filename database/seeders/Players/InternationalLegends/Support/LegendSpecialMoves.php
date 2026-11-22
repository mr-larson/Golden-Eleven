<?php

namespace Database\Seeders\Players\InternationalLegends\Support;

/**
 * Techniques spéciales des légendes réelles (univers `world_legends`).
 *
 * Règles de cohérence :
 *  - la technique traduit un geste ou un trait associé au joueur (dribble, coup franc, arrêt, tacle…),
 *    jamais une statistique de carrière ;
 *  - une technique par joueur, deux seulement pour quelques icônes, et jamais deux sur la même action
 *    (le moteur n'ouvre qu'un emplacement par action de base) ;
 *  - l'action de base suit les points forts du joueur (tir → attaquants/frappeurs, dribble → dribbleurs,
 *    passe → créateurs, centre → latéraux et ailiers, tacle/blocage/interception → défenseurs et
 *    récupérateurs, arrêt main → gardiens) ;
 *  - mode et temps de recharge sont déduits de l'action, comme pour les héros de Captain Tsubasa.
 *
 * Indexé par slug « prenom-nom » ; appliqué uniquement aux joueurs de l'origine `world_legends`.
 */
final class LegendSpecialMoves
{
    /** Temps de recharge par action : les frappes, centres et arrêts sont plus rares que les gestes courants. */
    private const COOLDOWN = ['shot' => 3, 'cross' => 3, 'hand_save' => 3, 'punch_save' => 3];

    /** Actions défensives ; toutes les autres sont offensives. */
    private const DEFENSE = ['tackle', 'block', 'intercept', 'hand_save', 'punch_save'];

    /** Construit une technique : clé, libellé, libellé court, action de base, description. */
    private static function m(string $key, string $label, string $short, string $action, string $description): array
    {
        return [
            'key'         => $key,
            'mode'        => in_array($action, self::DEFENSE, true) ? 'defense' : 'attack',
            'label'       => $label,
            'short_label' => $short,
            'cooldown'    => self::COOLDOWN[$action] ?? 2,
            'base_action' => $action,
            'description' => $description,
        ];
    }

    /** @return array<string, array<int, array<string, mixed>>> */
    public static function specialMoves(): array
    {
        $m = fn (string ...$a) => self::m(...$a);

        return [
            // ───────── Allemagne ─────────
            'franz-beckenbauer' => [$m('beckenbauer_kaiser', 'Libéro de l’Empereur', 'Empereur', 'intercept', 'Il lit l’attaque avant qu’elle ne se déclenche et récupère le ballon avec une élégance tranquille.')],
            'lothar-matthaus' => [$m('matthaus_long_range', 'Frappe de Lothar', 'Longue portée', 'shot', 'Milieu complet, il arme une frappe puissante de loin au moment où la défense recule.')],
            'philipp-lahm' => [$m('lahm_clean_tackle', 'Tacle de Philipp', 'Propre', 'tackle', 'Un tacle net, sans faute, qui coupe la trajectoire de l’ailier.')],
            'andreas-brehme' => [$m('brehme_two_footed', 'Centre ambidextre', 'Deux pieds', 'cross', 'Aussi à l’aise du gauche que du droit, il centre sans que le défenseur sache de quel côté il va jouer.')],
            'pierre-littbarski' => [$m('littbarski_slalom', 'Slalom du Kölner', 'Slalom', 'dribble', 'Petit gabarit et appuis vifs : il se faufile entre les défenseurs sans jamais perdre le ballon.')],
            'miroslav-klose' => [$m('klose_salto', 'Salto de Klose', 'Salto', 'shot', 'Une reprise de volée dans la surface, conclue par son salto pour célébrer.')],
            'karl-heinz-rummenigge' => [$m('rummenigge_cannon', 'Canon bavarois', 'Canon', 'shot', 'Une frappe sèche, du pied droit comme du gauche, lancée en pleine accélération.')],
            'gerd-muller' => [$m('gerd_muller_bomber', 'Bombardier', 'Bombardier', 'shot', 'Un geste bref et instinctif dans la surface : le ballon est au fond avant que la défense ne réagisse.')],
            'thomas-muller' => [$m('thomas_muller_raumdeuter', 'Chasseur d’espaces', 'Espaces', 'shot', 'Il se glisse dans le trou que personne n’a vu et conclut au dernier moment.')],
            'manuel-neuer' => [$m('neuer_sweeper_keeper', 'Gardien-libéro', 'Libéro', 'intercept', 'Il sort loin de sa surface pour couper la passe dans le dos de la défense.')],
            'oliver-kahn' => [$m('kahn_titan', 'Arrêt du Titan', 'Titan', 'hand_save', 'Une intensité féroce et un plongeon sans retenue pour repousser le tir.')],
            'sepp-maier' => [$m('maier_cat', 'Chat de Bavière', 'Chat', 'hand_save', 'Des réflexes de chat : il détourne le ballon d’une détente à bout de bras.')],
            'gunter-netzer' => [$m('netzer_long_pass', 'Long ballon de Netzer', 'Long ballon', 'pass', 'Une ouverture à soixante mètres qui transforme une récupération en occasion.')],
            'toni-kroos' => [$m('kroos_metronome', 'Métronome', 'Métronome', 'pass', 'Toucher de balle précis et tempo parfait : la passe arrive toujours dans le bon pied.')],
            'uwe-seeler' => [$m('seeler_header', 'Tête d’Uwe', 'Tête', 'shot', 'Petit mais explosif de la tête, il saute plus haut que les défenseurs sur chaque centre.')],
            'michael-ballack' => [$m('ballack_late_run', 'Arrivée lancée', 'Arrivée', 'shot', 'Il surgit de la deuxième ligne et frappe de volée au point de penalty.')],

            // ───────── France ─────────
            'fabien-barthez' => [$m('barthez_divine', 'Réflexe du Divin', 'Divin', 'hand_save', 'Un arrêt extravagant, à la limite de la folie, qui sauve ce qui semblait perdu.')],
            'hugo-lloris' => [$m('lloris_reflex', 'Réflexe de Lloris', 'Réflexe', 'hand_save', 'Capitaine des Bleus, il détend son corps entier pour repousser la frappe.')],
            'marcel-desailly' => [$m('desailly_rock', 'Le Roc', 'Roc', 'tackle', 'Puissant et placé, il stoppe net l’attaquant adverse dans son duel.')],
            'patrick-vieira' => [$m('vieira_long_stride', 'Grandes foulées', 'Foulées', 'tackle', 'Ses longues jambes reprennent le ballon en pleine course, puis relancent aussitôt.')],
            'claude-makelele' => [$m('makelele_screen', 'Écran de Makélélé', 'Écran', 'intercept', 'Il se place juste devant la défense et coupe chaque ligne de passe vers les attaquants.')],
            'zinedine-zidane' => [
                $m('zidane_roulette', 'Roulette', 'Roulette', 'dribble', 'Une rotation sur le ballon qui efface l’adversaire sans le toucher.'),
                $m('zidane_volley', 'Volée de Zizou', 'Volée', 'shot', 'Une reprise de volée du gauche, parfaite de gestuelle.'),
            ],
            'michel-platini' => [
                $m('platini_free_kick', 'Coup franc de Michel', 'Coup franc', 'shot', 'Un mur contourné, un ballon qui tombe à l’exact endroit voulu.'),
                $m('platini_vision', 'Passe de maître', 'Maître', 'pass', 'Il voit la solution deux touches avant tout le monde.'),
            ],
            'kylian-mbappe' => [
                $m('mbappe_turbo', 'Turbo', 'Turbo', 'dribble', 'Une pointe de vitesse qui laisse le défenseur sur place.'),
                $m('mbappe_flash_shot', 'Tir éclair', 'Éclair', 'shot', 'Une frappe sèche prise à pleine vitesse, avant que le gardien ne s’organise.'),
            ],
            'just-fontaine' => [$m('fontaine_record', 'Buteur de record', 'Record', 'shot', 'Un sens du but immédiat : une occasion, un but.')],
            'eric-cantona' => [$m('cantona_king', 'Frappe du Roi', 'Roi', 'shot', 'Un geste fier, un tir audacieux : le Roi Éric conclut avec panache.')],
            'thierry-henry' => [
                $m('henry_lightning', 'Course de Titi', 'Éclair', 'dribble', 'Il lance son accélération depuis le côté gauche et déborde plein axe.'),
                $m('henry_curler', 'Enroulé de Thierry', 'Enroulé', 'shot', 'Le ballon file au second poteau, enroulé du pied droit.'),
            ],
            'david-trezeguet' => [$m('trezeguet_volley', 'Volée d’or', 'Volée', 'shot', 'Un ballon frappé de volée, sans contrôle, qui finit dans la lucarne.')],
            'jean-pierre-papin' => [$m('papin_papinade', 'Papinade', 'Papinade', 'shot', 'Une volée instantanée, sans contrôle préalable, que l’on retient sous son nom.')],
            'franck-ribery' => [$m('ribery_percussion', 'Percussion de Franck', 'Percussion', 'dribble', 'Il fonce sur son vis-à-vis et le dépasse par la seule vitesse du geste.')],
            'karim-benzema' => [$m('benzema_subtle_touch', 'Remise subtile', 'Remise', 'pass', 'Une remise du talon ou de l’extérieur qui lance son partenaire.')],
            'antoine-griezmann' => [$m('griezmann_link', 'Relais intelligent', 'Relais', 'pass', 'Il décroche, touche une fois et relance l’attaque dans l’espace libre.')],
            'paul-pogba' => [$m('pogba_diagonal', 'Longue diagonale', 'Diagonale', 'pass', 'Un changement de jeu en une touche, du pied à l’aile opposée.')],
            'ngolo-kante' => [$m('kante_tireless', 'Récupérateur infatigable', 'Infatigable', 'intercept', 'Il couvre autant de terrain que deux joueurs et ferme chaque ligne de passe.')],
            'raphael-varane' => [$m('varane_cover', 'Couverture de Varane', 'Couverture', 'intercept', 'Rapide dans le dos de la défense, il rattrape l’attaquant avant qu’il ne tire.')],
            'raymond-kopa' => [$m('kopa_dribble', 'Dribble de Raymond', 'Dribble', 'dribble', 'Un dribble court et vif, à la limite de la cheville de l’adversaire.')],
            'bixente-lizarazu' => [$m('lizarazu_overlap', 'Débordement de Liza', 'Débordement', 'cross', 'Il remonte la ligne à pleine allure et centre sans ralentir.')],

            // ───────── Argentine ─────────
            'diego-maradona' => [
                $m('maradona_slalom', 'Slalom du siècle', 'Slalom', 'dribble', 'Il traverse la moitié du terrain, tous les défenseurs semés un à un.'),
                $m('maradona_magic_pass', 'Passe magique', 'Magie', 'pass', 'Une passe du gauche, au millimètre, que personne n’avait envisagée.'),
            ],
            'lionel-messi' => [
                $m('messi_pulga', 'Dribble de la Pulga', 'Pulga', 'dribble', 'Centre de gravité bas et ballon collé : il élimine en un seul crochet.'),
                $m('messi_left_curler', 'Enroulé du gauche', 'Enroulé', 'shot', 'Une frappe enroulée du pied gauche, imparable dans l’angle opposé.'),
            ],
            'mario-kempes' => [$m('kempes_matador', 'Matador', 'Matador', 'shot', 'Il perce la défense et achève le ballon d’un tir puissant.')],
            'gabriel-batistuta' => [$m('batistuta_batigol', 'Batigol', 'Batigol', 'shot', 'Une frappe terrible, tirée de loin, qui fait trembler le filet.')],
            'claudio-caniggia' => [$m('caniggia_wind', 'Sprint du vent', 'Vent', 'dribble', 'Cheveux au vent, il file en contre-attaque et laisse les défenseurs sur place.')],
            'alfredo-di-stefano' => [$m('di_stefano_total', 'Jeu total', 'Total', 'pass', 'Il joue partout sur le terrain, défend, relance et distribue.')],
            'juan-roman-riquelme' => [$m('riquelme_pause', 'La pause', 'Pause', 'pass', 'Il ralentit le jeu, fige la défense et distille la passe décisive.')],
            'angel-di-maria' => [$m('di_maria_chip', 'Lob de Fideo', 'Lob', 'shot', 'Un lob délicat par-dessus le gardien sorti.')],
            'sergio-aguero' => [$m('aguero_kun_shot', 'Frappe du Kun', 'Kun', 'shot', 'Un appui rapide, un tir sec : le gardien n’a pas le temps de réagir.')],
            'emiliano-martinez' => [$m('martinez_penalty_stopper', 'Chasseur de penalty', 'Penalty', 'hand_save', 'Il joue avec les nerfs du tireur et détourne le ballon au dernier moment.')],
            'javier-mascherano' => [$m('mascherano_jefecito', 'Jefecito', 'Jefecito', 'tackle', 'Un glissé désespéré qui coupe la course de l’attaquant.')],
            'diego-simeone' => [$m('simeone_cholo_rage', 'Rage du Cholo', 'Cholo', 'tackle', 'Un tacle engagé, dur sur l’homme, qui lance le ton du match.')],
            'juan-sebastian-veron' => [$m('veron_brujita', 'Passe de la Sorcière', 'Sorcière', 'pass', 'Une passe longue et tendue, parfaitement dosée.')],
            'fernando-redondo' => [$m('redondo_heel', 'Talonnade de Redondo', 'Talonnade', 'dribble', 'Il passe le ballon derrière lui du talon pour éliminer le défenseur.')],
            'daniel-passarella' => [$m('passarella_free_kick', 'Frappe du capitaine', 'Capitaine', 'shot', 'Défenseur buteur, il frappe les coups francs avec autorité.')],

            // ───────── Pays-Bas ─────────
            'johan-cruyff' => [
                $m('cruyff_turn', 'Virage de Cruyff', 'Virage', 'dribble', 'Il feint de centrer, tourne sur le ballon et laisse le défenseur dans son dos.'),
                $m('cruyff_total_football', 'Football total', 'Total', 'pass', 'Il se déplace partout et organise le mouvement collectif.'),
            ],
            'marco-van-basten' => [$m('van_basten_volley', 'Volée du cygne', 'Volée', 'shot', 'Une volée acrobatique en pleine course, à l’angle impossible.')],
            'ruud-gullit' => [$m('gullit_power_run', 'Puissance de Ruud', 'Puissance', 'dribble', 'Grand et rapide, il porte le ballon à travers la défense.')],
            'dennis-bergkamp' => [$m('bergkamp_first_touch', 'Première touche', 'Touche', 'dribble', 'Il contrôle le ballon du pied d’une seule touche, élimine le défenseur sans effort.')],
            'arjen-robben' => [
                $m('robben_cut_inside', 'Crochet intérieur', 'Crochet', 'dribble', 'Il part de la droite, rentre sur son pied gauche et déborde.'),
                $m('robben_curler', 'Enroulé de Robben', 'Enroulé', 'shot', 'Après son crochet, une frappe enroulée au second poteau.'),
            ],
            'wesley-sneijder' => [$m('sneijder_long_shot', 'Frappe de Wesley', 'Frappe', 'shot', 'Une frappe lourde prise à l’entrée de la surface.')],
            'robin-van-persie' => [$m('van_persie_flying_header', 'Tête plongeante', 'Plongeante', 'shot', 'Un plongeon de la tête, le corps horizontal, pour envoyer le ballon au fond.')],
            'virgil-van-dijk' => [$m('van_dijk_wall', 'Mur de Virgil', 'Mur', 'block', 'Grand et puissant, il ferme l’axe et bloque le tir adverse.')],
            'jaap-stam' => [$m('stam_colossus', 'Colosse', 'Colosse', 'tackle', 'Un tacle massif qui ne laisse aucune chance à l’attaquant.')],
            'ronald-koeman' => [$m('koeman_free_kick', 'Coup franc de Koeman', 'Coup franc', 'shot', 'Défenseur à la frappe de feu, il arme des coups francs terribles.')],
            'edgar-davids' => [$m('davids_pitbull', 'Pitbull', 'Pitbull', 'tackle', 'Lunettes sur le nez, il mord dans chaque duel jusqu’à récupérer le ballon.')],
            'marc-overmars' => [$m('overmars_speed', 'Flèche néerlandaise', 'Flèche', 'dribble', 'Une accélération foudroyante qui scie les défenses.')],
            'edwin-van-der-sar' => [$m('van_der_sar_calm', 'Calme olympien', 'Calme', 'hand_save', 'Placement parfait et sang-froid : l’arrêt paraît facile.')],
            'frank-de-boer' => [$m('de_boer_long_pass', 'Longue transversale', 'Transversale', 'pass', 'Une passe lobée de quarante mètres, qui lance l’aile opposée.')],
            'clarence-seedorf' => [$m('seedorf_long_shot', 'Frappe de Clarence', 'Frappe', 'shot', 'Une frappe puissante de loin, venue de nulle part.')],

            // ───────── Uruguay ─────────
            'luis-suarez' => [$m('suarez_pistolero', 'Pistolero', 'Pistolero', 'shot', 'Un appétit féroce de but, il frappe toujours le premier.')],
            'diego-forlan' => [$m('forlan_knuckleball', 'Frappe flottante', 'Flottante', 'shot', 'Un ballon sans rotation qui fuit dans les airs, imprévisible pour le gardien.')],
            'edinson-cavani' => [$m('cavani_header', 'Tête d’Edi', 'Tête', 'shot', 'Un saut puissant au point de penalty, qui domine la défense.')],
            'juan-alberto-schiaffino' => [$m('schiaffino_maestro', 'Maestro', 'Maestro', 'pass', 'Il lit le jeu comme personne et délivre la passe décisive.')],
            'enzo-francescoli' => [$m('francescoli_prince', 'Élégance du Prince', 'Prince', 'pass', 'Une passe élégante, du plat du pied, qui libère l’attaquant.')],
            'diego-godin' => [$m('godin_garra', 'Garra charrúa', 'Garra', 'tackle', 'Il se jette dans chaque duel, tout l’orgueil de l’Uruguay dans la jambe.')],
            'obdulio-varela' => [$m('varela_black_chief', 'Chef noir', 'Chef', 'tackle', 'Le capitaine règne sur le milieu et refuse tout recul.')],
            'alcides-ghiggia' => [$m('ghiggia_maracana', 'Frappe du Maracanã', 'Maracanã', 'shot', 'Il déborde sur l’aile et conclut au premier poteau quand on attend le centre.')],
            'federico-valverde' => [$m('valverde_falcon', 'Course du Faucon', 'Faucon', 'dribble', 'Une cavalcade de quarante mètres qui change le sens du match.')],
            'alvaro-recoba' => [$m('recoba_chino', 'Gaucher du Chino', 'Chino', 'shot', 'Une frappe du gauche d’une rare précision, de loin ou en coup franc.')],

            // ───────── Mexique ─────────
            'guillermo-ochoa' => [$m('ochoa_wall', 'Mur d’Ochoa', 'Mur', 'hand_save', 'Des arrêts impossibles quand tout semble perdu.')],
            'hugo-sanchez' => [$m('hugo_sanchez_overhead', 'Ciseau d’Hugo', 'Ciseau', 'shot', 'Une bicyclette en pleine surface, sa signature.')],
            'cuauhtemoc-blanco' => [$m('blanco_hop', 'Cuauhtemiña', 'Saut', 'dribble', 'Il coince le ballon entre ses chevilles et saute par-dessus le défenseur.')],
            'rafael-marquez' => [$m('marquez_captain', 'Capitaine de fer', 'Capitaine', 'intercept', 'Il lit la trajectoire et coupe la passe avec autorité.')],
            'javier-hernandez' => [$m('chicharito_poacher', 'Renard de la surface', 'Renard', 'shot', 'Un appel dans le dos du défenseur et une frappe de renard.')],
            'hirving-lozano' => [$m('lozano_sprint', 'Sprint du Chucky', 'Sprint', 'dribble', 'Il lance sa course dans les espaces et dépasse la défense à toute allure.')],

            // ───────── Brésil ─────────
            'pele' => [
                $m('pele_bicycle', 'Bicyclette de Pelé', 'Bicyclette', 'shot', 'Une reprise acrobatique, le corps en l’air, qui s’abat dans le filet.'),
                $m('pele_dummy', 'Feinte de Pelé', 'Feinte', 'dribble', 'Il laisse filer le ballon, trompe le gardien et le contourne.'),
            ],
            'ronaldo' => [
                $m('ronaldo_phenomenon_run', 'Course du Phénomène', 'Phénomène', 'dribble', 'Une accélération irrésistible, ballon collé au pied, jusque dans la surface.'),
                $m('ronaldo_power_shot', 'Frappe du Phénomène', 'Frappe', 'shot', 'Un tir puissant et net, croisé avec froideur face au gardien.'),
            ],
            'garrincha' => [$m('garrincha_bird', 'Dribble de l’oiseau', 'Oiseau', 'dribble', 'Ses jambes arquées trompent l’adversaire à chaque feinte.')],
            'didi' => [$m('didi_dry_leaf', 'Feuille morte', 'Feuille morte', 'shot', 'Un coup franc qui plonge brutalement juste avant le but.')],
            'jairzinho' => [$m('jairzinho_hurricane', 'Ouragan', 'Ouragan', 'shot', 'Un débordement sur l’aile et une frappe qui emporte tout.')],
            'romario' => [$m('romario_baixinho', 'Frappe du Baixinho', 'Baixinho', 'shot', 'Petit mais létal : un geste rapide, un but sûr.')],
            'leonidas' => [$m('leonidas_bicycle', 'Bicyclette de Leônidas', 'Bicyclette', 'shot', 'La reprise acrobatique qu’on lui attribue, lancée de dos au but.')],
            'rivaldo' => [$m('rivaldo_scissors', 'Ciseau de Rivaldo', 'Ciseau', 'shot', 'Une volée retournée à la dernière seconde pour arracher le but.')],
            'ronaldinho' => [
                $m('ronaldinho_elastico', 'Élastico', 'Élastico', 'dribble', 'Il feint du dehors, puis de l’intérieur : le défenseur est déjà battu.'),
                $m('ronaldinho_no_look', 'Passe aveugle', 'Aveugle', 'pass', 'Le regard à droite, la passe à gauche : tout le monde est surpris.'),
            ],
            'neymar' => [$m('neymar_ginga', 'Ginga', 'Ginga', 'dribble', 'Des feintes de corps répétées au rythme de la samba.')],
            'kaka' => [$m('kaka_run', 'Cavalcade de Kaká', 'Cavalcade', 'dribble', 'Ballon au pied, il avale le terrain avec une élégance fluide.')],
            'zico' => [$m('zico_free_kick', 'Coup franc de Zico', 'Coup franc', 'shot', 'Le ballon contourne le mur et retombe dans la lucarne.')],
            'socrates' => [$m('socrates_heel_pass', 'Passe du talon', 'Talon', 'pass', 'Une passe du talon, sans regarder, qui libère son partenaire.')],
            'vinicius-junior' => [$m('vinicius_samba', 'Samba de Vini', 'Samba', 'dribble', 'Une série de feintes endiablées pour éliminer son vis-à-vis.')],
            'roberto-carlos' => [$m('roberto_carlos_banana', 'Coup franc en banane', 'Banane', 'shot', 'Une frappe qui contourne le mur de l’extérieur du pied avant de retomber au but.')],
            'marcos-cafu' => [$m('cafu_express', 'Cafu Express', 'Express', 'cross', 'Il remonte l’aile droite sans cesse et centre à pleine allure.')],
            'claudio-taffarel' => [$m('taffarel_penalty', 'Mains de Taffarel', 'Mains', 'hand_save', 'Calme dans les séances décisives, il détourne le tir adverse.')],
            'alisson' => [$m('alisson_reflex', 'Réflexe d’Alisson', 'Réflexe', 'hand_save', 'Un réflexe à bout de bras sur un tir à bout portant.')],
            'carlos-dunga' => [$m('dunga_fighter', 'Combattant', 'Combattant', 'tackle', 'Un tacle sans concession qui impose le rythme et l’état d’esprit.')],
            'casemiro' => [$m('casemiro_destroyer', 'Destructeur', 'Destructeur', 'tackle', 'Il ferme l’axe devant la défense et casse chaque attaque.')],
            'thiago-silva' => [$m('thiago_silva_reading', 'Lecture de Thiago', 'Lecture', 'intercept', 'Il anticipe la passe longtemps avant de réagir.')],

            // ───────── Italie ─────────
            'roberto-baggio' => [
                $m('baggio_free_kick', 'Coup franc du Divin', 'Divin', 'shot', 'Un ballon posé à la perfection, qui plonge sous la barre.'),
                $m('baggio_dribble', 'Dribble du Divin', 'Queue-de-cheval', 'dribble', 'Il efface deux défenseurs d’un geste souple avant de se présenter devant le gardien.'),
            ],
            'francesco-totti' => [$m('totti_cucchiaio', 'Cuillère', 'Cuillère', 'shot', 'Un lob en cuillère, audacieux, qui trompe le gardien.')],
            'alessandro-del-piero' => [$m('del_piero_zone', 'Zone Del Piero', 'Zone', 'shot', 'Un enroulé du pied droit depuis le côté gauche, vers le second poteau.')],
            'andrea-pirlo' => [
                $m('pirlo_maestro', 'Maestro', 'Maestro', 'pass', 'Du fond du terrain, il dicte le jeu d’une passe lumineuse.'),
                $m('pirlo_free_kick', 'Coup franc de Pirlo', 'Coup franc', 'shot', 'Une feuille morte aérienne sur coup franc.'),
            ],
            'paolo-maldini' => [$m('maldini_perfect_tackle', 'Tacle parfait', 'Parfait', 'tackle', 'Un tacle à la précision d’horloger : il prend le ballon, pas l’homme.')],
            'fabio-cannavaro' => [$m('cannavaro_wall_2006', 'Muraille', 'Muraille', 'block', 'Petit mais indomptable, il bloque chaque tir adverse.')],
            'alessandro-nesta' => [$m('nesta_anticipation', 'Anticipation de Nesta', 'Anticipation', 'intercept', 'Il intercepte le ballon d’un tacle propre, souvent avant qu’il ne parvienne à l’attaquant.')],
            'franco-baresi' => [$m('baresi_libero', 'Libéro de Milan', 'Libéro', 'intercept', 'Il commande la ligne défensive et anticipe chaque ballon.')],
            'gianluigi-buffon' => [$m('buffon_gigi', 'Mains de Gigi', 'Mains', 'hand_save', 'Un arrêt à la détente, d’une main, qui refuse le but.')],
            'dino-zoff' => [$m('zoff_veteran', 'Doyen de 82', 'Doyen', 'hand_save', 'Sang-froid absolu : il capte chaque tir sans sourciller.')],
            'gennaro-gattuso' => [$m('gattuso_ringhio', 'Ringhio', 'Ringhio', 'tackle', 'Il grogne, il mord, il ne lâche jamais le ballon.')],
            'giorgio-chiellini' => [$m('chiellini_stopper', 'Stoppeur de fer', 'Stoppeur', 'block', 'Un corps-à-corps de chaque instant qui étouffe l’attaquant.')],
            'paolo-rossi' => [$m('rossi_pablito', 'Pablito', 'Pablito', 'shot', 'Il se trouve toujours au bon endroit, au bon moment.')],
            'giuseppe-meazza' => [$m('meazza_feint', 'Feinte de Meazza', 'Feinte', 'dribble', 'Une feinte de corps géniale qui fait basculer le défenseur.')],
            'gigi-riva' => [$m('riva_thunder', 'Coup de tonnerre', 'Tonnerre', 'shot', 'Une frappe de gauche d’une puissance monstrueuse.')],
            'gianni-rivera' => [$m('rivera_golden_boy', 'Golden Boy', 'Golden', 'pass', 'Une passe d’une élégance rare, glissée entre deux lignes.')],
            'filippo-inzaghi' => [$m('inzaghi_offside', 'Maître du hors-jeu', 'Hors-jeu', 'shot', 'Il joue sur le fil de la ligne et conclut d’une touche opportuniste.')],
            'christian-vieri' => [$m('vieri_bomber', 'Bobo bomber', 'Bomber', 'shot', 'Épaule contre défenseur puis frappe puissante des vingt mètres.')],

            // ───────── Portugal ─────────
            'eusebio' => [$m('eusebio_black_panther', 'Panthère noire', 'Panthère', 'shot', 'Un tir foudroyant déclenché en pleine course, d’une puissance féline.')],
            'cristiano-ronaldo' => [
                $m('cr7_stepover', 'Passement de jambes', 'Passements', 'dribble', 'Une succession de passements qui désorientent le défenseur avant l’accélération.'),
                $m('cr7_knuckle', 'Frappe flottante', 'Flottante', 'shot', 'Un ballon sans rotation, tiré de loin, qui dévie en l’air.'),
            ],
            'luis-figo' => [$m('figo_cross', 'Centre de Luís', 'Centre', 'cross', 'Il dribble puis centre à ras de terre dans la course du partenaire.')],
            'rui-costa' => [$m('rui_costa_maestro', 'Maestro de Lisbonne', 'Maestro', 'pass', 'Une passe sans regarder, glissée dans l’espace entre deux défenseurs.')],
            'deco' => [$m('deco_magician', 'Magicien', 'Magicien', 'pass', 'Toujours au bon endroit, il redistribue le ballon d’un geste simple.')],
            'pauleta' => [$m('pauleta_poacher', 'Pauleta tueur', 'Tueur', 'shot', 'Petit mouvement, frappe immédiate : le but est là.')],
            'paulo-futre' => [$m('futre_dribble', 'Dribble de Paulo', 'Dribble', 'dribble', 'Un jeu de jambes enlevé qui force la faute.')],
            'bernardo-silva' => [$m('bernardo_silva_tight', 'Dribble en espace réduit', 'Espace réduit', 'dribble', 'Il garde le ballon dans un mouchoir de poche et sort indemne.')],
            'bruno-fernandes' => [$m('bruno_laser_pass', 'Passe laser', 'Laser', 'pass', 'Une passe tendue, rapide et précise, qui coupe la défense.')],
            'ruben-dias' => [$m('ruben_dias_wall', 'Mur de Rúben', 'Mur', 'block', 'Il tient son axe avec autorité et bloque l’attaquant.')],
            'pepe' => [$m('pepe_hard_tackle', 'Tacle de Pepe', 'Tacle', 'tackle', 'Un tacle appuyé et sans pitié, qui décourage l’attaquant.')],

            // ───────── Angleterre ─────────
            'david-beckham' => [
                $m('beckham_free_kick', 'Coup franc de Becks', 'Coup franc', 'shot', 'Un ballon enroulé par-dessus le mur et retombé dans l’angle.'),
                $m('beckham_cross', 'Centre de Becks', 'Centre', 'cross', 'Un centre tendu du pied droit, à l’endroit exact du receveur.'),
            ],
            'michael-owen' => [$m('owen_acceleration', 'Accélération d’Owen', 'Accélération', 'dribble', 'Il lance sa course dans le dos de la défense et lui échappe.')],
            'alan-shearer' => [$m('shearer_thunder', 'Tonnerre de Newcastle', 'Tonnerre', 'shot', 'Une frappe lourde, bras levé, juste après la déviation du gardien.')],
            'paul-scholes' => [$m('scholes_through_ball', 'Passe en profondeur', 'Profondeur', 'pass', 'Une passe longue et précise qui perce la défense adverse.')],
            'steven-gerrard' => [$m('gerrard_rocket', 'Fusée de Steven', 'Fusée', 'shot', 'Une frappe de trente mètres qui jaillit sans prévenir.')],
            'frank-lampard' => [$m('lampard_late_arrival', 'Arrivée de Frank', 'Arrivée', 'shot', 'Il surgit de derrière et frappe du droit à l’entrée de la surface.')],
            'paul-gascoigne' => [$m('gascoigne_slalom', 'Slalom de Gazza', 'Slalom', 'dribble', 'Il s’invente un chemin, balle au pied, parmi les défenseurs.')],
            'bobby-charlton' => [$m('charlton_cannon', 'Canon des Red Devils', 'Canon', 'shot', 'Une frappe lourde du droit, de loin, qui enflamme le stade.')],
            'bobby-moore' => [$m('moore_clean_tackle', 'Tacle de Bobby', 'Tacle', 'tackle', 'Un tacle d’une rare pureté, sans le moindre geste inutile.')],
            'gordon-banks' => [$m('banks_save_century', 'Arrêt du siècle', 'Arrêt du siècle', 'hand_save', 'Une détente stupéfiante pour détourner une tête qui semblait au fond.')],
            'peter-shilton' => [$m('shilton_last_wall', 'Dernier rempart', 'Rempart', 'hand_save', 'Gardien imposant, il tient son but jusqu’au bout.')],
            'wayne-rooney' => [$m('rooney_bicycle', 'Ciseau de Wayne', 'Ciseau', 'shot', 'Une bicyclette inattendue, mise d’un ballon en cloche.')],
            'harry-kane' => [$m('kane_deflection', 'Déviation de Kane', 'Déviation', 'pass', 'Il décroche, joue en une touche et lance ses coéquipiers.')],
            'gary-lineker' => [$m('lineker_instinct', 'Instinct de Gary', 'Instinct', 'shot', 'Un appel de balle, une touche, un but : la surface est son territoire.')],
            'jude-bellingham' => [$m('bellingham_late_run', 'Arrivée de Jude', 'Arrivée', 'shot', 'Il suit l’action, surgit dans la surface et conclut de volée.')],
            'rio-ferdinand' => [$m('ferdinand_elegant', 'Défense élégante', 'Élégance', 'intercept', 'Il intercepte sans effort apparent, puis relance proprement.')],
            'john-terry' => [$m('terry_captain_block', 'Tête de Terry', 'Tête', 'block', 'Il se jette devant le tir, de la tête ou du corps, pour sauver son but.')],
            'ashley-cole' => [$m('ashley_cole_recovery', 'Retour d’Ashley', 'Retour', 'tackle', 'Il rattrape son vis-à-vis et lui reprend le ballon d’un tacle glissé.')],

            // ───────── Espagne ─────────
            'xavi-hernandez' => [$m('xavi_tiki_taka', 'Tiki-taka', 'Tiki-taka', 'pass', 'Une succession de passes courtes qui épuise l’adversaire.')],
            'andres-iniesta' => [$m('iniesta_croqueta', 'Croqueta', 'Croqueta', 'dribble', 'Un petit pas de côté, le ballon passe d’un pied à l’autre et le défenseur est battu.')],
            'iker-casillas' => [$m('casillas_san_iker', 'Saint Iker', 'Saint', 'hand_save', 'Des arrêts presque miraculeux dans les moments qui comptent.')],
            'rodri' => [$m('rodri_pivot', 'Pivot de Rodri', 'Pivot', 'intercept', 'Il gère les transitions et coupe les passes dans l’axe.')],
            'sergio-ramos' => [
                $m('ramos_tackle', 'Tacle de Ramos', 'Tacle', 'tackle', 'Un tacle engagé, souvent à la limite, qui stoppe l’attaque.'),
                $m('ramos_header', 'Tête sauveuse', 'Tête', 'shot', 'Une tête puissante sur corner, qui arrive tard et fait mouche.'),
            ],
            'carles-puyol' => [$m('puyol_captain', 'Capitaine de fer', 'Capitaine', 'block', 'Crinière au vent, il se jette devant chaque tir.')],
            'sergio-busquets' => [$m('busquets_pivot', 'Pivot de Sergio', 'Pivot', 'intercept', 'Il lit le jeu comme un livre ouvert et coupe la passe adverse.')],
            'xabi-alonso' => [$m('alonso_diagonal', 'Diagonale de Xabi', 'Diagonale', 'pass', 'Une passe longue et tendue, de l’autre bout du terrain.')],
            'cesc-fabregas' => [$m('fabregas_assist', 'Passe de Cesc', 'Passe', 'pass', 'Il glisse un ballon décisif entre deux défenseurs.')],
            'david-villa' => [$m('villa_guaje', 'Guaje', 'Guaje', 'shot', 'Il frappe du gauche, du droit, de loin, de près, toujours dans le cadre.')],
            'fernando-torres' => [$m('torres_nino', 'Frappe du Niño', 'Niño', 'shot', 'Un appel en profondeur, une frappe vive : le Niño est déjà passé.')],
            'raul-gonzalez' => [$m('raul_instinct', 'Instinct de Raúl', 'Instinct', 'shot', 'Un sens du but affûté : il flaire le ballon et conclut d’une touche.')],
            'emilio-butragueno' => [$m('butragueno_vulture', 'Vautour', 'Vautour', 'shot', 'Il rôde dans la surface, attend la moindre erreur et fond sur le ballon.')],
            'pep-guardiola' => [$m('guardiola_pivot', 'Pivot catalan', 'Pivot', 'pass', 'Il orchestre le jeu depuis la base du milieu avec une passe juste.')],
            'fernando-hierro' => [$m('hierro_free_kick', 'Frappe de Hierro', 'Frappe', 'shot', 'Défenseur buteur, il frappe fort sur coup franc ou penalty.')],
            'jordi-alba' => [$m('alba_overlap', 'Débordement d’Alba', 'Débordement', 'cross', 'Une course sur toute la ligne de touche et un centre à ras de terre.')],
            'lamine-yamal' => [$m('yamal_dribble', 'Dribble de Lamine', 'Dribble', 'dribble', 'Il élimine son vis-à-vis dès la première touche, avec une maturité déconcertante.')],
            'pedri' => [$m('pedri_touch', 'Touche de Pedri', 'Touche', 'pass', 'Un contrôle orienté puis une passe en une touche entre les lignes.')],

            // ───────── Croatie ─────────
            'luka-modric' => [
                $m('modric_outside_pass', 'Passe de l’extérieur', 'Extérieur', 'pass', 'Une passe de l’extérieur du pied, courbe, qui trouve son partenaire dans le dos de la défense.'),
                $m('modric_turn', 'Rotation de Luka', 'Rotation', 'dribble', 'Un pivot serré qui élimine le pressing.'),
            ],
            'davor-suker' => [$m('suker_chip', 'Lob de Davor', 'Lob', 'shot', 'Un lob par-dessus le gardien, d’un geste d’une désinvolture élégante.')],
            'robert-prosinecki' => [$m('prosinecki_pass', 'Passe de Robert', 'Passe', 'pass', 'Une passe lumineuse qui change l’orientation du jeu.')],
            'ivan-rakitic' => [$m('rakitic_metronome', 'Tempo de Rakitić', 'Tempo', 'pass', 'Il dicte le rythme, redistribue et temporise.')],
            'mario-mandzukic' => [$m('mandzukic_header', 'Tête de Mario', 'Tête', 'shot', 'Un puissant coup de tête, dos au but, qui surprend le gardien.')],
            'ivan-perisic' => [$m('perisic_volley', 'Volée de Ivan', 'Volée', 'shot', 'Une reprise de volée du pied gauche, tendue et précise.')],
            'darijo-srna' => [$m('srna_cross', 'Centre de Darijo', 'Centre', 'cross', 'Un centre sec du droit, frappé à pleine puissance.')],
            'josko-gvardiol' => [$m('gvardiol_stopper', 'Stoppeur de Joško', 'Stoppeur', 'tackle', 'Rapide et puissant, il gagne son duel dans le dos de la défense.')],

            // ───────── Danemark ─────────
            'peter-schmeichel' => [$m('schmeichel_star', 'Étoile de mer', 'Étoile', 'hand_save', 'Bras et jambes écartés, il occupe tout l’angle face à l’attaquant.')],
            'michael-laudrup' => [$m('laudrup_no_look', 'Passe aveugle de Michael', 'Aveugle', 'pass', 'Une passe en profondeur sans regard, qui laisse tout le monde derrière.')],
            'brian-laudrup' => [$m('b_laudrup_feint', 'Feinte de Brian', 'Feinte', 'dribble', 'Un jeu de jambes tranchant sur l’aile qui efface le latéral.')],
            'christian-eriksen' => [$m('eriksen_set_piece', 'Corner direct', 'Corner', 'cross', 'Un centre enroulé au millimètre, qui tombe dans le dos de la défense.')],
            'preben-elkjaer-larsen' => [$m('elkjaer_boot', 'Frappe de Vérone', 'Vérone', 'shot', 'Il garde sa course et frappe, même chaussure perdue.')],
            'kasper-schmeichel' => [$m('k_schmeichel_reflex', 'Réflexe de Kasper', 'Réflexe', 'hand_save', 'Un réflexe de chat sur un tir à bout portant.')],

            // ───────── Autres sélections européennes ─────────
            'jose-luis-chilavert' => [$m('chilavert_free_kick', 'Gardien buteur', 'Buteur', 'shot', 'Le gardien s’avance et arme un coup franc ou un penalty d’une frappe puissante.')],
            'miguel-almiron' => [$m('almiron_run', 'Course d’Almirón', 'Course', 'dribble', 'Une accélération rapide qui prend la défense à contre-pied.')],
            'carlos-gamarra' => [$m('gamarra_clean_tackle', 'Tacle propre', 'Propre', 'tackle', 'Un tacle qui prend le ballon sans jamais commettre la faute.')],
            'pavel-nedved' => [$m('nedved_furia', 'Furia Ceca', 'Furia', 'shot', 'Une frappe puissante de loin, lancée avec toute sa rage.')],
            'jan-koller' => [$m('koller_tower', 'Tour de contrôle', 'Tour', 'shot', 'Très grand, il domine les airs et conclut de la tête.')],
            'petr-cech' => [$m('cech_wall', 'Mur de Petr', 'Mur', 'hand_save', 'Un placement impeccable : le tir finit dans ses gants.')],
            'tomas-rosicky' => [$m('rosicky_mozart', 'Petit Mozart', 'Mozart', 'pass', 'Un jeu fluide et créatif, des passes qui jouent la mélodie.')],
            'karel-poborsky' => [$m('poborsky_chip', 'Lob de Karel', 'Lob', 'shot', 'Un lob délicat par-dessus le gardien, venu de loin.')],
            'antonin-panenka' => [$m('panenka_penalty', 'Panenka', 'Panenka', 'shot', 'Un penalty piqué en douceur au milieu du but, pendant que le gardien plonge.')],
            'roy-keane' => [$m('keane_captain_tackle', 'Capitaine de combat', 'Capitaine', 'tackle', 'Il récupère tout, avec une intensité qui impose le respect.')],
            'paul-mcgrath' => [$m('mcgrath_wall', 'Mur de McGrath', 'Mur', 'block', 'Il bloque chaque centre et chaque tir malgré tout.')],
            'robbie-keane' => [$m('r_keane_finisher', 'Frappe de Robbie', 'Frappe', 'shot', 'Un sens du placement qui lui fait trouver le bon coin.')],
            'liam-brady' => [$m('brady_left_foot', 'Gaucher de Liam', 'Gaucher', 'pass', 'Un pied gauche d’orfèvre qui distribue chaque ballon.')],
            'niall-quinn' => [$m('quinn_head', 'Tête de Niall', 'Tête', 'shot', 'Un géant qui gagne tous les ballons aériens.')],
            'damien-duff' => [$m('duff_dribble', 'Dribble de Damien', 'Dribble', 'dribble', 'Des feintes courtes et rapides sur l’aile gauche.')],
            'gheorghe-hagi' => [$m('hagi_carpathes', 'Gaucher des Carpates', 'Gaucher', 'shot', 'Une frappe du pied gauche d’une puissance et d’une précision spectaculaires.')],
            'dan-petrescu' => [$m('petrescu_cross', 'Centre de Dan', 'Centre', 'cross', 'Il remonte sur son côté droit et centre sans faiblir.')],
            'andriy-shevchenko' => [$m('shevchenko_sheva', 'Sheva', 'Sheva', 'shot', 'Vitesse et puissance : une frappe nette qui surprend le gardien.')],
            'oleh-blokhin' => [$m('blokhin_speed', 'Vitesse de Blokhin', 'Vitesse', 'dribble', 'Il file dans le couloir, plus vite que son vis-à-vis.')],
            'hristo-stoichkov' => [$m('stoichkov_free_kick', 'Coup franc de Hristo', 'Coup franc', 'shot', 'Un pied gauche rageur, un mur contourné.')],
            'dimitar-berbatov' => [$m('berbatov_touch', 'Contrôle de Dimitar', 'Contrôle', 'dribble', 'Un contrôle nonchalant qui désoriente le défenseur.')],
            'george-weah' => [$m('weah_run', 'Cavalcade de Milan', 'Cavalcade', 'dribble', 'Il part de sa surface et traverse tout le terrain, ballon au pied.')],
            'dejan-savicevic' => [$m('savicevic_feint', 'Feinte du Génie', 'Génie', 'dribble', 'Un déhanché nonchalant et deux défenseurs ont changé de sens.')],
            'jari-litmanen' => [$m('litmanen_pass', 'Passe de Jari', 'Passe', 'pass', 'Un jeu intelligent, une passe qui perce là où personne ne regarde.')],
            'ryan-giggs' => [$m('giggs_run', 'Cavalcade de Ryan', 'Cavalcade', 'dribble', 'Une course folle depuis le milieu du terrain, plusieurs défenseurs semés.')],
            'gareth-bale' => [
                $m('bale_sprint', 'Course de Gareth', 'Course', 'dribble', 'Une pointe de vitesse qui dépasse le défenseur dans la ligne.'),
                $m('bale_rocket', 'Frappe de Gareth', 'Frappe', 'shot', 'Un missile du pied gauche, de trente mètres.'),
            ],
            'ian-rush' => [$m('rush_poacher', 'Instinct d’Ian', 'Instinct', 'shot', 'Il rôde, anticipe et conclut en un clin d’œil.')],

            // ───────── Scandinavie ─────────
            'zlatan-ibrahimovic' => [$m('ibrahimovic_zlatan', 'Geste de Zlatan', 'Zlatan', 'shot', 'Un geste acrobatique de sa taille, une volée ou un ciseau inattendu.')],
            'henrik-larsson' => [$m('larsson_henke', 'Frappe de Henrik', 'Henke', 'shot', 'Une volée précise ou une tête vive, signée Henrik.')],
            'tomas-brolin' => [$m('brolin_feint', 'Feinte de Tomas', 'Feinte', 'dribble', 'Il garde le ballon et fait basculer le défenseur.')],
            'alexander-isak' => [$m('isak_glide', 'Glissade d’Alexander', 'Glissade', 'dribble', 'Grand mais fluide, il enchaîne les contrôles et déborde d’un seul mouvement.')],
            'gunnar-nordahl' => [$m('nordahl_goal_machine', 'Machine à buts', 'Machine', 'shot', 'Un avant-centre massif qui accumule les buts, méthodiquement.')],
            'thomas-ravelli' => [$m('ravelli_wall', 'Mur de Thomas', 'Mur', 'hand_save', 'Un gardien imperturbable, les tirs finissent dans ses mains.')],
            'erling-haaland' => [
                $m('haaland_terminator', 'Terminator', 'Terminator', 'shot', 'Une frappe sèche et puissante, déclenchée dès qu’il a la place.'),
                $m('haaland_power_run', 'Course de buffle', 'Buffle', 'dribble', 'Il lance sa course et personne ne peut l’arrêter.'),
            ],
            'martin-odegaard' => [$m('odegaard_through_ball', 'Passe de Martin', 'Passe', 'pass', 'Une passe décisive glissée entre deux défenseurs.')],
            'ole-gunnar-solskjaer' => [$m('solskjaer_supersub', 'Supersub', 'Supersub', 'shot', 'Il entre en cours de jeu et marque presque immédiatement.')],
            'john-arne-riise' => [$m('riise_left_cannon', 'Canon gauche', 'Canon', 'shot', 'Une frappe de gauche à bout de bras, sans prévenir.')],
            'kjetil-rekdal' => [$m('rekdal_long_shot', 'Frappe de Kjetil', 'Frappe', 'shot', 'Un tir lointain, plein de rage, qui s’invite dans la lucarne.')],

            // ───────── Afrique ─────────
            'jay-jay-okocha' => [$m('okocha_triple_feint', 'Triple feinte', 'Triple feinte', 'dribble', 'Une feinte puis une autre, et encore une : le défenseur est resté sur place.')],
            'nwankwo-kanu' => [$m('kanu_lanky', 'Dribble de Kanu', 'Dribble', 'dribble', 'Grand et souple, il garde le ballon à bout de jambe, d’un geste déroutant.')],
            'rashidi-yekini' => [$m('yekini_header', 'Tête de Rashidi', 'Tête', 'shot', 'Un ballon dévié d’une tête puissante dans la surface.')],
            'victor-osimhen' => [$m('osimhen_aerial', 'Détente d’Osimhen', 'Détente', 'shot', 'Un saut explosif qui domine tous les défenseurs.')],
            'vincent-enyeama' => [$m('enyeama_wall', 'Mur d’Enyeama', 'Mur', 'hand_save', 'Plongeon en extension, arrêt du bout des gants.')],
            'sunday-oliseh' => [$m('oliseh_long_shot', 'Frappe d’Oliseh', 'Frappe', 'shot', 'Une frappe lointaine, directe, qui surprend le gardien.')],
            'finidi-george' => [$m('finidi_cross', 'Centre de Finidi', 'Centre', 'cross', 'Un centre vif du pied droit, tiré depuis l’aile.')],
            'roger-milla' => [$m('milla_lion', 'Lion indomptable', 'Lion', 'shot', 'Un vétéran qui arrive en cours de match et conclut avec une fraîcheur insolente.')],
            'samuel-etoo' => [$m('etoo_counter', 'Contre-attaque d’Eto’o', 'Contre', 'shot', 'Il part en profondeur, seul, et conclut avec un calme féroce.')],
            'thomas-nkono' => [$m('nkono_dive', 'Plongeon de N’Kono', 'Plongeon', 'hand_save', 'Un envol à l’horizontale pour détourner le tir.')],
            'geremi-njitap' => [$m('geremi_cross', 'Centre de Geremi', 'Centre', 'cross', 'Un centre long et lourd, tiré du pied droit.')],
            'sadio-mane' => [$m('mane_dash', 'Accélération de Sadio', 'Accélération', 'dribble', 'Une explosion de vitesse qui laisse le défenseur dans son sillage.')],
            'kalidou-koulibaly' => [$m('koulibaly_wall', 'Mur de Naples', 'Mur', 'tackle', 'Il gagne ses duels dos au but comme face à l’attaquant.')],
            'edouard-mendy' => [$m('mendy_reflex', 'Réflexe d’Édouard', 'Réflexe', 'hand_save', 'Un grand gabarit : il couvre le but et capte le tir d’une main.')],
            'idrissa-gueye' => [$m('gueye_ball_winner', 'Récupérateur d’Idrissa', 'Récupérateur', 'tackle', 'Il prend le ballon à l’adversaire et relance sans perdre de temps.')],
            'papa-bouba-diop' => [$m('diop_2002', 'Frappe de Papa', 'Frappe', 'shot', 'Une frappe lourde au bon endroit, qui bouscule le favori.')],
            'achraf-hakimi' => [$m('hakimi_rocket', 'Fusée d’Achraf', 'Fusée', 'cross', 'Une course de feu sur le côté droit, un centre qui arrive avant la défense.')],
            'hakim-ziyech' => [$m('ziyech_left_foot', 'Gaucher de Hakim', 'Gaucher', 'pass', 'Une passe du pied gauche, tendue, qui fend les lignes.')],
            'larbi-benbarek' => [$m('benbarek_black_pearl', 'Perle noire', 'Perle', 'dribble', 'Un dribble inspiré et plein de grâce.')],
            'yassine-bounou' => [$m('bounou_wall', 'Mur de Yassine', 'Mur', 'hand_save', 'Un arrêt réflexe qui sauve le résultat.')],
            'sofyan-amrabat' => [$m('amrabat_tackle', 'Récupération de Sofyan', 'Récupération', 'tackle', 'Il court, il gratte, il rend le ballon propre.')],
            'youssef-en-nesyri' => [$m('en_nesyri_aerial', 'Détente d’Atlas', 'Détente', 'shot', 'Un saut puissant, une tête puissante, un but.')],

            // ───────── Asie & Amériques ─────────
            'hidetoshi-nakata' => [$m('nakata_through_pass', 'Passe de Hide', 'Passe', 'pass', 'Une passe tendue et rapide qui ouvre la défense.')],
            'bum-kun-cha' => [$m('cha_power', 'Frappe de Cha', 'Frappe', 'shot', 'Une frappe tendue du pied droit, très puissante.')],
            'heung-min-son' => [$m('son_curler', 'Enroulé de Sonny', 'Enroulé', 'shot', 'Une frappe enroulée du pied droit comme du gauche, au second poteau.')],
            'ji-sung-park' => [$m('park_three_lungs', 'Trois poumons', 'Poumons', 'tackle', 'Un pressing perpétuel qui épuise l’adversaire.')],
            'myung-bo-hong' => [$m('hong_libero', 'Libéro de Séoul', 'Libéro', 'intercept', 'Il lit le jeu et nettoie les ballons dans son dos.')],
            'saeed-al-owairan' => [$m('owairan_solo', 'Solo légendaire', 'Solo', 'dribble', 'Il traverse tout le terrain, dribblant adversaire après adversaire.')],
            'tim-howard' => [$m('howard_wall', 'Mur de Tim', 'Mur', 'hand_save', 'Un gardien aux réflexes affûtés, qui détourne les tirs.')],
            'rene-higuita' => [$m('higuita_scorpion', 'Scorpion', 'Scorpion', 'hand_save', 'Un dégagement acrobatique, les deux pieds en arrière, qui arrête le ballon.')],
            'carlos-valderrama' => [$m('valderrama_pibe', 'Passe d’El Pibe', 'Pibe', 'pass', 'Un tempo lent, une passe parfaitement dosée.')],
            'faustino-asprilla' => [$m('asprilla_dribble', 'Dribble de Tino', 'Dribble', 'dribble', 'Un dribble imprévisible qui coupe la défense en deux.')],
            'radamel-falcao' => [$m('falcao_tiger', 'Tigre', 'Tigre', 'shot', 'Un saut de tigre, une tête puissante au point de penalty.')],
            'james-rodriguez' => [$m('james_volley', 'Volée de James', 'Volée', 'shot', 'Une volée du pied gauche, sur un ballon qui tombe, d’une pureté rare.')],
            'luis-diaz' => [$m('diaz_sprint', 'Sprint de Lucho', 'Sprint', 'dribble', 'Une accélération électrique, ballon au pied, côté gauche.')],

            // ───────── Belgique ─────────
            'kevin-de-bruyne' => [
                $m('de_bruyne_laser', 'Passe laser', 'Laser', 'pass', 'Une passe tendue et rapide qui perce toutes les lignes.'),
                $m('de_bruyne_curler', 'Enroulé de Kevin', 'Enroulé', 'shot', 'Une frappe enroulée du pied droit, dans la lucarne.'),
            ],
            'eden-hazard' => [$m('hazard_dribble', 'Dribble d’Eden', 'Dribble', 'dribble', 'Un changement de direction soudain qui fait basculer le défenseur.')],
            'thibaut-courtois' => [$m('courtois_wall', 'Mur de Thibaut', 'Mur', 'hand_save', 'Très grand, il couvre tout son but et capte tous les tirs.')],
            'vincent-kompany' => [$m('kompany_captain', 'Capitaine de fer', 'Capitaine', 'block', 'Il se jette devant le tir, sans jamais hésiter.')],
            'enzo-scifo' => [$m('scifo_genius', 'Génie d’Anderlecht', 'Génie', 'pass', 'Une passe magnifique, créative, qui libère l’attaque.')],
            'jan-ceulemans' => [$m('ceulemans_head', 'Tête de Jan', 'Tête', 'shot', 'Un coup de tête puissant sur un centre tendu.')],
            'romelu-lukaku' => [$m('lukaku_power', 'Force de la nature', 'Force', 'shot', 'Un corps-à-corps gagné, puis une frappe puissante.')],
            'michel-preudhomme' => [$m('preud_homme_wall', 'Mur de Michel', 'Mur', 'hand_save', 'Un gardien placé à la perfection, qui détourne les tirs les plus durs.')],
            'jeremy-doku' => [$m('doku_sprint', 'Sprint de Jérémy', 'Sprint', 'dribble', 'Une pointe de vitesse qui prend de court n’importe quel latéral.')],
            'youri-tielemans' => [$m('tielemans_long_shot', 'Frappe de Youri', 'Frappe', 'shot', 'Une frappe lointaine, tendue, vers la lucarne.')],
        ];
    }
}
