<?php
session_start();
require_once __DIR__ . '/../Config/connection.php';
require_once __DIR__ . '/../Model/UsuarioModel.php';

$model = new UsuarioModel($pdo);
$usuario_id = $_SESSION['usuario_id'] ?? null;

if (!$usuario_id) {
    header("Location: ../View/pages/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // --- CAMBIO DE CONTRASEÑA ---
    if (isset($_POST['accion']) && $_POST['accion'] === 'cambiar_password') {
        $actual    = $_POST["actual"] ?? "";
        $nueva     = $_POST["nueva"] ?? "";
        $confirmar = $_POST["confirmar"] ?? "";

        if ($nueva !== $confirmar) {
            header("Location: ../View/pages/user.php?pass=nomatch");
            exit;
        }

        if (!$model->verificarPassword($usuario_id, $actual)) {
            header("Location: ../View/pages/user.php?pass=invalid");
            exit;
        }

        $exito = $model->actualizarPassword($usuario_id, $nueva);
        header("Location: ../View/pages/user.php?pass=" . ($exito ? "success" : "fail"));
        exit;
    }

    // --- ACTUALIZAR NOMBRE Y APELLIDO ---
    if (isset($_POST['accion']) && $_POST['accion'] === 'actualizar_info') {
        $nombre   = $_POST["nombre"] ?? "";
        $apellido = $_POST["apellido"] ?? "";

        if (!empty($nombre) && !empty($apellido)) {
            $exito = $model->actualizarNombreApellido($usuario_id, $nombre, $apellido);
            header("Location: ../View/pages/user.php?update=" . ($exito ? "success" : "fail"));
            exit;
        } else {
            header("Location: ../View/pages/user.php?update=empty");
            exit;
        }
    }
}
