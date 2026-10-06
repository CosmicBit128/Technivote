<?php
    require_once "db.php";

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $user_name = $_SESSION['user_name'];
        echo("<script> const currentUser = $user_id; /* php stuff */</script>\n");
    }
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technivote</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/main.css">
    <script type="module" src="js/main.js"></script>
</head>
<body data-page="home">
    <div id="accountMenu" class="glass" role="menu">
        <?php if (isset($_SESSION['user_id']) && isset($_SESSION['user_name'])) echo $_SESSION['user_name']."\n"; ?>
        <button id="logout" class="btn glass pill" role="menuitem">Wyloguj</button>
    </div>
    <nav>
        <h1 id="title">Technivote</h1>
        <div id="nav-right" class="hidden">
<?php if (isset($user_id) && $_SESSION['is_admin']) echo "            <a class=\"btn glass pill\" href=\"admin-panel.php\">Admin Panel</a>\n"; ?>
            <a class="btn glass pill hide-on-mobile" href="give-idea.php" aria-label="Nowe głosowanie">Daj pomysł</a>
            <div class="account">
                <button id="accountToggle" class="btn glass pill" aria-haspopup="true" aria-expanded="false" aria-controls="accountMenu">
                    <img src="res/user.svg" alt="" class="svg-icon">
                    <?php if (isset($user_id, $user_name)) echo $user_name."\n"; ?>
                </button>
            </div>
        </div>
    </nav>
    <main>
        <div id="ideaList"></div>
    </main>
    <div id="loginWrapper">
        <div id="loginDisclaimer" class="glass">
            <button id="loginDisclaimerClose">×</button>
            <h2>Musisz się zalogować</h2>
            <p>Aby głosować musisz być zalogowany</p>
            <button id="logInButton" class="glass btn">Zaloguj Się</button>
        </div>
    </div>
</body>
</html>