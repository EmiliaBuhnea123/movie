<?php
require_once('connection.php');
require_once 'logging.php';
session_start(); 

if (isset($_SESSION['manager_id'])) {
    $user_id = $_SESSION['manager_id']; 
    $role = $_SESSION['role']; 
} else {
    $user_id = "[-]";
    $role = "[-]";
}

write_logs("view");

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $film_id = $_GET['id'];

    $delete_reviews_query = "DELETE FROM recenzii_film WHERE id_film = '$film_id'";
    $result_delete_reviews = mysqli_query($conn, $delete_reviews_query);

    if ($result_delete_reviews) {
        $delete_film_query = "DELETE FROM filme WHERE id_film = '$film_id'";
        $result_delete_film = mysqli_query($conn, $delete_film_query);

        if ($result_delete_film) {
            header("Location: managerSpace.php");
            write_logs("movie deleted");
            exit();
        } else {
            echo "Eroare la ștergerea filmului.";
            error_log("Eroare la ștergerea filmului" . "\t\t". date("d/m/y H:i:s") . "\t\t" . $_SERVER['PHP_SELF'] . "\t\t" . "\r\n", 3, $logFile);
        }
    } else {
        echo "Eroare la ștergerea recenziilor.";
    }
} else {
    header("Location: managerSpace.php");
    exit();
}
?>

