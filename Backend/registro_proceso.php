<?php
include('conexion.php');
include('seguridad.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Validar Token CSRF
    $token_recibido = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    if (!validar_token_csrf($token_recibido)) {
        header("Location: ../Frontend/resources/registrarse.php?error=csrf");
        exit();
    }

    // 2. Capturar y sanitizar datos
    $nombre   = sanitizar_entrada($_POST['nombre']);
    $apellido = sanitizar_entrada($_POST['apellido']);
    $correo   = sanitizar_entrada($_POST['correo']);
    $password = $_POST['password']; // No sanitizar la contraseña para no alterar caracteres válidos

    // 3. Validaciones del lado del servidor
    if (empty($nombre) || empty($apellido) || empty($correo) || empty($password)) {
        header("Location: ../Frontend/resources/registrarse.php?error=campos_vacios");
        exit();
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../Frontend/resources/registrarse.php?error=correo_invalido");
        exit();
    }

    if (strlen($password) < 6) {
        header("Location: ../Frontend/resources/registrarse.php?error=password_corto");
        exit();
    }

    // 4. Encriptar contraseña
    $password_encriptada = password_hash($password, PASSWORD_BCRYPT);

    // 5. Preparar e insertar en la base de datos (con Prepared Statements)
    $sql = "INSERT INTO usuarios (nombre, apellido, correo, password) VALUES (?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssss", $nombre, $apellido, $correo, $password_encriptada);

    if ($stmt->execute()) {
        $stmt->close();
        $conexion->close();
        header("Location: ../Frontend/resources/iniciar_sesion.php?registro=exitoso");
        exit();
    } else {
        // Verificar si el error es por correo duplicado (código SQL 1062)
        if ($conexion->errno === 1062) {
            header("Location: ../Frontend/resources/registrarse.php?error=correo_duplicado");
        } else {
            header("Location: ../Frontend/resources/registrarse.php?error=error_registro");
        }
        $stmt->close();
        $conexion->close();
        exit();
    }
}
?>