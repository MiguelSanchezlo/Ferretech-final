<?php
require_once "../../Config/connection.php";
include_once "../Templates/cabeza.php";

$product_id = $_GET['id'] ?? null;
if (!$product_id) die("❌ Producto no especificado.");

// Obtener datos del producto
$stmt = $pdo->prepare("SELECT p.*, e.nombre_empresa FROM productos p JOIN empresas e ON p.empresa_id = e.id WHERE p.id = ?");
$stmt->execute([$product_id]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$producto) die("❌ Producto no encontrado.");

// Obtener valoraciones
$stmt = $pdo->prepare("
    SELECT v.*, u.nombre 
    FROM valoraciones v 
    JOIN usuarios u ON v.usuario_id = u.id 
    WHERE v.producto_id = ?
    ORDER BY v.fecha DESC
");
$stmt->execute([$product_id]);
$valoraciones = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Calcular promedio
$promedio = 0;
if (count($valoraciones) > 0) {
    $suma = array_sum(array_column($valoraciones, 'calificacion'));
    $promedio = round($suma / count($valoraciones), 1);
}
?>

<main class="product-detail">
    <h1><?= htmlspecialchars($producto['nombre']) ?></h1>

    <?php if ($producto['imagen']): ?>
        <img src="../../uploads/<?= $producto['imagen'] ?>" class="product-image">
    <?php endif; ?>

    <p><strong>Precio:</strong> $<?= number_format($producto['precio'], 2) ?></p>
    <p><strong>Categoría:</strong> <?= htmlspecialchars($producto['categoria']) ?></p>
    <p><strong>Vendido por:</strong> <?= htmlspecialchars($producto['nombre_empresa']) ?></p>
    <p><strong>Descripción:</strong> <?= nl2br(htmlspecialchars($producto['descripcion'])) ?></p>

    <form method="POST" action="../../Controller/carrito_crud.php" style="margin-top: 20px;">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="product_id" value="<?= $producto['id'] ?>">
        <label for="quantity">Cantidad:</label>
        <input type="number" name="quantity" value="1" min="1">
        <button type="submit">🛒 Añadir al carrito</button>
    </form>

    <hr>

    <h2>⭐ Valoraciones</h2>
    <?php if ($promedio > 0): ?>
        <p><strong>Promedio:</strong> <?= str_repeat("⭐", round($promedio)) ?> (<?= $promedio ?>/5)</p>
    <?php endif; ?>

    <?php if (count($valoraciones) > 0): ?>
        <?php foreach ($valoraciones as $v): ?>
            <div class="valoracion-box">
                <strong><?= htmlspecialchars($v['nombre']) ?></strong> —
                <?= str_repeat("⭐", $v['calificacion']) ?><br>
                <em><?= nl2br(htmlspecialchars($v['comentario'])) ?></em><br>
                <small><?= $v['fecha'] ?></small>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No hay valoraciones todavía.</p>
    <?php endif; ?>
    <a class="contacto-enlace" href="contacto_empresa.php?id=<?= $producto['empresa_id'] ?>">✉️ Contactar</a>

</main>

<?php include_once "../Templates/pie.php"; ?>