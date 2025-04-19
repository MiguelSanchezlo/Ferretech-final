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


$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre']);
    $descripcion = $_POST['descripcion'] ?? '';
    $precio = floatval($_POST['precio']);
    $stock = intval($_POST['stock']);
    $empresa_id = $_SESSION['empresa_id'];
    $imagen_nombre = null;
    $categoria = $_POST['categoria'] ?? '';

    // Validación de datos mínima
    if ($precio <= 0 || $stock < 0 || empty($nombre)) {
        $mensaje = "❌ Datos inválidos.";
    } else {
        // Subida de imagen
        if (!empty($_FILES['imagen']['name'])) {
            $nombre_archivo = basename($_FILES['imagen']['name']);
            $ext = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));
            $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($ext, $permitidas)) {
                $imagen_nombre = uniqid("prod_") . "." . $ext;
                $ruta_destino = "../../uploads/" . $imagen_nombre;

                if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
                    $mensaje = "❌ Error al subir la imagen.";
                }
            } else {
                $mensaje = "❌ Formato de imagen no permitido.";
            }
        }

        // Insertar en la base de datos
        if (empty($mensaje)) {
            $stmt = $pdo->prepare("INSERT INTO productos (empresa_id, nombre, descripcion, precio, stock, imagen, categoria) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $ok = $stmt->execute([$empresa_id, $nombre, $descripcion, $precio, $stock, $imagen_nombre, $categoria]);

            $mensaje = $ok ? "✅ Producto agregado correctamente." : "❌ Error al guardar en base de datos.";
        }
    }
}

include_once "../Templates/cabeza.php";
?>

<main class="formulario-producto">
    <h2>Agregar Nuevo Producto</h2>

    <?php if ($mensaje): ?>
        <p class="<?= str_starts_with($mensaje, '✅') ? 'mensaje-exito' : 'mensaje-error' ?>">
            <strong><?= $mensaje ?></strong>
        </p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Nombre:</label>
            <input type="text" name="nombre" required>
        </div>

        <div class="form-group">
            <label>Descripción:</label>
            <textarea name="descripcion" rows="3"></textarea>
        </div>

        <div class="form-group">
            <label>Categoría:</label>
            <select name="categoria" required>
                <option value="">-- Selecciona una categoría --</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Precio:</label>
            <input type="number" step="0.01" name="precio" required>
        </div>

        <div class="form-group">
            <label>Stock:</label>
            <input type="number" name="stock" min="0" required>
        </div>

        <div class="form-group">
            <label>Imagen:</label>
            <input type="file" name="imagen" accept="image/*">
        </div>

        <button type="submit" class="btn">📤 Guardar Producto</button>
    </form>

    <br><a href="productos_empresa.php">← Volver al listado</a>
</main>

<?php include_once "../Templates/pie.php"; ?>