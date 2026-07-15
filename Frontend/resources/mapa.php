<?php
// 1. Incluir la conexión y seguridad desde la carpeta Backend (subiendo un nivel desde Frontend/resources/)
include(__DIR__ . '/../../Backend/conexion.php');
include_once(__DIR__ . '/../../Backend/seguridad.php');

// 2. Consultar los reportes guardados en la base de datos JuárezNow
$query = "SELECT id, tipo_incidente, calle, referencia, descripcion, foto_url FROM reportes WHERE estado = 'Pendiente' OR estado = 'En revisión'";
$resultado = $conexion->query($query);

$reportes_json = [];
if ($resultado) {
    while ($fila = $resultado->fetch_assoc()) {
        $reportes_json[] = $fila;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>JuárezNow - Mapa</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">

    <!-- ===== LEAFLET CSS ===== -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        /* Asignamos una altura fija al contenedor del mapa */
        #map {
            height: 550px;
            width: 100%;
            border-radius: 16px;
        }
        .custom-map-icon {
            background: none;
            border: none;
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <a class="navbar-brand text-white fw-bold" href="../index.php">JuárezNow</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarText">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link" href="../index.php">Inicio</a></li>
                        <li class="nav-item"><a class="nav-link active" href="mapa.php">Mapa</a></li>
                        <li class="nav-item"><a class="nav-link" href="reportes.php">Reportes</a></li>
                        <li class="nav-item"><a class="nav-link" href="reportar.php">Reportar</a></li>
                    </ul>
                    <span class="navbar-text d-flex align-items-center gap-3">
                        <?php if (isset($_SESSION['usuario_id'])): ?>
                            <span class="text-white small">Hola, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></span>
                            <a class="btn bg-white text-dark btn-sm rounded-pill px-3" href="mis_reportes.php">Mis Reportes</a>
                            <a class="nav-link profile-icon" href="../../Backend/logout.php" title="Cerrar sesión">
                                <i class="fas fa-sign-out-alt text-white"></i>
                            </a>
                        <?php else: ?>
                            <a class="nav-link profile-icon" href="iniciar_sesion.php">
                                <i class="fas fa-user-circle"></i>
                            </a>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
        </nav>
    </header>

    <main class="container py-4">
        <div class="row">
            <div class="col-12">
                <h2 class="display-5 mb-4">
                    <i class="fas fa-map-marked-alt text-primary me-2"></i>
                    Mapa de incidentes en tiempo real
                </h2>
                
                <!-- Contenedor del Mapa donde Leaflet se renderizará -->
                <div class="shadow-sm rounded-4 overflow-hidden">
                    <div id="map" data-reportes='<?php echo json_encode($reportes_json, JSON_HEX_APOS | JSON_HEX_QUOT); ?>'></div>
                </div>
                
                <!-- Filtros y Leyenda --> 
                <div class="row mt-4">
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-filter me-2"></i>Filtrar incidentes</h5>
                                <select class="form-select mb-3" id="filtroTipo">
                                    <option value="Todos">Todos los tipos</option>
                                    <option value="accidente">Choque / Accidente</option>
                                    <option value="inundacion">Inundación</option>
                                    <option value="trafico">Tráfico</option>
                                    <option value="hundimiento">Hundimiento</option>
                                </select>
                                <button class="btn btn-dark w-100 rounded-pill" id="btnFiltrar">
                                    <i class="fas fa-search me-2"></i>Aplicar filtro
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8 mb-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-info-circle me-2"></i>Leyenda</h5>
                                <div class="d-flex flex-wrap gap-4 mt-3">
                                    <span><i class="fas fa-circle text-danger"></i> Choque</span>
                                    <span><i class="fas fa-circle text-info"></i> Inundacion</span>
                                    <span><i class="fas fa-circle text-warning"></i> Trafico</span>
                                    <span><i class="fas fa-circle text-secondary"></i> Hundimiento</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="text-white py-4 mt-5" id="footer">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4 mb-3">
                    <p class="mb-0">© 2026 JuárezNow</p>
                    <p>Todos los derechos reservados</p>
                </div>
                <div class="col-md-4 mb-3">
                    <p class="mb-0">
                        <i class="fab fa-facebook me-2"></i>
                        <i class="fab fa-instagram me-2"></i>
                        <i class="fab fa-twitter"></i>
                    </p>
                    <p>Síguenos en redes</p>
                </div>
                <div class="col-md-4">
                    <p><b>Nombre:</b> Sebastián Valencia Terrazas</p>
                    <p><b>Matrícula:</b> 222929</p>
                    <p><b>Materia:</b> Sistemas Web</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- ===== LEAFLET JAVASCRIPT ===== -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- ===== CUSTOM JAVASCRIPT ===== -->
    <script src="../js/mapa.js"></script>
</body>
</html>