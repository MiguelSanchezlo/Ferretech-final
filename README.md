#  FerretTech - Marketplace de Ferreterías

FerretTech es una plataforma web que conecta ferreterías con clientes en línea. Permite a las empresas publicar productos, gestionar pedidos y recibir mensajes; mientras los usuarios pueden buscar, comprar y dejar valoraciones.

##  Características

- Registro/Login de usuarios y empresas
- Panel de administración para empresas y administradores
- Catálogo de productos con búsqueda, filtros y valoraciones
- Mapa interactivo con ubicación de ferreterías
- Carrito de compras y pasarela de pago integrada (MercadoPago)
- Historial de pedidos y notificaciones
- Sistema de mensajes cliente-empresa

## Tecnologías utilizadas

- **Frontend:** HTML, CSS, JavaScript
- **Backend:** PHP 8+, PDO
- **Base de datos:** MySQL
- **Mapas:** Leaflet + OpenStreetMap
- **Pasarela de pago:** MercadoPago Checkout Pro
- **Correo electrónico:** PHPMailer
- **Servidor local:** WAMP / XAMPP

---

##  Requisitos previos

- PHP 8.x
- MySQL 5.7 o superior
- Composer (para PHPMailer)
- Servidor local como **WAMP** o **XAMPP**
- Cuenta de MercadoPago con credenciales para Checkout Pro

---

##  Instalación

1. Clona este repositorio:

git clone https://github.com/MiguelSanchezlo/Ferretech-final.git

cd ferretech

Importa el archivo SQL en tu servidor de base de datos (por ejemplo con phpMyAdmin): ferretech.sql

Instala las dependencias de PHPMailer con Composer:
- composer install
- composer require phpmailer/phpmailer


Para instalar la SDK de MercadoPago para PHP 
- composer require mercadopago/dx-php

Configura el envío de correos en:
- /Config/config_mail.php


Abre tu servidor local en el navegador:
- http://localhost/ferretech/View/pages/index.php

