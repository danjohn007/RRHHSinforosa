<?php
/**
 * Controlador de Sucursales
 */

class SucursalesController {
    
    public function index() {
        AuthController::check();
        
        $sucursalModel = new Sucursal();
        $sucursales = $sucursalModel->getAll();
        
        $data = [
            'title' => 'Gestión de Sucursales',
            'sucursales' => $sucursales
        ];
        
        ob_start();
        require_once BASE_PATH . 'app/views/sucursales/index.php';
        $content = ob_get_clean();
        
        require_once BASE_PATH . 'app/views/layouts/main.php';
    }
    
    public function crear() {
        AuthController::check();
        
        $db = Database::getInstance()->getConnection();
        $error = null;
        $success = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $codigo = $_POST['codigo'] ?? '';
            $direccion = $_POST['direccion'] ?? '';
            $telefono = $_POST['telefono'] ?? '';
            $urlPublica = $_POST['url_publica'] ?? '';
            $activo = isset($_POST['activo']) ? 1 : 0;
            
            if (empty($nombre) || empty($codigo)) {
                $error = 'El nombre y código son obligatorios';
            } else {
                // Validar URL única si se proporcionó
                if (!empty($urlPublica)) {
                    $stmt = $db->prepare("SELECT id FROM sucursales WHERE url_publica = ?");
                    $stmt->execute([$urlPublica]);
                    if ($stmt->fetch()) {
                        $error = 'La URL pública ya está en uso por otra sucursal';
                    }
                }
                
                if (!$error) {
                    $sucursalModel = new Sucursal();
                    $sucursalId = $sucursalModel->create([
                        'nombre' => $nombre,
                        'codigo' => $codigo,
                        'direccion' => $direccion,
                        'telefono' => $telefono,
                        'url_publica' => $urlPublica,
                        'activo' => $activo
                    ]);
                    
                    if ($sucursalId) {
                        $success = 'Sucursal creada exitosamente';
                        
                        // Redirigir después de 2 segundos
                        echo "<script>
                            setTimeout(function() {
                                window.location.href = '" . BASE_URL . "sucursales/editar?id=" . $sucursalId . "';
                            }, 2000);
                        </script>";
                    } else {
                        $error = 'Error al crear la sucursal';
                    }
                }
            }
        }
        
        $data = [
            'title' => 'Crear Sucursal',
            'error' => $error,
            'success' => $success
        ];
        
        ob_start();
        require_once BASE_PATH . 'app/views/sucursales/crear.php';
        $content = ob_get_clean();
        
        require_once BASE_PATH . 'app/views/layouts/main.php';
    }
    
    public function editar() {
        AuthController::check();
        
        $sucursalId = $_GET['id'] ?? null;
        
        if (!$sucursalId) {
            redirect('sucursales');
        }
        
        $sucursalModel = new Sucursal();
        $sucursal = $sucursalModel->getById($sucursalId);
        
        if (!$sucursal) {
            redirect('sucursales');
        }
        
        $error = null;
        $success = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $codigo = $_POST['codigo'] ?? '';
            $direccion = $_POST['direccion'] ?? '';
            $telefono = $_POST['telefono'] ?? '';
            $urlPublica = $_POST['url_publica'] ?? '';
            $activo = isset($_POST['activo']) ? 1 : 0;
            
            if (empty($nombre) || empty($codigo)) {
                $error = 'El nombre y código son obligatorios';
            } else {
                // Validar URL única si se proporcionó
                if (!empty($urlPublica)) {
                    $stmt = $db->prepare("SELECT id FROM sucursales WHERE url_publica = ? AND id != ?");
                    $stmt->execute([$urlPublica, $sucursalId]);
                    if ($stmt->fetch()) {
                        $error = 'La URL pública ya está en uso por otra sucursal';
                    }
                }
                
                if (!$error) {
                    $result = $sucursalModel->update($sucursalId, [
                        'nombre' => $nombre,
                        'codigo' => $codigo,
                        'direccion' => $direccion,
                        'telefono' => $telefono,
                        'url_publica' => $urlPublica,
                        'activo' => $activo
                    ]);
                    
                    if ($result) {
                        $success = 'Sucursal actualizada exitosamente';
                        $sucursal = $sucursalModel->getById($sucursalId);
                    } else {
                        $error = 'Error al actualizar la sucursal';
                    }
                }
            }
        }
        
        // Obtener gerentes, dispositivos y empleados de la sucursal
        $gerentes = $sucursalModel->getGerentes($sucursalId);
        $dispositivos = $sucursalModel->getDispositivos($sucursalId);
        $empleados = $sucursalModel->getEmpleados($sucursalId);
        
        // Obtener lista de empleados disponibles para ser gerentes
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("
            SELECT e.id, e.numero_empleado, e.codigo_empleado,
            CONCAT(e.nombres, ' ', e.apellido_paterno, ' ', IFNULL(e.apellido_materno, '')) as nombre_completo,
            e.puesto, u.rol
            FROM empleados e
            LEFT JOIN usuarios u ON e.usuario_id = u.id
            WHERE e.estatus = 'Activo'
            AND u.rol = 'gerente'
            ORDER BY e.nombres
        ");
        $empleadosDisponibles = $stmt->fetchAll();
        
        // Obtener dispositivos Shelly disponibles
        $stmt = $db->query("SELECT * FROM dispositivos_shelly WHERE habilitado = 1 ORDER BY nombre");
        $dispositivosDisponibles = $stmt->fetchAll();
        
        $data = [
            'title' => 'Editar Sucursal',
            'sucursal' => $sucursal,
            'gerentes' => $gerentes,
            'dispositivos' => $dispositivos,
            'empleados' => $empleados,
            'empleadosDisponibles' => $empleadosDisponibles,
            'dispositivosDisponibles' => $dispositivosDisponibles,
            'error' => $error,
            'success' => $success
        ];
        
        ob_start();
        require_once BASE_PATH . 'app/views/sucursales/editar.php';
        $content = ob_get_clean();
        
        require_once BASE_PATH . 'app/views/layouts/main.php';
    }
    
    public function eliminar() {
        AuthController::check();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        $sucursalId = $_POST['id'] ?? null;
        
        if (!$sucursalId) {
            echo json_encode(['success' => false, 'message' => 'ID de sucursal no proporcionado']);
            return;
        }
        
        $sucursalModel = new Sucursal();
        $result = $sucursalModel->delete($sucursalId);
        
        echo json_encode($result);
    }
    
    public function asignarGerente() {
        AuthController::check();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        // Read JSON body if content type is JSON, otherwise use POST
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $sucursalId = null;
        $empleadoId = null;
        
        if (strpos($contentType, 'application/json') !== false) {
            $input = json_decode(file_get_contents('php://input'), true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($input)) {
                $sucursalId = $input['sucursal_id'] ?? null;
                $empleadoId = $input['empleado_id'] ?? null;
            }
        }
        
        // Fallback to POST if JSON not provided or invalid
        if ($sucursalId === null) {
            $sucursalId = $_POST['sucursal_id'] ?? null;
        }
        if ($empleadoId === null) {
            $empleadoId = $_POST['empleado_id'] ?? null;
        }
        
        if (!$sucursalId || !$empleadoId) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }
        
        $sucursalModel = new Sucursal();
        $result = $sucursalModel->asignarGerente($sucursalId, $empleadoId);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Gerente asignado exitosamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al asignar gerente']);
        }
    }
    
    public function removerGerente() {
        AuthController::check();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        // Read JSON body if content type is JSON, otherwise use POST
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $sucursalId = null;
        $empleadoId = null;
        
        if (strpos($contentType, 'application/json') !== false) {
            $input = json_decode(file_get_contents('php://input'), true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($input)) {
                $sucursalId = $input['sucursal_id'] ?? null;
                $empleadoId = $input['empleado_id'] ?? null;
            }
        }
        
        // Fallback to POST if JSON not provided or invalid
        if ($sucursalId === null) {
            $sucursalId = $_POST['sucursal_id'] ?? null;
        }
        if ($empleadoId === null) {
            $empleadoId = $_POST['empleado_id'] ?? null;
        }
        
        if (!$sucursalId || !$empleadoId) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }
        
        $sucursalModel = new Sucursal();
        $result = $sucursalModel->removerGerente($sucursalId, $empleadoId);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Gerente removido exitosamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al remover gerente']);
        }
    }
    
    public function asignarDispositivo() {
        AuthController::check();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        // Read JSON body if content type is JSON, otherwise use POST
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $sucursalId = null;
        $dispositivoId = null;
        $tipoAccion = 'Ambos';
        
        if (strpos($contentType, 'application/json') !== false) {
            $input = json_decode(file_get_contents('php://input'), true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($input)) {
                $sucursalId = $input['sucursal_id'] ?? null;
                $dispositivoId = $input['dispositivo_id'] ?? null;
                $tipoAccion = $input['tipo_accion'] ?? 'Ambos';
            }
        }
        
        // Fallback to POST if JSON not provided or invalid
        if ($sucursalId === null) {
            $sucursalId = $_POST['sucursal_id'] ?? null;
        }
        if ($dispositivoId === null) {
            $dispositivoId = $_POST['dispositivo_id'] ?? null;
        }
        if ($tipoAccion === 'Ambos') {
            $tipoAccion = $_POST['tipo_accion'] ?? 'Ambos';
        }
        
        if (!$sucursalId || !$dispositivoId) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }
        
        $sucursalModel = new Sucursal();
        $result = $sucursalModel->asignarDispositivo($sucursalId, $dispositivoId, $tipoAccion);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Dispositivo asignado exitosamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al asignar dispositivo']);
        }
    }
    
    public function removerDispositivo() {
        AuthController::check();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        // Read JSON body if content type is JSON, otherwise use POST
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $sucursalId = null;
        $dispositivoId = null;
        
        if (strpos($contentType, 'application/json') !== false) {
            $input = json_decode(file_get_contents('php://input'), true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($input)) {
                $sucursalId = $input['sucursal_id'] ?? null;
                $dispositivoId = $input['dispositivo_id'] ?? null;
            }
        }
        
        // Fallback to POST if JSON not provided or invalid
        if ($sucursalId === null) {
            $sucursalId = $_POST['sucursal_id'] ?? null;
        }
        if ($dispositivoId === null) {
            $dispositivoId = $_POST['dispositivo_id'] ?? null;
        }
        
        if (!$sucursalId || !$dispositivoId) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }
        
        $sucursalModel = new Sucursal();
        $result = $sucursalModel->removerDispositivo($sucursalId, $dispositivoId);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Dispositivo removido exitosamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al remover dispositivo']);
        }
    }
}
