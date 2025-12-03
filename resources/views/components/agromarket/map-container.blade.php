@props([
    'mapId' => 'projectMap',
    'height' => '400px'
])

<div class="map-container" style="height: {{ $height }};">
    <div id="{{ $mapId }}" class="interactive-map" style="height: 100%; width: 100%;"></div>
</div>

@push('scripts')
<script>
(function() {
    // Esperar a que Leaflet esté disponible
    function init{{ $mapId }}() {
        if (typeof L === 'undefined') {
            console.error('Leaflet no está cargado');
            return;
        }

        const mapElement = document.getElementById('{{ $mapId }}');
        if (!mapElement) {
            console.error('Elemento del mapa no encontrado: {{ $mapId }}');
            return;
        }

        // Coordenadas de Colombia (centro)
        const map = L.map('{{ $mapId }}').setView([4.5709, -74.2973], 6);

        // Agregar tiles de OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 18
        }).addTo(map);

        // Proyectos de ejemplo
        const projects = [
            { id: 1, name: 'Aguacate Hass Premium', location: 'Valle del Cauca', lat: 3.4516, lng: -76.5320, roi: 22, minInvestment: 1000 },
            { id: 2, name: 'Cacao Orgánico', location: 'Santander', lat: 7.1301, lng: -73.1198, roi: 18, minInvestment: 800 },
            { id: 3, name: 'Café Especial', location: 'Huila', lat: 2.9273, lng: -75.2819, roi: 15, minInvestment: 500 }
        ];

        // Agregar marcadores
        projects.forEach(project => {
            const marker = L.marker([project.lat, project.lng]).addTo(map);
            marker.bindPopup(`
                <div class="map-popup">
                    <h4>${project.name}</h4>
                    <p><strong>Ubicación:</strong> ${project.location}</p>
                    <p><strong>ROI:</strong> ${project.roi}%</p>
                    <p><strong>Inversión mínima:</strong> $${project.minInvestment.toLocaleString()}</p>
                </div>
            `);
        });

        // Ajustar tamaño del mapa después de cargar
        setTimeout(() => {
            map.invalidateSize();
        }, 100);
    }

    // Inicializar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init{{ $mapId }});
    } else {
        // DOM ya está listo
        init{{ $mapId }}();
    }
})();
</script>
@endpush
