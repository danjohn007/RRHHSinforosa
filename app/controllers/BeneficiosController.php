<?php
/**
 * Controlador de Beneficios
 */

class BeneficiosController {
    
    public function index() {
        AuthController::check();
        
        $db = Database::getInstance()->getConnection();
        
        // Obtener préstamos activos
        $stmt = $db->query("
            SELECT p.*, 
                   CONCAT(e.nombres, ' ', e.apellido_paterno) as nombre_empleado,
                   e.departamento
            FROM prestamos p
            INNER JOIN empleados e ON p.empleado_id = e.id
            WHERE p.estatus = 'Activo'
            ORDER BY p.fecha_otorgamiento DESC
            LIMIT 20
        ");
        $prestamos = $stmt->fetchAll();
        
        // Obtener bonos recientes
        $stmt = $db->query("
            SELECT b.*, 
                   CONCAT(e.nombres, ' ', e.apellido_paterno) as nombre_empleado,
                   e.departamento
            FROM bonos b
            INNER JOIN empleados e ON b.empleado_id = e.id
            ORDER BY b.fecha_otorgamiento DESC
            LIMIT 20
        ");
        $bonos = $stmt->fetchAll();
        
        $data = [
            'title' => 'Gestión de Beneficios',
            'prestamos' => $prestamos,
            'bonos' => $bonos
        ];
        
        ob_start();
        require_once BASE_PATH . 'app/views/beneficios/index.php';
        $content = ob_get_clean();
        
        require_once BASE_PATH . 'app/views/layouts/main.php';
    }
    
    public function prestamos() {
        AuthController::check();
        redirect('beneficios');
    }
    
    public function bonos() {
        AuthController::check();
        redirect('beneficios');
    }
}
