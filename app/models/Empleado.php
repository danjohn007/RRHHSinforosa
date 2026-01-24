<?php
/**
 * Modelo de Empleado
 */

class Empleado {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Obtener todos los empleados
     */
    public function getAll($filters = []) {
        $sql = "SELECT e.*, 
                CONCAT(e.nombres, ' ', e.apellido_paterno, ' ', IFNULL(e.apellido_materno, '')) as nombre_completo,
                TIMESTAMPDIFF(YEAR, e.fecha_ingreso, CURDATE()) as anios_antiguedad,
                s.nombre as sucursal_nombre
                FROM empleados e 
                LEFT JOIN sucursales s ON e.sucursal_id = s.id
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['estatus'])) {
            $sql .= " AND e.estatus = ?";
            $params[] = $filters['estatus'];
        }
        
        if (!empty($filters['departamento'])) {
            $sql .= " AND e.departamento = ?";
            $params[] = $filters['departamento'];
        }
        
        if (!empty($filters['sucursal'])) {
            $sql .= " AND e.sucursal_id = ?";
            $params[] = $filters['sucursal'];
        }
        
        if (!empty($filters['search'])) {
            $searchTerm = '%' . $filters['search'] . '%';
            // Mejorar búsqueda para que funcione con nombre completo (nombre + apellidos)
            // Buscar en: nombre completo, nombre solo, apellidos, email, número, teléfonos
            $sql .= " AND (
                      CONCAT(e.nombres, ' ', e.apellido_paterno, ' ', IFNULL(e.apellido_materno, '')) LIKE ?
                      OR CONCAT(e.apellido_paterno, ' ', IFNULL(e.apellido_materno, ''), ' ', e.nombres) LIKE ?
                      OR e.nombres LIKE ? 
                      OR e.apellido_paterno LIKE ? 
                      OR e.apellido_materno LIKE ? 
                      OR e.email_personal LIKE ? 
                      OR e.numero_empleado LIKE ? 
                      OR e.celular LIKE ? 
                      OR e.telefono LIKE ?)";
            $params[] = $searchTerm;  // nombre completo (nombre + apellidos)
            $params[] = $searchTerm;  // nombre completo inverso (apellidos + nombre)
            $params[] = $searchTerm;  // nombres
            $params[] = $searchTerm;  // apellido_paterno
            $params[] = $searchTerm;  // apellido_materno
            $params[] = $searchTerm;  // email
            $params[] = $searchTerm;  // numero_empleado
            $params[] = $searchTerm;  // celular
            $params[] = $searchTerm;  // telefono
        }
        
        $sql .= " ORDER BY e.nombres, e.apellido_paterno";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener empleado por ID
     */
    public function getById($id) {
        $sql = "SELECT e.*,
                CONCAT(e.nombres, ' ', e.apellido_paterno, ' ', IFNULL(e.apellido_materno, '')) as nombre_completo,
                TIMESTAMPDIFF(YEAR, e.fecha_ingreso, CURDATE()) as anios_antiguedad,
                s.nombre as sucursal_nombre,
                s.codigo as sucursal_codigo
                FROM empleados e 
                LEFT JOIN sucursales s ON e.sucursal_id = s.id
                WHERE e.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Contar empleados por estatus
     */
    public function countByStatus() {
        $sql = "SELECT estatus, COUNT(*) as total FROM empleados GROUP BY estatus";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Contar empleados por departamento
     */
    public function countByDepartment() {
        $sql = "SELECT departamento, COUNT(*) as total FROM empleados WHERE estatus = 'Activo' GROUP BY departamento ORDER BY total DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener cumpleañeros del mes
     */
    public function getBirthdaysThisMonth() {
        $sql = "SELECT id, numero_empleado, 
                CONCAT(nombres, ' ', apellido_paterno, ' ', IFNULL(apellido_materno, '')) as nombre_completo,
                fecha_nacimiento, departamento, celular
                FROM empleados 
                WHERE MONTH(fecha_nacimiento) = MONTH(CURDATE()) 
                AND estatus = 'Activo'
                ORDER BY DAY(fecha_nacimiento)";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Crear nuevo empleado
     */
    public function create($data) {
        $sql = "INSERT INTO empleados (
                    numero_empleado, codigo_empleado, nombres, apellido_paterno, apellido_materno,
                    curp, rfc, nss, fecha_nacimiento, genero, estado_civil,
                    email_personal, telefono, celular,
                    calle, numero_exterior, numero_interior, colonia, codigo_postal, municipio, estado,
                    fecha_ingreso, tipo_contrato, departamento, puesto, 
                    salario_diario, salario_mensual, sucursal_id, turno_id, estatus
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['numero_empleado'], $data['codigo_empleado'], $data['nombres'], $data['apellido_paterno'], $data['apellido_materno'] ?? null,
            $data['curp'] ?? null, $data['rfc'] ?? null, $data['nss'] ?? null, 
            $data['fecha_nacimiento'] ?? null, $data['genero'] ?? null, $data['estado_civil'] ?? null,
            $data['email_personal'] ?? null, $data['telefono'] ?? null, $data['celular'] ?? null,
            $data['calle'] ?? null, $data['numero_exterior'] ?? null, $data['numero_interior'] ?? null,
            $data['colonia'] ?? null, $data['codigo_postal'] ?? null, $data['municipio'] ?? 'Querétaro', $data['estado'] ?? 'Querétaro',
            $data['fecha_ingreso'], $data['tipo_contrato'], $data['departamento'], $data['puesto'],
            $data['salario_diario'] ?? 0, $data['salario_mensual'] ?? 0, 
            $data['sucursal_id'] ?? null, $data['turno_id'] ?? null, $data['estatus'] ?? 'Activo'
        ]);
    }
    
    /**
     * Actualizar empleado
     */
    public function update($id, $data) {
        $sql = "UPDATE empleados SET 
                nombres = ?, apellido_paterno = ?, apellido_materno = ?,
                curp = ?, rfc = ?, nss = ?, fecha_nacimiento = ?, genero = ?, estado_civil = ?,
                email_personal = ?, telefono = ?, celular = ?,
                calle = ?, numero_exterior = ?, numero_interior = ?, colonia = ?, codigo_postal = ?, municipio = ?, estado = ?,
                fecha_ingreso = ?, tipo_contrato = ?, departamento = ?, puesto = ?, 
                salario_diario = ?, salario_mensual = ?, sucursal_id = ?, turno_id = ?,
                banco = ?, numero_cuenta = ?, clabe_interbancaria = ?,
                estatus = ?
                WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['nombres'], $data['apellido_paterno'], $data['apellido_materno'] ?? null,
            $data['curp'] ?? null, $data['rfc'] ?? null, $data['nss'] ?? null, 
            $data['fecha_nacimiento'] ?? null, $data['genero'] ?? null, $data['estado_civil'] ?? null,
            $data['email_personal'] ?? null, $data['telefono'] ?? null, $data['celular'] ?? null,
            $data['calle'] ?? null, $data['numero_exterior'] ?? null, $data['numero_interior'] ?? null,
            $data['colonia'] ?? null, $data['codigo_postal'] ?? null, $data['municipio'] ?? null, $data['estado'] ?? null,
            $data['fecha_ingreso'] ?? null, $data['tipo_contrato'] ?? null, $data['departamento'], $data['puesto'], 
            $data['salario_diario'] ?? null, $data['salario_mensual'],
            $data['sucursal_id'] ?? null, $data['turno_id'] ?? null,
            $data['banco'] ?? null, $data['numero_cuenta'] ?? null, $data['clabe_interbancaria'] ?? null,
            $data['estatus'], $id
        ]);
    }
    
    /**
     * Obtener departamentos únicos
     */
    public function getDepartments() {
        $sql = "SELECT DISTINCT departamento FROM empleados WHERE departamento IS NOT NULL ORDER BY departamento";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
