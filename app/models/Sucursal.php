<?php
/**
 * Modelo de Sucursal
 */

class Sucursal {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Obtener todas las sucursales
     */
    public function getAll($soloActivas = false) {
        $sql = "SELECT s.*, 
                COUNT(DISTINCT e.id) as total_empleados,
                COUNT(DISTINCT sg.empleado_id) as total_gerentes,
                COUNT(DISTINCT sd.dispositivo_shelly_id) as total_dispositivos
                FROM sucursales s
                LEFT JOIN empleados e ON s.id = e.sucursal_id AND e.estatus = 'Activo'
                LEFT JOIN sucursal_gerentes sg ON s.id = sg.sucursal_id AND sg.activo = 1
                LEFT JOIN sucursal_dispositivos sd ON s.id = sd.sucursal_id AND sd.activo = 1
                WHERE 1=1";
        
        if ($soloActivas) {
            $sql .= " AND s.activo = 1";
        }
        
        $sql .= " GROUP BY s.id ORDER BY s.nombre";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener sucursal por ID
     */
    public function getById($id) {
        $sql = "SELECT s.*,
                COUNT(DISTINCT e.id) as total_empleados
                FROM sucursales s
                LEFT JOIN empleados e ON s.id = e.sucursal_id AND e.estatus = 'Activo'
                WHERE s.id = ?
                GROUP BY s.id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Obtener sucursal por URL pública
     */
    public function getByUrlPublica($urlPublica) {
        $sql = "SELECT * FROM sucursales WHERE url_publica = ? AND activo = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$urlPublica]);
        return $stmt->fetch();
    }
    
    /**
     * Obtener gerentes de una sucursal
     */
    public function getGerentes($sucursalId) {
        $sql = "SELECT e.id, e.numero_empleado, e.codigo_empleado,
                CONCAT(e.nombres, ' ', e.apellido_paterno, ' ', IFNULL(e.apellido_materno, '')) as nombre_completo,
                e.puesto, sg.fecha_asignacion, sg.activo
                FROM sucursal_gerentes sg
                INNER JOIN empleados e ON sg.empleado_id = e.id
                WHERE sg.sucursal_id = ?
                ORDER BY sg.activo DESC, e.nombres";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$sucursalId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener dispositivos Shelly de una sucursal
     */
    public function getDispositivos($sucursalId) {
        $sql = "SELECT ds.*, sd.tipo_accion, sd.activo as asignado_activo
                FROM sucursal_dispositivos sd
                INNER JOIN dispositivos_shelly ds ON sd.dispositivo_shelly_id = ds.id
                WHERE sd.sucursal_id = ?
                ORDER BY ds.nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$sucursalId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Verificar si un empleado es gerente de una sucursal
     */
    public function esGerente($empleadoId, $sucursalId = null) {
        $sql = "SELECT COUNT(*) as es_gerente FROM sucursal_gerentes 
                WHERE empleado_id = ? AND activo = 1";
        $params = [$empleadoId];
        
        if ($sucursalId !== null) {
            $sql .= " AND sucursal_id = ?";
            $params[] = $sucursalId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['es_gerente'] > 0;
    }
    
    /**
     * Crear nueva sucursal
     */
    public function create($data) {
        $sql = "INSERT INTO sucursales (
                    nombre, codigo, direccion, telefono, url_publica, activo
                ) VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            $data['nombre'],
            $data['codigo'],
            $data['direccion'] ?? null,
            $data['telefono'] ?? null,
            $data['url_publica'] ?? null,
            $data['activo'] ?? 1
        ]);
        
        return $result ? $this->db->lastInsertId() : false;
    }
    
    /**
     * Actualizar sucursal
     */
    public function update($id, $data) {
        $sql = "UPDATE sucursales SET 
                nombre = ?, codigo = ?, direccion = ?, telefono = ?, 
                url_publica = ?, activo = ?,
                horario_toda_semana = ?,
                hora_entrada_general = ?, hora_salida_general = ?,
                hora_entrada_lunes = ?, hora_salida_lunes = ?,
                hora_entrada_martes = ?, hora_salida_martes = ?,
                hora_entrada_miercoles = ?, hora_salida_miercoles = ?,
                hora_entrada_jueves = ?, hora_salida_jueves = ?,
                hora_entrada_viernes = ?, hora_salida_viernes = ?,
                hora_entrada_sabado = ?, hora_salida_sabado = ?,
                hora_entrada_domingo = ?, hora_salida_domingo = ?
                WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['nombre'],
            $data['codigo'],
            $data['direccion'] ?? null,
            $data['telefono'] ?? null,
            $data['url_publica'] ?? null,
            $data['activo'] ?? 1,
            $data['horario_toda_semana'] ?? 0,
            $data['hora_entrada_general'] ?? '08:00',
            $data['hora_salida_general'] ?? '18:00',
            $data['hora_entrada_lunes'] ?? null,
            $data['hora_salida_lunes'] ?? null,
            $data['hora_entrada_martes'] ?? null,
            $data['hora_salida_martes'] ?? null,
            $data['hora_entrada_miercoles'] ?? null,
            $data['hora_salida_miercoles'] ?? null,
            $data['hora_entrada_jueves'] ?? null,
            $data['hora_salida_jueves'] ?? null,
            $data['hora_entrada_viernes'] ?? null,
            $data['hora_salida_viernes'] ?? null,
            $data['hora_entrada_sabado'] ?? null,
            $data['hora_salida_sabado'] ?? null,
            $data['hora_entrada_domingo'] ?? null,
            $data['hora_salida_domingo'] ?? null,
            $id
        ]);
    }
    
    /**
     * Eliminar sucursal
     */
    public function delete($id) {
        // Verificar que no tenga empleados asignados
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM empleados WHERE sucursal_id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        
        if ($result['total'] > 0) {
            return ['success' => false, 'message' => 'No se puede eliminar la sucursal porque tiene empleados asignados'];
        }
        
        $stmt = $this->db->prepare("DELETE FROM sucursales WHERE id = ?");
        if ($stmt->execute([$id])) {
            return ['success' => true, 'message' => 'Sucursal eliminada exitosamente'];
        }
        return ['success' => false, 'message' => 'Error al eliminar la sucursal'];
    }
    
    /**
     * Asignar gerente a sucursal
     */
    public function asignarGerente($sucursalId, $empleadoId) {
        $sql = "INSERT INTO sucursal_gerentes (sucursal_id, empleado_id, fecha_asignacion, activo)
                VALUES (?, ?, CURDATE(), 1)
                ON DUPLICATE KEY UPDATE activo = 1, fecha_asignacion = CURDATE()";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$sucursalId, $empleadoId]);
    }
    
    /**
     * Remover gerente de sucursal
     */
    public function removerGerente($sucursalId, $empleadoId) {
        $sql = "DELETE FROM sucursal_gerentes WHERE sucursal_id = ? AND empleado_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$sucursalId, $empleadoId]);
    }
    
    /**
     * Asignar dispositivo Shelly a sucursal
     */
    public function asignarDispositivo($sucursalId, $dispositivoId, $tipoAccion = 'Ambos') {
        $sql = "INSERT INTO sucursal_dispositivos (sucursal_id, dispositivo_shelly_id, tipo_accion, activo)
                VALUES (?, ?, ?, 1)
                ON DUPLICATE KEY UPDATE tipo_accion = ?, activo = 1";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$sucursalId, $dispositivoId, $tipoAccion, $tipoAccion]);
    }
    
    /**
     * Remover dispositivo de sucursal
     */
    public function removerDispositivo($sucursalId, $dispositivoId) {
        $sql = "DELETE FROM sucursal_dispositivos WHERE sucursal_id = ? AND dispositivo_shelly_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$sucursalId, $dispositivoId]);
    }
    
    /**
     * Obtener empleados de una sucursal
     */
    public function getEmpleados($sucursalId, $soloActivos = true) {
        $sql = "SELECT e.id, e.numero_empleado, e.codigo_empleado,
                CONCAT(e.nombres, ' ', e.apellido_paterno, ' ', IFNULL(e.apellido_materno, '')) as nombre_completo,
                e.departamento, e.puesto, e.estatus
                FROM empleados e
                WHERE e.sucursal_id = ?";
        
        if ($soloActivos) {
            $sql .= " AND e.estatus = 'Activo'";
        }
        
        $sql .= " ORDER BY e.nombres";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$sucursalId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener áreas de trabajo de una sucursal
     */
    public function getAreasTrabajo($sucursalId) {
        $sql = "SELECT sat.*, ds.nombre as dispositivo_nombre, ds.device_id
                FROM sucursal_areas_trabajo sat
                LEFT JOIN dispositivos_shelly ds ON sat.dispositivo_shelly_id = ds.id
                WHERE sat.sucursal_id = ?
                ORDER BY sat.es_predeterminada DESC, sat.nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$sucursalId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Crear área de trabajo
     */
    public function crearAreaTrabajo($sucursalId, $datos) {
        $sql = "INSERT INTO sucursal_areas_trabajo 
                (sucursal_id, nombre, descripcion, dispositivo_shelly_id, canal_asignado, activo, es_predeterminada)
                VALUES (?, ?, ?, ?, ?, ?, 0)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $sucursalId,
            $datos['nombre'],
            $datos['descripcion'] ?? null,
            $datos['dispositivo_shelly_id'] ?? null,
            $datos['canal_asignado'] ?? 0,
            $datos['activo'] ?? 1
        ]);
    }
    
    /**
     * Actualizar área de trabajo
     */
    public function actualizarAreaTrabajo($areaId, $datos) {
        $sql = "UPDATE sucursal_areas_trabajo 
                SET nombre = ?, descripcion = ?, dispositivo_shelly_id = ?, 
                    canal_asignado = ?, activo = ?
                WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $datos['nombre'],
            $datos['descripcion'] ?? null,
            $datos['dispositivo_shelly_id'] ?? null,
            $datos['canal_asignado'] ?? 0,
            $datos['activo'] ?? 1,
            $areaId
        ]);
    }
    
    /**
     * Eliminar área de trabajo
     */
    public function eliminarAreaTrabajo($areaId) {
        // No permitir eliminar áreas predeterminadas
        $stmt = $this->db->prepare("SELECT es_predeterminada FROM sucursal_areas_trabajo WHERE id = ?");
        $stmt->execute([$areaId]);
        $area = $stmt->fetch();
        
        if ($area && $area['es_predeterminada']) {
            return ['success' => false, 'message' => 'No se pueden eliminar áreas predeterminadas'];
        }
        
        $stmt = $this->db->prepare("DELETE FROM sucursal_areas_trabajo WHERE id = ?");
        if ($stmt->execute([$areaId])) {
            return ['success' => true, 'message' => 'Área eliminada exitosamente'];
        }
        return ['success' => false, 'message' => 'Error al eliminar área'];
    }
}
