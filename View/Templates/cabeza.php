<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$saludo = "Bienvenido a FerretTech";

if (isset($_SESSION['nombre'])) {
    $saludo = "Bienvenido, " . $_SESSION['nombre'];
} elseif (isset($_SESSION['nombre_empresa'])) {
    $saludo = "Bienvenido, " . $_SESSION['nombre_empresa'];
}

?>

<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>FerretTech - Inicio</title>



    <link rel="stylesheet" href="../../style/css/style.css">




    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />


    <title>Carrito de Compras - FerretTech</title>
    <link rel="stylesheet" href="../../style/css/stylecart.css">
    <style>
        #map {
            height: 500px !important;
            width: 100%;
            min-height: 400px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-top: 20px;
            z-index: 1;
        }
    </style>

</head>

<body>

    

    <header>
        <a href="index.php" class="logo">FerretTech</a>
        <div class="search-bar">
            <form action="product.php" method="GET" style="display: flex;">
                <input type="text" name="busqueda" placeholder="Buscar productos...">
                <button type="submit">🔍</button>
            </form>
        </div>
        <a href="#" class="menu-icon" onclick="toggleMenu()">☰</a>
        <div class="nav-links">
            <?php if (isset($_SESSION['empresa_id'])): ?>
                <a href="productos_empresa.php">Mis Productos</a>
            <?php endif; ?>

            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'administrador'): ?>
                <a href="admin_dashboard.php">Admin</a>
            <?php endif; ?>
            <a href="mapa_empresas.php">Mapa</a>
            <a href="product.php">Productos</a>
            <a href="contact.php">Contacto</a>
            <a href="user.php">Usuario</a>
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <a href="mis_pedidos.php">Mis Pedidos</a>
            <?php elseif (isset($_SESSION['empresa_id'])): ?>
                <a href="pedidos_empresa.php">Pedidos Recibidos</a>
                <a href="mensajes_recibidos.php">Mensajes</a>
            <?php endif; ?>
            <a href="car.php">🛒</a>
            <div class="user-welcome">
                <span>¡<?php echo $saludo; ?>!</span>
                <span>
                    <?php if (isset($_SESSION['nombre']) || isset($_SESSION['nombre_empresa'])): ?>
                        <a href="../../Controller/logout.php">Cerrar Sesión</a>
                    <?php else: ?>
                        <a href="login.php">Iniciar Sesión</a> /
                        <a href="register.php">Registrarse</a>
                    <?php endif; ?>
                </span>
            </div>
        </div>
    </header>
    <div class="dropdown-menu" id="dropdownMenu">
        <a href="product.php">Productos</a>
        <a href="contact.php">Contacto</a>
        <a href="user.php">Usuario</a>
        <a href="car.php">Carrito de Compras</a>
        <a href="login.php">Iniciar Sesión</a>
        <a href="register.php">Registrarse</a>
    </div>