<?php
// config.php

// Podaci za bazu podataka
$servername = "localhost";  // Adresa servera (obično localhost za lokalni razvoj)
$username = "root";         // Korisničko ime za bazu
$password = "";             // Lozinka za bazu (ostavi prazno za XAMPP default)
$dbname = "mirkolegenda";   // Ime baze podataka

// Kreiranje veze s bazom podataka
$conn = new mysqli($servername, $username, $password, $dbname);

// Provjera veze
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // Ako veza nije uspjela, ispisuje grešku i prekida daljnje izvršavanje
}

// Ako je veza uspješna, vrati $conn objekt za korištenje u drugim datotekama
?>
