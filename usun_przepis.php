<?php
require_once "config.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Sprawdź czy przepis istnieje
    $query = "SELECT * FROM przepis WHERE id = $id";
    $result = mysqli_query($link, $query);

    if (mysqli_num_rows($result) > 0) {
        // Usuń przepis
        $delete_query = "DELETE FROM przepis WHERE id = $id";
        if (mysqli_query($link, $delete_query)) {
            header("Location: index.php"); // Przekieruj na stronę główną lub inną odpowiednią stronę
            exit();
        } else {
            echo "Błąd podczas usuwania przepisu.";
        }
    } else {
        echo "Przepis nie istnieje.";
    }
} else {
    echo "Nieprawidłowe żądanie.";
}
?>