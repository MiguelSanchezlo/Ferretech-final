<?php
session_start();
require_once "../../Config/connection.php";

if (!isset($_SESSION['empresa_id'])) {
    header("Location: ../pages/login.php");
    exit;
}

$empresa_id = $_SESSION['empresa_id'];
$stmt = $pdo->prepare("SELECT * FROM productos WHERE empresa_id = ?");
$stmt->execute([$empresa_id]);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

include_once "../Templates/cabeza.php";
?>

<main class="panel-productos">
    <h1>📦 Panel de Productos de Mi Empresa</h1>
    
    <?php if (isset($_GET['error']) && $_GET['error'] === 'pedido_asociado'): ?>
        <p class="mensaje-error">❌ No se puede eliminar este producto porque ya ha sido vendido.</p>
        <?php elseif (isset($_GET['success']) && $_GET['success'] === 'eliminado'): ?>
            <p class="mensaje-exito">✅ Producto eliminado correctamente.</p>
            <?php endif; ?>

    <a href="producto_nuevo.php" style="display: inline-block; margin-bottom: 15px;" class="btn">➕ Agregar Producto</a>

    <?php if (count($productos) > 0): ?>
        <table class="tabla-productos">
        <thead style="background: #f0f0f0;">
                <tr>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $p): ?>
                    <tr>
                        <td style="text-align: center;">
                            <?php if (!empty($p['imagen']) && file_exists("../../uploads/" . $p['imagen'])): ?>
                                <img src="../../uploads/<?= htmlspecialchars($p['imagen']) ?>" width="60">
                            <?php else: ?>
                                <span>❌</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($p['nombre']) ?></td>
                        <td><?= htmlspecialchars($p['descripcion']) ?></td>
                        <td>$<?= number_format($p['precio'], 2) ?></td>
                        <td><?= $p['stock'] ?></td>
                        <td>
                            <a href="producto_editar.php?id=<?= $p['id'] ?>">✏️ Editar</a> |
                            <a href="../../Controller/eliminar_producto.php?id=<?= $p['id'] ?>" onclick="return confirm('¿Estás seguro de eliminar este producto?')">🗑️ Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="margin-top: 20px;">⚠️ No tienes productos registrados aún.</p>
    <?php endif; ?>
</main>

<?php include_once "../Templates/pie.php"; ?>