-- ============================================================
-- Migration:  Employee Improvements
-- Date: 2026-01-18
-- Description: Add codigo_empleado, sucursal_id, and turno_id to empleados table
-- ============================================================

-- Asegúrate de seleccionar la base de datos rrhh_sinforosa desde phpMyAdmin

-- Step 1: Add sucursal_id only if it doesn't exist
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

-- Step 2: Add turno_id only if it doesn't exist
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

-- Step 3: Add foreign key for sucursal_id (ignore error if already exists)
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

-- Step 4: Add foreign key for turno_id (ignore error if already exists)
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

-- Step 5: Add index for codigo_empleado (ignore if already exists)
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

-- Step 6: Generate codigo_empleado for existing employees that don't have one
UPDATE empleados 
SET codigo_empleado = CONCAT('183', LPAD(CAST(SUBSTRING(numero_empleado, 4) AS UNSIGNED), 3, '0'))
WHERE (codigo_empleado IS NULL OR codigo_empleado = '') AND numero_empleado LIKE 'EMP%';

-- Step 7: For any employees without a numero_empleado pattern, generate sequential codes
SET @counter = (SELECT COALESCE(MAX(CAST(SUBSTRING(codigo_empleado, 4) AS UNSIGNED)), 0) FROM empleados WHERE codigo_empleado LIKE '183%');

UPDATE empleados 
SET codigo_empleado = CONCAT('183', LPAD((@counter := @counter + 1), 3, '0'))
WHERE codigo_empleado IS NULL OR codigo_empleado = '';

-- Step 8: Make codigo_empleado NOT NULL if it's currently nullable
SET @is_nullable = (
  SELECT IS_NULLABLE
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = 'codigo_empleado'
);

SET @preparedStatement = IF(@is_nullable = 'YES',
  'ALTER TABLE empleados MODIFY COLUMN codigo_empleado VARCHAR(6) NOT NULL',
  'SELECT 1'
);
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Step 9: Add UNIQUE constraint if it doesn't exist
SET @unique_exists = (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND CONSTRAINT_NAME = 'codigo_empleado'
    AND CONSTRAINT_TYPE = 'UNIQUE'
);

SET @preparedStatement = IF(@unique_exists > 0,
  'SELECT 1',
  'ALTER TABLE empleados ADD UNIQUE INDEX codigo_empleado (codigo_empleado)'
);
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;
