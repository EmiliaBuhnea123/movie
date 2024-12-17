<?php
require_once('connection.php');
require_once('validare.php');
require_once 'logging.php';
write_logs("view");

$registrationSuccess = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $prenume = $_POST['prenume'];
    $email = $_POST['email'];
    $parola = $_POST['parola'];

    if (empty($name)) {
        $errorMessages[] = "Câmpul nume este obligatoriu.";
        error_log("Eroare înregistrare: câmpul nume gol" . "\t\t" . date("d/m/y H:i:s") . "\t\t" . $_SERVER['PHP_SELF'] . "\t\t" . "\r\n", 3, $logFile);
    }

    if (empty($prenume)) {
        $errorMessages[] = "Câmpul prenume este obligatoriu.";
        error_log("Eroare înregistrare: câmpul prenume gol" . "\t\t" . date("d/m/y H:i:s") . "\t\t" . $_SERVER['PHP_SELF'] . "\t\t" . "\r\n", 3, $logFile);
    }

    if (empty($email)) {
        $errorMessages[] = "Câmpul email este obligatoriu.";
        error_log("Eroare înregistrare: câmpul email gol" . "\t\t" . date("d/m/y H:i:s") . "\t\t" . $_SERVER['PHP_SELF'] . "\t\t" . "\r\n", 3, $logFile);
    }

    if (empty($parola)) {
        $errorMessages[] = "Câmpul parolă este obligatoriu.";
        error_log("Eroare înregistrare: câmpul parolă gol" . "\t\t". date("d/m/y H:i:s") . "\t\t" . $_SERVER['PHP_SELF'] . "\t\t" . "\r\n", 3, $logFile);
    }

    $hashedPassword = password_hash($parola, PASSWORD_DEFAULT);

    $errorMessage = validateFieldsUserSignIn($name, $prenume, $email, $parola);
   
    if (empty($errorMessage)) {
        if (verificaAutentificare($name, $prenume, $email, $parola)) {
            $errorMessage = "Acest cont deja există. Vă rugăm să vă autentificați sau să creați alt cont nou.";
        } else {
            $adaugare = "INSERT INTO utilizator(nume_utilizator, prenume_utilizator, email_utilizator, parola_utilizator) VALUES('$name', '$prenume', '$email', '$hashedPassword')";
            mysqli_query($conn, $adaugare);
            mysqli_close($conn);
            $registrationSuccess = true;
            write_logs("sign up success"); 
        }
    }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGN UP</title>
    <link rel="stylesheet" type="text/css" href="../css/style.css">
</head>
<body>
    
<img src="../images/gr-stocks-q8P8YoR6erg-unsplash.jpg">

<div class="menu">
        <a href="index.php">HOME</a>
        <a href="sign.php">SIGN UP</a>
        <a href="log.php">LOG IN</a>
    </div>

    <div class="title"><p>Alătură-te comunității noastre pasionate de filme - Înregistrează-te</p>
    </div>  
    <div class="content">
        

    <form method="POST" action="<?php echo $_SERVER['SCRIPT_NAME']?>">
            <label for="name">Nume:</label>
            <input type="text" id="name" name="name" >

            <label for="prenume">Prenume:</label>
            <input type="text" id="name" name="prenume" >
    
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email">
    
            <label for="parola">Parolă:</label>
            <input type="password" id="parola" name="parola">
    
            <input type="submit" class="submit" value="SIGN UP">
        </form>

        <?php 
       if (!empty($errorMessage)) {
        echo '<p style="color: red;">' . $errorMessage . '</p>';
        write_logs("sign up failed"); 
    } elseif ($registrationSuccess) { 
        echo '<p class="paragraf">Felicitări! V-ați înregistrat cu succes!</p>';
    }
    ?>

    </div>

</body>

</body>
</html>


