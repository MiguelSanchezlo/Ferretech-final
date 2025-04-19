-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 19-04-2025 a las 07:33:37
-- Versión del servidor: 9.1.0
-- Versión de PHP: 8.3.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `ferretech`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalles_pedido`
--

DROP TABLE IF EXISTS `detalles_pedido`;
CREATE TABLE IF NOT EXISTS `detalles_pedido` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pedido_id` int DEFAULT NULL,
  `producto_id` int DEFAULT NULL,
  `cantidad` int NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pedido_id` (`pedido_id`),
  KEY `producto_id` (`producto_id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `detalles_pedido`
--

INSERT INTO `detalles_pedido` (`id`, `pedido_id`, `producto_id`, `cantidad`, `precio_unitario`) VALUES
(1, 1, 1, 2, 25.99),
(2, 1, 2, 1, 5.50),
(3, 2, 3, 5, 8.75),
(4, 3, 1, 3, 25.99),
(5, 4, 1, 4, 25.99),
(6, 5, 1, 1, 25.99),
(7, 5, 2, 1, 5.50),
(8, 6, 1, 1, 25.99),
(9, 6, 2, 1, 5.50),
(10, 6, 3, 1, 8.75),
(11, 7, 1, 1, 2599.00),
(12, 8, 1, 1, 2599.00),
(13, 9, 1, 1, 2599.00),
(14, 10, 1, 1, 2599.00),
(15, 11, 1, 1, 2599.00),
(16, 12, 1, 1, 1000.00),
(17, 13, 1, 1, 1000.00),
(18, 14, 1, 5, 2.00),
(19, 15, 1, 500, 2.00),
(20, 16, 1, 500, 2.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresas`
--

DROP TABLE IF EXISTS `empresas`;
CREATE TABLE IF NOT EXISTS `empresas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre_empresa` varchar(150) NOT NULL,
  `NIT` varchar(50) NOT NULL,
  `tipo_negocio` varchar(100) NOT NULL,
  `direccion` text NOT NULL,
  `ciudad` varchar(100) NOT NULL,
  `codigo_postal` varchar(20) NOT NULL,
  `nombre_contacto` varchar(100) NOT NULL,
  `email_contacto` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `descripcion` text,
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `latitud` decimal(10,8) DEFAULT NULL,
  `longitud` decimal(11,8) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `NIT` (`NIT`),
  UNIQUE KEY `email_contacto` (`email_contacto`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `empresas`
--

INSERT INTO `empresas` (`id`, `nombre_empresa`, `NIT`, `tipo_negocio`, `direccion`, `ciudad`, `codigo_postal`, `nombre_contacto`, `email_contacto`, `telefono`, `password`, `descripcion`, `fecha_registro`, `latitud`, `longitud`) VALUES
(1, 'Ferretería El Tornillo', '900123456-7', 'Ferretería', 'Av. Principal #45', 'Bogotá', '110011', 'Carlos Ramírez', 'contacto@tornillo.com', '3123456789', 'claveSegura', 'Especialistas en herramientas y materiales de construcción.', '2025-03-06 20:27:40', NULL, NULL),
(3, 'mojang', '123456', 'manufacturer', 'suecia', 'algun lugar', '12345', 'nose', 'nose@nose.com', '123456', '$2y$10$WM0rqibAfgQFyxfQPIKRBu/254crEmT4Fxb8npbxJCNrR0NAAee42', 'el mejor juego', '2025-04-02 02:11:18', 6.30403752, -74.58216095),
(6, 'riot', '1212', 'wholesale', 'medellin', 'capital', '12345', 'nose', 'riot3@riot2.com', '0000000', '$2y$10$sAsBDRjaPLREoq/fwIEQZuIGNcddS1Gf4EqcSVeZvgQUR1GnZJPVi', 'safda', '2025-04-13 09:36:28', 6.29175249, -75.56636810),
(7, 'riot', '121212', 'manufacturer', 'medellin', 'capital', '12345', 'nose', 'rr@riot2.com', '0000000', '$2y$10$qMG2u08FkqGuZv518DwiP.IDgZZCKQTze51SHCC9SVcJjFssmTMd6', 'safdf', '2025-04-15 04:27:07', 6.23680756, -75.57117462),
(8, 'riot', '67567', 'wholesale', 'medellin', 'capital', '12345', 'nose', 'r@r.com', '0000000', '$2y$10$uQgvTL6jOlB3AqKOhhlwp.tNguuPmlYw1jtT.67I/osATifTM/Gem', 'sdghgh', '2025-04-15 22:50:12', 6.26376884, -75.60035706),
(9, 'mojang', '1', 'manufacturer', 'suecia', 'capital', '12345', 'nose', 'nemetrinus@gmail.com', '0000000', '$2y$10$1yPPFoM84YZvfNKB0ACk1OkPsezIyBtWkSljgFx009VqcKg/Bjsri', 'preuba', '2025-04-17 06:45:57', 6.33201900, -75.55881500);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes_contacto`
--

DROP TABLE IF EXISTS `mensajes_contacto`;
CREATE TABLE IF NOT EXISTS `mensajes_contacto` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `mensaje` text,
  `fecha` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes_empresa`
--

DROP TABLE IF EXISTS `mensajes_empresa`;
CREATE TABLE IF NOT EXISTS `mensajes_empresa` (
  `id` int NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `mensaje` text,
  `fecha` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `empresa_id` (`empresa_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `mensajes_empresa`
--

INSERT INTO `mensajes_empresa` (`id`, `empresa_id`, `nombre`, `email`, `mensaje`, `fecha`) VALUES
(1, 3, 'Miguel', 'miguelsl0927@gmail.com', 'Prueba del mensaje de contact', '2025-04-17 01:31:44'),
(2, 3, 'yo', 'nemetrinus@gmail.com', 'aaaaaaaaaaaaaa', '2025-04-17 01:44:03'),
(3, 9, 'wed', 'miguelsl0927@gmail.com', 'aaaaaassssss', '2025-04-17 01:51:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

DROP TABLE IF EXISTS `notificaciones`;
CREATE TABLE IF NOT EXISTS `notificaciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int DEFAULT NULL,
  `empresa_id` int DEFAULT NULL,
  `mensaje` text NOT NULL,
  `leido` tinyint(1) DEFAULT '0',
  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

DROP TABLE IF EXISTS `pagos`;
CREATE TABLE IF NOT EXISTS `pagos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pedido_id` int DEFAULT NULL,
  `metodo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `estado` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `pedido_id` (`pedido_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id`, `pedido_id`, `metodo`, `estado`, `fecha`) VALUES
(1, 1, 'tarjeta', 'pendiente', '2025-03-06 20:29:08'),
(2, 2, 'paypal', 'completado', '2025-03-06 20:29:08'),
(3, 8, '', '', '2025-04-11 04:36:55'),
(4, 9, '', '', '2025-04-11 04:45:04'),
(5, 10, '', '', '2025-04-11 04:48:41'),
(6, 11, '', '', '2025-04-11 04:57:40'),
(7, 12, '', '', '2025-04-11 04:59:10'),
(8, 13, 'account_money', 'approved', '2025-04-11 05:00:50'),
(9, 15, 'account_money', 'approved', '2025-04-16 04:54:25'),
(10, 16, 'account_money', 'approved', '2025-04-18 07:47:41');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

DROP TABLE IF EXISTS `pedidos`;
CREATE TABLE IF NOT EXISTS `pedidos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int DEFAULT NULL,
  `empresa_id` int DEFAULT NULL,
  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `total` decimal(10,2) NOT NULL,
  `estado` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `direccion_envio` varchar(255) DEFAULT NULL,
  `telefono_envio` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `empresa_id` (`empresa_id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `usuario_id`, `empresa_id`, `fecha`, `total`, `estado`, `direccion_envio`, `telefono_envio`) VALUES
(1, 1, 1, '2025-03-06 20:29:08', 100.50, 'pendiente', NULL, NULL),
(2, 1, 2, '2025-03-06 20:29:08', 55.75, 'pagado', NULL, NULL),
(3, 5, 1, '2025-03-31 05:20:19', 77.97, 'pendiente', NULL, NULL),
(4, 5, 1, '2025-03-31 05:21:27', 103.96, 'pendiente', NULL, NULL),
(5, 4, 1, '2025-03-31 13:33:19', 31.49, 'pendiente', NULL, NULL),
(6, 4, 2, '2025-04-10 04:21:27', 40.24, 'pendiente', NULL, NULL),
(7, 4, 1, '2025-04-10 20:45:42', 2599.00, 'pagado', NULL, NULL),
(8, 4, 1, '2025-04-11 04:36:55', 2599.00, 'pagado', NULL, NULL),
(9, 4, 1, '2025-04-11 04:45:04', 2599.00, 'pagado', NULL, NULL),
(10, 4, 1, '2025-04-11 04:48:41', 2599.00, 'pagado', NULL, NULL),
(11, 4, 1, '2025-04-11 04:57:40', 2599.00, 'pagado', NULL, NULL),
(12, 4, 1, '2025-04-11 04:59:10', 1000.00, 'pagado', NULL, NULL),
(13, 4, 1, '2025-04-11 05:00:50', 1000.00, 'pagado', NULL, NULL),
(14, 5, 3, '2025-04-15 21:49:21', 10.00, 'Pagado', NULL, NULL),
(15, 4, 3, '2025-04-16 04:54:25', 1000.00, 'Enviado', 'girar', '1234567'),
(16, 6, 3, '2025-04-18 07:47:41', 1000.00, 'pagado', 'aaaa', '1212');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

DROP TABLE IF EXISTS `productos`;
CREATE TABLE IF NOT EXISTS `productos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text,
  `precio` decimal(10,2) NOT NULL,
  `stock` int NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `categoria` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `empresa_id` (`empresa_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `empresa_id`, `nombre`, `descripcion`, `precio`, `stock`, `imagen`, `fecha_creacion`, `categoria`) VALUES
(1, 3, 'tuerca', 'una tuerca', 2.00, 13500, 'prod_67fb68d5ed6b8.jfif', '2025-04-13 02:33:41', 'Construcción'),
(2, 3, 'martillo', 'una martillo que martilla', 1000.00, 1000, 'prod_67ff4a4aad6d0.png', '2025-04-16 01:12:26', 'Herramientas'),
(3, 1, 'Martillo de Acero', 'Ideal para clavar con precisión.', 25.00, 100, NULL, '2025-04-16 02:31:55', 'Herramientas'),
(4, 1, 'Taladro Eléctrico', 'Potente y portátil.', 99.00, 50, NULL, '2025-04-16 02:31:55', 'Electricidad'),
(5, 3, 'Brocha 3\"', 'Perfecta para pintar superficies medianas.', 5.00, 200, NULL, '2025-04-16 02:31:55', 'Pintura'),
(6, 3, 'Pala de Jardín', 'Reforzada, mango ergonómico.', 18.00, 75, NULL, '2025-04-16 02:31:55', 'Jardinería'),
(7, 3, 'Cinta Métrica', '5 metros, con freno automático.', 6.00, 150, NULL, '2025-04-16 02:31:55', 'Construcción'),
(8, 3, 'Destornillador Philips', 'De acero inoxidable.', 4.00, 120, NULL, '2025-04-16 02:31:55', 'Herramientas'),
(9, 3, 'Guantes de Trabajo', 'Antideslizantes, talla única.', 3.00, 300, NULL, '2025-04-16 02:31:55', 'Seguridad'),
(10, 3, 'Caja de Herramientas', 'Con compartimentos múltiples.', 45.00, 40, NULL, '2025-04-16 02:31:55', 'Almacenamiento'),
(11, 9, 'zamael', 'aaaaa', 2000.00, 100, 'prod_6800a49f6c03d.png', '2025-04-17 01:50:07', 'Construcción');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tokens_recuperacion`
--

DROP TABLE IF EXISTS `tokens_recuperacion`;
CREATE TABLE IF NOT EXISTS `tokens_recuperacion` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `token` varchar(64) NOT NULL,
  `creado_en` datetime DEFAULT CURRENT_TIMESTAMP,
  `usado` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `tokens_recuperacion`
--

INSERT INTO `tokens_recuperacion` (`id`, `usuario_id`, `token`, `creado_en`, `usado`) VALUES
(1, 8, '69ae78b1304c5baff18d7dde00c50b7c1346bc098ec4d8a3e1c921a31e93b205', '2025-04-16 23:42:59', 0),
(2, 8, '8b9eb1fecec92a5aee0948fcdea7c02805475145208527f7815be58d8c4e5d6e', '2025-04-16 23:44:48', 1),
(3, 9, 'c7da92813d293315a5fe6a9e922c59498be8e5760357c04b86b04d16ea156995', '2025-04-16 23:50:41', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('cliente','administrador') NOT NULL DEFAULT 'cliente',
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `email`, `password`, `rol`, `fecha_registro`) VALUES
(8, 'Miguel', 'Sanchez', 'miguelsl0927@gmail.com', '$2y$10$tp41dkOB1U2AUJBewfPu4eIaGRkRz5AyB6SxCkbGTYhxSmHfXVuwy', 'cliente', '2025-04-17 04:38:24'),
(2, 'Ana', 'Gómez', 'ana.gomez@example.com', 'admin123', 'administrador', '2025-03-06 20:27:27'),
(5, 'zamael', 'tercero', 'yo@yo.com', '$2y$10$4gJ0JXljfSBToGjm2g/f0uIO6TzIwBHjb7/uksR.quN9K9sOkRZNG', 'cliente', '2025-03-31 03:55:18'),
(4, 'zamaell', 'segun', 'test@test.com', '$2y$10$CGERtV0Zc1eRxojLiBEsguFTV5H95JuserqKz8twBSxEx16TIII5G', 'cliente', '2025-03-31 03:35:45'),
(6, 'Admin', 'Principal', 'admin@admin.com', '$2y$10$Az3tm5UnazTnbaAS6V4nP.CnacoR/YevJ960uL9Bmu.gv.ixjDCrK', 'administrador', '2025-04-10 02:16:31'),
(7, 'Taty', 'sue', 'taty@tay.com', '$2y$10$UPgeUsgq8GSHRYCJeap/g.LPGzjpqRiIjk7aZKdvcW11a.cZkRrba', 'administrador', '2025-04-10 02:58:30'),
(9, 'Dylan', 'Garcia', 'dylnenatoxd@gmail.com', '$2y$10$vy8.Ah2O6ywgwFDYwsuSceRjNUUPiJBgx2wi7KWlyOAoFkub9Yce.', 'cliente', '2025-04-17 04:50:35');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `valoraciones`
--

DROP TABLE IF EXISTS `valoraciones`;
CREATE TABLE IF NOT EXISTS `valoraciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `producto_id` int NOT NULL,
  `pedido_id` int NOT NULL,
  `calificacion` int DEFAULT NULL,
  `comentario` text,
  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `producto_id` (`producto_id`),
  KEY `pedido_id` (`pedido_id`)
) ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
