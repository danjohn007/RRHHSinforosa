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
    
    /**
     * Obtener perfil completo de un candidato (AJAX)
     */
    public function obtenerPerfil() {
        AuthController::check();
        header('Content-Type: application/json');
        
        $id = $_GET['id'] ?? 0;
        
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID no válido']);
            exit;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM candidatos WHERE id = ?");
            $stmt->execute([$id]);
            $candidato = $stmt->fetch();
            
            if (!$candidato) {
                echo json_encode(['success' => false, 'message' => 'Candidato no encontrado']);
                exit;
            }
            
            // Obtener entrevistas del candidato
            $stmt = $db->prepare("SELECT * FROM entrevistas WHERE candidato_id = ? ORDER BY fecha_programada DESC");
            $stmt->execute([$id]);
            $entrevistas = $stmt->fetchAll();
            
            echo json_encode([
                'success' => true,
                'candidato' => $candidato,
                'entrevistas' => $entrevistas
            ]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al obtener el candidato: ' . $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Programar entrevista (AJAX)
     */
    public function programarEntrevista() {
        AuthController::check();
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }
        
        $candidato_id = $_POST['candidato_id'] ?? 0;
        $tipo = $_POST['tipo'] ?? '';
        $fecha_programada = $_POST['fecha_programada'] ?? '';
        $hora = $_POST['hora'] ?? '';
        $duracion = $_POST['duracion'] ?? 60;
        $ubicacion = $_POST['ubicacion'] ?? '';
        $observaciones = $_POST['observaciones'] ?? '';
        
        if (!$candidato_id || !$tipo || !$fecha_programada || !$hora) {
            echo json_encode(['success' => false, 'message' => 'Faltan datos obligatorios']);
            exit;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Verificar que el candidato existe
            $stmt = $db->prepare("SELECT id FROM candidatos WHERE id = ?");
            $stmt->execute([$candidato_id]);
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Candidato no encontrado']);
                exit;
            }
            
            // Crear fecha y hora completa
            $fecha_hora = $fecha_programada . ' ' . $hora;
            
            // Insertar entrevista
            $stmt = $db->prepare("
                INSERT INTO entrevistas 
                (candidato_id, tipo, fecha_programada, duracion_minutos, ubicacion, observaciones, estatus, entrevistador_id)
                VALUES (?, ?, ?, ?, ?, ?, 'Programada', ?)
            ");
            
            $entrevistador_id = $_SESSION['user_id'] ?? null;
            
            $stmt->execute([
                $candidato_id,
                $tipo,
                $fecha_hora,
                $duracion,
                $ubicacion,
                $observaciones,
                $entrevistador_id
            ]);
            
            // Actualizar estatus del candidato
            $stmt = $db->prepare("UPDATE candidatos SET estatus = 'Entrevista' WHERE id = ? AND estatus != 'Contratado'");
            $stmt->execute([$candidato_id]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Entrevista programada exitosamente',
                'id' => $db->lastInsertId()
            ]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al programar entrevista: ' . $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Contratar candidato (AJAX)
     */
    public function contratarCandidato() {
        AuthController::check();
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }
        
        $candidato_id = $_POST['candidato_id'] ?? 0;
        $fecha_ingreso = $_POST['fecha_ingreso'] ?? date('Y-m-d');
        $departamento = $_POST['departamento'] ?? '';
        $puesto = $_POST['puesto'] ?? '';
        $salario_diario = $_POST['salario_diario'] ?? 0;
        $tipo_contrato = $_POST['tipo_contrato'] ?? 'Planta';
        
        if (!$candidato_id) {
            echo json_encode(['success' => false, 'message' => 'ID de candidato no válido']);
            exit;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            $db->beginTransaction();
            
            // Obtener datos del candidato
            $stmt = $db->prepare("SELECT * FROM candidatos WHERE id = ?");
            $stmt->execute([$candidato_id]);
            $candidato = $stmt->fetch();
            
            if (!$candidato) {
                $db->rollBack();
                echo json_encode(['success' => false, 'message' => 'Candidato no encontrado']);
                exit;
            }
            
            // Verificar si ya fue contratado
            if ($candidato['estatus'] === 'Contratado') {
                $db->rollBack();
                echo json_encode(['success' => false, 'message' => 'Este candidato ya fue contratado']);
                exit;
            }
            
            // Generar número de empleado único
            $stmt = $db->query("SELECT MAX(CAST(SUBSTRING(numero_empleado, 4) AS UNSIGNED)) as max_num FROM empleados WHERE numero_empleado LIKE 'EMP%'");
            $result = $stmt->fetch();
            $next_num = ($result['max_num'] ?? 0) + 1;
            $numero_empleado = 'EMP' . str_pad($next_num, 3, '0', STR_PAD_LEFT);
            
            // Calcular salario mensual
            $salario_mensual = $salario_diario * 30;
            
            // Usar datos del candidato o valores por defecto
            $puesto_final = $puesto ?: $candidato['puesto_deseado'];
            
            // Crear empleado
            $stmt = $db->prepare("
                INSERT INTO empleados 
                (numero_empleado, nombres, apellido_paterno, apellido_materno, 
                 email_personal, telefono, celular, fecha_nacimiento,
                 calle, colonia, municipio, estado, codigo_postal,
                 fecha_ingreso, estatus, tipo_contrato, departamento, puesto,
                 salario_diario, salario_mensual)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Activo', ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $numero_empleado,
                $candidato['nombres'],
                $candidato['apellido_paterno'],
                $candidato['apellido_materno'],
                $candidato['email'],
                $candidato['telefono'],
                $candidato['celular'],
                $candidato['fecha_nacimiento'],
                $candidato['calle'],
                $candidato['colonia'],
                $candidato['municipio'],
                $candidato['estado'],
                $candidato['codigo_postal'],
                $fecha_ingreso,
                $tipo_contrato,
                $departamento,
                $puesto_final,
                $salario_diario,
                $salario_mensual
            ]);
            
            $empleado_id = $db->lastInsertId();
            
            // Actualizar estatus del candidato
            $stmt = $db->prepare("UPDATE candidatos SET estatus = 'Contratado' WHERE id = ?");
            $stmt->execute([$candidato_id]);
            
            $db->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Candidato contratado exitosamente',
                'numero_empleado' => $numero_empleado,
                'empleado_id' => $empleado_id
            ]);
            
        } catch (Exception $e) {
            $db->rollBack();
            echo json_encode(['success' => false, 'message' => 'Error al contratar candidato: ' . $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Rechazar candidato (AJAX)
     */
    public function rechazarCandidato() {
        AuthController::check();
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }
        
        $candidato_id = $_POST['candidato_id'] ?? 0;
        $motivo = $_POST['motivo'] ?? '';
        
        if (!$candidato_id) {
            echo json_encode(['success' => false, 'message' => 'ID de candidato no válido']);
            exit;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE candidatos SET estatus = 'Rechazado' WHERE id = ?");
            $stmt->execute([$candidato_id]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Candidato rechazado'
            ]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al rechazar candidato: ' . $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Obtener datos de una entrevista específica (AJAX)
     */
    public function obtenerEntrevista() {
        AuthController::check();
        header('Content-Type: application/json');
        
        $id = $_GET['id'] ?? 0;
        
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID no válido']);
            exit;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM entrevistas WHERE id = ?");
            $stmt->execute([$id]);
            $entrevista = $stmt->fetch();
            
            if (!$entrevista) {
                echo json_encode(['success' => false, 'message' => 'Entrevista no encontrada']);
                exit;
            }
            
            echo json_encode([
                'success' => true,
                'entrevista' => $entrevista
            ]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al obtener la entrevista: ' . $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Reagendar entrevista existente (AJAX)
     */
    public function reagendarEntrevista() {
        AuthController::check();
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }
        
        $entrevista_id = $_POST['entrevista_id'] ?? 0;
        $tipo = $_POST['tipo'] ?? '';
        $fecha_programada = $_POST['fecha_programada'] ?? '';
        $hora = $_POST['hora'] ?? '';
        $duracion = $_POST['duracion'] ?? 60;
        $ubicacion = $_POST['ubicacion'] ?? '';
        $observaciones = $_POST['observaciones'] ?? '';
        
        if (!$entrevista_id || !$tipo || !$fecha_programada || !$hora) {
            echo json_encode(['success' => false, 'message' => 'Faltan datos obligatorios']);
            exit;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Verificar que la entrevista existe
            $stmt = $db->prepare("SELECT id FROM entrevistas WHERE id = ?");
            $stmt->execute([$entrevista_id]);
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Entrevista no encontrada']);
                exit;
            }
            
            // Crear fecha y hora completa
            $fecha_hora = $fecha_programada . ' ' . $hora;
            
            // Actualizar entrevista
            $stmt = $db->prepare("
                UPDATE entrevistas 
                SET tipo = ?, fecha_programada = ?, duracion_minutos = ?, 
                    ubicacion = ?, observaciones = ?, estatus = 'Reprogramada'
                WHERE id = ?
            ");
            
            $stmt->execute([
                $tipo,
                $fecha_hora,
                $duracion,
                $ubicacion,
                $observaciones,
                $entrevista_id
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Entrevista reagendada exitosamente'
            ]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al reagendar entrevista: ' . $e->getMessage()]);
        }
        exit;
    }
    
    public function marcarRevision() {
        AuthController::check();
        
        // Limpiar cualquier output buffer previo
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }
        
        try {
            $candidato_id = $_POST['candidato_id'] ?? null;
            
            if (!$candidato_id) {
                echo json_encode(['success' => false, 'message' => 'ID de candidato no proporcionado']);
                exit;
            }
            
            $db = Database::getInstance()->getConnection();
            
            // Actualizar estatus del candidato
            $stmt = $db->prepare("UPDATE candidatos SET estatus = 'En Revisión' WHERE id = ?");
            $resultado = $stmt->execute([$candidato_id]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Candidato marcado como En Revisión',
                'rows_affected' => $stmt->rowCount()
            ]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
}
