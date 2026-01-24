-- ============================================================
-- ACTUALIZACIÓN DE SCHEMA - MÓDULO DE TIMBRADO DE NÓMINA
-- Agrega configuraciones para el timbrado de CFDI de nómina
-- Fecha: 2026-01-24
-- ============================================================

USE recursos_humanos;

-- ============================================================
-- NUEVAS CONFIGURACIONES - TIMBRADO DE NÓMINA
-- ============================================================

-- Insertar configuraciones para Timbrado de Nómina
INSERT IGNORE INTO configuraciones_globales (clave, valor, tipo, grupo, descripcion) VALUES
-- Datos del Emisor
('timbrado_rfc_emisor', '', 'texto', 'timbrado', 'RFC del emisor para CFDI'),
('timbrado_razon_social', '', 'texto', 'timbrado', 'Razón social o nombre del emisor'),

-- E.firma (Certificado Digital)
('timbrado_certificado', '', 'texto', 'timbrado', 'Ruta del archivo .cer del certificado digital'),
('timbrado_llave_privada', '', 'texto', 'timbrado', 'Ruta del archivo .key de la llave privada'),
('timbrado_password_llave', '', 'texto', 'timbrado', 'Contraseña de la llave privada'),

-- Configuración de API
('timbrado_api_url', '', 'texto', 'timbrado', 'URL de la API de timbrado'),
('timbrado_api_usuario', '', 'texto', 'timbrado', 'Usuario de la API de timbrado'),
('timbrado_api_password', '', 'texto', 'timbrado', 'Contraseña de la API de timbrado'),
('timbrado_api_token', '', 'texto', 'timbrado', 'Token de autenticación de la API'),

-- Configuración de Cancelación
('timbrado_api_cancelacion_url', '', 'texto', 'timbrado', 'URL de la API para cancelación de CFDI'),

-- Modo de Operación
('timbrado_modo', 'pruebas', 'texto', 'timbrado', 'Ambiente: pruebas o produccion');

-- ============================================================
-- NOTA IMPORTANTE
-- ============================================================
-- Este script agrega configuraciones para el módulo de Timbrado de Nómina.
-- Las configuraciones incluyen:
-- 
-- 1. Datos del Emisor (RFC y Razón Social)
-- 2. E.firma (Certificado y Llave Privada con contraseña)
-- 3. Configuración de API (URL, usuario, contraseña, token)
-- 4. Configuración de Cancelación (URL de API)
-- 5. Modo de Operación (pruebas/producción)
--
-- Los archivos de e.firma (.cer y .key) se subirán a través de la
-- interfaz web y se almacenarán en la carpeta uploads/efirma/
--
-- Códigos de Error del PAC:
-- 01 - Comprobante emitido con errores con relación
-- 02 - Comprobante emitido con errores sin relación
-- 03 - No se llevó a cabo la operación
-- 04 - Operación nominativa relacionada en una factura global
-- ============================================================

SELECT 'Script de migración ejecutado correctamente' as Resultado;
