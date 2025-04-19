<?php
require_once '../../vendor/autoload.php';

use MercadoPago\SDK;
use MercadoPago\User;

SDK::setAccessToken("TEST-2018521019697613-041000-da7f0e6877aece97c36183f4e0907853-2329140996");

// Opcional (solo para entorno local si aún hay errores SSL)
SDK::setHttpClientOptions([
    CURLOPT_SSL_VERIFYPEER => true
]);

try {
    $user = User::get();

    echo "<h2>✅ Conexión exitosa con MercadoPago</h2>";
    echo "<p><strong>ID:</strong> " . $user->id . "</p>";
    echo "<p><strong>País:</strong> " . $user->country_id . "</p>";
    echo "<p><strong>Email:</strong> " . $user->email . "</p>";
} catch (Exception $e) {
    echo "<h2 style='color:red;'>❌ Error al conectar con MercadoPago</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
