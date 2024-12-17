<?php
function write_logs($action) {
    $fileName = "D:/OSPanel/domains/localhost/movie/date_loguri.txt";
    
    $fieldsSeparator = "\t\t";
    $logLine = date("d/m/y H:i:s") . $fieldsSeparator . $_SERVER['PHP_SELF'] . $fieldsSeparator . session_id() . $fieldsSeparator . 
    (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : (isset($_SESSION['manager_id']) ? $_SESSION['manager_id'] : "[-]")) . $fieldsSeparator . 
    (isset($_SESSION['role']) ? $_SESSION['role'] : "[-]") . $fieldsSeparator . $action;

    $fieldsSeparatorAntet = "\t\t";

    if (!file_exists($fileName)) {
        $fileO = fopen($fileName, "w") or die("Eroare!");
        $antet = "Data ora" . $fieldsSeparatorAntet . "Fisier accesat" . $fieldsSeparatorAntet . "ID sesiune" . $fieldsSeparatorAntet . "ID user" . $fieldsSeparatorAntet . "Rol" . $fieldsSeparatorAntet . "Actiune";
        fwrite($fileO, $antet . "\r\n");
        fclose($fileO);
    }

    $fileO = fopen($fileName, "a") or die("Eroare!");
    fwrite($fileO, $logLine . "\r\n");
    fclose($fileO);
}
?>
