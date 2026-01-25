-- ============================================================
-- MIGRACIÓN: Actualizar Procedimiento Auto-Cortar Asistencias
-- Fecha: 2026-01-25
-- Descripción: Actualiza el procedimiento para incluir sucursal_salida_id
-- 
-- DEPENDENCIA: Requiere que migration_validacion_horas_extras.sql
--              haya sido ejecutado previamente (define función
--              obtener_hora_salida_sucursal)
-- ============================================================

USE recursos_humanos;

-- ============================================================
-- PASO 1: Agregar columna sucursal_salida_id si no existe
-- ============================================================
SET @dbname = DATABASE();
SET @tablename = 'asistencias';
SET @columnname = 'sucursal_salida_id';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' INT NULL AFTER sucursal_id, ADD CONSTRAINT fk_asistencias_sucursal_salida FOREIGN KEY (sucursal_salida_id) REFERENCES sucursales(id)')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- ============================================================
-- PASO 2: PROCEDIMIENTO ACTUALIZADO: Auto-cortar asistencias sin salida
-- Ahora incluye sucursal_salida_id
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
        -- La sucursal de salida es la misma que la de entrada en auto-corte
        UPDATE asistencias
        SET hora_salida = v_hora_salida_calculada,
            horas_trabajadas = v_horas_trabajadas,
            horas_extra = v_horas_extra,
            auto_cortado = 1,
            estatus = 'Por Validar',
            sucursal_salida_id = v_sucursal_id
        WHERE id = v_asistencia_id;
        
    END LOOP;
    
    CLOSE cur;
    
    -- Retornar número de registros actualizados
    SELECT ROW_COUNT() as registros_actualizados;
END$$

DELIMITER ;

-- ============================================================
-- PASO 3: Actualizar registros históricos
-- ============================================================
-- Ejecutar el procedimiento una vez para actualizar registros históricos
-- que ya tienen hora_salida pero no tienen sucursal_salida_id
UPDATE asistencias
SET sucursal_salida_id = sucursal_id
WHERE hora_salida IS NOT NULL 
  AND sucursal_salida_id IS NULL
  AND sucursal_id IS NOT NULL;
