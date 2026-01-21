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
        
        // === NUEVAS GRÁFICAS ===
        
        // 1. Nómina acumulada desde último corte
        $nominaAcumuladaStmt = $db->query("
            SELECT COALESCE(SUM(total_neto), 0) as total_acumulado
            FROM periodos_nomina
            WHERE estatus IN ('Procesado', 'Pagado')
            AND fecha_inicio >= (
                SELECT COALESCE(MAX(fecha_fin), DATE_SUB(NOW(), INTERVAL 3 MONTH))
                FROM periodos_nomina
                WHERE estatus = 'Cerrado'
            )
        ");
        $nominaAcumuladaResult = $nominaAcumuladaStmt->fetch();
        $nominaAcumulada = $nominaAcumuladaResult ? (float)$nominaAcumuladaResult['total_acumulado'] : 0;
        
        // 2. Distribución por género
        $genderStmt = $db->query("
            SELECT 
                genero,
                COUNT(*) as total
            FROM empleados
            WHERE estatus = 'Activo'
            GROUP BY genero
        ");
        $genderData = $genderStmt->fetchAll();
        $genderLabels = [];
        $genderCounts = [];
        foreach ($genderData as $row) {
            $genderLabel = $row['genero'] === 'M' ? 'Masculino' : ($row['genero'] === 'F' ? 'Femenino' : 'Otro');
            $genderLabels[] = $genderLabel;
            $genderCounts[] = (int)$row['total'];
        }
        
        // 3. Contrataciones por mes (últimos 6 meses)
        $hiringStmt = $db->query("
            SELECT 
                DATE_FORMAT(fecha_ingreso, '%Y-%m') as mes,
                DATE_FORMAT(fecha_ingreso, '%b') as mes_nombre,
                COUNT(*) as total
            FROM empleados
            WHERE fecha_ingreso >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY mes, mes_nombre
            ORDER BY mes ASC
        ");
        $hiringData = $hiringStmt->fetchAll();
        $hiringLabels = [];
        $hiringCounts = [];
        foreach ($hiringData as $row) {
            $hiringLabels[] = ucfirst($row['mes_nombre']);
            $hiringCounts[] = (int)$row['total'];
        }
        
        // 4. Resumen de incidencias (último mes)
        $incidenciasStmt = $db->query("
            SELECT 
                estatus,
                COUNT(*) as total
            FROM asistencias
            WHERE fecha >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY estatus
        ");
        $incidenciasData = $incidenciasStmt->fetchAll();
        $incidenciasLabels = [];
        $incidenciasCounts = [];
        foreach ($incidenciasData as $row) {
            $incidenciasLabels[] = $row['estatus'];
            $incidenciasCounts[] = (int)$row['total'];
        }
        
        // 5. Distribución salarial
        $salaryStmt = $db->query("
            SELECT 
                CASE
                    WHEN salario_mensual < 5000 THEN 'Menos de $5,000'
                    WHEN salario_mensual >= 5000 AND salario_mensual < 10000 THEN '$5,000 - $10,000'
                    WHEN salario_mensual >= 10000 AND salario_mensual < 15000 THEN '$10,000 - $15,000'
                    WHEN salario_mensual >= 15000 AND salario_mensual < 20000 THEN '$15,000 - $20,000'
                    ELSE 'Más de $20,000'
                END as rango,
                COUNT(*) as total
            FROM empleados
            WHERE estatus = 'Activo' AND salario_mensual > 0
            GROUP BY rango
            ORDER BY 
                CASE
                    WHEN salario_mensual < 5000 THEN 1
                    WHEN salario_mensual >= 5000 AND salario_mensual < 10000 THEN 2
                    WHEN salario_mensual >= 10000 AND salario_mensual < 15000 THEN 3
                    WHEN salario_mensual >= 15000 AND salario_mensual < 20000 THEN 4
                    ELSE 5
                END
        ");
        $salaryData = $salaryStmt->fetchAll();
        $salaryLabels = [];
        $salaryCounts = [];
        foreach ($salaryData as $row) {
            $salaryLabels[] = $row['rango'];
            $salaryCounts[] = (int)$row['total'];
        }
        
        $data = [
            'title' => 'Dashboard',
            'totalEmpleados' => $totalEmpleados,
            'empleadosActivos' => $empleadosActivos,
            'nominaCount' => $nominaCount,
            'vacacionesPendientes' => $vacacionesPendientes,
            'candidatosEnProceso' => $candidatosEnProceso,
            'departmentLabels' => $departmentLabels,
            'departmentData' => $departmentData,
            'birthdays' => $birthdays,
            // Nuevos datos
            'nominaAcumulada' => $nominaAcumulada,
            'genderLabels' => $genderLabels,
            'genderCounts' => $genderCounts,
            'hiringLabels' => $hiringLabels,
            'hiringCounts' => $hiringCounts,
            'incidenciasLabels' => $incidenciasLabels,
            'incidenciasCounts' => $incidenciasCounts,
            'salaryLabels' => $salaryLabels,
            'salaryCounts' => $salaryCounts
        ];
        
        // Cargar vista con layout
        ob_start();
        require_once BASE_PATH . 'app/views/dashboard/index.php';
        $content = ob_get_clean();
        
        require_once BASE_PATH . 'app/views/layouts/main.php';
    }
}
