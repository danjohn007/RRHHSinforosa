<?php
/**
 * Controlador de Asistencia
 */

class AsistenciaController {
    
    public function index() {
        AuthController::check();
        
        $db = Database::getInstance()->getConnection();
        
        // Obtener filtros desde la URL
        $fechaInicio = $_GET['fecha_inicio'] ?? date('Y-m-d');
        $fechaFin = $_GET['fecha_fin'] ?? date('Y-m-d');
        $busqueda = $_GET['busqueda'] ?? '';
        $estatusFiltro = $_GET['estatus'] ?? ''; // Nuevo filtro de estatus
        
        // Construir query con filtros usando la vista completa
        $query = "
            SELECT * FROM vista_asistencias_completa
            WHERE fecha BETWEEN ? AND ?
        ";
        
        $params = [$fechaInicio, $fechaFin];
        
        // Aplicar búsqueda si existe
        if ($busqueda) {
            $query .= " AND (
                empleado_nombre LIKE ? OR 
                numero_empleado LIKE ? OR 
                codigo_empleado LIKE ? OR
                departamento LIKE ?
            )";
            $busquedaParam = "%$busqueda%";
            $params[] = $busquedaParam;
            $params[] = $busquedaParam;
            $params[] = $busquedaParam;
            $params[] = $busquedaParam;
        }
        
        // Aplicar filtro de estatus si existe
        if ($estatusFiltro) {
            $query .= " AND estatus = ?";
            $params[] = $estatusFiltro;
        }
        
        $query .= " ORDER BY fecha DESC, hora_entrada DESC LIMIT 500";
        
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $asistencias = $stmt->fetchAll();
        
        $data = [
            'title' => 'Control de Asistencia',
            'asistencias' => $asistencias,
            'filtros' => [
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'busqueda' => $busqueda,
                'estatus' => $estatusFiltro
            ]
        ];
        
        extract($data);
        
        ob_start();
        require_once BASE_PATH . 'app/views/asistencia/index.php';
        $content = ob_get_clean();
        
        require_once BASE_PATH . 'app/views/layouts/main.php';
    }
    
    public function registro() {
        AuthController::check();
        
        $db = Database::getInstance()->getConnection();
        
        // Obtener lista de empleados activos
        $stmt = $db->query("
            SELECT id, numero_empleado, 
                   CONCAT(nombres, ' ', apellido_paterno, ' ', COALESCE(apellido_materno, '')) as nombre_completo,
                   departamento
            FROM empleados 
            WHERE estatus = 'Activo'
            ORDER BY numero_empleado
        ");
        $empleados = $stmt->fetchAll();
        
        $data = [
            'title' => 'Registro de Asistencia',
            'empleados' => $empleados
        ];
        
        extract($data);
        
        ob_start();
        require_once BASE_PATH . 'app/views/asistencia/registro.php';
        $content = ob_get_clean();
        
        require_once BASE_PATH . 'app/views/layouts/main.php';
    }
    
    public function guardarRegistro() {
        AuthController::check();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('asistencia/registro');
        }
        
        $empleadoId = $_POST['empleado_id'] ?? null;
        $fecha = $_POST['fecha'] ?? null;
        $horaEntrada = $_POST['hora_entrada'] ?? null;
        $horaSalida = $_POST['hora_salida'] ?? null;
        $notas = $_POST['notas'] ?? null;
        
        $db = Database::getInstance()->getConnection();
        $success = null;
        $error = null;
        
        if (!$empleadoId || !$fecha || !$horaEntrada) {
            $error = 'Empleado, fecha y hora de entrada son obligatorios';
        } else {
            try {
                // Verificar si ya existe un registro para este empleado en esta fecha
                $stmt = $db->prepare("
                    SELECT id FROM asistencias 
                    WHERE empleado_id = ? AND fecha = ?
                ");
                $stmt->execute([$empleadoId, $fecha]);
                $existe = $stmt->fetch();
                
                if ($existe) {
                    $error = 'Ya existe un registro de asistencia para este empleado en esta fecha';
                } else {
                    // Construir datetime completo para hora_entrada
                    $horaEntradaCompleta = $fecha . ' ' . $horaEntrada . ':00';
                    $horaSalidaCompleta = null;
                    $horasTrabajadas = null;
                    
                    if ($horaSalida) {
                        $horaSalidaCompleta = $fecha . ' ' . $horaSalida . ':00';
                        
                        // Calcular horas trabajadas (truncadas)
                        $entrada = new DateTime($horaEntradaCompleta);
                        $salida = new DateTime($horaSalidaCompleta);
                        $diferencia = $entrada->diff($salida);
                        
                        // Obtener solo las horas completas (truncar, no redondear)
                        $horasTrabajadas = $diferencia->h + ($diferencia->days * 24);
                    }
                    
                    // Insertar registro
                    $stmt = $db->prepare("
                        INSERT INTO asistencias 
                        (empleado_id, fecha, hora_entrada, hora_salida, horas_trabajadas, notas, estatus)
                        VALUES (?, ?, ?, ?, ?, ?, 'Presente')
                    ");
                    $stmt->execute([
                        $empleadoId, 
                        $fecha, 
                        $horaEntradaCompleta, 
                        $horaSalidaCompleta,
                        $horasTrabajadas,
                        $notas
                    ]);
                    
                    $success = 'Registro de asistencia guardado exitosamente';
                }
            } catch (Exception $e) {
                $error = 'Error al guardar el registro: ' . $e->getMessage();
            }
        }
        
        // Recargar la vista con mensajes
        $stmt = $db->query("
            SELECT id, numero_empleado, 
                   CONCAT(nombres, ' ', apellido_paterno, ' ', COALESCE(apellido_materno, '')) as nombre_completo,
                   departamento
            FROM empleados 
            WHERE estatus = 'Activo'
            ORDER BY numero_empleado
        ");
        $empleados = $stmt->fetchAll();
        
        $data = [
            'title' => 'Registro de Asistencia',
            'empleados' => $empleados
        ];
        
        // Solo agregar las variables si tienen contenido
        if ($success) {
            $data['success'] = $success;
        }
        if ($error) {
            $data['error'] = $error;
        }
        
        extract($data);
        
        ob_start();
        require_once BASE_PATH . 'app/views/asistencia/registro.php';
        $content = ob_get_clean();
        
        require_once BASE_PATH . 'app/views/layouts/main.php';
    }
    
    public function vacaciones() {
        AuthController::check();
        
        $db = Database::getInstance()->getConnection();
        
        // Obtener solicitudes de vacaciones
        $stmt = $db->query("
            SELECT sv.*, 
                   CONCAT(e.nombres, ' ', e.apellido_paterno) as nombre_empleado,
                   e.departamento
            FROM solicitudes_vacaciones sv
            INNER JOIN empleados e ON sv.empleado_id = e.id
            ORDER BY sv.fecha_solicitud DESC
            LIMIT 20
        ");
        $solicitudes = $stmt->fetchAll();
        
        // Obtener empleados activos para el formulario
        $stmt = $db->query("
            SELECT id, CONCAT(nombres, ' ', apellido_paterno, ' ', apellido_materno) as nombre_completo
            FROM empleados 
            WHERE estatus = 'Activo'
            ORDER BY nombres, apellido_paterno
        ");
        $empleados = $stmt->fetchAll();
        
        $data = [
            'title' => 'Gestión de Vacaciones',
            'solicitudes' => $solicitudes,
            'empleados' => $empleados
        ];
        
        extract($data);
        
        ob_start();
        require_once BASE_PATH . 'app/views/asistencia/vacaciones.php';
        $content = ob_get_clean();
        
        require_once BASE_PATH . 'app/views/layouts/main.php';
    }
    
    public function turnos() {
        AuthController::check();
        
        $db = Database::getInstance()->getConnection();
        
        // Obtener turnos
        $stmt = $db->query("SELECT * FROM turnos WHERE activo = 1 ORDER BY nombre");
        $turnos = $stmt->fetchAll();
        
        $data = [
            'title' => 'Gestión de Turnos',
            'turnos' => $turnos
        ];
        
        extract($data);
        
        ob_start();
        require_once BASE_PATH . 'app/views/asistencia/turnos.php';
        $content = ob_get_clean();
        
        require_once BASE_PATH . 'app/views/layouts/main.php';
    }
    
    public function incidencias() {
        AuthController::checkRole(['admin']);
        
        $db = Database::getInstance()->getConnection();
        
        // Obtener filtros
        $tipo = $_GET['tipo'] ?? '';
        $estatus = $_GET['estatus'] ?? '';
        $fechaInicio = $_GET['fecha_inicio'] ?? '';
        $fechaFin = $_GET['fecha_fin'] ?? '';
        $busqueda = $_GET['busqueda'] ?? '';
        
        // Construir query con filtros
        $query = "
            SELECT i.*, 
                   CONCAT(e.nombres, ' ', e.apellido_paterno, ' ', COALESCE(e.apellido_materno, '')) as nombre_empleado,
                   e.numero_empleado,
                   e.departamento,
                   u.nombre as nombre_usuario_registro
            FROM incidencias_nomina i
            INNER JOIN empleados e ON i.empleado_id = e.id
            LEFT JOIN usuarios u ON i.usuario_registro_id = u.id
            WHERE 1=1
        ";
        
        $params = [];
        
        if ($tipo) {
            $query .= " AND i.tipo_incidencia = ?";
            $params[] = $tipo;
        }
        
        if ($estatus) {
            $query .= " AND i.estatus = ?";
            $params[] = $estatus;
        }
        
        if ($fechaInicio) {
            $query .= " AND i.fecha_incidencia >= ?";
            $params[] = $fechaInicio;
        }
        
        if ($fechaFin) {
            $query .= " AND i.fecha_incidencia <= ?";
            $params[] = $fechaFin;
        }
        
        if ($busqueda) {
            $query .= " AND (e.nombres LIKE ? OR e.apellido_paterno LIKE ? OR e.numero_empleado LIKE ?)";
            $busquedaParam = "%$busqueda%";
            $params[] = $busquedaParam;
            $params[] = $busquedaParam;
            $params[] = $busquedaParam;
        }
        
        $query .= " ORDER BY i.fecha_incidencia DESC, i.fecha_creacion DESC LIMIT 100";
        
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $incidencias = $stmt->fetchAll();
        
        // Obtener empleados activos para el formulario
        $stmt = $db->query("
            SELECT id, numero_empleado, 
                   CONCAT(nombres, ' ', apellido_paterno, ' ', COALESCE(apellido_materno, '')) as nombre_completo
            FROM empleados 
            WHERE estatus = 'Activo'
            ORDER BY numero_empleado
        ");
        $empleados = $stmt->fetchAll();
        
        $data = [
            'title' => 'Gestión de Incidencias',
            'incidencias' => $incidencias,
            'empleados' => $empleados,
            'filtros' => [
                'tipo' => $tipo,
                'estatus' => $estatus,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'busqueda' => $busqueda
            ]
        ];
        
        extract($data);
        
        ob_start();
        require_once BASE_PATH . 'app/views/asistencia/incidencias.php';
        $content = ob_get_clean();
        
        require_once BASE_PATH . 'app/views/layouts/main.php';
    }
    
    public function obtenerIncidencia() {
        AuthController::checkRole(['admin']);
        header('Content-Type: application/json');
        
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
            exit;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("
                SELECT i.*, 
                       CONCAT(e.nombres, ' ', e.apellido_paterno, ' ', COALESCE(e.apellido_materno, '')) as nombre_empleado,
                       e.numero_empleado
                FROM incidencias_nomina i
                INNER JOIN empleados e ON i.empleado_id = e.id
                WHERE i.id = ?
            ");
            $stmt->execute([$id]);
            $incidencia = $stmt->fetch();
            
            if (!$incidencia) {
                echo json_encode(['success' => false, 'message' => 'Incidencia no encontrada']);
            } else {
                echo json_encode(['success' => true, 'incidencia' => $incidencia]);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    public function guardarIncidencia() {
        AuthController::checkRole(['admin']);
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        $id = $data['id'] ?? null;
        $empleadoId = $data['empleado_id'] ?? null;
        $tipoIncidencia = $data['tipo_incidencia'] ?? null;
        $fechaIncidencia = $data['fecha_incidencia'] ?? null;
        $cantidad = $data['cantidad'] ?? 1;
        $monto = $data['monto'] ?? 0;
        $descripcion = $data['descripcion'] ?? null;
        
        if (!$empleadoId || !$tipoIncidencia || !$fechaIncidencia) {
            echo json_encode(['success' => false, 'message' => 'Empleado, tipo y fecha son obligatorios']);
            exit;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            
            if ($id) {
                // Actualizar incidencia existente
                $stmt = $db->prepare("
                    UPDATE incidencias_nomina 
                    SET empleado_id = ?, tipo_incidencia = ?, fecha_incidencia = ?, 
                        cantidad = ?, monto = ?, descripcion = ?
                    WHERE id = ? AND estatus = 'Pendiente'
                ");
                $stmt->execute([$empleadoId, $tipoIncidencia, $fechaIncidencia, $cantidad, $monto, $descripcion, $id]);
                
                if ($stmt->rowCount() > 0) {
                    echo json_encode(['success' => true, 'message' => 'Incidencia actualizada exitosamente']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'No se pudo actualizar. La incidencia puede estar procesada.']);
                }
            } else {
                // Insertar nueva incidencia
                $stmt = $db->prepare("
                    INSERT INTO incidencias_nomina 
                    (empleado_id, tipo_incidencia, fecha_incidencia, cantidad, monto, descripcion, usuario_registro_id)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $empleadoId, 
                    $tipoIncidencia, 
                    $fechaIncidencia, 
                    $cantidad, 
                    $monto, 
                    $descripcion,
                    $_SESSION['user_id']
                ]);
                echo json_encode(['success' => true, 'message' => 'Incidencia creada exitosamente']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    public function eliminarIncidencia() {
        AuthController::checkRole(['admin']);
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;
        
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
            exit;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Verificar que la incidencia esté pendiente
            $stmt = $db->prepare("SELECT estatus FROM incidencias_nomina WHERE id = ?");
            $stmt->execute([$id]);
            $incidencia = $stmt->fetch();
            
            if (!$incidencia) {
                echo json_encode(['success' => false, 'message' => 'Incidencia no encontrada']);
                exit;
            }
            
            if ($incidencia['estatus'] !== 'Pendiente') {
                echo json_encode(['success' => false, 'message' => 'Solo se pueden eliminar incidencias pendientes']);
                exit;
            }
            
            $stmt = $db->prepare("DELETE FROM incidencias_nomina WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode(['success' => true, 'message' => 'Incidencia eliminada exitosamente']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    public function marcarRevisado() {
        AuthController::checkRole(['admin']);
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;
        
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
            exit;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Verificar que la incidencia exista y esté pendiente
            $stmt = $db->prepare("SELECT estatus FROM incidencias_nomina WHERE id = ?");
            $stmt->execute([$id]);
            $incidencia = $stmt->fetch();
            
            if (!$incidencia) {
                echo json_encode(['success' => false, 'message' => 'Incidencia no encontrada']);
                exit;
            }
            
            if ($incidencia['estatus'] !== 'Pendiente') {
                echo json_encode(['success' => false, 'message' => 'Solo se pueden marcar como revisadas las incidencias pendientes']);
                exit;
            }
            
            // Actualizar estatus a Revisado
            $stmt = $db->prepare("UPDATE incidencias_nomina SET estatus = 'Revisado' WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode(['success' => true, 'message' => 'Incidencia marcada como Revisada exitosamente']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    public function obtenerVacacion() {
        AuthController::check();
        header('Content-Type: application/json');
        
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
            exit;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            
            $stmt = $db->prepare("
                SELECT sv.*, 
                       CONCAT(e.nombres, ' ', e.apellido_paterno, ' ', e.apellido_materno) as nombre_empleado,
                       e.departamento
                FROM solicitudes_vacaciones sv
                INNER JOIN empleados e ON sv.empleado_id = e.id
                WHERE sv.id = ?
            ");
            $stmt->execute([$id]);
            $solicitud = $stmt->fetch();
            
            if (!$solicitud) {
                echo json_encode(['success' => false, 'message' => 'Solicitud no encontrada']);
            } else {
                echo json_encode(['success' => true, 'solicitud' => $solicitud]);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    public function guardarVacacion() {
        AuthController::checkRole(['admin', 'rrhh']);
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        $empleadoId = $data['empleado_id'] ?? null;
        $fechaInicio = $data['fecha_inicio'] ?? null;
        $fechaFin = $data['fecha_fin'] ?? null;
        $diasSolicitados = $data['dias_solicitados'] ?? null;
        $motivo = $data['motivo'] ?? null;
        
        if (!$empleadoId || !$fechaInicio || !$fechaFin || !$diasSolicitados) {
            echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
            exit;
        }
        
        // Validar que la fecha fin sea posterior a la fecha inicio
        if (strtotime($fechaFin) < strtotime($fechaInicio)) {
            echo json_encode(['success' => false, 'message' => 'La fecha fin debe ser posterior a la fecha inicio']);
            exit;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Verificar que el empleado existe
            $stmt = $db->prepare("SELECT id FROM empleados WHERE id = ? AND estatus = 'Activo'");
            $stmt->execute([$empleadoId]);
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Empleado no encontrado o inactivo']);
                exit;
            }
            
            // Insertar solicitud
            $stmt = $db->prepare("
                INSERT INTO solicitudes_vacaciones 
                (empleado_id, fecha_inicio, fecha_fin, dias_solicitados, motivo, estatus, fecha_solicitud)
                VALUES (?, ?, ?, ?, ?, 'Pendiente', NOW())
            ");
            
            if ($stmt->execute([$empleadoId, $fechaInicio, $fechaFin, $diasSolicitados, $motivo])) {
                echo json_encode(['success' => true, 'message' => 'Solicitud de vacaciones creada exitosamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al guardar la solicitud']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    public function exportar() {
        AuthController::check();
        
        $db = Database::getInstance()->getConnection();
        
        // Obtener filtros desde la URL
        $fechaInicio = $_GET['fecha_inicio'] ?? date('Y-m-d');
        $fechaFin = $_GET['fecha_fin'] ?? date('Y-m-d');
        $busqueda = $_GET['busqueda'] ?? '';
        
        // Construir query con filtros
        $query = "
            SELECT a.*, 
                   e.numero_empleado,
                   CONCAT(e.nombres, ' ', e.apellido_paterno, ' ', COALESCE(e.apellido_materno, '')) as nombre_empleado,
                   e.email_personal,
                   e.telefono,
                   e.departamento
            FROM asistencias a
            INNER JOIN empleados e ON a.empleado_id = e.id
            WHERE a.fecha BETWEEN ? AND ?
        ";
        
        $params = [$fechaInicio, $fechaFin];
        
        // Aplicar búsqueda si existe
        if ($busqueda) {
            $query .= " AND (
                e.nombres LIKE ? OR 
                e.apellido_paterno LIKE ? OR 
                e.apellido_materno LIKE ? OR
                e.numero_empleado LIKE ? OR 
                e.email_personal LIKE ? OR 
                e.telefono LIKE ?
            )";
            $busquedaParam = "%$busqueda%";
            $params[] = $busquedaParam;
            $params[] = $busquedaParam;
            $params[] = $busquedaParam;
            $params[] = $busquedaParam;
            $params[] = $busquedaParam;
            $params[] = $busquedaParam;
        }
        
        $query .= " ORDER BY a.fecha DESC, a.hora_entrada DESC";
        
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $asistencias = $stmt->fetchAll();
        
        // Generar reporte CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="reporte_asistencias_' . $fechaInicio . '_' . $fechaFin . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // BOM para UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Encabezados
        fputcsv($output, [
            'Fecha',
            'No. Empleado',
            'Nombre',
            'Email',
            'Teléfono',
            'Departamento',
            'Hora Entrada',
            'Hora Salida',
            'Horas Trabajadas',
            'Horas Extras',
            'Estatus',
            'Notas'
        ]);
        
        // Datos
        foreach ($asistencias as $asistencia) {
            fputcsv($output, [
                $asistencia['fecha'],
                $asistencia['numero_empleado'] ?? '',
                $asistencia['nombre_empleado'],
                $asistencia['email_personal'] ?? '',
                $asistencia['telefono'] ?? '',
                $asistencia['departamento'] ?? '',
                $asistencia['hora_entrada'] ? date('H:i', strtotime($asistencia['hora_entrada'])) : '',
                $asistencia['hora_salida'] ? date('H:i', strtotime($asistencia['hora_salida'])) : '',
                $asistencia['horas_trabajadas'] ?? '0',
                $asistencia['horas_extra'] ?? '0',
                $asistencia['estatus'],
                $asistencia['notas'] ?? ''
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Validar una asistencia (cambiar de "Por Validar" a "Validado")
     */
    public function validar() {
        AuthController::check();
        header('Content-Type: application/json');
        
        $db = Database::getInstance()->getConnection();
        
        // Solo permitir POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }
        
        $asistenciaId = $_POST['asistencia_id'] ?? null;
        $horaSalidaReal = $_POST['hora_salida_real'] ?? null;
        
        if (!$asistenciaId || !$horaSalidaReal) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            exit;
        }
        
        try {
            // Obtener la asistencia
            $stmt = $db->prepare("SELECT * FROM asistencias WHERE id = ?");
            $stmt->execute([$asistenciaId]);
            $asistencia = $stmt->fetch();
            
            if (!$asistencia) {
                echo json_encode(['success' => false, 'message' => 'Asistencia no encontrada']);
                exit;
            }
            
            // Calcular horas trabajadas con la hora real
            $horaEntrada = strtotime($asistencia['hora_entrada']);
            $horaSalidaRealTimestamp = strtotime($asistencia['fecha'] . ' ' . $horaSalidaReal);
            $horasTrabajadas = ($horaSalidaRealTimestamp - $horaEntrada) / 3600;
            $horasExtra = max(0, $horasTrabajadas - 8);
            
            // Actualizar asistencia
            $stmt = $db->prepare("
                UPDATE asistencias 
                SET hora_salida_real = ?,
                    hora_salida = ?,
                    horas_trabajadas = ?,
                    horas_extra = ?,
                    estatus = 'Validado',
                    validado_por_id = ?,
                    fecha_validacion = NOW()
                WHERE id = ?
            ");
            
            $horaSalidaRealCompleta = $asistencia['fecha'] . ' ' . $horaSalidaReal;
            $result = $stmt->execute([
                $horaSalidaRealCompleta,
                $horaSalidaRealCompleta,
                $horasTrabajadas,
                $horasExtra,
                $_SESSION['user_id'] ?? null,
                $asistenciaId
            ]);
            
            if ($result) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Asistencia validada correctamente',
                    'horas_trabajadas' => round($horasTrabajadas, 2),
                    'horas_extra' => round($horasExtra, 2)
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al validar asistencia']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
}
