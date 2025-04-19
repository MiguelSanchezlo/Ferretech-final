<?php
session_start();
require_once "../../Config/connection.php";

// Verifica que sea administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: index.php");
    exit;
}

// Filtros dinámicos
$condiciones = [];
$valores = [];

if (!empty($_GET['buscar'])) {
    $condiciones[] = "(nombre_empresa LIKE ? OR NIT LIKE ?)";
    $buscar = "%" . $_GET['buscar'] . "%";
    $valores[] = $buscar;
    $valores[] = $buscar;
}

if (!empty($_GET['tipo_negocio'])) {
    $condiciones[] = "tipo_negocio = ?";
    $valores[] = $_GET['tipo_negocio'];
}

$sql = "SELECT id, nombre_empresa, NIT, tipo_negocio, ciudad, email_contacto, telefono, fecha_registro FROM empresas";

if (!empty($condiciones)) {
    $sql .= " WHERE " . implode(" AND ", $condiciones);
}

$sql .= " ORDER BY fecha_registro DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($valores);
$empresas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include_once "../Templates/cabeza.php"; ?>

<main>
    <h1>🏢 Empresas Registradas</h1>

    <form method="GET" class="admin-filtros">
        <input type="text" name="buscar" placeholder="Buscar por nombre o NIT" value="<?= $_GET['buscar'] ?? '' ?>">
        <select name="tipo_negocio">
            <option value="">Todos los tipos</option>
            <option value="retail" <?= ($_GET['tipo_negocio'] ?? '') === 'retail' ? 'selected' : '' ?>>Venta al por menor</option>
            <option value="wholesale" <?= ($_GET['tipo_negocio'] ?? '') === 'wholesale' ? 'selected' : '' ?>>Venta al por mayor</option>
            <option value="manufacturer" <?= ($_GET['tipo_negocio'] ?? '') === 'manufacturer' ? 'selected' : '' ?>>Fabricante</option>
        </select>
        <button type="submit">🔍 Buscar</button>
        <a href="empresas_admin.php">Limpiar</a>
    </form>

    <?php if (count($empresas) > 0): ?>
        <table class="empresas-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>NIT</th>
                    <th>Tipo</th>
                    <th>Ciudad</th>
                    <th>Correo</th>
                    <th>Teléfono</th>
                    <th>Fecha</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($empresas as $empresa): ?>
                    <tr>
                        <td><?= $empresa['id'] ?></td>
                        <td><?= $empresa['nombre_empresa'] ?></td>
                        <td><?= $empresa['NIT'] ?></td>
                        <td><?= ucfirst($empresa['tipo_negocio']) ?></td>
                        <td><?= $empresa['ciudad'] ?></td>
                        <td><?= $empresa['email_contacto'] ?></td>
                        <td><?= $empresa['telefono'] ?></td>
                        <td><?= $empresa['fecha_registro'] ?></td>
                        <td>
                            <form method="POST" action="eliminar_empresa.php" onsubmit="return confirm('¿Eliminar esta empresa?');">
                                <input type="hidden" name="empresa_id" value="<?= $empresa['id'] ?>">
                                <button type="submit">🗑️ Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No se encontraron empresas.</p>
    <?php endif; ?>
</main>

<?php include_once "../Templates/pie.php"; ?>