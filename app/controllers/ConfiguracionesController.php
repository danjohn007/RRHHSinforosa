<?php
/**
 * Controlador de Configuraciones Globales
 */

class ConfiguracionesController {
    
    public function index() {
        AuthController::check();
        
        // Solo admins pueden acceder
        if ($_SESSION['user_rol'] !== 'admin') {
            redirect('dashboard');
        }
        
        $db = Database::getInstance()->getConnection();
        
        // Obtener todas las configuraciones agrupadas
        $stmt = $db->query("SELECT * FROM configuraciones_globales ORDER BY grupo, clave");
        $configuraciones = $stmt->fetchAll();
        
        // Agrupar configuraciones
        $configs = [];
        foreach ($configuraciones as $config) {
            $configs[$config['grupo']][] = $config;
        }
        
        $data = [
            'title' => 'Configuraciones Globales',
            'configs' => $configs
        ];
        
        // Cargar vista con layout
        ob_start();
        require_once BASE_PATH . 'app/views/configuraciones/index.php';
        $content = ob_get_clean();
        
        require_once BASE_PATH . 'app/views/layouts/main.php';
    }
    
    public function guardar() {
        AuthController::check();
        
        if ($_SESSION['user_rol'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        $db = Database::getInstance()->getConnection();
        
        try {
            $configuraciones = $_POST['configuraciones'] ?? [];
            
            // Guardar el nombre del sitio antes de procesar el logo
            $sitioNombre = $configuraciones['sitio_nombre'] ?? null;
            
            // Manejar upload de logo si existe
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $logoPath = $this->subirLogo($_FILES['logo']);
                if ($logoPath) {
                    $configuraciones['sitio_logo'] = $logoPath;
                }
            }
            
            // Manejar upload de certificado e.firma si existe
            if (isset($_FILES['timbrado_certificado']) && $_FILES['timbrado_certificado']['error'] === UPLOAD_ERR_OK) {
                $certPath = $this->subirEfirma($_FILES['timbrado_certificado'], 'certificado');
                if ($certPath) {
                    $configuraciones['timbrado_certificado'] = $certPath;
                }
            }
            
            // Manejar upload de llave privada e.firma si existe
            if (isset($_FILES['timbrado_llave_privada']) && $_FILES['timbrado_llave_privada']['error'] === UPLOAD_ERR_OK) {
                $llavePath = $this->subirEfirma($_FILES['timbrado_llave_privada'], 'llave');
                if ($llavePath) {
                    $configuraciones['timbrado_llave_privada'] = $llavePath;
                }
            }
            
            // Asegurar que sitio_nombre no se sobrescriba con la ruta del logo
            if ($sitioNombre !== null) {
                $configuraciones['sitio_nombre'] = $sitioNombre;
            }
            
            foreach ($configuraciones as $clave => $valor) {
                $stmt = $db->prepare("UPDATE configuraciones_globales SET valor = ? WHERE clave = ?");
                $stmt->execute([$valor, $clave]);
            }
            
            echo json_encode(['success' => true, 'message' => 'Configuraciones guardadas exitosamente']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al guardar configuraciones: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Subir logo del sistema
     */
    private function subirLogo($archivo) {
        try {
            // Validar tipo de archivo
            $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($archivo['type'], $tiposPermitidos)) {
                throw new Exception('Tipo de archivo no permitido');
            }
            
            // Validar tamaño (máximo 2MB)
            if ($archivo['size'] > 2 * 1024 * 1024) {
                throw new Exception('El archivo es muy grande (máximo 2MB)');
            }
            
            // Crear directorio si no existe
            $uploadDir = BASE_PATH . 'uploads/logos';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Generar nombre único
            $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
            $filename = 'logo_' . time() . '.' . $extension;
            $filepath = $uploadDir . '/' . $filename;
            
            // Mover archivo
            if (move_uploaded_file($archivo['tmp_name'], $filepath)) {
                // Retornar ruta relativa
                return 'uploads/logos/' . $filename;
            }
            
            return false;
        } catch (Exception $e) {
            error_log('Error al subir logo: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Subir archivos de e.firma (certificado y llave)
     */
    private function subirEfirma($archivo, $tipo) {
        try {
            // Validar extensión según tipo
            $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
            
            if ($tipo === 'certificado' && !in_array($extension, ['cer'])) {
                throw new Exception('Tipo de archivo no permitido para certificado. Use archivo .cer');
            }
            
            if ($tipo === 'llave' && !in_array($extension, ['key'])) {
                throw new Exception('Tipo de archivo no permitido para llave privada. Use archivo .key');
            }
            
            // Validar tamaño (máximo 5MB)
            if ($archivo['size'] > 5 * 1024 * 1024) {
                throw new Exception('El archivo es muy grande (máximo 5MB)');
            }
            
            // Crear directorio si no existe
            $uploadDir = BASE_PATH . 'uploads/efirma';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Generar nombre único
            $filename = 'efirma_' . $tipo . '_' . time() . '.' . $extension;
            $filepath = $uploadDir . '/' . $filename;
            
            // Mover archivo
            if (move_uploaded_file($archivo['tmp_name'], $filepath)) {
                // Retornar ruta relativa
                return 'uploads/efirma/' . $filename;
            }
            
            return false;
        } catch (Exception $e) {
            error_log('Error al subir e.firma: ' . $e->getMessage());
            return false;
        }
    }
    
    public function dispositivos() {
        AuthController::check();
        
        if ($_SESSION['user_rol'] !== 'admin') {
            redirect('dashboard');
        }
        
        $db = Database::getInstance()->getConnection();
        
        // Obtener dispositivos Shelly
        $shellyStmt = $db->query("SELECT * FROM dispositivos_shelly ORDER BY nombre");
        $dispositivos_shelly = $shellyStmt->fetchAll();
        
        // Obtener dispositivos HikVision
        $hikvisionStmt = $db->query("SELECT * FROM dispositivos_hikvision ORDER BY nombre");
        $dispositivos_hikvision = $hikvisionStmt->fetchAll();
        
        $data = [
            'title' => 'Dispositivos IoT',
            'dispositivos_shelly' => $dispositivos_shelly,
            'dispositivos_hikvision' => $dispositivos_hikvision
        ];
        
        // Cargar vista con layout
        ob_start();
        require_once BASE_PATH . 'app/views/configuraciones/dispositivos.php';
        $content = ob_get_clean();
        
        require_once BASE_PATH . 'app/views/layouts/main.php';
    }
    
    public function guardarDispositivo() {
        AuthController::check();
        
        if ($_SESSION['user_rol'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        $db = Database::getInstance()->getConnection();
        $tipo = $_POST['tipo'] ?? '';
        
        try {
            if ($tipo === 'shelly') {
                $id = $_POST['id'] ?? null;
                $data = [
                    'nombre' => trim($_POST['nombre']),
                    'device_id' => trim($_POST['device_id']),
                    'token_autenticacion' => trim($_POST['token_autenticacion']),
                    'servidor_cloud' => trim($_POST['servidor_cloud']),
                    'area' => trim($_POST['area']),
                    'canal_entrada' => $_POST['canal_entrada'] ?? 1,
                    'canal_salida' => $_POST['canal_salida'] ?? 0,
                    'duracion_pulso' => $_POST['duracion_pulso'] ?? 600,
                    'accion' => $_POST['accion'] ?? 'Abrir/Cerrar',
                    'habilitado' => (isset($_POST['habilitado']) && $_POST['habilitado']) ? 1 : 0,
                    'invertido' => (isset($_POST['invertido']) && $_POST['invertido']) ? 1 : 0,
                    'simultaneo' => (isset($_POST['simultaneo']) && $_POST['simultaneo']) ? 1 : 0
                ];
                
                if ($id) {
                    // Actualizar
                    $stmt = $db->prepare("UPDATE dispositivos_shelly SET nombre=?, device_id=?, token_autenticacion=?, servidor_cloud=?, area=?, canal_entrada=?, canal_salida=?, duracion_pulso=?, accion=?, habilitado=?, invertido=?, simultaneo=? WHERE id=?");
                    $stmt->execute([
                        $data['nombre'], $data['device_id'], $data['token_autenticacion'],
                        $data['servidor_cloud'], $data['area'], $data['canal_entrada'],
                        $data['canal_salida'], $data['duracion_pulso'], $data['accion'],
                        $data['habilitado'], $data['invertido'], $data['simultaneo'], $id
                    ]);
                } else {
                    // Insertar
                    $stmt = $db->prepare("INSERT INTO dispositivos_shelly (nombre, device_id, token_autenticacion, servidor_cloud, area, canal_entrada, canal_salida, duracion_pulso, accion, habilitado, invertido, simultaneo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $data['nombre'], $data['device_id'], $data['token_autenticacion'],
                        $data['servidor_cloud'], $data['area'], $data['canal_entrada'],
                        $data['canal_salida'], $data['duracion_pulso'], $data['accion'],
                        $data['habilitado'], $data['invertido'], $data['simultaneo']
                    ]);
                }
            } elseif ($tipo === 'hikvision') {
                $id = $_POST['id'] ?? null;
                $data = [
                    'nombre' => $_POST['nombre'],
                    'tipo_dispositivo' => $_POST['tipo_dispositivo'],
                    'api_key' => $_POST['api_key'],
                    'api_secret' => $_POST['api_secret'],
                    'endpoint_token' => $_POST['endpoint_token'],
                    'area_domain' => $_POST['area_domain'],
                    'device_index_code' => $_POST['device_index_code'],
                    'area_ubicacion' => $_POST['area_ubicacion'],
                    'isapi_habilitado' => (isset($_POST['isapi_habilitado']) && $_POST['isapi_habilitado']) ? 1 : 0,
                    'isapi_url' => $_POST['isapi_url'] ?? '',
                    'isapi_usuario' => $_POST['isapi_usuario'] ?? '',
                    'isapi_password' => $_POST['isapi_password'] ?? '',
                    'verificar_ssl' => (isset($_POST['verificar_ssl']) && $_POST['verificar_ssl']) ? 1 : 0,
                    'habilitado' => (isset($_POST['habilitado']) && $_POST['habilitado']) ? 1 : 0
                ];
                
                if ($id) {
                    // Actualizar
                    $stmt = $db->prepare("UPDATE dispositivos_hikvision SET nombre=?, tipo_dispositivo=?, api_key=?, api_secret=?, endpoint_token=?, area_domain=?, device_index_code=?, area_ubicacion=?, isapi_habilitado=?, isapi_url=?, isapi_usuario=?, isapi_password=?, verificar_ssl=?, habilitado=? WHERE id=?");
                    $stmt->execute([
                        $data['nombre'], $data['tipo_dispositivo'], $data['api_key'],
                        $data['api_secret'], $data['endpoint_token'], $data['area_domain'],
                        $data['device_index_code'], $data['area_ubicacion'], $data['isapi_habilitado'],
                        $data['isapi_url'], $data['isapi_usuario'], $data['isapi_password'],
                        $data['verificar_ssl'], $data['habilitado'], $id
                    ]);
                } else {
                    // Insertar
                    $stmt = $db->prepare("INSERT INTO dispositivos_hikvision (nombre, tipo_dispositivo, api_key, api_secret, endpoint_token, area_domain, device_index_code, area_ubicacion, isapi_habilitado, isapi_url, isapi_usuario, isapi_password, verificar_ssl, habilitado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $data['nombre'], $data['tipo_dispositivo'], $data['api_key'],
                        $data['api_secret'], $data['endpoint_token'], $data['area_domain'],
                        $data['device_index_code'], $data['area_ubicacion'], $data['isapi_habilitado'],
                        $data['isapi_url'], $data['isapi_usuario'], $data['isapi_password'],
                        $data['verificar_ssl'], $data['habilitado']
                    ]);
                }
            }
            
            echo json_encode(['success' => true, 'message' => 'Dispositivo guardado exitosamente']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al guardar dispositivo: ' . $e->getMessage()]);
        }
    }
    
    public function obtenerDispositivo() {
        AuthController::check();
        
        if ($_SESSION['user_rol'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            return;
        }
        
        $db = Database::getInstance()->getConnection();
        $id = $_GET['id'] ?? null;
        $tipo = $_GET['tipo'] ?? '';
        
        if (!$id || !$tipo) {
            echo json_encode(['success' => false, 'message' => 'Parámetros inválidos']);
            return;
        }
        
        try {
            if ($tipo === 'shelly') {
                $stmt = $db->prepare("SELECT * FROM dispositivos_shelly WHERE id = ?");
            } elseif ($tipo === 'hikvision') {
                $stmt = $db->prepare("SELECT * FROM dispositivos_hikvision WHERE id = ?");
            } else {
                echo json_encode(['success' => false, 'message' => 'Tipo de dispositivo inválido']);
                return;
            }
            
            $stmt->execute([$id]);
            $dispositivo = $stmt->fetch();
            
            if ($dispositivo) {
                echo json_encode(['success' => true, 'dispositivo' => $dispositivo]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Dispositivo no encontrado']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al obtener dispositivo: ' . $e->getMessage()]);
        }
    }
    
    public function eliminarDispositivo() {
        AuthController::check();
        
        if ($_SESSION['user_rol'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        $db = Database::getInstance()->getConnection();
        $id = $_POST['id'] ?? null;
        $tipo = $_POST['tipo'] ?? '';
        
        if (!$id || !$tipo) {
            echo json_encode(['success' => false, 'message' => 'Parámetros inválidos']);
            return;
        }
        
        try {
            if ($tipo === 'shelly') {
                $stmt = $db->prepare("DELETE FROM dispositivos_shelly WHERE id = ?");
            } elseif ($tipo === 'hikvision') {
                $stmt = $db->prepare("DELETE FROM dispositivos_hikvision WHERE id = ?");
            } else {
                echo json_encode(['success' => false, 'message' => 'Tipo de dispositivo inválido']);
                return;
            }
            
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Dispositivo eliminado exitosamente']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar dispositivo: ' . $e->getMessage()]);
        }
    }
    
    public function testShellyChannel() {
        AuthController::check();
        
        if ($_SESSION['user_rol'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        $dispositivoId = $_POST['dispositivo_id'] ?? null;
        $canal = $_POST['canal'] ?? null;
        
        if ($dispositivoId === null || $canal === null) {
            echo json_encode(['success' => false, 'message' => 'Parámetros incompletos']);
            return;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Obtener configuración del dispositivo
            $stmt = $db->prepare("SELECT * FROM dispositivos_shelly WHERE id = ?");
            $stmt->execute([$dispositivoId]);
            $dispositivo = $stmt->fetch();
            
            if (!$dispositivo) {
                echo json_encode(['success' => false, 'message' => 'Dispositivo no encontrado']);
                return;
            }
            
            // Validar configuración
            if (empty($dispositivo['device_id']) || empty($dispositivo['token_autenticacion']) || empty($dispositivo['servidor_cloud'])) {
                echo json_encode(['success' => false, 'message' => 'Configuración del dispositivo incompleta']);
                return;
            }
            
            // Validar canal
            if ($canal < 0 || $canal > 3) {
                echo json_encode(['success' => false, 'message' => 'Canal inválido. Debe ser entre 0 y 3.']);
                return;
            }
            
            // Activar canal
            $url = rtrim($dispositivo['servidor_cloud'], '/') . '/device/relay/control';
            
            $data = [
                'id' => trim($dispositivo['device_id']),
                'auth_key' => trim($dispositivo['token_autenticacion']),
                'channel' => (int)$canal,
                'turn' => 'on'
            ];
            
            // Si tiene duración de pulso, usarla
            if (!empty($dispositivo['duracion_pulso']) && $dispositivo['duracion_pulso'] > 0) {
                $data['timer'] = $dispositivo['duracion_pulso'] / 1000; // ms a segundos
            }
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Seguir redirects HTTP 301/302
            curl_setopt($ch, CURLOPT_MAXREDIRS, 5); // Máximo 5 redirects
            // Solo deshabilitar SSL verification en desarrollo
            if (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE === true) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            }
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
            
            // Log para debugging
            error_log("Test Shelly Channel - Request: " . json_encode($data));
            error_log("Test Shelly Channel - Response (HTTP $httpCode): " . substr($response, 0, LOG_RESPONSE_MAX_LENGTH));
            
            if ($httpCode == 200) {
                $responseData = json_decode($response, true);
                
                // Verificar si la respuesta es JSON válido
                if (json_last_error() !== JSON_ERROR_NONE) {
                    // La respuesta no es JSON válido
                    error_log("Test Shelly Channel - Invalid JSON response: " . json_last_error_msg());
                    echo json_encode([
                        'success' => false, 
                        'message' => 'Respuesta inválida del dispositivo. Verifica la configuración del servidor cloud.'
                    ]);
                    return;
                }
                
                if (isset($responseData['isok']) && $responseData['isok'] === true) {
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Canal activado correctamente. El dispositivo respondió exitosamente.'
                    ]);
                } elseif (isset($responseData['errors'])) {
                    echo json_encode([
                        'success' => false, 
                        'message' => 'El dispositivo reportó un error: ' . json_encode($responseData['errors'])
                    ]);
                } else {
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Comando enviado correctamente'
                    ]);
                }
            } else {
                $errorMsg = !empty($curlError) ? $curlError : "HTTP {$httpCode}";
                
                // Intentar decodificar respuesta de error si existe
                if (!empty($response)) {
                    $responseData = json_decode($response, true);
                    if (json_last_error() === JSON_ERROR_NONE && isset($responseData['error'])) {
                        $errorMsg = sprintf("%s: %s", $errorMsg, $responseData['error']);
                    }
                }
                
                echo json_encode([
                    'success' => false, 
                    'message' => "Error en la respuesta del dispositivo: {$errorMsg}. Verifica la URL del servidor cloud."
                ]);
            }
            
        } catch (Exception $e) {
            error_log('Error en testShellyChannel: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error al probar canal: ' . $e->getMessage()]);
        }
    }
}
