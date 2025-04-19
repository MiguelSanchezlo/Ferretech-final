<?php include_once "../Templates/cabeza.php"; ?>

<main>
    <div class="error-container">
        <h1 style="color: red;">❌ Pago no procesado</h1>
        <p>Tu pago fue cancelado o falló durante el proceso.</p>
        <p>Puedes intentar nuevamente desde tu carrito.</p>
        <a href="car.php" class="btn">🔄 Volver al carrito</a>
        <a href="product.php" class="btn">🛒 Ir al catálogo</a>
    </div>
</main>

<?php include_once "../Templates/pie.php"; ?>