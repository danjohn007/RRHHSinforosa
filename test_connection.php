<?php
/**
 * Test de Conexión a Base de Datos y Verificación de URL Base
 */

require_once 'config/config.php';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Configuración - <?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Test de Configuración</h1>
            
            <!-- URL Base -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-4">
                <h2 class="text-xl font-semibold text-gray-700 mb-3">
                    <span class="text-green-500">✓</span> URL Base
                </h2>
                <p class="text-gray-600">
                    <strong>URL Base Detectada:</strong><br>
                    <code class="bg-gray-100 px-2 py-1 rounded"><?php echo BASE_URL; ?></code>
                </p>
            </div>
            
            <!-- Conexión a Base de Datos -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-4">
                <h2 class="text-xl font-semibold text-gray-700 mb-3">
                    Conexión a Base de Datos
                </h2>
                <?php
                try {
                    $db = Database::getInstance();
                    $conn = $db->getConnection();
                    
                    // Verificar si la base de datos existe
                    $stmt = $conn->query("SELECT DATABASE() as db");
                    $result = $stmt->fetch();
                    
                    echo '<div class="bg-green-50 border-l-4 border-green-500 p-4">';
                    echo '<p class="text-green-700"><span class="font-bold">✓ Conexión Exitosa</span></p>';
                    echo '<p class="text-green-600 mt-2">Base de datos: <strong>' . $result['db'] . '</strong></p>';
                    echo '<p class="text-green-600">Host: <strong>' . DB_HOST . '</strong></p>';
                    echo '<p class="text-green-600">Usuario: <strong>' . DB_USER . '</strong></p>';
                    echo '</div>';
                    
                    // Verificar tablas
                    $stmt = $conn->query("SHOW TABLES");
                    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                    
                    echo '<div class="mt-4">';
                    echo '<p class="text-gray-700 font-semibold mb-2">Tablas encontradas (' . count($tables) . '):</p>';
                    if (count($tables) > 0) {
                        echo '<ul class="list-disc list-inside text-gray-600">';
                        foreach ($tables as $table) {
                            echo '<li>' . htmlspecialchars($table) . '</li>';
                        }
                        echo '</ul>';
                    } else {
                        echo '<p class="text-yellow-600">⚠ No se encontraron tablas. Ejecuta el archivo schema.sql para crear la estructura.</p>';
                    }
                    echo '</div>';
                    
                } catch (Exception $e) {
                    echo '<div class="bg-red-50 border-l-4 border-red-500 p-4">';
                    echo '<p class="text-red-700"><span class="font-bold">✗ Error de Conexión</span></p>';
                    echo '<p class="text-red-600 mt-2">' . htmlspecialchars($e->getMessage()) . '</p>';
                    echo '<p class="text-red-600 mt-2">Verifica las credenciales en <code>config/database.php</code></p>';
                    echo '</div>';
                }
                ?>
            </div>
            
            <!-- Configuración PHP -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-4">
                <h2 class="text-xl font-semibold text-gray-700 mb-3">
                    <span class="text-green-500">✓</span> Configuración PHP
                </h2>
                <ul class="text-gray-600 space-y-1">
                    <li><strong>Versión PHP:</strong> <?php echo phpversion(); ?></li>
                    <li><strong>Zona Horaria:</strong> <?php echo date_default_timezone_get(); ?></li>
                    <li><strong>PDO MySQL:</strong> <?php echo extension_loaded('pdo_mysql') ? '✓ Habilitado' : '✗ No disponible'; ?></li>
                    <li><strong>Session:</strong> <?php echo extension_loaded('session') ? '✓ Habilitado' : '✗ No disponible'; ?></li>
                </ul>
            </div>
            
            <!-- Directorios -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-4">
                <h2 class="text-xl font-semibold text-gray-700 mb-3">
                    <span class="text-green-500">✓</span> Estructura de Directorios
                </h2>
                <ul class="text-gray-600 space-y-1">
                    <?php
                    $dirs = ['app/controllers', 'app/models', 'app/views', 'config', 'public'];
                    foreach ($dirs as $dir) {
                        $exists = is_dir(BASE_PATH . $dir);
                        $icon = $exists ? '✓' : '✗';
                        $color = $exists ? 'text-green-600' : 'text-red-600';
                        echo "<li class='$color'><strong>$icon</strong> $dir</li>";
                    }
                    ?>
                </ul>
            </div>
            
            <div class="text-center mt-6">
                <a href="<?php echo BASE_URL; ?>" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                    Ir al Sistema
                </a>
            </div>
        </div>
    </div>
</body>
</html>
