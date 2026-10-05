<?php

require_once "db.php";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

if (isset($_GET['get_ideas'])) {
    $sql = "
    select i.id, i.idea, i.approved,
        sum(if(v.vote_yes = true, 1, 0)) as votes_yes,
        sum(if(v.vote_yes = false, 1, 0)) as votes_no
    from ideas i left join votes v on i.id = v.idea_id
    group by i.id, i.idea;
    ";

    $data = array();
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        if (!$row['approved']) continue;
        $data[] = array(
            "id" => intval($row['id']),
            "idea" => $row['idea'],
            "votes_yes" => intval($row['votes_yes']),
            "votes_no" => intval($row['votes_no'])
        );
    }

    mysqli_close($conn);

    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode($data);
}
?>