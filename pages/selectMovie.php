<?php
require_once('connection.php');
require_once 'logging.php';
write_logs("view");
session_start();

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: log.php');
    exit();
}

if (isset($_GET['logout'])) {
    $_SESSION = array();
    write_logs("log out");
    session_destroy();
    header("Location: log.php");
    exit();
}

$queryGenuri = "SELECT DISTINCT gen_film FROM filme";
$resultGenuri = mysqli_query($conn, $queryGenuri);

$resultFilme = null;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['gen_film'])) {
    $genAles = $_POST['gen_film'];

    if (!empty($genAles)) {
        write_logs("selected the genre: " . htmlspecialchars($genAles));
    } else {
        write_logs("selected the genre: none");
    }
    
    if (!empty($genAles)) {
        $queryFilme = "SELECT id_film, titlu_film, gen_film, an_lansare, poza_film FROM filme WHERE gen_film = '$genAles'";
        $resultFilme = mysqli_query($conn, $queryFilme);
    } else {
        $resultFilme = null;
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select</title>
    <link rel="stylesheet" type="text/css" href="../css/styleMovie.css">
</head>

<body>
    <div class="menu">
        <a href="index.php">HOME</a>
        <?php
        if (isset($_SESSION['user_logged_in'])) {
            echo '<div id="delogare"><a href="?logout=true">DELOGARE</a></div>';
        }
        ?>
    </div>

    <div class="container">
        <form method="POST" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>">
            <label for="gen_film">Selectați genul de film care vă interesează:</label>
            <select id="gen_film" name="gen_film">
                <option value="">Alegeți genul de film</option>
                <?php while ($row = mysqli_fetch_assoc($resultGenuri)) { ?>
                    <option value="<?php echo htmlspecialchars($row['gen_film']); ?>">
                        <?php echo htmlspecialchars($row['gen_film']); ?>
                    </option>
                <?php } ?>
            </select>
            <input type="submit" value="Afișează filmele">
        </form>

        <div class="container-filme">
            <?php if (!is_null($resultFilme) && mysqli_num_rows($resultFilme) > 0) { ?>
                <h2>Rezultate</h2>
                <div class="filme-lista">
                    <?php while ($film = mysqli_fetch_assoc($resultFilme)) { ?>
                        <a href="writeReview.php?id_film=<?php echo urlencode($film['id_film']); ?>">
                            <div class="film-item">
                                <img src="<?php echo htmlspecialchars($film['poza_film']); ?>" alt="Poza film" />
                                <h3><?php echo htmlspecialchars($film['titlu_film']); ?></h3>
                                <p>Anul lansării: <?php echo htmlspecialchars($film['an_lansare']); ?></p>
                            </div>
                        </a>
                    <?php } ?>
                </div>
            <?php } else {
                echo "<p style='color:red'>Nu au fost găsite filme pentru acest gen!</p>";
            } ?>
        </div>

</body>

</html>