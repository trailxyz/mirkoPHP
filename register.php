<?php
include 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $username, $email, $password);

    if ($stmt->execute()) {
        header('Location: login.php');
    } else {
        $error = "Greška pri registraciji.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="assets/bootstrap.min.css">
    <title>Registracija</title>
</head>
<body>
<div class="container">
    <h2>Registracija</h2>
    <form method="POST">
        <div class="mb-3">
            <label>Korisničko ime</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Lozinka</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Registriraj se</button>
    </form>
    <?php if (isset($error)) echo "<p class='text-danger'>$error</p>"; ?>
</div>
</body>
</html>
