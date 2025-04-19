<?php
session_start();
require_once "../../Config/connection.php";

if (!isset($_SESSION['empresa_id'])) {
  header("Location: login.php");
  exit;
}

$empresa_id = $_SESSION['empresa_id'];

$stmt = $pdo->prepare("SELECT * FROM mensajes_empresa WHERE empresa_id = ? ORDER BY fecha DESC");
$stmt->execute([$empresa_id]);
$mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);

include_once "../Templates/cabeza.php";

if (isset($_GET['enviado']) && $_GET['enviado'] == 1): ?>
  <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 6px; margin: 15px 0; border: 1px solid #c3e6cb;">
    ✅ Respuesta enviada correctamente al cliente.
  </div>
<?php endif; ?>

<main class="mensajes-container">
  <h1>📥 Mensajes Recibidos</h1>

  <?php if (count($mensajes) > 0): ?>
    <table>
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Email</th>
          <th>Mensaje</th>
          <th>Fecha</th>
          <th>Accion</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($mensajes as $m): ?>
          <tr>
            <td><?= htmlspecialchars($m['nombre']) ?></td>
            <td><?= htmlspecialchars($m['email']) ?></td>
            <td><?= nl2br(htmlspecialchars($m['mensaje'])) ?></td>
            <td><?= $m['fecha'] ?></td>
            <td>
              <a href="#" onclick="abrirModal('<?= $m['email'] ?>')">📧 Responder</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>📭 Aún no tienes mensajes.</p>
  <?php endif; ?>
</main>
<!-- Modal -->
<div id="modalRespuesta" class="modal" style="display:none;">
  <div class="modal-content">
    <span class="close" onclick="cerrarModal()" cursor: pointer;">❌</span>
    <h2>📧 Responder Mensaje</h2>
    <form method="POST" action="../../Controller/responder_mensaje.php">
      <input type="hidden" id="emailDestino" name="email">

      <label>Asunto:</label>
      <input type="text" name="asunto" value="Respuesta desde FerreTech" required>

      <label>Mensaje:</label>
      <textarea name="mensaje" rows="5" required></textarea>

      <button type="submit">Enviar respuesta</button>
    </form>
  </div>
</div>

<!-- Modal Styling -->
<style>
  .modal {
    position: fixed;
    z-index: 999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
  }
</style>
<script>
  function abrirModal(email) {
    document.getElementById("modalRespuesta").style.display = "block";
    document.getElementById("emailDestino").value = email;
  }

  function cerrarModal() {
    document.getElementById("modalRespuesta").style.display = "none";
  }
</script>

<?php include_once "../Templates/pie.php"; ?>