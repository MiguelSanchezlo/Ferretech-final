<?php
session_start();
require_once "../../Config/connection.php";

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: index.php");
    exit;
}
?>

<?php include_once "../Templates/cabeza.php"; ?>

<main>
    <h1>Panel de Administración</h1>

    <div class="admin-actions">
        <ul>
            <li><a href="usuarios_admin.php">👤 Ver Usuarios</a></li>
            <li><a href="empresas_admin.php">🏢 Ver Empresas</a></li>
            <li><a href="pedidos_admin.php">📦 Ver Todos los Pedidos</a></li>
            <li><a href="crear_admin.php">➕ Crear Nuevo Administrador</a></li>
        </ul>
    </div>
</main>


<?php include_once "../Templates/pie.php"; ?>
