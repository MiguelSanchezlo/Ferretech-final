<?php
// Incluye los archivos necesarios
include_once "../../Config/connection.php";  // Conexión a la base de datos
include_once "../Templates/cabeza.php";  // Encabezado de la página
?>

<main class="container">
    <section class="products-section">
        <?php
        // Obtener categorías únicas
        $stmtCat = $pdo->query("SELECT DISTINCT categoria FROM productos WHERE categoria IS NOT NULL AND categoria != ''");
        $categorias = $stmtCat->fetchAll(PDO::FETCH_COLUMN);
        ?>

        <form method="GET" style="margin-bottom: 20px;">
            <input type="text" name="busqueda" placeholder="🔍 Buscar productos..." value="<?= htmlspecialchars($_GET['busqueda'] ?? '') ?>">

            <label>Precio mínimo:</label>
            <input type="number" step="0.01" name="min" value="<?= htmlspecialchars($_GET['min'] ?? '') ?>" style="width: 80px;">

            <label>Precio máximo:</label>
            <input type="number" step="0.01" name="max" value="<?= htmlspecialchars($_GET['max'] ?? '') ?>" style="width: 80px;">

            <label>Categoría:</label>
            <select name="categoria">
                <option value="">-- Todas --</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>" <?= ($_GET['categoria'] ?? '') == $cat ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Filtrar</button>
            <?php if (!empty($_GET)): ?>
                <a href="product.php">❌ Limpiar filtros</a>
            <?php endif; ?>
        </form>

        <div class="products-grid">
            <?php
            $condiciones = [];
            $parametros = [];

            // Filtro por nombre
            if (!empty($_GET['busqueda'])) {
                $condiciones[] = "nombre LIKE ?";
                $parametros[] = "%" . $_GET['busqueda'] . "%";
            }

            // Filtro por precio mínimo
            if (!empty($_GET['min'])) {
                $condiciones[] = "precio >= ?";
                $parametros[] = $_GET['min'];
            }

            // Filtro por precio máximo
            if (!empty($_GET['max'])) {
                $condiciones[] = "precio <= ?";
                $parametros[] = $_GET['max'];
            }

            // Filtro por categoría
            if (!empty($_GET['categoria'])) {
                $condiciones[] = "categoria LIKE ?";
                $parametros[] = "%" . $_GET['categoria'] . "%";
            }

            $sql = "SELECT * FROM productos";
            if ($condiciones) {
                $sql .= " WHERE " . implode(" AND ", $condiciones);
            }

            $stmt = $pdo->prepare($sql);
            $stmt->execute($parametros);


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
    </section>
</main>

<?php
include_once "../Templates/pie.php";  // Pie de página
?>