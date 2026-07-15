<?php
session_start();
include('../../Backend/conexion.php');

// Paginación
$reportes_por_pagina = 4;

// Obtener total de reportes
$sql_total = "SELECT COUNT(*) as total FROM reportes";
$result_total = $conexion->query($sql_total);
$row_total = $result_total->fetch_assoc();
$total_reportes = $row_total['total'];

// Calcular total de páginas
$total_paginas = ceil($total_reportes / $reportes_por_pagina);

// Página actual
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina_actual < 1) {
    $pagina_actual = 1;
} elseif ($pagina_actual > $total_paginas && $total_paginas > 0) {
    $pagina_actual = $total_paginas;
}

// Calcular offset
$offset = ($pagina_actual - 1) * $reportes_por_pagina;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>JuárezNow - Reportes</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

    <header>
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <a class="navbar-brand text-white fw-bold" href="../index.php">
                    JuárezNow
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarText">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="../index.php">Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="mapa.php">Mapa</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="reportes.php">Reportes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="reportar.php">Reportar</a>
                        </li>
                    </ul>
                    <span class="navbar-text">
                        <a class="nav-link profile-icon" href="iniciar_sesion.php">
                            <i class="fas fa-user-circle"></i>
                        </a>
                    </span>
                </div>
            </div>
        </nav>
    </header>

    <!-- ===== MAIN ===== -->
    <main class="container py-4">


        <div class="row mb-4 align-items-center">
            <div class="col-md-8">
                <h2 class="display-5">
                    <i class="text-primary "></i>
                    Todos los reportes
                </h2>
                <p class="text-muted mt-1">Visualiza todos los incidentes reportados por la comunidad</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="reportar.php" class="btn btn-dark btn-lg rounded-pill">
                    <i class="fas fa-plus-circle me-2"></i>
                    Crear reporte
                </a>
            </div>
        </div>



        <!-- LISTA DE TODOS LOS REPORTES (MÁS QUE EN EL INICIO) -->
        <div class="bg-white p-4 rounded-4 shadow-sm">
            <div class="row g-3">

                <?php
                $sql = "SELECT r.*, u.nombre, u.apellido FROM reportes r LEFT JOIN usuarios u ON r.usuario_id = u.id ORDER BY r.fecha_creacion DESC LIMIT $reportes_por_pagina OFFSET $offset";
                $resultado = $conexion->query($sql);

                if ($resultado && $resultado->num_rows > 0) {
                    while ($row = $resultado->fetch_assoc()) {
                        $tipo = htmlspecialchars($row['tipo_incidente']);
                        $calle = htmlspecialchars($row['calle']);
                        $referencia = htmlspecialchars($row['referencia']);
                        $descripcion = htmlspecialchars($row['descripcion']);
                        $foto = $row['foto_url'];
                        $fecha = date("d/m/Y H:i", strtotime($row['fecha_creacion']));
                        
                        $icono = "fa-info-circle";
                        $tipo_str = "Otro";
                        if ($tipo == "accidente") {
                            $icono = "fa-car-crash";
                            $tipo_str = "Accidente";
                        } elseif ($tipo == "inundacion") {
                            $icono = "fa-water";
                            $tipo_str = "Inundación";
                        } elseif ($tipo == "trafico") {
                            $icono = "fa-traffic-light";
                            $tipo_str = "Tráfico";
                        } elseif ($tipo == "hundimiento") {
                            $icono = "fa-circle-exclamation";
                            $tipo_str = "Hundimiento";
                        }

                        if (!empty($foto)) {
                            $img_src = "../../Backend/" . htmlspecialchars($foto);
                        } else {
                            if ($tipo == "accidente") $img_src = "../img/choque.jpg";
                            elseif ($tipo == "inundacion") $img_src = "../img/inundacion.jpg";
                            else $img_src = "../img/trafico.jpg";
                        }
                        
                        $usuario_reporto = "Anónimo";
                        if (!empty($row['nombre'])) {
                            $usuario_reporto = htmlspecialchars($row['nombre'] . " " . $row['apellido']);
                        }
                        ?>
                        <article class="col-12">
                            <div class="card shadow-sm rounded-3 overflow-hidden">
                                <div class="card-header text-dark d-flex justify-content-between align-items-center">
                                    <span>
                                        <i class="fas <?php echo $icono; ?> me-2"></i>
                                        <?php echo $tipo_str; ?>
                                    </span>
                                    <div>
                                        <span class="badge bg-light text-dark rounded-pill me-2">Estado: <?php echo htmlspecialchars($row['estado'] ?? 'Pendiente'); ?></span>
                                        <span class="badge bg-light text-dark rounded-pill"><?php echo $fecha; ?></span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <p class="card-text mb-2">
                                                <strong>Descripción:</strong> <?php echo $descripcion; ?>
                                            </p>
                                            <p class="card-text mb-2">
                                                <strong>Ubicación:</strong> 
                                                <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                                <?php echo $calle; if (!empty($referencia)) echo " (Ref: " . $referencia . ")"; ?>
                                            </p>
                                            <p class="card-text mb-0 text-muted small">
                                                <strong>Reportado por:</strong> <?php echo $usuario_reporto; ?>
                                            </p>
                                            <a href="mapa.php" class="btn btn-primary btn-sm rounded-pill mt-3">
                                                <i class="fas fa-map me-2"></i>
                                                Ver en mapa
                                            </a>
                                        </div>
                                        <div class="col-md-4 mt-3 mt-md-0 d-flex justify-content-md-end">
                                            <img src="<?php echo $img_src; ?>" 
                                                 class="img-tabla-incidente shadow-sm" 
                                                 alt="<?php echo $tipo_str; ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                        <?php
                    }
                } else {
                    ?>
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No hay incidentes reportados en este momento.</p>
                    </div>
                    <?php
                }
                ?>


            </div>

            <!-- PAGINACIÓN -->
            <?php if ($total_paginas > 1): ?>
            <div class="row mt-4">
                <div class="col-12">
                    <nav aria-label="Navegación de reportes">
                        <ul class="pagination justify-content-center">
                            <!-- Anterior -->
                            <li class="page-item <?php echo ($pagina_actual <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo ($pagina_actual <= 1) ? '#' : '?pagina=' . ($pagina_actual - 1); ?>" tabindex="-1" <?php echo ($pagina_actual <= 1) ? 'aria-disabled="true"' : ''; ?>>Anterior</a>
                            </li>
                            
                            <!-- Páginas -->
                            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                                <li class="page-item <?php echo ($pagina_actual == $i) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <!-- Siguiente -->
                            <li class="page-item <?php echo ($pagina_actual >= $total_paginas) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo ($pagina_actual >= $total_paginas) ? '#' : '?pagina=' . ($pagina_actual + 1); ?>">Siguiente</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            <?php endif; ?>
        </div>


    </main>

    <!-- ===== FOOTER  ===== -->
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
</body>
</html>