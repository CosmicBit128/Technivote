<?php

require_once __DIR__ . '/db.php';

header("Content-Type: application/json; charset=UTF-8");


session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/cosmic/technivote',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);
if (!$conn) {
    echo json_encode(["status" => "error", "error" => "Database connection failed: " . mysqli_connect_error()]);
    exit;
}

mysqli_set_charset($conn, 'utf8mb4');

// Login, require "safe" method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);

    if (isset($_POST['login'], $_POST['pass'])) {
        if (isset($_SESSION['user_id'])) {
            echo json_encode([ "status" => "login" ]);
            exit;
        }

        $username = trim((string)$_POST['login']);
        $password = (string)$_POST['pass'];

        if ($username === '' || $password === '' || strlen($username) > 64 || strlen($password) > 64) {
            http_response_code(400);
            echo json_encode(["status" => "wrong", "error" => "Login i hasło muszą mieć od 1 do 64 znaków."]);
            exit;
        }

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
    }

    http_response_code(400);
    echo json_encode([ "status" => "error", "error" => "Bad request!", "post" => $_POST ]);
    exit;
}


// Get Ideas
if (isset($_GET['get_ideas'])) {
    $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
    $stmt = mysqli_prepare($conn, "
    select i.id, i.idea, i.approved,
           sum(if(v.vote_yes = true, 1, 0)) as votes_yes,
           sum(if(v.vote_yes = false, 1, 0)) as votes_no, 
           max(if(v.user_id = ?, v.vote_yes, null)) as user_vote
    from ideas i left join votes v on i.id = v.idea_id
    where i.approved = 1 and i.discarded = 0
    group by i.id, i.idea;
    ");
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $user_vote = null;
        if ($row['user_vote'] !== null) {
            $user_vote = (bool)$row['user_vote'];
        }

        $data[] = [
            "id" => (int)$row['id'],
            "text" => $row['idea'],
            "votes_yes" => (int)$row['votes_yes'],
            "votes_no" => (int)$row['votes_no'],
            "user_vote" => $user_vote
        ];
    }

    mysqli_stmt_close($stmt);
    echo json_encode($data);
    mysqli_close($conn);
    exit;
}

if (isset($_GET['cast_vote']) && isset($_GET['yes'])) {
    // Check for login
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["status" => "login"]);
        exit;
    }

    $idea_id = (int)$_GET['cast_vote'];
    $vote_yes = $_GET['yes'] === '0' ? 0 : 1;
    $user_id = (int)$_SESSION['user_id'];

    // Check if already voted
    $vote_check_stmt = mysqli_prepare($conn, "select id from votes where user_id = ? and idea_id = ? limit 1;");
    if (!$vote_check_stmt) {
        echo json_encode(["status" => "error", "error" => "Internal server error!"]);
        exit;
    }
    mysqli_stmt_bind_param($vote_check_stmt, 'ii', $user_id, $idea_id);
    mysqli_stmt_execute($vote_check_stmt);
    if (mysqli_num_rows(mysqli_stmt_get_result($vote_check_stmt)) != 0) {
        echo json_encode(["status" => "voted"]);
        exit;
    }

    // Check if idea exists
    $exist_check_stmt = mysqli_prepare($conn, "select * from ideas where id = ? and approved = 1 and discarded = 0 limit 1;");
    if (!$exist_check_stmt) {
        echo json_encode(["status" => "error", "error" => "Internal server error!"]);
        exit;
    }
    mysqli_stmt_bind_param($exist_check_stmt, 'i', $idea_id);
    mysqli_stmt_execute($exist_check_stmt);
    if (mysqli_num_rows(mysqli_stmt_get_result($exist_check_stmt)) == 0) {
        echo json_encode(["status" => "no_exist"]);
        exit;
    }

    // Vote
    $vote_stmt = mysqli_prepare($conn, "insert into votes (user_id, idea_id, vote_yes) values (?, ?, ?)");
    if (!$vote_stmt) {
        echo json_encode(["status" => "error", "error" => "Internal server error!"]);
        exit;
    }
    mysqli_stmt_bind_param($vote_stmt, 'iii', $user_id, $idea_id, $vote_yes);
    mysqli_stmt_execute($vote_stmt);

    echo json_encode(["status" => "yay"]);
    exit;
}

if (isset($_GET['logout'])) {
    if (isset($_SESSION['user_id'])) {
        unset($_SESSION['user_id']);
        if (isset($_SESSION['user_name'])) unset($_SESSION['user_name']);
        if (isset($_SESSION['is_admin'])) unset($_SESSION['is_admin']);

        echo json_encode( ["status" => "yay"] );
        exit;
    }

    echo json_encode([ "status" => "login" ]);
    exit;
}

if (isset($_GET['create_idea'])) {
    $idea = $_GET['create_idea'];
    if ($idea.trim("\n\r\t\v\0 ") == "") {
        echo json_encode([ "status" => "empty" ]);
        exit;
    }

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["status" => "login"]);
        exit;
    }

    $stmt = mysqli_prepare($conn, "insert into ideas (idea, created_by) values (?, ?)");
    if (!$stmt) {
        echo json_encode(["status" => "error", "error" => "Internal server error!"]);
        exit;
    }
    mysqli_stmt_bind_param($stmt, 'ss', $idea, $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);

    echo json_encode(["status" => "yay"]);
    exit;
}

if (isset($_GET['get_unapproved'])) {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode([ "status" => "login" ]);
        exit;
    }
    if (!$_SESSION['is_admin']) {
        http_response_code(403);
        exit;
    }

    $stmt = mysqli_prepare($conn, "select id, idea, created from ideas where approved = 0 and discarded = 0 order by created;");
    if (!$stmt) {
        echo json_encode([ "status" => "error", "error" => "Internal server error!" ]);
        exit;
    }
    mysqli_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = [
            "id" => (int)$row['id'],
            "text" => $row['idea']
        ];
    }

    echo json_encode([ "status" => "yay", "ideas" => $data ]);
    exit;
}

if (isset($_GET['review'], $_GET['res'])) {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["status" => "login"]);
        exit;
    }
    if (!$_SESSION['is_admin']) {
        http_response_code(403);
        exit;
    }

    $idea_id = $_GET['review'];
    $res = $_GET['res'];
    if ($res !== "approved" && $res !== "discarded") {
        http_response_code(400);
        exit;
    }
    $stmt = mysqli_prepare($conn, "update ideas set $res = 1 WHERE id = ?");
    if (!$stmt) {
        echo json_encode(["status" => "error", "error" => "Internal server error!"]);
        exit;
    }
    mysqli_stmt_bind_param($stmt, 'i', $idea_id);
    mysqli_stmt_execute($stmt);

    echo json_encode(["status" => "yay"]);
    exit;
}

http_response_code(400);
echo json_encode(["status" => "error", "error" => "Invalid request"]);
mysqli_close($conn);
?>