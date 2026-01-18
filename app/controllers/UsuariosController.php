<?php
/**
 * Controlador de Usuarios
 */

class UsuariosController {
    
    public function index() {
        AuthController::check();
        AuthController::checkRole(['admin']);
        
        $usuarioModel = new Usuario();
        $usuarios = $usuarioModel->getAllWithEmployeeInfo();
        
        $data = [
            'title' => 'Gestión de Usuarios',
            'usuarios' => $usuarios
        ];
        
        ob_start();
        require_once BASE_PATH . 'app/views/usuarios/index.php';
        $content = ob_get_clean();
        
        require_once BASE_PATH . 'app/views/layouts/main.php';
    }
    
    public function crear() {
        AuthController::check();
        AuthController::checkRole(['admin']);
        
        $error = null;
        $success = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $rol = $_POST['rol'] ?? '';
            $empleadoId = !empty($_POST['empleado_id']) ? $_POST['empleado_id'] : null;
            $activo = isset($_POST['activo']) ? 1 : 0;
            
            // Validaciones
            if (empty($nombre) || empty($email) || empty($password) || empty($rol)) {
                $error = 'Todos los campos obligatorios deben ser completados';
            } elseif ($password !== $confirmPassword) {
                $error = 'Las contraseñas no coinciden';
            } elseif (strlen($password) < 6) {
                $error = 'La contraseña debe tener al menos 6 caracteres';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'El correo electrónico no es válido';
            } else {
                $usuarioModel = new Usuario();
                
                // Verificar que el email no esté en uso
                if ($usuarioModel->existsByEmail($email)) {
                    $error = 'El correo electrónico ya está registrado';
                } else {
                    $result = $usuarioModel->create([
                        'nombre' => $nombre,
                        'email' => $email,
                        'password' => $password,
                        'rol' => $rol,
                        'empleado_id' => $empleadoId,
                        'activo' => $activo
                    ]);
                    
                    if ($result) {
                        $success = 'Usuario creado exitosamente';
                        
                        // Redirigir después de 2 segundos
                        echo "<script>
                            setTimeout(function() {
                                window.location.href = '" . BASE_URL . "usuarios';
                            }, 2000);
                        </script>";
                    } else {
                        $error = 'Error al crear el usuario';
                    }
                }
            }
        }
        
        // Obtener lista de empleados para seleccionar (opcional)
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("
            SELECT e.id, e.numero_empleado, e.codigo_empleado,
            CONCAT(e.nombres, ' ', e.apellido_paterno, ' ', IFNULL(e.apellido_materno, '')) as nombre_completo,
            e.departamento, e.puesto
            FROM empleados e
            WHERE e.estatus = 'Activo'
            AND e.id NOT IN (SELECT empleado_id FROM usuarios WHERE empleado_id IS NOT NULL)
            ORDER BY e.nombres
        ");
        $empleadosDisponibles = $stmt->fetchAll();
        
        $data = [
            'title' => 'Crear Usuario',
            'error' => $error,
            'success' => $success,
            'empleadosDisponibles' => $empleadosDisponibles
        ];
        
        ob_start();
        require_once BASE_PATH . 'app/views/usuarios/crear.php';
        $content = ob_get_clean();
        
        require_once BASE_PATH . 'app/views/layouts/main.php';
    }
    
    public function editar() {
        AuthController::check();
        AuthController::checkRole(['admin']);
        
        $usuarioId = $_GET['id'] ?? null;
        
        if (!$usuarioId) {
            redirect('usuarios');
        }
        
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getById($usuarioId);
        
        if (!$usuario) {
            redirect('usuarios');
        }
        
        $error = null;
        $success = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $rol = $_POST['rol'] ?? '';
            $empleadoId = !empty($_POST['empleado_id']) ? $_POST['empleado_id'] : null;
            $activo = isset($_POST['activo']) ? 1 : 0;
            
            // Validaciones
            if (empty($nombre) || empty($email) || empty($rol)) {
                $error = 'Nombre, email y rol son obligatorios';
            } elseif (!empty($password) && $password !== $confirmPassword) {
                $error = 'Las contraseñas no coinciden';
            } elseif (!empty($password) && strlen($password) < 6) {
                $error = 'La contraseña debe tener al menos 6 caracteres';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'El correo electrónico no es válido';
            } else {
                // Verificar que el email no esté en uso por otro usuario
                if ($usuarioModel->existsByEmail($email, $usuarioId)) {
                    $error = 'El correo electrónico ya está registrado por otro usuario';
                } else {
                    $dataToUpdate = [
                        'nombre' => $nombre,
                        'email' => $email,
                        'rol' => $rol,
                        'empleado_id' => $empleadoId,
                        'activo' => $activo
                    ];
                    
                    // Solo actualizar password si se proporcionó uno nuevo
                    if (!empty($password)) {
                        $dataToUpdate['password'] = $password;
                    }
                    
                    $result = $usuarioModel->update($usuarioId, $dataToUpdate);
                    
                    if ($result) {
                        $success = 'Usuario actualizado exitosamente';
                        $usuario = $usuarioModel->getById($usuarioId);
                    } else {
                        $error = 'Error al actualizar el usuario';
                    }
                }
            }
        }
        
        // Obtener lista de empleados para seleccionar (opcional)
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("
            SELECT e.id, e.numero_empleado, e.codigo_empleado,
            CONCAT(e.nombres, ' ', e.apellido_paterno, ' ', IFNULL(e.apellido_materno, '')) as nombre_completo,
            e.departamento, e.puesto
            FROM empleados e
            WHERE e.estatus = 'Activo'
            AND (e.id NOT IN (SELECT empleado_id FROM usuarios WHERE empleado_id IS NOT NULL AND id != ?)
                 OR e.id = ?)
            ORDER BY e.nombres
        ");
        $stmt->execute([$usuarioId, $usuario['empleado_id'] ?? 0]);
        $empleadosDisponibles = $stmt->fetchAll();
        
        $data = [
            'title' => 'Editar Usuario',
            'usuario' => $usuario,
            'empleadosDisponibles' => $empleadosDisponibles,
            'error' => $error,
            'success' => $success
        ];
        
        ob_start();
        require_once BASE_PATH . 'app/views/usuarios/editar.php';
        $content = ob_get_clean();
        
        require_once BASE_PATH . 'app/views/layouts/main.php';
    }
    
    public function eliminar() {
        AuthController::check();
        AuthController::checkRole(['admin']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        $usuarioId = $_POST['id'] ?? null;
        
        if (!$usuarioId) {
            echo json_encode(['success' => false, 'message' => 'ID de usuario no proporcionado']);
            return;
        }
        
        // No permitir eliminar el usuario actual
        if ($usuarioId == $_SESSION['user_id']) {
            echo json_encode(['success' => false, 'message' => 'No puedes eliminar tu propio usuario']);
            return;
        }
        
        $usuarioModel = new Usuario();
        $result = $usuarioModel->delete($usuarioId);
        
        echo json_encode($result);
    }
}
