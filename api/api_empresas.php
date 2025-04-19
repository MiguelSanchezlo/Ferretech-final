<?php
require_once "../Config/connection.php";

$stmt = $pdo->query("SELECT nombre_empresa, descripcion, ciudad, latitud, longitud FROM empresas WHERE latitud IS NOT NULL AND longitud IS NOT NULL");
$empresas = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($empresas);
