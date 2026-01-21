-- ============================================================================
-- SCRIPT DE DATOS DE EJEMPLO - HORAS EXTRAS
-- Sistema RRHH Sinforosa
-- ============================================================================
-- Este script agrega datos de ejemplo para horas extras de empleados
-- Asegura que haya suficiente información para visualizar en el dashboard
-- ============================================================================

-- Insertar horas extras para diferentes empleados en el periodo actual
-- Las horas extras se registran con estatus 'Aprobado' y 'Procesado'

-- Primero, obtener el periodo activo (si existe)
SET @periodo_actual = (SELECT id FROM periodos_nomina WHERE estatus = 'Activo' ORDER BY fecha_inicio DESC LIMIT 1);

-- Si no hay periodo activo, crear uno
INSERT INTO periodos_nomina (tipo, fecha_inicio, fecha_fin, fecha_pago, estatus)
SELECT 'Quincenal', 
       DATE_FORMAT(CURDATE(), '%Y-%m-01'),
       DATE_FORMAT(LAST_DAY(CURDATE()), '%Y-%m-%d'),
       DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 3 DAY),
       'Activo'
WHERE NOT EXISTS (SELECT 1 FROM periodos_nomina WHERE estatus = 'Activo');

SET @periodo_actual = (SELECT id FROM periodos_nomina WHERE estatus = 'Activo' ORDER BY fecha_inicio DESC LIMIT 1);

-- Insertar horas extras para empleados activos
-- Empleado 1: Juan Pérez - 8 horas extras
INSERT INTO incidencias_nomina (empleado_id, periodo_id, tipo_incidencia, fecha_incidencia, cantidad, monto, descripcion, estatus, usuario_registro_id)
SELECT 
    e.id,
    @periodo_actual,
    'Hora Extra',
    DATE_SUB(CURDATE(), INTERVAL 5 DAY),
    4.00,
    ROUND(e.salario_mensual / 30 / 8 * 1.5 * 4, 2),
    'Proyecto urgente - Sistema de nómina',
    'Aprobado',
    1
FROM empleados e 
WHERE e.estatus = 'Activo' 
LIMIT 1;

INSERT INTO incidencias_nomina (empleado_id, periodo_id, tipo_incidencia, fecha_incidencia, cantidad, monto, descripcion, estatus, usuario_registro_id)
SELECT 
    e.id,
    @periodo_actual,
    'Hora Extra',
    DATE_SUB(CURDATE(), INTERVAL 2 DAY),
    4.00,
    ROUND(e.salario_mensual / 30 / 8 * 1.5 * 4, 2),
    'Cierre de mes contable',
    'Aprobado',
    1
FROM empleados e 
WHERE e.estatus = 'Activo' 
LIMIT 1;

-- Empleado 2: María González - 6 horas extras
INSERT INTO incidencias_nomina (empleado_id, periodo_id, tipo_incidencia, fecha_incidencia, cantidad, monto, descripcion, estatus, usuario_registro_id)
SELECT 
    e.id,
    @periodo_actual,
    'Hora Extra',
    DATE_SUB(CURDATE(), INTERVAL 7 DAY),
    3.00,
    ROUND(e.salario_mensual / 30 / 8 * 1.5 * 3, 2),
    'Capacitación fuera de horario',
    'Procesado',
    1
FROM empleados e 
WHERE e.estatus = 'Activo' 
LIMIT 1 OFFSET 1;

INSERT INTO incidencias_nomina (empleado_id, periodo_id, tipo_incidencia, fecha_incidencia, cantidad, monto, descripcion, estatus, usuario_registro_id)
SELECT 
    e.id,
    @periodo_actual,
    'Hora Extra',
    DATE_SUB(CURDATE(), INTERVAL 3 DAY),
    3.00,
    ROUND(e.salario_mensual / 30 / 8 * 1.5 * 3, 2),
    'Reunión de planificación estratégica',
    'Procesado',
    1
FROM empleados e 
WHERE e.estatus = 'Activo' 
LIMIT 1 OFFSET 1;

-- Empleado 3: Carlos Rodríguez - 10 horas extras
INSERT INTO incidencias_nomina (empleado_id, periodo_id, tipo_incidencia, fecha_incidencia, cantidad, monto, descripcion, estatus, usuario_registro_id)
SELECT 
    e.id,
    @periodo_actual,
    'Hora Extra',
    DATE_SUB(CURDATE(), INTERVAL 8 DAY),
    5.00,
    ROUND(e.salario_mensual / 30 / 8 * 1.5 * 5, 2),
    'Soporte técnico urgente - cliente VIP',
    'Procesado',
    1
FROM empleados e 
WHERE e.estatus = 'Activo' 
LIMIT 1 OFFSET 2;

INSERT INTO incidencias_nomina (empleado_id, periodo_id, tipo_incidencia, fecha_incidencia, cantidad, monto, descripcion, estatus, usuario_registro_id)
SELECT 
    e.id,
    @periodo_actual,
    'Hora Extra',
    DATE_SUB(CURDATE(), INTERVAL 1 DAY),
    5.00,
    ROUND(e.salario_mensual / 30 / 8 * 1.5 * 5, 2),
    'Mantenimiento de servidores',
    'Aprobado',
    1
FROM empleados e 
WHERE e. estatus = 'Activo' 
LIMIT 1 OFFSET 2;

-- Empleado 4: Ana Martínez - 5 horas extras
INSERT INTO incidencias_nomina (empleado_id, periodo_id, tipo_incidencia, fecha_incidencia, cantidad, monto, descripcion, estatus, usuario_registro_id)
SELECT 
    e.id,
    @periodo_actual,
    'Hora Extra',
    DATE_SUB(CURDATE(), INTERVAL 6 DAY),
    2.50,
    ROUND(e. salario_mensual / 30 / 8 * 1.5 * 2.5, 2),
    'Inventario mensual',
    'Procesado',
    1
FROM empleados e 
WHERE e.estatus = 'Activo' 
LIMIT 1 OFFSET 3;

INSERT INTO incidencias_nomina (empleado_id, periodo_id, tipo_incidencia, fecha_incidencia, cantidad, monto, descripcion, estatus, usuario_registro_id)
SELECT 
    e.id,
    @periodo_actual,
    'Hora Extra',
    DATE_SUB(CURDATE(), INTERVAL 4 DAY),
    2.50,
    ROUND(e.salario_mensual / 30 / 8 * 1.5 * 2.5, 2),
    'Auditoría interna',
    'Aprobado',
    1
FROM empleados e 
WHERE e.estatus = 'Activo' 
LIMIT 1 OFFSET 3;

-- Empleado 5: Luis Hernández - 12 horas extras
INSERT INTO incidencias_nomina (empleado_id, periodo_id, tipo_incidencia, fecha_incidencia, cantidad, monto, descripcion, estatus, usuario_registro_id)
SELECT 
    e.id,
    @periodo_actual,
    'Hora Extra',
    DATE_SUB(CURDATE(), INTERVAL 10 DAY),
    6.00,
    ROUND(e.salario_mensual / 30 / 8 * 1.5 * 6, 2),
    'Instalación de nuevo equipo',
    'Procesado',
    1
FROM empleados e 
WHERE e.estatus = 'Activo' 
LIMIT 1 OFFSET 4;

INSERT INTO incidencias_nomina (empleado_id, periodo_id, tipo_incidencia, fecha_incidencia, cantidad, monto, descripcion, estatus, usuario_registro_id)
SELECT 
    e.id,
    @periodo_actual,
    'Hora Extra',
    DATE_SUB(CURDATE(), INTERVAL 2 DAY),
    6.00,
    ROUND(e.salario_mensual / 30 / 8 * 1.5 * 6, 2),
    'Capacitación de personal nuevo',
    'Aprobado',
    1
FROM empleados e 
WHERE e.estatus = 'Activo' 
LIMIT 1 OFFSET 4;

-- Empleado 6: Patricia López - 7 horas extras
INSERT INTO incidencias_nomina (empleado_id, periodo_id, tipo_incidencia, fecha_incidencia, cantidad, monto, descripcion, estatus, usuario_registro_id)
SELECT 
    e.id,
    @periodo_actual,
    'Hora Extra',
    DATE_SUB(CURDATE(), INTERVAL 9 DAY),
    3.50,
    ROUND(e. salario_mensual / 30 / 8 * 1.5 * 3.5, 2),
    'Preparación de reportes ejecutivos',
    'Procesado',
    1
FROM empleados e 
WHERE e.estatus = 'Activo' 
LIMIT 1 OFFSET 5;

INSERT INTO incidencias_nomina (empleado_id, periodo_id, tipo_incidencia, fecha_incidencia, cantidad, monto, descripcion, estatus, usuario_registro_id)
SELECT 
    e.id,
    @periodo_actual,
    'Hora Extra',
    DATE_SUB(CURDATE(), INTERVAL 3 DAY),
    3.50,
    ROUND(e.salario_mensual / 30 / 8 * 1.5 * 3.5, 2),
    'Análisis financiero trimestral',
    'Aprobado',
    1
FROM empleados e 
WHERE e. estatus = 'Activo' 
LIMIT 1 OFFSET 5;

-- Empleado 7: Roberto Sánchez - 9 horas extras
INSERT INTO incidencias_nomina (empleado_id, periodo_id, tipo_incidencia, fecha_incidencia, cantidad, monto, descripcion, estatus, usuario_registro_id)
SELECT 
    e.id,
    @periodo_actual,
    'Hora Extra',
    DATE_SUB(CURDATE(), INTERVAL 11 DAY),
    4.50,
    ROUND(e. salario_mensual / 30 / 8 * 1.5 * 4.5, 2),
    'Entrega urgente a cliente',
    'Procesado',
    1
FROM empleados e 
WHERE e.estatus = 'Activo' 
LIMIT 1 OFFSET 6;

INSERT INTO incidencias_nomina (empleado_id, periodo_id, tipo_incidencia, fecha_incidencia, cantidad, monto, descripcion, estatus, usuario_registro_id)
SELECT 
    e.id,
    @periodo_actual,
    'Hora Extra',
    DATE_SUB(CURDATE(), INTERVAL 1 DAY),
    4.50,
    ROUND(e.salario_mensual / 30 / 8 * 1.5 * 4.5, 2),
    'Revisión de documentación',
    'Aprobado',
    1
FROM empleados e 
WHERE e.estatus = 'Activo' 
LIMIT 1 OFFSET 6;

-- Empleado 8: Gabriela Torres - 8 horas extras
INSERT INTO incidencias_nomina (empleado_id, periodo_id, tipo_incidencia, fecha_incidencia, cantidad, monto, descripcion, estatus, usuario_registro_id)
SELECT 
    e.id,
    @periodo_actual,
    'Hora Extra',
    DATE_SUB(CURDATE(), INTERVAL 12 DAY),
    4.00,
    ROUND(e.salario_mensual / 30 / 8 * 1.5 * 4, 2),
    'Atención de evento corporativo',
    'Procesado',
    1
FROM empleados e 
WHERE e.estatus = 'Activo' 
LIMIT 1 OFFSET 7;

INSERT INTO incidencias_nomina (empleado_id, periodo_id, tipo_incidencia, fecha_incidencia, cantidad, monto, descripcion, estatus, usuario_registro_id)
SELECT 
    e.id,
    @periodo_actual,
    'Hora Extra',
    DATE_SUB(CURDATE(), INTERVAL 4 DAY),
    4.00,
    ROUND(e.salario_mensual / 30 / 8 * 1.5 * 4, 2),
    'Coordinación con proveedores',
    'Aprobado',
    1
FROM empleados e 
WHERE e. estatus = 'Activo' 
LIMIT 1 OFFSET 7;

-- Empleado 9: Fernando Ramírez - 11 horas extras
INSERT INTO incidencias_nomina (empleado_id, periodo_id, tipo_incidencia, fecha_incidencia, cantidad, monto, descripcion, estatus, usuario_registro_id)
SELECT 
    e.id,
    @periodo_actual,
    'Hora Extra',
    DATE_SUB(CURDATE(), INTERVAL 13 DAY),
    5.50,
    ROUND(e. salario_mensual / 30 / 8 * 1.5 * 5.5, 2),
    'Desarrollo de nuevo módulo',
    'Procesado',
    1
FROM empleados e 
WHERE e.estatus = 'Activo' 
LIMIT 1 OFFSET 8;

INSERT INTO incidencias_nomina (empleado_id, periodo_id, tipo_incidencia, fecha_incidencia, cantidad, monto, descripcion, estatus, usuario_registro_id)
SELECT 
    e.id,
    @periodo_actual,
    'Hora Extra',
    DATE_SUB(CURDATE(), INTERVAL 2 DAY),
    5.50,
    ROUND(e.salario_mensual / 30 / 8 * 1.5 * 5.5, 2),
    'Testing y corrección de bugs',
    'Aprobado',
    1
FROM empleados e 
WHERE e.estatus = 'Activo' 
LIMIT 1 OFFSET 8;

-- Empleado 10: Verónica Jiménez - 6.5 horas extras
INSERT INTO incidencias_nomina (empleado_id, periodo_id, tipo_incidencia, fecha_incidencia, cantidad, monto, descripcion, estatus, usuario_registro_id)
SELECT 
    e.id,
    @periodo_actual,
    'Hora Extra',
    DATE_SUB(CURDATE(), INTERVAL 14 DAY),
    3.25,
    ROUND(e. salario_mensual / 30 / 8 * 1.5 * 3.25, 2),
    'Revisión de contratos',
    'Procesado',
    1
FROM empleados e 
WHERE e.estatus = 'Activo' 
LIMIT 1 OFFSET 9;

INSERT INTO incidencias_nomina (empleado_id, periodo_id, tipo_incidencia, fecha_incidencia, cantidad, monto, descripcion, estatus, usuario_registro_id)
SELECT 
    e.id,
    @periodo_actual,
    'Hora Extra',
    DATE_SUB(CURDATE(), INTERVAL 5 DAY),
    3.25,
    ROUND(e.salario_mensual / 30 / 8 * 1.5 * 3.25, 2),
    'Asesoría legal',
    'Aprobado',
    1
FROM empleados e 
WHERE e.estatus = 'Activo' 
LIMIT 1 OFFSET 9;

-- Agregar más horas extras para empleados adicionales
INSERT INTO incidencias_nomina (empleado_id, periodo_id, tipo_incidencia, fecha_incidencia, cantidad, monto, descripcion, estatus, usuario_registro_id)
SELECT 
    e.id,
    @periodo_actual,
    'Hora Extra',
    DATE_SUB(CURDATE(), INTERVAL FLOOR(1 + RAND() * 14) DAY),
    ROUND(1 + RAND() * 6, 2),
    ROUND((e.salario_mensual / 30 / 8 * 1.5) * ROUND(1 + RAND() * 6, 2), 2),
    CASE 
        WHEN RAND() < 0.2 THEN 'Proyecto especial'
        WHEN RAND() < 0.4 THEN 'Soporte técnico'
        WHEN RAND() < 0.6 THEN 'Reunión extraordinaria'
        WHEN RAND() < 0.8 THEN 'Capacitación'
        ELSE 'Cierre de periodo'
    END,
    IF(RAND() < 0.7, 'Procesado', 'Aprobado'),
    1
FROM empleados e 
WHERE e.estatus = 'Activo'
AND e.id NOT IN (
    SELECT DISTINCT empleado_id 
    FROM incidencias_nomina 
    WHERE tipo_incidencia = 'Hora Extra' 
    AND periodo_id = @periodo_actual
)
LIMIT 15;

-- ============================================================================
-- VERIFICACIÓN DE DATOS INSERTADOS
-- ============================================================================

SELECT 
    COUNT(*) as total_registros,
    SUM(cantidad) as total_horas,
    SUM(monto) as costo_total
FROM incidencias_nomina 
WHERE tipo_incidencia = 'Hora Extra' 
AND periodo_id = @periodo_actual;

SELECT 
    e.numero_empleado,
    CONCAT(e.nombres, ' ', e.apellido_paterno) as nombre_completo,
    COUNT(i. id) as registros,
    SUM(i.cantidad) as horas_totales,
    SUM(i.monto) as costo_total
FROM incidencias_nomina i
INNER JOIN empleados e ON i. empleado_id = e.id
WHERE i.tipo_incidencia = 'Hora Extra' 
AND i.periodo_id = @periodo_actual
GROUP BY e.id
ORDER BY horas_totales DESC;
