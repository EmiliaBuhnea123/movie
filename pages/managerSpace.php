<?php 
require_once('connection.php');
require_once('validare.php');
require_once 'logging.php';
write_logs("view");

session_start();

$logFile = "D:/OSPanel/domains/localhost/movie/protocol_erori.txt";

if (!isset($_SESSION['manager_logged_in'])|| $_SESSION['manager_logged_in'] !== true) {
    header("Location: log.php");
    exit();
}

if (isset($_GET['logout'])) {
    $_SESSION = array();
    write_logs("log out");
    session_destroy();
    header("Location: log.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titlu = $_POST['titlu'];
    $gen = $_POST['gen'];
    $year = $_POST['year'];
    $poza = $_FILES['poza'];

    if (empty($titlu)) {
        $errorMessages[] = "Câmpul titlu este obligatoriu.";
        error_log("Eroare adăugare film: câmpul titlu gol" . "\t\t" . date("d/m/y H:i:s") . "\t\t" . $_SERVER['PHP_SELF'] . "\t\t" . "\r\n", 3, $logFile);
    }

    if (empty($gen)) {
        $errorMessages[] = "Câmpul gen este obligatoriu.";
        error_log("Eroare adăugare film: câmpul gen gol" . "\t\t" . date("d/m/y H:i:s") . "\t\t" . $_SERVER['PHP_SELF'] . "\t\t" . "\r\n", 3, $logFile);
    }

    if (empty($year)) {
        $errorMessages[] = "Câmpul an este obligatoriu.";
        error_log("Eroare adăugare film: câmpul an gol" . "\t\t". date("d/m/y H:i:s") . "\t\t" . $_SERVER['PHP_SELF'] . "\t\t" . "\r\n", 3, $logFile);
    }

    if (empty($poza)) {
        $errorMessages[] = "Câmpul poză este obligatoriu.";
        error_log("Eroare adăugare film: câmpul poză gol" . "\t\t". date("d/m/y H:i:s") . "\t\t" . $_SERVER['PHP_SELF'] . "\t\t" . "\r\n", 3, $logFile);
    }


    $locatie_temporara = $poza['tmp_name'];
    $locatie_permanenta = '../imaginiFilme/' . $poza['name'];
    move_uploaded_file($locatie_temporara, $locatie_permanenta);

    $errorMessage = validateAddFilmFields($titlu, $gen, $year, $poza);

    if (empty($errorMessage)) {
        $query = "INSERT INTO filme (titlu_film, gen_film, an_lansare, poza_film) 
            VALUES ('$titlu', '$gen', '$year', '$locatie_permanenta')";
        
        if (mysqli_query($conn, $query)) {
            echo "<p class='info'>Filmul a fost adăugat cu succes!</p>";
            write_logs("movie added");
        } else {
            echo "<p class='error'>Eroare la inserția în baza de date: " . mysqli_error($conn) . "</p>";
            error_log("Eroare adăugare film" . "\t\t". date("d/m/y H:i:s") . "\t\t" . $_SERVER['PHP_SELF'] . "\t\t" . "\r\n", 3, $logFile);
        }
    }

}

$query_filme = "SELECT * FROM filme";
$result_filme = mysqli_query($conn, $query_filme);

$query_recenzii = "SELECT * FROM recenzii_film WHERE id_film = '$row_filme[id_film]'";
$result_recenzii = mysqli_query($conn, $query_recenzii);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
<title>Manager Space</title>
<link rel="stylesheet" type="text/css" href="../css/style.css">
<link rel="stylesheet" type="text/css" href="../css/styleManager.css">
</head>
<body>

<div id="delogareManager">
    <a href="?logout=true">DELOGARE</a>
</div>

<div id="delogareManager">
    <a href="../pages/view_logs.php">Vezi fișierul de log-uri</a><br>
    <a href="../pages/error_logs.php">Vezi logurile de erori</a>
</div>

<h3 style="text-align: center;">Adăugare Film Nou</h3>

<div class="contentManager">
    <form method="POST" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" enctype="multipart/form-data">
        <label for="titlu">Titlu Film:</label>
        <input type="text" id="name" name="titlu" >

        <label for="gen">Gen Film:</label>
        <input type="text" id="name" name="gen" >
    
        <label for="year">An lansare:</label>
        <input type="text" id="name" name="year">
    
        <label for="poza">Adăugați poza:</label>
        <input type="file" id="name" name="poza">

        <input type="submit" class="submit" value="ADAUGĂ FILM">
    </form>

    <?php 
    if (isset($errorMessage)) {
        echo '<p style="color: red;">' . $errorMessage . '</p>';
    }
    if (isset($successMessage)) {
        echo '<p style="color: green;">' . $successMessage . '</p>';
    }
    ?>

    <h3 class="filmeBD">Filme înregistrate</h3>
    <table id="actions">
        <tr>
            <th>Titlu Film</th>
            <th>Gen Film</th>
            <th>An Lansare</th>
            <th>Acțiuni</th>
        </tr>
        <?php 
        while ($row_filme = mysqli_fetch_assoc($result_filme)) {
            echo "<tr>";
            echo "<td>" . $row_filme['titlu_film'] . "</td>";
            echo "<td>" . $row_filme['gen_film'] . "</td>";
            echo "<td>" . $row_filme['an_lansare'] . "</td>";
            echo '<td><a href="delete_film.php?id=' . $row_filme['id_film'] . '">Ștergere</a></td>';
            echo "</tr>";
        }
        ?>
    </table>
</div>

</body>
</html>
