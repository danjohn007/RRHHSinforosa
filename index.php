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
    '' => ['controller' => 'AuthController', 'method' => 'login'],  // Root redirects to login
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
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>404 - Página no encontrada</title>
            <script src="https://cdn.tailwindcss.com"></script>
        </head>
        <body class="bg-gray-100">
            <div class="min-h-screen flex items-center justify-center px-4">
                <div class="max-w-md w-full text-center">
                    <div class="mb-8">
                        <h1 class="text-6xl font-bold text-purple-600">404</h1>
                        <p class="text-2xl font-semibold text-gray-800 mt-4">Página no encontrada</p>
                        <p class="text-gray-600 mt-2">La ruta "<?php echo htmlspecialchars($request); ?>" no existe en el sistema.</p>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <p class="text-sm text-gray-600 mb-4">¿Necesitas ayuda? Intenta con estas páginas:</p>
                        <div class="space-y-2">
                            <a href="<?php echo BASE_URL; ?>login" class="block w-full bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
                                Ir al Login
                            </a>
                            <a href="<?php echo BASE_URL; ?>dashboard" class="block w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                                Ir al Dashboard
                            </a>
                        </div>
                    </div>
                    <div class="mt-6 text-xs text-gray-500">
                        <p>BASE_URL: <?php echo htmlspecialchars(BASE_URL); ?></p>
                        <p>Request: <?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?></p>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
    }
}
