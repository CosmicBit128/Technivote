<?php

require_once __DIR__ . '/db.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: /');
} else {
    $user_id = $_SESSION['user_id'];
    $user_name = $_SESSION['user_name'];
    echo("<script> const currentUser = $user_id; </script>");
}
?>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Technivote - Admin Panel</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/ideaList.css">
    <script type="module" src="js/admin.js"></script>
</head>
<body data-page="admin-panel">
    <div id="accountMenu" class="glass" role="menu">
        <?php if (isset($_SESSION['user_id']) && isset($_SESSION['user_name'])) echo $_SESSION['user_name']."\n"; ?>
        <button id="logout" class="btn glass pill" role="menuitem">Wyloguj</button>
    </div>
    <nav>
        <h1 id="title">Technivote</h1>
        <div id="nav-right">
            <button id="backButton" class="btn glass pill">Wróć</button>
            <a class="btn glass pill hide-on-mobile" href="add-idea.html" aria-label="Nowe głosowanie">Daj pomysł</a>
            <div class="account">
                <button id="accountToggle" class="btn glass pill" aria-haspopup="true" aria-expanded="false" aria-controls="accountMenu">
                    <img src="res/user.svg" alt="" class="svg-icon">
                    <?php if (isset($user_id, $user_name)) echo $user_name; ?>
                </button>
            </div>
        </div>
    </nav>
    <main>
        <div id="ideaList"></div>
    </main>
</body>
</html>
