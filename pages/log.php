<?php
require_once('connection.php');
require_once('validare.php');
require_once 'logging.php';
write_logs("view");
session_start();

$loginFailed = false;
$login = $parola = "";
$loginErr = $parolaErr = "";

$logFile = "D:/OSPanel/domains/localhost/movie/protocol_erori.txt";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['name'];
    $lastname = $_POST['prenume'];
    $password = $_POST['parola'];

    if (empty($username)) {
        $errorMessages[] = "Câmpul nume este obligatoriu.";
        error_log("Eroare autentificare: câmpul nume gol" . "\t\t" . date("d/m/y H:i:s") . "\t\t" . $_SERVER['PHP_SELF'] . "\t\t" . "\r\n", 3, $logFile);
    }

    if (empty($lastname)) {
        $errorMessages[] = "Câmpul prenume este obligatoriu.";
        error_log("Eroare autentificare: câmpul prenume gol" . "\t\t" . date("d/m/y H:i:s") . "\t\t" . $_SERVER['PHP_SELF'] . "\t\t" . "\r\n", 3, $logFile);
    }

    if (empty($password)) {
        $errorMessages[] = "Câmpul parolă este obligatoriu.";
        error_log("Eroare autentificare: câmpul parolă gol" . "\t\t". date("d/m/y H:i:s") . "\t\t" . $_SERVER['PHP_SELF'] . "\t\t" . "\r\n", 3, $logFile);
    }

    if (verificaAutentificare($username, $lastname, $password, 'utilizator')) {
        $queryUser = "SELECT id_utilizator FROM utilizator WHERE nume_utilizator = '$username' AND prenume_utilizator = '$lastname'";
        $resultUser = mysqli_query($conn, $queryUser);
        $userData = mysqli_fetch_assoc($resultUser);
        $_SESSION['user_id'] = $userData['id_utilizator'];
        $_SESSION['role'] = 'user'; 
        $_SESSION['user_logged_in'] = true;
        write_logs("user logged"); 
        header('Location: /movie/pages/selectMovie.php');
        exit();
    } else {
        error_log("Eroare autentificare: nume utilizator sau prenume incorecte pentru login: $username $lastname" . "\t\t" . date("d/m/y H:i:s") . "\t\t" . $_SERVER['PHP_SELF'] . "\t\t" . "\r\n", 3, $logFile);
        $loginFailed = true;
    }
    
    if (verificaAutentificare($username, $lastname, $password, 'manager')) {
        $queryManager = "SELECT id_manager FROM manager WHERE login = '$username' AND prenume = '$lastname'";
        $resultManager = mysqli_query($conn, $queryManager);
        $managerData = mysqli_fetch_assoc($resultManager);
        $_SESSION['manager_id'] = $managerData['id_manager'];
        $_SESSION['role'] = 'manager'; 
        $_SESSION['manager_logged_in'] = true;
        write_logs("manager logged"); 
        header('Location: /movie/pages/managerSpace.php');
        exit();
    } else {
        error_log("Eroare autentificare: nume manager sau prenume incorecte pentru login: $username $lastname" . "\t\t" . date("d/m/y H:i:s") . "\t\t" . $_SERVER['PHP_SELF'] . "\t\t" . "\r\n", 3, $logFile);
        $loginFailed = true;    

    $loginFailed = true;
    error_log("Eroare autentificare: date nevalide" . "\t\t". date("d/m/y H:i:s") . "\t\t" . $_SERVER['PHP_SELF'] . "\t\t" . "\r\n", 3, $logFile);
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOG IN</title>
    <link rel="stylesheet" type="text/css" href="../css/style.css">
</head>
<body>
<img src="../images/gr-stocks-q8P8YoR6erg-unsplash.jpg">
<div class="menu">
    <a href="index.php">HOME</a>    
    <a href="sign.php">SIGN UP</a>
    <a href="log.php">LOG IN</a>
</div>
<div class="title-log"><p>Conectează-te pentru a vedea cele mai actuale recenzii</p></div>  
<div class="content-log">
    <form method="POST" action="<?php echo $_SERVER['SCRIPT_NAME']?>">
        <label for="name">Nume:</label>
        <input type="text" id="name" name="name">

        <label for="prenume">Prenume:</label>
        <input type="text" id="prenume" name="prenume">

        <label for="parola">Parolă:</label>
        <input type="password" id="parola" name="parola">

        <input type="submit" class="submit" value="LOG IN">
    </form>

    <?php 
    if ($loginFailed) { 
        echo '<p class="paragraf">Autentificarea dumneavoastră nu a avut loc cu succes! Reîncercați sau creați un nou cont!</p>';
        write_logs("log failed"); 
    }            
    ?>
</div>
</body>
</html>
