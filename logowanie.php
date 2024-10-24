<?php
require_once "config.php";
session_start();

$message = "";

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    setcookie('username', '', time() - 3600, "/");
    setcookie('role', '', time() - 3600, "/");
    header("Location: logowanie.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['login'])) {
        // Logowanie
        $username = $_POST['username'];
        $password = $_POST['password'];
        $query = "SELECT * FROM users WHERE username='$username'";
        $result = mysqli_query($link, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            if (password_verify($password, $user['password'])) {
                // Logowanie udane
                $_SESSION['userid'] = $user['id'];
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $user['role'];
                setcookie('username', $username, time() + (86400 * 30), "/"); // Ciasteczko na 30 dni
                setcookie('role', $user['role'], time() + (86400 * 30), "/");
                setcookie('id', $user['id'], time() + (86400 * 30), "/");
                $message = "Logowanie udane!";
            } else {
                $message = "Nieprawidłowe hasło!";
            }
        } else {
            $message = "Nie znaleziono użytkownika o podanej nazwie!";
        }
    } elseif (isset($_POST['register'])) {
        // Rejestracja
        $username = $_POST['username'];
        $password = $_POST['password'];
        $role = $_POST['role'];

        // Sprawdzanie długości hasła
        if (strlen($password) < 6) {
            $message = "Hasło musi mieć co najmniej 6 znaków!";
        } else {
            $query = "SELECT * FROM users WHERE username='$username'";
            $result = mysqli_query($link, $query);

            if ($result && mysqli_num_rows($result) > 0) {
                $message = "Nazwa użytkownika jest już zajęta!";
            } else {
                $passwordHash = password_hash($password, PASSWORD_BCRYPT);
                $query = "INSERT INTO users (username, password, role, created_at) VALUES ('$username', '$passwordHash', '$role', NOW())";
                if (mysqli_query($link, $query)) {
                    $message = "Rejestracja udana!";
                } else {
                    $message = "Błąd rejestracji: " . mysqli_error($link);
                }
            }
        }
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
<!--    <h4>Zaloguj się</h4>-->
    <?php if (!empty($message)): ?>
        <p><?php echo $message; ?></p>
    <?php
        else: ?>
    <h4>Zaloguj się</h4>
    <?php endif; ?>
</div>

<?php if (!isset($_SESSION['username'])): ?>
<div class="add">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="contact">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>

        <input type="submit" name="login" value="Login">
    </form>
</div>

<div class="message">
    <h4>Lub zarejestruj, aby dodawać swoje przepisy!</h4>
</div>

<div class="add">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="contact">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>

        <label for="role">Rola:</label>
        <select id="role" name="role" required>
            <option value="author">Author</option>
            <option value="normal_user">Normal User</option>
        </select>

        <input type="submit" name="register" value="Register">
    </form>
    <?php endif ?>
</div>

</body>
</html>
