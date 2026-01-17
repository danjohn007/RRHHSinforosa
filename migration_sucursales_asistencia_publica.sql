-- ============================================================
-- ACTUALIZACIÓN DE SCHEMA - MÓDULO DE SUCURSALES Y ASISTENCIA PÚBLICA
-- Fecha: 2026-01-17
-- Descripción: Agrega módulo de sucursales, vista pública de asistencia,
--              códigos únicos de empleados y mejoras al sistema
-- ============================================================

USE recursos_humanos;

-- ============================================================
-- TABLA: sucursales (Gestión de Sucursales)
-- ============================================================
CREATE TABLE IF NOT EXISTS sucursales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    codigo VARCHAR(20) UNIQUE NOT NULL,
    direccion TEXT,
    telefono VARCHAR(15),
    url_publica VARCHAR(255) UNIQUE COMMENT 'URL única para vista pública de asistencia',
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_codigo (codigo),
    INDEX idx_url_publica (url_publica),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: sucursal_gerentes (Gerentes asignados a sucursales)
-- ============================================================
CREATE TABLE IF NOT EXISTS sucursal_gerentes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sucursal_id INT NOT NULL,
    empleado_id INT NOT NULL,
    fecha_asignacion DATE NOT NULL DEFAULT (CURRENT_DATE),
    activo TINYINT(1) DEFAULT 1,
    FOREIGN KEY (sucursal_id) REFERENCES sucursales(id) ON DELETE CASCADE,
    FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE CASCADE,
    UNIQUE KEY unique_sucursal_gerente (sucursal_id, empleado_id),
    INDEX idx_sucursal (sucursal_id),
    INDEX idx_empleado (empleado_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: sucursal_dispositivos (Dispositivos Shelly asignados a sucursales)
-- ============================================================
CREATE TABLE IF NOT EXISTS sucursal_dispositivos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sucursal_id INT NOT NULL,
    dispositivo_shelly_id INT NOT NULL,
    tipo_accion ENUM('Entrada', 'Salida', 'Ambos') DEFAULT 'Ambos',
    activo TINYINT(1) DEFAULT 1,
    fecha_asignacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sucursal_id) REFERENCES sucursales(id) ON DELETE CASCADE,
    FOREIGN KEY (dispositivo_shelly_id) REFERENCES dispositivos_shelly(id) ON DELETE CASCADE,
    UNIQUE KEY unique_sucursal_dispositivo (sucursal_id, dispositivo_shelly_id),
    INDEX idx_sucursal (sucursal_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- MODIFICACIONES A TABLA: empleados
-- ============================================================

-- Agregar código único de 6 dígitos para cada empleado
ALTER TABLE empleados 
ADD COLUMN IF NOT EXISTS codigo_empleado VARCHAR(6) UNIQUE COMMENT 'Código único de 6 dígitos para registro de asistencia' AFTER numero_empleado;

-- Agregar sucursal_id (requerido)
ALTER TABLE empleados 
ADD COLUMN IF NOT EXISTS sucursal_id INT COMMENT 'Sucursal de trabajo del empleado' AFTER departamento;

-- Agregar turno_id para asignar horarios
ALTER TABLE empleados 
ADD COLUMN IF NOT EXISTS turno_id INT COMMENT 'Turno/horario asignado al empleado' AFTER sucursal_id;

-- Agregar índices
ALTER TABLE empleados ADD INDEX IF NOT EXISTS idx_codigo_empleado (codigo_empleado);
ALTER TABLE empleados ADD INDEX IF NOT EXISTS idx_sucursal (sucursal_id);
ALTER TABLE empleados ADD INDEX IF NOT EXISTS idx_turno (turno_id);

-- Agregar foreign keys
ALTER TABLE empleados 
ADD CONSTRAINT fk_empleado_sucursal 
FOREIGN KEY IF NOT EXISTS (sucursal_id) REFERENCES sucursales(id) ON DELETE SET NULL;

ALTER TABLE empleados 
ADD CONSTRAINT fk_empleado_turno 
FOREIGN KEY IF NOT EXISTS (turno_id) REFERENCES turnos(id) ON DELETE SET NULL;

-- ============================================================
-- MODIFICACIONES A TABLA: asistencias
-- ============================================================

-- Agregar campos para fotos de entrada y salida
ALTER TABLE asistencias 
ADD COLUMN IF NOT EXISTS foto_entrada VARCHAR(255) COMMENT 'Ruta de la foto de entrada' AFTER dispositivo_entrada;

ALTER TABLE asistencias 
ADD COLUMN IF NOT EXISTS foto_salida VARCHAR(255) COMMENT 'Ruta de la foto de salida' AFTER dispositivo_salida;

-- Agregar campo para sucursal donde se registró
ALTER TABLE asistencias 
ADD COLUMN IF NOT EXISTS sucursal_id INT COMMENT 'Sucursal donde se registró la asistencia' AFTER empleado_id;

-- Agregar campo para código de gerente autorizador (cuando es otra sucursal)
ALTER TABLE asistencias 
ADD COLUMN IF NOT EXISTS gerente_autorizador_id INT COMMENT 'ID del gerente que autorizó acceso a otra sucursal' AFTER sucursal_id;

-- Agregar índice para sucursal
ALTER TABLE asistencias ADD INDEX IF NOT EXISTS idx_asistencia_sucursal (sucursal_id);

-- Agregar foreign key
ALTER TABLE asistencias 
ADD CONSTRAINT fk_asistencia_sucursal 
FOREIGN KEY IF NOT EXISTS (sucursal_id) REFERENCES sucursales(id) ON DELETE SET NULL;

ALTER TABLE asistencias 
ADD CONSTRAINT fk_asistencia_gerente_autorizador 
FOREIGN KEY IF NOT EXISTS (gerente_autorizador_id) REFERENCES empleados(id) ON DELETE SET NULL;

-- ============================================================
-- FUNCIÓN: Generar código único de 6 dígitos
-- ============================================================
DELIMITER $$

DROP FUNCTION IF EXISTS generar_codigo_empleado$$

CREATE FUNCTION generar_codigo_empleado() 
RETURNS VARCHAR(6)
DETERMINISTIC
BEGIN
    DECLARE nuevo_codigo VARCHAR(6);
    DECLARE codigo_existe INT;
    
    -- Intentar hasta 100 veces generar un código único
    SET @intentos = 0;
    
    REPEAT
        -- Generar número aleatorio de 6 dígitos (100000 a 999999)
        SET nuevo_codigo = LPAD(FLOOR(100000 + RAND() * 900000), 6, '0');
        
        -- Verificar si existe
        SELECT COUNT(*) INTO codigo_existe 
        FROM empleados 
        WHERE codigo_empleado = nuevo_codigo;
        
        SET @intentos = @intentos + 1;
        
    UNTIL codigo_existe = 0 OR @intentos >= 100
    END REPEAT;
    
    IF codigo_existe > 0 THEN
        -- Si después de 100 intentos no encontramos código único, usar timestamp
        SET nuevo_codigo = RIGHT(UNIX_TIMESTAMP(), 6);
    END IF;
    
    RETURN nuevo_codigo;
END$$

DELIMITER ;

-- ============================================================
-- TRIGGER: Asignar código automáticamente al crear empleado
-- ============================================================
DELIMITER $$

DROP TRIGGER IF EXISTS before_empleado_insert$$

CREATE TRIGGER before_empleado_insert
BEFORE INSERT ON empleados
FOR EACH ROW
BEGIN
    -- Solo asignar código si no se proporciona uno
    IF NEW.codigo_empleado IS NULL OR NEW.codigo_empleado = '' THEN
        SET NEW.codigo_empleado = generar_codigo_empleado();
    END IF;
END$$

DELIMITER ;

-- ============================================================
-- ACTUALIZAR empleados existentes con códigos únicos
-- ============================================================

-- Generar códigos para empleados existentes que no tienen código
UPDATE empleados 
SET codigo_empleado = generar_codigo_empleado()
WHERE codigo_empleado IS NULL OR codigo_empleado = '';

-- ============================================================
-- DATOS DE EJEMPLO: Sucursales
-- ============================================================

INSERT IGNORE INTO sucursales (nombre, codigo, direccion, telefono, url_publica, activo) VALUES
('Sucursal Centro', 'SUC-CENTRO', 'Av. Constituyentes #100, Centro, Querétaro', '4421234567', 'centro', 1),
('Sucursal Juriquilla', 'SUC-JURIQ', 'Blvd. Juriquilla #500, Juriquilla, Querétaro', '4421234568', 'juriquilla', 1),
('Sucursal Corregidora', 'SUC-CORREG', 'Av. Principal #200, Corregidora, Querétaro', '4421234569', 'corregidora', 1);

-- ============================================================
-- ASIGNAR sucursal a empleados existentes (ejemplo)
-- ============================================================

-- Asignar sucursal Centro a empleados 1-4
UPDATE empleados SET sucursal_id = (SELECT id FROM sucursales WHERE codigo = 'SUC-CENTRO' LIMIT 1)
WHERE id IN (1, 2, 3, 4) AND sucursal_id IS NULL;

-- Asignar sucursal Juriquilla a empleados 5-6
UPDATE empleados SET sucursal_id = (SELECT id FROM sucursales WHERE codigo = 'SUC-JURIQ' LIMIT 1)
WHERE id IN (5, 6) AND sucursal_id IS NULL;

-- Asignar sucursal Corregidora a empleados 7-8
UPDATE empleados SET sucursal_id = (SELECT id FROM sucursales WHERE codigo = 'SUC-CORREG' LIMIT 1)
WHERE id IN (7, 8) AND sucursal_id IS NULL;

-- ============================================================
-- ASIGNAR turnos a empleados existentes
-- ============================================================

-- Asignar turno Matutino a la mayoría de empleados
UPDATE empleados SET turno_id = (SELECT id FROM turnos WHERE nombre = 'Matutino' LIMIT 1)
WHERE turno_id IS NULL AND id <= 6;

-- Asignar turno Vespertino a algunos empleados
UPDATE empleados SET turno_id = (SELECT id FROM turnos WHERE nombre = 'Vespertino' LIMIT 1)
WHERE turno_id IS NULL AND id > 6;

-- ============================================================
-- VISTA: Empleados con sucursal y turno
-- ============================================================

CREATE OR REPLACE VIEW vista_empleados_completo AS
SELECT 
    e.id,
    e.numero_empleado,
    e.codigo_empleado,
    CONCAT(e.nombres, ' ', e.apellido_paterno, ' ', IFNULL(e.apellido_materno, '')) as nombre_completo,
    e.departamento,
    e.puesto,
    e.estatus,
    s.nombre as sucursal_nombre,
    s.codigo as sucursal_codigo,
    t.nombre as turno_nombre,
    t.hora_entrada,
    t.hora_salida,
    t.horas_laborales,
    e.fecha_ingreso,
    TIMESTAMPDIFF(YEAR, e.fecha_ingreso, CURDATE()) as anios_antiguedad
FROM empleados e
LEFT JOIN sucursales s ON e.sucursal_id = s.id
LEFT JOIN turnos t ON e.turno_id = t.id;

-- ============================================================
-- ÍNDICES ADICIONALES PARA OPTIMIZACIÓN
-- ============================================================

CREATE INDEX IF NOT EXISTS idx_asistencias_periodo ON asistencias(fecha, empleado_id);
CREATE INDEX IF NOT EXISTS idx_empleados_activos_sucursal ON empleados(estatus, sucursal_id);

-- ============================================================
-- FIN DE LA MIGRACIÓN
-- ============================================================

-- Verificación de la migración
SELECT 'Migración completada exitosamente' as mensaje;
SELECT COUNT(*) as total_sucursales FROM sucursales;
SELECT COUNT(*) as empleados_con_codigo FROM empleados WHERE codigo_empleado IS NOT NULL;
SELECT COUNT(*) as empleados_con_sucursal FROM empleados WHERE sucursal_id IS NOT NULL;
SELECT COUNT(*) as empleados_con_turno FROM empleados WHERE turno_id IS NOT NULL;
