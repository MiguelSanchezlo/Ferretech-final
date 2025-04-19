<?php

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

require_once "../../Config/connection.php";

$usuario_id = $_SESSION['usuario_id'];

$stmt = $pdo->prepare("
    SELECT p.id, p.total, p.estado, p.fecha, e.nombre_empresa 
    FROM pedidos p
    JOIN empresas e ON p.empresa_id = e.id
    WHERE p.usuario_id = ?
    ORDER BY p.fecha DESC
");
$stmt->execute([$usuario_id]);
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

include_once "../Templates/cabeza.php";

?>

<h1>Mis Pedidos</h1>

<?php if (count($pedidos) > 0): ?>
    <table class="styled-table">
        <tr>
            <th>ID Pedido</th>
            <th>Empresa</th>
            <th>Total</th>
            <th>Estado</th>
            <th>Fecha</th>
            <th>Ver Detalles</th>
        </tr>
        <?php foreach ($pedidos as $pedido): ?>
            <tr>
                <td><?= $pedido['id'] ?></td>
                <td><?= htmlspecialchars($pedido['nombre_empresa']) ?></td>
                <td>$ <?= number_format($pedido['total'], 2) ?></td>
                <td><?= ucfirst($pedido['estado']) ?></td>
                <td><?= $pedido['fecha'] ?></td>
                <td><a href="detalle_pedido.php?id=<?= $pedido['id'] ?>">🔍</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>No tienes pedidos aún.</p>
<?php
endif;

include_once "../Templates/pie.php"; // Pie de página
?>