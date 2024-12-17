<?php
session_start(); 

if (!isset($_SESSION['manager_id']) || $_SESSION['role'] !== 'manager') {
    header("Location: index.php"); 
    exit();
}

$logFile = "../date_loguri.txt"; 

if (!file_exists($logFile)) {
    die("Fișierul de log nu a fost găsit.");
}

$logs = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

if (!$logs) {
    die("Nu există date de afișat.");
}

$header = explode("\t\t", array_shift($logs)); 
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Vizualizare Loguri</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Log-uri</h1>
    <table>
        <tr>
            <?php foreach ($header as $columnName): ?>
                <th><?php echo htmlspecialchars($columnName); ?></th>
            <?php endforeach; ?>
        </tr>
        
        <?php
        foreach ($logs as $log) {
            $data = explode("\t\t", $log);

            echo "<tr>";
            foreach ($data as $cell) {
                echo "<td>" . htmlspecialchars($cell) . "</td>";
            }
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>
