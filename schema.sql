-- ============================================================
-- Sistema de GESTIÓN INTEGRAL DE TALENTO Y NÓMINA
-- Sinforosa Café - Base de Datos
-- Versión: 1.0.0
-- ============================================================

CREATE DATABASE IF NOT EXISTS rrhh_sinforosa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE rrhh_sinforosa;

-- ============================================================
-- TABLA: usuarios (Sistema de autenticación)
-- ============================================================
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'rrhh', 'gerente', 'empleado') DEFAULT 'empleado',
    activo TINYINT(1) DEFAULT 1,
    ultimo_acceso DATETIME,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: empleados (Gestión de Personal)
-- ============================================================
CREATE TABLE empleados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_empleado VARCHAR(20) UNIQUE NOT NULL,
    nombres VARCHAR(100) NOT NULL,
    apellido_paterno VARCHAR(100) NOT NULL,
    apellido_materno VARCHAR(100),
    curp VARCHAR(18) UNIQUE,
    rfc VARCHAR(13) UNIQUE,
    nss VARCHAR(11),
    fecha_nacimiento DATE,
    genero ENUM('M', 'F', 'Otro'),
    estado_civil ENUM('Soltero', 'Casado', 'Divorciado', 'Viudo', 'Unión Libre'),
    
    -- Contacto
    email_personal VARCHAR(100),
    telefono VARCHAR(15),
    celular VARCHAR(15),
    
    -- Dirección
    calle VARCHAR(150),
    numero_exterior VARCHAR(10),
    numero_interior VARCHAR(10),
    colonia VARCHAR(100),
    codigo_postal VARCHAR(5),
    municipio VARCHAR(100),
    estado VARCHAR(50) DEFAULT 'Querétaro',
    
    -- Información laboral
    fecha_ingreso DATE NOT NULL,
    fecha_baja DATE,
    motivo_baja TEXT,
    estatus ENUM('Activo', 'Baja', 'Suspendido', 'Vacaciones') DEFAULT 'Activo',
    tipo_contrato ENUM('Planta', 'Eventual', 'Honorarios', 'Practicante'),
    departamento VARCHAR(100),
    puesto VARCHAR(100),
    jefe_directo_id INT,
    salario_diario DECIMAL(10,2),
    salario_mensual DECIMAL(10,2),
    
    -- Datos bancarios
    banco VARCHAR(100),
    numero_cuenta VARCHAR(20),
    clabe_interbancaria VARCHAR(18),
    
    -- Sistema
    usuario_id INT,
    foto VARCHAR(255),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (jefe_directo_id) REFERENCES empleados(id) ON DELETE SET NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_numero_empleado (numero_empleado),
    INDEX idx_estatus (estatus),
    INDEX idx_departamento (departamento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: historial_laboral
-- ============================================================
CREATE TABLE historial_laboral (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empleado_id INT NOT NULL,
    tipo_evento ENUM('Contratación', 'Promoción', 'Cambio de Puesto', 'Cambio de Departamento', 'Aumento Salarial', 'Suspensión', 'Baja', 'Reingreso') NOT NULL,
    fecha_evento DATE NOT NULL,
    puesto_anterior VARCHAR(100),
    puesto_nuevo VARCHAR(100),
    departamento_anterior VARCHAR(100),
    departamento_nuevo VARCHAR(100),
    salario_anterior DECIMAL(10,2),
    salario_nuevo DECIMAL(10,2),
    motivo TEXT,
    notas TEXT,
    usuario_registro_id INT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_registro_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_empleado (empleado_id),
    INDEX idx_tipo_evento (tipo_evento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: documentos_empleados
-- ============================================================
CREATE TABLE documentos_empleados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empleado_id INT NOT NULL,
    tipo_documento VARCHAR(100) NOT NULL,
    nombre_archivo VARCHAR(255) NOT NULL,
    ruta_archivo VARCHAR(500) NOT NULL,
    descripcion TEXT,
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    usuario_subida_id INT,
    
    FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_subida_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_empleado (empleado_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: departamentos
-- ============================================================
CREATE TABLE departamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) UNIQUE NOT NULL,
    descripcion TEXT,
    responsable_id INT,
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (responsable_id) REFERENCES empleados(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: puestos
-- ============================================================
CREATE TABLE puestos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) UNIQUE NOT NULL,
    departamento_id INT,
    descripcion TEXT,
    salario_minimo DECIMAL(10,2),
    salario_maximo DECIMAL(10,2),
    nivel INT DEFAULT 1,
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (departamento_id) REFERENCES departamentos(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- MÓDULO: NÓMINA
-- ============================================================

-- Conceptos de nómina (percepciones y deducciones)
CREATE TABLE conceptos_nomina (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(20) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    tipo ENUM('Percepción', 'Deducción') NOT NULL,
    categoria ENUM('Fijo', 'Variable', 'Extraordinario'),
    formula TEXT,
    afecta_imss TINYINT(1) DEFAULT 0,
    afecta_isr TINYINT(1) DEFAULT 1,
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Periodos de nómina
CREATE TABLE periodos_nomina (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('Semanal', 'Quincenal', 'Mensual') NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    fecha_pago DATE NOT NULL,
    estatus ENUM('Abierto', 'En Proceso', 'Procesado', 'Pagado', 'Cerrado') DEFAULT 'Abierto',
    total_percepciones DECIMAL(12,2) DEFAULT 0,
    total_deducciones DECIMAL(12,2) DEFAULT 0,
    total_neto DECIMAL(12,2) DEFAULT 0,
    usuario_proceso_id INT,
    fecha_proceso DATETIME,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (usuario_proceso_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_periodo (fecha_inicio, fecha_fin),
    INDEX idx_estatus (estatus)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Nómina detalle
CREATE TABLE nomina_detalle (
    id INT AUTO_INCREMENT PRIMARY KEY,
    periodo_id INT NOT NULL,
    empleado_id INT NOT NULL,
    dias_trabajados DECIMAL(5,2) DEFAULT 0,
    salario_base DECIMAL(10,2),
    total_percepciones DECIMAL(10,2) DEFAULT 0,
    total_deducciones DECIMAL(10,2) DEFAULT 0,
    subtotal DECIMAL(10,2) DEFAULT 0,
    isr DECIMAL(10,2) DEFAULT 0,
    imss DECIMAL(10,2) DEFAULT 0,
    total_neto DECIMAL(10,2) DEFAULT 0,
    estatus ENUM('Pendiente', 'Calculado', 'Aprobado', 'Pagado') DEFAULT 'Pendiente',
    fecha_pago DATETIME,
    
    FOREIGN KEY (periodo_id) REFERENCES periodos_nomina(id) ON DELETE CASCADE,
    FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE CASCADE,
    UNIQUE KEY unique_periodo_empleado (periodo_id, empleado_id),
    INDEX idx_empleado (empleado_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Conceptos aplicados en nómina
CREATE TABLE nomina_conceptos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomina_detalle_id INT NOT NULL,
    concepto_id INT NOT NULL,
    cantidad DECIMAL(10,2) DEFAULT 1,
    monto DECIMAL(10,2) NOT NULL,
    
    FOREIGN KEY (nomina_detalle_id) REFERENCES nomina_detalle(id) ON DELETE CASCADE,
    FOREIGN KEY (concepto_id) REFERENCES conceptos_nomina(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Incidencias de nómina
CREATE TABLE incidencias_nomina (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empleado_id INT NOT NULL,
    periodo_id INT,
    tipo_incidencia ENUM('Falta', 'Retardo', 'Incapacidad', 'Permiso', 'Vacaciones', 'Hora Extra', 'Bono', 'Descuento', 'Otro') NOT NULL,
    fecha_incidencia DATE NOT NULL,
    cantidad DECIMAL(10,2) DEFAULT 1,
    monto DECIMAL(10,2) DEFAULT 0,
    descripcion TEXT,
    estatus ENUM('Pendiente', 'Aprobado', 'Rechazado', 'Procesado') DEFAULT 'Pendiente',
    usuario_registro_id INT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE CASCADE,
    FOREIGN KEY (periodo_id) REFERENCES periodos_nomina(id) ON DELETE SET NULL,
    FOREIGN KEY (usuario_registro_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_empleado_periodo (empleado_id, periodo_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- MÓDULO: ASISTENCIA Y TIEMPOS
-- ============================================================

-- Turnos
CREATE TABLE turnos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    hora_entrada TIME NOT NULL,
    hora_salida TIME NOT NULL,
    minutos_tolerancia INT DEFAULT 10,
    horas_laborales DECIMAL(4,2),
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Horarios de empleados
CREATE TABLE empleado_horarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empleado_id INT NOT NULL,
    turno_id INT NOT NULL,
    dia_semana ENUM('Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo') NOT NULL,
    fecha_inicio DATE,
    fecha_fin DATE,
    activo TINYINT(1) DEFAULT 1,
    
    FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE CASCADE,
    FOREIGN KEY (turno_id) REFERENCES turnos(id) ON DELETE CASCADE,
    INDEX idx_empleado (empleado_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Registro de asistencia
CREATE TABLE asistencias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empleado_id INT NOT NULL,
    fecha DATE NOT NULL,
    hora_entrada DATETIME,
    hora_salida DATETIME,
    horas_trabajadas DECIMAL(5,2),
    minutos_retardo INT DEFAULT 0,
    horas_extra DECIMAL(5,2) DEFAULT 0,
    estatus ENUM('Presente', 'Falta', 'Retardo', 'Permiso', 'Vacaciones', 'Incapacidad') DEFAULT 'Presente',
    notas TEXT,
    dispositivo_entrada VARCHAR(100),
    dispositivo_salida VARCHAR(100),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE CASCADE,
    UNIQUE KEY unique_empleado_fecha (empleado_id, fecha),
    INDEX idx_fecha (fecha),
    INDEX idx_estatus (estatus)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Vacaciones
CREATE TABLE vacaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empleado_id INT NOT NULL,
    anio INT NOT NULL,
    dias_correspondientes INT NOT NULL,
    dias_tomados INT DEFAULT 0,
    dias_disponibles INT NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE CASCADE,
    UNIQUE KEY unique_empleado_anio (empleado_id, anio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Solicitudes de vacaciones
CREATE TABLE solicitudes_vacaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empleado_id INT NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    dias_solicitados INT NOT NULL,
    motivo TEXT,
    estatus ENUM('Pendiente', 'Aprobada', 'Rechazada', 'Cancelada') DEFAULT 'Pendiente',
    fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    aprobador_id INT,
    fecha_respuesta DATETIME,
    comentarios_aprobador TEXT,
    
    FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE CASCADE,
    FOREIGN KEY (aprobador_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_empleado (empleado_id),
    INDEX idx_estatus (estatus)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- MÓDULO: RECLUTAMIENTO
-- ============================================================

-- Candidatos
CREATE TABLE candidatos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombres VARCHAR(100) NOT NULL,
    apellido_paterno VARCHAR(100) NOT NULL,
    apellido_materno VARCHAR(100),
    email VARCHAR(100),
    telefono VARCHAR(15),
    celular VARCHAR(15),
    fecha_nacimiento DATE,
    
    -- Dirección
    calle VARCHAR(150),
    colonia VARCHAR(100),
    municipio VARCHAR(100),
    estado VARCHAR(50),
    codigo_postal VARCHAR(5),
    
    -- Información profesional
    nivel_estudios ENUM('Primaria', 'Secundaria', 'Preparatoria', 'Técnico', 'Licenciatura', 'Maestría', 'Doctorado'),
    carrera VARCHAR(150),
    experiencia_anios INT,
    puesto_deseado VARCHAR(100),
    pretension_salarial DECIMAL(10,2),
    
    -- Documentos
    cv_ruta VARCHAR(500),
    foto_ruta VARCHAR(500),
    
    -- Proceso
    estatus ENUM('Nuevo', 'En Revisión', 'Entrevista', 'Evaluación', 'Seleccionado', 'Rechazado', 'Contratado') DEFAULT 'Nuevo',
    fuente_reclutamiento VARCHAR(100),
    fecha_aplicacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Sistema
    usuario_asignado_id INT,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (usuario_asignado_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_estatus (estatus)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Vacantes
CREATE TABLE vacantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    departamento_id INT,
    puesto_id INT,
    numero_vacantes INT DEFAULT 1,
    descripcion TEXT,
    requisitos TEXT,
    salario_minimo DECIMAL(10,2),
    salario_maximo DECIMAL(10,2),
    tipo_contrato ENUM('Planta', 'Eventual', 'Honorarios', 'Practicante'),
    estatus ENUM('Abierta', 'En Proceso', 'Cerrada', 'Cancelada') DEFAULT 'Abierta',
    fecha_apertura DATE NOT NULL,
    fecha_cierre DATE,
    usuario_creador_id INT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (departamento_id) REFERENCES departamentos(id) ON DELETE SET NULL,
    FOREIGN KEY (puesto_id) REFERENCES puestos(id) ON DELETE SET NULL,
    FOREIGN KEY (usuario_creador_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_estatus (estatus)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Postulaciones
CREATE TABLE postulaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vacante_id INT NOT NULL,
    candidato_id INT NOT NULL,
    fecha_postulacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estatus ENUM('Postulado', 'Revisando', 'Preseleccionado', 'Descartado', 'Contratado') DEFAULT 'Postulado',
    calificacion INT,
    notas TEXT,
    
    FOREIGN KEY (vacante_id) REFERENCES vacantes(id) ON DELETE CASCADE,
    FOREIGN KEY (candidato_id) REFERENCES candidatos(id) ON DELETE CASCADE,
    UNIQUE KEY unique_vacante_candidato (vacante_id, candidato_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Entrevistas
CREATE TABLE entrevistas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    candidato_id INT NOT NULL,
    vacante_id INT,
    tipo ENUM('Telefónica', 'Presencial', 'Virtual', 'Técnica', 'Final') NOT NULL,
    fecha_programada DATETIME NOT NULL,
    duracion_minutos INT DEFAULT 60,
    entrevistador_id INT,
    ubicacion VARCHAR(255),
    estatus ENUM('Programada', 'Realizada', 'Cancelada', 'Reprogramada') DEFAULT 'Programada',
    
    -- Resultados
    calificacion INT,
    observaciones TEXT,
    recomendacion ENUM('Contratar', 'Siguiente Fase', 'Rechazar', 'Pendiente'),
    
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (candidato_id) REFERENCES candidatos(id) ON DELETE CASCADE,
    FOREIGN KEY (vacante_id) REFERENCES vacantes(id) ON DELETE SET NULL,
    FOREIGN KEY (entrevistador_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_candidato (candidato_id),
    INDEX idx_fecha (fecha_programada)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- MÓDULO: BENEFICIOS E INCIDENCIAS
-- ============================================================

-- Préstamos
CREATE TABLE prestamos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empleado_id INT NOT NULL,
    monto_total DECIMAL(10,2) NOT NULL,
    monto_pendiente DECIMAL(10,2) NOT NULL,
    numero_pagos INT NOT NULL,
    monto_pago DECIMAL(10,2) NOT NULL,
    pagos_realizados INT DEFAULT 0,
    fecha_otorgamiento DATE NOT NULL,
    estatus ENUM('Activo', 'Pagado', 'Cancelado') DEFAULT 'Activo',
    motivo TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE CASCADE,
    INDEX idx_empleado (empleado_id),
    INDEX idx_estatus (estatus)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Pagos de préstamos
CREATE TABLE pagos_prestamos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prestamo_id INT NOT NULL,
    periodo_nomina_id INT,
    numero_pago INT NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    fecha_pago DATE NOT NULL,
    
    FOREIGN KEY (prestamo_id) REFERENCES prestamos(id) ON DELETE CASCADE,
    FOREIGN KEY (periodo_nomina_id) REFERENCES periodos_nomina(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bonos y apoyos
CREATE TABLE bonos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empleado_id INT NOT NULL,
    tipo_bono ENUM('Productividad', 'Puntualidad', 'Asistencia', 'Desempeño', 'Especial', 'Otro') NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    periodo_nomina_id INT,
    fecha_otorgamiento DATE NOT NULL,
    descripcion TEXT,
    usuario_autoriza_id INT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE CASCADE,
    FOREIGN KEY (periodo_nomina_id) REFERENCES periodos_nomina(id) ON DELETE SET NULL,
    FOREIGN KEY (usuario_autoriza_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- MÓDULO: DISPOSITIVOS HIKVISION
-- ============================================================

-- Dispositivos de control de acceso
CREATE TABLE dispositivos_hikvision (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    ip VARCHAR(15) NOT NULL,
    puerto INT DEFAULT 80,
    usuario VARCHAR(50),
    password VARCHAR(255),
    ubicacion VARCHAR(150),
    modelo VARCHAR(100),
    serial VARCHAR(100),
    activo TINYINT(1) DEFAULT 1,
    ultimo_sincronizado DATETIME,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_ip (ip)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Logs de sincronización
CREATE TABLE sync_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dispositivo_id INT NOT NULL,
    tipo ENUM('Entrada', 'Salida', 'Sincronización') NOT NULL,
    registros_procesados INT DEFAULT 0,
    estatus ENUM('Exitoso', 'Error', 'Parcial') NOT NULL,
    mensaje TEXT,
    fecha_sync TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (dispositivo_id) REFERENCES dispositivos_hikvision(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- DATOS DE EJEMPLO - ESTADO DE QUERÉTARO
-- ============================================================

-- Usuarios del sistema
INSERT INTO usuarios (nombre, email, password, rol, activo) VALUES
('Administrador Sistema', 'admin@sinforosa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1),
('María González', 'rrhh@sinforosa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'rrhh', 1),
('Carlos Ramírez', 'gerente@sinforosa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'gerente', 1);
-- Password por defecto: password

-- Departamentos
INSERT INTO departamentos (nombre, descripcion) VALUES
('Administración', 'Gestión administrativa y financiera'),
('Operaciones', 'Producción y operaciones del café'),
('Recursos Humanos', 'Gestión del talento humano'),
('Ventas', 'Ventas y atención al cliente'),
('Cocina', 'Preparación de alimentos y bebidas'),
('Mantenimiento', 'Mantenimiento de instalaciones');

-- Puestos
INSERT INTO puestos (nombre, departamento_id, salario_minimo, salario_maximo, nivel) VALUES
('Gerente General', 1, 25000, 35000, 5),
('Jefe de RRHH', 3, 18000, 25000, 4),
('Supervisor de Operaciones', 2, 15000, 20000, 3),
('Barista', 5, 8000, 12000, 2),
('Cajero', 4, 7500, 10000, 2),
('Cocinero', 5, 10000, 15000, 2),
('Ayudante General', 2, 6000, 8000, 1),
('Contador', 1, 15000, 20000, 3);

-- Empleados de ejemplo (Querétaro)
INSERT INTO empleados (numero_empleado, nombres, apellido_paterno, apellido_materno, curp, rfc, nss, fecha_nacimiento, genero, estado_civil, email_personal, telefono, celular, calle, numero_exterior, colonia, codigo_postal, municipio, estado, fecha_ingreso, tipo_contrato, departamento, puesto, salario_diario, salario_mensual, usuario_id) VALUES
('EMP001', 'Juan Carlos', 'López', 'Hernández', 'LOHJ850315HQTRPN01', 'LOHJ850315XY1', '12345678901', '1985-03-15', 'M', 'Casado', 'juan.lopez@email.com', '4421234567', '4421234567', 'Avenida Constituyentes', '123', 'Centro', '76000', 'Querétaro', 'Querétaro', '2020-01-15', 'Planta', 'Administración', 'Gerente General', 1166.67, 35000, 3),
('EMP002', 'Ana María', 'Martínez', 'García', 'MAGA900525MQTRNN02', 'MAGA900525AB2', '12345678902', '1990-05-25', 'F', 'Soltera', 'ana.martinez@email.com', '4421234568', '4421234568', 'Boulevard Bernardo Quintana', '456', 'Alamos', '76140', 'Querétaro', 'Querétaro', '2021-03-10', 'Planta', 'Recursos Humanos', 'Jefe de RRHH', 833.33, 25000, 2),
('EMP003', 'Roberto', 'Sánchez', 'Pérez', 'SAPR880712HQTRNB03', 'SAPR880712CD3', '12345678903', '1988-07-12', 'M', 'Casado', 'roberto.sanchez@email.com', '4421234569', '4421234569', 'Avenida Universidad', '789', 'Juriquilla', '76230', 'Querétaro', 'Querétaro', '2021-06-01', 'Planta', 'Operaciones', 'Supervisor de Operaciones', 666.67, 20000, NULL),
('EMP004', 'Laura', 'Ramírez', 'Flores', 'RAFL950208MQTRMR04', 'RAFL950208EF4', '12345678904', '1995-02-08', 'F', 'Soltera', 'laura.ramirez@email.com', '4421234570', '4421234570', 'Calzada de los Arcos', '321', 'El Mirador', '76020', 'Querétaro', 'Querétaro', '2022-01-20', 'Planta', 'Cocina', 'Barista', 400.00, 12000, NULL),
('EMP005', 'Miguel Ángel', 'Torres', 'Morales', 'TOMM920615HQTRRL05', 'TOMM920615GH5', '12345678905', '1992-06-15', 'M', 'Soltero', 'miguel.torres@email.com', '4421234571', '4421234571', 'Avenida 5 de Febrero', '654', 'Santa Rosa Jáuregui', '76220', 'Querétaro', 'Querétaro', '2022-03-15', 'Planta', 'Ventas', 'Cajero', 333.33, 10000, NULL),
('EMP006', 'Patricia', 'Jiménez', 'Castro', 'JICP931120MQTMST06', 'JICP931120IJ6', '12345678906', '1993-11-20', 'F', 'Casada', 'patricia.jimenez@email.com', '4421234572', '4421234572', 'Boulevard Peña Flor', '987', 'El Refugio', '76146', 'Querétaro', 'Querétaro', '2022-05-01', 'Planta', 'Cocina', 'Cocinero', 500.00, 15000, NULL),
('EMP007', 'José Luis', 'Hernández', 'Ruiz', 'HERJ940830HQTRRS07', 'HERJ940830KL7', '12345678907', '1994-08-30', 'M', 'Soltero', 'jose.hernandez@email.com', '4421234573', '4421234573', 'Avenida Paseo de la República', '147', 'La Loma', '76060', 'Querétaro', 'Querétaro', '2023-01-10', 'Planta', 'Operaciones', 'Ayudante General', 266.67, 8000, NULL),
('EMP008', 'Carmen', 'Gómez', 'Mendoza', 'GOMC891205MQTMND08', 'GOMC891205MN8', '12345678908', '1989-12-05', 'F', 'Divorciada', 'carmen.gomez@email.com', '4421234574', '4421234574', 'Corregidora Norte', '258', 'Centro', '76000', 'Querétaro', 'Querétaro', '2021-09-01', 'Planta', 'Administración', 'Contador', 666.67, 20000, NULL);

-- Turnos
INSERT INTO turnos (nombre, hora_entrada, hora_salida, minutos_tolerancia, horas_laborales) VALUES
('Matutino', '08:00:00', '16:00:00', 10, 8.00),
('Vespertino', '14:00:00', '22:00:00', 10, 8.00),
('Nocturno', '22:00:00', '06:00:00', 10, 8.00),
('Mixto', '10:00:00', '18:00:00', 10, 8.00);

-- Conceptos de nómina
INSERT INTO conceptos_nomina (clave, nombre, tipo, categoria, afecta_imss, afecta_isr) VALUES
('P001', 'Sueldo Base', 'Percepción', 'Fijo', 1, 1),
('P002', 'Bono de Puntualidad', 'Percepción', 'Variable', 0, 1),
('P003', 'Bono de Productividad', 'Percepción', 'Variable', 0, 1),
('P004', 'Horas Extra', 'Percepción', 'Variable', 1, 1),
('P005', 'Apoyo de Transporte', 'Percepción', 'Fijo', 0, 0),
('P006', 'Vales de Despensa', 'Percepción', 'Fijo', 0, 0),
('D001', 'IMSS', 'Deducción', 'Fijo', 1, 0),
('D002', 'ISR', 'Deducción', 'Fijo', 0, 1),
('D003', 'Préstamo', 'Deducción', 'Variable', 0, 0),
('D004', 'Fondo de Ahorro', 'Deducción', 'Variable', 0, 0),
('D005', 'INFONAVIT', 'Deducción', 'Fijo', 0, 0);

-- Historial laboral
INSERT INTO historial_laboral (empleado_id, tipo_evento, fecha_evento, puesto_nuevo, departamento_nuevo, salario_nuevo, motivo, usuario_registro_id) VALUES
(1, 'Contratación', '2020-01-15', 'Gerente General', 'Administración', 35000, 'Contratación inicial', 1),
(2, 'Contratación', '2021-03-10', 'Jefe de RRHH', 'Recursos Humanos', 25000, 'Contratación inicial', 1),
(3, 'Contratación', '2021-06-01', 'Supervisor de Operaciones', 'Operaciones', 20000, 'Contratación inicial', 1);

-- Vacaciones
INSERT INTO vacaciones (empleado_id, anio, dias_correspondientes, dias_tomados, dias_disponibles) VALUES
(1, 2024, 20, 5, 15),
(2, 2024, 16, 3, 13),
(3, 2024, 16, 0, 16),
(4, 2024, 12, 6, 6),
(5, 2024, 12, 0, 12);

-- Candidatos de ejemplo
INSERT INTO candidatos (nombres, apellido_paterno, apellido_materno, email, celular, municipio, estado, nivel_estudios, carrera, puesto_deseado, pretension_salarial, estatus, fuente_reclutamiento) VALUES
('Pedro', 'Velázquez', 'Luna', 'pedro.velazquez@email.com', '4429876543', 'Querétaro', 'Querétaro', 'Licenciatura', 'Administración de Empresas', 'Supervisor', 18000, 'En Revisión', 'LinkedIn'),
('Sofia', 'Morales', 'Campos', 'sofia.morales@email.com', '4429876544', 'El Marqués', 'Querétaro', 'Preparatoria', 'N/A', 'Barista', 9000, 'Entrevista', 'Referido'),
('Fernando', 'Cruz', 'Reyes', 'fernando.cruz@email.com', '4429876545', 'Corregidora', 'Querétaro', 'Técnico', 'Gastronomía', 'Cocinero', 12000, 'Nuevo', 'Indeed');

-- Dispositivos HikVision de ejemplo
INSERT INTO dispositivos_hikvision (nombre, ip, puerto, usuario, ubicacion, activo) VALUES
('Entrada Principal', '192.168.1.100', 80, 'admin', 'Acceso Principal - Querétaro Centro', 1),
('Comedor Empleados', '192.168.1.101', 80, 'admin', 'Área de Comedor - Planta Baja', 1);

-- ============================================================
-- VISTAS ÚTILES PARA REPORTES
-- ============================================================

CREATE VIEW vista_empleados_activos AS
SELECT 
    e.id, e.numero_empleado, 
    CONCAT(e.nombres, ' ', e.apellido_paterno, ' ', IFNULL(e.apellido_materno, '')) as nombre_completo,
    e.departamento, e.puesto, e.salario_mensual,
    e.fecha_ingreso,
    TIMESTAMPDIFF(YEAR, e.fecha_ingreso, CURDATE()) as anios_antiguedad,
    e.email_personal, e.celular
FROM empleados e
WHERE e.estatus = 'Activo';

CREATE VIEW vista_nomina_resumen AS
SELECT 
    p.id as periodo_id,
    p.tipo,
    p.fecha_inicio,
    p.fecha_fin,
    p.estatus,
    COUNT(nd.id) as total_empleados,
    SUM(nd.total_percepciones) as total_percepciones,
    SUM(nd.total_deducciones) as total_deducciones,
    SUM(nd.total_neto) as total_neto
FROM periodos_nomina p
LEFT JOIN nomina_detalle nd ON p.id = nd.periodo_id
GROUP BY p.id;

-- ============================================================
-- ÍNDICES ADICIONALES PARA OPTIMIZACIÓN
-- ============================================================

CREATE INDEX idx_empleados_fecha_ingreso ON empleados(fecha_ingreso);
CREATE INDEX idx_asistencias_empleado_fecha ON asistencias(empleado_id, fecha);
CREATE INDEX idx_historial_fecha ON historial_laboral(fecha_evento);

-- ============================================================
-- TABLA: configuraciones_globales (Sistema de Configuraciones)
-- ============================================================
CREATE TABLE configuraciones_globales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(100) UNIQUE NOT NULL,
    valor TEXT,
    tipo ENUM('texto', 'numero', 'email', 'color', 'imagen', 'json', 'boolean') DEFAULT 'texto',
    grupo VARCHAR(50) NOT NULL COMMENT 'sitio, email, contacto, estilo, paypal, qr',
    descripcion VARCHAR(255),
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_grupo (grupo),
    INDEX idx_clave (clave)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar configuraciones por defecto
INSERT INTO configuraciones_globales (clave, valor, tipo, grupo, descripcion) VALUES
-- Sitio
('sitio_nombre', 'Sistema RRHH Sinforosa Café', 'texto', 'sitio', 'Nombre del sitio'),
('sitio_logo', '', 'imagen', 'sitio', 'Logotipo del sitio'),
-- Email
('email_remitente', '', 'email', 'email', 'Correo que envía los mensajes del sistema'),
('email_remitente_nombre', 'Sistema RRHH', 'texto', 'email', 'Nombre del remitente'),
('email_smtp_host', '', 'texto', 'email', 'Servidor SMTP'),
('email_smtp_puerto', '587', 'numero', 'email', 'Puerto SMTP'),
('email_smtp_usuario', '', 'texto', 'email', 'Usuario SMTP'),
('email_smtp_password', '', 'texto', 'email', 'Contraseña SMTP'),
('email_smtp_seguridad', 'tls', 'texto', 'email', 'Seguridad SMTP (tls/ssl)'),
-- Contacto
('contacto_telefono1', '', 'texto', 'contacto', 'Teléfono principal'),
('contacto_telefono2', '', 'texto', 'contacto', 'Teléfono secundario'),
('contacto_whatsapp', '', 'texto', 'contacto', 'WhatsApp de contacto'),
('contacto_horario_inicio', '09:00', 'texto', 'contacto', 'Hora de inicio de atención'),
('contacto_horario_fin', '18:00', 'texto', 'contacto', 'Hora de fin de atención'),
('contacto_dias_atencion', 'Lunes a Viernes', 'texto', 'contacto', 'Días de atención'),
-- Estilos
('estilo_color_primario', '#667eea', 'color', 'estilo', 'Color primario del sistema'),
('estilo_color_secundario', '#764ba2', 'color', 'estilo', 'Color secundario del sistema'),
('estilo_color_acento', '#f59e0b', 'color', 'estilo', 'Color de acento'),
-- PayPal
('paypal_client_id', '', 'texto', 'paypal', 'Client ID de PayPal'),
('paypal_secret', '', 'texto', 'paypal', 'Secret de PayPal'),
('paypal_modo', 'sandbox', 'texto', 'paypal', 'Modo (sandbox/live)'),
-- QR API
('qr_api_key', '', 'texto', 'qr', 'API Key para generar QR'),
('qr_api_url', '', 'texto', 'qr', 'URL de la API de QR');

-- ============================================================
-- TABLA: dispositivos_shelly (Dispositivos Shelly Cloud)
-- ============================================================
CREATE TABLE dispositivos_shelly (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    device_id VARCHAR(50) UNIQUE NOT NULL,
    token_autenticacion VARCHAR(255) NOT NULL,
    servidor_cloud VARCHAR(255) NOT NULL,
    area VARCHAR(100),
    canal_entrada INT DEFAULT 1,
    canal_salida INT DEFAULT 0,
    duracion_pulso INT DEFAULT 600 COMMENT 'Duración del pulso en ms',
    accion VARCHAR(20) DEFAULT 'Abrir/Cerrar',
    habilitado TINYINT(1) DEFAULT 1,
    invertido TINYINT(1) DEFAULT 0 COMMENT 'Invertir estado off->on',
    simultaneo TINYINT(1) DEFAULT 0 COMMENT 'Dispositivo simultáneo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_device_id (device_id),
    INDEX idx_habilitado (habilitado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: dispositivos_hikvision (Dispositivos HikVision)
-- ============================================================
CREATE TABLE dispositivos_hikvision (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    tipo_dispositivo ENUM('LPR', 'Barcode') DEFAULT 'LPR' COMMENT 'Cámara LPR o Lector de código de barras',
    api_key VARCHAR(255) NOT NULL,
    api_secret VARCHAR(255) NOT NULL,
    endpoint_token VARCHAR(255) NOT NULL COMMENT 'URL para obtener token',
    area_domain VARCHAR(255) NOT NULL COMMENT 'Dominio del área para consultas',
    device_index_code VARCHAR(50) NOT NULL COMMENT 'Código de índice del dispositivo',
    area_ubicacion VARCHAR(100),
    -- Configuración ISAPI Local (Opcional)
    isapi_habilitado TINYINT(1) DEFAULT 0,
    isapi_url VARCHAR(255) COMMENT 'URL de API ISAPI local',
    isapi_usuario VARCHAR(100),
    isapi_password VARCHAR(255),
    verificar_ssl TINYINT(1) DEFAULT 1,
    -- Estado
    habilitado TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_device_code (device_index_code),
    INDEX idx_habilitado (habilitado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- FIN DEL SCHEMA
-- ============================================================
