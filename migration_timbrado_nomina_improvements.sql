-- ============================================================
-- MIGRACIÓN: Timbrado de Nómina y Mejoras al Sistema
-- Fecha: 2026-01-24
-- Descripción: Agrega funcionalidad de timbrado CFDI, horas extras en nómina,
--              importación de empleados, y correcciones en búsqueda
-- ============================================================

USE recursos_humanos;

-- ============================================================
-- PROCEDIMIENTOS AUXILIARES PARA LA MIGRACIÓN
-- ============================================================

DELIMITER $$

-- Procedimiento para agregar columna si no existe
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

-- Procedimiento para agregar índice si no existe
DROP PROCEDURE IF EXISTS add_index_if_not_exists$$

CREATE PROCEDURE add_index_if_not_exists(
    IN tableName VARCHAR(128),
    IN indexName VARCHAR(128),
    IN indexDefinition TEXT
)
BEGIN
    DECLARE index_count INT;
    
    SELECT COUNT(*) INTO index_count
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME COLLATE utf8_general_ci = tableName COLLATE utf8_general_ci
        AND INDEX_NAME COLLATE utf8_general_ci = indexName COLLATE utf8_general_ci;
    
    IF index_count = 0 THEN
        SET @sql = CONCAT('ALTER TABLE ', tableName, ' ADD INDEX ', indexName, ' ', indexDefinition);
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END$$

DELIMITER ;

-- ============================================================
-- 1. MODIFICACIONES A TABLA: nomina_detalle
-- Agregar horas de trabajo y horas extras
-- ============================================================

CALL add_column_if_not_exists('nomina_detalle', 'horas_trabajadas', 
    'DECIMAL(10,2) DEFAULT 0 COMMENT "Total de horas trabajadas en el período" AFTER dias_trabajados');

CALL add_column_if_not_exists('nomina_detalle', 'horas_extras', 
    'DECIMAL(10,2) DEFAULT 0 COMMENT "Total de horas extras trabajadas en el período" AFTER horas_trabajadas');

CALL add_column_if_not_exists('nomina_detalle', 'pago_horas_extras', 
    'DECIMAL(10,2) DEFAULT 0 COMMENT "Monto pagado por horas extras" AFTER horas_extras');

-- Campos para timbrado CFDI
CALL add_column_if_not_exists('nomina_detalle', 'cfdi_uuid', 
    'VARCHAR(36) UNIQUE COMMENT "UUID del CFDI timbrado" AFTER total_neto');

CALL add_column_if_not_exists('nomina_detalle', 'cfdi_serie', 
    'VARCHAR(25) COMMENT "Serie del CFDI" AFTER cfdi_uuid');

CALL add_column_if_not_exists('nomina_detalle', 'cfdi_folio', 
    'VARCHAR(40) COMMENT "Folio del CFDI" AFTER cfdi_serie');

CALL add_column_if_not_exists('nomina_detalle', 'cfdi_fecha_timbrado', 
    'DATETIME COMMENT "Fecha y hora de timbrado" AFTER cfdi_folio');

CALL add_column_if_not_exists('nomina_detalle', 'cfdi_certificado_sat', 
    'VARCHAR(255) COMMENT "Número de certificado del SAT" AFTER cfdi_fecha_timbrado');

CALL add_column_if_not_exists('nomina_detalle', 'cfdi_xml', 
    'LONGTEXT COMMENT "XML del CFDI timbrado" AFTER cfdi_certificado_sat');

CALL add_column_if_not_exists('nomina_detalle', 'cfdi_pdf_url', 
    'VARCHAR(500) COMMENT "URL del PDF del CFDI" AFTER cfdi_xml');

CALL add_column_if_not_exists('nomina_detalle', 'cfdi_estatus', 
    'ENUM("Sin Timbrar", "Timbrado", "Cancelado", "Error") DEFAULT "Sin Timbrar" COMMENT "Estatus del timbrado" AFTER cfdi_pdf_url');

CALL add_column_if_not_exists('nomina_detalle', 'cfdi_error_mensaje', 
    'TEXT COMMENT "Mensaje de error en caso de fallo de timbrado" AFTER cfdi_estatus');

CALL add_column_if_not_exists('nomina_detalle', 'cfdi_fecha_cancelacion', 
    'DATETIME COMMENT "Fecha de cancelación del CFDI" AFTER cfdi_error_mensaje');

CALL add_column_if_not_exists('nomina_detalle', 'cfdi_motivo_cancelacion', 
    'TEXT COMMENT "Motivo de cancelación del CFDI" AFTER cfdi_fecha_cancelacion');

-- Agregar índices para campos CFDI
CALL add_index_if_not_exists('nomina_detalle', 'idx_cfdi_uuid', '(cfdi_uuid)');
CALL add_index_if_not_exists('nomina_detalle', 'idx_cfdi_estatus', '(cfdi_estatus)');
CALL add_index_if_not_exists('nomina_detalle', 'idx_periodo_cfdi', '(periodo_id, cfdi_estatus)');

-- ============================================================
-- 2. TABLA: nomina_timbrado_log
-- Registro de intentos y resultados de timbrado
-- ============================================================

CREATE TABLE IF NOT EXISTS nomina_timbrado_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomina_detalle_id INT NOT NULL,
    accion ENUM('Timbrar', 'Cancelar', 'Consultar') NOT NULL,
    resultado ENUM('Exitoso', 'Error', 'Pendiente') NOT NULL,
    codigo_respuesta VARCHAR(10) COMMENT 'Código de respuesta de la API',
    mensaje TEXT COMMENT 'Mensaje de respuesta o error',
    datos_request JSON COMMENT 'Datos enviados en la petición',
    datos_response JSON COMMENT 'Respuesta completa de la API',
    usuario_id INT COMMENT 'Usuario que ejecutó la acción',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (nomina_detalle_id) REFERENCES nomina_detalle(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_nomina_detalle (nomina_detalle_id),
    INDEX idx_fecha (fecha_registro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 3. TABLA: nomina_importaciones
-- Registro de importaciones de datos
-- ============================================================

CREATE TABLE IF NOT EXISTS nomina_importaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('Empleados', 'Asistencias', 'Nomina', 'Conceptos') NOT NULL,
    archivo_nombre VARCHAR(255) NOT NULL,
    archivo_ruta VARCHAR(500),
    total_registros INT DEFAULT 0,
    registros_exitosos INT DEFAULT 0,
    registros_errores INT DEFAULT 0,
    estatus ENUM('Procesando', 'Completado', 'Error', 'Parcial') DEFAULT 'Procesando',
    errores_detalle JSON COMMENT 'Detalle de errores encontrados',
    usuario_id INT NOT NULL,
    fecha_importacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_tipo (tipo),
    INDEX idx_estatus (estatus),
    INDEX idx_fecha (fecha_importacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 4. MODIFICACIONES A TABLA: empleados
-- Agregar campo para controlar elegibilidad como gerente
-- ============================================================

-- Campo para indicar si un empleado puede ser gerente (independiente del usuario)
CALL add_column_if_not_exists('empleados', 'puede_ser_gerente', 
    'TINYINT(1) DEFAULT 0 COMMENT "Indica si el empleado puede ser asignado como gerente de sucursal" AFTER puesto');

-- Por defecto, empleados con ciertos puestos pueden ser gerentes
UPDATE empleados 
SET puede_ser_gerente = 1 
WHERE LOWER(puesto) LIKE '%gerente%' 
   OR LOWER(puesto) LIKE '%director%'
   OR LOWER(puesto) LIKE '%coordinador%'
   OR LOWER(puesto) LIKE '%jefe%'
   OR LOWER(puesto) LIKE '%supervisor%'
   OR usuario_id IN (SELECT id FROM usuarios WHERE rol IN ('gerente', 'admin', 'rrhh'));

-- Agregar índice
CALL add_index_if_not_exists('empleados', 'idx_puede_ser_gerente', '(puede_ser_gerente)');

-- ============================================================
-- 5. VISTA: Vista mejorada de empleados con búsqueda por nombre completo
-- ============================================================

-- Vista para facilitar búsquedas por nombre completo
CREATE OR REPLACE VIEW vista_empleados_busqueda AS
SELECT 
    e.id,
    e.numero_empleado,
    e.codigo_empleado,
    e.nombres,
    e.apellido_paterno,
    e.apellido_materno,
    CONCAT(e.nombres, ' ', e.apellido_paterno, ' ', COALESCE(e.apellido_materno, '')) as nombre_completo,
    CONCAT(e.apellido_paterno, ' ', COALESCE(e.apellido_materno, ''), ' ', e.nombres) as nombre_completo_inverso,
    LOWER(CONCAT(e.nombres, ' ', e.apellido_paterno, ' ', COALESCE(e.apellido_materno, ''))) as nombre_completo_lower,
    e.email_personal,
    e.celular,
    e.telefono,
    e.departamento,
    e.puesto,
    e.estatus,
    e.sucursal_id,
    s.nombre as sucursal_nombre,
    e.puede_ser_gerente,
    e.usuario_id,
    u.rol as usuario_rol
FROM empleados e
LEFT JOIN sucursales s ON e.sucursal_id = s.id
LEFT JOIN usuarios u ON e.usuario_id = u.id;

-- ============================================================
-- 6. VISTA: Empleados elegibles para ser gerentes
-- ============================================================

CREATE OR REPLACE VIEW vista_empleados_gerentes AS
SELECT 
    e.id,
    e.numero_empleado,
    e.codigo_empleado,
    CONCAT(e.nombres, ' ', e.apellido_paterno, ' ', COALESCE(e.apellido_materno, '')) as nombre_completo,
    e.puesto,
    e.departamento,
    e.sucursal_id,
    s.nombre as sucursal_nombre,
    e.puede_ser_gerente,
    u.rol as usuario_rol,
    CASE 
        WHEN u.rol IN ('gerente', 'admin', 'rrhh') THEN 1
        WHEN e.puede_ser_gerente = 1 THEN 1
        ELSE 0
    END as es_elegible_gerente
FROM empleados e
LEFT JOIN usuarios u ON e.usuario_id = u.id
LEFT JOIN sucursales s ON e.sucursal_id = s.id
WHERE e.estatus = 'Activo'
  AND (
      u.rol IN ('gerente', 'admin', 'rrhh', 'empleado')
      OR e.puede_ser_gerente = 1
      OR e.usuario_id IS NULL
  );

-- ============================================================
-- 7. TABLA: configuraciones_sistema
-- Configuraciones generales del sistema (incluye API keys)
-- ============================================================

CREATE TABLE IF NOT EXISTS configuraciones_sistema (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(100) UNIQUE NOT NULL COMMENT 'Identificador único de la configuración',
    valor TEXT COMMENT 'Valor de la configuración',
    descripcion TEXT COMMENT 'Descripción de la configuración',
    tipo ENUM('Texto', 'Número', 'Boolean', 'JSON', 'Secreto') DEFAULT 'Texto',
    categoria VARCHAR(50) DEFAULT 'General' COMMENT 'Categoría: General, CFDI, Email, etc.',
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_clave (clave),
    INDEX idx_categoria (categoria)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar configuraciones predeterminadas para CFDI
INSERT INTO configuraciones_sistema (clave, valor, descripcion, tipo, categoria) VALUES
('cfdi_api_url', 'https://api.facturaplus.com/v1', 'URL base de la API de FacturaloPlus', 'Texto', 'CFDI'),
('cfdi_api_key', '', 'API Key de FacturaloPlus', 'Secreto', 'CFDI'),
('cfdi_rfc_emisor', '', 'RFC del emisor (empresa)', 'Texto', 'CFDI'),
('cfdi_razon_social', '', 'Razón social del emisor', 'Texto', 'CFDI'),
('cfdi_regimen_fiscal', '601', 'Clave del régimen fiscal', 'Texto', 'CFDI'),
('cfdi_lugar_expedicion', '', 'Código postal del lugar de expedición', 'Texto', 'CFDI'),
('cfdi_habilitar_timbrado', '0', 'Habilitar timbrado automático de nómina', 'Boolean', 'CFDI'),
('cfdi_ambiente', 'pruebas', 'Ambiente: pruebas o produccion', 'Texto', 'CFDI'),
('importacion_max_file_size', '5242880', 'Tamaño máximo de archivo para importación (bytes) - 5MB por defecto', 'Número', 'Importación'),
('importacion_registros_por_lote', '100', 'Número de registros a procesar por lote', 'Número', 'Importación')
ON DUPLICATE KEY UPDATE fecha_actualizacion = CURRENT_TIMESTAMP;

-- ============================================================
-- 8. TRIGGER: Calcular horas trabajadas y extras en nómina
-- ============================================================

DELIMITER $$

DROP TRIGGER IF EXISTS before_nomina_detalle_insert$$

CREATE TRIGGER before_nomina_detalle_insert
BEFORE INSERT ON nomina_detalle
FOR EACH ROW
BEGIN
    DECLARE horas_estandar DECIMAL(10,2);
    DECLARE horas_extras_calculadas DECIMAL(10,2);
    DECLARE dias_periodo INT;
    
    -- Si no se proporcionan las horas, calcularlas desde asistencias
    IF NEW.horas_trabajadas IS NULL OR NEW.horas_trabajadas = 0 THEN
        -- Obtener fechas del período
        SELECT DATEDIFF(fecha_fin, fecha_inicio) + 1 INTO dias_periodo
        FROM periodos_nomina 
        WHERE id = NEW.periodo_id;
        
        -- Calcular horas estándar (8 horas por día trabajado)
        SET horas_estandar = NEW.dias_trabajados * 8;
        
        -- Calcular horas trabajadas desde asistencias
        SELECT COALESCE(SUM(
            TIMESTAMPDIFF(MINUTE, 
                CONCAT(a.fecha, ' ', a.hora_entrada), 
                CONCAT(a.fecha, ' ', COALESCE(a.hora_salida, a.hora_entrada))
            ) / 60.0
        ), 0) INTO NEW.horas_trabajadas
        FROM asistencias a
        INNER JOIN periodos_nomina p ON NEW.periodo_id = p.id
        WHERE a.empleado_id = NEW.empleado_id
          AND a.fecha BETWEEN p.fecha_inicio AND p.fecha_fin
          AND a.estatus = 'Presente';
        
        -- Si no hay registro de asistencias, usar horas estándar
        IF NEW.horas_trabajadas = 0 THEN
            SET NEW.horas_trabajadas = horas_estandar;
        END IF;
        
        -- Calcular horas extras (todo lo que exceda las horas estándar)
        SET horas_extras_calculadas = GREATEST(0, NEW.horas_trabajadas - horas_estandar);
        
        IF NEW.horas_extras IS NULL OR NEW.horas_extras = 0 THEN
            SET NEW.horas_extras = horas_extras_calculadas;
        END IF;
    END IF;
    
    -- Asegurar que el estatus CFDI tenga un valor por defecto
    IF NEW.cfdi_estatus IS NULL OR NEW.cfdi_estatus = '' THEN
        SET NEW.cfdi_estatus = 'Sin Timbrar';
    END IF;
END$$

DROP TRIGGER IF EXISTS before_nomina_detalle_update$$

CREATE TRIGGER before_nomina_detalle_update
BEFORE UPDATE ON nomina_detalle
FOR EACH ROW
BEGIN
    -- Si se actualiza el CFDI, registrar fecha
    IF OLD.cfdi_estatus != NEW.cfdi_estatus THEN
        IF NEW.cfdi_estatus = 'Timbrado' AND NEW.cfdi_fecha_timbrado IS NULL THEN
            SET NEW.cfdi_fecha_timbrado = NOW();
        END IF;
        IF NEW.cfdi_estatus = 'Cancelado' AND NEW.cfdi_fecha_cancelacion IS NULL THEN
            SET NEW.cfdi_fecha_cancelacion = NOW();
        END IF;
    END IF;
END$$

DELIMITER ;

-- ============================================================
-- 9. FUNCIÓN: Calcular pago de horas extras
-- ============================================================

DELIMITER $$

DROP FUNCTION IF EXISTS calcular_pago_horas_extras$$

CREATE FUNCTION calcular_pago_horas_extras(
    horas_extras DECIMAL(10,2),
    salario_diario DECIMAL(10,2)
) 
RETURNS DECIMAL(10,2)
DETERMINISTIC
BEGIN
    DECLARE pago_total DECIMAL(10,2);
    DECLARE horas_dobles DECIMAL(10,2);
    DECLARE horas_triples DECIMAL(10,2);
    DECLARE salario_hora DECIMAL(10,2);
    
    -- Calcular salario por hora (salario diario / 8 horas)
    SET salario_hora = salario_diario / 8;
    
    -- Primeras 9 horas extras a doble
    SET horas_dobles = LEAST(horas_extras, 9);
    
    -- Horas adicionales a triple
    SET horas_triples = GREATEST(0, horas_extras - 9);
    
    -- Calcular pago total
    SET pago_total = (horas_dobles * salario_hora * 2) + (horas_triples * salario_hora * 3);
    
    RETURN ROUND(pago_total, 2);
END$$

DELIMITER ;

-- ============================================================
-- 10. ACTUALIZACIÓN DE DATOS EXISTENTES
-- ============================================================

-- Actualizar empleados existentes: marcar como elegibles para gerente
-- si tienen usuario con rol gerente/admin/rrhh
UPDATE empleados e
INNER JOIN usuarios u ON e.usuario_id = u.id
SET e.puede_ser_gerente = 1
WHERE u.rol IN ('gerente', 'admin', 'rrhh')
  AND e.puede_ser_gerente = 0;

-- Calcular horas trabajadas y extras para nóminas existentes
UPDATE nomina_detalle nd
SET 
    horas_trabajadas = dias_trabajados * 8,
    horas_extras = 0
WHERE horas_trabajadas IS NULL OR horas_trabajadas = 0;

-- ============================================================
-- 11. PROCEDIMIENTO: Obtener empleados elegibles para gerente
-- ============================================================

DELIMITER $$

DROP PROCEDURE IF EXISTS obtener_empleados_gerentes$$

CREATE PROCEDURE obtener_empleados_gerentes(
    IN p_sucursal_id INT
)
BEGIN
    -- Retorna todos los empleados activos que pueden ser gerentes
    -- Ya sea porque tienen el flag puede_ser_gerente = 1
    -- o porque su usuario tiene rol gerente/admin/rrhh
    SELECT 
        e.id,
        e.numero_empleado,
        e.codigo_empleado,
        CONCAT(e.nombres, ' ', e.apellido_paterno, ' ', COALESCE(e.apellido_materno, '')) as nombre_completo,
        e.puesto,
        e.departamento,
        e.sucursal_id,
        s.nombre as sucursal_nombre,
        u.rol as usuario_rol,
        e.puede_ser_gerente,
        CASE 
            WHEN sg.empleado_id IS NOT NULL THEN 1
            ELSE 0
        END as ya_asignado
    FROM empleados e
    LEFT JOIN usuarios u ON e.usuario_id = u.id
    LEFT JOIN sucursales s ON e.sucursal_id = s.id
    LEFT JOIN sucursal_gerentes sg ON sg.empleado_id = e.id AND sg.sucursal_id = p_sucursal_id AND sg.activo = 1
    WHERE e.estatus = 'Activo'
      AND (
          e.puede_ser_gerente = 1
          OR u.rol IN ('gerente', 'admin', 'rrhh')
      )
    ORDER BY ya_asignado ASC, e.nombres, e.apellido_paterno;
END$$

DELIMITER ;

-- ============================================================
-- 12. ÍNDICES ADICIONALES PARA OPTIMIZACIÓN
-- ============================================================

CALL add_index_if_not_exists('empleados', 'idx_nombres_apellidos', '(nombres, apellido_paterno, apellido_materno)');
CALL add_index_if_not_exists('nomina_detalle', 'idx_empleado_periodo', '(empleado_id, periodo_id)');
CALL add_index_if_not_exists('asistencias', 'idx_empleado_fecha_estatus', '(empleado_id, fecha, estatus)');

-- ============================================================
-- 13. LIMPIAR PROCEDIMIENTOS TEMPORALES
-- ============================================================

DROP PROCEDURE IF EXISTS add_column_if_not_exists;
DROP PROCEDURE IF EXISTS add_index_if_not_exists;

-- ============================================================
-- VERIFICACIÓN DE LA MIGRACIÓN
-- ============================================================

SELECT '=====================================' as separador;
SELECT 'MIGRACIÓN COMPLETADA EXITOSAMENTE' as mensaje;
SELECT '=====================================' as separador;

-- Verificar columnas agregadas a nomina_detalle
SELECT 'Verificando columnas en nomina_detalle...' as paso;
SELECT COLUMN_NAME, COLUMN_TYPE, COLUMN_COMMENT 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'nomina_detalle' 
  AND COLUMN_NAME IN ('horas_trabajadas', 'horas_extras', 'cfdi_uuid', 'cfdi_estatus');

-- Verificar tabla nomina_timbrado_log
SELECT 'Verificando tabla nomina_timbrado_log...' as paso;
SELECT COUNT(*) as tabla_existe 
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'nomina_timbrado_log';

-- Verificar tabla configuraciones_sistema
SELECT 'Verificando tabla configuraciones_sistema...' as paso;
SELECT COUNT(*) as configuraciones_cfdi 
FROM configuraciones_sistema 
WHERE categoria = 'CFDI';

-- Verificar empleados elegibles como gerentes
SELECT 'Verificando empleados elegibles como gerentes...' as paso;
SELECT COUNT(*) as empleados_pueden_ser_gerentes 
FROM empleados 
WHERE puede_ser_gerente = 1 AND estatus = 'Activo';

-- Verificar vistas creadas
SELECT 'Verificando vistas creadas...' as paso;
SELECT TABLE_NAME 
FROM information_schema.VIEWS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME IN ('vista_empleados_busqueda', 'vista_empleados_gerentes');

SELECT '=====================================' as separador;
SELECT 'RESUMEN DE LA MIGRACIÓN' as resumen;
SELECT '=====================================' as separador;
SELECT 'Nuevas columnas agregadas a nomina_detalle: 13' as detalle;
SELECT 'Nuevas tablas creadas: 3 (nomina_timbrado_log, nomina_importaciones, configuraciones_sistema)' as detalle;
SELECT 'Vistas creadas/actualizadas: 2 (vista_empleados_busqueda, vista_empleados_gerentes)' as detalle;
SELECT 'Triggers creados: 2 (before_nomina_detalle_insert, before_nomina_detalle_update)' as detalle;
SELECT 'Funciones creadas: 1 (calcular_pago_horas_extras)' as detalle;
SELECT 'Procedimientos creados: 1 (obtener_empleados_gerentes)' as detalle;
SELECT '=====================================' as separador;

-- FIN DE LA MIGRACIÓN
