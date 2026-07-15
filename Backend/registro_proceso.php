<?php
include('conexion.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Captura los datos enviados por el formulario
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $password = $_POST['password'];

    // Encripta la contraseña por seguridad
    $password_encriptada = password_hash($password, PASSWORD_BCRYPT);

    // Prepara e inserta en la base de datos
    $sql = "INSERT INTO usuarios (nombre, apellido, correo, password) VALUES (?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssss", $nombre, $apellido, $correo, $password_encriptada);

    if ($stmt->execute()) {
        // Registro exitoso: Redirige al usuario a la vista de inicio de sesión en el Frontend
        header("Location: ../Frontend/resources/iniciar_sesion.php?registro=exitoso");
        exit();
    } else {
        echo "Error al registrar el usuario: " . $stmt->error;
    }

    $stmt->close();
    $conexion->close();
}
?>