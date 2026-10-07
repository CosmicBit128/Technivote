<?php
    require_once "db.php";

    if (!isset($_SESSION['user_id'])) {
        header('Location: /');
        exit;
    }
    $user_id = $_SESSION['user_id'];
    $user_name = $_SESSION['user_name'];
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technivote</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/idea.css">
    <script type="module" src="js/idea.js"></script>
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
            <button id="backButton" class="btn glass pill" aria-label="Wróć">Wróć</button>
            <div class="account">
                <button id="accountToggle" class="btn glass pill" aria-haspopup="true" aria-expanded="false" aria-controls="accountMenu">
                    <img src="res/user.svg" alt="" class="svg-icon">
<?php if (isset($user_id, $user_name)) echo "                    ".$user_name."\n"; ?>
                </button>
            </div>
        </div>
    </nav>
    <main>
        <section id="ideaForm" class="glass">
            <label class="field">
                <span class="row">
                    Podziel się swoim pomysłem
                    <span class="info" title="Maksymalnie 256 znaków">ⓘ</span>
                </span>
                <textarea id="ideaTextarea" class="glass" maxlength="256"></textarea>
            </label>
        </section>
        <div id="buttonRow">
            <button id="submitButton" class="glass btn">Gotowe</button>
        </div>
    </main>
</body>
</html>