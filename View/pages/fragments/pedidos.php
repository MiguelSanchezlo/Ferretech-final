<h3>📦 Mis Pedidos</h3>

<?php
$usuario_id = $_SESSION['usuario_id'];

$stmt = $pdo->prepare("SELECT id, total, estado, fecha FROM pedidos WHERE usuario_id = ? ORDER BY fecha DESC");
$stmt->execute([$usuario_id]);
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
  <p>No tienes pedidos registrados.</p>
<?php endif; ?>
