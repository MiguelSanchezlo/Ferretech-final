<?php
session_start();
include_once "../Config/connection.php"; // Conexión a la base de datos

if (!isset($pdo)) {
  die("Error: No se pudo conectar a la base de datos.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $action = $_POST['action'];
  $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
  $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

  if ($product_id > 0) {
    // Obtener detalles del producto desde la base de datos
    $query = $pdo->prepare("SELECT id, nombre, precio FROM productos WHERE id = ?");
    $query->execute([$product_id]);
    $product = $query->fetch(PDO::FETCH_ASSOC);
  }

  if ($action == 'add' && $product) {
    if (!isset($_SESSION['car'][$product_id])) {
      $_SESSION['car'][$product_id] = [
        'product_id' => $product['id'],
        'product_name' => $product['nombre'],
        'price' => $product['precio'],
        'quantity' => $quantity
      ];
    } else {
      $_SESSION['car'][$product_id]['quantity'] += $quantity;
    }
  }

  if ($action == 'update' && isset($_SESSION['car'][$product_id])) {
    $_SESSION['car'][$product_id]['quantity'] = max(1, $quantity);

    // Calcular nuevo subtotal del producto
    $subtotal = $_SESSION['car'][$product_id]['price'] * $_SESSION['car'][$product_id]['quantity'];

    // Calcular nuevo total del carrito
    $total = 0;
    foreach ($_SESSION['car'] as $item) {
      $total += $item['price'] * $item['quantity'];
    }

    echo json_encode([
      "success" => true,
      "subtotal" => $subtotal,
      "total" => $total
    ]);
    exit;
  }



  if ($action == 'remove' && isset($_SESSION['car'][$product_id])) {
    unset($_SESSION['car'][$product_id]);
  }
}

// Redirigir de vuelta al carrito
header("Location: ../View/Pages/car.php");
exit;
