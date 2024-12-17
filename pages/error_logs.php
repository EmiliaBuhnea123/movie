<?php
session_start();

if ($_SESSION['role'] != 'manager') {
    echo "Acces interzis!";
    exit();
}

$logFile = "D:/OSPanel/domains/localhost/movie/protocol_erori.txt";

if (file_exists($logFile)) {
    $logContents = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    echo '<table border="1" cellspacing="0" cellpadding="5">';
    echo '<tr><th>Mesaj eroare</th><th>Data</th><th>Script</th></tr>';

    foreach ($logContents as $line) {
        $parts = explode("\t\t", $line);
        $errorMessage = trim($parts[0]);
        $date = trim($parts[1]);
        $script = trim($parts[2]);

        echo '<tr>';
        echo '<td>' . htmlspecialchars($errorMessage) . '</td>';
        echo '<td>' . htmlspecialchars($date) . '</td>';
        echo '<td>' . htmlspecialchars($script) . '</td>';
        echo '</tr>';
    }
    echo '</table>';
} else {
    echo "Fișierul de loguri nu a fost găsit!";
}


