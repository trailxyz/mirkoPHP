// login.php
<?php
include 'includes/config.php';
include 'includes/functions.php';

startSession(); // Pokreće sesiju

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prijava korisnika
    if (loginUser($conn, $username, $password)) {
        header("Location: admin/dashboard.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Invalid credentials!</div>";
    }
}
?>

<form method="POST">
    <div class="form-group">
        <label for="username">Username</label>
        <input type="text" class="form-control" name="username" required>
    </div>
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" class="form-control" name="password" required>
    </div>
    <button type="submit" class="btn btn-primary">Login</button>
</form>
