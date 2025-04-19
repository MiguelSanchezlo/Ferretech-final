<?php
require_once "../../Config/connection.php";
require_once "../../Controller/CompanyController.php";

include_once "../Templates/cabeza.php"; // Encabezado HTML

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $companyController = new CompanyController($pdo);
    $companyController->register();
}

if (isset($_GET['success']) && $_GET['success'] == 1):
?>
    <div class="alert success" style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px;">
        ¡Tu empresa ha sido registrada exitosamente!
        <a href="login.php" style="margin-left: 10px;">Iniciar sesión</a>
    </div>
<?php

endif;

?>

<main>


    <div class="register-container">
        <form class="register-form" method="POST" action="">
            <h1>Registro de Empresa</h1>
            <p>Registra tu empresa en FerretTech para comenzar a vender tus productos</p>

            <div class="form-row">
                <div class="form-group">
                    <label for="company-name">Nombre de la Empresa</label>
                    <input type="text" id="company-name" name="nombre_empresa" placeholder="FerretTech S.A." required>
                </div>
                <div class="form-group">
                    <label for="nif">NIF/CIF</label>
                    <input type="text" id="nif" name="nit" placeholder="B12345678" required>
                </div>
            </div>

            <div class="form-group">
                <label for="business-type">Tipo de Negocio</label>
                <select id="business-type" name="tipo_negocio" required>
                    <option value="">Selecciona el tipo de negocio</option>
                    <option value="retail">Venta al por menor</option>
                    <option value="wholesale">Venta al por mayor</option>
                    <option value="manufacturer">Fabricante</option>
                </select>
            </div>



            <div class="form-row">
                <div class="form-group">
                    <label for="city">Ciudad</label>
                    <input type="text" id="city" name="ciudad" placeholder="Madrid" required>
                </div>
                <div class="form-group">
                    <label for="postal-code">Código Postal</label>
                    <input type="text" id="postal-code" name="codigo_postal" placeholder="28001" required>
                </div>
            </div>

            <div class="form-group">
                <label for="contact-name">Nombre del Contacto Principal</label>
                <input type="text" id="contact-name" name="nombre_contacto" placeholder="Juan Pérez" required>
            </div>

            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email_contacto" placeholder="contacto@empresa.com" required>
            </div>

            <div class="form-group">
                <label for="phone">Teléfono</label>
                <input type="tel" id="phone" name="telefono" placeholder="+34 123 456 789" required>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <div class="password-input">
                    <input type="password" id="password" name="password" placeholder="********" required>
                </div>
            </div>
            <div class="form-group">
                <label for="confirm-password">Confirmar Contraseña</label>
                <input type="password" id="confirm-password" name="confirm_password" placeholder="********" required>
            </div>

            <div class="form-group">
                <label for="description">Descripción de la Empresa</label>
                <textarea id="description" name="descripcion" placeholder="Cuéntanos sobre tu empresa..." required></textarea>
            </div>

            <div class="form-group">
                <label>Dirección (texto):</label>
                <input type="text" id="direccion" name="direccion" placeholder="Ej: Calle 123, Medellín" style="width: 100%;" required>
                <button type="button" id="buscar">📍 Buscar en el mapa</button>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Latitud:</label>
                    <input type="text" id="latitud" name="latitud" readonly required>
                </div>
                <div class="form-group">
                    <label>Longitud:</label>
                    <input type="text" id="longitud" name="longitud" readonly required>
                </div>
            </div>

            <div class="form-group" style="margin-top: 20px;">
                <label>📍 Selecciona la ubicación en el mapa:</label>
                <div id="map" style="height: 400px; width: 100%; border: 1px solid #ccc; border-radius: 8px;"></div>
            </div>

            <div class="terms-checkbox">
                <input type="checkbox" id="terms" name="terminos" required>
                <a href="terminos.php" style="color: #000;">Acepto los Términos y Condiciones</a>
            </div>
            <button type="submit" name="registerCompany" class="register-button">Registrar Empresa</button>

            <div class="login-link">
                ¿Ya tienes una cuenta de empresa? <a href="login.php">Inicia sesión</a>
            </div>

        </form>
    </div>
</main>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    const map = L.map('map').setView([6.25449039, -75.57349205], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    let marker = L.marker([6.25449039, -75.57349205], {
        draggable: true
    }).addTo(map);

    // Actualizar campos al mover marcador
    marker.on('dragend', function(e) {
        const pos = marker.getLatLng();
        document.getElementById('latitud').value = pos.lat.toFixed(8);
        document.getElementById('longitud').value = pos.lng.toFixed(8);
    });

    // Buscar dirección
    document.getElementById('buscar').addEventListener('click', function() {
        const dir = document.getElementById('direccion').value;
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(dir)}`)
            .then(res => res.json())
            .then(data => {
                if (data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lon = parseFloat(data[0].lon);
                    marker.setLatLng([lat, lon]);
                    map.setView([lat, lon], 15);
                    document.getElementById('latitud').value = lat.toFixed(8);
                    document.getElementById('longitud').value = lon.toFixed(8);
                } else {
                    alert("Dirección no encontrada.");
                }
            });
    });
</script>
<?php include_once "../Templates/pie.php"; ?>