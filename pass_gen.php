<?php
if (isset($_POST["pass"])) {
    echo password_hash($_POST["pass"], PASSWORD_DEFAULT);
}
?>
<form method="post">
    <input type="password" name="pass">
</form>
