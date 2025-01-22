<?php

// Pokreće sesiju
if (!function_exists('startSession')) {
    function startSession() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }
}

// Prijava korisnika
if (!function_exists('loginUser')) {
    function loginUser($conn, $username, $password) {
        $sql = "SELECT id, username, role, password FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $db_username, $db_role, $db_password);
            $stmt->fetch();

            // Provjera lozinke
            if (password_verify($password, $db_password)) {
                // Pohranjivanje podataka u sesiju
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $db_username;
                $_SESSION['role'] = $db_role;
                return true;
            }
        }
        return false;
    }
}

// Provjera je li korisnik prijavljen
if (!function_exists('isUserLoggedIn')) {
    function isUserLoggedIn() {
        return isset($_SESSION['user_id']);
    }
}

// Provjera uloge korisnika
if (!function_exists('isUserAdmin')) {
    function isUserAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
}

// Odjava korisnika
if (!function_exists('logoutUser')) {
    function logoutUser() {
        session_start();
        session_destroy();
        header("Location: login.php");
        exit();
    }
}

// Rukovanje greškama
if (!function_exists('handleError')) {
    function handleError($errorMessage) {
        $_SESSION['error_message'] = $errorMessage;
        header("Location: error.php");
        exit();
    }
}
?>