<?php
// Ya deberías tener $empresa cargado desde user.php
?>

<h3>🏢 Información de la Empresa</h3>

<?php if (isset($_GET['update'])): ?>
  <p style="color: <?= $_GET['update'] === 'success' ? 'green' : 'red' ?>;">
    <?= $_GET['update'] === 'success' ? '✅ Datos actualizados correctamente.' : '❌ Error al actualizar.' ?>
  </p>
<?php endif; ?>

<form method="POST" action="../../Controller/EmpresaController.php">
  <input type="hidden" name="accion" value="actualizar_info">

  <div class="form-group">
    <label>Nombre de la Empresa</label>
    <input type="text" name="nombre_empresa" value="<?= htmlspecialchars($empresa['nombre_empresa']) ?>">
  </div>

  <div class="form-group">
    <label>Email de Contacto</label>
    <input type="email" value="<?= htmlspecialchars($empresa['email_contacto']) ?>" readonly>
  </div>

  <div class="form-group">
    <label>Ciudad</label>
    <input type="text" name="ciudad" value="<?= htmlspecialchars($empresa['ciudad']) ?>">
  </div>

  <div class="form-group">
    <label>Dirección</label>
    <input type="text" name="direccion" value="<?= htmlspecialchars($empresa['direccion']) ?>">
  </div>

  <div class="form-group">
    <label>Teléfono</label>
    <input type="text" name="telefono" value="<?= htmlspecialchars($empresa['telefono']) ?>">
  </div>

  <button type="submit" class="btn">💾 Guardar Cambios</button>
</form>
