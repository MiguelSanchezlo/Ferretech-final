<?php
require_once "../Config/connection.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $empresa_id = $_POST['empresa_id'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $mensaje = $_POST['mensaje'];

    $stmt = $pdo->prepare("INSERT INTO mensajes_empresa (empresa_id, nombre, email, mensaje, fecha) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$empresa_id, $nombre, $email, $mensaje]);

    header("Location: ../View/pages/contacto_empresa.php?id=$empresa_id&enviado=1");
    exit;
}
?>
