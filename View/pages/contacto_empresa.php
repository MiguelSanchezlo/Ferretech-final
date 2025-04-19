<?php
require_once "../../Config/connection.php";
include_once "../Templates/cabeza.php";

$empresa_id = $_GET['id'] ?? null;
if (!$empresa_id) die("❌ Empresa no especificada.");

// Obtener nombre de la empresa
$stmt = $pdo->prepare("SELECT nombre_empresa FROM empresas WHERE id = ?");
$stmt->execute([$empresa_id]);
$empresa = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$empresa) die("❌ Empresa no encontrada.");
?>

<main class="contact-form">
    <h1>✉️ Contactar a <?= htmlspecialchars($empresa['nombre_empresa']) ?></h1>

    <form method="POST" action="../../Controller/enviar_mensaje_empresa.php">
        <input type="hidden" name="empresa_id" value="<?= $empresa_id ?>">

        <label>Tu Nombre:</label>
        <input type="text" name="nombre" required>

        <label>Tu Email:</label>
        <input type="email" name="email" required>

        <label>Mensaje:</label>
        <textarea name="mensaje" rows="5" required></textarea>

        <button type="submit">📩 Enviar Mensaje</button>
    </form>
</main>

<?php include_once "../Templates/pie.php"; ?>
