<?php

include "mysql.php";

session_start();

//User zu index.php senden, wenn nicht eingeloggt:
if(!isset($_SESSION["user_id"])) {

    header("location: index.php");
    exit;
}

//UserID:
$user_id = $_SESSION["user_id"];

//Alles aus der Datenbank ziehen durch die ID:
$search_user = $pdo->prepare("SELECT id, username, email, password, notepad FROM users WHERE id = :user_id");
$search_user->bindValue(":user_id",$user_id);
$search_user->execute();
$user = $search_user->fetch(PDO::FETCH_ASSOC);

//Userdaten in Variablen speichern:
$user_username = $user["username"];
$user_email = $user["email"];
$user_password = $user["password"]; //Noch mit Hash verschlüsselt
$user_notepad = $user["notepad"];

//Notizbuch:
if(isset($_POST["notepad-save"])) {
    $user_notepad = $_POST["notepad"];

    //Notizen einfügen:
    $insert = $pdo->prepare("UPDATE users SET notepad = :user_notepad WHERE username = :user_username");
    $insert->bindValue(":user_notepad", $user_notepad);
    $insert->bindValue(":user_username", $user_username);
    $insert->execute();
}

//Userdaten in Session speichern:
$_SESSION["user_username"] = $user_username;
$_SESSION["user_email"] = $user_email;
$_SESSION["user_password"] = $user_password;
$_SESSION["user_notepad"] = $user_notepad;

?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <title>Userinterface</title>
        <?php
        include "stylesheets.php";
        ?>
    </head>
    <body class="background-color">
        
        <!-- Navbar -->
        
        <header class="header">
            <nav class="div-username">
                <ul>
                    <li>
                        <a href="#settings">
                            <?php
                            $user_username = $_SESSION["user_username"];
                            echo '<span class="username"><i class="fas fa-user"></i> ' .$user_username. '</span>';
                            ?>
                        </a>
                    </li>
                </ul>
            </nav>
            <nav class="nav">
                <i class="fa fa-bars" id="nav_icon" onclick="toggleNav()" aria-hidden="true"></i>
                <ul class="nav-ul" id="nav">
                    <li class="nav-item"><a href="#home"><i class="fas fa-home"></i> Home</a></li>
                    <li class="nav-item"><a href="#notepad"><i class="fas fa-edit"></i> Notizen</a></li>
                    <li class="nav-item"><a href="#settings"><i class="fas fa-user-cog"></i> Account</a></li>
                    <li class="nav-logout">
                        <form action="logout.php">
                            <button class="nav-button animation-push"><i class="fas fa-sign-out-alt"></i> Logout</button>
                        </form>
                    </li>
                </ul>
            </nav> 
            <script>
                function toggleNav() {
                    var x = document.getElementById("nav");
                    if (x.className === "nav-ul") {
                        x.className = "nav-ul-open";
                    } else {
                        x.className = "nav-ul";
                    }
                }
            </script>
        </header>
        
        <!-- Home -->
        <section class="fullscreen-section" id="home">
        </section>
        <hr>
        <!-- Notizen -->
        <section class="fullscreen-section" id="notepad">
            <div class="section-box shadow-red">
                <h1 class="section-title">Notizen</h1>
                <form action="user.php#notepad" method="post"> 
                    <div class="center">
                        <?php
                        $user_notepad = $_SESSION["user_notepad"];
                        echo '<textarea class="notepad" id="notepad" name="notepad" placeholder="Notizen">'.$user_notepad.'</textarea>'
                        ?><br>
                        <input class="button animation-push" type="submit" value="Speichern" name="notepad-save">
                    </div> 
                </form>
            </div>
        </section>
        <hr>
        <!-- Account -->
        <section class="fullscreen-section" id="settings">
            
        </section>
    </body>
</html>
<?php
//Sessionvariablen leeren:
unset($_SESSION["user_notepad"]);
unset($_SESSION["user_username"]);
unset($_SESSION["user_email"]);
unset($_SESSION["user_password"]);
?>