<div id="map" class="h-56 md:h-96 md:w-full rounded-md"></div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Crear el mapa
        var map = L.map('map', {
            attributionControl: false
        }).setView([41.749828, -40.152340], 2); // Coordenadas iniciales y zoom global

        // Añadir la capa base (OpenStreetMap)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Coordenadas de ejemplo o reales (pasadas desde Laravel)
        var locations = @json($locations);

        // Añadir marcadores al mapa
        locations.forEach(function(location) {
            if (location.latitude && location.longitude) {
                L.marker([location.latitude, location.longitude])
                    .addTo(map)
                    .bindPopup(
                        `<strong>${location.city || 'Unknown City'}, ${location.country || 'Unknown Country'}</strong>`
                    );
            }
        });
    });
</script>
