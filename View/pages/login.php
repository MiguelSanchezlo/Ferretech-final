<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once "../../Config/connection.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Buscar en usuarios
    $stmt = $pdo->prepare("SELECT id, nombre, password, rol FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($password, $usuario["password"])) {
        $_SESSION["usuario_id"] = $usuario["id"];
        $_SESSION["nombre"] = $usuario["nombre"];
        $_SESSION["rol"] = $usuario["rol"];
        header("Location: index.php");
        exit;
    }

    // Buscar en empresas
    $stmt = $pdo->prepare("SELECT id, nombre_empresa, password FROM empresas WHERE email_contacto = ?");
    $stmt->execute([$email]);
    $empresa = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($empresa && password_verify($password, $empresa["password"])) {
        $_SESSION["empresa_id"] = $empresa["id"];
        $_SESSION["nombre_empresa"] = $empresa["nombre_empresa"];
        header("Location: index.php");
        exit;
    }

    $error = "❌ Credenciales incorrectas.";
}
?>

<?php include_once "../Templates/cabeza.php"; ?>

<section class="login-section">
    <div class="login-card">
        <h2>Iniciar Sesión</h2>
        <p>Accede con tu cuenta de usuario o empresa</p>

        <form method="POST">
            <?php if (!empty($error)): ?>
                <div class="alert" style="color: red; margin-bottom: 15px;"><?= $error ?></div>
            <?php endif; ?>

            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit" class="login-button">Ingresar</button>

            <div class="register-link">
                ¿No tienes cuenta? <a href="register.php">Registrarse</a><br>
                ¿Eres una empresa? <a href="registercompany.php">Registrar Empresa</a><br>
                ¿Olvidaste tu contraseña? <a href="recoverypassword.php">Recupérala aquí</a>
            </div>
        </form>
    </div>
</section>

<?php include_once "../Templates/pie.php"; ?>
