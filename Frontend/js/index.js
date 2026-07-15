function escapeHTML(str) {
    if (!str) return '';
    return str.replace(/[&<>"']/g, function(m) {
        switch (m) {
            case '&': return '&amp;';
            case '<': return '&lt;';
            case '>': return '&gt;';
            case '"': return '&quot;';
            case "'": return '&#039;';
            default: return m;
        }
    });
}

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

    // 4. Renderizar cada reporte con su icono correspondiente
    datosReportes.forEach(reporte => {
        // Generación de coordenadas pseudoaleatorias fijas basadas en el ID
        const id = parseInt(reporte.id) || 1;
        const x1 = Math.sin(id) * 10000;
        const x2 = Math.cos(id) * 10000;
        const latSimulada = 31.6500 + ((x1 - Math.floor(x1)) * 0.08);
        const lngSimulada = -106.4800 + ((x2 - Math.floor(x2)) * 0.12);

        // Icono personalizado según categoría
        let iconoFA = 'fa-info-circle';
        let colorClase = 'bg-secondary text-white';
        let tipo_str = 'Otro';
        
        const tipo = reporte.tipo_incidente;
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

        const marker = L.marker([latSimulada, lngSimulada], { icon: customIcon }).addTo(map);

        // Prevenir XSS escapando las variables dinámicas
        const calleEscaped = escapeHTML(reporte.calle);
        const referenciaEscaped = reporte.referencia ? escapeHTML(reporte.referencia) : '';
        const descripcionEscaped = escapeHTML(reporte.descripcion);

        let contenidoPopup = `
            <div style="font-family: sans-serif; min-width: 200px;">
                <strong style="font-size: 14px; text-transform: uppercase; color: #333;">${tipo_str}</strong><br>
                <span style="color: #666; font-size: 12px;"><i class="fas fa-map-marker-alt"></i> ${calleEscaped}</span><br>
                ${referenciaEscaped ? `<small style="color: #888;">Ref: ${referenciaEscaped}</small><br>` : ''}
                <p style="margin-top: 8px; font-size: 13px; color: #444;">${descripcionEscaped}</p>
        `;

        if (reporte.foto_url) {
            contenidoPopup += `<img src="../Backend/${reporte.foto_url}" alt="Evidencia" style="width: 100%; height: auto; border-radius: 8px; margin-top: 5px;">`;
        }

        contenidoPopup += `</div>`;
        marker.bindPopup(contenidoPopup);
    });
});
