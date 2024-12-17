<style>
    .body-color {
        background-color: rgb(205, 176, 51);
    }

    .title-R {
        position: absolute;
        left: 600px;
        font-family: 'Dancing Script', cursive;
    }

    .content-recenzii {
        max-width: 400px;
        margin: 0 auto;
    }

    .content-form {
        position: absolute;
        top: 130px;
        left: 550px;
    }

    #tabel {
        border-collapse: collapse;
        width: 80%;
        margin: 20px auto;
    }

    #tabel th,
    #tabel td {
        border: 1px solid black;
        padding: 8px;
        text-align: left;
    }

    #tabel th {
        background-color: #f2f2f2;
    }
</style>

<?php
require_once('connection.php');
require_once('validare.php');
require_once 'logging.php';
write_logs("view");

session_start();

$logFile = "D:/OSPanel/domains/localhost/movie/protocol_erori.txt";

if (!isset($_SESSION['user_logged_in'])) {
    header("Location: log.php");
    exit();
}

if (!isset($_GET['id_film'])) {
    header("Location: writeReview.php");
    exit();
}

$id_film = isset($_GET['id_film']) ? intval($_GET['id_film']) : 0;
if ($id_film <= 0) {
    header("Location: selectMovie.php");
    exit();
}

$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['nota_film']) && isset($_POST['review'])) {
        $nota_film = $_POST['nota_film'];
        $review = $_POST['review'];

        if (empty($nota_film) || empty($review)) {
            $errorMessage = "Vă rugăm să completați toate câmpurile.";
            error_log("Eroare adăugare recenzie: câmpuri goale" . "\t\t". date("d/m/y H:i:s") . "\t\t" . $_SERVER['PHP_SELF'] . "\t\t" . "\r\n", 3, $logFile);
        } else {
            $review = htmlspecialchars($review, ENT_QUOTES, 'UTF-8');

            $id_utilizator = $_SESSION['user_id'];

            $query = "INSERT INTO recenzii_film (nota_film, text_recenzie, data_creare, id_utilizator, id_film) VALUES ('$nota_film', '$review', NOW(), '$id_utilizator', '$id_film')";
            $result = mysqli_query($conn, $query);
            
            if ($result) {
                header("Location: writeReview.php?id_film=$id_film");
                write_logs("review added");
                exit();
            } else {
                $errorMessage = "Eroare la adăugarea recenziei. Vă rugăm să încercați din nou. ";
                error_log("Eroare adăugare recenzie " . mysqli_error($conn) . "\t\t". date("d/m/y H:i:s") . "\t\t" . $_SERVER['PHP_SELF'] . "\t\t" . "\r\n", 3, $logFile);
            }
        }
    } else {
        $errorMessage = "Vă rugăm să completați toate câmpurile.";
    }
}

// Interogare pentru a extrage recenziile filmului din baza de date
$query_recenzii = "SELECT utilizator.prenume_utilizator, recenzii_film.nota_film, recenzii_film.text_recenzie, recenzii_film.data_creare FROM recenzii_film INNER JOIN utilizator ON recenzii_film.id_utilizator = utilizator.id_utilizator WHERE recenzii_film.id_film = '$id_film'";
$result_recenzii = mysqli_query($conn, $query_recenzii);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adaugă recenzie</title>
    <link rel="stylesheet" type="text/css" href="../css/style.css">
</head>

<body class="body-color">
    <div class="menu">
        <a href="index.php">HOME</a>
        <a href="selectMovie.php">FILME</a>
    </div>

    <div class="title-R">
        <h2 id="connect">Adaugă recenzie pentru acest film</h2>
    </div>

    <?php
    if (isset($errorMessage)) {
        echo '<p style="color: red;">' . $errorMessage . '</p>';
    }
    ?>

    <div class="content-form">
        <div class="content-recenzii">
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . '?id_film=' . $id_film; ?>">
                <label for="nota_film">Nota filmului (1-10):</label>
                <input type="number" id="name" name="nota_film" min="1" max="10">

                <label for="review">Recenzie:</label>
                <textarea id="name" name="review" rows="4" cols="50"></textarea>

                <input type="submit" class="submit" value="Trimite recenzia">
            </form>
        </div>

        <div class="recenzii">
            <h3 style="text-align: center">Recenzii pentru acest film:</h3>
            <table id="tabel">
                <tr>
                    <th>Nume Utilizator</th>
                    <th>Nota Film</th>
                    <th>Recenzie</th>
                    <th>Data Creare</th>
                </tr>
                <?php
                if (isset($result_recenzii) && mysqli_num_rows($result_recenzii) > 0) {
                    while ($row_recenzie = mysqli_fetch_assoc($result_recenzii)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row_recenzie['prenume_utilizator']) . "</td>";
                        echo "<td>" . htmlspecialchars($row_recenzie['nota_film']) . "</td>";
                        echo "<td>" . htmlspecialchars($row_recenzie['text_recenzie']) . "</td>";
                        echo "<td>" . htmlspecialchars($row_recenzie['data_creare']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<p>Nu sunt recenzii pentru acest film.</p>";
                }
                ?>
            </table>
        </div>
    </div>
</body>

</html>