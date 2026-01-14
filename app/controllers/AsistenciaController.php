<?php
/**
 * Controlador de Asistencia
 */

class AsistenciaController {
    
    public function index() {
        AuthController::check();
        
        $db = Database::getInstance()->getConnection();
        
        // Obtener asistencias de hoy
        $stmt = $db->query("
            SELECT a.*, 
                   CONCAT(e.nombres, ' ', e.apellido_paterno) as nombre_empleado,
                   e.departamento
            FROM asistencias a
            INNER JOIN empleados e ON a.empleado_id = e.id
            WHERE a.fecha = CURDATE()
            ORDER BY a.hora_entrada DESC
        ");
        $asistencias = $stmt->fetchAll();
        
        $data = [
            'title' => 'Control de Asistencia',
            'asistencias' => $asistencias
        ];
        
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
        
        $data = [
            'title' => 'Gestión de Vacaciones',
            'solicitudes' => $solicitudes
        ];
        
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
        
        ob_start();
        require_once BASE_PATH . 'app/views/asistencia/turnos.php';
        $content = ob_get_clean();
        
        require_once BASE_PATH . 'app/views/layouts/main.php';
    }
}
