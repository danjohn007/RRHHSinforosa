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
            
            // Obtener último periodo calculado (solo periodos hasta la fecha actual)
            $stmt = $db->prepare("
                SELECT MAX(pn.fecha_fin) as ultima_fecha 
                FROM periodos_nomina pn
                INNER JOIN nomina_detalle nd ON pn.id = nd.periodo_id
                WHERE nd.empleado_id = ? 
                AND pn.estatus IN ('Procesado', 'Pagado', 'Cerrado')
                AND pn.fecha_fin <= CURDATE()
            ");
            $stmt->execute([$empleadoId]);
            $ultimoPeriodo = $stmt->fetch();
            
            $fechaInicio = $ultimoPeriodo && $ultimoPeriodo['ultima_fecha'] 
                ? date('Y-m-d', strtotime($ultimoPeriodo['ultima_fecha'] . ' +1 day'))
                : $empleado['fecha_ingreso'];
            $fechaFin = date('Y-m-d'); // Fecha actual
            
            // Asegurar que la fecha de inicio no sea posterior a la fecha fin
            if (strtotime($fechaInicio) > strtotime($fechaFin)) {
                $fechaInicio = $fechaFin;
            }
            
            // Calcular días trabajados desde último periodo
            $stmt = $db->prepare("
                SELECT DATE(fecha) as fecha, hora_entrada, hora_salida, horas_trabajadas, horas_extra, estatus
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
                    $horas = floatval($asistencia['horas_trabajadas'] ?? 0);
                    $extra = floatval($asistencia['horas_extra'] ?? 0);
                    
                    // Si no hay horas extra registradas directamente, calcular en base a horas trabajadas
                    if ($extra > 0) {
                        $horasExtras += $extra;
                        $horasNormales += min($horas, 8);
                    } else if ($horas > 8) {
                        $horasNormales += 8;
                        $horasExtras += ($horas - 8);
                    } else {
                        $horasNormales += $horas;
                    }
                }
            }
            
            // Obtener incidencias
            $stmt = $db->prepare("
                SELECT tipo_incidencia, descripcion, fecha_incidencia as fecha, monto
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
            $salarioDiario = floatval($empleado['salario_diario'] ?? 0);
            $salarioMensual = floatval($empleado['salario_mensual'] ?? 0);
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
    
    /**
     * Descargar plantilla CSV para importación de empleados
     */
    public function descargarPlantilla() {
        AuthController::checkRole(['admin', 'rrhh']);
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="plantilla_importacion_empleados.csv"');
        
        $output = fopen('php://output', 'w');
        
        // BOM para UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Encabezados
        fputcsv($output, [
            'nombres',
            'apellido_paterno',
            'apellido_materno',
            'curp',
            'rfc',
            'nss',
            'fecha_nacimiento',
            'genero',
            'email_personal',
            'celular',
            'fecha_ingreso',
            'tipo_contrato',
            'departamento',
            'puesto',
            'salario_mensual'
        ]);
        
        // Datos de ejemplo
        fputcsv($output, [
            'Juan',
            'Pérez',
            'García',
            'PEGJ850101HQRRRN01',
            'PEGJ850101ABC',
            '12345678901',
            '1985-01-01',
            'M',
            'juan.perez@example.com',
            '4421234567',
            '2024-01-01',
            'Planta',
            'Ventas',
            'Vendedor',
            '10000'
        ]);
        
        fputcsv($output, [
            'María',
            'López',
            'Hernández',
            'LOHM900215MQRRRS02',
            'LOHM900215XYZ',
            '98765432109',
            '1990-02-15',
            'F',
            'maria.lopez@example.com',
            '4429876543',
            '2024-02-01',
            'Planta',
            'Administración',
            'Asistente',
            '8000'
        ]);
        
        fclose($output);
        exit;
    }
    
    /**
     * Importar empleados desde archivo CSV
     */
    public function importar() {
        AuthController::checkRole(['admin', 'rrhh']);
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }
        
        // Validar que se subió un archivo
        if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'No se recibió el archivo o hubo un error en la carga']);
            exit;
        }
        
        $archivo = $_FILES['archivo'];
        
        // Validar extensión
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        if ($extension !== 'csv') {
            echo json_encode(['success' => false, 'message' => 'El archivo debe ser formato CSV']);
            exit;
        }
        
        // Validar tamaño (máximo 5MB)
        if ($archivo['size'] > 5 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'El archivo no debe exceder 5MB']);
            exit;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            $db->beginTransaction();
            
            // Registrar importación
            $stmt = $db->prepare("
                INSERT INTO nomina_importaciones 
                (tipo, archivo_nombre, usuario_id, estatus)
                VALUES ('Empleados', ?, ?, 'Procesando')
            ");
            $stmt->execute([$archivo['name'], $_SESSION['user_id'] ?? 1]);
            $importacionId = $db->lastInsertId();
            
            // Procesar archivo CSV
            $handle = fopen($archivo['tmp_name'], 'r');
            
            // Saltar BOM si existe
            $bom = fread($handle, 3);
            if ($bom !== chr(0xEF).chr(0xBB).chr(0xBF)) {
                rewind($handle);
            }
            
            // Leer encabezados
            $encabezados = fgetcsv($handle);
            
            $registrosExitosos = 0;
            $registrosErrores = 0;
            $erroresDetalle = [];
            $linea = 1;
            
            $empleadoModel = new Empleado();
            
            while (($datos = fgetcsv($handle)) !== false) {
                $linea++;
                
                try {
                    // Validar que tenga suficientes columnas
                    if (count($datos) < 15) {
                        throw new Exception("Faltan columnas requeridas");
                    }
                    
                    // Mapear datos
                    $datosEmpleado = [
                        'nombres' => trim($datos[0]),
                        'apellido_paterno' => trim($datos[1]),
                        'apellido_materno' => trim($datos[2] ?? ''),
                        'curp' => trim($datos[3] ?? ''),
                        'rfc' => trim($datos[4] ?? ''),
                        'nss' => trim($datos[5] ?? ''),
                        'fecha_nacimiento' => trim($datos[6] ?? ''),
                        'genero' => trim($datos[7] ?? ''),
                        'email_personal' => trim($datos[8] ?? ''),
                        'celular' => trim($datos[9] ?? ''),
                        'fecha_ingreso' => trim($datos[10]),
                        'tipo_contrato' => trim($datos[11]),
                        'departamento' => trim($datos[12]),
                        'puesto' => trim($datos[13]),
                        'salario_mensual' => floatval($datos[14])
                    ];
                    
                    // Validar campos requeridos
                    if (empty($datosEmpleado['nombres']) || empty($datosEmpleado['apellido_paterno']) ||
                        empty($datosEmpleado['fecha_ingreso']) || empty($datosEmpleado['departamento']) ||
                        empty($datosEmpleado['puesto']) || $datosEmpleado['salario_mensual'] <= 0) {
                        throw new Exception("Campos requeridos faltantes");
                    }
                    
                    // Generar número de empleado
                    $stmt = $db->query("SELECT MAX(CAST(SUBSTRING(numero_empleado, 4) AS UNSIGNED)) as max_num FROM empleados");
                    $result = $stmt->fetch();
                    $nextNum = ($result['max_num'] ?? 0) + 1;
                    $datosEmpleado['numero_empleado'] = 'EMP' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
                    
                    // Generar código de empleado
                    $stmtCodigo = $db->query("SELECT MAX(CAST(codigo_empleado AS UNSIGNED)) as max_codigo FROM empleados WHERE codigo_empleado LIKE '183%'");
                    $resultCodigo = $stmtCodigo->fetch();
                    $nextCodigo = ($resultCodigo['max_codigo'] ?? 183000) + 1;
                    $datosEmpleado['codigo_empleado'] = str_pad($nextCodigo, 6, '0', STR_PAD_LEFT);
                    
                    // Calcular salario diario
                    $datosEmpleado['salario_diario'] = $datosEmpleado['salario_mensual'] / 30;
                    
                    // Crear empleado
                    if ($empleadoModel->create($datosEmpleado)) {
                        $registrosExitosos++;
                    } else {
                        throw new Exception("Error al guardar en base de datos");
                    }
                    
                } catch (Exception $e) {
                    $registrosErrores++;
                    $erroresDetalle[] = "Línea $linea: " . $e->getMessage();
                }
            }
            
            fclose($handle);
            
            // Actualizar registro de importación
            $estatus = $registrosErrores === 0 ? 'Completado' : ($registrosExitosos === 0 ? 'Error' : 'Parcial');
            $stmt = $db->prepare("
                UPDATE nomina_importaciones 
                SET total_registros = ?,
                    registros_exitosos = ?,
                    registros_errores = ?,
                    estatus = ?,
                    errores_detalle = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $registrosExitosos + $registrosErrores,
                $registrosExitosos,
                $registrosErrores,
                $estatus,
                json_encode($erroresDetalle),
                $importacionId
            ]);
            
            $db->commit();
            
            echo json_encode([
                'success' => true,
                'registros_exitosos' => $registrosExitosos,
                'registros_errores' => $registrosErrores,
                'errores_detalle' => $erroresDetalle
            ]);
            
        } catch (Exception $e) {
            $db->rollBack();
            echo json_encode(['success' => false, 'message' => 'Error al procesar importación: ' . $e->getMessage()]);
        }
        
        exit;
    }
}
