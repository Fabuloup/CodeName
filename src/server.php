<?php
session_start();
// Path to the JSON file storing game data
const GAME_FILE = 'games.json';

// Function to load all games from JSON
function loadGames() {
    if (file_exists(GAME_FILE)) {
        return json_decode(file_get_contents(GAME_FILE), true) ?: [];
    }
    return [];
}

// Function to save all games to JSON
function saveGames($games) {
    file_put_contents(GAME_FILE, json_encode($games, JSON_PRETTY_PRINT));
}

// Function to generate a random word list for the game
function generateWordList() {
    $words = ["Afrique", "Aiguille", "Aile", "Air", "Alien", "Allemagne", "Alpes", "Amour", "Ampoule", "Amérique", "Ange", "Angleterre", "Anneau", "Appareil", "Araignée", "Arc", "Argent", "Asile", "Astérix", "Atlantique", "Atout", "Australie", "Avion", "Avocat", "Baguette", "Baie", "Balance", "Baleine", "Balle", "Ballon", "Banane", "Banc", "Bande", "Banque", "Bar", "Barbe", "Base", "Bateau", "Berlin", "Bière", "Blé", "Bombe", "Bon", "Botte", "Bouche", "Bouchon", "Bougie", "Boulet", "Bourse", "Bouteille", "Bouton", "Boîte", "Branche", "Bretelle", "Brique", "Bureau", "But", "Bâton", "Bête", "Bûche", "Bɶuf", "Cabinet", "Cadre", "Cafard", "Café", "Camembert", "Campagne", "Canada", "Canard", "Canne", "Canon", "Carreau", "Carrière", "Carte", "Carton", "Cartouche", "Casino", "Ceinture", "Cellule", "Centre", "Cercle", "Champ", "Champagne", "Chance", "Chapeau", "Charge", "Charme", "Chasse", "Chat", "Chausson", "Chaîne", "Chef", "Chemise", "Cheval", "Chevalier", "Chien", "Chine", "Chocolat", "Chou", "Château", "Cinéma", "Cirque", "Citrouille", "Classe", "Club", "Clé", "Cochon", "Code", "Col", "Colle", "Commerce", "Coq", "Corde", "Corne", "Coton", "Coupe", "Courant", "Couronne", "Course", "Court", "Couteau", "Couverture", "Critique", "Crochet", "Cuisine", "Cycle", "Cɶur", "Danse", "Dinosaure", "Docteur", "Don", "Dragon", "Droit", "Droite", "Eau", "Enceinte", "Ensemble", "Entrée", "Espace", "Espagne", "Espion", "Esprit", "Essence", "Europe", "Facteur", "Fantôme", "Farce", "Fer", "Ferme", "Feu", "Feuille", "Figure", "Filet", "Fin", "Flûte", "Formule", "Fort", "Forêt", "Fou", "Foyer", "Fraise", "Français", "Front", "Fuite", "Garde", "Gauche", "Gel", "Glace", "Gorge", "Grain", "Grenade", "Grue", "Grèce", "Guerre", "Guide", "Géant", "Génie", "Herbe", "Himalaya", "Histoire", "Hiver", "Hollywood", "Héros", "Hôpital", "Hôtel", "Indien", "Iris", "Jet", "Jeu", "Jour", "Journal", "Jumelles", "Jungle", "Kangourou", "Kiwi", "Lait", "Langue", "Laser", "Lentille", "Lettre", "Licorne", "Lien", "Ligne", "Lion", "Liquide", "Lit", "Livre", "Londres", "Louche", "Lumière", "Lune", "Lunettes", "Luxe", "Machine", "Magie", "Main", "Majeur", "Maladie", "Manche", "Manège", "Marche", "Marin", "Marque", "Marron", "Mars", "Maîtresse", "Membre", "Menu", "Meuble", "Microscope", "Miel", "Millionaire", "Mine", "Mineur", "Mode", "Molière", "Mort", "Mouche", "Moule", "Mousse", "Moustache", "Mémoire", "Nain", "Napoléon", "Neige", "New-York", "Ninja", "Noir", "Note", "Noël", "Nuit", "Numéro", "Nɶud", "Oiseau", "Opéra", "Opération", "Or", "Orange", "Ordre", "Page", "Paille", "Palais", "Palme", "Papier", "Parachute", "Paris", "Partie", "Passe", "Patron", "Pendule", "Pensée", "Perle", "Peste", "Phare", "Physique", "Piano", "Pied", "Pigeon", "Pile", "Pilote", "Pingouin", "Pirate", "Pièce", "Place", "Plage", "Plan", "Planche", "Plante", "Plat", "Plateau", "Plume", "Point", "Poire", "Poison", "Poisson", "Police", "Pomme", "Pompe", "Portable", "Poste", "Pouce", "Poêle", "Princesse", "Prise", "Prêt", "Pyramide", "Pétrole", "Pêche", "Pôle", "Quartier", "Queue", "Radio", "Raie", "Rame", "Rat", "Rayon", "Recette", "Reine", "Religieuse", "Remise", "Requin", "Restaurant", "Robe", "Robot", "Roi", "Rome", "Ronde", "Rose", "Rouge", "Rouleau", "Roulette", "Russie", "Règle", "Résistance", "Révolution", "Sardine", "Satellite", "Schtroumpf", "Science", "Scène", "Sens", "Sept", "Serpent", "Sirène", "Siège", "Sol", "Soldat", "Soleil", "Solution", "Somme", "Sorcière", "Sortie", "Souris", "Table", "Tableau", "Talon", "Tambour", "Temple", "Temps", "Tennis", "Terre", "Timbre", "Titre", "Toile", "Tokyo", "Tour", "Trait", "Trou", "Trésor", "Tube", "Tuile", "Tête", "Uniforme", "Vague", "Vaisseau", "Vampire", "Vase", "Vent", "Verre", "Vert", "Vie", "Vin", "Visage", "Vision", "Voile", "Voiture", "Vol", "Voleur", "Volume", "Zéro", "Échelle", "Éclair", "École", "Égalité", "Égypte", "Éponge", "Étoile", "Étude", "Œil", "Œuf"];
    shuffle($words);
    return array_slice($words, 0, 25);
}

// Function to generate team assignments
function assignTeams($words) {
    $assignments = array_fill(0, 9, "red");
    $assignments = array_merge($assignments, array_fill(0, 8, "blue"));
    $assignments = array_merge($assignments, array_fill(0, 7, "neutral"));
    $assignments[] = "assassin";
    shuffle($assignments);
    return array_combine($words, $assignments);
}

$games = loadGames();

function initGame($code) {
    global $games;
    $games[$code] = [
        'words' => generateWordList(),
        'teams' => [],
        'revealed' => [],
        'turn' => 'red',
        'players' => [],
        'chief' => [],
        'chat' => []
    ];
    $games[$code]['teams'] = assignTeams($games[$code]['words']);
    $games[$code]['revealed'] = array_fill_keys($games[$code]['words'], false);
    if($_SESSION['pseudo'])
    {
        array_push($games[$code]['players'], $_SESSION['pseudo']);
    }
    saveGames($games);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['code'])) {
        $code = $_GET['code'];
        if (!isset($games[$code])) {
            initGame($code);
        }
        echo json_encode($games[$code]);
        exit;
    }
    echo json_encode(["error" => "No game code provided"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    

    $data = $_POST;

    if(!isset($data['code']))
    {
        echo json_encode(["error" => "No game code provided"]);
        exit;
    }

    $code = $data['code'];
    
    if(!isset($games[$code]))
    {
        echo json_encode(["error" => "Game not found"]);
        exit;
    }


    if (isset($data['pseudo'])) {
        $pseudo = $data['pseudo'];
        if (isset($_SESSION['pseudo'])) {
            $oldPseudo = $_SESSION['pseudo'];
            if (($key = array_search($oldPseudo, $games[$code]['players'])) !== false) {
                $games[$code]['players'][$key] = $pseudo;
                foreach ($games[$code]['chat'] as &$message) {
                    $message = str_replace($oldPseudo . ':', $pseudo . ':', $message);
                }
                if (($key = array_search($oldPseudo, $games[$code]['chief'])) !== false) {
                    $games[$code]['chief'][$key] = $pseudo;
                }
            }
            else
            {
                array_push($games[$code]['players'], $pseudo);
            }
        }
        else
        {
            array_push($games[$code]['players'], $pseudo);
        }
        $_SESSION['pseudo'] = $pseudo;

        if(isset($data['chiefApplication']) && !in_array($pseudo, $games[$code]['chief']))
        {
            array_push($games[$code]['chief'], $pseudo);
        }

        if (isset($data['message'])) {
            $games[$code]['chat'][] = htmlspecialchars($data['pseudo']) . ': ' . htmlspecialchars($data['message']);
        }
    }

    
    
    if (isset($data['word'])) {
        $word = $data['word'];
        if (isset($games[$code]['revealed'][$word]) && !$games[$code]['revealed'][$word]) {
            $games[$code]['revealed'][$word] = true;
            if ($games[$code]['teams'][$word] !== $games[$code]['turn']) {
                $games[$code]['turn'] = ($games[$code]['turn'] === 'red') ? 'blue' : 'red';
            }
        }
    }
    
    saveGames($games);
    echo json_encode($games[$code]);
    exit;
}
