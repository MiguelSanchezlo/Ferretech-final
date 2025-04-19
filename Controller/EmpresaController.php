<?php
session_start();
require_once "../Config/connection.php";

// Verificar que haya sesión de empresa
if (!isset($_SESSION['empresa_id'])) {
    header("Location: ../View/pages/login.php");
    exit;
}

// Validar acción
$accion = $_POST['accion'] ?? '';

if ($accion === 'actualizar_info') {
    $empresa_id = $_SESSION['empresa_id'];

    $nombre_empresa = trim($_POST['nombre_empresa'] ?? '');
    $ciudad = trim($_POST['ciudad'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');

    // Validación mínima
    if (empty($nombre_empresa)) {
        header("Location: ../View/pages/user.php?update=error");
        exit;
    }

    // Actualizar en BD
    $stmt = $pdo->prepare("UPDATE empresas SET nombre_empresa = ?, ciudad = ?, direccion = ?, telefono = ? WHERE id = ?");
    $ok = $stmt->execute([$nombre_empresa, $ciudad, $direccion, $telefono, $empresa_id]);

    header("Location: ../View/pages/user.php?update=" . ($ok ? "success" : "error"));
    exit;
}

// Cambiar contraseña de empresa
if ($accion === 'cambiar_password') {
  $empresa_id = $_SESSION['empresa_id'];

  $actual = $_POST['actual'] ?? '';
  $nueva = $_POST['nueva'] ?? '';
  $confirmar = $_POST['confirmar'] ?? '';

  // Validar coincidencia
  if ($nueva !== $confirmar) {
      header("Location: ../View/pages/user.php?pass=nomatch");
      exit;
  }

  // Obtener contraseña actual
  $stmt = $pdo->prepare("SELECT password FROM empresas WHERE id = ?");
  $stmt->execute([$empresa_id]);
  $hash_actual = $stmt->fetchColumn();

  if (!$hash_actual || !password_verify($actual, $hash_actual)) {
      header("Location: ../View/pages/user.php?pass=invalid");
      exit;
  }

  // Encriptar y actualizar
  $nuevo_hash = password_hash($nueva, PASSWORD_DEFAULT);
  $update = $pdo->prepare("UPDATE empresas SET password = ? WHERE id = ?");
  $ok = $update->execute([$nuevo_hash, $empresa_id]);

  header("Location: ../View/pages/user.php?pass=" . ($ok ? "success" : "fail"));
  exit;
}


// Si llega aquí sin acción válida
header("Location: ../View/pages/user.php");
exit;
