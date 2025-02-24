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
    $words = ["apple", "banana", "cat", "dog", "elephant", "falcon", "guitar", "honey", "island", "jungle", "kangaroo", "lemon", "mountain", "notebook", "octopus", "piano", "queen", "rocket", "sunflower", "tiger", "umbrella", "volcano", "whale", "xylophone", "yogurt"];
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
        $_SESSION['pseudo'] = $data['pseudo'];
    }
    
    if (isset($data['message'])) {
        $games[$code]['chat'][] = htmlspecialchars($data['pseudo']) . ': ' . htmlspecialchars($data['message']);
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
