<?php
/**
 * Controlador de Empleados
 */

class EmpleadosController {
    
    /**
     * Listar todos los empleados
     */
    public function index() {
        AuthController::check();
        
        $empleadoModel = new Empleado();
        
        // Filtros
        $filters = [];
        if (isset($_GET['estatus'])) {
            $filters['estatus'] = $_GET['estatus'];
        }
        if (isset($_GET['departamento'])) {
            $filters['departamento'] = $_GET['departamento'];
        }
        if (isset($_GET['sucursal'])) {
            $filters['sucursal'] = $_GET['sucursal'];
        }
        if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
            $filters['search'] = trim($_GET['search']);
        }
        
        $empleados = $empleadoModel->getAll($filters);
        $departamentos = $empleadoModel->getDepartments();
        
        // Obtener sucursales activas
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT id, nombre FROM sucursales WHERE activo = 1 ORDER BY nombre");
        $sucursales = $stmt->fetchAll();
        
        $data = [
            'title' => 'Gestión de Empleados',
            'empleados' => $empleados,
            'departamentos' => $departamentos,
            'sucursales' => $sucursales,
            'filters' => $filters
        ];
        
        ob_start();
        require_once BASE_PATH . 'app/views/empleados/index.php';
        $content = ob_get_clean();
        
        require_once BASE_PATH . 'app/views/layouts/main.php';
    }
    
    /**
     * Formulario para crear empleado
     */
    public function crear() {
        AuthController::checkRole(['admin', 'rrhh']);
        
        $error = '';
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Cargar validador
            require_once BASE_PATH . 'app/helpers/Validator.php';
            $validator = new Validator();
            
            // Limpiar datos
            $data = Validator::sanitizeArray($_POST);
            
            // Limpiar teléfonos
            if (!empty($data['telefono'])) {
                $data['telefono'] = Validator::limpiarTelefono($data['telefono']);
            }
            if (!empty($data['celular'])) {
                $data['celular'] = Validator::limpiarTelefono($data['celular']);
            }
            
            // Verificar si se debe omitir validaciones (modo prueba)
            $omitirValidaciones = isset($_POST['omitir_validaciones']) && $_POST['omitir_validaciones'] === '1';
            
            // Validar datos
            if ($validator->validarEmpleado($data, $omitirValidaciones)) {
                $empleadoModel = new Empleado();
                
                // Generar número de empleado automático
                $db = Database::getInstance()->getConnection();
                $stmt = $db->query("SELECT MAX(CAST(SUBSTRING(numero_empleado, 4) AS UNSIGNED)) as max_num FROM empleados");
                $result = $stmt->fetch();
                $nextNum = ($result['max_num'] ?? 0) + 1;
                $numeroEmpleado = 'EMP' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
                
                // Generar código de empleado (6 dígitos) - formato: 183XXX
                $stmtCodigo = $db->query("SELECT MAX(CAST(codigo_empleado AS UNSIGNED)) as max_codigo FROM empleados WHERE codigo_empleado LIKE '183%'");
                $resultCodigo = $stmtCodigo->fetch();
                $nextCodigo = ($resultCodigo['max_codigo'] ?? 183000) + 1;
                $codigoEmpleado = str_pad($nextCodigo, 6, '0', STR_PAD_LEFT);
                
                $dataEmpleado = [
                    'numero_empleado' => $numeroEmpleado,
                    'codigo_empleado' => $codigoEmpleado,
                    'nombres' => $data['nombres'],
                    'apellido_paterno' => $data['apellido_paterno'],
                    'apellido_materno' => $data['apellido_materno'] ?? null,
                    'curp' => !empty($data['curp']) ? strtoupper($data['curp']) : null,
                    'rfc' => !empty($data['rfc']) ? strtoupper($data['rfc']) : null,
                    'nss' => $data['nss'] ?? null,
                    'fecha_nacimiento' => $data['fecha_nacimiento'] ?? null,
                    'genero' => $data['genero'] ?? null,
                    'estado_civil' => $data['estado_civil'] ?? null,
                    'email_personal' => $data['email_personal'] ?? null,
                    'telefono' => $data['telefono'] ?? null,
                    'celular' => $data['celular'] ?? null,
                    'calle' => $data['calle'] ?? null,
                    'numero_exterior' => $data['numero_exterior'] ?? null,
                    'numero_interior' => $data['numero_interior'] ?? null,
                    'colonia' => $data['colonia'] ?? null,
                    'codigo_postal' => $data['codigo_postal'] ?? null,
                    'municipio' => $data['municipio'] ?? 'Querétaro',
                    'estado' => $data['estado'] ?? 'Querétaro',
                    'fecha_ingreso' => $data['fecha_ingreso'],
                    'tipo_contrato' => $data['tipo_contrato'],
                    'departamento' => $data['departamento'],
                    'puesto' => $data['puesto'],
                    'salario_diario' => $data['salario_diario'] ?? 0,
                    'salario_mensual' => $data['salario_mensual'] ?? 0,
                    'sucursal_id' => $data['sucursal_id'] ?? null,
                    'turno_id' => $data['turno_id'] ?? null,
                    'estatus' => 'Activo'
                ];
                
                if ($empleadoModel->create($dataEmpleado)) {
                    $success = 'Empleado creado exitosamente';
                    // Redirigir después de 2 segundos
                    header("refresh:2;url=" . BASE_URL . "empleados");
                } else {
                    $error = 'Error al crear empleado en la base de datos';
                }
            } else {
                // Obtener errores de validación
                $errores = $validator->getErrors();
                $error = 'Errores de validación: ' . implode(', ', $errores);
            }
        }
        
        // Obtener datos para los select
        $db = Database::getInstance()->getConnection();
        
        // Obtener sucursales activas
        $stmtSucursales = $db->query("SELECT id, nombre, codigo FROM sucursales WHERE activo = 1 ORDER BY nombre");
        $sucursales = $stmtSucursales->fetchAll();
        
        // Obtener turnos activos
        $stmtTurnos = $db->query("SELECT id, nombre, hora_entrada, hora_salida FROM turnos WHERE activo = 1 ORDER BY nombre");
        $turnos = $stmtTurnos->fetchAll();
        
        // Obtener departamentos activos
        $stmtDepartamentos = $db->query("SELECT id, nombre FROM departamentos WHERE activo = 1 ORDER BY nombre");
        $departamentos = $stmtDepartamentos->fetchAll();
        
        // Obtener puestos activos
        $stmtPuestos = $db->query("SELECT id, nombre, departamento_id FROM puestos WHERE activo = 1 ORDER BY nombre");
        $puestos = $stmtPuestos->fetchAll();
        
        $data = [
            'title' => 'Nuevo Empleado',
            'error' => $error,
            'success' => $success,
            'sucursales' => $sucursales,
            'turnos' => $turnos,
            'departamentos' => $departamentos,
            'puestos' => $puestos
        ];
        
        ob_start();
        require_once BASE_PATH . 'app/views/empleados/crear.php';
        $content = ob_get_clean();
        
        require_once BASE_PATH . 'app/views/layouts/main.php';
    }
    
    /**
     * Ver detalles de empleado
     */
    public function ver() {
        AuthController::check();
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            redirect('empleados');
        }
        
        $empleadoModel = new Empleado();
        $empleado = $empleadoModel->getById($id);
        
        if (!$empleado) {
            redirect('empleados');
        }
        
        // Obtener historial laboral
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM historial_laboral WHERE empleado_id = ? ORDER BY fecha_evento DESC");
        $stmt->execute([$id]);
        $historial = $stmt->fetchAll();
        
        // Obtener documentos
        $stmt = $db->prepare("SELECT * FROM documentos_empleados WHERE empleado_id = ? ORDER BY fecha_subida DESC");
        $stmt->execute([$id]);
        $documentos = $stmt->fetchAll();
        
        $data = [
            'title' => 'Detalles del Empleado',
            'empleado' => $empleado,
            'historial' => $historial,
            'documentos' => $documentos
        ];
        
        ob_start();
        require_once BASE_PATH . 'app/views/empleados/ver.php';
        $content = ob_get_clean();
        
        require_once BASE_PATH . 'app/views/layouts/main.php';
    }
    
    /**
     * Editar empleado
     */
    public function editar() {
        AuthController::checkRole(['admin', 'rrhh']);
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            redirect('empleados');
        }
        
        $empleadoModel = new Empleado();
        $empleado = $empleadoModel->getById($id);
        
        if (!$empleado) {
            redirect('empleados');
        }
        
        $error = '';
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombres' => $_POST['nombres'],
                'apellido_paterno' => $_POST['apellido_paterno'],
                'apellido_materno' => $_POST['apellido_materno'] ?? null,
                'curp' => !empty($_POST['curp']) ? strtoupper($_POST['curp']) : null,
                'rfc' => !empty($_POST['rfc']) ? strtoupper($_POST['rfc']) : null,
                'nss' => $_POST['nss'] ?? null,
                'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? null,
                'genero' => $_POST['genero'] ?? null,
                'estado_civil' => $_POST['estado_civil'] ?? null,
                'email_personal' => $_POST['email_personal'] ?? null,
                'telefono' => $_POST['telefono'] ?? null,
                'celular' => $_POST['celular'] ?? null,
                'calle' => $_POST['calle'] ?? null,
                'numero_exterior' => $_POST['numero_exterior'] ?? null,
                'numero_interior' => $_POST['numero_interior'] ?? null,
                'colonia' => $_POST['colonia'] ?? null,
                'codigo_postal' => $_POST['codigo_postal'] ?? null,
                'municipio' => $_POST['municipio'] ?? null,
                'estado' => $_POST['estado'] ?? null,
                'fecha_ingreso' => $_POST['fecha_ingreso'] ?? null,
                'tipo_contrato' => $_POST['tipo_contrato'] ?? null,
                'departamento' => $_POST['departamento'],
                'puesto' => $_POST['puesto'],
                'salario_diario' => $_POST['salario_diario'] ?? null,
                'salario_mensual' => $_POST['salario_mensual'],
                'sucursal_id' => $_POST['sucursal_id'] ?? null,
                'turno_id' => $_POST['turno_id'] ?? null,
                'banco' => $_POST['banco'] ?? null,
                'numero_cuenta' => $_POST['numero_cuenta'] ?? null,
                'clabe_interbancaria' => $_POST['clabe_interbancaria'] ?? null,
                'estatus' => $_POST['estatus']
            ];
            
            if ($empleadoModel->update($id, $data)) {
                $success = 'Empleado actualizado exitosamente';
                $empleado = $empleadoModel->getById($id);
            } else {
                $error = 'Error al actualizar empleado';
            }
        }
        
        // Obtener datos para los select
        $db = Database::getInstance()->getConnection();
        
        // Obtener sucursales activas
        $stmtSucursales = $db->query("SELECT id, nombre, codigo FROM sucursales WHERE activo = 1 ORDER BY nombre");
        $sucursales = $stmtSucursales->fetchAll();
        
        // Obtener turnos activos
        $stmtTurnos = $db->query("SELECT id, nombre, hora_entrada, hora_salida FROM turnos WHERE activo = 1 ORDER BY nombre");
        $turnos = $stmtTurnos->fetchAll();
        
        // Obtener departamentos activos
        $stmtDepartamentos = $db->query("SELECT id, nombre FROM departamentos WHERE activo = 1 ORDER BY nombre");
        $departamentos = $stmtDepartamentos->fetchAll();
        
        // Obtener puestos activos
        $stmtPuestos = $db->query("SELECT id, nombre, departamento_id FROM puestos WHERE activo = 1 ORDER BY nombre");
        $puestos = $stmtPuestos->fetchAll();
        
        $data = [
            'title' => 'Editar Empleado',
            'empleado' => $empleado,
            'error' => $error,
            'success' => $success,
            'sucursales' => $sucursales,
            'turnos' => $turnos,
            'departamentos' => $departamentos,
            'puestos' => $puestos
        ];
        
        ob_start();
        require_once BASE_PATH . 'app/views/empleados/editar.php';
        $content = ob_get_clean();
        
        require_once BASE_PATH . 'app/views/layouts/main.php';
    }
    
    /**
     * Generar carta de recomendación
     */
    public function cartaRecomendacion() {
        AuthController::check();
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            redirect('empleados');
        }
        
        $empleadoModel = new Empleado();
        $empleado = $empleadoModel->getById($id);
        
        if (!$empleado) {
            redirect('empleados');
        }
        
        // Obtener configuraciones del sistema para logo
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT clave, valor FROM configuraciones_globales WHERE grupo = 'sitio'");
        $configuraciones = $stmt->fetchAll();
        $configs = [];
        foreach ($configuraciones as $config) {
            $configs[$config['clave']] = $config['valor'];
        }
        
        header('Content-Type: text/html; charset=utf-8');
        require_once BASE_PATH . 'app/views/empleados/carta_recomendacion.php';
    }
    
    /**
     * Subir documento del empleado
     */
    public function subirDocumento() {
        AuthController::checkRole(['admin', 'rrhh']);
        
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        $empleadoId = $_POST['empleado_id'] ?? null;
        $tipoDocumento = $_POST['tipo_documento'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        
        if (!$empleadoId) {
            echo json_encode(['success' => false, 'message' => 'ID de empleado no proporcionado']);
            return;
        }
        
        // Validar que se haya subido un archivo
        if (!isset($_FILES['documento']) || $_FILES['documento']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'No se ha seleccionado ningún archivo o hubo un error al subirlo']);
            return;
        }
        
        $file = $_FILES['documento'];
        
        // Validar tamaño (max 10MB)
        if ($file['size'] > 10 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'El archivo es demasiado grande. Máximo 10MB']);
            return;
        }
        
        // Validar extensión
        $allowedExtensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($fileExtension, $allowedExtensions)) {
            echo json_encode(['success' => false, 'message' => 'Tipo de archivo no permitido. Permitidos: PDF, DOC, DOCX, JPG, PNG']);
            return;
        }
        
        try {
            // Crear directorio si no existe
            $uploadDir = BASE_PATH . 'uploads/documentos_empleados/' . $empleadoId;
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Generar nombre único
            $nombreArchivo = uniqid() . '_' . time() . '.' . $fileExtension;
            $rutaCompleta = $uploadDir . '/' . $nombreArchivo;
            
            // Mover archivo
            if (!move_uploaded_file($file['tmp_name'], $rutaCompleta)) {
                echo json_encode(['success' => false, 'message' => 'Error al guardar el archivo']);
                return;
            }
            
            // Guardar en base de datos
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("
                INSERT INTO documentos_empleados (empleado_id, tipo_documento, nombre_archivo, ruta_archivo, descripcion, usuario_subida_id)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $rutaRelativa = 'uploads/documentos_empleados/' . $empleadoId . '/' . $nombreArchivo;
            $usuarioId = $_SESSION['usuario_id'] ?? null;
            
            $stmt->execute([
                $empleadoId,
                $tipoDocumento,
                $file['name'],
                $rutaRelativa,
                $descripcion,
                $usuarioId
            ]);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Documento subido exitosamente',
                'documento' => [
                    'tipo_documento' => $tipoDocumento,
                    'nombre_archivo' => $file['name'],
                    'fecha_subida' => date('Y-m-d H:i:s')
                ]
            ]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Descargar documento del empleado
     */
    public function descargarDocumento() {
        AuthController::check();
        
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            die('ID de documento no proporcionado');
        }
        
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM documentos_empleados WHERE id = ?");
        $stmt->execute([$id]);
        $documento = $stmt->fetch();
        
        if (!$documento) {
            die('Documento no encontrado');
        }
        
        $rutaArchivo = BASE_PATH . $documento['ruta_archivo'];
        
        if (!file_exists($rutaArchivo)) {
            die('Archivo no encontrado en el servidor');
        }
        
        // Enviar headers para descarga
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $documento['nombre_archivo'] . '"');
        header('Content-Length: ' . filesize($rutaArchivo));
        readfile($rutaArchivo);
        exit;
    }
    
    /**
     * Generar constancia de trabajo
     */
    public function constancia() {
        AuthController::check();
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            redirect('empleados');
        }
        
        $empleadoModel = new Empleado();
        $empleado = $empleadoModel->getById($id);
        
        if (!$empleado) {
            redirect('empleados');
        }
        
        // Obtener configuraciones del sistema para logo
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT clave, valor FROM configuraciones_globales WHERE grupo = 'sitio'");
        $configuraciones = $stmt->fetchAll();
        $configs = [];
        foreach ($configuraciones as $config) {
            $configs[$config['clave']] = $config['valor'];
        }
        
        header('Content-Type: text/html; charset=utf-8');
        require_once BASE_PATH . 'app/views/empleados/constancia.php';
    }
    
    /**
     * Cálculo rápido de nómina de un empleado
     */
    public function calculoRapidoNomina() {
        AuthController::check();
        header('Content-Type: application/json');
        
        $empleadoId = $_GET['id'] ?? null;
        
        if (!$empleadoId) {
            echo json_encode(['success' => false, 'message' => 'ID de empleado no proporcionado']);
            exit;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Obtener empleado
            $empleadoModel = new Empleado();
            $empleado = $empleadoModel->getById($empleadoId);
            
            if (!$empleado) {
                echo json_encode(['success' => false, 'message' => 'Empleado no encontrado']);
                exit;
            }
            
            // Obtener último periodo calculado
            $stmt = $db->prepare("
                SELECT MAX(pn.fecha_fin) as ultima_fecha 
                FROM periodos_nomina pn
                INNER JOIN nomina_detalle nd ON pn.id = nd.periodo_id
                WHERE nd.empleado_id = ? AND pn.estatus IN ('Procesado', 'Pagado', 'Cerrado')
            ");
            $stmt->execute([$empleadoId]);
            $ultimoPeriodo = $stmt->fetch();
            
            $fechaInicio = $ultimoPeriodo && $ultimoPeriodo['ultima_fecha'] 
                ? date('Y-m-d', strtotime($ultimoPeriodo['ultima_fecha'] . ' +1 day'))
                : $empleado['fecha_ingreso'];
            $fechaFin = date('Y-m-d');
            
            // Calcular días trabajados desde último periodo
            $stmt = $db->prepare("
                SELECT DATE(fecha) as fecha, entrada, salida, horas_trabajadas, estatus
                FROM asistencias 
                WHERE empleado_id = ? 
                AND fecha BETWEEN ? AND ?
                ORDER BY fecha DESC
            ");
            $stmt->execute([$empleadoId, $fechaInicio, $fechaFin]);
            $asistencias = $stmt->fetchAll();
            
            $diasTrabajados = 0;
            $horasNormales = 0;
            $horasExtras = 0;
            
            foreach ($asistencias as $asistencia) {
                if (in_array($asistencia['estatus'], ['Presente', 'Retardo'])) {
                    $diasTrabajados++;
                    $horas = floatval($asistencia['horas_trabajadas']);
                    if ($horas > 8) {
                        $horasNormales += 8;
                        $horasExtras += ($horas - 8);
                    } else {
                        $horasNormales += $horas;
                    }
                }
            }
            
            // Obtener incidencias
            $stmt = $db->prepare("
                SELECT tipo_incidencia, motivo, fecha_incidencia as fecha, monto
                FROM incidencias_nomina
                WHERE empleado_id = ? 
                AND fecha_incidencia BETWEEN ? AND ?
                AND estatus = 'Aprobado'
                ORDER BY fecha_incidencia DESC
            ");
            $stmt->execute([$empleadoId, $fechaInicio, $fechaFin]);
            $incidencias = $stmt->fetchAll();
            
            // Obtener deducciones activas (si existe tabla deducciones_empleado)
            $deducciones = [];
            
            // Calcular montos
            $salarioDiario = floatval($empleado['salario_diario']);
            $salarioMensual = floatval($empleado['salario_mensual']);
            $salarioBase = $diasTrabajados * $salarioDiario;
            
            // Calcular pago de horas extras (doble) y bonos/descuentos desde incidencias
            $valorHoraExtra = ($salarioDiario / 8) * 2;
            $pagoHorasExtras = $horasExtras * $valorHoraExtra;
            $bonos = 0;
            $descuentos = 0;
            
            foreach ($incidencias as $inc) {
                if ($inc['tipo_incidencia'] === 'Bono') {
                    $bonos += floatval($inc['monto']);
                } elseif ($inc['tipo_incidencia'] === 'Descuento') {
                    $descuentos += floatval($inc['monto']);
                }
            }
            
            $totalPercepciones = $salarioBase + $pagoHorasExtras + $bonos;
            
            // Calcular deducciones estimadas
            require_once BASE_PATH . 'app/services/NominaService.php';
            $nominaService = new NominaService();
            
            $isr = $nominaService->calcularISR($totalPercepciones);
            $subsidio = $nominaService->calcularSubsidioEmpleo($totalPercepciones);
            $isrNeto = max(0, $isr - $subsidio);
            
            $cuotasIMSS = $nominaService->calcularIMSS($salarioMensual);
            
            // IMSS proporcional a días trabajados del periodo actual
            $diasEnPeriodo = (new DateTime($fechaFin))->diff(new DateTime($fechaInicio))->days + 1;
            $diasMensual = 30; // Base mensual estándar para cálculos
            $factorProporcion = $diasEnPeriodo / $diasMensual;
            $imssProporcionado = $cuotasIMSS['total'] * $factorProporcion;
            
            $totalDeducciones = $isrNeto + $imssProporcionado + $descuentos;
            
            $totalNeto = $totalPercepciones - $totalDeducciones;
            
            echo json_encode([
                'success' => true,
                'empleado' => [
                    'nombre' => $empleado['nombre_completo'],
                    'numero' => $empleado['numero_empleado'],
                    'puesto' => $empleado['puesto']
                ],
                'periodo' => [
                    'fecha_inicio' => $fechaInicio,
                    'fecha_fin' => $fechaFin
                ],
                'asistencias' => [
                    'dias_trabajados' => $diasTrabajados,
                    'horas_normales' => round($horasNormales, 2),
                    'horas_extras' => round($horasExtras, 2),
                    'detalle' => $asistencias
                ],
                'incidencias' => $incidencias,
                'deducciones' => $deducciones,
                'calculos' => [
                    'salario_base' => round($salarioBase, 2),
                    'pago_horas_extras' => round($pagoHorasExtras, 2),
                    'bonos' => round($bonos, 2),
                    'total_percepciones' => round($totalPercepciones, 2),
                    'isr' => round($isrNeto, 2),
                    'imss' => round($imssProporcionado, 2),
                    'descuentos' => round($descuentos, 2),
                    'total_deducciones' => round($totalDeducciones, 2),
                    'total_neto' => round($totalNeto, 2)
                ]
            ]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
}
