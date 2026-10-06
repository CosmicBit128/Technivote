<?php
session_start();
if(!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: /");
    exit;
}

if (isset($_POST["pass"])) {
    echo password_hash($_POST["pass"], PASSWORD_DEFAULT);
}
?>
<form method="post">
    <input type="password" name="pass">
</form>
