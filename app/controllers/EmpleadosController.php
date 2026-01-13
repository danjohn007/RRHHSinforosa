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
        
        $data = [
            'title' => 'Nuevo Empleado',
            'error' => $error,
            'success' => $success
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
                'email_personal' => $_POST['email_personal'] ?? null,
                'telefono' => $_POST['telefono'] ?? null,
                'celular' => $_POST['celular'] ?? null,
                'departamento' => $_POST['departamento'],
                'puesto' => $_POST['puesto'],
                'salario_mensual' => $_POST['salario_mensual'],
                'estatus' => $_POST['estatus']
            ];
            
            if ($empleadoModel->update($id, $data)) {
                $success = 'Empleado actualizado exitosamente';
                $empleado = $empleadoModel->getById($id);
            } else {
                $error = 'Error al actualizar empleado';
            }
        }
        
        $data = [
            'title' => 'Editar Empleado',
            'empleado' => $empleado,
            'error' => $error,
            'success' => $success
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
        
        header('Content-Type: text/html; charset=utf-8');
        require_once BASE_PATH . 'app/views/empleados/carta_recomendacion.php';
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
        
        header('Content-Type: text/html; charset=utf-8');
        require_once BASE_PATH . 'app/views/empleados/constancia.php';
    }
}
