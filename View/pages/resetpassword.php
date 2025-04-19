<?php
session_start();
require_once "../../Config/connection.php";

$token = $_GET['token'] ?? null;
$mensaje = "";

if (!$token) {
    die("❌ Token no proporcionado.");
}

// Buscar token válido
$stmt = $pdo->prepare("
    SELECT tr.usuario_id, tr.usado, tr.creado_en, u.email 
    FROM tokens_recuperacion tr 
    JOIN usuarios u ON tr.usuario_id = u.id 
    WHERE token = ? AND tr.usado = 0
");
$stmt->execute([$token]);
$data = $stmt->fetch();

if (!$data) {
    die("❌ Token inválido o ya utilizado.");
}

// Verifica si el token ha expirado (ej: 1 hora)
$fecha_creacion = strtotime($data['creado_en']);
if (time() - $fecha_creacion > 36000) {
    die("⏰ El enlace ha expirado. Solicita uno nuevo.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nueva = $_POST['nueva'];
    $confirmar = $_POST['confirmar'];

    if ($nueva !== $confirmar) {
        $mensaje = "❌ Las contraseñas no coinciden.";
    } else {
        $hash = password_hash($nueva, PASSWORD_DEFAULT);

        // Actualizar contraseña del usuario
        $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
        $stmt->execute([$hash, $data['usuario_id']]);

        // Marcar token como usado
        $stmt = $pdo->prepare("UPDATE tokens_recuperacion SET usado = 1 WHERE token = ?");
        $stmt->execute([$token]);

        $mensaje = "✅ Contraseña actualizada correctamente. <a href='login.php'>Iniciar sesión</a>";
    }
}
?>

<?php include_once "../Templates/cabeza.php"; ?>
<style>
    form {
        max-width: 400px;
        margin: auto;
        background: #f9f9f9;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #ddd;
    }

    form input[type="password"] {
        width: 100%;
        padding: 8px;
        margin: 5px 0 15px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    form button {
        background: #1a1a1a;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }

    form button:hover {
        background: #333;
    }
</style>
<main>
    <h1>🔑 Restablecer Contraseña</h1>

    <?php if ($mensaje): ?>
        <p><strong><?= $mensaje ?></strong></p>
    <?php endif; ?>

    <form method="POST">
        <label>Nueva contraseña:</label>
        <input type="password" name="nueva" required><br><br>

        <label>Confirmar contraseña:</label>
        <input type="password" name="confirmar" required><br><br>

        <button type="submit">💾 Guardar nueva contraseña</button>
    </form>
</main>

<?php include_once "../Templates/pie.php"; ?>