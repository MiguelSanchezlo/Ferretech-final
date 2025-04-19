<?php
session_start();
require_once "../../Config/connection.php";

// Verificar sesión y carrito
if (!isset($_SESSION['usuario_id']) || empty($_SESSION['car'])) {
    header("Location: index.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$carrito = $_SESSION['car'];
$total = 0;
$empresa_id = null;
$direccion = $_SESSION['direccion_envio'] ?? '';
$telefono = $_SESSION['telefono_envio'] ?? '';

// Calcular total y validar stock
foreach ($carrito as $item) {
    $stmt = $pdo->prepare("SELECT precio, stock, empresa_id FROM productos WHERE id = ?");
    $stmt->execute([$item['product_id']]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$producto || $producto['stock'] < $item['quantity']) {
        die("❌ Producto sin stock suficiente o no encontrado.");
    }

    $subtotal = $producto['precio'] * $item['quantity'];
    $total += $subtotal;
    $empresa_id = $producto['empresa_id'];
}

// Crear pedido
$stmt = $pdo->prepare("INSERT INTO pedidos (usuario_id, empresa_id, total, estado, fecha, direccion_envio, telefono_envio)
                       VALUES (?, ?, ?, 'pagado', NOW(), ?, ?)");
$stmt->execute([$usuario_id, $empresa_id, $total, $direccion, $telefono]);

$pedido_id = $pdo->lastInsertId();

// Obtener estado y método desde la redirección de MercadoPago
$estado = $_GET['status'] ?? 'desconocido';
$metodo = $_GET['payment_type'] ?? 'mercadopago';

// Registrar el pago
$stmt = $pdo->prepare("INSERT INTO pagos (pedido_id, metodo, estado, fecha) VALUES (?, ?, ?, NOW())");
$stmt->execute([$pedido_id, $metodo, $estado]);

// Registrar detalles del pedido
foreach ($carrito as $item) {
    $stmt = $pdo->prepare("INSERT INTO detalles_pedido (pedido_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
    $stmt->execute([$pedido_id, $item['product_id'], $item['quantity'], $item['price']]);

    // Actualizar stock
    $stmt = $pdo->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");
    $stmt->execute([$item['quantity'], $item['product_id']]);
}

// Limpiar carrito
unset($_SESSION['car']);
?>

<?php include_once "../Templates/cabeza.php"; ?>

<main>
    <div class="success-container">
        <h1 style="color: green;">✅ ¡Tu compra fue exitosa!</h1>
        <p>Tu pedido ha sido registrado correctamente. ID del pedido: <strong>#<?= $pedido_id ?></strong></p>
        <a href="mis_pedidos.php" class="btn">📦 Ver mis pedidos</a>
        <a href="product.php" class="btn">🛍️ Seguir comprando</a>
    </div>

</main>

<?php
unset($_SESSION['car'], $_SESSION['direccion_envio'], $_SESSION['telefono_envio']);

include_once "../Templates/pie.php"; ?>