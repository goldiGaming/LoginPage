<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "login";
$dbcreate = "CREATE DATABASE IF NOT EXISTS $dbname";
$dbcreatetable ="CREATE TABLE IF NOT EXISTS `users` (
`id` INT NOT NULL AUTO_INCREMENT ,
`email` VARCHAR(255) NOT NULL ,
`password` VARCHAR(255) NOT NULL ,
`username` VARCHAR(255) NOT NULL ,
`notepad` LONGTEXT ,
`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
`updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY (`id`), UNIQUE (`email`), UNIQUE (`username`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"; 

try {
    //Verbindung mit Server:
    $pdo = new PDO("mysql:host=$servername", $username, $password);
    //PDO Errormode zu Exception:
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    //Datenbank erstellen mit IF NOT EXISTS (Erstellt Datenbank falls nicht vorhanden):
    $pdo->exec($dbcreate);
    /*echo "Datenbank erfolgreich<br>";*/
    
    //Neue Verbindung zum Server (mit Datenbank):
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    //Tabelle erstellen mit IF NOT EXISTS (Erstellt Tabelle falls nicht vorhanden):
    $pdo->exec($dbcreatetable);
    /*echo "Tabelle erfolgreich<br>";*/
    
} catch(PDOException $e) {
    echo $dbcreate . "<br>" . $e->getMessage();
    echo $dbcreatetable . "<br>" . $e->getMessage();
}
?>