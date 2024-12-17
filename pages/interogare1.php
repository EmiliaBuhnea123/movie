<?php
require_once('connection.php');

$parola = 'Alex123@';
$hashedPassword = password_hash($parola, PASSWORD_DEFAULT);

$queryManager = "INSERT INTO manager (login, prenume, parola_manager) VALUES ('Alex', 'Vasile', '$hashedPassword')";
mysqli_query($conn, $queryManager);
mysqli_close($conn);

?>
