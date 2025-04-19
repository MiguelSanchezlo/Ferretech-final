<h3>🧾 Historial de Ventas</h3>

<?php
$empresa_id = $_SESSION['empresa_id'];

$stmt = $pdo->prepare("
  SELECT p.id AS pedido_id, p.fecha, p.total, g.estado AS estado_pago, g.metodo
  FROM pedidos p
  LEFT JOIN pagos g ON g.pedido_id = p.id
  WHERE p.empresa_id = ?
  ORDER BY p.fecha DESC
");
$stmt->execute([$empresa_id]);
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
          <td><?= $h['metodo'] ? ucfirst($h['metodo']) : '—' ?></td>
          <td><?= $h['estado_pago'] ? ucfirst($h['estado_pago']) : 'Sin registrar' ?></td>
          <td>$ <?= number_format($h['total'], 2) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php else: ?>
  <p>No se han registrado ventas aún.</p>
<?php endif; ?>
