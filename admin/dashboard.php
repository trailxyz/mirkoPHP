<?php
// Uključivanje potrebnih datoteka
include '../includes/config.php';
include '../includes/functions.php';

// Pokreće sesiju
startSession();

// Provjera je li korisnik prijavljen i ima li administrativnu ulogu
if (!isUserLoggedIn() || !isUserAdmin()) {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Uključivanje Bootstrap CSS-a -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <div class="container mt-4">
        <!-- Navigacija s pozdravnom porukom -->
        <div class="jumbotron">
            <h1 class="display-4">Welcome, <?php echo $_SESSION['username']; ?>!</h1>
            <p class="lead">This is your admin dashboard. From here, you can manage users, articles, and other resources.</p>
            <hr class="my-4">
            <p>You are logged in as an <strong><?php echo $_SESSION['role']; ?></strong>.</p>
        </div>

        <!-- Linkovi za pristup različitim administrativnim funkcijama -->
        <h2>Admin Panel</h2>
        <div class="list-group">
            <a href="users.php" class="list-group-item list-group-item-action">Manage Users</a>
            <a href="articles.php" class="list-group-item list-group-item-action">Manage Articles</a>
            <a href="mailing_lists.php" class="list-group-item list-group-item-action">Manage Mailing Lists</a>
            <a href="page_reviews.php" class="list-group-item list-group-item-action">Manage Page Reviews</a>
            <a href="product_reviews.php" class="list-group-item list-group-item-action">Manage Product Reviews</a>
        </div>
    </div>

    <!-- Uključivanje Bootstrap JS-a -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
