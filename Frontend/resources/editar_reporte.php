<?php
include_once('../../Backend/seguridad.php');
include_once('../../Backend/conexion.php');

// Validar que el usuario tenga una sesión activa
validar_sesion_activa("iniciar_sesion.php");

$usuario_id = $_SESSION['usuario_id'];
$reporte_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Buscar el reporte en la base de datos
$sql = "SELECT * FROM reportes WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $reporte_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    header("Location: mis_reportes.php?error=no_encontrado");
    exit();
}

$reporte = $resultado->fetch_assoc();

// Verificar que el usuario actual sea el creador del reporte
if ($reporte['usuario_id'] != $usuario_id) {
    header("Location: mis_reportes.php?error=no_propietario");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>JuárezNow - Editar Reporte</title>
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
                        <a class="btn bg-white text-dark btn-sm rounded-pill px-3" href="mis_reportes.php">Mis Reportes</a>
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
                    <i class="fas fa-edit text-primary me-2"></i>
                    Editar Reporte
                </h2>
                <p class="text-muted">Modifica los detalles del incidente reportado</p>
                <hr class="w-50 mx-auto">
            </div>
        </div>

        <!-- FORMULARIO DE EDICIÓN -->
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="bg-white p-4 p-md-5 rounded-4 shadow-sm">
                    <form id="reporteForm" action="../../Backend/editar_reporte_proceso.php" method="POST" enctype="multipart/form-data">
                        
                        <!-- Token CSRF oculto -->
                        <input type="hidden" name="csrf_token" value="<?php echo obtener_token_csrf(); ?>">
                        
                        <!-- ID del Reporte oculto -->
                        <input type="hidden" name="id" value="<?php echo $reporte['id']; ?>">

                        <!-- CATEGORÍA -->
                        <div class="mb-4">
                            <label for="categoria" class="form-label fw-bold">
                                Tipo de incidente
                            </label>
                            <select id="categoria" name="tipo_incidente" class="form-select form-select-lg rounded-pill" required>
                                <option value="accidente" <?php echo ($reporte['tipo_incidente'] === 'accidente') ? 'selected' : ''; ?>>Accidente automovilístico</option>
                                <option value="inundacion" <?php echo ($reporte['tipo_incidente'] === 'inundacion') ? 'selected' : ''; ?>>Inundación</option>
                                <option value="trafico" <?php echo ($reporte['tipo_incidente'] === 'trafico') ? 'selected' : ''; ?>>Tráfico</option>
                                <option value="hundimiento" <?php echo ($reporte['tipo_incidente'] === 'hundimiento') ? 'selected' : ''; ?>>Hundimiento</option>
                            </select>
                        </div>

                        <!-- FILA: CALLE + REFERENCIA -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="calle" class="form-label fw-bold">
                                    Calle
                                </label>
                                <input type="text" id="calle" name="calle" 
                                       class="form-control form-control-lg rounded-pill" 
                                       placeholder="Ej. Av. De las Torres" 
                                       value="<?php echo htmlspecialchars($reporte['calle']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="referencia" class="form-label fw-bold">
                                    Referencia
                                </label>
                                <input type="text" id="referencia" name="referencia" 
                                       class="form-control form-control-lg rounded-pill" 
                                       placeholder="Entre calles o punto de referencia"
                                       value="<?php echo htmlspecialchars($reporte['referencia'] ?? ''); ?>">
                            </div>
                        </div>

                        <!-- DESCRIPCIÓN -->
                        <div class="mb-4">
                            <label for="descripcion" class="form-label fw-bold">
                                Descripción
                            </label>
                            <textarea id="descripcion" name="descripcion" 
                                      class="form-control form-control-lg" 
                                      rows="4" 
                                      placeholder="Describe el incidente con detalle..." 
                                      required><?php echo htmlspecialchars($reporte['descripcion']); ?></textarea>
                        </div>

                        <!-- FOTO ACTUAL -->
                        <?php if (!empty($reporte['foto_url'])): ?>
                            <div class="mb-4">
                                <label class="form-label fw-bold d-block">Foto actual</label>
                                <img src="../../Backend/<?php echo htmlspecialchars($reporte['foto_url']); ?>" alt="Foto del incidente" class="rounded border shadow-sm" style="max-width: 250px; height: auto;">
                            </div>
                        <?php endif; ?>

                        <!-- FOTO NUEVA (OPCIONAL) -->
                        <div class="mb-4">
                            <label for="foto" class="form-label fw-bold">
                                Nueva Foto (opcional, reemplaza la anterior, máx. 2MB)
                            </label>
                            <div class="d-flex align-items-center gap-3">
                                <input type="file" id="foto" name="foto" accept="image/png, image/jpeg, image/jpg, image/gif">
                                <span class="text-muted small">
                                    <i class="fas fa-image me-1"></i>
                                    PNG, JPG, JPEG, GIF
                                </span>
                            </div>
                            <div id="fotoError" class="text-danger small mt-1" style="display: none;">
                                <i class="fas fa-exclamation-circle me-1"></i>
                                Archivo no válido. El tamaño máximo es 2MB y el formato debe ser imagen.
                            </div>
                        </div>

                        <!-- BOTONES -->
                        <div class="d-flex gap-3 mt-4">
                            <button type="submit" class="btn btn-dark btn-lg rounded-pill py-3 flex-grow-1">
                                Guardar Cambios
                            </button>
                            <a href="mis_reportes.php" class="btn btn-outline-secondary btn-lg rounded-pill py-3 flex-grow-1 text-center">
                                Cancelar
                            </a>
                        </div>

                    </form>
                </div>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS (reutilizamos la misma validación de reportar.js) -->
    <script src="../js/reportar.js"></script>
</body>
</html>
