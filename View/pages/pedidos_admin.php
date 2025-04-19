<?php
session_start();
require_once "../../Config/connection.php";

// Verifica que sea administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: index.php");
    exit;
}

// Obtener todos los pedidos con info de cliente y empresa
$stmt = $pdo->prepare("
    SELECT p.id, p.total, p.estado, p.fecha,
           u.nombre AS nombre_usuario,
           e.nombre_empresa
    FROM pedidos p
    JOIN usuarios u ON p.usuario_id = u.id
    JOIN empresas e ON p.empresa_id = e.id
    ORDER BY p.fecha DESC
");
$stmt->execute();
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include_once "../Templates/cabeza.php"; ?>

<main>
    <h1>📦 Pedidos del Sistema</h1>

    <?php if (count($pedidos) > 0): ?>
        <table class="pedidos-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Empresa</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Ver</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pedidos as $pedido): ?>
                    <tr>
                        <td><?= $pedido['id'] ?></td>
                        <td><?= $pedido['nombre_usuario'] ?></td>
                        <td><?= $pedido['nombre_empresa'] ?></td>
                        <td>$ <?= number_format($pedido['total'], 2) ?></td>
                        <td><?= ucfirst($pedido['estado']) ?></td>
                        <td><?= $pedido['fecha'] ?></td>
                        <td><a href="detalle_pedido_admin.php?id=<?= $pedido['id'] ?>">🔍</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay pedidos registrados.</p>
    <?php endif; ?>
</main>

<?php include_once "../Templates/pie.php"; ?>