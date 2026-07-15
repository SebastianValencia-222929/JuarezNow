<?php
// Asegurar que la sesión esté activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Genera o recupera el token CSRF para el usuario actual
 */
function obtener_token_csrf() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Valida si el token recibido coincide con el de la sesión
 */
function validar_token_csrf($token) {
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Asegura que el usuario esté autenticado, de lo contrario redirige
 */
function validar_sesion_activa($path_retorno = "../resources/iniciar_sesion.php") {
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: " . $path_retorno . "?error=sesion_requerida");
        exit();
    }
}

/**
 * Limpia y sanitiza las entradas de texto
 */
function sanitizar_entrada($datos) {
    if (is_array($datos)) {
        return array_map('sanitizar_entrada', $datos);
    }
    $datos = trim($datos);
    $datos = stripslashes($datos);
    $datos = htmlspecialchars($datos, ENT_QUOTES, 'UTF-8');
    return $datos;
}
?>
