<?php
/**
 * Controlador para Vista Pública de Asistencias
 * No requiere autenticación
 */

class PublicoController {
    
    /**
     * Vista pública de registro de asistencia por sucursal
     */
    public function asistencia() {
        // Obtener URL pública de la sucursal desde la URL
        global $request;
        $parts = explode('/', $request);
        $urlPublica = $parts[2] ?? null;
        
        if (!$urlPublica) {
            die('Sucursal no especificada');
        }
        
        // Buscar sucursal por URL pública
        $sucursalModel = new Sucursal();
        $sucursal = $sucursalModel->getByUrlPublica($urlPublica);
        
        if (!$sucursal) {
            die('Sucursal no encontrada o inactiva');
        }
        
        // Obtener dispositivos Shelly de la sucursal
        $dispositivos = $sucursalModel->getDispositivos($sucursal['id']);
        
        // Obtener configuraciones del sistema para logo y colores
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT clave, valor FROM configuraciones_globales WHERE grupo IN ('sitio', 'estilo')");
        $configuraciones = $stmt->fetchAll();
        $configs = [];
        foreach ($configuraciones as $config) {
            $configs[$config['clave']] = $config['valor'];
        }
        
        // Prepare variables for the view
        // Note: Only passing trusted, validated data to the view
        $sucursal = $sucursal;
        $dispositivos = $dispositivos;
        $configs = $configs;
        $url_publica = $urlPublica;
        
        // Cargar vista pública (sin layout de admin)
        require_once BASE_PATH . 'app/views/publico/asistencia.php';
    }
    
    /**
     * Procesar registro de asistencia (entrada o salida)
     */
    public function registrarAsistencia() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        $codigoEmpleado = $_POST['codigo_empleado'] ?? '';
        $tipoRegistro = $_POST['tipo_registro'] ?? ''; // 'entrada' o 'salida'
        $urlPublica = $_POST['url_publica'] ?? '';
        $fotoBase64 = $_POST['foto'] ?? '';
        $codigoGerente = $_POST['codigo_gerente'] ?? null;
        
        $db = Database::getInstance()->getConnection();
        
        try {
            // Validar código de empleado
            $stmt = $db->prepare("
                SELECT e.*, s.id as sucursal_id, s.nombre as sucursal_nombre,
                       t.hora_entrada, t.hora_salida, t.horas_laborales
                FROM empleados e
                LEFT JOIN sucursales s ON e.sucursal_id = s.id
                LEFT JOIN turnos t ON e.turno_id = t.id
                WHERE e.codigo_empleado = ? AND e.estatus = 'Activo'
            ");
            $stmt->execute([$codigoEmpleado]);
            $empleado = $stmt->fetch();
            
            if (!$empleado) {
                echo json_encode(['success' => false, 'message' => 'Código de empleado no válido']);
                return;
            }
            
            // Obtener sucursal actual por URL
            $sucursalModel = new Sucursal();
            $sucursalActual = $sucursalModel->getByUrlPublica($urlPublica);
            
            if (!$sucursalActual) {
                echo json_encode(['success' => false, 'message' => 'Sucursal no encontrada']);
                return;
            }
            
            $gerenteAutorizadorId = null;
            
            // Verificar si está en su sucursal asignada
            if ($empleado['sucursal_id'] != $sucursalActual['id']) {
                // Está en otra sucursal, requiere código de gerente
                if (empty($codigoGerente)) {
                    echo json_encode([
                        'success' => false, 
                        'message' => 'Estás en una sucursal diferente a la asignada. Ingresa el código del gerente para autorizar.',
                        'requiere_gerente' => true
                    ]);
                    return;
                }
                
                // Validar código de gerente
                $stmtGerente = $db->prepare("
                    SELECT e.id, e.codigo_empleado,
                           CONCAT(e.nombres, ' ', e.apellido_paterno) as nombre_completo
                    FROM empleados e
                    INNER JOIN sucursal_gerentes sg ON e.id = sg.empleado_id
                    WHERE e.codigo_empleado = ? 
                    AND sg.sucursal_id = ? 
                    AND sg.activo = 1
                    AND e.estatus = 'Activo'
                ");
                $stmtGerente->execute([$codigoGerente, $sucursalActual['id']]);
                $gerente = $stmtGerente->fetch();
                
                if (!$gerente) {
                    echo json_encode([
                        'success' => false, 
                        'message' => 'Código de gerente no válido para esta sucursal'
                    ]);
                    return;
                }
                
                $gerenteAutorizadorId = $gerente['id'];
            }
            
            // Guardar foto
            $fotoPath = null;
            if (!empty($fotoBase64)) {
                $fotoPath = $this->guardarFoto($fotoBase64, $empleado['id'], $tipoRegistro);
            }
            
            $fechaHoy = date('Y-m-d');
            $horaActual = date('Y-m-d H:i:s');
            
            if ($tipoRegistro === 'entrada') {
                // Verificar si ya tiene registro de entrada hoy
                $stmtCheck = $db->prepare("SELECT id FROM asistencias WHERE empleado_id = ? AND fecha = ? AND hora_entrada IS NOT NULL");
                $stmtCheck->execute([$empleado['id'], $fechaHoy]);
                
                if ($stmtCheck->fetch()) {
                    echo json_encode(['success' => false, 'message' => 'Ya registraste tu entrada hoy']);
                    return;
                }
                
                // Registrar entrada
                $stmtInsert = $db->prepare("
                    INSERT INTO asistencias (
                        empleado_id, fecha, hora_entrada, estatus, 
                        dispositivo_entrada, foto_entrada, sucursal_id, gerente_autorizador_id
                    ) VALUES (?, ?, ?, 'Presente', ?, ?, ?, ?)
                ");
                $stmtInsert->execute([
                    $empleado['id'], 
                    $fechaHoy, 
                    $horaActual, 
                    'Web-' . $sucursalActual['codigo'],
                    $fotoPath,
                    $sucursalActual['id'],
                    $gerenteAutorizadorId
                ]);
                
                // Activar dispositivo Shelly
                $activacionShelly = $this->activarDispositivoShelly($sucursalActual['id'], 'Entrada');
                
                // Log error si Shelly no se activa pero continuar con registro
                if (!$activacionShelly['activado']) {
                    error_log('Advertencia: Shelly no activado para entrada - ' . $activacionShelly['mensaje']);
                }
                
                // Calcular horas extras acumuladas en el periodo actual
                $horasExtras = $this->calcularHorasExtras($empleado['id']);
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Entrada registrada exitosamente',
                    'tipo' => 'entrada',
                    'empleado' => $empleado['nombres'] . ' ' . $empleado['apellido_paterno'],
                    'hora' => date('H:i:s'),
                    'horas_extras_acumuladas' => $horasExtras,
                    'activacion_shelly' => $activacionShelly
                ]);
                
            } elseif ($tipoRegistro === 'salida') {
                // Buscar registro de entrada de hoy
                $stmtCheck = $db->prepare("
                    SELECT id, hora_entrada 
                    FROM asistencias 
                    WHERE empleado_id = ? AND fecha = ? AND hora_entrada IS NOT NULL
                ");
                $stmtCheck->execute([$empleado['id'], $fechaHoy]);
                $asistencia = $stmtCheck->fetch();
                
                if (!$asistencia) {
                    echo json_encode(['success' => false, 'message' => 'No tienes registro de entrada hoy']);
                    return;
                }
                
                if (!empty($asistencia['hora_salida'])) {
                    echo json_encode(['success' => false, 'message' => 'Ya registraste tu salida hoy']);
                    return;
                }
                
                // Calcular horas trabajadas
                $horaEntrada = new DateTime($asistencia['hora_entrada']);
                $horaSalida = new DateTime($horaActual);
                $diff = $horaEntrada->diff($horaSalida);
                $horasTrabajadas = $diff->h + ($diff->i / 60);
                
                // Calcular horas extras si trabajó más de sus horas normales
                $horasNormales = $empleado['horas_laborales'] ?? 8;
                $horasExtras = max(0, $horasTrabajadas - $horasNormales);
                
                // Actualizar registro de salida
                $stmtUpdate = $db->prepare("
                    UPDATE asistencias SET 
                        hora_salida = ?, 
                        horas_trabajadas = ?, 
                        horas_extra = ?,
                        dispositivo_salida = ?,
                        foto_salida = ?
                    WHERE id = ?
                ");
                $stmtUpdate->execute([
                    $horaActual,
                    $horasTrabajadas,
                    $horasExtras,
                    'Web-' . $sucursalActual['codigo'],
                    $fotoPath,
                    $asistencia['id']
                ]);
                
                // Activar dispositivo Shelly
                $activacionShelly = $this->activarDispositivoShelly($sucursalActual['id'], 'Salida');
                
                // Log error si Shelly no se activa pero continuar con registro
                if (!$activacionShelly['activado']) {
                    error_log('Advertencia: Shelly no activado para salida - ' . $activacionShelly['mensaje']);
                }
                
                // Calcular horas extras acumuladas en el periodo actual
                $horasExtrasAcumuladas = $this->calcularHorasExtras($empleado['id']);
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Salida registrada exitosamente',
                    'tipo' => 'salida',
                    'empleado' => $empleado['nombres'] . ' ' . $empleado['apellido_paterno'],
                    'hora' => date('H:i:s'),
                    'horas_trabajadas' => round($horasTrabajadas, 2),
                    'horas_extras_hoy' => round($horasExtras, 2),
                    'horas_extras_acumuladas' => $horasExtrasAcumuladas,
                    'activacion_shelly' => $activacionShelly
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Tipo de registro no válido']);
            }
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Guardar foto de asistencia
     */
    private function guardarFoto($fotoBase64, $empleadoId, $tipo) {
        try {
            // Crear directorio si no existe
            $uploadDir = BASE_PATH . 'uploads/asistencias/' . date('Y-m');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Remover el prefijo data:image/...;base64,
            $fotoBase64 = preg_replace('/^data:image\/\w+;base64,/', '', $fotoBase64);
            $fotoData = base64_decode($fotoBase64);
            
            // Generar nombre de archivo único
            $filename = $empleadoId . '_' . $tipo . '_' . date('Ymd_His') . '.jpg';
            $filepath = $uploadDir . '/' . $filename;
            
            // Guardar archivo
            file_put_contents($filepath, $fotoData);
            
            // Retornar ruta relativa
            return 'uploads/asistencias/' . date('Y-m') . '/' . $filename;
            
        } catch (Exception $e) {
            error_log('Error al guardar foto: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Calcular horas extras acumuladas en el periodo de nómina actual
     */
    private function calcularHorasExtras($empleadoId) {
        $db = Database::getInstance()->getConnection();
        
        // Obtener periodo de nómina actual (abierto o en proceso)
        $stmtPeriodo = $db->query("
            SELECT id, fecha_inicio, fecha_fin 
            FROM periodos_nomina 
            WHERE estatus IN ('Abierto', 'En Proceso')
            ORDER BY fecha_inicio DESC
            LIMIT 1
        ");
        $periodo = $stmtPeriodo->fetch();
        
        if (!$periodo) {
            // Si no hay periodo, usar el mes actual
            $fechaInicio = date('Y-m-01');
            $fechaFin = date('Y-m-t');
        } else {
            $fechaInicio = $periodo['fecha_inicio'];
            $fechaFin = $periodo['fecha_fin'];
        }
        
        // Sumar horas extras del periodo
        $stmt = $db->prepare("
            SELECT COALESCE(SUM(horas_extra), 0) as total_horas_extras
            FROM asistencias
            WHERE empleado_id = ? 
            AND fecha BETWEEN ? AND ?
        ");
        $stmt->execute([$empleadoId, $fechaInicio, $fechaFin]);
        $result = $stmt->fetch();
        
        return round($result['total_horas_extras'] ?? 0, 2);
    }
    
    /**
     * Activar dispositivo Shelly asignado a la sucursal
     */
    private function activarDispositivoShelly($sucursalId, $tipoAccion) {
        try {
            $db = Database::getInstance()->getConnection();
            
            // Buscar dispositivos Shelly de la sucursal para este tipo de acción
            $stmt = $db->prepare("
                SELECT ds.* 
                FROM dispositivos_shelly ds
                INNER JOIN sucursal_dispositivos sd ON ds.id = sd.dispositivo_shelly_id
                WHERE sd.sucursal_id = ? 
                AND sd.activo = 1 
                AND ds.habilitado = 1
                AND (sd.tipo_accion = ? OR sd.tipo_accion = 'Ambos')
                LIMIT 1
            ");
            $stmt->execute([$sucursalId, $tipoAccion]);
            $dispositivo = $stmt->fetch();
            
            if (!$dispositivo) {
                return ['activado' => false, 'mensaje' => 'No hay dispositivo configurado'];
            }
            
            // Llamar a la API de Shelly Cloud para activar el dispositivo
            $resultado = $this->activarShellyCloud($dispositivo);
            
            return $resultado;
            
        } catch (Exception $e) {
            error_log('Error al activar Shelly: ' . $e->getMessage());
            return ['activado' => false, 'mensaje' => 'Error al activar dispositivo'];
        }
    }
    
    /**
     * Activar dispositivo Shelly via Cloud API
     */
    private function activarShellyCloud($dispositivo) {
        try {
            $url = rtrim($dispositivo['servidor_cloud'], '/') . '/device/relay/control';
            
            $data = [
                'id' => $dispositivo['device_id'],
                'auth_key' => $dispositivo['token_autenticacion'],
                'channel' => $dispositivo['canal_entrada'],
                'turn' => 'on'
            ];
            
            // Si tiene duración de pulso configurada
            if ($dispositivo['duracion_pulso'] > 0) {
                $data['turn'] = 'on';
                $data['timer'] = $dispositivo['duracion_pulso'] / 1000; // Convertir ms a segundos
            }
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode == 200) {
                return ['activado' => true, 'mensaje' => 'Dispositivo activado correctamente'];
            } else {
                return ['activado' => false, 'mensaje' => 'Error en respuesta del dispositivo'];
            }
            
        } catch (Exception $e) {
            error_log('Error en activarShellyCloud: ' . $e->getMessage());
            return ['activado' => false, 'mensaje' => 'Error al conectar con dispositivo'];
        }
    }
}
