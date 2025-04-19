<?php
$model = new UsuarioModel($pdo);
$usuario = $model->obtenerUsuarioPorId($_SESSION['usuario_id']);
?>

<h3>🧑 Información Personal</h3>

<?php if (isset($_GET['update'])): ?>
  <p style="color: <?= $_GET['update'] === 'success' ? 'green' : 'red' ?>;">
    <?= $_GET['update'] === 'success' ? '✅ Datos actualizados correctamente.' : '❌ Error al actualizar.' ?>
  </p>
<?php endif; ?>

<form method="POST" action="../../Controller/UsuarioController.php">
  <input type="hidden" name="accion" value="actualizar_info">

  <div class="form-group">
    <label>Nombre</label>
    <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>">
  </div>

  <div class="form-group">
    <label>Apellido</label>
    <input type="text" name="apellido" value="<?= htmlspecialchars($usuario['apellido']) ?>">
  </div>

  <div class="form-group">
    <label>Email</label>
    <input type="email" value="<?= htmlspecialchars($usuario['email']) ?>" readonly>
  </div>

  <button type="submit" class="btn">💾 Guardar Cambios</button>
</form>
