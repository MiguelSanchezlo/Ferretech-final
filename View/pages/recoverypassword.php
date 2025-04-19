<?php
session_start();
require_once "../../Config/connection.php";
require_once "../../vendor/autoload.php";
$mail_config = require_once "../../Config/config_mail.php";

use PHPMailer\PHPMailer\PHPMailer;

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];

    // Buscar usuario
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if ($usuario) {
        // Generar token
        $token = bin2hex(random_bytes(32));
        $usuario_id = $usuario['id'];

        // Guardar token
        $stmt = $pdo->prepare("INSERT INTO tokens_recuperacion (usuario_id, token) VALUES (?, ?)");
        $stmt->execute([$usuario_id, $token]);

        // Enviar correo
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $mail_config['smtp_host'];
            $mail->SMTPAuth = true;
            $mail->Username = $mail_config['smtp_user'];
            $mail->Password = $mail_config['smtp_pass'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $mail_config['smtp_port'];

            $mail->setFrom($mail_config['correo_origen'], $mail_config['nombre_origen']);
            $mail->addAddress($email);

            $enlace = "http://localhost:8082/hoy/ProyectoFerretchDM/ProyectoFerretchDM/View/pages/resetpassword.php?token=$token";
            $mail->Subject = "Recuperación de contraseña - FerreTech";
            $mail->Body = "Hola,\n\nPara restablecer tu contraseña, haz clic en este enlace:\n\n$enlace\n\nSi no lo solicitaste, ignora este mensaje.";

            $mail->send();
            $mensaje = "📨 Se ha enviado un enlace de recuperación a tu correo.";
        } catch (Exception $e) {
            $mensaje = "❌ Error al enviar correo: {$mail->ErrorInfo}";
        }
    } else {
        $mensaje = "❌ No se encontró una cuenta con ese correo.";
    }
}
?>

<?php include_once "../Templates/cabeza.php"; ?>

<main>
    <div class="recovery-container">
        <h1>🔐 Recuperar Contraseña</h1>

        <?php if ($mensaje): ?>
            <p class="<?= str_starts_with($mensaje, '❌') ? 'error' : 'mensaje' ?>">
                <?= $mensaje ?>
            </p>
        <?php endif; ?>

        <form method="POST">
            <label for="email">Correo electrónico:</label>
            <input type="email" name="email" required>
            <button type="submit">📧 Enviar enlace de recuperación</button>
        </form>
    </div>
</main>


<?php include_once "../Templates/pie.php"; ?>
