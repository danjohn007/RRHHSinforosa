<?php
/**
 * Controlador de Reclutamiento
 */

class ReclutamientoController {
    
    public function index() {
        AuthController::check();
        
        $db = Database::getInstance()->getConnection();
        
        // Obtener candidatos
        $stmt = $db->query("SELECT * FROM candidatos ORDER BY fecha_aplicacion DESC LIMIT 20");
        $candidatos = $stmt->fetchAll();
        
        // Contar por estatus
        $stmt = $db->query("SELECT estatus, COUNT(*) as total FROM candidatos GROUP BY estatus");
        $stats = $stmt->fetchAll();
        
        $data = [
            'title' => 'Gestión de Candidatos',
            'candidatos' => $candidatos,
            'stats' => $stats
        ];
        
        ob_start();
        require_once BASE_PATH . 'app/views/reclutamiento/index.php';
        $content = ob_get_clean();
        
        require_once BASE_PATH . 'app/views/layouts/main.php';
    }
    
    public function candidatos() {
        AuthController::check();
        redirect('reclutamiento');
    }
    
    public function entrevistas() {
        AuthController::check();
        
        $db = Database::getInstance()->getConnection();
        
        // Obtener entrevistas programadas
        $stmt = $db->query("
            SELECT e.*, 
                   CONCAT(c.nombres, ' ', c.apellido_paterno) as nombre_candidato,
                   c.puesto_deseado
            FROM entrevistas e
            INNER JOIN candidatos c ON e.candidato_id = c.id
            WHERE e.fecha_programada >= CURDATE()
            ORDER BY e.fecha_programada ASC
            LIMIT 20
        ");
        $entrevistas = $stmt->fetchAll();
        
        $data = [
            'title' => 'Gestión de Entrevistas',
            'entrevistas' => $entrevistas
        ];
        
        ob_start();
        require_once BASE_PATH . 'app/views/reclutamiento/entrevistas.php';
        $content = ob_get_clean();
        
        require_once BASE_PATH . 'app/views/layouts/main.php';
    }
}
