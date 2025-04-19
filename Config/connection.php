<?php
$host = "localhost"; //Host de tu base de datos
$dbname = "ferretech"; // Nombre de tu base de datos
$username = "root"; // Usuario de la base de datos
$password = ""; // Contraseña

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
