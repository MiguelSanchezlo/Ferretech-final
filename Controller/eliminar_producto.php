<?php
session_start();
require_once "../Config/connection.php";

if (!isset($_SESSION['empresa_id'])) {
    header("Location: ../View/pages/login.php");
    exit;
}

$empresa_id = $_SESSION['empresa_id'];
$producto_id = $_GET['id'] ?? null;

if (!$producto_id || !is_numeric($producto_id)) {
    die("ID de producto inválido.");
}

// Verificar que el producto pertenece a la empresa
$stmt = $pdo->prepare("SELECT imagen FROM productos WHERE id = ? AND empresa_id = ?");
$stmt->execute([$producto_id, $empresa_id]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    die("Producto no encontrado o no autorizado.");
}

// Verificar si hay pedidos con ese producto
$stmt = $pdo->prepare("SELECT COUNT(*) FROM detalles_pedido WHERE producto_id = ?");
$stmt->execute([$producto_id]);
$tiene_pedidos = $stmt->fetchColumn() > 0;

if ($tiene_pedidos) {
    // Redirigir con mensaje
    header("Location: ../View/pages/productos_empresa.php?error=pedido_asociado");
    exit;
}

// Eliminar imagen si existe
if (!empty($producto['imagen'])) {
    $ruta_imagen = "../uploads/" . $producto['imagen'];
    if (file_exists($ruta_imagen)) {
        unlink($ruta_imagen);
    }
}

// Eliminar producto
$stmt = $pdo->prepare("DELETE FROM productos WHERE id = ? AND empresa_id = ?");
$stmt->execute([$producto_id, $empresa_id]);

header("Location: ../View/pages/productos_empresa.php?success=eliminado");
exit;
