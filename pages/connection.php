<?php
$servername = "localhost";
$username = "root";
$password = "";
$db_name = "recenzii";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
	$conn = mysqli_connect($servername, $username, $password, $db_name);
} catch (Exception $e) {
	error_log($e->getMessage() . "\t\t" . date("d/m/y H:i:s") . "\t\t" . $_SERVER['PHP_SELF'] . "\r\n", 3, "D:/OSPanel/domains/localhost/movie/protocol_erori.txt");

	header('Location: http://' . $_SERVER['SERVER_NAME'] . '/movie/pages/eroare.php');
	exit();
}
?>



