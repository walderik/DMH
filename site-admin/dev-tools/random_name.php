<?php
class RandomName {
    
    const GROUPS = array("The Comrades of the Red Protectors", "The Howling Boar Kindred", "The Glistening Revellers", "Cabal of Banditsfleet", "The Party of the Smoky Duck", "Seven from Heaven", "Chaotic Brew League", "The Mystic Spy Guild", "The Delvers", "The Black Mark Sages", "Fallen Ant Scions", "The Brazen Unicorn Shields", "Molaragina's Cliffs Protectors", "Bright Order of the Dusk", "The Bright Servants of Fairreef", "Black Jesters", "Kindred of Castershaw", "Knights of the Armored Pig", "Pat's Flagons", "Bright Posts Servants", "Society of the Jug", "Long Fowl Society", "The Whispering Striders", "The Winter Shrine Guardians", "The Ruins of Feng's Hill Five", "Itchy Genocide", "Macabre Vengeance", "Mystic Devils", "Hunters of the Bitter", "Flash of the Tainted", "Tempest of the Immoral", "Victims of the Night", "Doomflayers", "Swiftflags", "Bellowbow", "Hellfire Tyranny", "Sacred Vitality", "Diseased Enemy", "Tranquillity of the Obscure", "Myth of the Hallowed", "Wizards of the Noble", "Immortals of the Serene", "Darkstand", "Ravencloaks", "Bellowshapers", "Grubby Vultures", "Grim Criminals", "Promised Exiles", "Residents of Strength", "Recruits of Light", "Myth of Seduction", "Ghosts of the Passive", "Rumblecry", "Eboncloaks", "Ebonsins", "Eternal Grave", "Deceived Titans", "Hopeful Dust", "House of the Holy", "Immortals of the Wrong", "Bandits of Impurity", "Whispers of the Moon", "Fallenshroud", "Burningfury", "Ebonlanders", "Holy Genesis", "Eternal Blow", "Tyranny Illusions", "Birth of Oceans", "Rebellion of the Lost Age", "Boon of the Withered", "Whispers of the Dog", "Whitevale", "Lightningclouds", "Earthfall", "Rotten Hooligans", "Joyful Oblivion", "Resolute Force", "Relics of Corruption", "Struggle of Rejects", "Widows of the Weasel", "Witches of the Revenant", "Redbeards", "Thundercloaks", "Bannerflags", "Elite Immortals", "Faded Vultures", "Hallowed War", "Crushers of Might", "Minds of the Lost Age", "Plague of the Bitter", "Prophecies of Trust", "Splitroses", "Monsterbow", "Thunderbeards", "Wasted Champions", "Collapsed Berserkers", "Deserted Embers", "Screams of the Eagle", "Might of the Sacrificed", "Tears of the Devoted", "Treasure of the Righteous", "Snowshade", "Stonegarde", "Steelbow", "Jackal Gang", "Demon Demon Posse", "Boar Brotherhood", "Sapphire Dragontooth Squad", "Fire Butterfly Squad", "Leopard Company");

    const GIRLS = array("Lisbeth", "Maggan", "Doris", "Misley", "Dagmar", "Annette", "Alicia", "Hokey", "Lise", "Lotte", "Magdalena", "Johanna", "Pepsi", "Ultima", "Regalia", "Pippi", "Manilla", "Georgia", "Ådel", "Amalfrieda", "Ada", "Millan", "Hjördis",
        "Alessandra", "Alex", "Alexandra", "Alexa", "Cassandra", "Desdemona", "Emmanuelle", "Gwendolyn", "Mirabella", "Philomena", "Loise", "Noppa", "Norpa", "Alberta", "Adalberta", "Albrun", "Esmeralda", "Vilhelmina", "Klarabella",
        "Harrietta", "Constantia", "Jacqueline", "Waqi", "Shunnareh", "Sarsoureh", "Rigel", "Rima", "Laila", "Solstråle", "Månstråle", "Elderberry", "Sunflower", "Asta", "Gabriella", "Carin", "Erika", "Georgina", "Freja", "Gry", "Agneta",
        "Margareta", "Rey", "Kay", "Kaj", "Kajsa", "Kritan", "Ellinor", "Paris", "Delphine", "Eleonor", "Andrea", "Angela", "Angelika", "Adolfa", "Antoinette", "Hanna", "Gunvor", "Zulema", "Carina", "Karina", "Anna", "Karin", "Emelie", "Ylva",
        "Ettan", "Nettan", "Alice", "Lisen", "Kim", "Katarina", "Naila", "Adolfine", "Anita", "Adriane", "Anna", "Anne", "Antje", "Agatha", "Antonia", "Elin", "Ellen", "Anna", "Ada", "Adela", "Bella", "Adina", "Adriana", "Agnes", "Matilda", "Magda",
        "Sanna", "Beata", "Morrigan", "Morgana", "Beate", "Beate", "Beatrix", "Beatrice", "Brigitte", "Birgitta", "Charlotte", "Barbara", "Christiane", "Jackie", "Angelina", "Ange", "Camilla", "Kamilla", "Larissa", "Olga", "Agnes", "Juni",
        "Alice", "Leah", "Dora", "Dorothea", "Elke", "Elisabeth", "Else", "Emma", "Edda", "Erna", "Eva", "Frida", "Fausta", "Fabiola", "Felicitas", "Barbro", "Jordan", "Alicia", "Leia", "Alma", "Lilly", "Alva", "Linnea", "Amanda", "Liv", "Amelia", "Livia",
        "Astrid", "Li", "My", "Ayla", "Lova", "Belle", "Bianca", "Lovisa", "Bonnie", "Luna", "Lykke", "Beatrice", "Csaba", "Arild", "Lisa", "Jojjo", "Lisa", "Annika", "Felicia", "Martina", "Ulla", "Elvira", "Josefine", "Frauke", "Gaby", "Gabriele", "Fieke",
        "Geraldine", "Gerda", "Gerta", "Gertraud", "Gisa", "Gisberta", "Gisela", "Gitte", "Hedwig", "Hedda", "Waldhild", "Waltrada", "Wendelgard", "Wanda", "Nyamko", "Linda", "Elin", "Maj", "Lis", "Mona", "Gunborg", "Kristina", "Fia", "Adelina",
        "Nadenka", "Nadezhda", "Alena", "Alieta", "Nadja", "Nadyenka", "Alla", "Nasta", "Alyona", "Nastasia", "Anastasia", "Natasha", "Nelli", "Annushka", "Nikita", "Anya", "Nina", "Kicki", "Arina", "Oksana", "Olga", "Belka", "Panya",
        "Polina", "Bojana", "Christina", "Raisa", "Daria", "Roza", "Diana", "Sabina", "Dinara", "Dominika", "Saschenka", "Sasha", "Duschinka", "Dusica", "Slava", "Ekaterina", "Sofia", "Elena", "Eleonora", "Sonja", "Esfir", "Sonya", "Stefaniya",
        "Evelina", "Svea", "Sveta", "Svetlana", "Feodora", "Taisia", "Gala", "Galina", "Tamara", "Grusha", "Tanya", "Inessa", "Inga", "Irina", "Ivanna", "Tatiana", "Jelena", "Jelina", "Vanja", "Karina", "Vanka", "Katerina", "Vanna", "Katia", "Katina",
        "Katinka", "Venera", "Venus", "Kenya", "Vera", "Khrystyna", "Kira", "Veronika", "Klara", "Victoria", "Ksenia", "Violetta", "Violet", "Lada", "Lara", "Viveca", "Larisa", "Lelyah", "Vor", "Lena", "Lidia", "Yana", "Lilia", "Yarina", "Asia",
        "Europa", "Yasemin", "Lizabeta", "Yasmina", "Yekaterina", "Luba", "Lucya", "Lucy", "Gabriel", "Ludmila", "Manya", "Margarita", "Yuriko", "Maria", "Zasha", "Marianna", "Marina", "Zia", "Marisha", "Marta", "Masha", "Matrena", "Ester",
        "Elsi", "Elsy", "Oceania", "Frigida", "Gotica", "Venus", "Hestia", "Hera", "Birgitta", "Titania", "Ida", "Idun", "Hel", "Cornelia", "Aurora", "Rut", "Korea", "Nefretiti", "Salome", "Afrodite", "Athena", "Beirut", "Persefone", "Angelica",
        "Malin", "Ingeborg", "Ingemo", "Murael", "Duni", "Lunabelle", "Zimal", "Irha", "Mileya", "Kayan", "Kabher", "Hoorain", "Linelle", "Miraal", "Mistral", "Abrish", "Rittal", "Amaira", "Eleysa", "Mileah", "Raitl", "Aava", "Almaas", "Celina",
        "Elara", "Liyana", "Nyra", "Watin", "Heylin", "Maiza", "Ember", "Amber", "Advika", "Daneen", "Melaher", "Wateen", "Anabia", "Aylina", "Ruftalem", "Cinemon", "Chimamanda", "Amada", "Melael", "Aarna", "Eliora", "Eminella", "Laren",
        "Loelle", "Lovelia", "Aleyah", "Inaaya", "Mineah", "Retal", "Lovi", "Jovi", "Cataleya", "Lamia", "Larissa", "Elsa", "Lina", "Maia", "Ambrosia", "Melissa", "Andromeda", "Ariadne", "Apollonia", "Myrra", "Artemis", "Astria", "Astra", "Asta",
        "Nemesis", "Atalanta", "Nike", "Atena", "Pandora", "Panope", "Paris", "Penelope", "Persefone", "Dione", "Therese", "Abriana", "Bambi", "Bianca", "Caprice", "Cara", "Carin", "Carlotta", "Cettina", "Contessa", "Domnina", "Donatella",
        "Fabiana", "Fiorella", "Fiorenza", "Geatana", "Gioia", "Giordana", "Giovanna", "Graziella", "Ilaria", "Italia", "Svea", "Justina", "Lanza", "Lave", "Liona", "Luca", "Lucia", "Luciana", "Mariabella", "Marietta", "Marsala", "Mia",
        "Ella", "Michelle", "Mila", "Natalia", "Ornella", "Prima", "Primavera", "Quorra", "Racarda", "Romana", "Ruffina", "Sidonia", "Sienna", "Sistine", "Speranza", "Tessa", "Trilby", "Sizzy", "Zissy", "Mamma", "Desiré");
    
    
    
    const BOYS = array("Muller", "Tomas", "Hinter", "Benjamin", "Antony", "Nestor", "Kelly", "Sven", "Daniel", "Swensk", "Joakim", "Jocke", "Timmy",
        "Åke", "Örjan", "Charles", "Volodymyr", "Petro", "Ihor", "Ior", "Oleksandr", "Mykola", "Oleksij", "Shmyhal", "Edmundur", "Art", "Adamsker",
        "Pöcke", "Alessandro", "Clementine", "Fredrik", "Fredrico", "Alex", "Alexander", "Alexsandro", "Maximilianus", "Max", "Maximus", "Maximisimus",
        "Hugo", "Dale", "Manos", "Albrun", "Adel", "Rashaad", "Ibrahim", "Linus", "Hassan", "Frederico", "Tomas", "Thomas", "Tomaso", "Figuero",
        "Ramon", "Caron", "Carolus", "Tony", "Tim", "Barth", "Virus", "Noppe", "Abbo", "Abo", "Alberich", "Elberich", "Agilbert", "Dolf", "Clemens",
        "Gabriel", "Erik", "Göran", "George", "Juno", "Douglas", "Ståle", "Jens", "Hjorvard", "Frode", "Roy", "Kay", "Kaj", "Kritan", "Pelle",
        "Pellinor", "Johan", "Morgan", "Amadeus", "Amadeo", "Armin", "Arnold", "Berthold", "Balduin", "Sigard", "Harabanar", "Eric", "Gunnar",
        "Viggo", "Jörgen", "Bartholomew", "Beauregard", "Montgomery", "Norik", "Ettan", "Daniel", "Jim", "Mats", "Kjell", "Simon", "Magnus", "Kim",
        "Botho", "Benno", "Chlodwig", "Clemens", "Dagobert", "Hartman", "Harkilar", "Pelle", "Anton", "Sune", "Peter", "Hannes", "Morgan", "Jon",
        "John", "Fredrik", "Adrian", "Göran", "Björn", "Ben", "Parsi", "Klas", "Storebror", "Börje", "Carl", "Calle", "Kalle", "Peder", "Tor",
        "Tore", "Ture", "Emil", "Erhard", "Lance", "Lancet", "Dag", "Jackie", "Ange", "Ola", "Olle", "Olov", "Olof", "Håkan", "Andreas", "Anders",
        "Alov", "Aleg", "Varg", "Wolf", "Kristian", "Christian", "Felix", "Fingal", "Johan", "Erwin", "Fabian", "Fabius", "Falco", "Fleke", "Lo",
        "Mo", "Lovis", "Lykke", "Jan", "Steffen", "Isak", "Graf", "Ultimor", "Ferdinand", "Freddy", "Florianus", "Friedrich", "Gandolf", "Georg",
        "Georg", "Georg", "Georg", "Georg", "Engelbrecht", "Eduard", "Christoph", "Azdin", "Bror", "Vilde", "Neo", "Bryan", "Per", "Pär", "Åke",
        "Lars", "Ove", "Kalle", "Michel", "Morris", "Wa", "Tobias", "Justin", "Gerwig", "Giselbert", "Godehard", "Gottfried", "Gottlieb", "Götz",
        "Hansdieter", "Hans", "Dieter", "Hartmann", "Hartmut", "Heiko", "Hasso", "Hasse", "Harald", "Hauke", "Walbert", "Weikhard", "Walderik",
        "Walpurgis", "Welfhard", "Waldebert", "Wendelbert", "Waldemar", "Woldemar", "Walfried", "Wastl", "Werner", "Wedekind", "Septimus",
        "Wernfried", "Butcha", "Sabuni", "Flash", "Fabian", "Zane", "Algernon", "Fergus", "Tobias", "Buck", "Rufus", "Fergus", "Hök", "Devin",
        "Kevin", "Dregen", "Rock", "Marlcolm", "Igor", "Derek", "Dagge", "Dusty", "Emanuel", "Ingemark", "Zohan", "Tiar", "Falke", "Falkon",
        "Avyaan", "Ivaan", "Ivan", "Kayan", "Kiaan", "Jaxx", "Ahil", "Kabiel", "Priam", "Rajan", "Rejjan", "Kylo", "Lijon", "Lejon", "Aylan",
        "Floke", "Folke", "Yuvaan", "Makbel", "Villot", "Vilgon", "Vilmer", "Albus", "Helix", "Kaon", "Mucad", "Mazon", "Yoab", "Agastya", "Ayansh",
        "Rudransh", "Roger", "Robert", "Viaan", "Cassian", "Kion", "Ellian", "Sarim", "Samarin", "Sebion", "Troi", "Zayn", "Divit", "Jaxon",
        "Kylian", "Milas", "Midas", "Kiian", "Nelion", "Aadvik", "Arvid", "Knox", "Rohaan", "Easton", "Gastgon", "Gillion", "Gilliam", "Lias", "Bob",
        "Bobbo", "Sture", "Agato", "Amedeo", "Ilya", "Nikita", "Goergi", "Amerigo", "Aretino", "Arrigo", "Attilio", "Benvenuto", "Biondello",
        "Borachio", "Braulio", "Broinze", "Cajetan", "Carmelo", "Carmine", "Celesto", "Cirrillo", "Corrado", "Demarco", "Donato", "Donus",
        "Eriberto", "Ermanno", "Ettore", "Falito", "Fiorello", "Flavio", "Floritzel", "Fortino", "Galileo", "Genovese", "Giancarlo", "Gianni",
        "Gino", "Giovanni", "Honorius", "Hormisdad", "Hortensio", "Indro", "Lombardi", "Marco", "Mariano", "Martino", "Massimo", "Maurizio",
        "Mercury", "Messala", "Michelangelo", "Napoleon", "Nek", "Nino", "Nuncio", "Othello", "Paco", "Pancrazio", "Paolo", "Paris", "Philario",
        "Pino", "Pisano", "Primo", "Primus", "Rocco", "Proculeius", "Romeo", "Ruggerio", "Santo", "Santa", "Saverio", "Silvano", "Solanio", "Taddeo",
        "Tancredo", "Ugo", "Uno", "Umberto", "Venezio", "Venturo", "Vesuvio", "Vitalian", "Vittorio", "Zanebono", "Zanipolo", "Frank", "Molgan",
        "Kasper", "Kaspian", "Shi", "Ji", "Bo", "Ted", "Tod", "Teodor", "Papa");
    
    
    const LASTNAMES = array("Andersson", "Libby", "Hjort", "Dimma", "Tillberg", "Nerdy", "Emmenthalerberg", "Lemmon", "Shiny", "Frost", "Zimmerman",
        "Überblücker", "Wassermitter", "Mitterfeller", "Hemmeldemmel", "Materhorn", "Johansson", "Roos", "Röse", "Müller", "Topper", "Pahlo",
        "Andersén", "Andrén", "Blackhouse", "Österström", "Achté", "Bräutigam", "Delbrück", "Flügge", "Çelik", "Şahin", "Wróblewski", "Gonçalves",
        "Oliveira", "Hjort", "Lillebror", "Storebror", "Brorsson", "Kemppainen", "Aronsson", "Kjellson", "Persson", "Iklódi", "Svensson", "Järndahl",
        "Erixon", "Aallosvaara", "Kristersson", "Borgholm", "Fiskarholm", "Rappe", "Glansberg", "Snaberg", "Holmberg", "Hallonbacka", "Ängstrand",
        "Västerskog", "Kummelberg", "Danylyuk", "Azarov", "Paduan", "Danielsson", "Änglamark", "Gustavsson", "Bertilsson", "Denkert", "Stekvek",
        "Makron", "Svensson", "Delorean", "Hoppe", "Grevo", "Sigbrandt", "Lycke", "Rappe", "Mulshine", "Roos", "Lööf", "Andersson", "Quirk",
        "Edström", "Hontjaruk", "Nobel", "Egers", "Ahvenuslampi", "Alangonmäki", "Hangasvuori", "Antesson", "Svensson", "Johansson", "Pettersson",
        "Nilsson", "Nilsdotter", "Tryggvesson", "Jonsson", "Dahlström", "Karlsson", "Lunström", "Wallmo", "Lantz", "Peres", "Florin", "Kopek",
        "Thelberg", "Petersson", "Nordström", "Sandin", "Runström", "Backström", "Bäckström", "Lindström", "Hult", "Hultkvist", "Lindahl", "Rundahl",
        "Kindberg", "Nyberg", "Nyström", "Nydal", "Rydberg", "Denys", "Ölmunger", "Lerden", "Nordal", "Nordberg", "Kindahl", "Malm", "Modin",
        "Norberg", "Zelenskyj", "Sjmyhal", "Misley", "Barkley", "Putin", "Akari", "Olovsson", "Olofsson", "Olofsen", "Jensen", "Jepson", "Jephson",
        "Jepsen", "Jeppesen", "Porosjenko", "Gillek", "Söderberg", "Ambjörnsson", "Antonsson", "Haber", "Holm", "Alling", "Holmberg", "Hallgren",
        "Ben", "Benben", "Ragnerstam", "Jansson", "Hofling", "Videgård", "Eberhart", "Börjesson", "Thorell", "Carlsson", "Harding", "Mozard", "Bash",
        "Hayden", "Ferm", "Dehn", "Morgonstråle", "Dagg", "Rapace", "Hallon", "Hallin", "Kleerup", "Steinmaier", "Preppe", "Skugge", "Durström",
        "Edwardsson", "Nordegren", "Epstein", "Alling", "Storskär", "Arlov", "Wolf", "Åström", "Strömberg", "Bergström", "Åberg", "Ånglok",
        "Bergkvist", "Granat", "Fisk", "Fågel", "Gädda", "Borre", "Kryckel", "Eng", "Enkel", "Härnek", "Forslin", "Renklint", "Ekberg", "Härberg",
        "Renberg", "Nordberg", "Lindberg", "Nyberg", "Makron", "Veritas", "Standar", "Ritter", "Jäger", "Sackrisson", "Labba", "Isaksson", "Nilsen",
        "Klar", "Weng", "Kallbo", "Månsen", "Hedman", "Unemyr", "Graaf", "Hedin", "Hurdin", "Lagerman", "Lagerfält", "Lagerberg", "Lager",
        "Lagersjö", "Lagerblad", "Lagerdal", "Lagersen", "Lagersten", "Nelson", "Panther", "Tiger", "Ramone", "Glam", "Vikars", "Grossman",
        "Madsen", "Lagerström", "Bergsjö", "Camara", "Evander", "Vinblad", "Vingård", "Linné", "Houellebecqs", "Blomberg", "To", "Olsbu",
        "Röiseland", "Dudenbostel", "Preuss", "Sola", "Hinz", "Reztsova", "Cranston", "Sullican", "Remington", "Hedvall", "Knivile", "Zaphir",
        "Rubin", "Diamant", "Krutov", "Rastapopulus", "Myllymäki", "Ångstrom", "Hell", "Black", "Vokovera", "Tusen", "Granat", "Morgoth", "Moria",
        "Moldor", "Toth", "Thyk", "Kanker", "Totenkoph", "Quasar", "Sinclair", "Pemberton", "Arkwright", "Sundholm", "Mumin", "Åker", "Åkerberg",
        "Åkerbacka", "Åkerbacke", "Åkerskog", "Åkerman", "Åkervarg", "Åkerkvist", "Åkerquist", "Åkergård", "Åkerfält", "Åkerstam", "Åkerström",
        "Åkersjö", "Åkerguld", "Åkerek", "Åkersköld", "Biden", "Korell", "Jemtoff", "Silver", "Silverberg", "Silverbacka", "Silverbacke",
        "Silverkog", "Silverman", "Silvervarg", "Silverkvist", "SilverQuist", "Silvergård", "Silverfält", "Silverstam", "Silverström", "Silversjö",
        "Silverek", "Silversköld", "Öberg", "Öbacka", "Öman", "Ökvist", "Öquist", "Ögård", "Öfält", "Östam", "Öström", "Silverö", "Öholm", "Noren",
        "Fury", "Alabaster", "Quisling", "Kisinger", "Quark", "Kefi", "Nonjer", "Kleffner", "Makron", "Ouch", "Boxbon", "Beard", "Hicks", "Flowers",
        "Rosen", "Rosenberg", "Rosenbacka", "Rosenbacke", "Rosenskog", "Rosenvarg", "Rosenkvist", "Rosenquist", "Rosengård", "Moon", "Rosenfält",
        "Rosenstam", "Rosenström", "Rosensjö", "Rosenguld", "Rosensköld", "Doyle", "Davenport", "McCullough", "Hooper", "Dior", "Punk", "Platina",
        "Rasputin", "Ek", "Ekberg", "Ekbacka", "Ekbacke", "Ekskog", "Ekenskog", "Ekman", "Ekenvarg", "Ekenkvist", "Ekenquist", "Ekengård",
        "Ekenfält", "Ekenstam", "Ekström", "Ekenksjö", "Ekensköld", "Mackaron", "Mictlantecuhtli", "Barton", "George", "Bernard", "Riley", "Harmon",
        "Hood", "Richmond", "Dye", "Rasmussen", "Strong", "Fletcher", "Rowland", "Cooper", "Sheehan", "Gray", "Gry", "Couch", "Simon", "McClain",
        "Carr", "Casey", "Melton", "ONeil", "Sellers", "Hartley", "Costa", "Noble", "Sexton", "Driscoll", "Ritter", "Wyatt", "Hök", "Dufva", "Kråk",
        "Sprängare", "Dentika", "Eskerson", "Lundström", "Ågren", "Hedman", "Cederström", "Abakumov", "Abdulov", "Abramov", "Agapov", "Agafonov",
        "Alexeyev", "Andreyev", "Antonov", "Arsenyev", "Artyomov", "Alekseev", "Angeloff", "Mabuse", "Pälsänger", "Norin", "Wagner", "Stuka",
        "Frigid", "Gotic", "Bering", "Bertling", "Arkhangelsky", "Aslanov", "Andreev", "Belyaev", "Belov", "Babanin", "Balabanov", "Balakin",
        "Paludan", "Balakirev", "Balandin", "Baranov", "Barinov", "Belsky", "Babin", "Bocharov", "Borisyuk", "Borovkov", "Borodin", "Bortnik",
        "Bortsov", "Berlin", "Bugrov", "Bychkov", "Chaban", "Chernoff", "Chugunov", "Davydov", "Dmitriev", "Devin", "Dobrow", "Dominik", "Drozdov",
        "Uggla", "Igelkotte", "Martin", "Egorov", "Elin", "Evanoff", "Fedorov", "Gorky", "Gorbachev", "Gusev", "Galkin", "Garin", "Genrich",
        "Gurin", "Golubev", "Snygg", "Ibragimov", "Ilyin", "Ivanov", "Kuznetsov", "Kalashnik", "Kozlov", "Kamenev", "Komarov", "Kotov", "Kiselyov",
        "Kravtsov", "Kovalyov", "Krupin", "Kuzmin", "Glad", "Svart", "Stare", "Marin", "Lagunov", "Lebedev", "Lenkov", "Medvedev", "Morozov",
        "Mikhailov", "Meknikov", "Molchalin", "Molotov", "Nikolaev", "Novikov", "Nikitin", "Westwood", "Orlov", "Pasternak", "Petrov", "Pavlov",
        "Petukhov", "Jordan", "Plotnikov", "Popov", "Poletov", "Portnov", "Rabinovich", "Rogov", "Rybakov", "Smirnov", "Sidorov", "Mondrian",
        "Sokolov", "Semyonov", "Stepanov", "Ocean", "Triton", "Biden", "Eros", "Sorpresini", "Solina", "Tortilone", "Krishna", "Jacobs", "Hansen",
        "Dow", "Jones", "Pollak", "Nato", "Washington", "Katsanidou", "Oberg", "Graneloni", "Hodges", "Rossi", "Rosso", "Marciano", "Fabiano",
        "Sebastiano", "Cappellari", "Lanaro", "Cestaro", "Fucilla", "Scarlo", "Sbarbaro", "Soru", "Nieddu", "Madu", "Biondi", "Quattrochi", "Cicala",
        "Volpe", "Colletta", "Checati", "Cecati", "Alessandrini", "Alessandro", "Albino", "Accomoando", "Achille", "Abromo", "Albertini",
        "Albertelli", "Alfonso", "Alu", "Agresti", "Agosto", "Aquila", "Antonino", "Amorosi", "Capozzi", "Canal", "Campolo", "Polo", "Lotus",
        "Messel", "Broman", "Müller", "Schmidt", "Schneider", "Fischer", "Weber", "Schäfer", "Meyer", "Wagner", "Becker", "Bauer", "Hoffmann",
        "Schulz", "Koch", "Richter", "Klein", "Wolf", "Schröder", "Neumann", "Braun", "Werner", "Schwarz", "Zimmermann", "Schmitt", "Hartmann",
        "Schmid", "Weiß", "Krüger", "Lange", "Meier", "Walter", "Köhler", "Maier", "Beck", "König", "Krause", "Schulze", "Huber", "Mayer", "Frank",
        "Lehmann", "Kaiser", "Fuchs", "Herrmann", "Peters", "Stein", "Jung", "Möller", "Berger", "Martin", "Friedrich", "Scholz", "Keller", "Groß",
        "Hahn", "Roth", "Günther", "Vogel", "Schubert", "Winkler", "Schuster", "Lorenz", "Ludwig", "Baumann", "Heinrich", "Otto", "Simon", "Graf",
        "Kraus", "Krämer", "Böhm", "Schulte", "Albrecht", "Franke", "Winter", "Schumacher", "Vogt", "Haas", "Sommer", "Schreiber", "Engel",
        "Ziegler", "Dietrich", "Brandt", "Seidel", "Kuhn", "Busch", "Horn", "Arnold", "Kühn", "Bergmann", "Pohl", "Pfeiffer", "Wolf", "Voigt",
        "Sauer", "Granström", "Arbsjö", "Björklund", "Sydov", "Fortenbach", "Katamadze", "Bonde", "Bondesson", "Is", "Engelbrekt", "Kerro", "Weibel",
        "Nielsen", "Refs", "Lockney", "Krämer", "Pin", "Tao", "Hüttner", "Pellikaan", "Baker", "Cooper", "Butcher");
    
    
    
    public static function getName(?int $gender=0) {
     $name = "";
    if ($gender == 0) {
        //Take random gender
        $gender = rand(1, 2);
    }
    if ($gender == 1) {
        $nr_of_names = static::getBucketFromWeights(array(0,90,8,2));
        $nameIds = array_rand(RandomName::GIRLS, $nr_of_names);
        if (is_array($nameIds)) {
            foreach($nameIds as $nameId) $name .= " ". RandomName::GIRLS[$nameId];
        } else $name .= RandomName::GIRLS[$nameIds];
    } else {
        $nr_of_names = static::getBucketFromWeights(array(0,90,8,2));
        $nameIds = array_rand(RandomName::BOYS, $nr_of_names);
        if (is_array($nameIds)) {
            foreach($nameIds as $nameId) $name .= " ". RandomName::BOYS[$nameId];
        } else $name .= RandomName::BOYS[$nameIds];
    }
    
    $nr_of_names = static::getBucketFromWeights(array(0,90,10));
    $nameIds = array_rand(RandomName::LASTNAMES, $nr_of_names);
    if (is_array($nameIds)) {
        foreach($nameIds as $nameId) $name .= " ". RandomName::LASTNAMES[$nameId];
    } else $name .= " ". RandomName::LASTNAMES[$nameIds];
    return trim($name);
}



public static function getGroupNames(int $nr_of_names) {
    $nameIds = array_rand(RandomName::GROUPS, $nr_of_names);
    $names = array();
    foreach($nameIds as $nameId) {
        $names[] = RandomName::GROUPS[$nameId];
    }
    return $names;
}



private static function getBucketFromWeights($values) {
    $total = $currentTotal = $bucket = 0;
    $firstRand = mt_rand(1, 100);
    
    foreach ($values as $amount) {
        $total += $amount;
    }
    
    $rand = ($firstRand / 100) * $total;
    
    foreach ($values as $amount) {
        $currentTotal += $amount;
        
        if ($rand > $currentTotal) {
            $bucket++;
        }
        else {
            break;
        }
    }
    
    return $bucket;
}
}