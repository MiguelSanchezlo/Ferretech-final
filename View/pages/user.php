<?php
session_start();
require_once "../../Config/connection.php";
require_once "../../Model/UsuarioModel.php";

// Redirección si no está autenticado
if (!isset($_SESSION['usuario_id']) && !isset($_SESSION['empresa_id'])) {
    header("Location: login.php");
    exit;
}

// Cargar datos del usuario o emprea
$esEmpresa = isset($_SESSION['empresa_id']);

if ($esEmpresa) {
    $stmt = $pdo->prepare("SELECT * FROM empresas WHERE id = ?");
    $stmt->execute([$_SESSION['empresa_id']]);
    $empresa = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $model = new UsuarioModel($pdo);
    $usuario = $model->obtenerUsuarioPorId($_SESSION['usuario_id']);
}


include_once "../Templates/cabeza.php";
if (!$esEmpresa) {
    // NOTIFICACIONES DE USUARIO
    // Obtener notificaciones no leídas
    $stmt = $pdo->prepare("SELECT id, mensaje, fecha FROM notificaciones WHERE usuario_id = ? AND leido = 0 ORDER BY fecha DESC");
    $stmt->execute([$_SESSION['usuario_id']]);
    $notificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mostrar notificaciones si existen

    if ($notificaciones) {
        echo "<div class='notificaciones' style='background: #e3f7e3; border: 1px solid #b3e6b3; padding: 10px; margin: 15px 0;'>";
        foreach ($notificaciones as $n) {
            echo "<p>🔔 " . htmlspecialchars($n['mensaje']) . " <small>(" . $n['fecha'] . ")</small></p>";
        }
        echo "</div>";

        // Marcar como leídas
        $ids = implode(",", array_map("intval", array_column($notificaciones, 'id')));
        $pdo->query("UPDATE notificaciones SET leido = 1 WHERE id IN ($ids)");
    }
}

?>

<main>
    <h1>Perfil de Usuario</h1>

    <div class="profile-tabs">
        <button class="tab-button active" data-tab="perfil">Información Personal</button>
        <button class="tab-button" data-tab="seguridad">Seguridad</button>
        <button class="tab-button" data-tab="pedidos">Pedidos</button>
        <button class="tab-button" data-tab="historial">Historial</button>
    </div>

    <div class="tab-content" id="perfil">
        <?php include $esEmpresa ? "fragments/perfil_empresa.php" : "fragments/perfil.php"; ?>
    </div>

    <div class="tab-content" id="seguridad" style="display: none;">
        <?php include $esEmpresa ? "fragments/seguridad_empresa.php" : "fragments/seguridad.php"; ?>
    </div>

    <div class="tab-content" id="pedidos" style="display: none;">
        <?php include $esEmpresa ? "fragments/pedidos_empresa.php" : "fragments/pedidos.php"; ?>
    </div>

    <div class="tab-content" id="historial" style="display: none;">
        <?php include $esEmpresa ? "fragments/historial_empresa.php" : "fragments/historial.php"; ?>
    </div>
</main>

<?php include_once "../Templates/pie.php"; ?>

<script>
    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', () => {
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.style.display = 'none');

            button.classList.add('active');
            const tabId = button.getAttribute('data-tab');
            document.getElementById(tabId).style.display = 'block';
        });
    });
</script>