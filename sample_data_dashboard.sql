-- ============================================================
-- DATOS DE EJEMPLO PARA PRUEBAS DE DASHBOARD
-- Sistema de Recursos Humanos Sinforosa
-- ============================================================

-- Insertar departamentos de ejemplo
INSERT INTO departamentos (nombre, descripcion, estatus) VALUES
('Administración', 'Departamento administrativo', 'Activo'),
('Ventas', 'Departamento de ventas', 'Activo'),
('Operaciones', 'Departamento de operaciones', 'Activo'),
('Cocina', 'Departamento de cocina', 'Activo'),
('Recursos Humanos', 'Departamento de recursos humanos', 'Activo'),
('Sistemas', 'Departamento de sistemas', 'Activo')
ON DUPLICATE KEY UPDATE nombre=VALUES(nombre);

-- Insertar puestos de ejemplo
INSERT INTO puestos (nombre, departamento_id, nivel, descripcion, estatus) VALUES
('Gerente General', 1, 'Directivo', 'Gerente general de la empresa', 'Activo'),
('Contador', 1, 'Ejecutivo', 'Contador general', 'Activo'),
('Vendedor', 2, 'Operativo', 'Vendedor de tienda', 'Activo'),
('Supervisor de Ventas', 2, 'Ejecutivo', 'Supervisor de ventas', 'Activo'),
('Operador', 3, 'Operativo', 'Operador de producción', 'Activo'),
('Chef', 4, 'Ejecutivo', 'Chef principal', 'Activo'),
('Cocinero', 4, 'Operativo', 'Cocinero', 'Activo'),
('Reclutador', 5, 'Ejecutivo', 'Reclutador de personal', 'Activo'),
('Desarrollador', 6, 'Ejecutivo', 'Desarrollador de software', 'Activo')
ON DUPLICATE KEY UPDATE nombre=VALUES(nombre);

-- Insertar empleados de ejemplo con variedad de datos
INSERT INTO empleados (
    numero_empleado, nombres, apellido_paterno, apellido_materno,
    curp, rfc, nss, fecha_nacimiento, genero, estado_civil,
    email_personal, telefono, celular,
    calle, numero_exterior, colonia, codigo_postal, municipio, estado,
    fecha_ingreso, departamento_id, puesto_id,
    salario_mensual, salario_diario, tipo_contrato, estatus
) VALUES
-- Empleados contratados hace 6-12 meses
('EMP001', 'Juan Carlos', 'García', 'López', 'GALJ850101HQTRPN01', 'GALJ850101', '12345678901', '1985-01-01', 'M', 'Casado', 'juan.garcia@email.com', '4421234567', '4421234567', 'Av. 5 de Febrero', '123', 'Centro', '76000', 'Querétaro', 'Querétaro', DATE_SUB(NOW(), INTERVAL 10 MONTH), 1, 1, 25000.00, 833.33, 'Planta', 'Activo'),
('EMP002', 'María Elena', 'Martínez', 'Hernández', 'MAHM900215MQRRRL02', 'MAHM900215', '23456789012', '1990-02-15', 'F', 'Soltera', 'maria.martinez@email.com', '4421234568', '4421234568', 'Calle Juárez', '456', 'Centro', '76000', 'Querétaro', 'Querétaro', DATE_SUB(NOW(), INTERVAL 8 MONTH), 1, 2, 18000.00, 600.00, 'Planta', 'Activo'),

-- Empleados contratados hace 3-6 meses
('EMP003', 'Pedro Antonio', 'Rodríguez', 'Sánchez', 'ROSP880320HQTRDN03', 'ROSP880320', '34567890123', '1988-03-20', 'M', 'Casado', 'pedro.rodriguez@email.com', '4421234569', '4421234569', 'Blvd. Bernardo Quintana', '789', 'Centro Sur', '76090', 'Querétaro', 'Querétaro', DATE_SUB(NOW(), INTERVAL 5 MONTH), 2, 4, 20000.00, 666.67, 'Planta', 'Activo'),
('EMP004', 'Ana Luisa', 'Fernández', 'Torres', 'FETA920410MQRRNN04', 'FETA920410', '45678901234', '1992-04-10', 'F', 'Casada', 'ana.fernandez@email.com', '4421234570', '4421234570', 'Av. Universidad', '321', 'Centro', '76000', 'Querétaro', 'Querétaro', DATE_SUB(NOW(), INTERVAL 4 MONTH), 2, 3, 12000.00, 400.00, 'Planta', 'Activo'),
('EMP005', 'Luis Miguel', 'González', 'Ramírez', 'GORL870525HQTNMS05', 'GORL870525', '56789012345', '1987-05-25', 'M', 'Divorciado', 'luis.gonzalez@email.com', '4421234571', '4421234571', 'Calle Madero', '654', 'Centro', '76000', 'Querétaro', 'Querétaro', DATE_SUB(NOW(), INTERVAL 4 MONTH), 2, 3, 13000.00, 433.33, 'Planta', 'Activo'),

-- Empleados contratados hace 1-3 meses
('EMP006', 'Carmen Rosa', 'Díaz', 'Morales', 'DIMC910615MQRZRR06', 'DIMC910615', '67890123456', '1991-06-15', 'F', 'Soltera', 'carmen.diaz@email.com', '4421234572', '4421234572', 'Av. Constituyentes', '987', 'Centro', '76000', 'Querétaro', 'Querétaro', DATE_SUB(NOW(), INTERVAL 2 MONTH), 3, 5, 11000.00, 366.67, 'Planta', 'Activo'),
('EMP007', 'Roberto Carlos', 'Pérez', 'Jiménez', 'PEJR890705HQTRBN07', 'PEJR890705', '78901234567', '1989-07-05', 'M', 'Soltero', 'roberto.perez@email.com', '4421234573', '4421234573', 'Calle Allende', '147', 'Centro', '76000', 'Querétaro', 'Querétaro', DATE_SUB(NOW(), INTERVAL 2 MONTH), 3, 5, 10500.00, 350.00, 'Temporal', 'Activo'),
('EMP008', 'Patricia Isabel', 'López', 'Cruz', 'LOCP930820MQTPRT08', 'LOCP930820', '89012345678', '1993-08-20', 'F', 'Casada', 'patricia.lopez@email.com', '4421234574', '4421234574', 'Av. Zaragoza', '258', 'Centro', '76000', 'Querétaro', 'Querétaro', DATE_SUB(NOW(), INTERVAL 1 MONTH), 4, 6, 22000.00, 733.33, 'Planta', 'Activo'),

-- Empleados contratados este mes
('EMP009', 'Jorge Alberto', 'Ramírez', 'Vargas', 'RAVJ860905HQTMRG09', 'RAVJ860905', '90123456789', '1986-09-05', 'M', 'Casado', 'jorge.ramirez@email.com', '4421234575', '4421234575', 'Calle Hidalgo', '369', 'Centro', '76000', 'Querétaro', 'Querétaro', DATE_SUB(NOW(), INTERVAL 15 DAY), 4, 7, 14000.00, 466.67, 'Planta', 'Activo'),
('EMP010', 'Silvia Marcela', 'Torres', 'Medina', 'TOMS940930MQTRLD10', 'TOMS940930', '01234567890', '1994-09-30', 'F', 'Soltera', 'silvia.torres@email.com', '4421234576', '4421234576', 'Av. Corregidora', '741', 'Centro', '76000', 'Querétaro', 'Querétaro', DATE_SUB(NOW(), INTERVAL 10 DAY), 4, 7, 13500.00, 450.00, 'Planta', 'Activo'),
('EMP011', 'Miguel Ángel', 'Sánchez', 'Ruiz', 'SARM881015HQTNGL11', 'SARM881015', '12345098765', '1988-10-15', 'M', 'Unión Libre', 'miguel.sanchez@email.com', '4421234577', '4421234577', 'Calle Morelos', '852', 'Centro', '76000', 'Querétaro', 'Querétaro', DATE_SUB(NOW(), INTERVAL 5 DAY), 5, 8, 16000.00, 533.33, 'Planta', 'Activo'),
('EMP012', 'Laura Beatriz', 'Gutiérrez', 'Flores', 'GUFL921120MQTTRR12', 'GUFL921120', '23450987654', '1992-11-20', 'F', 'Casada', 'laura.gutierrez@email.com', '4421234578', '4421234578', 'Av. Tecnológico', '963', 'Centro Sur', '76090', 'Querétaro', 'Querétaro', DATE_SUB(NOW(), INTERVAL 2 DAY), 6, 9, 19000.00, 633.33, 'Planta', 'Activo'),

-- Más empleados para distribución
('EMP013', 'Fernando José', 'Morales', 'Castro', 'MOCF870225HQTRRS13', 'MOCF870225', '34560987654', '1987-02-25', 'M', 'Soltero', 'fernando.morales@email.com', '4421234579', '4421234579', 'Calle Pino Suárez', '159', 'Centro', '76000', 'Querétaro', 'Querétaro', DATE_SUB(NOW(), INTERVAL 7 MONTH), 2, 3, 12500.00, 416.67, 'Planta', 'Activo'),
('EMP014', 'Gabriela Monserrat', 'Reyes', 'Ortiz', 'REOG950330MQTYRB14', 'REOG950330', '45670987654', '1995-03-30', 'F', 'Soltera', 'gabriela.reyes@email.com', '4421234580', '4421234580', 'Av. Antea', '357', 'Jurica', '76100', 'Querétaro', 'Querétaro', DATE_SUB(NOW(), INTERVAL 3 MONTH), 3, 5, 10800.00, 360.00, 'Temporal', 'Activo')
ON DUPLICATE KEY UPDATE nombres=VALUES(nombres);

-- Insertar períodos de nómina
INSERT INTO periodos_nomina (tipo, fecha_inicio, fecha_fin, fecha_pago, estatus, total_percepciones, total_deducciones, total_neto, fecha_proceso) VALUES
-- Periodo cerrado (más antiguo)
('Quincenal', DATE_SUB(NOW(), INTERVAL 90 DAY), DATE_SUB(NOW(), INTERVAL 76 DAY), DATE_SUB(NOW(), INTERVAL 74 DAY), 'Cerrado', 150000.00, 35000.00, 115000.00, DATE_SUB(NOW(), INTERVAL 75 DAY)),

-- Periodos procesados/pagados después del último corte
('Quincenal', DATE_SUB(NOW(), INTERVAL 75 DAY), DATE_SUB(NOW(), INTERVAL 61 DAY), DATE_SUB(NOW(), INTERVAL 59 DAY), 'Pagado', 160000.00, 38000.00, 122000.00, DATE_SUB(NOW(), INTERVAL 60 DAY)),
('Quincenal', DATE_SUB(NOW(), INTERVAL 60 DAY), DATE_SUB(NOW(), INTERVAL 46 DAY), DATE_SUB(NOW(), INTERVAL 44 DAY), 'Pagado', 165000.00, 39000.00, 126000.00, DATE_SUB(NOW(), INTERVAL 45 DAY)),
('Quincenal', DATE_SUB(NOW(), INTERVAL 45 DAY), DATE_SUB(NOW(), INTERVAL 31 DAY), DATE_SUB(NOW(), INTERVAL 29 DAY), 'Procesado', 170000.00, 40000.00, 130000.00, DATE_SUB(NOW(), INTERVAL 30 DAY)),
('Quincenal', DATE_SUB(NOW(), INTERVAL 30 DAY), DATE_SUB(NOW(), INTERVAL 16 DAY), DATE_SUB(NOW(), INTERVAL 14 DAY), 'Procesado', 175000.00, 42000.00, 133000.00, DATE_SUB(NOW(), INTERVAL 15 DAY)),
('Quincenal', DATE_SUB(NOW(), INTERVAL 15 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY), NOW(), 'En Proceso', 0.00, 0.00, 0.00, NULL)
ON DUPLICATE KEY UPDATE tipo=VALUES(tipo);

-- Insertar asistencias del último mes con variedad de estatus
-- Generar asistencias para los últimos 30 días para varios empleados
INSERT INTO asistencias (empleado_id, fecha, hora_entrada, hora_salida, horas_trabajadas, estatus) VALUES
-- Asistencias de EMP001
(1, DATE_SUB(NOW(), INTERVAL 29 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 29 DAY), ' 08:00:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 29 DAY), ' 17:00:00'), 9.00, 'Presente'),
(1, DATE_SUB(NOW(), INTERVAL 28 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 28 DAY), ' 08:15:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 28 DAY), ' 17:00:00'), 8.75, 'Retardo'),
(1, DATE_SUB(NOW(), INTERVAL 27 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 27 DAY), ' 08:00:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 27 DAY), ' 17:00:00'), 9.00, 'Presente'),
(1, DATE_SUB(NOW(), INTERVAL 26 DAY), NULL, NULL, 0, 'Falta'),
(1, DATE_SUB(NOW(), INTERVAL 25 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 25 DAY), ' 08:00:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 25 DAY), ' 17:00:00'), 9.00, 'Presente'),
(1, DATE_SUB(NOW(), INTERVAL 24 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 24 DAY), ' 08:00:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 24 DAY), ' 17:00:00'), 9.00, 'Presente'),
(1, DATE_SUB(NOW(), INTERVAL 23 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 23 DAY), ' 08:00:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 23 DAY), ' 17:00:00'), 9.00, 'Presente'),
(1, DATE_SUB(NOW(), INTERVAL 22 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 22 DAY), ' 08:00:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 22 DAY), ' 13:00:00'), 5.00, 'Permiso'),
(1, DATE_SUB(NOW(), INTERVAL 21 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 21 DAY), ' 08:00:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 21 DAY), ' 17:00:00'), 9.00, 'Presente'),
(1, DATE_SUB(NOW(), INTERVAL 20 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 20 DAY), ' 08:00:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 20 DAY), ' 17:00:00'), 9.00, 'Presente'),

-- Asistencias de EMP002
(2, DATE_SUB(NOW(), INTERVAL 29 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 29 DAY), ' 08:00:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 29 DAY), ' 17:00:00'), 9.00, 'Presente'),
(2, DATE_SUB(NOW(), INTERVAL 28 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 28 DAY), ' 08:00:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 28 DAY), ' 17:00:00'), 9.00, 'Presente'),
(2, DATE_SUB(NOW(), INTERVAL 27 DAY), NULL, NULL, 0, 'Vacaciones'),
(2, DATE_SUB(NOW(), INTERVAL 26 DAY), NULL, NULL, 0, 'Vacaciones'),
(2, DATE_SUB(NOW(), INTERVAL 25 DAY), NULL, NULL, 0, 'Vacaciones'),
(2, DATE_SUB(NOW(), INTERVAL 24 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 24 DAY), ' 08:00:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 24 DAY), ' 17:00:00'), 9.00, 'Presente'),
(2, DATE_SUB(NOW(), INTERVAL 23 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 23 DAY), ' 08:10:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 23 DAY), ' 17:00:00'), 8.83, 'Retardo'),
(2, DATE_SUB(NOW(), INTERVAL 22 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 22 DAY), ' 08:00:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 22 DAY), ' 17:00:00'), 9.00, 'Presente'),
(2, DATE_SUB(NOW(), INTERVAL 21 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 21 DAY), ' 08:00:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 21 DAY), ' 17:00:00'), 9.00, 'Presente'),
(2, DATE_SUB(NOW(), INTERVAL 20 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 20 DAY), ' 08:00:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 20 DAY), ' 17:00:00'), 9.00, 'Presente'),

-- Asistencias de EMP003
(3, DATE_SUB(NOW(), INTERVAL 29 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 29 DAY), ' 08:00:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 29 DAY), ' 17:00:00'), 9.00, 'Presente'),
(3, DATE_SUB(NOW(), INTERVAL 28 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 28 DAY), ' 08:00:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 28 DAY), ' 17:00:00'), 9.00, 'Presente'),
(3, DATE_SUB(NOW(), INTERVAL 27 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 27 DAY), ' 08:00:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 27 DAY), ' 17:00:00'), 9.00, 'Presente'),
(3, DATE_SUB(NOW(), INTERVAL 26 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 26 DAY), ' 08:20:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 26 DAY), ' 17:00:00'), 8.67, 'Retardo'),
(3, DATE_SUB(NOW(), INTERVAL 25 DAY), NULL, NULL, 0, 'Incapacidad'),
(3, DATE_SUB(NOW(), INTERVAL 24 DAY), NULL, NULL, 0, 'Incapacidad'),
(3, DATE_SUB(NOW(), INTERVAL 23 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 23 DAY), ' 08:00:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 23 DAY), ' 17:00:00'), 9.00, 'Presente'),
(3, DATE_SUB(NOW(), INTERVAL 22 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 22 DAY), ' 08:00:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 22 DAY), ' 17:00:00'), 9.00, 'Presente'),
(3, DATE_SUB(NOW(), INTERVAL 21 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 21 DAY), ' 08:00:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 21 DAY), ' 17:00:00'), 9.00, 'Presente'),
(3, DATE_SUB(NOW(), INTERVAL 20 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 20 DAY), ' 08:00:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 20 DAY), ' 17:00:00'), 9.00, 'Presente'),

-- Asistencias de EMP004 (muchas presentes)
(4, DATE_SUB(NOW(), INTERVAL 29 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 29 DAY), ' 08:00:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 29 DAY), ' 17:00:00'), 9.00, 'Presente'),
(4, DATE_SUB(NOW(), INTERVAL 28 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 28 DAY), ' 08:00:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 28 DAY), ' 17:00:00'), 9.00, 'Presente'),
(4, DATE_SUB(NOW(), INTERVAL 27 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 27 DAY), ' 08:00:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 27 DAY), ' 17:00:00'), 9.00, 'Presente'),
(4, DATE_SUB(NOW(), INTERVAL 26 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 26 DAY), ' 08:00:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 26 DAY), ' 17:00:00'), 9.00, 'Presente'),
(4, DATE_SUB(NOW(), INTERVAL 25 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 25 DAY), ' 08:00:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 25 DAY), ' 17:00:00'), 9.00, 'Presente'),
(4, DATE_SUB(NOW(), INTERVAL 24 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 24 DAY), ' 08:00:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 24 DAY), ' 17:00:00'), 9.00, 'Presente'),
(4, DATE_SUB(NOW(), INTERVAL 23 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 23 DAY), ' 08:00:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 23 DAY), ' 17:00:00'), 9.00, 'Presente'),
(4, DATE_SUB(NOW(), INTERVAL 22 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 22 DAY), ' 08:00:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 22 DAY), ' 17:00:00'), 9.00, 'Presente'),
(4, DATE_SUB(NOW(), INTERVAL 21 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 21 DAY), ' 08:00:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 21 DAY), ' 17:00:00'), 9.00, 'Presente'),
(4, DATE_SUB(NOW(), INTERVAL 20 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 20 DAY), ' 08:00:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 20 DAY), ' 17:00:00'), 9.00, 'Presente'),

-- Más asistencias para otros empleados (abreviado)
(5, DATE_SUB(NOW(), INTERVAL 29 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 29 DAY), ' 08:00:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 29 DAY), ' 17:00:00'), 9.00, 'Presente'),
(5, DATE_SUB(NOW(), INTERVAL 28 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 28 DAY), ' 08:00:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 28 DAY), ' 17:00:00'), 9.00, 'Presente'),
(5, DATE_SUB(NOW(), INTERVAL 27 DAY), NULL, NULL, 0, 'Falta'),
(5, DATE_SUB(NOW(), INTERVAL 26 DAY), NULL, NULL, 0, 'Falta'),
(6, DATE_SUB(NOW(), INTERVAL 25 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 25 DAY), ' 08:00:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 25 DAY), ' 17:00:00'), 9.00, 'Presente'),
(6, DATE_SUB(NOW(), INTERVAL 24 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 24 DAY), ' 08:00:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 24 DAY), ' 17:00:00'), 9.00, 'Presente'),
(7, DATE_SUB(NOW(), INTERVAL 23 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 23 DAY), ' 08:00:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 23 DAY), ' 17:00:00'), 9.00, 'Presente'),
(7, DATE_SUB(NOW(), INTERVAL 22 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 22 DAY), ' 08:30:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 22 DAY), ' 17:00:00'), 8.50, 'Retardo'),
(8, DATE_SUB(NOW(), INTERVAL 21 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 21 DAY), ' 08:00:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 21 DAY), ' 17:00:00'), 9.00, 'Presente'),
(8, DATE_SUB(NOW(), INTERVAL 20 DAY), CONCAT(DATE_SUB(NOW(), INTERVAL 20 DAY), ' 08:00:00'), CONCAT(DATE_SUB(NOW(), INTERVAL 20 DAY), ' 17:00:00'), 9.00, 'Presente')
ON DUPLICATE KEY UPDATE estatus=VALUES(estatus);

-- Insertar solicitudes de vacaciones pendientes
INSERT INTO solicitudes_vacaciones (empleado_id, fecha_inicio, fecha_fin, dias_solicitados, motivo, estatus) VALUES
(1, DATE_ADD(NOW(), INTERVAL 10 DAY), DATE_ADD(NOW(), INTERVAL 15 DAY), 5, 'Vacaciones familiares', 'Pendiente'),
(3, DATE_ADD(NOW(), INTERVAL 20 DAY), DATE_ADD(NOW(), INTERVAL 25 DAY), 5, 'Descanso personal', 'Pendiente')
ON DUPLICATE KEY UPDATE estatus=VALUES(estatus);

-- Insertar candidatos en proceso
INSERT INTO candidatos (
    nombre, apellido_paterno, apellido_materno, email, telefono,
    fecha_nacimiento, genero, cv_path, estatus, fecha_registro
) VALUES
('Alberto', 'Vargas', 'Silva', 'alberto.vargas@email.com', '4421234590', '1990-05-15', 'M', '/uploads/cv_001.pdf', 'En Revisión', NOW()),
('Diana', 'Castillo', 'Mendoza', 'diana.castillo@email.com', '4421234591', '1993-08-22', 'F', '/uploads/cv_002.pdf', 'Entrevista', DATE_SUB(NOW(), INTERVAL 2 DAY)),
('Emilio', 'Rojas', 'Guerrero', 'emilio.rojas@email.com', '4421234592', '1988-12-10', 'M', '/uploads/cv_003.pdf', 'Evaluación', DATE_SUB(NOW(), INTERVAL 5 DAY))
ON DUPLICATE KEY UPDATE estatus=VALUES(estatus);

-- Insertar conceptos de nómina si no existen
INSERT INTO conceptos_nomina (clave, nombre, tipo, descripcion, estatus) VALUES
('P001', 'Sueldo Base', 'Percepción', 'Sueldo base del empleado', 'Activo'),
('P003', 'Bonos', 'Percepción', 'Bonos y premios', 'Activo'),
('P004', 'Horas Extra', 'Percepción', 'Pago por horas extra', 'Activo'),
('D001', 'IMSS', 'Deducción', 'Cuota IMSS obrera', 'Activo'),
('D002', 'ISR', 'Deducción', 'Impuesto Sobre la Renta', 'Activo'),
('D003', 'Préstamos', 'Deducción', 'Descuentos por préstamos', 'Activo'),
('D004', 'Otros Descuentos', 'Deducción', 'Otros descuentos varios', 'Activo')
ON DUPLICATE KEY UPDATE nombre=VALUES(nombre);

-- Mensaje final
SELECT 'Datos de ejemplo insertados correctamente para el Dashboard' as Mensaje;
