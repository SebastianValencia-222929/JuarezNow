document.addEventListener('DOMContentLoaded', () => {
    const mapElement = document.getElementById('map');
    if (!mapElement) return;

    // 1. Inicializar el mapa centrado en Ciudad Juárez
    const map = L.map('map').setView([31.6904, -106.4245], 12);

    // 2. Cargar OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // 3. Obtener los reportes inyectados desde el atributo data-reportes
    const datosReportes = JSON.parse(mapElement.getAttribute('data-reportes') || '[]');

    // 4. Grupo de marcadores Leaflet para facilitar su filtrado y borrado dinámico
    const markersGroup = L.layerGroup().addTo(map);

    // 5. Función para renderizar los marcadores en el mapa según el filtro de categoría
    function renderMarkers(filtro) {
        markersGroup.clearLayers();

        datosReportes.forEach(reporte => {
            const tipo = reporte.tipo_incidente;

            // Filtrar según el valor seleccionado en el select dropdown
            if (filtro !== 'Todos' && tipo !== filtro) {
                return;
            }

            // Generación de coordenadas pseudoaleatorias fijas basadas en el ID
            const id = parseInt(reporte.id) || 1;
            const x1 = Math.sin(id) * 10000;
            const x2 = Math.cos(id) * 10000;
            const latSimulada = 31.6500 + ((x1 - Math.floor(x1)) * 0.08);
            const lngSimulada = -106.4800 + ((x2 - Math.floor(x2)) * 0.12);

            // Definir icono y color del marcador basado en la categoría
            let iconoFA = 'fa-info-circle';
            let colorClase = 'bg-secondary text-white';
            let tipo_str = 'Otro';

            if (tipo === 'accidente') {
                iconoFA = 'fa-car-crash';
                colorClase = 'bg-danger text-white';
                tipo_str = 'Choque / Accidente';
            } else if (tipo === 'inundacion') {
                iconoFA = 'fa-water';
                colorClase = 'bg-primary text-white';
                tipo_str = 'Inundación';
            } else if (tipo === 'trafico') {
                iconoFA = 'fa-traffic-light';
                colorClase = 'bg-warning text-dark';
                tipo_str = 'Tráfico';
            } else if (tipo === 'hundimiento') {
                iconoFA = 'fa-circle-exclamation';
                colorClase = 'bg-dark text-white';
                tipo_str = 'Hundimiento';
            }

            // Crear un L.divIcon con FontAwesome
            const iconHTML = `<div class="d-flex align-items-center justify-content-center rounded-circle border border-white shadow ${colorClase}" style="width: 36px; height: 36px; font-size: 16px;">
                                <i class="fas ${iconoFA}"></i>
                              </div>`;

            const customIcon = L.divIcon({
                html: iconHTML,
                className: 'custom-map-icon',
                iconSize: [36, 36],
                iconAnchor: [18, 18],
                popupAnchor: [0, -18]
            });

            // Crear el marcador con el icono personalizado
            const marker = L.marker([latSimulada, lngSimulada], { icon: customIcon });

            // Contenido HTML dinámico que aparecerá al dar clic sobre el marcador en el mapa
            let contenidoPopup = `
                <div style="font-family: sans-serif; min-width: 200px;">
                    <strong style="font-size: 14px; text-transform: uppercase; color: #333;">${tipo_str}</strong><br>
                    <span style="color: #666; font-size: 12px;"><i class="fas fa-map-marker-alt"></i> ${reporte.calle}</span><br>
                    ${reporte.referencia ? `<small style="color: #888;">Ref: ${reporte.referencia}</small><br>` : ''}
                    <p style="margin-top: 8px; font-size: 13px; color: #444;">${reporte.descripcion}</p>
            `;

            if (reporte.foto_url) {
                // Si el reporte cuenta con imagen subida en el Backend, la incrustamos en miniatura
                // Nota: Desde resources/mapa.php la ruta al Backend es ../../Backend/
                contenidoPopup += `<img src="../../Backend/${reporte.foto_url}" alt="Evidencia" style="width: 100%; height: auto; border-radius: 8px; margin-top: 5px;">`;
            }

            contenidoPopup += `</div>`;
            marker.bindPopup(contenidoPopup);
            markersGroup.addLayer(marker);
        });
    }

    // Renderizar marcadores iniciales al cargar la página
    renderMarkers('Todos');

    // Escuchar el evento de click en el botón de aplicar filtro
    const btnFiltrar = document.getElementById('btnFiltrar');
    const filtroTipo = document.getElementById('filtroTipo');
    if (btnFiltrar && filtroTipo) {
        btnFiltrar.addEventListener('click', () => {
            renderMarkers(filtroTipo.value);
        });
    }
});
