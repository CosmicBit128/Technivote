<?php

require_once __DIR__ . '/../db.php';

header("Content-Type: application/json; charset=UTF-8");

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$conn) {
    echo json_encode(["status" => "error", "error" => "Database connection failed: " . mysqli_connect_error()]);
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
    where i.approved = 1
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
    $exist_check_stmt = mysqli_prepare($conn, "select * from ideas where id = ? and approved=1 limit 1;");
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

    http_response_code(400);
    echo json_encode([ "status" => "login" ]);
    exit;
}

http_response_code(400);
echo json_encode(["status" => "error", "error" => "Invalid request"]);
mysqli_close($conn);
?>