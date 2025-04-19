<h3>📦 Pedidos Recibidos</h3>

<?php
$empresa_id = $_SESSION['empresa_id'];

$stmt = $pdo->prepare("
    SELECT 
        id, usuario_id, total, estado, fecha
    FROM pedidos
    WHERE empresa_id = ?
    ORDER BY fecha DESC
");
$stmt->execute([$empresa_id]);
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if (count($pedidos) > 0): ?>
  <table border="1" cellpadding="8" cellspacing="0">
    <thead>
      <tr>
        <th>ID Pedido</th>
        <th>Fecha</th>
        <th>Total</th>
        <th>Estado</th>
        <th>Detalles</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($pedidos as $pedido): ?>
        <tr>
          <td>#<?= $pedido['id'] ?></td>
          <td><?= $pedido['fecha'] ?></td>
          <td>$ <?= number_format($pedido['total'], 2) ?></td>
          <td><?= ucfirst($pedido['estado']) ?></td>
          <td><a href="detalle_pedido.php?id=<?= $pedido['id'] ?>">Ver</a></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php else: ?>
  <p>No has recibido pedidos aún.</p>
<?php endif; ?>
