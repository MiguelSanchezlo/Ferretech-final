<?php
session_start();
require_once "../Config/connection.php";

if (!isset($_SESSION['usuario_id'])) {
    die("❌ Usuario no autenticado.");
}

if (empty($_SESSION['car'])) {
    die("🛒 Carrito vacío.");
}

$usuario_id = $_SESSION['usuario_id'];
$carrito = $_SESSION['car'];
$total = 0;
$errores = [];
$empresa_id = null;  // Asumimos productos de una sola empresa

foreach ($carrito as $item) {
    $stmt = $pdo->prepare("SELECT precio, stock, empresa_id FROM productos WHERE id = ?");
    $stmt->execute([$item['product_id']]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        $errores[] = "❌ Producto no encontrado: " . htmlspecialchars($item['product_name']);
        continue;
    }

    if ($item['price'] != $producto['precio']) {
        $errores[] = "⚠️ El precio de " . htmlspecialchars($item['product_name']) . " ha cambiado.";
    }

    if ($item['quantity'] > $producto['stock']) {
        $errores[] = "🚫 Stock insuficiente para " . htmlspecialchars($item['product_name']) . ".";
    }

    $total += $item['quantity'] * $producto['precio'];
    $empresa_id = $producto['empresa_id'];
}

if (!empty($errores)) {
    echo "<h3>Errores encontrados:</h3><ul>";
    foreach ($errores as $error) {
        echo "<li>$error</li>";
    }
    echo "</ul><a href='../View/Pages/car.php'>Volver al carrito</a>";
    exit;
}

// Crear pedido (pendiente hasta que se pague)
$stmt = $pdo->prepare("INSERT INTO pedidos (usuario_id, empresa_id, total, estado, fecha) VALUES (?, ?, ?, 'pendiente', NOW())");
$stmt->execute([$usuario_id, $empresa_id, $total]);
$pedido_id = $pdo->lastInsertId();

// Notificación a empresa
$mensaje = "🔔 Nuevo pedido recibido (ID: $pedido_id)";
$stmt = $pdo->prepare("INSERT INTO notificaciones (empresa_id, mensaje) VALUES (?, ?)");
$stmt->execute([$empresa_id, $mensaje]);

// Guardar detalles del pedido
foreach ($carrito as $item) {
    $stmt = $pdo->prepare("INSERT INTO detalles_pedido (pedido_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
    $stmt->execute([$pedido_id, $item['product_id'], $item['quantity'], $item['price']]);

    // Actualizar stock
    $stmt = $pdo->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");
    $stmt->execute([$item['quantity'], $item['product_id']]);
}

// Limpiar carrito
unset($_SESSION['car']);

echo "<div style='padding:20px;'>";
echo "<h2>✅ Pedido registrado con éxito</h2>";
echo "<p>ID del pedido: <strong>#$pedido_id</strong></p>";
echo "<a href='../View/Pages/product.php'>🛍️ Volver al catálogo</a>";
echo "</div>";
