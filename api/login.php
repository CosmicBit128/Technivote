<?php

declare(strict_types=1);

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/../db.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    http_response_code(200);
    echo json_encode([ "status" => "yay", "logged_in" => $_SESSION['user_id'] ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "error" => "Method not allowed"]);
    exit;
}

$username = trim((string)($_POST['login'] ?? ''));
$password = (string)($_POST['pass'] ?? '');

if ($username === '' || $password === '' || strlen($username) > 64 || strlen($password) > 64) {
    http_response_code(400);
    echo json_encode(["status" => "wrong", "error" => "Login i hasło muszą mieć od 1 do 64 znaków."]);
    exit;
}

$conn = mysqli_init();

if (!$conn || !mysqli_real_connect($conn, $db_host, $db_user, $db_pass, $db_name)) {
    http_response_code(500);
    echo json_encode(["status" => "error", "error" => "Internal server error!"]);
    exit;
}

mysqli_set_charset($conn, 'utf8mb4');

$stmt = mysqli_prepare($conn, 'SELECT id, username, password, admin FROM users WHERE username = ? LIMIT 1');

if (!$stmt) {
    http_response_code(500);
    echo json_encode(["status" => "error", "error" => "Internal server error!"]);
    exit;
}

mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);
mysqli_close($conn);

if (!$user || !password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode(["status" => "wrong", "error" => "Niepoprawny login lub hasło!"]);
    exit;
}

session_regenerate_id(true);

$_SESSION['user_id'] = (int)$user['id'];
$_SESSION['user_name'] = $user['username'];
$_SESSION['is_admin'] = (bool)$user['admin'];

echo json_encode([ "status" => "yay" ]);
exit;