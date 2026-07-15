<?php
include_once('../../Backend/seguridad.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>JuárezNow - Registrarse</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Tu CSS -->
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <!-- ===== HEADER (REUTILIZADO) ===== -->
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
                    <span class="navbar-text">
                        <a class="nav-link profile-icon" href="iniciar_sesion.php">
                            <i class="fas fa-user-circle"></i>
                        </a>
                    </span>
                </div>
            </div>
        </nav>
    </header>

    <!-- ===== MAIN (REGISTRO) ===== -->
    <main class="container py-4">

        <!-- TÍTULO -->
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="display-5">
                    <i class="fas fa-user-plus me-2"></i>
                    Registrarse
                </h2>
                <p class="text-muted">Crea tu cuenta para empezar a reportar incidentes</p>
                <hr class="w-50 mx-auto">
            </div>
        </div>

        <!-- ALERTAS DINÁMICAS DE ERROR/ÉXITO -->
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php
                        $err = $_GET['error'];
                        if ($err === 'csrf') echo 'Sesión o formulario expirado. Intente nuevamente.';
                        elseif ($err === 'campos_vacios') echo 'Por favor rellene todos los campos obligatorios.';
                        elseif ($err === 'correo_invalido') echo 'El formato de correo no es válido.';
                        elseif ($err === 'password_corto') echo 'La contraseña debe tener al menos 6 caracteres.';
                        elseif ($err === 'correo_duplicado') echo 'Este correo electrónico ya está registrado.';
                        else echo 'Ocurrió un error al procesar el registro. Intente de nuevo.';
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- FORMULARIO DE REGISTRO -->
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="bg-white p-4 p-md-5 rounded-4 shadow-sm">
                    <form id="registroForm" action="../../Backend/registro_proceso.php" method="POST">
                        
                        <!-- Token CSRF oculto -->
                        <input type="hidden" name="csrf_token" value="<?php echo obtener_token_csrf(); ?>">

                        <!-- FILA: NOMBRE + APELLIDO -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="nombre" class="form-label fw-bold">
                                    <i class="fas fa-user me-2"></i>
                                    Nombre
                                </label>
                                <input type="text" id="nombre" name="nombre" 
                                       class="form-control form-control-lg" 
                                       placeholder="Carlos" 
                                       required>
                            </div>
                            <div class="col-md-6">
                                <label for="apellido" class="form-label fw-bold">
                                    <i class="fas fa-user me-2"></i>
                                    Apellido
                                </label>
                                <input type="text" id="apellido" name="apellido" 
                                       class="form-control form-control-lg" 
                                       placeholder="Gómez" 
                                       required>
                            </div>
                        </div>

                        <!-- CORREO ELECTRÓNICO -->
                        <div class="mb-4">
                            <label for="email" class="form-label fw-bold">
                                <i class="fas fa-envelope me-2"></i>
                                Correo electrónico
                            </label>
                            <input type="email" id="email" name="correo" 
                                   class="form-control form-control-lg" 
                                   placeholder="usuario@ejemplo.com" 
                                   required>
                            <div id="emailError" class="text-danger small mt-1" style="display: none;">
                                <i class="fas fa-exclamation-circle me-1"></i>
                                Ingresa un correo electrónico válido.
                            </div>
                        </div>

                        <!-- CONTRASEÑA -->
                        <div class="mb-4">
                            <label for="password" class="form-label fw-bold">
                                <i class="fas fa-lock me-2"></i>
                                Contraseña
                            </label>
                            <div class="input-group">
                                <input type="password" id="password" name="password" 
                                       class="form-control form-control-lg" 
                                       placeholder="Mínimo 6 caracteres" 
                                       required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Mínimo 6 caracteres.
                            </div>
                            <div id="passwordError" class="text-danger small mt-1" style="display: none;">
                                <i class="fas fa-exclamation-circle me-1"></i>
                                La contraseña debe tener al menos 6 caracteres.
                            </div>
                        </div>

                        <!-- CONFIRMAR CONTRASEÑA -->
                        <div class="mb-4">
                            <label for="confirmPassword" class="form-label fw-bold">
                                <i class="fas fa-lock me-2"></i>
                                Confirmar contraseña
                            </label>
                            <input type="password" id="confirmPassword" name="confirmPassword" 
                                   class="form-control form-control-lg" 
                                   placeholder="Vuelve a escribir tu contraseña" 
                                   required>
                            <div id="confirmError" class="text-danger small mt-1" style="display: none;">
                                <i class="fas fa-exclamation-circle me-1"></i>
                                Las contraseñas no coinciden.
                            </div>
                        </div>

                        <!-- BOTÓN REGISTRARSE -->
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-dark btn-lg py-3">
                                <i class="fas fa-user-check me-2"></i>
                                Registrarse
                            </button>
                        </div>

                        <!-- MENSAJE DE CONFIRMACIÓN (oculto inicialmente) -->
                        <div id="mensajeConfirmacion" class="alert alert-success mt-4" style="display: none;">
                            <i class="fas fa-check-circle me-2"></i>
                            ¡Cuenta creada exitosamente! Bienvenido a JuárezNow.
                        </div>

                    </form>

                    <!-- ENLACE A LOGIN -->
                    <div class="text-center mt-4">
                        <p class="mb-0">
                            ¿Ya tienes una cuenta? 
                            <a href="iniciar_sesion.php" class="fw-bold text-decoration-none">
                                Inicia sesión aquí
                            </a>
                        </p>
                    </div>

                    <!-- VOLVER AL INICIO -->
                    <div class="text-center mt-3">
                        <a href="../index.php" class="text-muted text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i>
                            Volver al inicio
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <!-- ===== FOOTER (REUTILIZADO) ===== -->
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
    <script src="../js/registrarse.js"></script>
    
</body>
</html>