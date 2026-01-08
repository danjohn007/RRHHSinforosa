<?php
/**
 * Archivo principal - Router y punto de entrada
 */

require_once 'config/config.php';

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Función auxiliar para cargar vistas
function view($view, $data = []) {
    extract($data);
    $viewFile = BASE_PATH . "app/views/{$view}.php";
    if (file_exists($viewFile)) {
        require_once $viewFile;
    } else {
        die("Vista no encontrada: {$view}");
    }
}

// Función para redireccionar
function redirect($url) {
    header("Location: " . BASE_URL . ltrim($url, '/'));
    exit();
}

// Autoloader simple para modelos y controladores
spl_autoload_register(function ($class) {
    $paths = [
        BASE_PATH . 'app/models/' . $class . '.php',
        BASE_PATH . 'app/controllers/' . $class . '.php',
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Router simple
$request = $_SERVER['REQUEST_URI'];
$request = str_replace(parse_url(BASE_URL, PHP_URL_PATH), '', $request);
$request = strtok($request, '?');
$request = trim($request, '/');

// Rutas del sistema
$routes = [
    '' => ['controller' => 'DashboardController', 'method' => 'index'],
    'login' => ['controller' => 'AuthController', 'method' => 'login'],
    'logout' => ['controller' => 'AuthController', 'method' => 'logout'],
    'dashboard' => ['controller' => 'DashboardController', 'method' => 'index'],
    
    // Gestión de Personal
    'empleados' => ['controller' => 'EmpleadosController', 'method' => 'index'],
    'empleados/crear' => ['controller' => 'EmpleadosController', 'method' => 'crear'],
    'empleados/editar' => ['controller' => 'EmpleadosController', 'method' => 'editar'],
    'empleados/ver' => ['controller' => 'EmpleadosController', 'method' => 'ver'],
    'empleados/historial' => ['controller' => 'EmpleadosController', 'method' => 'historial'],
    'empleados/documentos' => ['controller' => 'EmpleadosController', 'method' => 'documentos'],
    'empleados/carta-recomendacion' => ['controller' => 'EmpleadosController', 'method' => 'cartaRecomendacion'],
    'empleados/constancia' => ['controller' => 'EmpleadosController', 'method' => 'constancia'],
    
    // Nómina
    'nomina' => ['controller' => 'NominaController', 'method' => 'index'],
    'nomina/procesar' => ['controller' => 'NominaController', 'method' => 'procesar'],
    'nomina/recibos' => ['controller' => 'NominaController', 'method' => 'recibos'],
    'nomina/configuracion' => ['controller' => 'NominaController', 'method' => 'configuracion'],
    
    // Asistencia
    'asistencia' => ['controller' => 'AsistenciaController', 'method' => 'index'],
    'asistencia/registro' => ['controller' => 'AsistenciaController', 'method' => 'registro'],
    'asistencia/vacaciones' => ['controller' => 'AsistenciaController', 'method' => 'vacaciones'],
    'asistencia/turnos' => ['controller' => 'AsistenciaController', 'method' => 'turnos'],
    
    // Reclutamiento
    'reclutamiento' => ['controller' => 'ReclutamientoController', 'method' => 'index'],
    'reclutamiento/candidatos' => ['controller' => 'ReclutamientoController', 'method' => 'candidatos'],
    'reclutamiento/entrevistas' => ['controller' => 'ReclutamientoController', 'method' => 'entrevistas'],
    
    // Beneficios
    'beneficios' => ['controller' => 'BeneficiosController', 'method' => 'index'],
    'beneficios/prestamos' => ['controller' => 'BeneficiosController', 'method' => 'prestamos'],
    'beneficios/bonos' => ['controller' => 'BeneficiosController', 'method' => 'bonos'],
    
    // Reportes
    'reportes' => ['controller' => 'ReportesController', 'method' => 'index'],
    'reportes/personal' => ['controller' => 'ReportesController', 'method' => 'personal'],
    'reportes/nomina' => ['controller' => 'ReportesController', 'method' => 'nomina'],
    'reportes/vacaciones' => ['controller' => 'ReportesController', 'method' => 'vacaciones'],
];

// Buscar ruta coincidente
$matched = false;
foreach ($routes as $route => $handler) {
    if ($request === $route) {
        $controller = new $handler['controller']();
        $method = $handler['method'];
        $controller->$method();
        $matched = true;
        break;
    }
}

// Si no se encuentra la ruta
if (!$matched) {
    // Intentar con parámetros dinámicos (ej: empleados/ver/1)
    $parts = explode('/', $request);
    $baseRoute = implode('/', array_slice($parts, 0, 2));
    
    if (isset($routes[$baseRoute])) {
        $controller = new $routes[$baseRoute]['controller']();
        $method = $routes[$baseRoute]['method'];
        $controller->$method();
    } else {
        http_response_code(404);
        echo "404 - Página no encontrada";
    }
}
