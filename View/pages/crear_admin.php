<?php
session_start();
require_once "../../Config/connection.php";

// Bloquea acceso a no administradores
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: index.php");
    exit;
}

// Manejo del formulario
$mensaje = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre     = $_POST["nombre"];
    $apellido   = $_POST["apellido"];
    $email      = $_POST["email"];
    $password   = $_POST["password"];
    $confirmar  = $_POST["confirmar"];

    if ($password !== $confirmar) {
        $mensaje = "❌ Las contraseñas no coinciden.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "❌ Correo inválido.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Verificar si ya existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $mensaje = "❌ El correo ya está registrado.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, apellido, email, password, rol) VALUES (?, ?, ?, ?, 'administrador')");
            $exito = $stmt->execute([$nombre, $apellido, $email, $hash]);

            if ($exito) {
                $mensaje = "✅ Administrador registrado exitosamente.";
            } else {
                $mensaje = "❌ Error al registrar el administrador.";
            }
        }
    }
}
?>

<?php include_once "../Templates/cabeza.php"; ?>

<main>
    <div class="register-container-admin">
        <h1>Registrar Nuevo Administrador</h1>

        <?php if ($mensaje): ?>
            <div class="mensaje <?= str_starts_with($mensaje, '✅') ? 'success' : 'error' ?>">
                <?= $mensaje ?>
            </div>
        <?php endif; ?>


        <form method="POST" class="register-form">
            <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="nombre" required>
            </div>

            <div class="form-group">
                <label>Apellido</label>
                <input type="text" name="apellido" required>
            </div>

            <div class="form-group">
                <label>Correo Electrónico</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" required>
            </div>

            <div class="form-group">
                <label>Confirmar Contraseña</label>
                <input type="password" name="confirmar" required>
            </div>

            <button type="submit">Registrar Administrador</button>
        </form>
    </div>
</main>

<?php include_once "../Templates/pie.php"; ?>