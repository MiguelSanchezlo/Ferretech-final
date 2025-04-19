<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seleccionar Ubicación</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map { height: 400px; margin-bottom: 20px; }
    </style>
</head>
<body>

<h2>🧭 Selecciona la ubicación de tu ferretería</h2>

<form method="POST" action="guardar_empresa.php">
    <label>Dirección:</label><br>
    <input type="text" id="direccion" name="direccion" placeholder="Ej: Calle 123, Bogotá" style="width: 300px;">
    <button type="button" id="buscar">📍 Buscar</button><br><br>

    <label>Latitud:</label>
    <input type="text" id="latitud" name="latitud" readonly><br>
    <label>Longitud:</label>
    <input type="text" id="longitud" name="longitud" readonly><br><br>

    <div id="map"></div>

    <button type="submit">Guardar Ubicación</button>
</form>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
const mapa = L.map('map').setView([4.710989, -74.072090], 12); // Bogotá

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
}).addTo(mapa);

// Pin arrastrable
let marcador = L.marker([4.710989, -74.072090], { draggable: true }).addTo(mapa);

// Actualiza campos al mover el marcador
marcador.on('dragend', function(e) {
    const pos = marcador.getLatLng();
    document.getElementById('latitud').value = pos.lat.toFixed(8);
    document.getElementById('longitud').value = pos.lng.toFixed(8);
});

// Botón de búsqueda por dirección
document.getElementById('buscar').addEventListener('click', function() {
    const direccion = document.getElementById('direccion').value;
    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(direccion)}`)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                const lat = parseFloat(data[0].lat);
                const lon = parseFloat(data[0].lon);
                marcador.setLatLng([lat, lon]);
                mapa.setView([lat, lon], 15);
                document.getElementById('latitud').value = lat.toFixed(8);
                document.getElementById('longitud').value = lon.toFixed(8);
            } else {
                alert("Dirección no encontrada.");
            }
        });
});
</script>

</body>
</html>
