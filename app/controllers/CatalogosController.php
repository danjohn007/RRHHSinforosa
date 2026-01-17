<?php
/**
 * Controlador de Catálogos (Departamentos y Puestos)
 */

class CatalogosController {
    
    public function index() {
        AuthController::check();
        AuthController::checkRole(['admin', 'rrhh']);
        
        redirect('catalogos/departamentos');
    }
    
    public function departamentos() {
        AuthController::check();
        AuthController::checkRole(['admin', 'rrhh']);
        
        $db = Database::getInstance()->getConnection();
        
        // Obtener todos los departamentos
        $stmt = $db->query("SELECT * FROM departamentos ORDER BY nombre");
        $departamentos = $stmt->fetchAll();
        
        $data = [
            'title' => 'Catálogo de Departamentos',
            'departamentos' => $departamentos
        ];
        
        ob_start();
        require_once BASE_PATH . 'app/views/catalogos/departamentos.php';
        $content = ob_get_clean();
        
        require_once BASE_PATH . 'app/views/layouts/main.php';
    }
    
    public function puestos() {
        AuthController::check();
        AuthController::checkRole(['admin', 'rrhh']);
        
        $db = Database::getInstance()->getConnection();
        
        // Obtener todos los puestos con información del departamento
        $stmt = $db->query("
            SELECT p.*, d.nombre as departamento_nombre
            FROM puestos p
            LEFT JOIN departamentos d ON p.departamento_id = d.id
            ORDER BY p.nombre
        ");
        $puestos = $stmt->fetchAll();
        
        // Obtener departamentos para el formulario
        $stmtDept = $db->query("SELECT id, nombre FROM departamentos WHERE activo = 1 ORDER BY nombre");
        $departamentos = $stmtDept->fetchAll();
        
        $data = [
            'title' => 'Catálogo de Puestos',
            'puestos' => $puestos,
            'departamentos' => $departamentos
        ];
        
        ob_start();
        require_once BASE_PATH . 'app/views/catalogos/puestos.php';
        $content = ob_get_clean();
        
        require_once BASE_PATH . 'app/views/layouts/main.php';
    }
    
    public function guardarDepartamento() {
        AuthController::check();
        AuthController::checkRole(['admin', 'rrhh']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        $id = $_POST['id'] ?? null;
        $nombre = $_POST['nombre'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $activo = isset($_POST['activo']) ? 1 : 0;
        
        if (empty($nombre)) {
            echo json_encode(['success' => false, 'message' => 'El nombre es obligatorio']);
            return;
        }
        
        $db = Database::getInstance()->getConnection();
        
        try {
            if ($id) {
                // Actualizar
                $stmt = $db->prepare("UPDATE departamentos SET nombre = ?, descripcion = ?, activo = ? WHERE id = ?");
                $stmt->execute([$nombre, $descripcion, $activo, $id]);
                echo json_encode(['success' => true, 'message' => 'Departamento actualizado exitosamente']);
            } else {
                // Crear nuevo
                $stmt = $db->prepare("INSERT INTO departamentos (nombre, descripcion, activo) VALUES (?, ?, ?)");
                $stmt->execute([$nombre, $descripcion, $activo]);
                echo json_encode(['success' => true, 'message' => 'Departamento creado exitosamente']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    public function eliminarDepartamento() {
        AuthController::check();
        AuthController::checkRole(['admin', 'rrhh']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        $id = $_POST['id'] ?? null;
        
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
            return;
        }
        
        $db = Database::getInstance()->getConnection();
        
        // Verificar si hay empleados o puestos asignados
        $stmt = $db->prepare("
            SELECT COUNT(*) as total 
            FROM empleados e 
            INNER JOIN departamentos d ON e.departamento = d.nombre 
            WHERE d.id = ?
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        
        if ($result['total'] > 0) {
            echo json_encode(['success' => false, 'message' => 'No se puede eliminar el departamento porque tiene empleados asignados']);
            return;
        }
        
        try {
            $stmt = $db->prepare("DELETE FROM departamentos WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Departamento eliminado exitosamente']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    public function guardarPuesto() {
        AuthController::check();
        AuthController::checkRole(['admin', 'rrhh']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        $id = $_POST['id'] ?? null;
        $nombre = $_POST['nombre'] ?? '';
        $departamentoId = !empty($_POST['departamento_id']) ? $_POST['departamento_id'] : null;
        $descripcion = $_POST['descripcion'] ?? '';
        $activo = isset($_POST['activo']) ? 1 : 0;
        
        if (empty($nombre)) {
            echo json_encode(['success' => false, 'message' => 'El nombre es obligatorio']);
            return;
        }
        
        $db = Database::getInstance()->getConnection();
        
        try {
            if ($id) {
                // Actualizar
                $stmt = $db->prepare("UPDATE puestos SET nombre = ?, departamento_id = ?, descripcion = ?, activo = ? WHERE id = ?");
                $stmt->execute([$nombre, $departamentoId, $descripcion, $activo, $id]);
                echo json_encode(['success' => true, 'message' => 'Puesto actualizado exitosamente']);
            } else {
                // Crear nuevo
                $stmt = $db->prepare("INSERT INTO puestos (nombre, departamento_id, descripcion, activo) VALUES (?, ?, ?, ?)");
                $stmt->execute([$nombre, $departamentoId, $descripcion, $activo]);
                echo json_encode(['success' => true, 'message' => 'Puesto creado exitosamente']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    public function eliminarPuesto() {
        AuthController::check();
        AuthController::checkRole(['admin', 'rrhh']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        $id = $_POST['id'] ?? null;
        
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
            return;
        }
        
        $db = Database::getInstance()->getConnection();
        
        // Verificar si hay empleados asignados
        $stmt = $db->prepare("
            SELECT COUNT(*) as total 
            FROM empleados e 
            INNER JOIN puestos p ON e.puesto = p.nombre 
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        
        if ($result['total'] > 0) {
            echo json_encode(['success' => false, 'message' => 'No se puede eliminar el puesto porque tiene empleados asignados']);
            return;
        }
        
        try {
            $stmt = $db->prepare("DELETE FROM puestos WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Puesto eliminado exitosamente']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    public function obtenerDepartamento() {
        AuthController::check();
        AuthController::checkRole(['admin', 'rrhh']);
        
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
            return;
        }
        
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM departamentos WHERE id = ?");
        $stmt->execute([$id]);
        $departamento = $stmt->fetch();
        
        if ($departamento) {
            echo json_encode(['success' => true, 'data' => $departamento]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Departamento no encontrado']);
        }
    }
    
    public function obtenerPuesto() {
        AuthController::check();
        AuthController::checkRole(['admin', 'rrhh']);
        
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
            return;
        }
        
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM puestos WHERE id = ?");
        $stmt->execute([$id]);
        $puesto = $stmt->fetch();
        
        if ($puesto) {
            echo json_encode(['success' => true, 'data' => $puesto]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Puesto no encontrado']);
        }
    }
}
