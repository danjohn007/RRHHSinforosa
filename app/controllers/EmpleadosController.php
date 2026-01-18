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
        
        $empleados = $empleadoModel->getAll($filters);
        $departamentos = $empleadoModel->getDepartments();
        
        $data = [
            'title' => 'Gestión de Empleados',
            'empleados' => $empleados,
            'departamentos' => $departamentos,
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
                
                $dataEmpleado = [
                    'numero_empleado' => $numeroEmpleado,
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
}
