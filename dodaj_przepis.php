<?php
require_once "config.php";
date_default_timezone_set('Europe/Warsaw');
session_start();

$recipeAdded = false;
$error = '';

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$editMode = $id !== null;

if ($editMode) {
    // Pobierz dane przepisu do edycji
    $query = "SELECT * FROM przepis WHERE id = $id";
    $result = mysqli_query($link, $query);
    $recipe = mysqli_fetch_assoc($result);
} else {
    $recipe = [
        'name' => '',
        'ingrediens' => '',
        'recepie' => '',
        'tips' => ''
    ];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $recepie_date = date('Y-m-d H:i:s');
    $name = $_POST["recepie_name"];
    $ingrediens = $_POST["ingrediens"];
    $recepie = $_POST["recepie"];
    $tips = $_POST["tips"];
    $userid = $_SESSION["userid"];

    if ($editMode) {
        // Aktualizacja istniejącego przepisu
        $query = "UPDATE przepis SET name = '$name', ingrediens = '$ingrediens', recepie = '$recepie', tips = '$tips' WHERE id = $id";
    } else {
        // Dodawanie nowego przepisu
        $query = "INSERT INTO przepis (recepie_date, name, ingrediens, recepie, tips, user_id) 
                    VALUES ('$recepie_date', '$name', '$ingrediens', '$recepie', '$tips', $userid)";
    }

    if (mysqli_query($link, $query)) {
        $recipeAdded = true;
    } else {
        $error = "Błąd: coś poszło nie tak, spróbuj ponownie.";
    }
}
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

<?php if ($recipeAdded): ?>
    <div class="message success">
        Sukces! <?php echo $editMode ? 'Udało się zaktualizować przepis.' : 'Udało się dodać nowy przepis.'; ?>
    </div>
<?php elseif ($error): ?>
    <div class="message error">
        <?php echo $error; ?>
    </div>
<?php endif; ?>

<div class="add">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . ($editMode ? "?id=$id" : ""); ?>" method="POST" class="new_recepie">
        <label for="recepie_name">Recepie name:</label>
        <input type="text" id="recepie_name" name="recepie_name" value="<?php echo htmlspecialchars($recipe['name']); ?>" required><br>

        <label for="ingrediens">Ingrediens:</label>
        <input type="text" id="ingrediens" name="ingrediens" value="<?php echo htmlspecialchars($recipe['ingrediens']); ?>" required><br>

        <label for="recepie">Recepie:</label>
        <textarea id="recepie" name="recepie" required><?php echo htmlspecialchars($recipe['recepie']); ?></textarea><br>

        <label for="tips">Tips (optional):</label>
        <input type="text" id="tips" name="tips" value="<?php echo htmlspecialchars($recipe['tips']); ?>"><br>

        <input type="submit" value="Submit">
    </form>
</div>

</body>
</html>
