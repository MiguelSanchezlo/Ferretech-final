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
    $condiciones[] = "(nombre LIKE ? OR email LIKE ?)";
    $buscar = "%" . $_GET['buscar'] . "%";
    $valores[] = $buscar;
    $valores[] = $buscar;
}

if (!empty($_GET['rol'])) {
    $condiciones[] = "rol = ?";
    $valores[] = $_GET['rol'];
}

$sql = "SELECT id, nombre, apellido, email, rol, fecha_registro FROM usuarios";

if (!empty($condiciones)) {
    $sql .= " WHERE " . implode(" AND ", $condiciones);
}

$sql .= " ORDER BY fecha_registro DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($valores);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include_once "../Templates/cabeza.php"; ?>

<main>
    <h1>👤 Usuarios Registrados</h1>

    <form method="GET" class="admin-filtros">
        <input type="text" name="buscar" placeholder="Buscar por nombre o correo" value="<?= $_GET['buscar'] ?? '' ?>">
        <select name="rol">
            <option value="">Todos los roles</option>
            <option value="cliente" <?= ($_GET['rol'] ?? '') === 'cliente' ? 'selected' : '' ?>>Cliente</option>
            <option value="administrador" <?= ($_GET['rol'] ?? '') === 'administrador' ? 'selected' : '' ?>>Administrador</option>
        </select>
        <button type="submit">🔍 Buscar</button>
        <a href="usuarios_admin.php">Limpiar</a>
    </form>

    <?php if (count($usuarios) > 0): ?>
        <table class="usuarios-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Fecha de Registro</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= $user['nombre'] . ' ' . $user['apellido'] ?></td>
                        <td><?= $user['email'] ?></td>
                        <td><?= ucfirst($user['rol']) ?></td>
                        <td><?= $user['fecha_registro'] ?></td>
                        <td>
                            <?php if ($user['rol'] !== 'administrador'): ?>
                                <form method="POST" action="eliminar_usuario.php" onsubmit="return confirm('¿Eliminar este usuario?');">
                                    <input type="hidden" name="usuario_id" value="<?= $user['id'] ?>">
                                    <button type="submit">🗑️ Eliminar</button>
                                </form>
                            <?php else: ?>
                                <span style="color: gray;">Protegido</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No se encontraron usuarios.</p>
    <?php endif; ?>
</main>

<?php include_once "../Templates/pie.php"; ?>