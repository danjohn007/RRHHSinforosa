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
        
        // Make url_publica available to the view
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
                
                // Activar dispositivo Shelly para Entrada
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
                
                // Activar dispositivo Shelly para Salida
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
            
            // Buscar área de trabajo correspondiente al tipo de acción con dispositivo asignado
            $stmt = $db->prepare("
                SELECT sat.*, ds.* 
                FROM sucursal_areas_trabajo sat
                INNER JOIN dispositivos_shelly ds ON sat.dispositivo_shelly_id = ds.id
                WHERE sat.sucursal_id = ? 
                AND sat.activo = 1
                AND ds.habilitado = 1
                AND sat.nombre = ?
                LIMIT 1
            ");
            $stmt->execute([$sucursalId, $tipoAccion]);
            $area = $stmt->fetch();
            
            if (!$area) {
                // Fallback: Buscar dispositivo asignado directamente a la sucursal (compatibilidad con versión anterior)
                $stmt = $db->prepare("
                    SELECT ds.*, sd.tipo_accion 
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
                    return ['activado' => false, 'mensaje' => 'No hay dispositivo configurado para ' . $tipoAccion];
                }
                
                // Determinar canal según tipo de acción
                $canal = ($tipoAccion === 'Entrada') ? $dispositivo['canal_entrada'] : $dispositivo['canal_salida'];
                
                // Llamar a la API de Shelly Cloud para activar el dispositivo
                $resultado = $this->activarShellyCloud($dispositivo, $canal);
                
                return $resultado;
            }
            
            // Usar configuración del área de trabajo
            $canal = $area['canal_asignado'];
            
            // Llamar a la API de Shelly Cloud para activar el dispositivo
            $resultado = $this->activarShellyCloud($area, $canal);
            
            return $resultado;
            
        } catch (Exception $e) {
            error_log('Error al activar Shelly: ' . $e->getMessage());
            return ['activado' => false, 'mensaje' => 'Error al activar dispositivo: ' . $e->getMessage()];
        }
    }
    
    /**
     * Activar dispositivo Shelly via Cloud API
     */
    private function activarShellyCloud($dispositivo, $canal = null) {
        try {
            // Validar datos requeridos
            if (empty($dispositivo['device_id']) || empty($dispositivo['token_autenticacion']) || empty($dispositivo['servidor_cloud'])) {
                return ['activado' => false, 'mensaje' => 'Configuración de dispositivo incompleta'];
            }
            
            $url = rtrim($dispositivo['servidor_cloud'], '/') . '/device/relay/control';
            
            // Usar el canal especificado o el canal_entrada por defecto
            $canalActivo = ($canal !== null) ? $canal : ($dispositivo['canal_entrada'] ?? 0);
            
            // Validar canal
            if ($canal < 0 || $canal > 3) {
                return ['activado' => false, 'mensaje' => 'Canal inválido (debe ser 0-3)'];
            }
            
            $data = [
                'id' => $dispositivo['device_id'],
                'auth_key' => $dispositivo['token_autenticacion'],
                'channel' => (int)$canalActivo,
                'turn' => 'on'
            ];
            
            // Si tiene duración de pulso configurada
            if (!empty($dispositivo['duracion_pulso']) && $dispositivo['duracion_pulso'] > 0) {
                $data['turn'] = 'on';
                $data['timer'] = $dispositivo['duracion_pulso'] / 1000; // Convertir ms a segundos
            }
            
            // Sanitizar token para prevenir inyección de encabezados
            $authToken = str_replace(["\r", "\n"], '', $dispositivo['token_autenticacion']);
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $authToken,
                'Content-Type: application/x-www-form-urlencoded'
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Seguir redirects HTTP 301/302
            curl_setopt($ch, CURLOPT_MAXREDIRS, 5); // Máximo 5 redirects
            // Solo deshabilitar SSL verification en desarrollo
            if (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE === true) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            }
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
            
            // Log para debugging
            error_log("Shelly Cloud Request: " . json_encode($data));
            error_log("Shelly Cloud Response (HTTP $httpCode): " . substr($response, 0, LOG_RESPONSE_MAX_LENGTH));
            
            if ($httpCode == 200) {
                $responseData = json_decode($response, true);
                
                // Verificar si la respuesta es JSON válido
                if (json_last_error() !== JSON_ERROR_NONE) {
                    error_log("Shelly Cloud - Invalid JSON response: " . json_last_error_msg());
                    return ['activado' => false, 'mensaje' => 'Respuesta inválida del dispositivo'];
                }
                
                // Verificar si la respuesta contiene error
                if (isset($responseData['isok']) && $responseData['isok'] === true) {
                    return ['activado' => true, 'mensaje' => 'Dispositivo activado correctamente'];
                } elseif (isset($responseData['errors'])) {
                    return ['activado' => false, 'mensaje' => 'Error del dispositivo: ' . json_encode($responseData['errors'])];
                }
                return ['activado' => true, 'mensaje' => 'Dispositivo activado'];
            } else {
                $errorMsg = !empty($curlError) ? $curlError : "HTTP {$httpCode}";
                
                // Intentar decodificar respuesta de error si existe
                if (!empty($response)) {
                    $responseData = json_decode($response, true);
                    if (json_last_error() === JSON_ERROR_NONE && isset($responseData['error'])) {
                        $errorMsg = sprintf("%s: %s", $errorMsg, $responseData['error']);
                    }
                }
                
                return ['activado' => false, 'mensaje' => "Error en respuesta del dispositivo: {$errorMsg}"];
            }
            
        } catch (Exception $e) {
            error_log('Error en activarShellyCloud: ' . $e->getMessage());
            return ['activado' => false, 'mensaje' => 'Error al conectar con dispositivo: ' . $e->getMessage()];
        }
    }
}
