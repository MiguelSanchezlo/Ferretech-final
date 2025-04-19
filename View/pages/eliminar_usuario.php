<?php
session_start();
require_once "../../Config/connection.php";

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['usuario_id'])) {
    $usuario_id = $_POST['usuario_id'];

    // Verificar que no sea administrador antes de eliminar
    $stmt = $pdo->prepare("SELECT rol FROM usuarios WHERE id = ?");
    $stmt->execute([$usuario_id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && $usuario['rol'] !== 'administrador') {
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->execute([$usuario_id]);
    }

    header("Location: usuarios_admin.php");
    exit;
}
?>
