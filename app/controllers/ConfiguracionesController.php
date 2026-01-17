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
            
            foreach ($configuraciones as $clave => $valor) {
                $stmt = $db->prepare("UPDATE configuraciones_globales SET valor = ? WHERE clave = ?");
                $stmt->execute([$valor, $clave]);
            }
            
            echo json_encode(['success' => true, 'message' => 'Configuraciones guardadas exitosamente']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al guardar configuraciones: ' . $e->getMessage()]);
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
                    'nombre' => $_POST['nombre'],
                    'device_id' => $_POST['device_id'],
                    'token_autenticacion' => $_POST['token_autenticacion'],
                    'servidor_cloud' => $_POST['servidor_cloud'],
                    'area' => $_POST['area'],
                    'canal_entrada' => $_POST['canal_entrada'] ?? 1,
                    'canal_salida' => $_POST['canal_salida'] ?? 0,
                    'duracion_pulso' => $_POST['duracion_pulso'] ?? 600,
                    'accion' => $_POST['accion'] ?? 'Abrir/Cerrar',
                    'habilitado' => isset($_POST['habilitado']) ? 1 : 0,
                    'invertido' => isset($_POST['invertido']) ? 1 : 0,
                    'simultaneo' => isset($_POST['simultaneo']) ? 1 : 0
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
                    'isapi_habilitado' => isset($_POST['isapi_habilitado']) ? 1 : 0,
                    'isapi_url' => $_POST['isapi_url'] ?? '',
                    'isapi_usuario' => $_POST['isapi_usuario'] ?? '',
                    'isapi_password' => $_POST['isapi_password'] ?? '',
                    'verificar_ssl' => isset($_POST['verificar_ssl']) ? 1 : 0,
                    'habilitado' => isset($_POST['habilitado']) ? 1 : 0
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
}
