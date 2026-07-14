<?php
// 1. Iniciar la sesión antes de cualquier otra cosa
session_start();

// 2. Incluir la conexión (está en la misma carpeta Backend)
include('conexion.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 3. Capturar únicamente los dos campos que viste en tu interfaz
    $correo = $_POST['correo'];
    $password = $_POST['password'];

    // 4. Buscar al usuario en la base de datos por su correo electrónico
    $sql = "SELECT id, nombre, apellido, password FROM usuarios WHERE correo = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    // 5. Verificar si el correo existe
    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        // 6. Verificar si la contraseña ingresada coincide con el hash encriptado de la BD
        if (password_verify($password, $usuario['password'])) {
            
            // ¡Login correcto! Guardamos los datos clave en la sesión
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_apellido'] = $usuario['apellido'];

            // Redirigir a la página de inicio o al mapa del Frontend
            header("Location: ../Frontend/resources/reportar.php"); 
            exit();
        } else {
            // Contraseña incorrecta
            header("Location: ../Frontend/resources/iniciar_sesion.php?error=credenciales");
            exit();
        }
    } else {
        // El correo no está registrado
        header("Location: ../Frontend/resources/iniciar_sesion.php?error=credenciales");
        exit();
    }

    $stmt->close();
    $conexion->close();
}
?>