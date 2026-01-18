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
COMMENT 'Roles:  admin=Administrador, rrhh=RRHH, gerente=Gerente, empleado=Empleado, socio=Socio, empleado_confianza=Empleado de Confianza';

-- ============================================================
-- Agregar columna empleado_id si no existe
-- ============================================================

DELIMITER $$

DROP PROCEDURE IF EXISTS add_empleado_id_column$$
CREATE PROCEDURE add_empleado_id_column()
BEGIN
    DECLARE column_exists INT DEFAULT 0;
    
    SELECT COUNT(*) INTO column_exists
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = 'recursos_humanos'
    AND TABLE_NAME = 'usuarios'
    AND COLUMN_NAME = 'empleado_id';
    
    IF column_exists = 0 THEN
        ALTER TABLE usuarios 
        ADD COLUMN empleado_id INT NULL COMMENT 'Relación opcional con empleado' AFTER rol;
        SELECT 'Columna empleado_id agregada correctamente' AS resultado;
    ELSE
        SELECT 'Columna empleado_id ya existe' AS resultado;
    END IF;
END$$

DELIMITER ;

CALL add_empleado_id_column();
DROP PROCEDURE add_empleado_id_column;

-- ============================================================
-- Agregar índice idx_empleado si no existe
-- ============================================================

DELIMITER $$

DROP PROCEDURE IF EXISTS add_idx_empleado$$
CREATE PROCEDURE add_idx_empleado()
BEGIN
    DECLARE index_exists INT DEFAULT 0;
    
    SELECT COUNT(*) INTO index_exists
    FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = 'recursos_humanos'
    AND TABLE_NAME = 'usuarios'
    AND INDEX_NAME = 'idx_empleado';
    
    IF index_exists = 0 THEN
        ALTER TABLE usuarios ADD INDEX idx_empleado (empleado_id);
        SELECT 'Índice idx_empleado agregado correctamente' AS resultado;
    ELSE
        SELECT 'Índice idx_empleado ya existe' AS resultado;
    END IF;
END$$

DELIMITER ;

CALL add_idx_empleado();
DROP PROCEDURE add_idx_empleado;

-- ============================================================
-- Agregar foreign key si no existe
-- ============================================================

DELIMITER $$

DROP PROCEDURE IF EXISTS add_fk_usuario_empleado$$
CREATE PROCEDURE add_fk_usuario_empleado()
BEGIN
    DECLARE fk_exists INT DEFAULT 0;
    
    SELECT COUNT(*) INTO fk_exists
    FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = 'recursos_humanos'
    AND TABLE_NAME = 'usuarios'
    AND CONSTRAINT_NAME = 'fk_usuario_empleado'
    AND CONSTRAINT_TYPE = 'FOREIGN KEY';
    
    IF fk_exists = 0 THEN
        ALTER TABLE usuarios 
        ADD CONSTRAINT fk_usuario_empleado 
        FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE SET NULL;
        SELECT 'Foreign key fk_usuario_empleado agregada correctamente' AS resultado;
    ELSE
        SELECT 'Foreign key fk_usuario_empleado ya existe' AS resultado;
    END IF;
END$$

DELIMITER ;

CALL add_fk_usuario_empleado();
DROP PROCEDURE add_fk_usuario_empleado;

-- ============================================================
-- ACTUALIZAR TABLA: empleados - Asegurar relación inversa
-- ============================================================

DELIMITER $$

DROP PROCEDURE IF EXISTS add_idx_usuario$$
CREATE PROCEDURE add_idx_usuario()
BEGIN
    DECLARE index_exists INT DEFAULT 0;
    
    SELECT COUNT(*) INTO index_exists
    FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = 'recursos_humanos'
    AND TABLE_NAME = 'empleados'
    AND INDEX_NAME = 'idx_usuario';
    
    IF index_exists = 0 THEN
        ALTER TABLE empleados ADD INDEX idx_usuario (usuario_id);
        SELECT 'Índice idx_usuario agregado correctamente' AS resultado;
    ELSE
        SELECT 'Índice idx_usuario ya existe' AS resultado;
    END IF;
END$$

DELIMITER ;

CALL add_idx_usuario();
DROP PROCEDURE add_idx_usuario;

-- ============================================================
-- VISTA: Usuarios con información de empleado relacionado
-- ============================================================

DROP VIEW IF EXISTS vista_usuarios_completo;

CREATE VIEW vista_usuarios_completo AS
SELECT 
    u.id,
    u.nombre as usuario_nombre,
    u.email,
    u.rol,
    u.activo,
    u. ultimo_acceso,
    u.fecha_creacion,
    u.empleado_id,
    CASE 
        WHEN u.empleado_id IS NOT NULL THEN CONCAT(e. nombres, ' ', e. apellido_paterno, ' ', IFNULL(e.apellido_materno, ''))
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
        ELSE u. rol
    END as rol_texto
FROM usuarios u
LEFT JOIN empleados e ON u.empleado_id = e.id
ORDER BY u.nombre;

-- ============================================================
-- FIN DE LA MIGRACIÓN
-- ============================================================

-- Verificación de la migración
SELECT 'Migración de usuarios completada exitosamente' as mensaje;
