<?php
require_once "../Config/connection.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $mensaje = $_POST['mensaje'];

    $stmt = $pdo->prepare("INSERT INTO mensajes_contacto (nombre, email, mensaje, fecha) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$nombre, $email, $mensaje]);

    header("Location: ../View/pages/contact.php?success=1");
    exit;
}
?>
