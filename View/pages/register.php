<?php
include_once "../../Config/connection.php";
include_once "../Templates/cabeza.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre     = $_POST['nombre'];
    $apellido   = $_POST['apellido'];
    $email      = $_POST['correo'];
    $password   = $_POST['contraseña'];
    $confirmar  = $_POST['confirmar-contraseña'];
    $rol        = "cliente";

    if ($password !== $confirmar) {
        echo "<p style='color: red;'>❌ Las contraseñas no coinciden.</p>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<p style='color: red;'>❌ Correo inválido.</p>";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            echo "<p style='color: red;'>❌ Este correo ya está registrado.</p>";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, apellido, email, password) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$nombre, $apellido, $email, $hash])) {
                header("Location: login.php?registrado=1");
                exit;
            } else {
                echo "<p style='color: red;'>❌ Error al registrar el usuario.</p>";
            }
        }
    }
}
?>

<section class="login-section">
    <div class="login-card">
        <h2>Registro</h2>
        <p>Regístrate como usuario de FerretTech para realizar compras</p>
        <form method="POST">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>

            <div class="form-group">
                <label for="apellido">Apellido</label>
                <input type="text" id="apellido" name="apellido" required>
            </div>

            <div class="form-group">
                <label for="correo">Correo Electrónico</label>
                <input type="email" id="correo" name="correo" required>
            </div>

            <div class="form-group">
                <label for="contraseña">Contraseña</label>
                <input type="password" id="contraseña" name="contraseña" required>
            </div>

            <div class="form-group">
                <label for="confirmar-contraseña">Confirmar Contraseña</label>
                <input type="password" id="confirmar-contraseña" name="confirmar-contraseña" required>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" required> Acepto los <a href="terminos.php" target="_blank">términos y condiciones</a>
                </label>
            </div>

            <button type="submit" class="login-button">Registrarse</button>

            <div class="register-link">
                ¿Ya tienes una cuenta? <a href="login.php">Inicia sesión</a><br>
                ¿Eres una empresa? <a href="registercompany.php">Regístrate aquí</a>
            </div>
        </form>
    </div>
</section>

<?php include_once "../Templates/pie.php"; ?>
