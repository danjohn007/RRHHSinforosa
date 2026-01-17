<?php
/**
 * Modelo de Usuario
 */

class Usuario {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Autenticar usuario
     */
    public function login($email, $password) {
        $sql = "SELECT * FROM usuarios WHERE email = ? AND activo = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Actualizar último acceso
            $updateSql = "UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?";
            $updateStmt = $this->db->prepare($updateSql);
            $updateStmt->execute([$user['id']]);
            
            return $user;
        }
        
        return false;
    }
    
    /**
     * Obtener usuario por ID
     */
    public function getById($id) {
        $sql = "SELECT * FROM usuarios WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Obtener todos los usuarios
     */
    public function getAll() {
        $sql = "SELECT id, nombre, email, rol, empleado_id, activo, ultimo_acceso FROM usuarios ORDER BY nombre";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener todos los usuarios con información de empleado relacionado
     */
    public function getAllWithEmployeeInfo() {
        $sql = "SELECT u.*, 
                CONCAT(e.nombres, ' ', e.apellido_paterno, ' ', IFNULL(e.apellido_materno, '')) as empleado_nombre,
                e.numero_empleado, e.codigo_empleado, e.departamento, e.puesto
                FROM usuarios u
                LEFT JOIN empleados e ON u.empleado_id = e.id
                ORDER BY u.nombre";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener usuarios por rol
     */
    public function getByRole($rol) {
        $sql = "SELECT u.id, u.nombre, u.email, u.rol, u.empleado_id,
                CONCAT(e.nombres, ' ', e.apellido_paterno, ' ', IFNULL(e.apellido_materno, '')) as empleado_nombre
                FROM usuarios u
                LEFT JOIN empleados e ON u.empleado_id = e.id
                WHERE u.rol = ? AND u.activo = 1
                ORDER BY u.nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$rol]);
        return $stmt->fetchAll();
    }
    
    /**
     * Verificar si existe un email
     */
    public function existsByEmail($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) as total FROM usuarios WHERE email = ?";
        $params = [$email];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['total'] > 0;
    }
    
    /**
     * Crear nuevo usuario
     */
    public function create($data) {
        $sql = "INSERT INTO usuarios (nombre, email, password, rol, empleado_id, activo) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['nombre'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['rol'],
            $data['empleado_id'] ?? null,
            $data['activo'] ?? 1
        ]);
    }
    
    /**
     * Actualizar usuario
     */
    public function update($id, $data) {
        if (isset($data['password'])) {
            $sql = "UPDATE usuarios SET nombre = ?, email = ?, password = ?, rol = ?, empleado_id = ?, activo = ? WHERE id = ?";
            $params = [
                $data['nombre'],
                $data['email'],
                password_hash($data['password'], PASSWORD_DEFAULT),
                $data['rol'],
                $data['empleado_id'] ?? null,
                $data['activo'] ?? 1,
                $id
            ];
        } else {
            $sql = "UPDATE usuarios SET nombre = ?, email = ?, rol = ?, empleado_id = ?, activo = ? WHERE id = ?";
            $params = [
                $data['nombre'],
                $data['email'],
                $data['rol'],
                $data['empleado_id'] ?? null,
                $data['activo'] ?? 1,
                $id
            ];
        }
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    /**
     * Eliminar usuario
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM usuarios WHERE id = ?");
        if ($stmt->execute([$id])) {
            return ['success' => true, 'message' => 'Usuario eliminado exitosamente'];
        }
        return ['success' => false, 'message' => 'Error al eliminar el usuario'];
    }
}
