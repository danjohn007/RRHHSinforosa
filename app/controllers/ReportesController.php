<?php
/**
 * Controlador de Reportes
 */

class ReportesController {
    
    public function index() {
        AuthController::check();
        
        $data = [
            'title' => 'Centro de Reportes'
        ];
        
        ob_start();
        require_once BASE_PATH . 'app/views/reportes/index.php';
        $content = ob_get_clean();
        
        require_once BASE_PATH . 'app/views/layouts/main.php';
    }
    
    public function personal() {
        AuthController::check();
        
        $data = [
            'title' => 'Reporte de Personal'
        ];
        
        ob_start();
        require_once BASE_PATH . 'app/views/reportes/personal.php';
        $content = ob_get_clean();
        
        require_once BASE_PATH . 'app/views/layouts/main.php';
    }
    
    public function nomina() {
        AuthController::check();
        
        $data = [
            'title' => 'Reporte de Nómina'
        ];
        
        ob_start();
        require_once BASE_PATH . 'app/views/reportes/nomina.php';
        $content = ob_get_clean();
        
        require_once BASE_PATH . 'app/views/layouts/main.php';
    }
    
    public function vacaciones() {
        AuthController::check();
        
        $data = [
            'title' => 'Reporte de Vacaciones'
        ];
        
        ob_start();
        require_once BASE_PATH . 'app/views/reportes/vacaciones.php';
        $content = ob_get_clean();
        
        require_once BASE_PATH . 'app/views/layouts/main.php';
    }
}
