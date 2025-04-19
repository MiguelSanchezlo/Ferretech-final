<?php
session_start();
require_once "../../Config/connection.php";

if (!isset($_SESSION['empresa_id'])) {
    header("Location: login.php");
    exit;
}

$empresa_id = $_SESSION['empresa_id'];

$pedido_id = $_GET['id'] ?? null;
if (!$pedido_id || !is_numeric($pedido_id)) {
    die("ID de pedido inválido.");
}

// Verificar que el pedido pertenezca a la empresa
$stmt = $pdo->prepare("
    SELECT p.*, u.nombre, u.apellido, u.email, g.estado AS estado_pago, g.metodo 
    FROM pedidos p
    JOIN usuarios u ON p.usuario_id = u.id
    LEFT JOIN pagos g ON g.pedido_id = p.id
    WHERE p.id = ? AND p.empresa_id = ?
");
$stmt->execute([$pedido_id, $empresa_id]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
    die("Pedido no encontrado o no autorizado.");
}

// Obtener productos del pedido
$stmt = $pdo->prepare("
    SELECT dp.cantidad, dp.precio_unitario, pr.nombre AS nombre_producto
    FROM detalles_pedido dp
    JOIN productos pr ON dp.producto_id = pr.id
    WHERE dp.pedido_id = ?
");
$stmt->execute([$pedido_id]);
$detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

include_once "../Templates/cabeza.php";
?>

<main style="padding: 20px;">
    <h2>📦 Detalle del Pedido #<?= $pedido['id'] ?></h2>

    <p><strong>Cliente:</strong> <?= htmlspecialchars($pedido['nombre'] . " " . $pedido['apellido']) ?> (<?= $pedido['email'] ?>)</p>
    <p><strong>Fecha:</strong> <?= $pedido['fecha'] ?></p>
    <p><strong>Estado del pedido:</strong> <?= ucfirst($pedido['estado']) ?></p>
    <p><strong>Estado del pago:</strong> <?= $pedido['estado_pago'] ? ucfirst($pedido['estado_pago']) . " (" . ucfirst($pedido['metodo']) . ")" : 'No registrado' ?></p>
    <p><strong>Dirección de envío:</strong> <?= $pedido['direccion_envio'] ?: '—' ?></p>
    <p><strong>Teléfono:</strong> <?= $pedido['telefono_envio'] ?: '—' ?></p>

    <hr>

    <h3>🧾 Productos del pedido</h3>
    <table border="1" cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse;">
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
                    <td><?= htmlspecialchars($item['nombre_producto']) ?></td>
                    <td><?= $item['cantidad'] ?></td>
                    <td>$<?= number_format($item['precio_unitario'], 2) ?></td>
                    <td>$<?= number_format($subtotal, 2) ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3" align="right"><strong>Total:</strong></td>
                <td><strong>$<?= number_format($total, 2) ?></strong></td>
            </tr>
        </tbody>
    </table>
    <br>
    <button onclick="window.print()" style="margin-top: 15px;">🖨️ Imprimir Pedido</button>
    <br>
    <a href="pedidos_empresa.php">← Volver a pedidos</a>
</main>

<?php include_once "../Templates/pie.php"; ?>