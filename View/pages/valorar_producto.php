<?php
session_start();
require_once "../../Config/connection.php";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$producto_id = $_GET['producto_id'] ?? null;
$pedido_id = $_GET['pedido_id'] ?? null;

// Validar que el usuario haya comprado este producto
$check = $pdo->prepare("SELECT * FROM detalles_pedido WHERE pedido_id = ? AND producto_id = ?");
$check->execute([$pedido_id, $producto_id]);
$valid = $check->fetch();

if (!$valid) die("No autorizado.");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $calificacion = $_POST['calificacion'];
    $comentario = $_POST['comentario'];

    $insert = $pdo->prepare("INSERT INTO valoraciones (usuario_id, producto_id, pedido_id, calificacion, comentario) VALUES (?, ?, ?, ?, ?)");
    $ok = $insert->execute([$usuario_id, $producto_id, $pedido_id, $calificacion, $comentario]);

    if ($ok) {
        header("Location: detalle_pedido.php?id=$pedido_id");
        exit;
    } else {
        echo "Error al guardar tu valoración.";
    }
}

include_once "../Templates/cabeza.php";
?>

<main style="padding: 20px;">
    <h1>⭐ Valorar Producto</h1>

    <form method="POST">
        <label>Calificación:</label>
        <select name="calificacion" required>
            <option value="5">⭐ 5 Excelente</option>
            <option value="4">⭐ 4 Muy bueno</option>
            <option value="3">⭐ 3 Bueno</option>
            <option value="2">⭐ 2 Regular</option>
            <option value="1">⭐ 1 Malo</option>
        </select>

        <br><br>
        <label>Comentario (opcional):</label><br>
        <textarea name="comentario" rows="4" style="width: 100%;"></textarea><br><br>

        <button type="submit">💬 Enviar valoración</button>
    </form>
</main>

<?php include_once "../Templates/pie.php"; ?>
