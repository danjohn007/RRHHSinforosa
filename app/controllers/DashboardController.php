<?php
/**
 * Controlador del Dashboard
 */

class DashboardController {
    
    public function index() {
        AuthController::check();
        
        $empleadoModel = new Empleado();
        
        // Obtener estadísticas
        $statusCounts = $empleadoModel->countByStatus();
        $departmentCounts = $empleadoModel->countByDepartment();
        $birthdays = $empleadoModel->getBirthdaysThisMonth();
        
        // Calcular totales
        $totalEmpleados = 0;
        $empleadosActivos = 0;
        foreach ($statusCounts as $status) {
            $totalEmpleados += $status['total'];
            if ($status['estatus'] === 'Activo') {
                $empleadosActivos = $status['total'];
            }
        }
        
        // Preparar datos para gráficas
        $departmentLabels = [];
        $departmentData = [];
        foreach ($departmentCounts as $dept) {
            $departmentLabels[] = $dept['departamento'];
            $departmentData[] = $dept['total'];
        }
        
        // Obtener datos de nómina reciente (simulado por ahora)
        $db = Database::getInstance()->getConnection();
        $nominaStmt = $db->query("SELECT COUNT(*) as total FROM periodos_nomina");
        $nominaCount = $nominaStmt->fetch()['total'];
        
        // Solicitudes de vacaciones pendientes
        $vacacionesStmt = $db->query("SELECT COUNT(*) as total FROM solicitudes_vacaciones WHERE estatus = 'Pendiente'");
        $vacacionesPendientes = $vacacionesStmt->fetch()['total'];
        
        // Candidatos en proceso
        $candidatosStmt = $db->query("SELECT COUNT(*) as total FROM candidatos WHERE estatus IN ('En Revisión', 'Entrevista', 'Evaluación')");
        $candidatosEnProceso = $candidatosStmt->fetch()['total'];
        
        $data = [
            'title' => 'Dashboard',
            'totalEmpleados' => $totalEmpleados,
            'empleadosActivos' => $empleadosActivos,
            'nominaCount' => $nominaCount,
            'vacacionesPendientes' => $vacacionesPendientes,
            'candidatosEnProceso' => $candidatosEnProceso,
            'departmentLabels' => json_encode($departmentLabels),
            'departmentData' => json_encode($departmentData),
            'birthdays' => $birthdays
        ];
        
        // Cargar vista con layout
        ob_start();
        require_once BASE_PATH . 'app/views/dashboard/index.php';
        $content = ob_get_clean();
        
        require_once BASE_PATH . 'app/views/layouts/main.php';
    }
}
