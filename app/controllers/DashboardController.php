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
        if (!empty($departmentCounts)) {
            foreach ($departmentCounts as $dept) {
                $departmentLabels[] = $dept['departamento'] ?? 'Sin departamento';
                $departmentData[] = (int)$dept['total'];
            }
        }
        
        // Si no hay datos, agregar un placeholder
        if (empty($departmentLabels)) {
            $departmentLabels = ['Sin datos'];
            $departmentData = [0];
        }
        
        // Obtener datos de nómina reciente
        $db = Database::getInstance()->getConnection();
        $nominaStmt = $db->query("SELECT COUNT(*) as total FROM periodos_nomina");
        $nominaResult = $nominaStmt->fetch();
        $nominaCount = $nominaResult ? (int)$nominaResult['total'] : 0;
        
        // Solicitudes de vacaciones pendientes
        $vacacionesStmt = $db->query("SELECT COUNT(*) as total FROM solicitudes_vacaciones WHERE estatus = 'Pendiente'");
        $vacacionesResult = $vacacionesStmt->fetch();
        $vacacionesPendientes = $vacacionesResult ? (int)$vacacionesResult['total'] : 0;
        
        // Candidatos en proceso
        $candidatosStmt = $db->query("SELECT COUNT(*) as total FROM candidatos WHERE estatus IN ('En Revisión', 'Entrevista', 'Evaluación')");
        $candidatosResult = $candidatosStmt->fetch();
        $candidatosEnProceso = $candidatosResult ? (int)$candidatosResult['total'] : 0;
        
        $data = [
            'title' => 'Dashboard',
            'totalEmpleados' => $totalEmpleados,
            'empleadosActivos' => $empleadosActivos,
            'nominaCount' => $nominaCount,
            'vacacionesPendientes' => $vacacionesPendientes,
            'candidatosEnProceso' => $candidatosEnProceso,
            'departmentLabels' => $departmentLabels,
            'departmentData' => $departmentData,
            'birthdays' => $birthdays
        ];
        
        // Cargar vista con layout
        ob_start();
        require_once BASE_PATH . 'app/views/dashboard/index.php';
        $content = ob_get_clean();
        
        require_once BASE_PATH . 'app/views/layouts/main.php';
    }
}
