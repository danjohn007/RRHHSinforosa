<?php
/**
 * Configuración General del Sistema
 * Sistema de GESTIÓN INTEGRAL DE TALENTO Y NÓMINA
 */

// Configuración de la URL base (detección automática)
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $script = $_SERVER['SCRIPT_NAME'];
    $path = str_replace(basename($script), '', $script);
    return $protocol . '://' . $host . $path;
}

define('BASE_URL', getBaseUrl());
define('BASE_PATH', dirname(__DIR__) . '/');

// Configuración de la aplicación
define('APP_NAME', 'Sistema RRHH Sinforosa Café');
define('APP_VERSION', '1.0.0');

// Zona horaria
date_default_timezone_set('America/Mexico_City');

// Modo de desarrollo (cambiar a false en producción)
// En desarrollo, permite conexiones SSL sin verificar certificados
define('DEVELOPMENT_MODE', true);

// Configuración de sesiones
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));

// Manejo de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir archivo de base de datos
require_once BASE_PATH . 'config/database.php';
