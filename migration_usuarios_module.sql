-- ============================================================
-- ACTUALIZACIÓN DE SCHEMA - MÓDULO DE USUARIOS Y MEJORAS
-- Fecha: 2026-01-17
-- Descripción: Agrega roles adicionales de usuario y mejoras al sistema
-- ============================================================

USE recursos_humanos;

-- ============================================================
-- MODIFICAR TABLA: usuarios - Agregar nuevos roles
-- ============================================================

-- Modificar el campo rol para incluir los nuevos tipos de usuario
ALTER TABLE usuarios 
MODIFY COLUMN rol ENUM('admin', 'rrhh', 'gerente', 'empleado', 'socio', 'empleado_confianza') DEFAULT 'empleado'
COMMENT 'Roles: admin=Administrador, rrhh=RRHH, gerente=Gerente, empleado=Empleado, socio=Socio, empleado_confianza=Empleado de Confianza';

-- Agregar columna para relación opcional con empleado
ALTER TABLE usuarios 
ADD COLUMN IF NOT EXISTS empleado_id INT NULL COMMENT 'Relación opcional con empleado' AFTER rol,
ADD INDEX IF NOT EXISTS idx_empleado (empleado_id);

-- Agregar foreign key si no existe
SET @fk_exists = (SELECT COUNT(*) 
                  FROM information_schema.TABLE_CONSTRAINTS 
                  WHERE CONSTRAINT_SCHEMA = DATABASE() 
                  AND TABLE_NAME = 'usuarios' 
                  AND CONSTRAINT_NAME = 'fk_usuario_empleado');

SET @sql = IF(@fk_exists = 0,
    'ALTER TABLE usuarios ADD CONSTRAINT fk_usuario_empleado FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE SET NULL',
    'SELECT "Foreign key ya existe" as mensaje');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================================
-- ACTUALIZAR TABLA: empleados - Asegurar relación inversa
-- ============================================================

-- El campo usuario_id ya existe en la tabla empleados
-- Solo verificamos que el índice exista
ALTER TABLE empleados 
ADD INDEX IF NOT EXISTS idx_usuario (usuario_id);

-- ============================================================
-- VISTA: Usuarios con información de empleado relacionado
-- ============================================================

CREATE OR REPLACE VIEW vista_usuarios_completo AS
SELECT 
    u.id,
    u.nombre as usuario_nombre,
    u.email,
    u.rol,
    u.activo,
    u.ultimo_acceso,
    u.fecha_creacion,
    u.empleado_id,
    CASE 
        WHEN u.empleado_id IS NOT NULL THEN CONCAT(e.nombres, ' ', e.apellido_paterno, ' ', IFNULL(e.apellido_materno, ''))
        ELSE NULL
    END as empleado_nombre_completo,
    e.numero_empleado,
    e.codigo_empleado,
    e.departamento,
    e.puesto,
    e.estatus as empleado_estatus,
    CASE u.rol
        WHEN 'admin' THEN 'Administrador'
        WHEN 'rrhh' THEN 'RRHH'
        WHEN 'gerente' THEN 'Gerente'
        WHEN 'empleado' THEN 'Empleado'
        WHEN 'socio' THEN 'Socio'
        WHEN 'empleado_confianza' THEN 'Empleado de Confianza'
        ELSE u.rol
    END as rol_texto
FROM usuarios u
LEFT JOIN empleados e ON u.empleado_id = e.id
ORDER BY u.nombre;

-- ============================================================
-- FIN DE LA MIGRACIÓN
-- ============================================================

-- Verificación de la migración
SELECT 'Migración de usuarios completada exitosamente' as mensaje;
SELECT COLUMN_TYPE 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'usuarios' 
  AND COLUMN_NAME = 'rol' as tipo_rol;
