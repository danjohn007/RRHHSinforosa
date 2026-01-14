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
                // Verificar que el período existe y está disponible
                $stmt = $db->prepare("
                    SELECT * FROM periodos_nomina 
                    WHERE id = ? AND estatus IN ('Abierto', 'En Proceso')
                ");
                $stmt->execute([$periodoId]);
                $periodoVerificado = $stmt->fetch();
                
                if (!$periodoVerificado) {
                    $error = "El período seleccionado no está disponible para procesamiento";
                } else {
                    // Verificar si ya fue procesado
                    $stmt = $db->prepare("
                        SELECT COUNT(*) as total FROM nomina_detalle 
                        WHERE periodo_id = ?
                    ");
                    $stmt->execute([$periodoId]);
                    $yaExiste = $stmt->fetch();
                    
                    if ($yaExiste['total'] > 0 && !isset($_POST['reprocesar'])) {
                        $error = "Este período ya fue procesado. Si desea reprocesarlo, marque la casilla 'Reprocesar'.";
                    } else {
                        require_once BASE_PATH . 'app/services/NominaService.php';
                        $nominaService = new NominaService();
                        $resultado = $nominaService->procesarNomina($periodoId);
                        
                        if ($resultado['success']) {
                            $success = "Nómina procesada exitosamente. {$resultado['procesados']} empleados procesados.";
                            if (!empty($resultado['errores'])) {
                                $success .= " Se encontraron " . count($resultado['errores']) . " errores.";
                            }
                        } else {
                            $error = "Error al procesar nómina: " . $resultado['error'];
                        }
                    }
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
                // Validar que las fechas sean coherentes
                $inicio = strtotime($fechaInicio);
                $fin = strtotime($fechaFin);
                $pago = strtotime($fechaPago);
                
                if ($fin <= $inicio) {
                    $error = "La fecha fin debe ser posterior a la fecha inicio";
                } elseif ($pago < $fin) {
                    $error = "La fecha de pago debe ser igual o posterior a la fecha fin del período";
                } else {
                    // Verificar que no haya solapamiento de períodos
                    $stmt = $db->prepare("
                        SELECT COUNT(*) as total FROM periodos_nomina 
                        WHERE tipo = ? 
                        AND estatus != 'Cancelado'
                        AND (
                            (fecha_inicio BETWEEN ? AND ?) OR
                            (fecha_fin BETWEEN ? AND ?) OR
                            (? BETWEEN fecha_inicio AND fecha_fin)
                        )
                    ");
                    $stmt->execute([$tipo, $fechaInicio, $fechaFin, $fechaInicio, $fechaFin, $fechaInicio]);
                    $solapamiento = $stmt->fetch();
                    
                    if ($solapamiento['total'] > 0) {
                        $error = "Ya existe un período de nómina que se solapa con estas fechas";
                    } else {
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
                    }
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
    
    public function obtenerConcepto() {
        AuthController::checkRole(['admin', 'rrhh']);
        header('Content-Type: application/json');
        
        $clave = $_GET['clave'] ?? null;
        
        if (!$clave) {
            echo json_encode(['success' => false, 'message' => 'Clave no proporcionada']);
            exit;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM conceptos_nomina WHERE clave = ?");
            $stmt->execute([$clave]);
            $concepto = $stmt->fetch();
            
            if (!$concepto) {
                echo json_encode(['success' => false, 'message' => 'Concepto no encontrado']);
            } else {
                echo json_encode(['success' => true, 'concepto' => $concepto]);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    public function guardarConcepto() {
        AuthController::checkRole(['admin', 'rrhh']);
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        $clave = $data['clave'] ?? null;
        $claveOriginal = $data['clave_original'] ?? null;
        $nombre = $data['nombre'] ?? null;
        $tipo = $data['tipo'] ?? null;
        $categoria = $data['categoria'] ?? null;
        $afectaIMSS = isset($data['afecta_imss']) ? (int)$data['afecta_imss'] : 0;
        $afectaISR = isset($data['afecta_isr']) ? (int)$data['afecta_isr'] : 0;
        $activo = isset($data['activo']) ? (int)$data['activo'] : 1;
        
        if (!$clave || !$nombre || !$tipo || !$categoria) {
            echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
            exit;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            
            if ($claveOriginal) {
                // Actualizar concepto existente
                $stmt = $db->prepare("
                    UPDATE conceptos_nomina 
                    SET clave = ?, nombre = ?, tipo = ?, categoria = ?, 
                        afecta_imss = ?, afecta_isr = ?, activo = ?
                    WHERE clave = ?
                ");
                $stmt->execute([$clave, $nombre, $tipo, $categoria, $afectaIMSS, $afectaISR, $activo, $claveOriginal]);
                echo json_encode(['success' => true, 'message' => 'Concepto actualizado exitosamente']);
            } else {
                // Verificar si la clave ya existe
                $stmt = $db->prepare("SELECT COUNT(*) FROM conceptos_nomina WHERE clave = ?");
                $stmt->execute([$clave]);
                if ($stmt->fetchColumn() > 0) {
                    echo json_encode(['success' => false, 'message' => 'La clave ya existe']);
                    exit;
                }
                
                // Insertar nuevo concepto
                $stmt = $db->prepare("
                    INSERT INTO conceptos_nomina 
                    (clave, nombre, tipo, categoria, afecta_imss, afecta_isr, activo)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$clave, $nombre, $tipo, $categoria, $afectaIMSS, $afectaISR, $activo]);
                echo json_encode(['success' => true, 'message' => 'Concepto creado exitosamente']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    public function eliminarConcepto() {
        AuthController::checkRole(['admin', 'rrhh']);
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        $clave = $data['clave'] ?? null;
        
        if (!$clave) {
            echo json_encode(['success' => false, 'message' => 'Clave no proporcionada']);
            exit;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Verificar si el concepto está en uso
            $stmt = $db->prepare("
                SELECT COUNT(*) FROM nomina_conceptos 
                WHERE concepto_clave = ?
            ");
            $stmt->execute([$clave]);
            $enUso = $stmt->fetchColumn();
            
            if ($enUso > 0) {
                echo json_encode(['success' => false, 'message' => 'No se puede eliminar el concepto porque está en uso en registros de nómina']);
                exit;
            }
            
            $stmt = $db->prepare("DELETE FROM conceptos_nomina WHERE clave = ?");
            $stmt->execute([$clave]);
            
            echo json_encode(['success' => true, 'message' => 'Concepto eliminado exitosamente']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    public function detalle() {
        AuthController::check();
        header('Content-Type: application/json');
        
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
            exit;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Obtener período
            $stmt = $db->prepare("SELECT * FROM periodos_nomina WHERE id = ?");
            $stmt->execute([$id]);
            $periodo = $stmt->fetch();
            
            if (!$periodo) {
                echo json_encode(['success' => false, 'message' => 'Período no encontrado']);
                exit;
            }
            
            // Obtener detalle de empleados
            $stmt = $db->prepare("
                SELECT 
                    nd.*,
                    e.numero_empleado,
                    CONCAT(e.nombres, ' ', e.apellido_paterno, ' ', COALESCE(e.apellido_materno, '')) as nombre_empleado
                FROM nomina_detalle nd
                INNER JOIN empleados e ON nd.empleado_id = e.id
                WHERE nd.periodo_id = ?
                ORDER BY e.numero_empleado
            ");
            $stmt->execute([$id]);
            $empleados = $stmt->fetchAll();
            
            echo json_encode([
                'success' => true,
                'periodo' => $periodo,
                'empleados' => $empleados
            ]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    public function descargar() {
        AuthController::check();
        
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            redirect('nomina');
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Obtener período
            $stmt = $db->prepare("SELECT * FROM periodos_nomina WHERE id = ?");
            $stmt->execute([$id]);
            $periodo = $stmt->fetch();
            
            if (!$periodo) {
                redirect('nomina');
            }
            
            // Obtener detalle
            $stmt = $db->prepare("
                SELECT 
                    nd.*,
                    e.numero_empleado,
                    CONCAT(e.nombres, ' ', e.apellido_paterno, ' ', COALESCE(e.apellido_materno, '')) as nombre_empleado,
                    e.rfc,
                    e.curp
                FROM nomina_detalle nd
                INNER JOIN empleados e ON nd.empleado_id = e.id
                WHERE nd.periodo_id = ?
                ORDER BY e.numero_empleado
            ");
            $stmt->execute([$id]);
            $empleados = $stmt->fetchAll();
            
            // Generar reporte CSV
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="nomina_' . $periodo['tipo'] . '_' . date('Y-m-d', strtotime($periodo['fecha_inicio'])) . '.csv"');
            
            $output = fopen('php://output', 'w');
            
            // BOM para UTF-8
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Encabezados
            fputcsv($output, [
                'No. Empleado',
                'Nombre',
                'RFC',
                'CURP',
                'Días Trabajados',
                'Salario Base',
                'Total Percepciones',
                'ISR',
                'IMSS',
                'Total Deducciones',
                'Total Neto'
            ]);
            
            // Datos
            foreach ($empleados as $emp) {
                fputcsv($output, [
                    $emp['numero_empleado'],
                    $emp['nombre_empleado'],
                    $emp['rfc'] ?? '',
                    $emp['curp'] ?? '',
                    $emp['dias_trabajados'],
                    number_format($emp['salario_base'], 2),
                    number_format($emp['total_percepciones'], 2),
                    number_format($emp['isr'], 2),
                    number_format($emp['imss'], 2),
                    number_format($emp['total_deducciones'], 2),
                    number_format($emp['total_neto'], 2)
                ]);
            }
            
            fclose($output);
            exit;
            
        } catch (Exception $e) {
            redirect('nomina');
        }
    }
}
