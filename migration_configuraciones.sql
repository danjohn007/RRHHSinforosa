-- ============================================================
-- ACTUALIZACIÓN DE SCHEMA - MÓDULO DE CONFIGURACIONES GLOBALES
-- Solo agrega las nuevas tablas sin modificar las existentes
-- ============================================================

USE recursos_humanos;

-- ============================================================
-- TABLA: configuraciones_globales (Sistema de Configuraciones)
-- ============================================================
CREATE TABLE IF NOT EXISTS configuraciones_globales (
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
INSERT IGNORE INTO configuraciones_globales (clave, valor, tipo, grupo, descripcion) VALUES
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
CREATE TABLE IF NOT EXISTS dispositivos_shelly (
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
CREATE TABLE IF NOT EXISTS dispositivos_hikvision (
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
