<?php
    session_start();
    require_once "config.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zdrowe Odżywianie - Przepisy</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <div class="header-content">
            <img src="kanapki.jpg" alt="Zdjęcie" class="logo">
            <div class="titles">
                <h1>Moje jedzonko Polecam</h1>
                <h3>Fajne pomysły na wege jedzonko</h3>
            </div>
        </div>
    </header>

<nav>
    <a href="index.php">Przepisy</a>
    <?php if (isset($_SESSION['role']) && ($_SESSION['role'] == 'ADMIN' || $_SESSION['role'] == 'AUTHOR')) : ?>
        <a href="dodaj_przepis.php">Dodaj przepis</a>
    <?php endif ?>
    <a href="kontakt.php">Kontakt</a>
    <?php if (isset($_SESSION['username'])): ?>
        <a href="logowanie.php?logout=true">Wyloguj się</a>
    <?php else: ?>
        <a href="logowanie.php">Logowanie/Rejestracja</a>
    <?php endif;?>
</nav>

<div class="content">
    <!-- Treść bloga będzie tutaj -->
    <?php
        $query = 'SELECT * from przepis
         ORDER BY recepie_date';
        $result = $link->query($query);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo '<div class="recipe">';
                echo '<h2><a href="szczegoly.php?id=' . $row['id'] . '">' . $row['name'] . '</a></h2>';
                echo '<p><strong>Data dodania:</strong> ' . $row['recepie_date'] . '</p>';
                echo '</div>';
            }
        } else {
            echo "Błąd" . '<br>';
            '</div>
            </body>
            </html>';
        }
    ?>
</div>

</body>
</html>