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
        
        $data = [
            'title' => 'Registro de Asistencia'
        ];
        
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
