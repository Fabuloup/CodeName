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

function saveGames($games) {
    file_put_contents(GAME_FILE, json_encode($games, JSON_PRETTY_PRINT));
}

if ($_SERVER['REQUEST_METHOD'] === 'GET')
{
    if(isset($_GET['code']))
    {
        $games = loadGames();
        if (isset($_GET['reset'])) {
            unset($games[$_GET['code']]);
            saveGames($games);
            header('Location: /?code=' . $_GET['code']);
            exit;
        }

        if(!isset($_SESSION['pseudo']))
        {
            $pseudo = "Anonymous".rand(0,9999);
            $_SESSION['pseudo'] = $pseudo;
        }
    }

}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Codenames</title>
    <link rel="icon" href="favicon.ico">
    <link rel="stylesheet" type="text/css" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        gameCode = "<?= $_GET['code'] ?? "" ?>";

        function updateGame() {
            $.get("server.php", { code: gameCode }, function(data) {
                let game = JSON.parse(data);
                $(".word-grid").html("");
                game.words.forEach(word => {
                    let revealedClass = game.revealed[word] || game.chief.includes($("#pseudo-input").val()) ? " team_" + game.teams[word] : " hidden";
                    $(".word-grid").append(`<div class='word-card${revealedClass}' onclick='selectWord("${word}")'>${word}</div>`);
                });
                $(".chat-box").html(game.chat.map(msg => `<p>${msg}</p>`).join(""));
                $("#game-state").text("Tour : " + game.turn.charAt(0).toUpperCase() + game.turn.slice(1) + " Team");
                if (game.turn === "blue") {
                    $("body").removeClass("red").addClass("blue");
                } else if (game.turn === "red") {
                    $("body").removeClass("blue").addClass("red");
                }
            });
        }
        
        function selectWord(word) {
            $.post("server.php", {code: gameCode, word: word }, updateGame);
        }
        
        function sendMessage() {
            let message = $("#chat-input").val();
            let pseudo = "<?= htmlspecialchars($_SESSION['pseudo'] ?? 'Anonymous') ?>";
            if (message.trim() !== "") {
                $.post("server.php", { code: gameCode, pseudo: pseudo, message: message }, updateGame);
                $("#chat-input").val("");
            }
        }
        
        function sendPseudo() {
            let pseudo = $("#pseudo-input").val();
            if (pseudo.trim() !== "") {
                $.post("server.php", { code: gameCode, pseudo: pseudo }, updateGame);
            }
        }
        
        function sendChiefApplication() {
            let pseudo = $("#pseudo-input").val();
            if (pseudo.trim() !== "") {
                $.post("server.php", { code: gameCode, pseudo: pseudo, chiefApplication: true }, updateGame);
            }
        }

        function resetGame() {
            if (confirm("Êtes-vous sûr de vouloir relancer la partie ?")) {
                $.get("index.php", { code: gameCode, reset: true }, function() {
                    updateGame();
                });
            }
        }
        
        $(document).ready(function() {
            updateGame();
            setInterval(updateGame, 2000);
        });
    </script>
</head>
<body>
    <h1>Codenames</h1>
    <div class="container">
        <div class="game-container">
            <h2 id="game-state">Tour : ...</h2>
            <div class="pseudo-container">
                <input type="text" id="pseudo-input" placeholder="Entrez votre pseudo..." onchange="sendPseudo()" value="<?= $_SESSION['pseudo'] ?>">
                <button onclick="sendChiefApplication()">Devenir chef</button>
            </div>
            <div class="word-grid"></div>
            <button onclick="resetGame()">Relancer la partie</button>
        </div>
        
        <div class="chat-container">
            <h2>Chat</h2>
            <div class="chat-box"></div>
            <input type="text" id="chat-input" placeholder="Tapez votre message...">
            <button onclick="sendMessage()">Envoyer</button>
        </div>
    </div>
</body>
</html>
