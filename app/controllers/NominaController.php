<?php
/**
 * Controlador de Nómina
 */

class NominaController {
    
    public function index() {
        AuthController::check();
        
        $db = Database::getInstance()->getConnection();
        
        // Obtener periodos de nómina
        $stmt = $db->query("SELECT * FROM periodos_nomina ORDER BY fecha_inicio DESC LIMIT 10");
        $periodos = $stmt->fetchAll();
        
        $data = [
            'title' => 'Administración de Nómina',
            'periodos' => $periodos
        ];
        
        ob_start();
        require_once BASE_PATH . 'app/views/nomina/index.php';
        $content = ob_get_clean();
        
        require_once BASE_PATH . 'app/views/layouts/main.php';
    }
    
    public function procesar() {
        AuthController::checkRole(['admin', 'rrhh']);
        
        $data = [
            'title' => 'Procesar Nómina'
        ];
        
        ob_start();
        require_once BASE_PATH . 'app/views/nomina/procesar.php';
        $content = ob_get_clean();
        
        require_once BASE_PATH . 'app/views/layouts/main.php';
    }
    
    public function configuracion() {
        AuthController::checkRole(['admin', 'rrhh']);
        
        $db = Database::getInstance()->getConnection();
        
        // Obtener conceptos de nómina
        $stmt = $db->query("SELECT * FROM conceptos_nomina ORDER BY tipo, nombre");
        $conceptos = $stmt->fetchAll();
        
        $data = [
            'title' => 'Configuración de Nómina',
            'conceptos' => $conceptos
        ];
        
        ob_start();
        require_once BASE_PATH . 'app/views/nomina/configuracion.php';
        $content = ob_get_clean();
        
        require_once BASE_PATH . 'app/views/layouts/main.php';
    }
    
    public function recibos() {
        AuthController::check();
        
        $data = [
            'title' => 'Recibos de Nómina'
        ];
        
        ob_start();
        require_once BASE_PATH . 'app/views/nomina/recibos.php';
        $content = ob_get_clean();
        
        require_once BASE_PATH . 'app/views/layouts/main.php';
    }
}
