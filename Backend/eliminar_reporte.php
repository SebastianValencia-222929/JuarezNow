<?php
include('conexion.php');
include('seguridad.php');

// 1. Asegurar sesión activa
validar_sesion_activa("../Frontend/resources/iniciar_sesion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 2. Validar Token CSRF
    $token_recibido = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    if (!validar_token_csrf($token_recibido)) {
        header("Location: ../Frontend/resources/mis_reportes.php?error=csrf");
        exit();
    }

    // 3. Capturar y sanitizar ID del reporte
    $reporte_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $usuario_id = $_SESSION['usuario_id'];

    // 4. Buscar el reporte para validar pertenencia y obtener ruta de foto
    $sql = "SELECT foto_url, usuario_id FROM reportes WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $reporte_id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
        header("Location: ../Frontend/resources/mis_reportes.php?error=no_encontrado");
        exit();
    }

    $reporte = $resultado->fetch_assoc();

    // 5. Validar que el usuario actual sea el creador del reporte
    if ($reporte['usuario_id'] != $usuario_id) {
        header("Location: ../Frontend/resources/mis_reportes.php?error=no_propietario");
        exit();
    }

    // 6. Eliminar el archivo físico de imagen del servidor si existe
    if (!empty($reporte['foto_url'])) {
        $ruta_foto = __DIR__ . '/' . $reporte['foto_url'];
        if (file_exists($ruta_foto)) {
            unlink($ruta_foto);
        }
    }

    // 7. Eliminar el registro en la base de datos (con Prepared Statement)
    $sql_delete = "DELETE FROM reportes WHERE id = ?";
    $stmt_delete = $conexion->prepare($sql_delete);
    $stmt_delete->bind_param("i", $reporte_id);

    if ($stmt_delete->execute()) {
        $stmt_delete->close();
        $stmt->close();
        $conexion->close();
        header("Location: ../Frontend/resources/mis_reportes.php?eliminado=exitoso");
        exit();
    } else {
        $stmt_delete->close();
        $stmt->close();
        $conexion->close();
        header("Location: ../Frontend/resources/mis_reportes.php?error=db");
        exit();
    }
}
?>
