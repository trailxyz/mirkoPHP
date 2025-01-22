// footer.php
<?php
include 'includes/functions.php';
startSession(); // Pokreće sesiju
?>

<footer class="footer mt-auto py-3 bg-light">
    <div class="container">
        <span class="text-muted">© 2025 PHP programiranje - Mirko Vrlec</span>
        <?php if (isUserLoggedIn()): ?>
            <a href="logout.php" class="btn btn-danger float-right">Logout</a>
        <?php endif; ?>
    </div>
</footer>
</body>
</html>
