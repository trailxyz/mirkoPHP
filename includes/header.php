// header.php
<?php
include 'includes/functions.php';
startSession(); // Pokreće sesiju
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your CMS</title>
    <!-- Bootstrap ili druge CSS biblioteke -->
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="/">Your CMS</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="/">Home</a>
                </li>
                <?php if (isUserLoggedIn()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/dashboard.php">Dashboard</a>
                    </li>
                    <?php if (isUserAdmin()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/users.php">Manage Users</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                    <span class="navbar-text">
                        Welcome, <?php echo $_SESSION['username']; ?>
                    </span>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
</body>
</html>
