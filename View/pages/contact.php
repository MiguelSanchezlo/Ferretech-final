<?php
include_once "../Templates/cabeza.php";
?>
<main class="contact-container">
    <h1>📬 Contáctanos</h1>
    <p>¿Tienes alguna pregunta o necesitas ayuda? Llena el siguiente formulario:</p>

    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div class="contact-success">
            ✅ Tu mensaje fue enviado correctamente. ¡Gracias por contactarnos!
        </div>
    <?php endif; ?>

    <form method="POST" action="../../Controller/contacto_mensaje.php">
        <label>Nombre:</label>
        <input type="text" name="nombre" required>

        <label>Correo:</label>
        <input type="email" name="email" required>

        <label>Mensaje:</label>
        <textarea name="mensaje" rows="5" required></textarea>

        <button type="submit">📨 Enviar</button>
    </form>
</main>


<?php
include_once "../Templates/pie.php";
?>
