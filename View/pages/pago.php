<?php
session_start();

error_reporting(E_ALL & ~E_DEPRECATED);
$_SESSION['direccion_envio'] = $_POST['direccion'] ?? '';
$_SESSION['telefono_envio'] = $_POST['telefono'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['direccion_envio'] = $_POST['direccion_envio'] ?? '';
    $_SESSION['telefono_envio'] = $_POST['telefono_envio'] ?? '';
}

require_once '../../vendor/autoload.php';

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;

if (!isset($_SESSION['usuario_id']) || empty($_SESSION['car'])) {
    header("Location: car.php");
    exit;
}

MercadoPagoConfig::setAccessToken("APP_USR-4030564772297099-041015-2fededc559d8668833e791e69d4e6987-2385143200");
MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);

// Preparar items
$items = [];
foreach ($_SESSION['car'] as $item) {
    $items[] = [
        "title" => $item['product_name'],
        "quantity" => $item['quantity'],
        "unit_price" => intval($item['price']), // recomendable usar int para evitar errores
        "currency_id" => "COP"
    ];
}

// Configurar URLs
$back_urls = [
    "success" => "http://localhost:8082/hoy/ProyectoFerretchDM/ProyectoFerretchDM/View/pages/pago_exitoso.php",
    "failure" => "http://localhost:8082/hoy/ProyectoFerretchDM/ProyectoFerretchDM/View/pages/pago_error.php",
    "pending" => "http://localhost:8082/hoy/ProyectoFerretchDM/ProyectoFerretchDM/View/pages/pago_pendiente.php"
];


// Crear preferencia
$request = [
    "items" => $items,
    "back_urls" => $back_urls,
    "auto_return" => "approved"
];

$client = new PreferenceClient();

try {
    $preference = $client->create($request);
    header("Location: " . $preference->init_point);
    exit;
} catch (Exception $e) {
    echo "<h2>❌ Error al crear preferencia</h2>";
    echo "<pre>";
    print_r($e);
    echo "</pre>";
}
