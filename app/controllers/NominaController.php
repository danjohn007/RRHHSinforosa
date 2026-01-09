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
        
        $db = Database::getInstance()->getConnection();
        $success = '';
        $error = '';
        $resultado = null;
        
        // Procesar nómina si se envió el formulario
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['procesar_nomina'])) {
            $periodoId = $_POST['periodo_id'] ?? null;
            
            if ($periodoId) {
                require_once BASE_PATH . 'app/services/NominaService.php';
                $nominaService = new NominaService();
                $resultado = $nominaService->procesarNomina($periodoId);
                
                if ($resultado['success']) {
                    $success = "Nómina procesada exitosamente. {$resultado['procesados']} empleados procesados.";
                } else {
                    $error = "Error al procesar nómina: " . $resultado['error'];
                }
            } else {
                $error = "Debe seleccionar un período";
            }
        }
        
        // Crear nuevo período si se solicitó
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_periodo'])) {
            $tipo = $_POST['tipo'] ?? 'Quincenal';
            $fechaInicio = $_POST['fecha_inicio'] ?? null;
            $fechaFin = $_POST['fecha_fin'] ?? null;
            $fechaPago = $_POST['fecha_pago'] ?? null;
            
            if ($fechaInicio && $fechaFin && $fechaPago) {
                $stmt = $db->prepare("
                    INSERT INTO periodos_nomina (tipo, fecha_inicio, fecha_fin, fecha_pago, estatus)
                    VALUES (?, ?, ?, ?, 'Abierto')
                ");
                if ($stmt->execute([$tipo, $fechaInicio, $fechaFin, $fechaPago])) {
                    $success = "Período creado exitosamente";
                    header("refresh:2;url=" . BASE_URL . "nomina");
                } else {
                    $error = "Error al crear período";
                }
            } else {
                $error = "Todos los campos son requeridos";
            }
        }
        
        // Obtener períodos disponibles para procesar
        $stmt = $db->query("
            SELECT * FROM periodos_nomina 
            WHERE estatus IN ('Abierto', 'En Proceso')
            ORDER BY fecha_inicio DESC
        ");
        $periodosDisponibles = $stmt->fetchAll();
        
        $data = [
            'title' => 'Procesar Nómina',
            'periodosDisponibles' => $periodosDisponibles,
            'success' => $success,
            'error' => $error,
            'resultado' => $resultado
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
