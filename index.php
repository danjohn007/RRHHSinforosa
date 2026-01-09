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
        BASE_PATH . 'app/services/' . $class . '.php',
        BASE_PATH . 'app/helpers/' . $class . '.php',
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

// Si no hay sesión y no está en login, redirigir
if (!isset($_SESSION['user_id']) && $request !== 'login' && $request !== 'auth/login') {
    redirect('login');
}

// Rutas
if ($request === '' || $request === 'login') {
    $controller = new AuthController();
    $controller->login();
} elseif ($request === 'logout') {
    $controller = new AuthController();
    $controller->logout();
} elseif ($request === 'dashboard') {
    $controller = new DashboardController();
    $controller->index();
} elseif (strpos($request, 'empleados') === 0) {
    $controller = new EmpleadosController();
    $parts = explode('/', $request);
    
    if (count($parts) === 1) {
        $controller->index();
    } elseif ($parts[1] === 'crear') {
        $controller->crear();
    } elseif ($parts[1] === 'editar' && isset($parts[2])) {
        $controller->editar($parts[2]);
    } elseif ($parts[1] === 'ver' && isset($parts[2])) {
        $controller->ver($parts[2]);
    } elseif ($parts[1] === 'eliminar' && isset($parts[2])) {
        $controller->eliminar($parts[2]);
    } elseif ($parts[1] === 'constancia' && isset($parts[2])) {
        $controller->constancia($parts[2]);
    } elseif ($parts[1] === 'carta-recomendacion' && isset($parts[2])) {
        $controller->cartaRecomendacion($parts[2]);
    } else {
        http_response_code(404);
        die('Página no encontrada');
    }
} elseif (strpos($request, 'asistencia') === 0) {
    $controller = new AsistenciaController();
    $parts = explode('/', $request);
    
    if (count($parts) === 1) {
        $controller->index();
    } elseif ($parts[1] === 'registro') {
        $controller->registro();
    } elseif ($parts[1] === 'turnos') {
        $controller->turnos();
    } elseif ($parts[1] === 'vacaciones') {
        $controller->vacaciones();
    } else {
        http_response_code(404);
        die('Página no encontrada');
    }
} elseif (strpos($request, 'nomina') === 0) {
    $controller = new NominaController();
    $parts = explode('/', $request);
    
    if (count($parts) === 1) {
        $controller->index();
    } elseif ($parts[1] === 'procesar') {
        $controller->procesar();
    } elseif ($parts[1] === 'recibos') {
        $controller->recibos();
    } elseif ($parts[1] === 'configuracion') {
        $controller->configuracion();
    } else {
        http_response_code(404);
        die('Página no encontrada');
    }
} elseif (strpos($request, 'beneficios') === 0) {
    $controller = new BeneficiosController();
    $controller->index();
} elseif (strpos($request, 'reclutamiento') === 0) {
    $controller = new ReclutamientoController();
    $parts = explode('/', $request);
    
    if (count($parts) === 1) {
        $controller->index();
    } elseif ($parts[1] === 'entrevistas') {
        $controller->entrevistas();
    } else {
        http_response_code(404);
        die('Página no encontrada');
    }
} elseif (strpos($request, 'reportes') === 0) {
    $controller = new ReportesController();
    $parts = explode('/', $request);
    
    if (count($parts) === 1) {
        $controller->index();
    } elseif ($parts[1] === 'personal') {
        $controller->personal();
    } elseif ($parts[1] === 'nomina') {
        $controller->nomina();
    } elseif ($parts[1] === 'vacaciones') {
        $controller->vacaciones();
    } else {
        http_response_code(404);
        die('Página no encontrada');
    }
} elseif (strpos($request, 'errores') === 0) {
    $controller = new ErroresController();
    $parts = explode('/', $request);
    
    if (count($parts) === 1) {
        $controller->index();
    } elseif ($parts[1] === 'limpiar') {
        $controller->limpiar();
    } elseif ($parts[1] === 'descargar') {
        $controller->descargar();
    } elseif ($parts[1] === 'obtener-json') {
        $controller->obtenerJson();
    } else {
        http_response_code(404);
        die('Página no encontrada');
    }
} else {
    http_response_code(404);
    die('Página no encontrada');
}
