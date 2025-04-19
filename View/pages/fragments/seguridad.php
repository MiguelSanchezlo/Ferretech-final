<h3>🔐 Cambiar Contraseña</h3>

<?php if (isset($_GET['pass'])): ?>
  <p style="color: <?= $_GET['pass'] === 'success' ? 'green' : 'red' ?>;">
    <?php
      switch ($_GET['pass']) {
        case 'success': echo '✅ Contraseña actualizada correctamente.'; break;
        case 'fail': echo '❌ Error al actualizar la contraseña.'; break;
        case 'invalid': echo '❌ La contraseña actual no es correcta.'; break;
        case 'nomatch': echo '❌ Las contraseñas no coinciden.'; break;
      }
    ?>
  </p>
<?php endif; ?>

<form id="form-password" method="POST" action="../../Controller/UsuarioController.php">
  <input type="hidden" name="accion" value="cambiar_password">

  <div class="form-group">
    <label>Contraseña Actual</label>
    <input type="password" name="actual" required>
  </div>

  <div class="form-group">
    <label>Nueva Contraseña</label>
    <input type="password" name="nueva" required>
  </div>

  <div class="form-group">
    <label>Confirmar Nueva Contraseña</label>
    <input type="password" name="confirmar" required>
  </div>

  <button type="submit" class="btn">🔁 Cambiar Contraseña</button>
</form>

<script>
document.getElementById("form-password").addEventListener("submit", function (e) {
  const nueva = document.querySelector("input[name='nueva']").value;
  const confirmar = document.querySelector("input[name='confirmar']").value;

  if (nueva !== confirmar) {
    alert("❌ Las contraseñas no coinciden.");
    e.preventDefault();
  } else if (nueva.length < 6) {
    alert("❌ La contraseña debe tener al menos 6 caracteres.");
    e.preventDefault();
  }
});
</script>
