<?php
session_start();
require_once "../../Config/connection.php";

if (!isset($_SESSION['empresa_id'])) {
    header("Location: ../pages/login.php");
    exit;
}

// Obtener categorías únicas
$stmtCat = $pdo->query("SELECT DISTINCT categoria FROM productos WHERE categoria IS NOT NULL AND categoria != ''");
$categorias = $stmtCat->fetchAll(PDO::FETCH_COLUMN);


$empresa_id = $_SESSION['empresa_id'];
$producto_id = $_GET['id'] ?? null;
$mensaje = "";

// Validar ID
if (!$producto_id || !is_numeric($producto_id)) {
    die("ID de producto inválido.");
}

// Obtener producto actual
$stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ? AND empresa_id = ?");
$stmt->execute([$producto_id, $empresa_id]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    die("Producto no encontrado o no autorizado.");
}

// Actualizar producto
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre']);
    $descripcion = $_POST['descripcion'] ?? '';
    $precio = floatval($_POST['precio']);
    $stock = intval($_POST['stock']);
    $imagen_actual = $producto['imagen'];
    $imagen_nombre = $imagen_actual;
    $categoria = $_POST['categoria'] ?? '';

    // Subir nueva imagen si se proporciona
    if (!empty($_FILES['imagen']['name'])) {
        $nombre_archivo = basename($_FILES['imagen']['name']);
        $ext = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $permitidas)) {
            $imagen_nombre = uniqid("prod_") . "." . $ext;
            $ruta_destino = "../../uploads/" . $imagen_nombre;

            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
                // Borrar la anterior si existe
                if (!empty($imagen_actual) && file_exists("../../uploads/" . $imagen_actual)) {
                    unlink("../../uploads/" . $imagen_actual);
                }
            } else {
                $mensaje = "❌ Error al subir la nueva imagen.";
            }
        } else {
            $mensaje = "❌ Formato de imagen no válido.";
        }
    }

    // Guardar cambios en BD si no hubo errores de imagen
    if (empty($mensaje)) {
        $update = $pdo->prepare("UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, stock = ?, imagen = ?, categoria = ? WHERE id = ? AND empresa_id = ?");
        $ok = $update->execute([$nombre, $descripcion, $precio, $stock, $imagen_nombre, $producto_id, $empresa_id, $categoria]);

        $mensaje = $ok ? "✅ Producto actualizado correctamente." : "❌ Error al actualizar.";

        // Recargar producto actualizado
        if ($ok) {
            $stmt->execute([$producto_id, $empresa_id]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
}

include_once "../Templates/cabeza.php";
?>

<main class="formulario-producto">
    <h2>Editar Producto</h2>

    <?php if ($mensaje): ?>
        <p class="<?= str_starts_with($mensaje, '✅') ? 'mensaje-exito' : 'mensaje-error' ?>">
            <strong><?= $mensaje ?></strong>
        </p>
    <?php endif; ?>


    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Nombre:</label>
            <input type="text" name="nombre" value="<?= htmlspecialchars($producto['nombre']) ?>" required>
        </div>

        <div class="form-group">
            <label>Descripción:</label>
            <textarea name="descripcion" rows="3"><?= htmlspecialchars($producto['descripcion']) ?></textarea>
        </div>

        <div class="form-group">
            <label>Categoría:</label>
            <select name="categoria" required>
                <option value="">-- Selecciona una categoría --</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>" <?= $producto['categoria'] === $cat ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Precio:</label>
            <input type="number" step="0.01" name="precio" value="<?= $producto['precio'] ?>" required>
        </div>

        <div class="form-group">
            <label>Stock:</label>
            <input type="number" name="stock" min="0" value="<?= $producto['stock'] ?>" required>
        </div>

        <div class="form-group">
            <label>Imagen actual:</label><br>
            <?php if (!empty($producto['imagen'])): ?>
                <img src="../../uploads/<?= $producto['imagen'] ?>" width="100"><br>
            <?php else: ?>
                <em>Sin imagen</em><br>
            <?php endif; ?>
            <label>Cambiar imagen:</label>
            <input type="file" name="imagen" accept="image/*">
        </div>

        <button type="submit" class="btn">💾 Actualizar Producto</button>
    </form>

    <br>
    <a href="productos_empresa.php">← Volver al listado</a>
</main>

<?php include_once "../Templates/pie.php"; ?>