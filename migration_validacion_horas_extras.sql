-- ============================================================
-- MIGRACIÓN: Validación de Horas Extras
-- Fecha: 2026-01-25
-- Descripción: Agrega horarios de sucursal por día de la semana,
--              status de validación de asistencias, y mejoras
--              al control de asistencia
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
-- MODIFICACIONES A TABLA: sucursales
-- Agregar campos de horarios por día de la semana
-- ============================================================

-- Agregar flag para aplicar horario a toda la semana
CALL add_column_if_not_exists('sucursales', 'horario_toda_semana', 
    'TINYINT(1) DEFAULT 0 COMMENT "Si es 1, aplica el mismo horario a todos los días" AFTER activo');

-- Horarios generales (cuando horario_toda_semana = 1)
CALL add_column_if_not_exists('sucursales', 'hora_entrada_general', 
    'TIME DEFAULT "08:00:00" COMMENT "Hora de entrada general" AFTER horario_toda_semana');

CALL add_column_if_not_exists('sucursales', 'hora_salida_general', 
    'TIME DEFAULT "18:00:00" COMMENT "Hora de salida general" AFTER hora_entrada_general');

-- Horarios por día de la semana
-- Lunes
CALL add_column_if_not_exists('sucursales', 'hora_entrada_lunes', 
    'TIME DEFAULT "08:00:00" COMMENT "Hora de entrada - Lunes" AFTER hora_salida_general');

CALL add_column_if_not_exists('sucursales', 'hora_salida_lunes', 
    'TIME DEFAULT "18:00:00" COMMENT "Hora de salida - Lunes" AFTER hora_entrada_lunes');

-- Martes
CALL add_column_if_not_exists('sucursales', 'hora_entrada_martes', 
    'TIME DEFAULT "08:00:00" COMMENT "Hora de entrada - Martes" AFTER hora_salida_lunes');

CALL add_column_if_not_exists('sucursales', 'hora_salida_martes', 
    'TIME DEFAULT "18:00:00" COMMENT "Hora de salida - Martes" AFTER hora_entrada_martes');

-- Miércoles
CALL add_column_if_not_exists('sucursales', 'hora_entrada_miercoles', 
    'TIME DEFAULT "08:00:00" COMMENT "Hora de entrada - Miércoles" AFTER hora_salida_martes');

CALL add_column_if_not_exists('sucursales', 'hora_salida_miercoles', 
    'TIME DEFAULT "18:00:00" COMMENT "Hora de salida - Miércoles" AFTER hora_entrada_miercoles');

-- Jueves
CALL add_column_if_not_exists('sucursales', 'hora_entrada_jueves', 
    'TIME DEFAULT "08:00:00" COMMENT "Hora de entrada - Jueves" AFTER hora_salida_miercoles');

CALL add_column_if_not_exists('sucursales', 'hora_salida_jueves', 
    'TIME DEFAULT "18:00:00" COMMENT "Hora de salida - Jueves" AFTER hora_entrada_jueves');

-- Viernes
CALL add_column_if_not_exists('sucursales', 'hora_entrada_viernes', 
    'TIME DEFAULT "08:00:00" COMMENT "Hora de entrada - Viernes" AFTER hora_salida_jueves');

CALL add_column_if_not_exists('sucursales', 'hora_salida_viernes', 
    'TIME DEFAULT "18:00:00" COMMENT "Hora de salida - Viernes" AFTER hora_entrada_viernes');

-- Sábado
CALL add_column_if_not_exists('sucursales', 'hora_entrada_sabado', 
    'TIME DEFAULT "09:00:00" COMMENT "Hora de entrada - Sábado" AFTER hora_salida_viernes');

CALL add_column_if_not_exists('sucursales', 'hora_salida_sabado', 
    'TIME DEFAULT "14:00:00" COMMENT "Hora de salida - Sábado" AFTER hora_entrada_sabado');

-- Domingo
CALL add_column_if_not_exists('sucursales', 'hora_entrada_domingo', 
    'TIME DEFAULT NULL COMMENT "Hora de entrada - Domingo (NULL = cerrado)" AFTER hora_salida_sabado');

CALL add_column_if_not_exists('sucursales', 'hora_salida_domingo', 
    'TIME DEFAULT NULL COMMENT "Hora de salida - Domingo (NULL = cerrado)" AFTER hora_entrada_domingo');

-- ============================================================
-- MODIFICACIONES A TABLA: asistencias
-- Agregar campos para validación y hora real de salida
-- ============================================================

-- Verificar si el enum ya tiene los valores necesarios
SET @column_type = (
    SELECT COLUMN_TYPE 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'asistencias' 
    AND COLUMN_NAME = 'estatus'
);

-- Actualizar ENUM si no contiene 'Por Validar' o 'Validado'
SET @has_por_validar = LOCATE('Por Validar', @column_type) > 0;
SET @has_validado = LOCATE('Validado', @column_type) > 0;

-- Si falta alguno de los valores, actualizar el ENUM
SET @update_enum = (@has_por_validar = 0 OR @has_validado = 0);

-- Preparar el ALTER TABLE solo si es necesario
SET @sql_update_enum = IF(@update_enum,
    "ALTER TABLE asistencias MODIFY COLUMN estatus ENUM('Presente', 'Falta', 'Retardo', 'Permiso', 'Vacaciones', 'Incapacidad', 'Por Validar', 'Validado') DEFAULT 'Presente'",
    'SELECT 1'
);

PREPARE stmt FROM @sql_update_enum;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar campo para hora de salida real (cuando se valida manualmente)
CALL add_column_if_not_exists('asistencias', 'hora_salida_real', 
    'DATETIME NULL COMMENT "Hora de salida real registrada al validar" AFTER hora_salida');

-- Agregar campo para indicar si fue auto-cortado
CALL add_column_if_not_exists('asistencias', 'auto_cortado', 
    'TINYINT(1) DEFAULT 0 COMMENT "1 si la salida fue agregada automáticamente" AFTER hora_salida_real');

-- Agregar campo para usuario que validó
CALL add_column_if_not_exists('asistencias', 'validado_por_id', 
    'INT NULL COMMENT "ID del usuario que validó la asistencia" AFTER auto_cortado');

-- Agregar campo para fecha de validación
CALL add_column_if_not_exists('asistencias', 'fecha_validacion', 
    'DATETIME NULL COMMENT "Fecha y hora cuando se validó" AFTER validado_por_id');

-- Agregar foreign key para validado_por_id
SET @fk_exists = (
    SELECT COUNT(*)
    FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = DATABASE()
        AND TABLE_NAME = 'asistencias'
        AND CONSTRAINT_NAME = 'fk_asistencia_validador'
);

SET @sql_add_fk = IF(@fk_exists = 0,
    'ALTER TABLE asistencias ADD CONSTRAINT fk_asistencia_validador FOREIGN KEY (validado_por_id) REFERENCES usuarios(id) ON DELETE SET NULL',
    'SELECT 1'
);

PREPARE stmt FROM @sql_add_fk;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================================
-- FUNCIÓN: Obtener horario de salida de sucursal por día
-- ============================================================
DELIMITER $$

DROP FUNCTION IF EXISTS obtener_hora_salida_sucursal$$

CREATE FUNCTION obtener_hora_salida_sucursal(
    p_sucursal_id INT,
    p_fecha DATE
) RETURNS TIME
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_hora_salida TIME;
    DECLARE v_dia_semana INT;
    DECLARE v_horario_toda_semana TINYINT(1);
    
    -- Obtener el día de la semana (1=Lunes, 7=Domingo)
    -- MySQL DAYOFWEEK: 1=Domingo, 2=Lunes, ..., 7=Sábado
    -- Convertir a: 1=Lunes, 7=Domingo
    SET v_dia_semana = DAYOFWEEK(p_fecha);
    SET v_dia_semana = IF(v_dia_semana = 1, 7, v_dia_semana - 1);
    
    -- Obtener configuración de horario
    SELECT horario_toda_semana INTO v_horario_toda_semana
    FROM sucursales
    WHERE id = p_sucursal_id;
    
    -- Si aplica horario a toda la semana, usar hora_salida_general
    IF v_horario_toda_semana = 1 THEN
        SELECT hora_salida_general INTO v_hora_salida
        FROM sucursales
        WHERE id = p_sucursal_id;
    ELSE
        -- Obtener horario específico del día
        CASE v_dia_semana
            WHEN 1 THEN -- Lunes
                SELECT hora_salida_lunes INTO v_hora_salida FROM sucursales WHERE id = p_sucursal_id;
            WHEN 2 THEN -- Martes
                SELECT hora_salida_martes INTO v_hora_salida FROM sucursales WHERE id = p_sucursal_id;
            WHEN 3 THEN -- Miércoles
                SELECT hora_salida_miercoles INTO v_hora_salida FROM sucursales WHERE id = p_sucursal_id;
            WHEN 4 THEN -- Jueves
                SELECT hora_salida_jueves INTO v_hora_salida FROM sucursales WHERE id = p_sucursal_id;
            WHEN 5 THEN -- Viernes
                SELECT hora_salida_viernes INTO v_hora_salida FROM sucursales WHERE id = p_sucursal_id;
            WHEN 6 THEN -- Sábado
                SELECT hora_salida_sabado INTO v_hora_salida FROM sucursales WHERE id = p_sucursal_id;
            WHEN 7 THEN -- Domingo
                SELECT hora_salida_domingo INTO v_hora_salida FROM sucursales WHERE id = p_sucursal_id;
        END CASE;
    END IF;
    
    -- Si no hay horario configurado, usar 18:00 por defecto
    IF v_hora_salida IS NULL THEN
        SET v_hora_salida = '18:00:00';
    END IF;
    
    RETURN v_hora_salida;
END$$

DELIMITER ;

-- ============================================================
-- FUNCIÓN: Obtener horario de entrada de sucursal por día
-- ============================================================
DELIMITER $$

DROP FUNCTION IF EXISTS obtener_hora_entrada_sucursal$$

CREATE FUNCTION obtener_hora_entrada_sucursal(
    p_sucursal_id INT,
    p_fecha DATE
) RETURNS TIME
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_hora_entrada TIME;
    DECLARE v_dia_semana INT;
    DECLARE v_horario_toda_semana TINYINT(1);
    
    -- Obtener el día de la semana (1=Lunes, 7=Domingo)
    SET v_dia_semana = DAYOFWEEK(p_fecha);
    SET v_dia_semana = IF(v_dia_semana = 1, 7, v_dia_semana - 1);
    
    -- Obtener configuración de horario
    SELECT horario_toda_semana INTO v_horario_toda_semana
    FROM sucursales
    WHERE id = p_sucursal_id;
    
    -- Si aplica horario a toda la semana, usar hora_entrada_general
    IF v_horario_toda_semana = 1 THEN
        SELECT hora_entrada_general INTO v_hora_entrada
        FROM sucursales
        WHERE id = p_sucursal_id;
    ELSE
        -- Obtener horario específico del día
        CASE v_dia_semana
            WHEN 1 THEN -- Lunes
                SELECT hora_entrada_lunes INTO v_hora_entrada FROM sucursales WHERE id = p_sucursal_id;
            WHEN 2 THEN -- Martes
                SELECT hora_entrada_martes INTO v_hora_entrada FROM sucursales WHERE id = p_sucursal_id;
            WHEN 3 THEN -- Miércoles
                SELECT hora_entrada_miercoles INTO v_hora_entrada FROM sucursales WHERE id = p_sucursal_id;
            WHEN 4 THEN -- Jueves
                SELECT hora_entrada_jueves INTO v_hora_entrada FROM sucursales WHERE id = p_sucursal_id;
            WHEN 5 THEN -- Viernes
                SELECT hora_entrada_viernes INTO v_hora_entrada FROM sucursales WHERE id = p_sucursal_id;
            WHEN 6 THEN -- Sábado
                SELECT hora_entrada_sabado INTO v_hora_entrada FROM sucursales WHERE id = p_sucursal_id;
            WHEN 7 THEN -- Domingo
                SELECT hora_entrada_domingo INTO v_hora_entrada FROM sucursales WHERE id = p_sucursal_id;
        END CASE;
    END IF;
    
    -- Si no hay horario configurado, usar 08:00 por defecto
    IF v_hora_entrada IS NULL THEN
        SET v_hora_entrada = '08:00:00';
    END IF;
    
    RETURN v_hora_entrada;
END$$

DELIMITER ;

-- ============================================================
-- PROCEDIMIENTO: Auto-cortar asistencias sin salida
-- Este procedimiento se puede ejecutar desde un CRON job
-- ============================================================
DELIMITER $$

DROP PROCEDURE IF EXISTS auto_cortar_asistencias$$

CREATE PROCEDURE auto_cortar_asistencias()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_asistencia_id INT;
    DECLARE v_sucursal_id INT;
    DECLARE v_fecha DATE;
    DECLARE v_hora_entrada DATETIME;
    DECLARE v_hora_salida_calculada DATETIME;
    DECLARE v_hora_salida_sucursal TIME;
    DECLARE v_horas_trabajadas DECIMAL(5,2);
    DECLARE v_horas_extra DECIMAL(5,2);
    
    -- Cursor para asistencias sin salida del día anterior o más antiguas
    DECLARE cur CURSOR FOR 
        SELECT a.id, a.sucursal_id, a.fecha, a.hora_entrada
        FROM asistencias a
        WHERE a.hora_salida IS NULL 
          AND a.fecha < CURDATE()
          AND a.estatus = 'Presente';
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    OPEN cur;
    
    read_loop: LOOP
        FETCH cur INTO v_asistencia_id, v_sucursal_id, v_fecha, v_hora_entrada;
        
        IF done THEN
            LEAVE read_loop;
        END IF;
        
        -- Obtener hora de salida de la sucursal para ese día
        SET v_hora_salida_sucursal = obtener_hora_salida_sucursal(v_sucursal_id, v_fecha);
        
        -- Calcular hora de salida completa (fecha + hora)
        SET v_hora_salida_calculada = CONCAT(v_fecha, ' ', v_hora_salida_sucursal);
        
        -- Calcular horas trabajadas
        SET v_horas_trabajadas = TIMESTAMPDIFF(MINUTE, v_hora_entrada, v_hora_salida_calculada) / 60.0;
        
        -- Calcular horas extra (más de 8 horas)
        SET v_horas_extra = GREATEST(0, v_horas_trabajadas - 8);
        
        -- Actualizar asistencia
        UPDATE asistencias
        SET hora_salida = v_hora_salida_calculada,
            horas_trabajadas = v_horas_trabajadas,
            horas_extra = v_horas_extra,
            auto_cortado = 1,
            estatus = 'Por Validar'
        WHERE id = v_asistencia_id;
        
    END LOOP;
    
    CLOSE cur;
    
    -- Retornar número de registros actualizados
    SELECT ROW_COUNT() as registros_actualizados;
END$$

DELIMITER ;

-- ============================================================
-- VISTA: Asistencias con información completa para reporte
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
    -- Información de la sucursal
    s.id as sucursal_id,
    s.nombre as sucursal_nombre,
    s.codigo as sucursal_codigo,
    -- Información de validación
    a.validado_por_id,
    u.nombre as validado_por_nombre,
    a.fecha_validacion,
    -- Información del gerente autorizador (para accesos a otras sucursales)
    a.gerente_autorizador_id,
    CONCAT(g.nombres, ' ', g.apellido_paterno, ' ', IFNULL(g.apellido_materno, '')) as gerente_autorizador_nombre,
    -- Flags útiles
    (a.horas_extra > 0) as tiene_horas_extra,
    (a.estatus = 'Por Validar') as requiere_validacion,
    a.fecha_creacion
FROM asistencias a
INNER JOIN empleados e ON a.empleado_id = e.id
LEFT JOIN sucursales s ON a.sucursal_id = s.id
LEFT JOIN usuarios u ON a.validado_por_id = u.id
LEFT JOIN empleados g ON a.gerente_autorizador_id = g.id;

-- ============================================================
-- DATOS INICIALES: Configurar horarios para sucursales existentes
-- ============================================================

-- Configurar horario general para todas las sucursales existentes
UPDATE sucursales
SET horario_toda_semana = 1,
    hora_entrada_general = '08:00:00',
    hora_salida_general = '18:00:00'
WHERE horario_toda_semana IS NULL OR horario_toda_semana = 0;

-- ============================================================
-- LIMPIAR PROCEDIMIENTOS TEMPORALES
-- ============================================================

DROP PROCEDURE IF EXISTS add_column_if_not_exists;

-- ============================================================
-- FIN DE LA MIGRACIÓN
-- ============================================================

SELECT 'Migración de Validación de Horas Extras completada exitosamente' as mensaje;

-- Verificación de campos agregados
SELECT 
    COUNT(*) as sucursales_con_horarios
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'sucursales' 
    AND COLUMN_NAME = 'horario_toda_semana';

SELECT 
    COUNT(*) as asistencias_con_validacion
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'asistencias' 
    AND COLUMN_NAME = 'hora_salida_real';

-- Mostrar configuración actual de sucursales
SELECT 
    id,
    nombre,
    codigo,
    horario_toda_semana,
    hora_entrada_general,
    hora_salida_general
FROM sucursales
WHERE activo = 1;
