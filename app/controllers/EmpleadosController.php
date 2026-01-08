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
            $empleadoModel = new Empleado();
            
            // Generar número de empleado automático
            $db = Database::getInstance()->getConnection();
            $stmt = $db->query("SELECT MAX(CAST(SUBSTRING(numero_empleado, 4) AS UNSIGNED)) as max_num FROM empleados");
            $result = $stmt->fetch();
            $nextNum = ($result['max_num'] ?? 0) + 1;
            $numeroEmpleado = 'EMP' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
            
            $data = [
                'numero_empleado' => $numeroEmpleado,
                'nombres' => $_POST['nombres'],
                'apellido_paterno' => $_POST['apellido_paterno'],
                'apellido_materno' => $_POST['apellido_materno'] ?? null,
                'curp' => $_POST['curp'] ?? null,
                'rfc' => $_POST['rfc'] ?? null,
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
                'municipio' => $_POST['municipio'] ?? 'Querétaro',
                'estado' => $_POST['estado'] ?? 'Querétaro',
                'fecha_ingreso' => $_POST['fecha_ingreso'],
                'tipo_contrato' => $_POST['tipo_contrato'],
                'departamento' => $_POST['departamento'],
                'puesto' => $_POST['puesto'],
                'salario_diario' => $_POST['salario_diario'] ?? 0,
                'salario_mensual' => $_POST['salario_mensual'] ?? 0,
                'estatus' => 'Activo'
            ];
            
            if ($empleadoModel->create($data)) {
                $success = 'Empleado creado exitosamente';
                // Redirigir después de 2 segundos
                header("refresh:2;url=" . BASE_URL . "empleados");
            } else {
                $error = 'Error al crear empleado';
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
