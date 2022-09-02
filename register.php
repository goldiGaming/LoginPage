<?php

include "mysql.php";
include "errors.php";

if(isset($_POST["register"])) {

    $error = false;

    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $password_confirm = $_POST["password-confirm"];

    //Testen ob Usernamefeld leer ist:
    if(strlen($username) == 0) {
        $_SESSION["username_empty_output"] = $username_empty_output;
        $error = true;
    }

    //Testen ob Emailfeld leer ist:
    if(strlen($email) == 0) {
        $_SESSION["email_empty_output"] = $email_empty_output;
        $error = true;
    } else 
        //Testen ob Email valide ist:
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION["email_validation_output"] = $email_validation_output;
            $error = true;
        }

    //Testen ob Passwortfeld leer ist:
    if(strlen($password) == 0) {
        $_SESSION["password_empty_output"] = $password_empty_output;
        $error = true;
    }

    //Testen ob Passwortbestätigungsfeld leer ist:
    if(strlen($password_confirm) == 0 && strlen($password) != 0) {
        $_SESSION["password_confirm_empty_output"] = $password_confirm_empty_output;
        $error = true;
    }

    //Wenn keine Fehler:
    if($error === false) {

        //Zählen wie oft der Username bereits genutzt wurde:
        $search_username = $pdo->prepare("SELECT COUNT(username) AS num FROM users WHERE username = :username");
        $search_username->bindValue(":username",$username);
        $search_username->execute();
        $search_username_result = $search_username->fetch(PDO::FETCH_ASSOC);

        //Zählen wie oft die Email bereits genutzt wurde:
        $search_email = $pdo->prepare("SELECT COUNT(email) AS num FROM users WHERE email = :email");
        $search_email->bindValue(":email",$email);
        $search_email->execute();
        $search_email_result = $search_email->fetch(PDO::FETCH_ASSOC);

        //Testen ob der Username bereits vergeben ist:
        if($search_username_result["num"] > 0) {
            $_SESSION["username_already_assigned_output"] = $username_already_assigned_output;
            $error = true;
        }

        //Testen ob die Email bereits vergeben ist:
        if($search_email_result["num"] > 0) {
            $_SESSION["email_already_assigned_output"] = $email_already_assigned_output;
            $error = true;
        }

        //Testen ob Passwort valide ist:
        $password_uppercase = preg_match("@[A-Z]@", $password);
        $password_lowercase = preg_match("@[a-z]@", $password);
        $password_number = preg_match("@[0-9]@", $password);

        if($password_uppercase && $password_lowercase && $password_number && strlen($password) >= 5) {

            //Testen ob Passwörter übereinstimmen:
            if($password != $password_confirm) {
                $_SESSION["password_confirmation_output"] = $password_confirmation_output;
                $error = true;
            }
        } else {
            $_SESSION["password_validation_output"] = $password_validation_output;
            $error = true;
        }

        //Wenn keine Fehler ( -> Username und Email wurden nicht bereits genutzt):
        if($error === false) {

            //Passwort verschlüsseln:
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            
            //User erstellen:
            $insert = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
            $insert->bindValue(":username", $username);
            $insert->bindValue(":email", $email);
            $insert->bindValue(":password", $passwordHash);

            $result = $insert->execute();

            if($result) {

                //Automatischer Login nach Registrierung:
                $search_user = $pdo->prepare("SELECT id FROM users WHERE email = :email");
                $search_user->bindValue(":email",$email);
                $search_user->execute();
                $search_user_result = $search_user->fetch();

                //ID des Users in einer Sessionvariable abspeichern:
                $_SESSION['user_id'] = $search_user_result['id'];

                //Weiter zum privaten Bereich:
                header('Location: user.php');
                exit;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <title>Register</title>
        <?php 
        include "stylesheets.php";
        ?>
    </head>
    <body class="background-image">
        <div class=fullscreen-flex>
            <div class="home-login shadow-red">
                <h1 class=title> REGISTRIEREN </h1>
                <form action="register.php" method="post">
                    <div class="center">
                        <i class="icon fas fa-user-circle"></i><input class="input-text" type="text" name="username" placeholder="Username" maxlength="25"><br>

                        <?php

                        //Fehlermeldung ausgeben:
                        if(isset($_SESSION["username_empty_output"])){
                            $username_empty_output = $_SESSION["username_empty_output"];
                            echo "$username_empty_output";
                        }

                        //Fehlermeldung ausgeben:
                        if(isset($_SESSION["username_already_assigned_output"])){
                            $username_already_assigned_output = $_SESSION["username_already_assigned_output"];
                            echo "$username_already_assigned_output";
                        }
                        ?>

                    </div>

                    <div class="center">
                        <i class="icon fas fa-user"></i><input class="input-text" type="text" name="email" placeholder="Email" autocomplete="on" maxlength="250"><br>

                        <?php

                        //Fehlermeldung ausgeben:
                        if(isset($_SESSION["email_empty_output"])){
                            $email_empty_output = $_SESSION["email_empty_output"];
                            echo "$email_empty_output";
                        }

                        //Fehlermeldung ausgeben:
                        if(isset($_SESSION["email_validation_output"])){
                            $email_validation_output = $_SESSION["email_validation_output"];
                            echo "$email_validation_output";
                        }

                        //Fehlermeldung ausgeben:
                        if(isset($_SESSION["email_already_assigned_output"])){
                            $email_already_assigned_output = $_SESSION["email_already_assigned_output"];
                            echo "$email_already_assigned_output";
                        }
                        ?>

                    </div>

                    <div class="center">
                        <i class="icon fas fa-lock"></i><input class="input-text" id="password-box" type="password" name="password" placeholder="Passwort" maxlength="250">
                        <i class="far fa-eye" onclick="togglePassword()" id="password-icon"></i>
                        <div class="right-tooltip"><i class="far fa-question-circle"></i>
                            <span class="right-tooltipcontent shadow-red">
                                <span class="right-tooltiptext">Mindestens 5 Zeichen</span><br>
                                <span class="right-tooltiptext">Mindestens 1 Nummer</span><br>
                                <span class="right-tooltiptext">Mindestens 1 Großbuchstabe</span><br>
                                <span class="right-tooltiptext">Mindestens 1 Kleinbuchstabe</span><br>
                            </span>
                        </div><br>

                        <?php

                        //Fehlermeldung ausgeben:
                        if(isset($_SESSION["password_empty_output"])){
                            $password_empty_output = $_SESSION["password_empty_output"];
                            echo "$password_empty_output";
                        }

                        //Fehlermeldung ausgeben:
                        if(isset($_SESSION["password_validation_output"])){
                            $password_validation_output = $_SESSION["password_validation_output"];
                            echo "$password_validation_output";
                        }

                        //Fehlermeldung ausgeben:
                        if(isset($_SESSION["password_confirmation_output"])){
                            $password_confirmation_output = $_SESSION["password_confirmation_output"];
                            echo "$password_confirmation_output";
                        }
                        ?>

                    </div>

                    <div class="center">
                        <i class="icon fas fa-lock"></i><input class="input-text" id="password-confirm" type="password" name="password-confirm" placeholder="Passwort best&auml;tigen" maxlength="250">
                        <i class="far fa-eye" onclick="toggleConfirmPassword()" id="password-confirm-icon"></i><br>

                        <?php

                        //Fehlermeldung ausgeben:
                        if(isset($_SESSION["password_confirm_empty_output"])){
                            $password_confirm_empty_output = $_SESSION["password_confirm_empty_output"];
                            echo "$password_confirm_empty_output";
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

                        function toggleConfirmPassword() {
                            if (document.getElementById("password-confirm").type === "password") {
                                document.getElementById("password-confirm").type = "text";
                                document.getElementById("password-confirm-icon").className = "far fa-eye-slash";
                            }
                            else {
                                document.getElementById("password-confirm").type = "password"
                                document.getElementById("password-confirm-icon").className = "far fa-eye";
                            }
                        }
                    </script>

                    <div class="center">
                        <input class="button animation-push" type="submit" name="register" value="Registrieren">
                    </div>
                    <div class="center text-forwarding">
                        <span>Du hast bereits einen Account? <a class="forwarding" href="login.php">Login <i class="fas fa-arrow-right"></i></a></span>
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
unset($_SESSION["username_empty_output"]);
unset($_SESSION["email_empty_output"]);
unset($_SESSION["password_empty_output"]);
unset($_SESSION["password_confirm_empty_output"]);
unset($_SESSION["email_validation_output"]);
unset($_SESSION["password_validation_output"]);
unset($_SESSION["password_confirmation_output"]);
unset($_SESSION["username_already_assigned_output"]);
unset($_SESSION["email_already_assigned_output"]);
?>