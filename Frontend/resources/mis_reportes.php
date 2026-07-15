<?php
include_once('../../Backend/seguridad.php');
include_once('../../Backend/conexion.php');

// Validar que el usuario tenga una sesión activa
validar_sesion_activa("iniciar_sesion.php");

$usuario_id = $_SESSION['usuario_id'];

// Obtener los reportes del usuario actual
$sql = "SELECT * FROM reportes WHERE usuario_id = ? ORDER BY fecha_creacion DESC";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>JuárezNow - Mis Reportes</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <!-- ===== HEADER ===== -->
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
                            <a class="nav-link" href="reportes.php">Reportes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="reportar.php">Reportar</a>
                        </li>
                    </ul>
                    <span class="navbar-text d-flex align-items-center gap-3">
                        <span class="text-white small">Hola, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></span>
                        <a class="btn btn-light btn-sm rounded-pill px-3" href="mis_reportes.php">Mis Reportes</a>
                        <a class="nav-link profile-icon" href="../../Backend/logout.php" title="Cerrar sesión">
                            <i class="fas fa-sign-out-alt text-white"></i>
                        </a>
                    </span>
                </div>
            </div>
        </nav>
    </header>

    <!-- ===== MAIN ===== -->
    <main class="container py-4">

        <!-- TÍTULO -->
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="display-5">
                    <i class="fas fa-list-alt text-primary me-2"></i>
                    Mis Reportes Creados
                </h2>
                <p class="text-muted">Gestiona, edita o elimina los incidentes que has reportado</p>
                <hr class="w-50 mx-auto">
            </div>
        </div>

        <!-- ALERTAS DE ÉXITO O ERROR -->
        <div class="row justify-content-center">
            <div class="col-12">
                <?php if (isset($_GET['actualizado']) && $_GET['actualizado'] === 'exitoso'): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        ¡Reporte actualizado con éxito!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['eliminado']) && $_GET['eliminado'] === 'exitoso'): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        ¡Reporte eliminado de forma definitiva!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php
                        $err = $_GET['error'];
                        if ($err === 'no_propietario') echo 'Acceso denegado: No eres el creador de este reporte.';
                        elseif ($err === 'csrf') echo 'Sesión o formulario expirado. Intente nuevamente.';
                        elseif ($err === 'no_encontrado') echo 'El reporte seleccionado no existe.';
                        else echo 'Ocurrió un error al procesar tu solicitud.';
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- TABLA DE REPORTES -->
        <div class="bg-white p-4 rounded-4 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 25%;">Imagen</th>
                            <th scope="col" style="width: 15%;">Tipo</th>
                            <th scope="col" style="width: 35%;">Ubicación y Detalles</th>
                            <th scope="col" style="width: 10%;">Estado</th>
                            <th scope="col" style="width: 15%;" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado && $resultado->num_rows > 0): ?>
                            <?php while ($row = $resultado->fetch_assoc()): 
                                $tipo = htmlspecialchars($row['tipo_incidente']);
                                $calle = htmlspecialchars($row['calle']);
                                $referencia = htmlspecialchars($row['referencia']);
                                $descripcion = htmlspecialchars($row['descripcion']);
                                $foto = $row['foto_url'];
                                $estado = htmlspecialchars($row['estado']);
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
                                    $img_src = "../../Backend/" . htmlspecialchars($foto);
                                } else {
                                    if ($tipo == "accidente") $img_src = "../img/choque.jpg";
                                    elseif ($tipo == "inundacion") $img_src = "../img/inundacion.jpg";
                                    else $img_src = "../img/trafico.jpg";
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
                                        <span class="text-secondary small">
                                            <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                            <?php echo $calle; if (!empty($referencia)) echo " (Ref: " . $referencia . ")"; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($estado === 'Pendiente'): ?>
                                            <span class="badge bg-warning text-dark border">Pendiente</span>
                                        <?php elseif ($estado === 'En revisión'): ?>
                                            <span class="badge bg-info text-white border">En revisión</span>
                                        <?php else: ?>
                                            <span class="badge bg-success text-white border">Resuelto</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="editar_reporte.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm rounded-pill px-3">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <!-- Formulario seguro de borrado con CSRF -->
                                            <form action="../../Backend/eliminar_reporte.php" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este reporte de forma permanente?');" style="margin: 0;">
                                                <input type="hidden" name="csrf_token" value="<?php echo obtener_token_csrf(); ?>">
                                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="fas fa-clipboard-list fa-2x mb-2"></i>
                                    <p class="mb-0">No has registrado ningún reporte aún.</p>
                                    <a href="reportar.php" class="btn btn-sm btn-primary rounded-pill mt-2">Crear reporte ahora</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- ===== FOOTER ===== -->
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
</body>
</html>
