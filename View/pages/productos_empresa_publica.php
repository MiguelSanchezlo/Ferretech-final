<?php
require_once "../../Config/connection.php";
include_once "../Templates/cabeza.php";

$empresa_id = $_GET['id'] ?? null;
if (!$empresa_id) die("❌ Empresa no especificada.");

// Obtener nombre de la empresa
$stmtEmp = $pdo->prepare("SELECT id, nombre_empresa, descripcion FROM empresas WHERE id = ?");
$stmtEmp->execute([$empresa_id]);
$empresa = $stmtEmp->fetch(PDO::FETCH_ASSOC);
if (!$empresa) die("❌ Empresa no encontrada.");

// Obtener productos de esa empresa
$stmt = $pdo->prepare("SELECT * FROM productos WHERE empresa_id = ?");
$stmt->execute([$empresa_id]);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="container" style="padding: 20px;">
  <h1>🏪 Productos de <?= htmlspecialchars($empresa['nombre_empresa']) ?></h1>
  <a href="mapa_empresas.php" style="display: inline-block; margin: 10px 0 20px; background: #eee; padding: 8px 12px; border-radius: 6px; text-decoration: none;">
    🗺️ Volver al mapa
  </a>

  <p><?= nl2br(htmlspecialchars($empresa['descripcion'])) ?></p>
  <a href="contacto_empresa.php?id=<?= $empresa_id ?>">✉️ Contactar</a>

  <?php if (count($productos) > 0): ?>
    <div class="products-grid">
      <?php foreach ($productos as $row): ?>
        <div class="product-card">
          <a href="infoproducto.php?id=<?= $row['id'] ?>" style="text-decoration: none; color: inherit; display: block;">
            <div class="product-image">
              <?php if ($row['imagen']): ?>
                <img src="../../uploads/<?= $row['imagen'] ?>" alt="<?= htmlspecialchars($row['nombre']) ?>" style="max-width: 100%; height: auto;">
              <?php else: ?>
                <span class='placeholder'>🖼️</span>
              <?php endif; ?>
            </div>
            <h2 class="product-title"><?= htmlspecialchars($row['nombre']) ?></h2>
            <p class="product-description"><?= htmlspecialchars($row['descripcion']) ?></p>
            <div class="product-price">$ <?= number_format($row['precio'], 2) ?></div>
          </a>

          <form action="../../Controller/carrito_crud.php" method="POST">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
            <input type="number" name="quantity" value="1" min="1">
            <button type="submit">Añadir al carrito</button>
          </form>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p>❌ Esta empresa aún no ha publicado productos.</p>
  <?php endif; ?>
</main>

<?php include_once "../Templates/pie.php"; ?>