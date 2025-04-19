<?php
include_once "../../Config/connection.php"; // Ruta correcta a connection.php

if (!isset($pdo)) {
    die("Error: No se pudo conectar a la base de datos.");
}

include_once "../Templates/cabeza.php";  // Encabezado de la página
?>

<div class="inicio-mapa-container">
    <div id="map"></div>
</div>


<main class="products-section">
    <h1>Productos Destacados</h1>

    <div class="products-grid">
        <?php
        $stmt = $pdo->prepare("SELECT * FROM productos");
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                echo "<div class='product-card'>
                    <a href='infoproduct.php?id={$row['id']}' style='text-decoration: none; color: inherit; display: block;'>
                        <div class='product-image'>";

                if ($row['imagen']) {
                    echo "<img src='../../uploads/{$row['imagen']}' alt='" . htmlspecialchars($row['nombre']) . "' style='max-width: 100%; height: auto;'>";
                } else {
                    echo "<span class='placeholder'>🖼️</span>";
                }

                echo "    </div>
                        <h2 class='product-title'>" . htmlspecialchars($row['nombre']) . "</h2>
                        <p class='product-description'>" . htmlspecialchars($row['descripcion']) . "</p>
                        <div class='product-price'>$ " . number_format($row['precio'], 2) . "</div>
                    </a>
        
                    <form action='../../Controller/carrito_crud.php' method='POST'>
                        <input type='hidden' name='action' value='add'>
                        <input type='hidden' name='product_id' value='{$row['id']}'>
                        <input type='number' name='quantity' value='1' min='1'>
                        <button type='submit'>🛒Añadir al carrito</button>
                    </form>
                </div>";
            }
        } else {
            echo "No se encontraron productos.";
        }

        ?>
    </div>
</main>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const map = L.map('map').setView([4.710989, -74.072090], 6);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        fetch("../../api/api_empresas.php")
            .then(res => res.json())
            .then(empresas => {
                empresas.forEach(e => {
                    L.marker([e.latitud, e.longitud])
                        .addTo(map)
                        .bindPopup(`<strong>${e.nombre_empresa}</strong><br>${e.descripcion}<br><em>${e.ciudad}</em><br><a href="productos_empresa_publica.php?id=${e.id}" target="_blank">🛒 Ver productos</a>
`);
                });
            });
    });
</script>

<?php
include_once "../Templates/pie.php";  // Pie de página
?>