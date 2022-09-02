<?php
session_start();

$email_username_not_found_output = '<span class="error-text">Es wurde kein User gefunden!</span>';
$password_wrong_output = '<span class="error-text">Falsches Passwort!</span>';
$username_already_assigned_output = '<span class="error-text">Der Username ist bereits vergeben!</span>';
$email_already_assigned_output = '<span class="error-text">Die Email ist bereits vergeben!</span>';
$email_username_empty_output = '<span id="test" class="error-text">Bitte gib eine(n) Email/Username ein!</span>';
$email_empty_output = '<span id="test" class="error-text">Bitte gib eine Email ein!</span>';
$password_empty_output = '<span class="error-text">Bitte gib ein Passwort ein!</span>';
$username_empty_output = '<span class="error-text">Bitte gib ein Username ein!</span>';
$password_confirm_empty_output = '<span class="error-text">Bitte wiederhole dein Passwort!</span>';
$email_validation_output = '<span class="error-text">Ung&uuml;ltige Email!</span>';
$password_validation_output = '<span class="error-text">Ung&uuml;ltiges Passwort!</span>';
$password_confirmation_output = '<span class="error-text">Passw&ouml;rter stimmen nicht &uuml;berein!</span>';

?>