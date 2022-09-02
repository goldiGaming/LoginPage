<?php

session_start();

if(isset($_SESSION['user_id'])) {

    header("Location: user.php");
    exit;
}
else {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <title></title>

        <!-- <meta http-equiv="refresh" content="0; URL=login.php"> -->

    </head>
    <body>
    </body>
</html>