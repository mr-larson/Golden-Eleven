<?php

namespace Database\Seeders\Players\CaptainTsubasa\Clubs;

use App\Enums\TeamStyle;

/**
 * Clubs de la période « Professionnel » : un nouveau catalogue d'équipes et de
 * effectifs qui réutilise les MÊMES joueurs que le Collège (table `players`),
 * mais répartis autrement. Lu par ProClubSeeder (équipes, contrats, capitaines).
 *
 * 20 clubs historiques (Europe et Amérique du Sud). Chaque joueur n'a qu'UN contrat par
 * période : il est attribué au club le plus emblématique de sa carrière ; les places restantes
 * sont complétées par des joueurs libres de nationalité proche, en prenant les mieux notés.
 *
 * Format de `teams()` : [nom, description, style tactique, philosophie, formation].
 * Format de `players()` : nom du club => titulaires (11) puis remplaçants (3),
 * en « Prénom Nom » comme dans le catalogue. `captains()` : nom du club => capitaine.
 * Le nom d'un club pro doit être différent de tous les noms de clubs du Collège
 * (la colonne `teams.name` est unique).
 */
final class ProClubs
{
    /** @return array<int, array{0: string, 1: string, 2: string, 3: string, 4: string}> */
    public static function teams(): array
    {
        return [
            ['Real Madrid', 'Les Galactiques : un onze de stars, intensité offensive.',
                TeamStyle::TACTICAL_OFFENSIVE, TeamStyle::PHILOSOPHY_STARS, '4-2-2-2'],
            ['FC Barcelone', 'Jeu de possession et école de la passe, un collectif de génies.',
                TeamStyle::TACTICAL_POSSESSION, TeamStyle::PHILOSOPHY_COLLECTIVE, '4-2-1-3'],
            ['Paris Saint-Germain', 'Un onze offensif bâti autour de stars mondiales.',
                TeamStyle::TACTICAL_OFFENSIVE, TeamStyle::PHILOSOPHY_STARS, '4-2-1-3'],
            ['Juventus', 'Solidité défensive et contre-attaque, la Vieille Dame.',
                TeamStyle::TACTICAL_COUNTER, TeamStyle::PHILOSOPHY_BALANCED, '4-3-1-2'],
            ['AC Milan', 'Équilibre et élégance, un collectif de légendes.',
                TeamStyle::TACTICAL_BALANCED, TeamStyle::PHILOSOPHY_COLLECTIVE, '4-3-1-2'],
            ['Inter Milan', 'Bloc compact et transitions rapides.',
                TeamStyle::TACTICAL_COUNTER, TeamStyle::PHILOSOPHY_BALANCED, '4-3-1-2'],
            ['Bayern Munich', 'Puissance, rigueur et efficacité devant le but.',
                TeamStyle::TACTICAL_OFFENSIVE, TeamStyle::PHILOSOPHY_STARS, '4-2-1-3'],
            ['Ajax', 'Le football total : mouvement, possession et formation.',
                TeamStyle::TACTICAL_POSSESSION, TeamStyle::PHILOSOPHY_COLLECTIVE, '4-2-1-3'],
            ['Manchester United', 'Football offensif et mental de vainqueur.',
                TeamStyle::TACTICAL_OFFENSIVE, TeamStyle::PHILOSOPHY_BALANCED, '4-2-2-2'],
            ['Liverpool', 'Pressing intense et ferveur d\'Anfield.',
                TeamStyle::TACTICAL_OFFENSIVE, TeamStyle::PHILOSOPHY_COLLECTIVE, '4-2-1-3'],
            ['SL Benfica', 'Jeu de possession et talents portugais.',
                TeamStyle::TACTICAL_POSSESSION, TeamStyle::PHILOSOPHY_ECONOMIST, '4-2-2-2'],
            ['Arsenal', 'Jeu de passes léger et technique.',
                TeamStyle::TACTICAL_POSSESSION, TeamStyle::PHILOSOPHY_BALANCED, '4-2-2-2'],
            ['Chelsea', 'Bloc solide et contres tranchants.',
                TeamStyle::TACTICAL_COUNTER, TeamStyle::PHILOSOPHY_STARS, '4-2-2-2'],
            ['Santos', 'La maison de Pelé : dribbles, flair et attaque.',
                TeamStyle::TACTICAL_OFFENSIVE, TeamStyle::PHILOSOPHY_STARS, '4-2-1-3'],
            ['São Paulo', 'Collectif brésilien discipliné, terre d\'accueil de Tsubasa.',
                TeamStyle::TACTICAL_BALANCED, TeamStyle::PHILOSOPHY_COLLECTIVE, '4-2-2-2'],
            ['Boca Juniors', 'Combativité et caractère, la Bombonera en feu.',
                TeamStyle::TACTICAL_COUNTER, TeamStyle::PHILOSOPHY_COLLECTIVE, '4-2-2-2'],
            ['River Plate', 'Jeu posé et technique du Río de la Plata.',
                TeamStyle::TACTICAL_POSSESSION, TeamStyle::PHILOSOPHY_BALANCED, '4-3-1-2'],
            ['Manchester City', 'Possession totale et domination du jeu.',
                TeamStyle::TACTICAL_POSSESSION, TeamStyle::PHILOSOPHY_STARS, '4-2-1-3'],
            ['Atlético de Madrid', 'Bloc bas, intensité et mentalité de guerrier.',
                TeamStyle::TACTICAL_DEFENSIVE, TeamStyle::PHILOSOPHY_ECONOMIST, '5-3-1-1'],
            ['RSC Anderlecht', 'Le grand club belge, vivier de talents.',
                TeamStyle::TACTICAL_BALANCED, TeamStyle::PHILOSOPHY_ECONOMIST, '4-3-1-2'],
            ['Esperion', 'Le club formateur d\'Ao Ashi : jeu technique, lecture tactique et jeunes talents.',
                TeamStyle::TACTICAL_POSSESSION, TeamStyle::PHILOSOPHY_COLLECTIVE, '4-3-3'],
        ];
    }

    /** @return array<string, array<int, string>> */
    public static function players(): array
    {
        return [
            'Real Madrid' => [
                // Titulaires (11)
                'Iker Casillas', 'Raphaël Varane', 'Fernando Hierro', 'Roberto Carlos', 'Robert Kovač', 'Zinédine Zidane', 'Luka Modrić', 'Jude Bellingham', 'Xabi Alonso', 'Cristiano Ronaldo', 'Gareth Bale',
                // Remplaçants (3)
                'Alfredo Di Stéfano', 'Raúl González', ' Casemiro',
            ],
            'FC Barcelone' => [
                // Titulaires (11)
                'Andoni Zubizarreta', 'Carles Puyol', 'Gerard Piqué', 'Ronald Koeman', 'Jordi Alba', ' Ronaldinho', 'Andrés Iniesta', 'Xavi Hernández', 'Lionel Messi', 'Lamine Yamal', 'Samuel Eto\'o',
                // Remplaçants (3)
                ' Pedri', 'Pep Guardiola', 'Sergio Busquets',
            ],
            'Paris Saint-Germain' => [
                // Titulaires (11)
                'Gianluigi Buffon', 'Sergio Ramos', 'Thiago Silva', 'Achraf Hakimi', 'Juan Pablo Sorín', 'Jay-Jay Okocha', 'Safet Sušić', 'Idrissa Gueye', 'Kylian Mbappé', 'Dominique Rocheteau', 'Zlatan Ibrahimović',
                // Remplaçants (3)
                'Edinson Cavani', ' Pauleta', 'Larbi Benbarek',
            ],
            'Juventus' => [
                // Titulaires (11)
                'Dino Zoff', 'Fabio Cannavaro', 'Gaetano Scirea', 'Lilian Thuram', 'Ciro Ferrara', 'Michel Platini', 'Pavel Nedvěd', 'Paul Pogba', 'Edgar Davids', 'Kojiro Hyuga', 'Alessandro Del Piero',
                // Remplaçants (3)
                'Paolo Rossi', 'David Trezeguet', 'Didier Deschamps',
            ],
            'AC Milan' => [
                // Titulaires (11)
                'Sebastiano Rossi', 'Alessandro Nesta', 'Franco Baresi', 'Paolo Maldini', 'Gianluca Zambrotta', ' Kaká', 'Andrea Pirlo', 'Zvonimir Boban', 'Clarence Seedorf', 'Marco van Basten', 'George Weah',
                // Remplaçants (3)
                'Dejan Savićević', 'Frank Rijkaard', 'Gennaro Gattuso',
            ],
            'Inter Milan' => [
                // Titulaires (11)
                'Angelo Peruzzi', 'Javier Zanetti', 'Cristian Chivu', 'Andreas Brehme', 'Giacinto Facchetti', 'Wesley Sneijder', 'Álvaro Recoba', 'Dejan Stanković', 'Gianni Rivera', ' Ronaldo', 'Jürgen Klinsmann',
                // Remplaçants (3)
                'Christian Vieri', 'Youri Djorkaeff', 'Siniša Mihajlović',
            ],
            'Bayern Munich' => [
                // Titulaires (11)
                'Manuel Neuer', 'Franz Beckenbauer', 'Philipp Lahm', 'Hermann Kaltz', ' Lúcio', 'Lothar Matthäus', 'Bastian Schweinsteiger', 'Michael Ballack', 'Karl-Heinz Schneider', 'Arjen Robben', 'Gerd Müller',
                // Remplaçants (3)
                'Miroslav Klose', 'Toni Kroos', 'Paul Breitner',
            ],
            'Ajax' => [
                // Titulaires (11)
                'Edwin van der Sar', 'Frank de Boer', 'Philippe Cocu', 'Zdeněk Grygera', 'Michael Reiziger', 'Jari Litmanen', 'Ronald de Boer', 'Johan Neeskens', 'Johan Cruyff', 'Marc Overmars', 'Patrick Kluivert',
                // Remplaçants (3)
                'John Bosman', 'Mark van Bommel', 'Brian Cruyfford',
            ],
            'Manchester United' => [
                // Titulaires (11)
                'Peter Schmeichel', 'Jaap Stam', 'Nemanja Vidić', 'Rio Ferdinand', 'Gary Neville', 'Bobby Charlton', 'Ryan Giggs', 'David Beckham', 'Paul Scholes', 'Wayne Rooney', 'Ole Gunnar Solskjær',
                // Remplaçants (3)
                'Andy Cole', 'Roy Keane', 'Denis Irwin',
            ],
            'Liverpool' => [
                // Titulaires (11)
                ' Alisson', 'Virgil van Dijk', 'Daniel Agger', 'John Arne Riise', 'Steve Staunton', 'Steven Gerrard', 'Javier Mascherano', 'Joe Allen', 'Sadio Mané', 'Luis Suárez', 'Michael Owen',
                // Remplaçants (3)
                'Luis Díaz', 'Ian Rush', 'Dirk Kuyt',
            ],
            'SL Benfica' => [
                // Titulaires (11)
                'Michel Preud\'homme', 'Jan Vertonghen', 'Ricardo Carvalho', 'João Pinto', 'Fábio Coentrão', 'Luís Figo', 'Rui Costa', 'Sérgio Conceição', 'Mário Coluna', ' Eusébio', ' Nani',
                // Remplaçants (3)
                'Ángel Di María', 'José Cardozo', 'João Moutinho',
            ],
            'Arsenal' => [
                // Titulaires (11)
                'Emiliano Martínez', 'Ashley Cole', 'Thomas Vermaelen', 'Giovanni van Bronckhorst', 'Oleksandr Zinchenko', 'Fredrik Ljungberg', 'Robert Pirès', 'Cesc Fàbregas', 'Patrick Vieira', 'Thierry Henry', 'Robin van Persie',
                // Remplaçants (3)
                'Dennis Bergkamp', 'Aaron Ramsey', 'Emmanuel Petit',
            ],
            'Chelsea' => [
                // Titulaires (11)
                'Petr Čech', 'Kalidou Koulibaly', 'Marcel Desailly', 'John Terry', 'Dan Petrescu', 'Ruud Gullit', ' Deco', 'Frank Lampard', 'Claude Makélélé', 'Eden Hazard', 'Mark Hughes',
                // Remplaçants (3)
                'John Obi Mikel', 'Louis Napoleon', 'Victor Osimhen',
            ],
            'Santos' => [
                // Titulaires (11)
                'Cláudio Taffarel', ' Alberto', ' Casa Grande', ' Dante', 'Carlos Alberto Torres', ' Natureza', ' Didi', 'Carlos Dunga', ' Pelé', ' Neymar', ' Garrincha',
                // Remplaçants (3)
                ' Leônidas', ' Vavá', 'Vinícius Júnior',
            ],
            'São Paulo' => [
                // Titulaires (11)
                ' Salinas', 'Ramon Galvan', 'Marcos Cafu', ' Giorgi', ' Senardo', ' Rivaldo', ' Zico', ' Sócrates', ' Branco', 'Carlos Santana', ' Romário',
                // Remplaçants (3)
                'Tsubasa Ozora', ' Jairzinho', ' Bebeto',
            ],
            'Boca Juniors' => [
                // Titulaires (11)
                'Nery Pumpido', 'Roberto Ayala', 'Oscar Ruggeri', 'Celso Ayala', 'Francisco Arce', 'Diego Maradona', 'Juan Román Riquelme', 'Jorge Burruchaga', 'Ricardo Giusti', 'Claudio Caniggia', 'Gabriel Batistuta',
                // Remplaçants (3)
                'Martín Palermo', 'Nicolas Frutos', 'Juan Diaz',
            ],
            'River Plate' => [
                // Titulaires (11)
                'Ubaldo Fillol', 'José Nasazzi', 'Diego Lugano', 'Daniel Passarella', 'Paolo Montero', 'Enzo Francescoli', 'Diego Pérez', 'Egidio Arévalo Ríos', 'Obdulio Varela', 'Juan Alberto Schiaffino', 'Alcides Ghiggia',
                // Remplaçants (3)
                'Mario Kempes', 'Jorge Valdano', 'Ryoma Hino',
            ],
            'Manchester City' => [
                // Titulaires (11)
                'Kasper Schmeichel', 'Vincent Kompany', 'Joško Gvardiol', 'Rúben Dias', 'Ben Davies', 'Kevin De Bruyne', 'Bernardo Silva', ' Rodri', 'Sergio Agüero', 'Erling Haaland', 'Jérémy Doku',
                // Remplaçants (3)
                'Joe Ledley', 'François Van der Elst', 'Davor Šuker',
            ],
            'Atlético de Madrid' => [
                // Titulaires (11)
                'Thibaut Courtois', 'Diego Godín', 'Iván Córdoba', 'Laurent Blanc', 'Mario Yepes', 'Andrés Escobar', 'Antoine Griezmann', 'Diego Simeone', 'El Sid Pierre', 'Federico Valverde', 'Hugo Sánchez',
                // Remplaçants (3)
                'Paulo Futre', 'Diego Forlán', 'Radamel Falcao',
            ],
            'RSC Anderlecht' => [
                // Titulaires (11)
                'Filip De Wilde', 'Georges Grün', 'Philippe Albert', 'Glen De Boeck', 'Régis Genaux', 'Enzo Scifo', 'Marc Degryse', 'Pär Zetterberg', 'Alin Stoica', 'Luc Nilis', 'Romelu Lukaku',
                // Remplaçants (3)
                'Johan Walem', 'Lucas Biglia', 'Olivier Deschacht',
            ],
            'Esperion' => [
                // Titulaires (11)
                'Enshin Akiyama', 'Ashito Aoi', 'Nagisa Akutsu', 'Keiji Togashi', 'Yutaka Yamada',
                'Eisaku Ohtomo', 'Yoichi Kiriki', 'Haruhisa Kuribayashi',
                'Eita Takasugi', 'Kenta Yoshitsune', 'Yuma Motoki',
                // Remplaçants (4)
                'Kanji Nikaido', 'Kyo Shimura', 'Jun Asari', 'Soichiro Tachibana',
            ],
        ];
    }

    /** @return array<string, string> */
    public static function captains(): array
    {
        return [
            'Real Madrid' => 'Raúl González',
            'FC Barcelone' => 'Carles Puyol',
            'Paris Saint-Germain' => 'Thiago Silva',
            'Juventus' => 'Alessandro Del Piero',
            'AC Milan' => 'Paolo Maldini',
            'Inter Milan' => 'Javier Zanetti',
            'Bayern Munich' => 'Franz Beckenbauer',
            'Ajax' => 'Johan Cruyff',
            'Manchester United' => 'Roy Keane',
            'Liverpool' => 'Steven Gerrard',
            'SL Benfica' => 'Rui Costa',
            'Arsenal' => 'Thierry Henry',
            'Chelsea' => 'John Terry',
            'Santos' => ' Pelé',
            'São Paulo' => 'Tsubasa Ozora',
            'Boca Juniors' => 'Diego Maradona',
            'River Plate' => 'Daniel Passarella',
            'Manchester City' => 'Kevin De Bruyne',
            'Atlético de Madrid' => 'Diego Simeone',
            'RSC Anderlecht' => 'Enzo Scifo',
            'Esperion' => 'Kenta Yoshitsune',
        ];
    }
}
