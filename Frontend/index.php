<?php
include_once('../Backend/conexion.php');
include_once('../Backend/seguridad.php');

// Consultar los reportes guardados en la base de datos para el mapa de inicio
$query_mapa = "SELECT id, tipo_incidente, calle, referencia, descripcion, foto_url FROM reportes WHERE estado = 'Pendiente' OR estado = 'En revisión'";
$result_mapa = $conexion->query($query_mapa);
$reportes_mapa_json = [];
if ($result_mapa) {
    while ($fila = $result_mapa->fetch_assoc()) {
        $reportes_mapa_json[] = $fila;
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <title>JuárezNow</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <!-- ===== LEAFLET CSS ===== -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
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
                <a class="navbar-brand text-white fw-bold" href="index.php">
                    JuárezNow
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarText">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" href="index.php">Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="resources/mapa.php">Mapa</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="resources/reportes.php">Reportes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="resources/reportar.php">Reportar</a>
                        </li>
                    </ul>
                    <span class="navbar-text d-flex align-items-center gap-3">
                        <?php if (isset($_SESSION['usuario_id'])): ?>
                            <span class="text-white small">Hola, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></span>
                            <a class="btn bg-white text-dark btn-sm rounded-pill px-3" href="resources/mis_reportes.php">Mis Reportes</a>
                            <a class="nav-link profile-icon" href="../Backend/logout.php" title="Cerrar sesión">
                                <i class="fas fa-sign-out-alt text-white"></i>
                            </a>
                        <?php else: ?>
                            <a class="nav-link profile-icon" href="resources/iniciar_sesion.php">
                                <i class="fas fa-user-circle"></i>
                            </a>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
        </nav>
    </header>

    <!-- ===== HERO SECTION ===== -->
    <section class="hero-section text-center text-white" style="background-image: url('img/flood.jpg');">
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <span class="badge rounded-pill mb-3">
                <i class="fas fa-bullhorn text-warning me-2"></i>JuárezNow Activo
            </span>
            <h1 class="display-4 fw-extrabold text-white">
                Reporta y visualiza incidentes en tiempo real
            </h1>
            <p class="lead text-white-50 mx-auto" style="max-width: 700px;">
                Consulta inundaciones, accidentes viales, semáforos dañados y hundimientos en Ciudad Juárez. Apoya a tu comunidad reportando incidentes al instante.
            </p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="resources/reportar.php" class="btn btn-dark btn-lg rounded-pill px-4 py-3 shadow-sm border-0" style="background-color: var(--azul-oscuro);">
                    <i class="fas fa-plus-circle me-2"></i>Reportar incidente
                </a>
                <a href="resources/mapa.php" class="btn btn-outline-light btn-lg rounded-pill px-4 py-3">
                    <i class="fas fa-map-marked-alt me-2"></i>Ver mapa en vivo
                </a>
            </div>
        </div>
    </section>

    <!--MAIN-->
    <main class="container py-4">

        <!-- ===== CARACTERÍSTICAS PRINCIPALES ===== -->
        <section class="mb-5 border-0 bg-transparent p-0 shadow-none">
            <div class="row g-3">
                <div class="col-lg-3 col-sm-6">
                    <div class="feature-card d-flex align-items-center h-100">
                        <div class="feature-icon-wrapper me-3">
                            <i class="fas fa-bolt fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Reporte veloz</h6>
                            <p class="text-muted small mb-0">Sube alertas en segundos</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="feature-card d-flex align-items-center h-100">
                        <div class="feature-icon-wrapper me-3">
                            <i class="fas fa-map-marked-alt fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Geolocalización</h6>
                            <p class="text-muted small mb-0">Visualiza en mapa interactivo</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="feature-card d-flex align-items-center h-100">
                        <div class="feature-icon-wrapper me-3">
                            <i class="fas fa-users fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Colaborativo</h6>
                            <p class="text-muted small mb-0">Apoyo mutuo ciudadano</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="feature-card d-flex align-items-center h-100">
                        <div class="feature-icon-wrapper me-3">
                            <i class="fas fa-shield-alt fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Acceso libre</h6>
                            <p class="text-muted small mb-0">Consulta sin registrarte</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== ESTADÍSTICAS===== -->
        <section class="mb-5">
            <div class="row">
                <div class="col-12">
                    <h3 class="mb-3">
                        Conteo de los tipos de reportes subidos a la plataforma en el día actual
                    </h3>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-3 col-6">
                    <div class="feature-card d-flex align-items-center h-100">
                        <div class="feature-icon-wrapper me-3">
                            <i class="fas fa-car-crash fa-lg"></i>
                        </div>
                        <div>
                            <h3 class="display-6 fw-bold mb-0">3</h3>
                            <p class="text-muted small mb-0">Accidentes</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="feature-card d-flex align-items-center h-100">
                        <div class="feature-icon-wrapper me-3">
                            <i class="fas fa-water fa-lg"></i>
                        </div>
                        <div>
                            <h3 class="display-6 fw-bold mb-0">5</h3>
                            <p class="text-muted small mb-0">Inundaciones</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="feature-card d-flex align-items-center h-100">
                        <div class="feature-icon-wrapper me-3">
                            <i class="fas fa-traffic-light fa-lg"></i>
                        </div>
                        <div>
                            <h3 class="display-6 fw-bold mb-0">4</h3>
                            <p class="text-muted small mb-0">Tráfico</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="feature-card d-flex align-items-center h-100">
                        <div class="feature-icon-wrapper me-3">
                            <i class="fas fa-circle-exclamation fa-lg"></i>
                        </div>
                        <div>
                            <h3 class="display-6 fw-bold mb-0">2</h3>
                            <p class="text-muted small mb-0">Hundimientos</p>
                        </div>
                    </div>
                </div>
            </div>
        <!-- ===== EVENTOS RECIENTES ===== -->
        <section class="mb-5">
            <div class="row">
                <div class="col-12">
                    <h3 class="mb-4">
                        Eventos recientes:
                    </h3>
                </div>
            </div>

            <div class="bg-white p-4 rounded-4 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 25%;">Imagen</th>
                                <th scope="col" style="width: 10%;">Tipo</th>
                                <th scope="col" style="width: 40%;">Descripción e Ubicación</th>
                                <th scope="col" style="width: 10%;">Fecha</th>
                                <th scope="col" style="width: 10%;" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT r.*, u.nombre, u.apellido FROM reportes r LEFT JOIN usuarios u ON r.usuario_id = u.id ORDER BY r.fecha_creacion DESC LIMIT 5";
                            $resultado = $conexion->query($sql);
                            if ($resultado && $resultado->num_rows > 0) {
                                while ($row = $resultado->fetch_assoc()) {
                                    $tipo = htmlspecialchars($row['tipo_incidente']);
                                    $calle = htmlspecialchars($row['calle']);
                                    $referencia = htmlspecialchars($row['referencia']);
                                    $descripcion = htmlspecialchars($row['descripcion']);
                                    $foto = $row['foto_url'];
                                    $fecha = date("d/m/Y", strtotime($row['fecha_creacion']));
                                    $icono = "fa-info-circle";
                                    $tipo_str = "Otro";
                                    $color_clase = "text-secondary";
                                    if ($tipo == "accidente") {
                                        $icono = "fa-car-crash";
                                        $tipo_str = "Accidente";
                                        $color_clase = "text-danger";
                                    } elseif ($tipo == "inundacion") {
                                        $icono = "fa-water";
                                        $tipo_str = "Inundación";
                                        $color_clase = "text-primary";
                                    } elseif ($tipo == "trafico") {
                                        $icono = "fa-traffic-light";
                                        $tipo_str = "Tráfico";
                                        $color_clase = "text-warning";
                                    } elseif ($tipo == "hundimiento") {
                                        $icono = "fa-circle-exclamation";
                                        $tipo_str = "Hundimiento";
                                        $color_clase = "text-dark";
                                    }
                                    if (!empty($foto)) {
                                        $img_src = "../Backend/" . htmlspecialchars($foto);
                                    } else {
                                        if ($tipo == "accidente") $img_src = "img/choque.jpg";
                                        elseif ($tipo == "inundacion") $img_src = "img/inundacion.jpg";
                                        else $img_src = "img/trafico.jpg";
                                    }
                                    $usuario_reporto = "Anónimo";
                                    if (!empty($row['nombre'])) {
                                        $usuario_reporto = htmlspecialchars($row['nombre'] . " " . $row['apellido']);
                                    }
                                    ?>
                                    <tr>
                                        <td>
                                            <img src="<?php echo $img_src; ?>" class="img-tabla-incidente shadow-sm" alt="<?php echo $tipo_str; ?>">
                                        </td>
                                        <td>
                                            <span class="fw-bold text-dark d-flex align-items-center">
                                                <i class="fas <?php echo $icono; ?> <?php echo $color_clase; ?> me-2"></i>
                                                <?php echo $tipo_str; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <p class="mb-1 text-muted"><?php echo $descripcion; ?></p>
                                            <div class="mb-1">
                                                <span class="text-secondary small">
                                                    <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                                    <?php echo $calle; if (!empty($referencia)) echo " (Ref: " . $referencia . ")"; ?>
                                                </span>
                                            </div>
                                            <span class="text-muted small">
                                                <i class="fas fa-user text-secondary me-1"></i>
                                                Reportado por: <?php echo $usuario_reporto; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border rounded-pill"><?php echo $fecha; ?></span>
                                        </td>
                                        <td class="text-center">
                                            <a href="resources/mapa.php" class="btn btn-primary btn-sm rounded-pill px-3">
                                                <i class="fas fa-map me-1"></i>
                                                Ver
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fas fa-clipboard-list fa-2x mb-2"></i>
                                        <p class="mb-0">No hay incidentes registrados.</p>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Ver todos -->
                <div class="row mt-4">
                    <div class="col-12 text-center">
                        <a href="resources/reportes.php" class="btn rounded-pill">
                            <i class="fas fa-list me-2"></i>
                            Ver todos los reportes
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!--EXPLORAR MAPA-->
        <section>
            <div class="text-center mt-3">
                <h3 class="mb-4">
                    Explora el mapa de incidentes
                </h3>
                <div class="shadow-sm rounded-4 overflow-hidden mb-3">
                    <div id="map" style="height: 480px; width: 100%;" data-reportes='<?php echo json_encode($reportes_mapa_json, JSON_HEX_APOS | JSON_HEX_QUOT); ?>'></div>
                </div>
                <div class="text-center mt-3">
                    <a href="resources/mapa.php" class="btn btn-dark rounded-pill px-4 py-2 mt-2">
                        <i class="fas fa-map-marked-alt me-2"></i>
                        Ver mapa en pantalla completa
                    </a>
                </div>
            </div>
        </section>


    </main>

    <!-- FOOTER -->
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ===== LEAFLET JAVASCRIPT ===== -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- ===== CUSTOM JAVASCRIPT ===== -->
    <script src="js/index.js"></script>
</body>

</html>