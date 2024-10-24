<?php
    require_once "config.php";
    session_start();

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
        $message = mysqli_real_escape_string($link, $_POST['message']);
        $user_id = isset($_SESSION['userid']) ? $_SESSION['userid'] : 0;
        $recipe_id = (int)$_GET['id'];
        $comment_date = date('Y-m-d H:i:s');

        $query = "INSERT INTO comments (user_id, recipe_id, message, comment_date) VALUES ('$user_id', '$recipe_id', '$message', '$comment_date')";
        if (!mysqli_query($link, $query)) {
            echo "Błąd dodawania komentarza: " . mysqli_error($link);
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

<div class="details">
    <!-- Treść bloga będzie tutaj -->
    <?php
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $query = "SELECT przepis.*, users.username FROM przepis
                  JOIN users ON przepis.user_id = users.id
                  WHERE przepis.id = $id";
        if (!$result = mysqli_query($link, $query)) {
            echo "Błąd" . '<br>';
            '</body>';
            '</html>';
        }
    }
    ?>
        <?php
        if ($row = mysqli_fetch_assoc($result)) {
            echo '<div class="recipe_details">';
            echo '<h2><a href="szczegoly.php?id=' . $row['id'] . '">' . $row['name'] . '</a></h2>';
            echo '<p><strong>Data dodania:</strong> ' . $row['recepie_date'] . '</p>';
            echo '<p><strong>Składniki:</strong> ' . $row['ingrediens'] . '</p>';
            echo '<p><strong>Przepis:</strong> ' . $row['recepie'] . '</p>';
            if (!is_null($row['tips']) && $row['tips'] !== '') {
                echo '<p><strong>Wskazówki:</strong> ' . $row['tips'] . '</p>';
            }
            echo '<p><strong>Autor:</strong> ' . $row['username'] . '</p>';
            if ((isset($_SESSION['username'])) && ($_SESSION['role'] == 'ADMIN' || $_SESSION['username'] == $row['username'])) {
                echo '<a href="dodaj_przepis.php?id=' . $row['id'] . '">Edytuj</a>';
                echo '&emsp;';
                echo '<a href="usun_przepis.php?id=' . $row['id'] . '" onclick="return confirm(\'Czy na pewno chcesz usunąć ten przepis?\');">Usuń</a>';
            }
            echo '</div>';

            // Get previous and next recipe IDs

            $prev_query = "SELECT id FROM przepis WHERE recepie_date < (SELECT recepie_date FROM przepis WHERE id = $id) ORDER BY recepie_date DESC LIMIT 1";
            $next_query = "SELECT id FROM przepis WHERE recepie_date > (SELECT recepie_date FROM przepis WHERE id = $id) ORDER BY recepie_date ASC LIMIT 1";


            $prev_result = mysqli_query($link, $prev_query);
            $next_result = mysqli_query($link, $next_query);

            $prev_id_row = mysqli_fetch_assoc($prev_result);
            $next_id_row = mysqli_fetch_assoc($next_result);

            $prev_id = isset($prev_id_row['id']) ? $prev_id_row['id'] : null;
            $next_id = isset($next_id_row['id']) ? $next_id_row['id'] : null;

            echo '<div class="navigation-buttons">';
            if ($prev_id) {
                echo '<a href="szczegoly.php?id=' . $prev_id . '" class="button">Poprzedni Przepis</a>';
            }
            if ($next_id) {
                echo '<a href="szczegoly.php?id=' . $next_id . '" class="button">Następny Przepis</a>';
            }
            echo '</div>'; ?>

            <hr>

            <div class="message_comment">
                <h5>Zostaw komentarz!</h5>
            </div>
            <div class="add">
                <form action="szczegoly.php?id=<?php echo $id; ?>" method="POST" class="comment">
                    <label for="message">Komentarz:</label>
                    <textarea id="message" name="message" required></textarea><br>
                    <input type="submit" value="Comment">
                </form>
            </div>

            <?php
            // Display comments
            $comment_query = "SELECT comments.*, users.username FROM comments
                          LEFT JOIN users ON comments.user_id = users.id
                          WHERE comments.recipe_id = $id
                          ORDER BY comments.comment_date DESC";
            $comment_result = mysqli_query($link, $comment_query);

            if (mysqli_num_rows($comment_result) > 0) {
                echo '<div class="comments">';
                while ($comment_row = mysqli_fetch_assoc($comment_result)) {
                    $username = $comment_row['username'] ? $comment_row['username'] : 'Gość';
                    echo '<div class="comment">';
                    echo '<p><strong>' . $username . '</strong> - ' . $comment_row['comment_date'] . '</p>';
                    echo '<p>' . $comment_row['message'] . '</p>';
                    echo '</div>';
                }
                echo '</div>';
            } else {
                echo '<p>Brak komentarzy.</p>';
            }
            ?>
    <?php
        } else {
            echo "Nie znaleziono przepisu.";
        }
        ?>

    </table>
</div>

</body>
</html>