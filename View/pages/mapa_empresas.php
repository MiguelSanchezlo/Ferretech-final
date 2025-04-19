<?php
require_once "../../Config/connection.php";

// Filtros desde URL
$ciudad_filtro = $_GET['ciudad'] ?? null;
$rango_filtro = $_GET['rango'] ?? null;

$sql = "SELECT id, nombre_empresa, descripcion, ciudad, latitud, longitud FROM empresas WHERE latitud IS NOT NULL AND longitud IS NOT NULL";
$params = [];

if ($ciudad_filtro) {
    $sql .= " AND ciudad LIKE ?";
    $params[] = "%$ciudad_filtro%";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$empresas = $stmt->fetchAll(PDO::FETCH_ASSOC);

include_once "../Templates/cabeza.php";
?>

<main class="mapa-empresas">
    <h1>🗺️ Ferreterías Registradas</h1>

    <form method="GET" style="margin-bottom: 20px;">
        <label for="ciudad">Filtrar por ciudad:</label><br>
        <input type="text" name="ciudad" id="ciudad" value="<?= htmlspecialchars($ciudad_filtro ?? '') ?>">
        <br><br><br>
        <label for="rango">Distancia (km):</label><br>
        <select id="rango" name="rango">
            <option value="">Todas</option>
            <option value="1" <?= $rango_filtro == 1 ? 'selected' : '' ?>>1 km</option>
            <option value="2" <?= $rango_filtro == 2 ? 'selected' : '' ?>>2 km</option>
            <option value="3" <?= $rango_filtro == 3 ? 'selected' : '' ?>>3 km</option>
            <option value="4" <?= $rango_filtro == 4 ? 'selected' : '' ?>>4 km</option>
            <option value="5" <?= $rango_filtro == 5 ? 'selected' : '' ?>>5 km</option>
            <option value="10" <?= $rango_filtro == 10 ? 'selected' : '' ?>>10 km</option>
            <option value="20" <?= $rango_filtro == 20 ? 'selected' : '' ?>>20 km</option>
        </select>
        <br><br><br>

        <button type="submit">Aplicar</button>
        <?php if ($ciudad_filtro || $rango_filtro): ?>
            <a href="mapa_empresas.php">Quitar filtros</a>
        <?php endif; ?>
    </form>
    <div id="contador-empresas" class="contador-empresas"></div>
    <div id="map" style="height: 500px; border: 1px solid #ccc; border-radius: 8px;"></div>
</main>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const empresas = <?= json_encode($empresas) ?>;
        const rangoFiltro = <?= isset($_GET['rango']) ? (int)$_GET['rango'] : 0 ?>;

        const map = L.map('map').setView([4.710989, -74.072090], 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Pintar sin filtro si no hay geolocalización
        empresas.forEach(e => {
            L.marker([e.latitud, e.longitud])
                .addTo(map)
                .bindPopup(`
                    <strong>${e.nombre_empresa}</strong><br>
                    ${e.descripcion}<br>
                    <em>${e.ciudad}</em><br>
                    <a href="productos_empresa_publica.php?id=${e.id}" target="_blank">🛒 Ver productos</a>
                `);
        });

        document.getElementById("contador-empresas").textContent =
            `🔎 Se encontraron ${empresas.length} ferretería(s) registradas.`;


        // Si hay geolocalización, ordenar y filtrar
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const userLat = position.coords.latitude;
                const userLon = position.coords.longitude;

                // Centrar mapa
                map.setView([userLat, userLon], 12);
                // Marcar ubicación
                L.marker([userLat, userLon], {
                    icon: L.icon({
                        iconUrl: 'https://cdn-icons-png.flaticon.com/512/64/64113.png',
                        iconSize: [25, 25],
                    })
                }).addTo(map).bindPopup("📍 Tú estás aquí").openPopup();

                // Distancia con fórmula Haversine
                function getDistance(lat1, lon1, lat2, lon2) {
                    const R = 6371;
                    const dLat = (lat2 - lat1) * Math.PI / 180;
                    const dLon = (lon2 - lon1) * Math.PI / 180;
                    const a = Math.sin(dLat / 2) ** 2 + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLon / 2) ** 2;
                    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                    return R * c;
                }

                // Ordenar y filtrar por rango
                const empresasOrdenadas = empresas
                    .map(e => {
                        const distancia = getDistance(userLat, userLon, e.latitud, e.longitud);
                        return {
                            ...e,
                            distancia
                        };
                    })
                    .filter(e => rangoFiltro === 0 || e.distancia <= rangoFiltro)
                    .sort((a, b) => a.distancia - b.distancia);

                // Limpiar mapa (excepto capa base)
                map.eachLayer(layer => {
                    if (layer instanceof L.Marker && !layer._icon.src.includes('64/64113')) {
                        map.removeLayer(layer);
                    }
                });

                if (empresasOrdenadas.length === 0) {
                    const mensaje = document.createElement('div');
                    mensaje.innerHTML = `<p class="mensaje-error">❌ No hay ferreterías cercanas dentro de ${rangoFiltro} km.</p>`;
                    document.querySelector("main").appendChild(mensaje);
                    return; // no seguir pintando
                }
                const contador = document.getElementById("contador-empresas");
                if (rangoFiltro > 0) {
                    contador.textContent = `🔎 Se encontraron ${empresasOrdenadas.length} ferretería(s) dentro de ${rangoFiltro} km.`;
                } else {
                    contador.textContent = `🔎 Se encontraron ${empresasOrdenadas.length} ferretería(s) cercanas.`;
                }


                // Pintar empresas filtradas
                empresasOrdenadas.forEach(e => {
                    L.marker([e.latitud, e.longitud])
                        .addTo(map)
                        .bindPopup(`
                            <strong>${e.nombre_empresa}</strong><br>
                            ${e.descripcion}<br>
                            <em>${e.ciudad}</em><br>
                            📏 ${e.distancia.toFixed(2)} km<br>
                            <a href="productos_empresa_publica.php?id=${e.id}" target="_blank">🛒 Ver productos</a>
                        `);

                });
            });
        }
    });
</script>

<?php include_once "../Templates/pie.php"; ?>