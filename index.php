<?php
    require_once "db.php";

    if (isset($_SESSION['user_id'])) {
        // echo("<script>const currentUser = " . $_SESSION['user_id'] . ";</script>");
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technivote</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/main.css">
    <script type="module" src="js/main.js"></script>
</head>
<body>
    <nav>
        <h1 id="title">Technivote</h1>
    </nav>
    <main>
        <div id="ideaList">
            <div class="idea glass">
                <p class="ideaText">
                    Bicie uczniów po 15:35, a szczególnie ubunciarza, bo nie lubi Windowsa, a lubi Linuxa<br />
                    <i>It's stupid, and also dumb!!!</i><br />
                    <span style="text-align: right;">- Uzi</span><br />
                    I love Murder Drones!!!1!
                </p>
                <div class="ideaVotes">
                    <button class="voteYes btn">21</button>
                    <button class="voteNo btn">1</button>
                </div>
            </div>
        </div>
    </main>
    <div id="loginWrapper" class="open">
        <div id="loginDisclaimer" class="glass">
            <button id="loginDisclaimerClose">×</button>
            <h2>Musisz się zalogować</h2>
            <p>Aby głosować musisz być zalogowany</p>
            <button id="logInButton" class="glass btn">Zaloguj Się</button>
        </div>
    </div>
</body>
</html>