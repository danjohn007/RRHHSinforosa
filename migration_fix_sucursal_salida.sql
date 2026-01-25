-- ============================================================
-- MIGRACIÓN: Fix Sucursal de Salida
-- Fecha: 2026-01-25
-- Descripción: Agrega campo sucursal_salida_id para registrar
--              correctamente la sucursal donde se registra la salida
-- ============================================================

USE recursos_humanos;

-- ============================================================
-- PROCEDIMIENTO: Agregar columna si no existe (helper temporal)
-- ============================================================
DELIMITER $$

DROP PROCEDURE IF EXISTS add_column_if_not_exists$$

CREATE PROCEDURE add_column_if_not_exists(
    IN tableName VARCHAR(128),
    IN columnName VARCHAR(128),
    IN columnDefinition TEXT
)
BEGIN
    DECLARE column_count INT;
    
    SELECT COUNT(*) INTO column_count
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME COLLATE utf8_general_ci = tableName COLLATE utf8_general_ci
        AND COLUMN_NAME COLLATE utf8_general_ci = columnName COLLATE utf8_general_ci;
    
    IF column_count = 0 THEN
        SET @sql = CONCAT('ALTER TABLE ', tableName, ' ADD COLUMN ', columnName, ' ', columnDefinition);
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END$$

DELIMITER ;

-- ============================================================
-- PROCEDIMIENTO: Agregar foreign key si no existe
-- ============================================================
DELIMITER $$

DROP PROCEDURE IF EXISTS add_fk_if_not_exists$$

CREATE PROCEDURE add_fk_if_not_exists(
    IN tableName VARCHAR(128),
    IN constraintName VARCHAR(128),
    IN constraintDefinition TEXT
)
BEGIN
    DECLARE fk_count INT;
    
    SELECT COUNT(*) INTO fk_count
    FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = DATABASE()
        AND TABLE_NAME COLLATE utf8_general_ci = tableName COLLATE utf8_general_ci
        AND CONSTRAINT_NAME COLLATE utf8_general_ci = constraintName COLLATE utf8_general_ci;
    
    IF fk_count = 0 THEN
        SET @sql = CONCAT('ALTER TABLE ', tableName, ' ADD CONSTRAINT ', constraintName, ' ', constraintDefinition);
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END$$

DELIMITER ;

-- ============================================================
-- MODIFICACIONES A TABLA: asistencias
-- Agregar campo sucursal_salida_id
-- ============================================================

-- Agregar campo para sucursal donde se registró la salida
CALL add_column_if_not_exists('asistencias', 'sucursal_salida_id', 
    'INT NULL COMMENT "Sucursal donde se registró la salida" AFTER sucursal_id');

-- Agregar foreign key para sucursal_salida_id
CALL add_fk_if_not_exists('asistencias', 'fk_asistencia_sucursal_salida', 
    'FOREIGN KEY (sucursal_salida_id) REFERENCES sucursales(id) ON DELETE SET NULL');

-- ============================================================
-- Actualizar vista de asistencias completa
-- ============================================================
CREATE OR REPLACE VIEW vista_asistencias_completa AS
SELECT 
    a.id,
    a.fecha,
    a.hora_entrada,
    a.hora_salida,
    a.hora_salida_real,
    a.horas_trabajadas,
    a.horas_extra,
    a.minutos_retardo,
    a.estatus,
    a.auto_cortado,
    a.foto_entrada,
    a.foto_salida,
    a.notas,
    -- Información del empleado
    e.id as empleado_id,
    e.numero_empleado,
    e.codigo_empleado,
    CONCAT(e.nombres, ' ', e.apellido_paterno, ' ', IFNULL(e.apellido_materno, '')) as empleado_nombre,
    e.departamento,
    e.puesto,
    -- Información de la sucursal de ENTRADA
    s.id as sucursal_id,
    s.nombre as sucursal_nombre,
    s.codigo as sucursal_codigo,
    -- Información de la sucursal de SALIDA (puede ser NULL si no ha registrado salida)
    ss.id as sucursal_salida_id,
    ss.nombre as sucursal_salida_nombre,
    ss.codigo as sucursal_salida_codigo,
    -- Información de validación
    a.validado_por_id,
    u.nombre as validado_por_nombre,
    a.fecha_validacion
FROM asistencias a
INNER JOIN empleados e ON a.empleado_id = e.id
LEFT JOIN sucursales s ON a.sucursal_id = s.id
LEFT JOIN sucursales ss ON a.sucursal_salida_id = ss.id
LEFT JOIN usuarios u ON a.validado_por_id = u.id;

-- ============================================================
-- Verificación final
-- ============================================================
SELECT 
    'asistencias' as tabla,
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'asistencias' 
     AND COLUMN_NAME = 'sucursal_salida_id') as sucursal_salida_id_existe;

-- ============================================================
-- Limpiar procedimientos temporales
-- ============================================================
DROP PROCEDURE IF EXISTS add_column_if_not_exists;
DROP PROCEDURE IF EXISTS add_fk_if_not_exists;
