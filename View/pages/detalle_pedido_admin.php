<?php
session_start();
require_once "../../Config/connection.php";

// Verifica que sea administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: index.php");
    exit;
}

$pedido_id = $_GET['id'] ?? null;

if (!$pedido_id) {
    die("ID de pedido no proporcionado.");
}

// Obtener datos del pedido (cliente + empresa)
$stmt = $pdo->prepare("
    SELECT p.*, 
           u.nombre AS nombre_usuario,
           e.nombre_empresa
    FROM pedidos p
    JOIN usuarios u ON p.usuario_id = u.id
    JOIN empresas e ON p.empresa_id = e.id
    WHERE p.id = ?
");
$stmt->execute([$pedido_id]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
    die("Pedido no encontrado.");
}

// Obtener productos del pedido
$stmt = $pdo->prepare("
    SELECT dp.*, pr.nombre AS nombre_producto
    FROM detalles_pedido dp
    JOIN productos pr ON dp.producto_id = pr.id
    WHERE dp.pedido_id = ?
");
$stmt->execute([$pedido_id]);
$detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include_once "../Templates/cabeza.php"; ?>

<main>
    <h1>🔍 Detalle del Pedido #<?= $pedido['id'] ?></h1>

    <div class="pedido-info">
        <p><strong>Cliente:</strong> <?= $pedido['nombre_usuario'] ?></p>
        <p><strong>Empresa:</strong> <?= $pedido['nombre_empresa'] ?></p>
        <p><strong>Estado:</strong> <?= ucfirst($pedido['estado']) ?></p>
        <p><strong>Fecha:</strong> <?= $pedido['fecha'] ?></p>
    </div>

    <table class="detalle-pedido-tabla">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total = 0;
            foreach ($detalles as $item):
                $subtotal = $item['cantidad'] * $item['precio_unitario'];
                $total += $subtotal;
            ?>
            <tr>
                <td><?= $item['nombre_producto'] ?></td>
                <td><?= $item['cantidad'] ?></td>
                <td>$ <?= number_format($item['precio_unitario'], 2) ?></td>
                <td>$ <?= number_format($subtotal, 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3">Total:</td>
                <td>$ <?= number_format($total, 2) ?></td>
            </tr>
        </tfoot>
    </table>

    <a href="pedidos_admin.php" class="volver-link">← Volver a todos los pedidos</a>
</main>


<?php include_once "../Templates/pie.php"; ?>
