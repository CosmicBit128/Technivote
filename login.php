<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    header('Location: /', true, 303);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit;
}

$username = trim((string)($_POST['login'] ?? ''));
$password = (string)($_POST['pass'] ?? '');

if ($username === '' || $password === '') {
    $_SESSION['error'] = 'Incorrect login or password!';
    header('Location: /login.php', true, 303);
    exit;
}

if (strlen($username) > 64 || strlen($password) > 64) {
    $_SESSION['error'] = 'Incorrect login or password!';
    header('Location: /login.php', true, 303);
    exit;
}

$conn = mysqli_init();

if (!$conn || !mysqli_real_connect($conn, $db_host, $db_user, $db_pass, $db_name)) {
    $_SESSION['error'] = "An internal error occurred. Please try again later.<br />" . mysqli_error($conn);
    header('Location: /login.php', true, 500);
    exit;
}

mysqli_set_charset($conn, 'utf8mb4');

$stmt = mysqli_prepare($conn, 'SELECT id, password, admin FROM users WHERE username = ? LIMIT 1');

if (!$stmt) {
    $_SESSION['error'] = "An internal error occurred. Please try again later.<br />" . mysqli_error($conn);
    header('Location: /login.php', true, 500);
    exit;
}

mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);
mysqli_close($conn);

if (!$user || !password_verify($password, $user['pass'])) {
    $_SESSION['error'] = 'Niepoprawny login lub hasło!';
    header('Location: /login.php', true, 303);
    exit;
}

session_regenerate_id(true);

$_SESSION['user_id'] = (int)$user['id'];
$_SESSION['is_admin'] = (bool)$user['admin'];
$_SESSION['error'] = '';

header('Location: /', true, 303);
exit;
?>