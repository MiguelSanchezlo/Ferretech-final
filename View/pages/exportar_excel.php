<?php
session_start(); 
require_once "../../Config/connection.php";

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=pedidos_empresa.xls");

$empresa_id = $_SESSION['empresa_id'] ?? null;

if (!$empresa_id) {
    die("Acceso no autorizado.");
}

$filtroDesde = $_GET['desde'] ?? null;
$filtroHasta = $_GET['hasta'] ?? null;

$sql = "
  SELECT 
    p.id AS pedido_id, p.fecha, p.total, p.estado,
    u.nombre, u.apellido,
    p.direccion_envio, p.telefono_envio,
    g.estado AS estado_pago, g.metodo
  FROM pedidos p
  JOIN usuarios u ON p.usuario_id = u.id
  LEFT JOIN pagos g ON g.pedido_id = p.id
  WHERE p.empresa_id = :empresa_id
";

$params = ['empresa_id' => $empresa_id];

if ($filtroDesde) {
    $sql .= " AND p.fecha >= :desde";
    $params['desde'] = $filtroDesde;
}
if ($filtroHasta) {
    $sql .= " AND p.fecha <= :hasta";
    $params['hasta'] = $filtroHasta . ' 23:59:59';
}

$sql .= " ORDER BY p.fecha DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1'>";
echo "<tr>
        <th>ID Pedido</th>
        <th>Fecha</th>
        <th>Cliente</th>
        <th>Total</th>
        <th>Estado Pedido</th>
        <th>Estado Pago</th>
        <th>Método</th>
        <th>Dirección</th>
        <th>Teléfono</th>
      </tr>";

foreach ($pedidos as $p) {
    echo "<tr>
            <td>{$p['pedido_id']}</td>
            <td>{$p['fecha']}</td>
            <td>{$p['nombre']} {$p['apellido']}</td>
            <td>{$p['total']}</td>
            <td>{$p['estado']}</td>
            <td>{$p['estado_pago']}</td>
            <td>{$p['metodo']}</td>
            <td>{$p['direccion_envio']}</td>
            <td>{$p['telefono_envio']}</td>
          </tr>";
}
echo "</table>";
