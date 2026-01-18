-- ============================================================
-- Migration: Employee Improvements
-- Date: 2026-01-18
-- Description: Add codigo_empleado, sucursal_id, and turno_id to empleados table
-- ============================================================

USE rrhh_sinforosa;

-- Alter empleados table to add new fields
ALTER TABLE empleados 
ADD COLUMN codigo_empleado VARCHAR(6) UNIQUE AFTER numero_empleado,
ADD COLUMN sucursal_id INT AFTER salario_mensual,
ADD COLUMN turno_id INT AFTER sucursal_id;

-- Add foreign keys
ALTER TABLE empleados
ADD CONSTRAINT fk_empleado_sucursal FOREIGN KEY (sucursal_id) REFERENCES sucursales(id) ON DELETE SET NULL,
ADD CONSTRAINT fk_empleado_turno FOREIGN KEY (turno_id) REFERENCES turnos(id) ON DELETE SET NULL;

-- Add index for codigo_empleado for fast lookups
ALTER TABLE empleados
ADD INDEX idx_codigo_empleado (codigo_empleado);

-- Generate codigo_empleado for existing employees based on their numero_empleado
-- Convert EMP001 to 183001, EMP002 to 183002, etc.
-- Using 183 as prefix (can be customized)
UPDATE empleados 
SET codigo_empleado = CONCAT('183', LPAD(CAST(SUBSTRING(numero_empleado, 4) AS UNSIGNED), 3, '0'))
WHERE codigo_empleado IS NULL AND numero_empleado LIKE 'EMP%';

-- For any employees without a numero_empleado pattern, generate sequential codes
SET @counter = 0;
UPDATE empleados 
SET codigo_empleado = CONCAT('183', LPAD(@counter := @counter + 1, 3, '0'))
WHERE codigo_empleado IS NULL;

-- Make codigo_empleado NOT NULL after populating existing records
ALTER TABLE empleados 
MODIFY codigo_empleado VARCHAR(6) NOT NULL;
