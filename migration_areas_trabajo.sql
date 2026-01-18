-- ============================================================
-- MIGRACIÓN: ÁREAS DE TRABAJO EN SUCURSALES
-- Fecha: 2026-01-18
-- Descripción: Agrega sistema de áreas de trabajo para sucursales
--              con asignación de dispositivos Shelly y canales específicos
-- ============================================================

USE recursos_humanos;

-- ============================================================
-- TABLA: sucursal_areas_trabajo
-- ============================================================
CREATE TABLE IF NOT EXISTS sucursal_areas_trabajo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sucursal_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL COMMENT 'Nombre del área (Entrada, Salida, etc.)',
    descripcion TEXT,
    dispositivo_shelly_id INT COMMENT 'Dispositivo Shelly asignado al área',
    canal_asignado INT DEFAULT 0 COMMENT 'Canal específico del dispositivo (0-3)',
    activo TINYINT(1) DEFAULT 1,
    es_predeterminada TINYINT(1) DEFAULT 0 COMMENT 'Área predeterminada del sistema',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sucursal_id) REFERENCES sucursales(id) ON DELETE CASCADE,
    FOREIGN KEY (dispositivo_shelly_id) REFERENCES dispositivos_shelly(id) ON DELETE SET NULL,
    INDEX idx_sucursal (sucursal_id),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- PROCEDIMIENTO: Agregar columna si no existe
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
        AND TABLE_NAME COLLATE utf8mb4_unicode_ci = tableName COLLATE utf8mb4_unicode_ci
        AND COLUMN_NAME COLLATE utf8mb4_unicode_ci = columnName COLLATE utf8mb4_unicode_ci;
    
    IF column_count = 0 THEN
        SET @sql = CONCAT('ALTER TABLE ', tableName, ' ADD COLUMN ', columnName, ' ', columnDefinition);
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END$$

DELIMITER ;

-- ============================================================
-- MODIFICACIONES A TABLA: sucursal_dispositivos
-- ============================================================

-- Agregar referencia a área de trabajo
CALL add_column_if_not_exists('sucursal_dispositivos', 'area_trabajo_id', 
    'INT COMMENT "Área de trabajo asociada" AFTER dispositivo_shelly_id');

-- ============================================================
-- MODIFICACIONES A TABLA: asistencias
-- ============================================================

-- Agregar área de trabajo donde se registró la asistencia
CALL add_column_if_not_exists('asistencias', 'area_trabajo_entrada_id', 
    'INT COMMENT "Área donde se registró la entrada" AFTER sucursal_id');

CALL add_column_if_not_exists('asistencias', 'area_trabajo_salida_id', 
    'INT COMMENT "Área donde se registró la salida" AFTER area_trabajo_entrada_id');

-- ============================================================
-- TRIGGER: Crear áreas predeterminadas al crear sucursal
-- ============================================================
DELIMITER $$

DROP TRIGGER IF EXISTS after_sucursal_insert$$

CREATE TRIGGER after_sucursal_insert
AFTER INSERT ON sucursales
FOR EACH ROW
BEGIN
    -- Crear área de Entrada predeterminada
    INSERT INTO sucursal_areas_trabajo (sucursal_id, nombre, descripcion, activo, es_predeterminada)
    VALUES (NEW.id, 'Entrada', 'Área de registro de entrada de empleados', 1, 1);
    
    -- Crear área de Salida predeterminada
    INSERT INTO sucursal_areas_trabajo (sucursal_id, nombre, descripcion, activo, es_predeterminada)
    VALUES (NEW.id, 'Salida', 'Área de registro de salida de empleados', 1, 1);
END$$

DELIMITER ;

-- ============================================================
-- CREAR ÁREAS PREDETERMINADAS para sucursales existentes
-- ============================================================

-- Crear áreas de Entrada y Salida para todas las sucursales existentes
INSERT INTO sucursal_areas_trabajo (sucursal_id, nombre, descripcion, activo, es_predeterminada)
SELECT 
    s.id,
    'Entrada',
    'Área de registro de entrada de empleados',
    1,
    1
FROM sucursales s
WHERE NOT EXISTS (
    SELECT 1 FROM sucursal_areas_trabajo sat 
    WHERE sat.sucursal_id = s.id AND sat.nombre = 'Entrada'
);

INSERT INTO sucursal_areas_trabajo (sucursal_id, nombre, descripcion, activo, es_predeterminada)
SELECT 
    s.id,
    'Salida',
    'Área de registro de salida de empleados',
    1,
    1
FROM sucursales s
WHERE NOT EXISTS (
    SELECT 1 FROM sucursal_areas_trabajo sat 
    WHERE sat.sucursal_id = s.id AND sat.nombre = 'Salida'
);

-- ============================================================
-- MIGRAR DISPOSITIVOS EXISTENTES A ÁREAS DE TRABAJO
-- ============================================================

-- Asignar dispositivos de tipo 'Entrada' o 'Ambos' al área de Entrada
UPDATE sucursal_dispositivos sd
INNER JOIN sucursal_areas_trabajo sat ON sd.sucursal_id = sat.sucursal_id
SET sd.area_trabajo_id = sat.id
WHERE sat.nombre = 'Entrada'
  AND sd.tipo_accion IN ('Entrada', 'Ambos')
  AND sd.area_trabajo_id IS NULL;

-- ============================================================
-- LIMPIAR PROCEDIMIENTOS TEMPORALES
-- ============================================================

DROP PROCEDURE IF EXISTS add_column_if_not_exists;

-- ============================================================
-- VERIFICACIÓN DE LA MIGRACIÓN
-- ============================================================

SELECT 'Migración de áreas de trabajo completada exitosamente' as mensaje;
SELECT COUNT(*) as total_areas_trabajo FROM sucursal_areas_trabajo;
SELECT 
    s.nombre as sucursal, 
    COUNT(sat.id) as areas_trabajo
FROM sucursales s
LEFT JOIN sucursal_areas_trabajo sat ON s.id = sat.sucursal_id
GROUP BY s.id, s.nombre;

-- ============================================================
-- FIN DE LA MIGRACIÓN
-- ============================================================
