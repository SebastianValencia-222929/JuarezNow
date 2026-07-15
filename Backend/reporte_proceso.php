<?php
include('conexion.php');
include('seguridad.php');

// 1. Asegurar sesión activa
validar_sesion_activa("../Frontend/resources/iniciar_sesion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 2. Validar Token CSRF
    $token_recibido = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    if (!validar_token_csrf($token_recibido)) {
        header("Location: ../Frontend/resources/reportar.php?error=csrf");
        exit();
    }

    // 3. Capturar y sanitizar datos
    $tipo_incidente = sanitizar_entrada($_POST['tipo_incidente']);
    $calle          = sanitizar_entrada($_POST['calle']);
    $referencia     = !empty($_POST['referencia']) ? sanitizar_entrada($_POST['referencia']) : null;
    $descripcion    = sanitizar_entrada($_POST['descripcion']);
    $usuario_id     = $_SESSION['usuario_id']; // Recuperado de la sesión activa
    
    // 4. Validar campos obligatorios del servidor
    if (empty($tipo_incidente) || empty($calle) || empty($descripcion)) {
        header("Location: ../Frontend/resources/reportar.php?error=campos_vacios");
        exit();
    }

    // Validar tipo de incidente permitido
    $categorias_permitidas = ['accidente', 'inundacion', 'trafico', 'hundimiento'];
    if (!in_array($tipo_incidente, $categorias_permitidas)) {
        header("Location: ../Frontend/resources/reportar.php?error=categoria_invalida");
        exit();
    }

    $foto_url = null; // Por defecto es null si no suben foto

    // 5. Procesar la subida de la foto de forma totalmente segura
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        
        $file_tmp  = $_FILES['foto']['tmp_name'];
        $file_name = $_FILES['foto']['name'];
        $file_size = $_FILES['foto']['size'];
        
        // A. Validar tamaño máximo: 2 Megabytes (2 * 1024 * 1024 bytes)
        $max_size = 2 * 1024 * 1024;
        if ($file_size > $max_size) {
            header("Location: ../Frontend/resources/reportar.php?error=tamano_imagen");
            exit();
        }

        // B. Validar extensión permitida
        $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($extension, $extensiones_permitidas)) {
            header("Location: ../Frontend/resources/reportar.php?error=extension_invalida");
            exit();
        }

        // C. Validar tipo MIME real de la imagen usando getimagesize o mime_content_type
        if (function_exists('mime_content_type')) {
            $mime_type = mime_content_type($file_tmp);
        } else {
            $image_info = getimagesize($file_tmp);
            $mime_type = $image_info ? $image_info['mime'] : '';
        }

        $mimes_permitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/pjpeg', 'image/x-png'];
        if (!in_array($mime_type, $mimes_permitidos)) {
            header("Location: ../Frontend/resources/reportar.php?error=formato_imagen");
            exit();
        }

        // D. Crear directorio si no existe
        $directorio_subida = __DIR__ . '/uploads/';
        if (!file_exists($directorio_subida)) {
            mkdir($directorio_subida, 0777, true);
        }
        
        // E. Generar un nombre único aleatorio para evitar sobreescritura y malware de ejecución
        $nuevo_nombre_foto = "reporte_" . time() . "_" . bin2hex(random_bytes(8)) . "." . $extension;
        $ruta_destino = $directorio_subida . $nuevo_nombre_foto;

        // F. Mover el archivo temporal a la carpeta definitiva
        if (move_uploaded_file($file_tmp, $ruta_destino)) {
            $foto_url = 'uploads/' . $nuevo_nombre_foto; 
        } else {
            header("Location: ../Frontend/resources/reportar.php?error=error_subida");
            exit();
        }
    }

    // 6. Preparar e insertar en la base de datos (con Prepared Statements)
    $sql = "INSERT INTO reportes (tipo_incidente, calle, referencia, descripcion, foto_url, usuario_id) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssssi", $tipo_incidente, $calle, $referencia, $descripcion, $foto_url, $usuario_id);

    if ($stmt->execute()) {
        $stmt->close();
        $conexion->close();
        header("Location: ../Frontend/resources/reportar.php?envio=exitoso");
        exit();
    } else {
        $stmt->close();
        $conexion->close();
        header("Location: ../Frontend/resources/reportar.php?error=db");
        exit();
    }
}
?>