<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $para = $_POST['email'];
  $asunto = $_POST['asunto'];
  $mensaje = $_POST['mensaje'];

  $mail = new PHPMailer(true);

  try {
    // Configuración SMTP
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'CORREO';       
    $mail->Password   = 'CONTRASEÑA DE APLICACION';       
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    // Remitente y destinatario
    $mail->setFrom('CORREO REMITENTE', 'FerreTech');
    $mail->addAddress($para);

    // Contenido
    $mail->isHTML(true);
    $mail->Subject = $asunto;
    $mail->Body    = nl2br($mensaje);

    $mail->send();
    header("Location: ../View/pages/mensajes_recibidos.php?enviado=1");
  } catch (Exception $e) {
    echo "Error al enviar: {$mail->ErrorInfo}";
  }
}
