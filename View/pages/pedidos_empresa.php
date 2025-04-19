<?php
session_start();
require_once "../../Config/connection.php";

if (!isset($_SESSION['empresa_id'])) {
    header("Location: login.php");
    exit;
}

include_once "../Templates/cabeza.php";
$empresa_id = $_SESSION['empresa_id'];

// Filtros
$filtroDesde = $_GET['desde'] ?? null;
$filtroHasta = $_GET['hasta'] ?? null;

// Paginación
$pagina = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
$por_pagina = 10;
$offset = ($pagina - 1) * $por_pagina;

// Notificaciones no leídas
$stmtNotif = $pdo->prepare("SELECT id, mensaje FROM notificaciones WHERE empresa_id = ? AND leido = 0 ORDER BY fecha DESC");
$stmtNotif->execute([$empresa_id]);
$notificaciones = $stmtNotif->fetchAll(PDO::FETCH_ASSOC);
if ($notificaciones) {
    echo "<div class='notificaciones' style='background: #fff3cd; border: 1px solid #ffeeba; padding: 10px; margin: 15px 0;'>";
    foreach ($notificaciones as $n) {
        echo "<p>🔔 " . htmlspecialchars($n['mensaje']) . "</p>";
    }
    echo "</div>";
    $ids = implode(",", array_map("intval", array_column($notificaciones, 'id')));
    $pdo->query("UPDATE notificaciones SET leido = 1 WHERE id IN ($ids)");
}

// Cambiar estado si se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pedido_id'], $_POST['cambiar_estado'])) {
    $pedido_id = $_POST['pedido_id'];
    $nuevo_estado = $_POST['cambiar_estado'];

    $pdo->prepare("UPDATE pedidos SET estado = ? WHERE id = ? AND empresa_id = ?")
        ->execute([$nuevo_estado, $pedido_id, $empresa_id]);

    // Obtener usuario del pedido
    $stmtU = $pdo->prepare("SELECT usuario_id FROM pedidos WHERE id = ?");
    $stmtU->execute([$pedido_id]);
    $usuario_id = $stmtU->fetchColumn();

    if ($usuario_id && in_array($nuevo_estado, ['en preparación', 'enviado', 'entregado'])) {
        $mensaje = "📦 Tu pedido #$pedido_id ha sido actualizado a '$nuevo_estado'";
        $pdo->prepare("INSERT INTO notificaciones (usuario_id, tipo, mensaje) VALUES (?, 'usuario', ?)")
            ->execute([$usuario_id, $mensaje]);
    }

    header("Location: pedidos_empresa.php?pagina=$pagina");
    exit;
}

// Total pedidos
$sql_count = "SELECT COUNT(*) FROM pedidos WHERE empresa_id = ?";
$stmt_count = $pdo->prepare($sql_count);
$stmt_count->execute([$empresa_id]);
$total_pedidos = $stmt_count->fetchColumn();
$total_paginas = ceil($total_pedidos / $por_pagina);

// Pedidos
$sql = "
    SELECT 
        p.id AS pedido_id, p.fecha, p.total, p.estado,
        p.direccion_envio, p.telefono_envio,
        u.nombre, u.apellido,
        g.estado AS estado_pago, g.metodo,
        (SELECT SUM(dp.cantidad) FROM detalles_pedido dp WHERE dp.pedido_id = p.id) AS cantidad_productos
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
    $params['hasta'] = $filtroHasta . " 23:59:59";
}

$sql .= " ORDER BY p.fecha DESC LIMIT $offset, $por_pagina";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="pedidos-container">
    <h1>📦 Pedidos Recibidos</h1>

    <form method="GET" class="filtro-form">
        <label>Desde:</label>
        <input type="date" name="desde" value="<?= htmlspecialchars($filtroDesde ?? '') ?>">
        <label>Hasta:</label>
        <input type="date" name="hasta" value="<?= htmlspecialchars($filtroHasta ?? '') ?>">
        <button type="submit">🔍 Filtrar</button>
        <a href="exportar_excel.php?desde=<?= $filtroDesde ?>&hasta=<?= $filtroHasta ?>" class="btn">Exportar Excel</a>
    </form>

    <?php if ($pedidos): ?>
        <table class="tabla-pedidos">
            <thead>
                <tr style="background: #f4f4f4;">
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Total</th>
                    <th>Cant.</th>
                    <th>Estado</th>
                    <th>Cambiar</th>
                    <th>Pago</th>
                    <th>Dirección</th>
                    <th>Teléfono</th>
                    <th>Detalles</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pedidos as $pedido): ?>
                    <tr>
                        <td>#<?= $pedido['pedido_id'] ?></td>
                        <td><?= $pedido['fecha'] ?></td>
                        <td><?= htmlspecialchars($pedido['nombre'] . " " . $pedido['apellido']) ?></td>
                        <td>$<?= number_format($pedido['total'], 2) ?></td>
                        <td><?= $pedido['cantidad_productos'] ?? 0 ?></td>
                        <td><?= ucfirst($pedido['estado']) ?></td>
                        <td>
                            <form method="POST" class="estado-form">
                                <input type="hidden" name="pedido_id" value="<?= $pedido['pedido_id'] ?>">
                                <select name="cambiar_estado">
                                    <option <?= $pedido['estado'] == 'pagado' ? 'selected' : '' ?>>Pagado</option>
                                    <option <?= $pedido['estado'] == 'en preparación' ? 'selected' : '' ?>>En preparación</option>
                                    <option <?= $pedido['estado'] == 'enviado' ? 'selected' : '' ?>>Enviado</option>
                                    <option <?= $pedido['estado'] == 'entregado' ? 'selected' : '' ?>>Entregado</option>
                                </select>
                                <button type="submit">💾</button>
                            </form>
                        </td>
                        <td><?= $pedido['estado_pago'] ? ucfirst($pedido['estado_pago']) . " (" . ucfirst($pedido['metodo']) . ")" : 'No registrado' ?></td>
                        <td><?= $pedido['direccion_envio'] ? htmlspecialchars($pedido['direccion_envio']) : '—' ?></td>
                        <td><?= $pedido['telefono_envio'] ? htmlspecialchars($pedido['telefono_envio']) : '—' ?></td>
                        <td>
                            <a href="detalle_pedido.php?id=<?= $pedido['pedido_id'] ?>" class="btn-detalle">Ver</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Paginación -->
        <div class="paginacion">
            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <a href="?pagina=<?= $i ?>" class="<?= $i == $pagina ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>

    <?php else: ?>
        <p>❌ No se encontraron pedidos.</p>
    <?php endif; ?>
</main>

<?php include_once "../Templates/pie.php"; ?>