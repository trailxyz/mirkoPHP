<?php
// Uključivanje potrebnih datoteka
include '../includes/config.php';
include '../includes/functions.php';

// Pokreće sesiju
startSession();

// Provjera je li korisnik prijavljen i ima li administrativnu ulogu
if (!isUserLoggedIn()) {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Uključivanje Bootstrap CSS-a -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <div class="container mt-4">
        <!-- Navigacija s pozdravnom porukom -->
        <div class="jumbotron">
            <h1 class="display-4">Welcome, <?php echo $_SESSION['username']; ?>!</h1>
            <p class="lead">tu se moze sve i svasta vidit</p>
            <hr class="my-4">
            <p>Logirani ste kao <strong><?php echo $_SESSION['role']; ?></strong>.</p>
        </div>

        <!-- Linkovi za pristup različitim administrativnim funkcijama -->
        <h2>Panel</h2>
        <div class="list-group">
            <a href="articles.php" class="list-group-item list-group-item-action">clankovi</a>
            <a href="mailing_lists.php" class="list-group-item list-group-item-action">mejling lista</a>
            <a href="page_reviews.php" class="list-group-item list-group-item-action">rivjuovi porno sajtova</a>
            <a href="product_reviews.php" class="list-group-item list-group-item-action">rivjuovi dildacha</a>
        </div>
    </div>

    <!-- Uključivanje Bootstrap JS-a -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
