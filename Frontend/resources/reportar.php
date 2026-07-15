<?php
include_once('../../Backend/seguridad.php');
// Validar que el usuario tenga una sesión activa
validar_sesion_activa("iniciar_sesion.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>JuárezNow - Reportar incidente</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <!-- ===== HEADER  ===== -->
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
                            <a class="nav-link active" href="reportar.php">Reportar</a>
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

    <!-- ===== MAIN (FORMULARIO DE REPORTE) ===== -->
    <main class="container py-4">

        <!-- TÍTULO -->
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="display-5 ">
                    <i class=" text-primary "></i>
                    Crear reporte
                </h2>
                <p class="text-muted">Reporta un incidente en Ciudad Juárez y ayuda a tu comunidad</p>
                <hr class="w-50 mx-auto">
            </div>
        </div>

        <!-- ALERTAS DE ERROR / ÉXITO -->
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php
                        $err = $_GET['error'];
                        if ($err === 'csrf') echo 'Sesión o formulario expirado. Intente nuevamente.';
                        elseif ($err === 'campos_vacios') echo 'Por favor complete todos los campos obligatorios.';
                        elseif ($err === 'categoria_invalida') echo 'El tipo de incidente seleccionado no es válido.';
                        elseif ($err === 'tamano_imagen') echo 'La foto excede el límite permitido de 2MB.';
                        elseif ($err === 'extension_invalida') echo 'El formato de archivo no está permitido. Use JPG, PNG o GIF.';
                        elseif ($err === 'formato_imagen') echo 'El archivo subido no es una imagen válida.';
                        else echo 'Ocurrió un error al guardar el reporte. Intente de nuevo.';
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['envio']) && $_GET['envio'] === 'exitoso'): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        ¡Reporte creado exitosamente! Se mostrará en el inicio y el mapa interactivo.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- FORMULARIO DE REPORTE -->
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="bg-white p-4 p-md-5 rounded-4 shadow-sm">
                    <form id="reporteForm" action="../../Backend/reporte_proceso.php" method="POST" enctype="multipart/form-data">
                        
                        <!-- Token CSRF oculto -->
                        <input type="hidden" name="csrf_token" value="<?php echo obtener_token_csrf(); ?>">

                        <!-- CATEGORÍA -->
                        <div class="mb-4">
                            <label for="categoria" class="form-label fw-bold">
                                <i class="text-primary "></i>
                                Tipo de incidente
                            </label>
                            <select id="categoria" name="tipo_incidente" class="form-select form-select-lg rounded-pill" required>
                                <option value="">Selecciona una categoría</option>
                                <option value="accidente">Accidente automovilístico</option>
                                <option value="inundacion">Inundación</option>
                                <option value="trafico">Tráfico</option>
                                <option value="hundimiento">Hundimiento</option>
                            </select>
                        </div>

                        <!-- FILA: CALLE + REFERENCIA -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="calle" class="form-label fw-bold">
                                    <i class="text-primary "></i>
                                    Calle
                                </label>
                                <input type="text" id="calle" name="calle" 
                                       class="form-control form-control-lg rounded-pill" 
                                       placeholder="Ej. Av. De las Torres" required>
                            </div>
                            <div class="col-md-6">
                                <label for="referencia" class="form-label fw-bold">
                                    <i class="text-primary "></i>
                                    Referencia
                                </label>
                                <input type="text" id="referencia" name="referencia" 
                                       class="form-control form-control-lg rounded-pill" 
                                       placeholder="Entre calles o punto de referencia">
                            </div>
                        </div>

                        <!-- DESCRIPCIÓN -->
                        <div class="mb-4">
                            <label for="descripcion" class="form-label fw-bold">
                                <i class=" text-primary "></i>
                                Descripción
                            </label>
                            <textarea id="descripcion" name="descripcion" 
                                      class="form-control form-control-lg" 
                                      rows="4" 
                                      placeholder="Describe el incidente con detalle..." 
                                      required></textarea>
                        </div>

                        <!-- FOTO OPCIONAL -->
                        <div class="mb-4">
                            <label for="foto" class="form-label fw-bold">
                                <i class="text-primary "></i>
                                Foto (opcional, máx. 2MB)
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

                        <!-- BOTÓN ENVIAR -->
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-dark btn-lg rounded-pill py-3">
                                Enviar reporte
                            </button>
                        </div>

                    </form>
                </div>

                <!-- BOTÓN VOLVER AL INICIO -->
                <div class="text-center mt-4">
                    <a href="../index.php" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="fas fa-arrow-left me-2"></i>
                        Volver al inicio
                    </a>
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
    <!-- Custom JS -->
    <script src="../js/reportar.js"></script>
</body>
</html>