<?php
$logFile = "D:/OSPanel/domains/localhost/movie/protocol_erori.txt";

if (!file_exists($logFile)) {
    $file = fopen($logFile, "w");
    if ($file) {
        fclose($file);
    } else {
        die("Nu s-a putut crea fișierul de loguri.");
    }
}

function validateName($name)
{
    //numele conține doar litere, spații, cratime și diacritice, având între 2 și 30 de caractere.
    return preg_match("/^[A-Za-zăâșțîĂÂȘȚÎ\s-]{2,30}(-[A-Za-zăâșțîĂÂȘȚÎ\s-]{2,30}){0,3}$/", $name);
}

function validatePrenume($prenume)
{
    return preg_match("/^[A-Za-zăâșțîĂÂȘȚÎ\s-]{2,30}(-[A-Za-zăâșțîĂÂȘȚÎ\s-]{2,30}){0,3}$/", $prenume);
}

function validateEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePassword($parola)
{
    //parola respectă următoarele criterii: minim 8 caractere, cel puțin o literă mică, o literă mare, un număr și un caracter special.
    return preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\w\s]).{8,}$/", $parola);
}

function validateFieldsUserSignIn($name, $prenume, $email, $parola)
{
    global $logFile;
    if (empty($name) || empty($prenume) || empty($email) || empty($parola)) {
        error_log("Eroare înregistrare: Câmpuri goale" . "\t\t" . date("d/m/y H:i:s") . "\t\t" . $_SERVER['PHP_SELF'] . "\t\t" . "\r\n", 3, $logFile);
        return "Eroare: Toate câmpurile trebuie completate!";
    }
    if (!validateName($name)) {
        error_log("Eroare înregistrare: Câmpul nume nevaild" . "\t\t" . date("d/m/y H:i:s") . "\t\t" . $_SERVER['PHP_SELF'] . "\t\t" . "\r\n", 3, $logFile);
        return "Eroare: Numele trebuie să conțină doar litere mici (a-z), litere mari (A-Z), cifre (0-9), puncte (.), sau sublinieri (_) și să aibă lungimea între 4 și 20 de caractere.!";
    }
    if (!validatePrenume($prenume)) {
        error_log("Eroare înregistrare: Câmpul prenume nevalid" . "\t\t" . date("d/m/y H:i:s") . "\t\t" . $_SERVER['PHP_SELF'] . "\t\t" . "\r\n", 3, $logFile);
        return "Eroare: Prenumele trebuie să conțină doar litere mici (a-z), litere mari (A-Z), cifre (0-9), puncte (.), sau sublinieri (_) și să aibă lungimea între 4 și 20 de caractere.!";    }
    if (!validateEmail($email)) {
        error_log("Eroare înregistrare: Email nevalid" . "\t\t" . date("d/m/y H:i:s") . "\t\t" . $_SERVER['PHP_SELF'] . "\t\t" . "\r\n", 3, $logFile);
        return "Email-ul introdus nu este valid.";
    }
    if (!validatePassword($parola)) {
        error_log("Eroare înregistrare: Parola nevalidă" . "\t\t" . date("d/m/y H:i:s") . "\t\t" . $_SERVER['PHP_SELF'] . "\t\t" . "\r\n", 3, $logFile);
        return "Parola trebuie să conțină cel puțin o literă mică, o literă mare, un număr și un simbol special și să aibă cel puțin 8 caractere.";
    }
    return "";
}

require_once('connection.php');

function verificaAutentificare($username, $prenume, $parola, $tipCont)
{
    global $conn;
    $username = mysqli_real_escape_string($conn, $username);
    $prenume = mysqli_real_escape_string($conn, $prenume);
    $parola = mysqli_real_escape_string($conn, $parola);

    if ($tipCont == 'utilizator') {
        $query = "SELECT parola_utilizator FROM utilizator WHERE nume_utilizator = '$username' AND prenume_utilizator = '$prenume'";
    } else {
        $query = "SELECT parola_manager FROM manager WHERE login = '$username' AND prenume = '$prenume'";
    }

    $rezultat = mysqli_query($conn, $query);
    if (!$rezultat) {
        die('Eroare la interogare: ' . mysqli_error($conn));
    }

    $numarRanduri = mysqli_num_rows($rezultat);
    if ($numarRanduri > 0) {
        $row = mysqli_fetch_assoc($rezultat);
        $storedPasswordHash = ($tipCont == 'utilizator') ? $row['parola_utilizator'] : $row['parola_manager'];

        if (password_verify($parola, $storedPasswordHash)) {
            return true;
        }
    }

    return false;
}

//add film
function validateTitlu($titlu)
{
    return preg_match("/^[\w\s\-]{2,30}$/", $titlu);
}

function validateGen($gen)
{
    return preg_match("/^[\w\s\-]{2,30}$/", $gen);
}
function validateYear($year)
{
    if (filter_var($year, FILTER_VALIDATE_INT) && strlen($year) == 4) {
        return true;
    }
    return false;
}

function validateImage($poza)
{
    $upload_dir = "../imaginiFilme/";

    $errorMessage = "";

    if (!empty($poza["name"])) {
        $file_path = $upload_dir . basename($poza["name"]);

        if (file_exists($file_path)) {
            $errorMessage .= "<span class='error'>Fișierul există deja!</span><br />";
        }

        if ($poza["size"] > 5 * 1024 * 1024) {
            $errorMessage .= "<span class='error'>Fișierul încărcat este prea voluminos!</span><br />";
        }

        $file_extension = pathinfo($file_path, PATHINFO_EXTENSION);

        if (!in_array(strtolower($file_extension), ['jpeg', 'jpg', 'png'])) {
            $errorMessage .= "<span class='error'>Fișierul trebuie să fie de tipul JPEG, PNG sau JPG!</span><br />";
        }

        if (empty($errorMessage)) {
            if (move_uploaded_file($poza["tmp_name"], $file_path)) {
                "<span class='info'>Fișierul " . basename($poza["name"]) . " a fost încărcat cu succes!</span><br />";
            } else {
                $errorMessage .= "<span class='error'>Încărcarea fișierului a eșuat dintr-un motiv necunoscut.</span><br />";
            }
        }
    } else {
        $errorMessage .= "<span class='error'>Nu ați încărcat niciun fișier!</span><br />";
    }

    return $errorMessage;
}


function validateAddFilmFields($titlu, $gen, $year, $poza)
{
    global $logFile;
    if (empty($titlu) || empty($gen) || empty($year) || empty($poza)) {
        error_log("Eroare adăugare film: câmpuri goale" . "\t\t" . date("d/m/y H:i:s") . "\t\t" . $_SERVER['PHP_SELF'] . "\t\t" . "\r\n", 3, $logFile);
        return "Eroare: Toate câmpurile trebuie completate!";
    }
    if (!validateTitlu($titlu)) {
        error_log("Eroare adăugare film: Titlu film nevalid" . "\t\t" . date("d/m/y H:i:s") . "\t\t" . $_SERVER['PHP_SELF'] . "\t\t" . "\r\n", 3, $logFile);
        return "Titlul poate să conțină litere, opțional - și cifre (maxim 30 de caractere)!";
    }
    if (!validateGen($gen)) {
        error_log("Eroare adăugare film: gen film nevalid" . "\t\t" . date("d/m/y H:i:s") . "\t\t" . $_SERVER['PHP_SELF'] . "\t\t" . "\r\n", 3, $logFile);
        return "Genul filmului poate să conțină litere, - opțional (maxim 30 de caractere)!";
    }
    if (!validateYear($year)) {
        error_log("Eroare adăugare film: an film nevalid" . "\t\t" . date("d/m/y H:i:s") . "\t\t" . $_SERVER['PHP_SELF'] . "\t\t" . "\r\n", 3, $logFile);
        return "Anul trebuie să conțină 4 cifre!";
    }
    if (!validateImage($poza)) {
        error_log("Eroare adăugare film: poză film nevalidă" . "\t\t" . date("d/m/y H:i:s") . "\t\t" . $_SERVER['PHP_SELF'] . "\t\t" . "\r\n", 3, $logFile);
        return "Imaginea trebuie să fie de tipul JPEG, PNG sau JPG și să nu depășească 5 MB!";
    }

    return "";
}
