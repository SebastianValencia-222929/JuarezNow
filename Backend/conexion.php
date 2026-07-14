<?php
$servidor   = "localhost";
$usuario    = "root";       
$clave      = "";           
$base_datos = "JuarezNow";  

// Conexión
$conexion = new mysqli($servidor, $usuario, $clave, $base_datos);

// Verifica si hay errores de conexión
if ($conexion->connect_error) {
    die("Error crítico: No se pudo conectar a la base de datos " . $conexion->connect_error);
}

// Configurar el juego de caracteres a UTF-8 para evitar problemas con acentos y la 'ñ'
$conexion->set_charset("utf8");
?>