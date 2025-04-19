<?php

require_once "../../Config/connection.php";

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['usuario_id']) && !isset($_SESSION['empresa_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'] ?? null;
$empresa_id = $_SESSION['empresa_id'] ?? null;

$pedido_id = $_GET['id'] ?? null;

if (!$pedido_id) {
    die("ID de pedido no proporcionado.");
}

// Verificar que el pedido le pertenece al usuario
if (isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];
    $stmt = $pdo->prepare("
        SELECT p.*, e.nombre_empresa 
        FROM pedidos p
        JOIN empresas e ON p.empresa_id = e.id
        WHERE p.id = ? AND p.usuario_id = ?
    ");
    $stmt->execute([$pedido_id, $usuario_id]);
} elseif (isset($_SESSION['empresa_id'])) {
    $empresa_id = $_SESSION['empresa_id'];
    $stmt = $pdo->prepare("
        SELECT p.*, e.nombre_empresa, u.nombre AS nombre_usuario, u.apellido AS apellido_usuario 
        FROM pedidos p
        JOIN empresas e ON p.empresa_id = e.id
        JOIN usuarios u ON p.usuario_id = u.id
        WHERE p.id = ? AND p.empresa_id = ?
    ");

    $stmt->execute([$pedido_id, $empresa_id]);
}

$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
    die("Pedido no encontrado o no autorizado.");
}

// Obtener los productos del pedido
$stmt = $pdo->prepare("
    SELECT dp.producto_id, dp.cantidad, dp.precio_unitario, p.nombre AS nombre_producto
    FROM detalles_pedido dp
    JOIN productos p ON dp.producto_id = p.id
    WHERE dp.pedido_id = ?
");
$stmt->execute([$pedido_id]);
$detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

include_once "../Templates/cabeza.php";
?>

<main class="detalle-pedido">
<h1 class="titulo-impresion">📋 Factura / Resumen del Pedido</h1>

<h1 class="titulo-pantalla">📄 Detalles del Pedido #<?= htmlspecialchars($pedido_id) ?></h1>
    <?php if (isset($_SESSION['usuario_id'])): ?>
        <p><strong>Empresa:</strong> <?= htmlspecialchars($pedido['nombre_empresa']) ?></p>
    <?php elseif (isset($_SESSION['empresa_id'])): ?>
        <p><strong>Cliente:</strong> <?= htmlspecialchars($pedido['nombre_usuario'] . ' ' . $pedido['apellido_usuario']) ?></p>
        <?php endif; ?>
    <p><strong>Fecha:</strong> <?= htmlspecialchars($pedido['fecha']) ?></p>
    <p><strong>Dirección de envío:</strong> <?= htmlspecialchars($pedido['direccion_envio'] ?? '—') ?></p>
    <p><strong>Teléfono:</strong> <?= htmlspecialchars($pedido['telefono_envio'] ?? '—') ?></p>

    <h3>📦 Estado del pedido</h3>
    <div class="estado-pedido">
        <?php
        $estados = ["pagado", "en preparación", "enviado", "entregado"];
        $estado_actual = strtolower($pedido['estado']);
        foreach ($estados as $estado) {
            $completo = $estado_actual == $estado || array_search($estado_actual, $estados) > array_search($estado, $estados);
            echo "<span style='padding: 5px 10px; border-radius: 5px; background: " . ($completo ? "#28a745" : "#ccc") . "; color: white; margin-right: 10px;'>$estado</span>";
        }
        ?>
    </div>

    <table class="tabla-detalle">
        <thead style="background: #f0f0f0;">
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
                    <td><?= (int)$item['cantidad'] ?></td>
                    <td>$<?= number_format($item['precio_unitario'], 2) ?></td>
                    <td>$<?= number_format($subtotal, 2) ?></td>
                </tr>
            <?php endforeach; ?>
            <tr style="background: #f9f9f9;">
                <td colspan="3" align="right"><strong>Total:</strong></td>
                <td><strong>$<?= number_format($total, 2) ?></strong></td>
            </tr>
        </tbody>
    </table>

    <br>
    <button onclick="window.print()" class="boton-imprimir">🖨️ Imprimir</button>
    <br><br>
    <?php if (isset($_SESSION['usuario_id'])): ?>
        <a href="mis_pedidos.php" class="link-volver">← Volver</a>
    <?php else: ?>
        <a href="pedidos_empresa.php" class="link-volver">← Volver</a>
    <?php endif; ?>

</main>

<?php include_once "../Templates/pie.php"; ?>