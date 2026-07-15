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

    // 3. Capturar y sanitizar datos
    $reporte_id     = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $tipo_incidente = sanitizar_entrada($_POST['tipo_incidente']);
    $calle          = sanitizar_entrada($_POST['calle']);
    $referencia     = !empty($_POST['referencia']) ? sanitizar_entrada($_POST['referencia']) : null;
    $descripcion    = sanitizar_entrada($_POST['descripcion']);
    $usuario_id     = $_SESSION['usuario_id'];

    // 4. Buscar el reporte para validar pertenencia y obtener foto anterior
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

    // 6. Validar campos obligatorios
    if (empty($tipo_incidente) || empty($calle) || empty($descripcion)) {
        header("Location: ../Frontend/resources/editar_reporte.php?id={$reporte_id}&error=campos_vacios");
        exit();
    }

    $categorias_permitidas = ['accidente', 'inundacion', 'trafico', 'hundimiento'];
    if (!in_array($tipo_incidente, $categorias_permitidas)) {
        header("Location: ../Frontend/resources/editar_reporte.php?id={$reporte_id}&error=categoria_invalida");
        exit();
    }

    $foto_url = $reporte['foto_url']; // Por defecto se mantiene la foto anterior

    // 7. Procesar nueva foto si el usuario subió una
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        
        $file_tmp  = $_FILES['foto']['tmp_name'];
        $file_name = $_FILES['foto']['name'];
        $file_size = $_FILES['foto']['size'];
        
        // A. Validar tamaño máximo: 2 Megabytes
        $max_size = 2 * 1024 * 1024;
        if ($file_size > $max_size) {
            header("Location: ../Frontend/resources/editar_reporte.php?id={$reporte_id}&error=tamano_imagen");
            exit();
        }

        // B. Validar extensión permitida
        $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($extension, $extensiones_permitidas)) {
            header("Location: ../Frontend/resources/editar_reporte.php?id={$reporte_id}&error=extension_invalida");
            exit();
        }

        // C. Validar tipo MIME
        if (function_exists('mime_content_type')) {
            $mime_type = mime_content_type($file_tmp);
        } else {
            $image_info = getimagesize($file_tmp);
            $mime_type = $image_info ? $image_info['mime'] : '';
        }

        $mimes_permitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/pjpeg', 'image/x-png'];
        if (!in_array($mime_type, $mimes_permitidos)) {
            header("Location: ../Frontend/resources/editar_reporte.php?id={$reporte_id}&error=formato_imagen");
            exit();
        }

        // D. Crear directorio si no existe
        $directorio_subida = __DIR__ . '/uploads/';
        if (!file_exists($directorio_subida)) {
            mkdir($directorio_subida, 0777, true);
        }

        // E. Eliminar foto anterior física del servidor si existe
        if (!empty($reporte['foto_url'])) {
            $ruta_foto_ant = __DIR__ . '/' . $reporte['foto_url'];
            if (file_exists($ruta_foto_ant)) {
                unlink($ruta_foto_ant);
            }
        }

        // F. Generar nombre único y mover archivo temporal
        $nuevo_nombre_foto = "reporte_" . time() . "_" . bin2hex(random_bytes(8)) . "." . $extension;
        $ruta_destino = $directorio_subida . $nuevo_nombre_foto;

        if (move_uploaded_file($file_tmp, $ruta_destino)) {
            $foto_url = 'uploads/' . $nuevo_nombre_foto; 
        } else {
            header("Location: ../Frontend/resources/editar_reporte.php?id={$reporte_id}&error=error_subida");
            exit();
        }
    }

    // 8. Actualizar registro en la base de datos (con Prepared Statement)
    $sql_update = "UPDATE reportes SET tipo_incidente = ?, calle = ?, referencia = ?, descripcion = ?, foto_url = ? WHERE id = ?";
    $stmt_update = $conexion->prepare($sql_update);
    $stmt_update->bind_param("sssssi", $tipo_incidente, $calle, $referencia, $descripcion, $foto_url, $reporte_id);

    if ($stmt_update->execute()) {
        $stmt_update->close();
        $stmt->close();
        $conexion->close();
        header("Location: ../Frontend/resources/mis_reportes.php?actualizado=exitoso");
        exit();
    } else {
        $stmt_update->close();
        $stmt->close();
        $conexion->close();
        header("Location: ../Frontend/resources/editar_reporte.php?id={$reporte_id}&error=db");
        exit();
    }
}
?>
