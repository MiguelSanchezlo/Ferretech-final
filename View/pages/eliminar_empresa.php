<?php
session_start();
require_once "../../Config/connection.php";

// Verificación de acceso
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['empresa_id'])) {
    $empresa_id = $_POST['empresa_id'];

    // Eliminar empresa (se eliminan sus productos si hay ON DELETE CASCADE)
    $stmt = $pdo->prepare("DELETE FROM empresas WHERE id = ?");
    $stmt->execute([$empresa_id]);

    // Opcional: eliminar productos, pedidos o manejar con ON DELETE CASCADE
    header("Location: empresas_admin.php");
    exit;
}
?>
