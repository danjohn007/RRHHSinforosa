<?php
/**
 * API REST para operaciones AJAX
 * Maneja todas las peticiones asíncronas del sistema
 */

// Configuración
require_once '../config/config.php';

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'No autenticado']);
    exit;
}

// Headers para JSON
header('Content-Type: application/json');

// Obtener método y acción
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    $db = Database::getInstance()->getConnection();
    
    switch ($action) {
        
        // ===== INCIDENCIAS =====
        case 'crear_incidencia':
            if ($method !== 'POST') {
                throw new Exception('Método no permitido');
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            $stmt = $db->prepare("
                INSERT INTO incidencias_nomina (
                    empleado_id, tipo_incidencia, fecha_incidencia, 
                    cantidad, monto, descripcion, usuario_registro_id, estatus
                ) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pendiente')
            ");
            
            $result = $stmt->execute([
                $data['empleado_id'],
                $data['tipo_incidencia'],
                $data['fecha_incidencia'],
                $data['cantidad'] ?? 1,
                $data['monto'] ?? 0,
                $data['descripcion'] ?? '',
                $_SESSION['user_id']
            ]);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Incidencia creada exitosamente',
                    'id' => $db->lastInsertId()
                ]);
            } else {
                throw new Exception('Error al crear incidencia');
            }
            break;
            
        case 'aprobar_incidencia':
            if ($method !== 'POST') {
                throw new Exception('Método no permitido');
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            $id = $data['id'] ?? null;
            
            if (!$id) {
                throw new Exception('ID no proporcionado');
            }
            
            $stmt = $db->prepare("UPDATE incidencias_nomina SET estatus = 'Aprobado' WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Incidencia aprobada'
                ]);
            } else {
                throw new Exception('Error al aprobar incidencia');
            }
            break;
            
        case 'rechazar_incidencia':
            if ($method !== 'POST') {
                throw new Exception('Método no permitido');
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            $id = $data['id'] ?? null;
            
            if (!$id) {
                throw new Exception('ID no proporcionado');
            }
            
            $stmt = $db->prepare("UPDATE incidencias_nomina SET estatus = 'Rechazado' WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Incidencia rechazada'
                ]);
            } else {
                throw new Exception('Error al rechazar incidencia');
            }
            break;
            
        case 'listar_incidencias':
            $empleadoId = $_GET['empleado_id'] ?? null;
            $periodoId = $_GET['periodo_id'] ?? null;
            
            $sql = "SELECT i.*, 
                    CONCAT(e.nombres, ' ', e.apellido_paterno) as nombre_empleado
                    FROM incidencias_nomina i
                    INNER JOIN empleados e ON i.empleado_id = e.id
                    WHERE 1=1";
            $params = [];
            
            if ($empleadoId) {
                $sql .= " AND i.empleado_id = ?";
                $params[] = $empleadoId;
            }
            
            if ($periodoId) {
                $sql .= " AND i.periodo_id = ?";
                $params[] = $periodoId;
            }
            
            $sql .= " ORDER BY i.fecha_incidencia DESC LIMIT 50";
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $incidencias = $stmt->fetchAll();
            
            echo json_encode([
                'success' => true,
                'data' => $incidencias
            ]);
            break;
            
        // ===== PRÉSTAMOS =====
        case 'crear_prestamo':
            if ($method !== 'POST') {
                throw new Exception('Método no permitido');
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            $montoTotal = $data['monto_total'];
            $numeroPagos = $data['numero_pagos'];
            $montoPago = round($montoTotal / $numeroPagos, 2);
            
            $stmt = $db->prepare("
                INSERT INTO prestamos (
                    empleado_id, monto_total, monto_pendiente, numero_pagos,
                    monto_pago, fecha_otorgamiento, motivo, estatus
                ) VALUES (?, ?, ?, ?, ?, ?, ?, 'Activo')
            ");
            
            $result = $stmt->execute([
                $data['empleado_id'],
                $montoTotal,
                $montoTotal,
                $numeroPagos,
                $montoPago,
                $data['fecha_otorgamiento'] ?? date('Y-m-d'),
                $data['motivo'] ?? '',
            ]);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Préstamo creado exitosamente',
                    'id' => $db->lastInsertId(),
                    'monto_pago' => $montoPago
                ]);
            } else {
                throw new Exception('Error al crear préstamo');
            }
            break;
            
        case 'listar_prestamos':
            $empleadoId = $_GET['empleado_id'] ?? null;
            $estatus = $_GET['estatus'] ?? 'Activo';
            
            $sql = "SELECT p.*, 
                    CONCAT(e.nombres, ' ', e.apellido_paterno) as nombre_empleado,
                    e.departamento
                    FROM prestamos p
                    INNER JOIN empleados e ON p.empleado_id = e.id
                    WHERE p.estatus = ?";
            $params = [$estatus];
            
            if ($empleadoId) {
                $sql .= " AND p.empleado_id = ?";
                $params[] = $empleadoId;
            }
            
            $sql .= " ORDER BY p.fecha_otorgamiento DESC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $prestamos = $stmt->fetchAll();
            
            echo json_encode([
                'success' => true,
                'data' => $prestamos
            ]);
            break;
            
        // ===== BONOS =====
        case 'crear_bono':
            if ($method !== 'POST') {
                throw new Exception('Método no permitido');
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            $stmt = $db->prepare("
                INSERT INTO bonos (
                    empleado_id, tipo_bono, monto, fecha_otorgamiento,
                    descripcion, usuario_autoriza_id
                ) VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([
                $data['empleado_id'],
                $data['tipo_bono'],
                $data['monto'],
                $data['fecha_otorgamiento'] ?? date('Y-m-d'),
                $data['descripcion'] ?? '',
                $_SESSION['user_id']
            ]);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Bono creado exitosamente',
                    'id' => $db->lastInsertId()
                ]);
            } else {
                throw new Exception('Error al crear bono');
            }
            break;
            
        case 'listar_bonos':
            $empleadoId = $_GET['empleado_id'] ?? null;
            $limit = $_GET['limit'] ?? 50;
            
            $sql = "SELECT b.*, 
                    CONCAT(e.nombres, ' ', e.apellido_paterno) as nombre_empleado,
                    e.departamento
                    FROM bonos b
                    INNER JOIN empleados e ON b.empleado_id = e.id
                    WHERE 1=1";
            $params = [];
            
            if ($empleadoId) {
                $sql .= " AND b.empleado_id = ?";
                $params[] = $empleadoId;
            }
            
            $sql .= " ORDER BY b.fecha_otorgamiento DESC LIMIT ?";
            $params[] = (int)$limit;
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $bonos = $stmt->fetchAll();
            
            echo json_encode([
                'success' => true,
                'data' => $bonos
            ]);
            break;
            
        // ===== ASISTENCIA =====
        case 'registrar_asistencia':
            if ($method !== 'POST') {
                throw new Exception('Método no permitido');
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            $empleadoId = $data['empleado_id'];
            $tipo = $data['tipo']; // 'entrada' o 'salida'
            $fecha = date('Y-m-d');
            $hora = date('Y-m-d H:i:s');
            
            // Verificar si ya existe registro de hoy
            $stmt = $db->prepare("SELECT * FROM asistencias WHERE empleado_id = ? AND fecha = ?");
            $stmt->execute([$empleadoId, $fecha]);
            $registro = $stmt->fetch();
            
            if ($tipo === 'entrada') {
                if ($registro) {
                    throw new Exception('Ya existe un registro de entrada para hoy');
                }
                
                // Crear nuevo registro
                $stmt = $db->prepare("
                    INSERT INTO asistencias (empleado_id, fecha, hora_entrada, estatus)
                    VALUES (?, ?, ?, 'Presente')
                ");
                $stmt->execute([$empleadoId, $fecha, $hora]);
                
                $message = 'Entrada registrada exitosamente';
                
            } else if ($tipo === 'salida') {
                if (!$registro) {
                    throw new Exception('No existe un registro de entrada para hoy');
                }
                
                if ($registro['hora_salida']) {
                    throw new Exception('Ya se registró la salida');
                }
                
                // Calcular horas trabajadas
                $horaEntrada = new DateTime($registro['hora_entrada']);
                $horaSalida = new DateTime($hora);
                $diff = $horaEntrada->diff($horaSalida);
                $horasTrabajadas = $diff->h + ($diff->i / 60);
                
                // Actualizar registro
                $stmt = $db->prepare("
                    UPDATE asistencias 
                    SET hora_salida = ?, horas_trabajadas = ?
                    WHERE id = ?
                ");
                $stmt->execute([$hora, $horasTrabajadas, $registro['id']]);
                
                $message = 'Salida registrada exitosamente';
            } else {
                throw new Exception('Tipo de registro inválido');
            }
            
            echo json_encode([
                'success' => true,
                'message' => $message
            ]);
            break;
            
        // ===== VACACIONES =====
        case 'aprobar_vacaciones':
            if ($method !== 'POST') {
                throw new Exception('Método no permitido');
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            $id = $data['id'] ?? null;
            
            if (!$id) {
                throw new Exception('ID no proporcionado');
            }
            
            $db->beginTransaction();
            
            try {
                // Obtener solicitud
                $stmt = $db->prepare("SELECT * FROM solicitudes_vacaciones WHERE id = ?");
                $stmt->execute([$id]);
                $solicitud = $stmt->fetch();
                
                if (!$solicitud) {
                    throw new Exception('Solicitud no encontrada');
                }
                
                // Actualizar solicitud
                $stmt = $db->prepare("
                    UPDATE solicitudes_vacaciones 
                    SET estatus = 'Aprobada', 
                        aprobador_id = ?,
                        fecha_respuesta = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$_SESSION['user_id'], $id]);
                
                // Actualizar días disponibles
                $anio = date('Y', strtotime($solicitud['fecha_inicio']));
                $stmt = $db->prepare("
                    UPDATE vacaciones 
                    SET dias_tomados = dias_tomados + ?,
                        dias_disponibles = dias_disponibles - ?
                    WHERE empleado_id = ? AND anio = ?
                ");
                $stmt->execute([
                    $solicitud['dias_solicitados'],
                    $solicitud['dias_solicitados'],
                    $solicitud['empleado_id'],
                    $anio
                ]);
                
                $db->commit();
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Solicitud aprobada exitosamente'
                ]);
                
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            break;
            
        case 'rechazar_vacaciones':
            if ($method !== 'POST') {
                throw new Exception('Método no permitido');
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            $id = $data['id'] ?? null;
            $comentario = $data['comentario'] ?? '';
            
            if (!$id) {
                throw new Exception('ID no proporcionado');
            }
            
            $stmt = $db->prepare("
                UPDATE solicitudes_vacaciones 
                SET estatus = 'Rechazada',
                    aprobador_id = ?,
                    fecha_respuesta = NOW(),
                    comentarios_aprobador = ?
                WHERE id = ?
            ");
            $result = $stmt->execute([$_SESSION['user_id'], $comentario, $id]);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Solicitud rechazada'
                ]);
            } else {
                throw new Exception('Error al rechazar solicitud');
            }
            break;
            
        // ===== EMPLEADOS =====
        case 'buscar_empleados':
            $query = $_GET['q'] ?? '';
            
            $stmt = $db->prepare("
                SELECT id, numero_empleado, 
                       CONCAT(nombres, ' ', apellido_paterno, ' ', IFNULL(apellido_materno, '')) as nombre_completo,
                       departamento, puesto
                FROM empleados 
                WHERE estatus = 'Activo' 
                AND (numero_empleado LIKE ? OR nombres LIKE ? OR apellido_paterno LIKE ?)
                LIMIT 10
            ");
            $searchTerm = "%{$query}%";
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
            $empleados = $stmt->fetchAll();
            
            echo json_encode([
                'success' => true,
                'data' => $empleados
            ]);
            break;
            
        default:
            throw new Exception('Acción no válida');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
