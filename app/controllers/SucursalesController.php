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
            
            // Horarios
            $horarioTodaSemana = isset($_POST['horario_toda_semana']) ? 1 : 0;
            $horaEntradaGeneral = $_POST['hora_entrada_general'] ?? '08:00';
            $horaSalidaGeneral = $_POST['hora_salida_general'] ?? '18:00';
            
            // Horarios por día - convertir cadenas vacías a NULL para días cerrados
            $horaEntradaLunes = !empty($_POST['hora_entrada_lunes']) ? $_POST['hora_entrada_lunes'] : null;
            $horaSalidaLunes = !empty($_POST['hora_salida_lunes']) ? $_POST['hora_salida_lunes'] : null;
            $horaEntradaMartes = !empty($_POST['hora_entrada_martes']) ? $_POST['hora_entrada_martes'] : null;
            $horaSalidaMartes = !empty($_POST['hora_salida_martes']) ? $_POST['hora_salida_martes'] : null;
            $horaEntradaMiercoles = !empty($_POST['hora_entrada_miercoles']) ? $_POST['hora_entrada_miercoles'] : null;
            $horaSalidaMiercoles = !empty($_POST['hora_salida_miercoles']) ? $_POST['hora_salida_miercoles'] : null;
            $horaEntradaJueves = !empty($_POST['hora_entrada_jueves']) ? $_POST['hora_entrada_jueves'] : null;
            $horaSalidaJueves = !empty($_POST['hora_salida_jueves']) ? $_POST['hora_salida_jueves'] : null;
            $horaEntradaViernes = !empty($_POST['hora_entrada_viernes']) ? $_POST['hora_entrada_viernes'] : null;
            $horaSalidaViernes = !empty($_POST['hora_salida_viernes']) ? $_POST['hora_salida_viernes'] : null;
            $horaEntradaSabado = !empty($_POST['hora_entrada_sabado']) ? $_POST['hora_entrada_sabado'] : null;
            $horaSalidaSabado = !empty($_POST['hora_salida_sabado']) ? $_POST['hora_salida_sabado'] : null;
            $horaEntradaDomingo = !empty($_POST['hora_entrada_domingo']) ? $_POST['hora_entrada_domingo'] : null;
            $horaSalidaDomingo = !empty($_POST['hora_salida_domingo']) ? $_POST['hora_salida_domingo'] : null;
            
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
                        'activo' => $activo,
                        'horario_toda_semana' => $horarioTodaSemana,
                        'hora_entrada_general' => $horaEntradaGeneral,
                        'hora_salida_general' => $horaSalidaGeneral,
                        'hora_entrada_lunes' => $horaEntradaLunes,
                        'hora_salida_lunes' => $horaSalidaLunes,
                        'hora_entrada_martes' => $horaEntradaMartes,
                        'hora_salida_martes' => $horaSalidaMartes,
                        'hora_entrada_miercoles' => $horaEntradaMiercoles,
                        'hora_salida_miercoles' => $horaSalidaMiercoles,
                        'hora_entrada_jueves' => $horaEntradaJueves,
                        'hora_salida_jueves' => $horaSalidaJueves,
                        'hora_entrada_viernes' => $horaEntradaViernes,
                        'hora_salida_viernes' => $horaSalidaViernes,
                        'hora_entrada_sabado' => $horaEntradaSabado,
                        'hora_salida_sabado' => $horaSalidaSabado,
                        'hora_entrada_domingo' => $horaEntradaDomingo,
                        'hora_salida_domingo' => $horaSalidaDomingo
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
        
        // Obtener gerentes, dispositivos, áreas de trabajo y empleados de la sucursal
        $gerentes = $sucursalModel->getGerentes($sucursalId);
        $dispositivos = $sucursalModel->getDispositivos($sucursalId);
        $areasTrabajo = $sucursalModel->getAreasTrabajo($sucursalId);
        $empleados = $sucursalModel->getEmpleados($sucursalId);
        
        // Obtener lista de empleados disponibles para ser gerentes
        // Solo incluye empleados con puesto 'Gerente General'
        $stmt = $db->prepare("
            SELECT e.id, e.numero_empleado, e.codigo_empleado,
            CONCAT(e.nombres, ' ', e.apellido_paterno, ' ', IFNULL(e.apellido_materno, '')) as nombre_completo,
            e.puesto, u.rol,
            CASE 
                WHEN sg.empleado_id IS NOT NULL THEN 1
                ELSE 0
            END as ya_asignado
            FROM empleados e
            LEFT JOIN usuarios u ON e.usuario_id = u.id
            LEFT JOIN sucursal_gerentes sg ON sg.empleado_id = e.id AND sg.sucursal_id = ? AND sg.activo = 1
            WHERE e.estatus = 'Activo'
            AND e.puesto = 'Gerente General'
            ORDER BY ya_asignado ASC, e.nombres, e.apellido_paterno
        ");
        $stmt->execute([$sucursalId]);
        $empleadosDisponibles = $stmt->fetchAll();
        
        // Obtener dispositivos Shelly disponibles
        $stmt = $db->query("SELECT * FROM dispositivos_shelly WHERE habilitado = 1 ORDER BY nombre");
        $dispositivosDisponibles = $stmt->fetchAll();
        
        $data = [
            'title' => 'Editar Sucursal',
            'sucursal' => $sucursal,
            'gerentes' => $gerentes,
            'dispositivos' => $dispositivos,
            'areasTrabajo' => $areasTrabajo,
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
        $tipoAccion = null;
        
        if (strpos($contentType, 'application/json') !== false) {
            $input = json_decode(file_get_contents('php://input'), true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($input)) {
                $sucursalId = $input['sucursal_id'] ?? null;
                $dispositivoId = $input['dispositivo_id'] ?? null;
                $tipoAccion = $input['tipo_accion'] ?? null;
            }
        }
        
        // Fallback to POST if JSON not provided or invalid
        if ($sucursalId === null) {
            $sucursalId = $_POST['sucursal_id'] ?? null;
        }
        if ($dispositivoId === null) {
            $dispositivoId = $_POST['dispositivo_id'] ?? null;
        }
        if ($tipoAccion === null) {
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
    
    public function guardarAreaTrabajo() {
        AuthController::check();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $input = null;
        
        if (strpos($contentType, 'application/json') !== false) {
            $input = json_decode(file_get_contents('php://input'), true);
        } else {
            $input = $_POST;
        }
        
        $areaId = $input['area_id'] ?? null;
        $sucursalId = $input['sucursal_id'] ?? null;
        $nombre = $input['nombre'] ?? '';
        $descripcion = $input['descripcion'] ?? '';
        $dispositivoId = $input['dispositivo_shelly_id'] ?? null;
        $canalAsignado = $input['canal_asignado'] ?? 0;
        $activo = !empty($input['activo']) && ($input['activo'] === 1 || $input['activo'] === '1' || $input['activo'] === true) ? 1 : 0;
        
        if (!$sucursalId || empty($nombre)) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }
        
        $sucursalModel = new Sucursal();
        $datos = [
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'dispositivo_shelly_id' => $dispositivoId,
            'canal_asignado' => $canalAsignado,
            'activo' => $activo
        ];
        
        if ($areaId) {
            // Actualizar área existente
            $result = $sucursalModel->actualizarAreaTrabajo($areaId, $datos);
            $message = 'Área actualizada exitosamente';
        } else {
            // Crear nueva área
            $result = $sucursalModel->crearAreaTrabajo($sucursalId, $datos);
            $message = 'Área creada exitosamente';
        }
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => $message]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al guardar área']);
        }
    }
    
    public function eliminarAreaTrabajo() {
        AuthController::check();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $areaId = null;
        
        if (strpos($contentType, 'application/json') !== false) {
            $input = json_decode(file_get_contents('php://input'), true);
            $areaId = $input['area_id'] ?? null;
        } else {
            $areaId = $_POST['area_id'] ?? null;
        }
        
        if (!$areaId) {
            echo json_encode(['success' => false, 'message' => 'ID de área no proporcionado']);
            return;
        }
        
        $sucursalModel = new Sucursal();
        $result = $sucursalModel->eliminarAreaTrabajo($areaId);
        
        echo json_encode($result);
    }
}
