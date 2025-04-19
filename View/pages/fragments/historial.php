<h3>🧾 Historial de Compras</h3>

<?php
$stmt = $pdo->prepare("
  SELECT p.id AS pedido_id, p.fecha, p.total, g.estado AS estado_pago, g.metodo
  FROM pedidos p
  INNER JOIN pagos g ON g.pedido_id = p.id
  WHERE p.usuario_id = ?
  ORDER BY p.fecha DESC
");
$stmt->execute([$_SESSION['usuario_id']]);
$historial = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if (count($historial) > 0): ?>
  <table border="1" cellpadding="8" cellspacing="0">
    <thead>
      <tr>
        <th>Pedido</th>
        <th>Fecha</th>
        <th>Método</th>
        <th>Estado del Pago</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($historial as $h): ?>
        <tr>
          <td>#<?= $h['pedido_id'] ?></td>
          <td><?= $h['fecha'] ?></td>
          <td><?= ucfirst($h['metodo']) ?></td>
          <td><?= ucfirst($h['estado_pago']) ?></td>
          <td>$ <?= number_format($h['total'], 2) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php else: ?>
  <p>No hay historial de pagos registrado.</p>
<?php endif; ?>
