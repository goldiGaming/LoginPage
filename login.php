<?php

include "mysql.php";
include "errors.php";

if(isset($_POST["login"])) {

    $error = false;

    $email_username = $_POST["email-username"];
    $email = $_POST["email-username"];
    $username = $_POST["email-username"];
    $password = $_POST["password"];

    //Testen ob Email/Usernamefeld leer ist:
    if(strlen($email_username) == 0) {
        $_SESSION["email_username_empty_output"] = $email_username_empty_output;
        $error = true;
    }

    //Testen ob Passwortfeld leer ist:
    if(strlen($password) == 0) {
        $_SESSION["password_empty_output"] = $password_empty_output;
        $error = true;
    }

    //Wenn keine Fehler:
    if($error === false) {

        //Alles aus der Datenbank ziehen durch die Email oder Username:
        $search_user = $pdo->prepare("SELECT id, username, email, password FROM users WHERE username = :username OR email = :email");
        $search_user->execute(array(":username"=>$username, ":email"=>$email));
        $search_user_result = $search_user->fetch(PDO::FETCH_ASSOC);

        if($search_user_result === false) {
            //Wenn die Email und Username nicht existiert
            $_SESSION["email_username_not_found_output"] = $email_username_not_found_output;

        } else{
            //Wenn die Email oder Username existiert

            //Passwort überprüfen:
            if(password_verify($password, $search_user_result['password'])){
                //Wenn Passwort stimmt

                //ID des Users in einer Sessionvariable abspeichern:
                $_SESSION['user_id'] = $search_user_result['id'];

                //Weiter zum privaten Bereich:
                header('Location: user.php');
                exit;

            } else{
                //Wenn Passwort nicht stimmt
                $_SESSION["password_wrong_output"] = $password_wrong_output;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <title>Login</title>
        <?php 
        include "stylesheets.php";
        ?>
    </head>
    <body class="background-image">
        <div class=fullscreen-flex>
            <div class="home-login shadow-red">
                <h1 class=title> LOGIN </h1>
                <form action="login.php" method="post">
                    <div class="center">
                        <i class="icon fas fa-user"></i><input class="input-text" type="text" name="email-username" placeholder="Email/Username" autofocus autocomplete="on" maxlength="25"><br>

                        <?php

                        //Fehlermeldung ausgeben:
                        if(isset($_SESSION["email_username_empty_output"])){
                            $email_username_empty_output = $_SESSION["email_username_empty_output"];
                            echo "$email_username_empty_output";
                        }

                        //Fehlermeldung ausgeben:
                        if(isset($_SESSION["email_username_not_found_output"])){
                            $email_username_not_found_output = $_SESSION["email_username_not_found_output"];
                            echo "$email_username_not_found_output";
                        }
                        ?>

                    </div>

                    <div class="center">
                        <i class="icon fas fa-lock"></i><input class="input-text" id="password-box" type="password" name="password" placeholder="Passwort" maxlength="250">
                        <i class="far fa-eye" onclick="togglePassword()" id="password-icon"></i><br>

                        <?php

                        //Fehlermeldung ausgeben:
                        if(isset($_SESSION["password_empty_output"])){
                            $password_empty_output = $_SESSION["password_empty_output"];
                            echo "$password_empty_output";
                        }

                        //Fehlermeldung ausgeben:
                        if(isset($_SESSION["password_wrong_output"])){
                            $password_wrong_output = $_SESSION["password_wrong_output"];
                            echo "$password_wrong_output";
                        }
                        ?>

                    </div>

                    <script>
                        function togglePassword() {
                            if (document.getElementById("password-box").type === "password") {
                                //Wenn password-box auf type="password" steht, dann...
                                document.getElementById("password-box").type = "text";
                                // -> type bei bassword-box auf text (damit es sichtbar wird)
                                document.getElementById("password-icon").className = "far fa-eye-slash";
                                // -> Icon bei password-icon auf geschlossenes Auge
                            }
                            else {
                                //Wenn password-box nicht auf type="password" steht, dann...
                                document.getElementById("password-box").type = "password"
                                // -> type bei bassword-box auf password (damit es wieder als * angezeigt wird)
                                document.getElementById("password-icon").className = "far fa-eye";
                                // -> Icon bei password-icon auf offenes Auge
                            }
                        }
                    </script>

                    <div class="center">
                        <input class="button animation-push" type="submit" name="login" value="Login">
                    </div>
                    <div class="center text-forwarding">
                        <span>Du hast noch keinen Account? <a class="forwarding" href="register.php">Registrieren <i class="fas fa-arrow-right"></i></a></span>
                    </div>
                </form>
            </div>
        </div>
        <script>
            //Verschwinden des Error Textes nach 5 Sekunden:
            function Timeout_Errors() {
                var x = document.querySelectorAll(".error-text");
                var i;
                for (i = 0; i < x.length; i++) {
                    x[i].style.display = "none";
                }
            }
            setTimeout(Timeout_Errors, 5000);
        </script>
    </body>
</html>
<?php
//Sessionvariablen leeren:
unset($_SESSION["email_username_empty_output"]);
unset($_SESSION["password_empty_output"]);
unset($_SESSION["email_username_not_found_output"]);
unset($_SESSION["password_wrong_output"]);
?>