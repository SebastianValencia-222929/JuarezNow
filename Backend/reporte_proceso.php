<?php
// 1. Iniciar la sesión para saber qué usuario está reportando
session_start();

// Incluir la conexión a la base de datos JuárezNow
include('conexion.php');

// Verificar que el usuario tenga una sesión activa (por seguridad)
if (!isset($_SESSION['usuario_id'])) {
    die("Acceso denegado. Debes iniciar sesión para reportar.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 2. Capturar los campos de texto del formulario ($_POST)
    $tipo_incidente = $_POST['tipo_incidente'];
    $calle          = $_POST['calle'];
    $referencia     = !empty($_POST['referencia']) ? $_POST['referencia'] : null;
    $descripcion    = $_POST['descripcion'];
    $usuario_id     = $_SESSION['usuario_id']; // Recuperado de la sesión activa
    
    $foto_url = null; // Por defecto es null si no suben foto

    // 3. Procesar la subida de la foto opcional ($_FILES)
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        
        // Creamos una carpeta llamada 'uploads' dentro de Backend si no existe
        $directorio_subida = __DIR__ . '/uploads/';
        if (!file_exists($directorio_subida)) {
            mkdir($directorio_subida, 0777, true);
        }

        // Obtener detalles del archivo subido
        $nombre_original = $_FILES['foto']['name'];
        $extension = pathinfo($nombre_original, PATHINFO_EXTENSION);
        
        // Generar un nombre único para evitar que fotos con el mismo nombre se sobreescriban
        $nuevo_nombre_foto = "reporte_" . time() . "_" . uniqid() . "." . $extension;
        $ruta_destino = $directorio_subida . $nuevo_nombre_foto;

        // Mover el archivo temporal a la carpeta definitiva
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $ruta_destino)) {
            // Guardamos esta ruta para insertarla en la base de datos (relativa a Backend/)
            $foto_url = 'uploads/' . $nuevo_nombre_foto; 
        }
    }

    // 4. Preparar la consulta SQL con los campos correspondientes
    $sql = "INSERT INTO reportes (tipo_incidente, calle, referencia, descripcion, foto_url, usuario_id) VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conexion->prepare($sql);
    
    // "sssssi" significa: 5 strings (s) y 1 entero (i) para el usuario_id
    $stmt->bind_param("sssssi", $tipo_incidente, $calle, $referencia, $descripcion, $foto_url, $usuario_id);

    if ($stmt->execute()) {
        // Redirigir al usuario con un mensaje de éxito
        header("Location: ../Frontend/resources/reportar.php?envio=exitoso");
        exit();
    } else {
        echo "Error al guardar el reporte: " . $stmt->error;
    }

    $stmt->close();
    $conexion->close();
}
?>