-- ============================================================
-- Migration: Control de Asistencia Improvements
-- Date: 2026-01-20
-- Description: Ensure sucursal_id and turno_id exist in empleados table
--              and add any missing indexes
-- ============================================================

-- Selecciona la base de datos
USE recursos_humanos;

-- Step 1: Add sucursal_id if it doesn't exist
SET @dbname = DATABASE();
SET @tablename = 'empleados';
SET @columnname = 'sucursal_id';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      TABLE_SCHEMA = @dbname
      AND TABLE_NAME = @tablename
      AND COLUMN_NAME = @columnname
  ) > 0,
  'SELECT 1',
  'ALTER TABLE empleados ADD COLUMN sucursal_id INT AFTER salario_mensual'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Step 2: Add turno_id if it doesn't exist
SET @columnname = 'turno_id';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      TABLE_SCHEMA = @dbname
      AND TABLE_NAME = @tablename
      AND COLUMN_NAME = @columnname
  ) > 0,
  'SELECT 1',
  'ALTER TABLE empleados ADD COLUMN turno_id INT AFTER sucursal_id'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Step 3: Add codigo_empleado if it doesn't exist
SET @columnname = 'codigo_empleado';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      TABLE_SCHEMA = @dbname
      AND TABLE_NAME = @tablename
      AND COLUMN_NAME = @columnname
  ) > 0,
  'SELECT 1',
  'ALTER TABLE empleados ADD COLUMN codigo_empleado VARCHAR(6) AFTER numero_empleado'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Step 4: Add foreign key for sucursal_id (ignore error if already exists)
SET @fk_exists = (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND CONSTRAINT_NAME = 'fk_empleado_sucursal'
);

SET @preparedStatement = IF(@fk_exists > 0,
  'SELECT 1',
  'ALTER TABLE empleados ADD CONSTRAINT fk_empleado_sucursal FOREIGN KEY (sucursal_id) REFERENCES sucursales(id) ON DELETE SET NULL'
);
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Step 5: Add foreign key for turno_id (ignore error if already exists)
SET @fk_exists = (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND CONSTRAINT_NAME = 'fk_empleado_turno'
);

SET @preparedStatement = IF(@fk_exists > 0,
  'SELECT 1',
  'ALTER TABLE empleados ADD CONSTRAINT fk_empleado_turno FOREIGN KEY (turno_id) REFERENCES turnos(id) ON DELETE SET NULL'
);
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Step 6: Generate codigo_empleado for existing employees that don't have one
UPDATE empleados 
SET codigo_empleado = CONCAT('183', LPAD(CAST(SUBSTRING(numero_empleado, 4) AS UNSIGNED), 3, '0'))
WHERE (codigo_empleado IS NULL OR codigo_empleado = '') 
  AND numero_empleado LIKE 'EMP%';

-- Step 7: For any employees without a numero_empleado pattern, generate sequential codes
SET @counter = (SELECT COALESCE(MAX(CAST(SUBSTRING(codigo_empleado, 4) AS UNSIGNED)), 0) FROM empleados WHERE codigo_empleado LIKE '183%');

UPDATE empleados 
SET codigo_empleado = CONCAT('183', LPAD((@counter := @counter + 1), 3, '0'))
WHERE codigo_empleado IS NULL OR codigo_empleado = '';

-- Step 8: Add index for codigo_empleado if it doesn't exist
SET @index_exists = (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.STATISTICS
  WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND INDEX_NAME = 'idx_codigo_empleado'
);

SET @preparedStatement = IF(@index_exists > 0,
  'SELECT 1',
  'ALTER TABLE empleados ADD INDEX idx_codigo_empleado (codigo_empleado)'
);
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Step 9: Add index for sucursal_id if it doesn't exist
SET @index_exists = (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.STATISTICS
  WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND INDEX_NAME = 'idx_sucursal_id'
);

SET @preparedStatement = IF(@index_exists > 0,
  'SELECT 1',
  'ALTER TABLE empleados ADD INDEX idx_sucursal_id (sucursal_id)'
);
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Step 10: Ensure tipo column exists in periodos_nomina
SET @tablename = 'periodos_nomina';
SET @columnname = 'tipo';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      TABLE_SCHEMA = @dbname
      AND TABLE_NAME = @tablename
      AND COLUMN_NAME = @columnname
  ) > 0,
  'SELECT 1',
  'ALTER TABLE periodos_nomina ADD COLUMN tipo ENUM(''Semanal'', ''Quincenal'', ''Mensual'') DEFAULT ''Quincenal'' AFTER id'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Verificación final
SELECT 
    'empleados' as tabla,
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'empleados' AND COLUMN_NAME = 'sucursal_id') as sucursal_id_existe,
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'empleados' AND COLUMN_NAME = 'turno_id') as turno_id_existe,
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'empleados' AND COLUMN_NAME = 'codigo_empleado') as codigo_empleado_existe
UNION ALL
SELECT 
    'periodos_nomina' as tabla,
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'periodos_nomina' AND COLUMN_NAME = 'tipo') as tipo_existe,
    0 as col2,
    0 as col3;
