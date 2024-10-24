<?php
require_once "config.php";
date_default_timezone_set('Europe/Warsaw');
session_start();

$messageSent = false;
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $person_name = $_POST["person_name"];
    $date = date('Y-m-d H:i:s');
    $email_from = $_POST["e-mail"];
    $message = $_POST["message"];

    $query = "INSERT INTO contact(person_name, date, email_from, message)
              VALUES ('$person_name', '$date', '$email_from', '$message')";

    if (mysqli_query($link, $query)) {
        $messageSent = true;
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

<div class="message">
    <h2>Skontaktuj się z autorem strony!</h2>
</div>

<?php if ($messageSent): ?>
    <div class="message success">
        Sukces! Udało się wysłać wiadomość.
    </div>
<?php elseif ($error): ?>
    <div class="message error">
        <?php echo $error; ?>
    </div>
<?php endif; ?>

<div class="add">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="contact">
        <label for="person_name">Name:</label>
        <input type="text" id="person_name" name="person_name" required><br>

        <label for="e-mail">E-mail address:</label>
        <input type="email" id="e-mail" name="e-mail" required><br>

        <label for="message">Message:</label>
        <textarea id="message" name="message" required></textarea><br>

        <input type="submit" value="Send">
    </form>
</div>

</body>
</html>
