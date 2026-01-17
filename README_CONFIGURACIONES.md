# Módulo de Configuraciones Globales

## Descripción

Este módulo agrega funcionalidad de configuraciones globales al sistema, permitiendo administrar:

1. **Configuración del Sitio**: Nombre y logotipo
2. **Configuración de Email**: Servidor SMTP para envío de correos
3. **Información de Contacto**: Teléfonos y horarios de atención
4. **Personalización de Estilos**: Colores del sistema
5. **Configuración de PayPal**: Integración de pagos
6. **API de QR**: Generación masiva de códigos QR
7. **Dispositivos Shelly Cloud**: Control de acceso IoT
8. **Dispositivos HikVision**: Cámaras LPR y lectores de código de barras

## Instalación

### 1. Aplicar Migración de Base de Datos

Ejecuta el siguiente comando para agregar las nuevas tablas a la base de datos:

```bash
mysql -u recursos_humanos -p'Danjohn007!' recursos_humanos < migration_configuraciones.sql
```

O desde MySQL:

```sql
USE recursos_humanos;
SOURCE /ruta/completa/a/migration_configuraciones.sql;
```

### 2. Verificar las Tablas

Las siguientes tablas deben crearse:
- `configuraciones_globales`: Almacena todas las configuraciones del sistema
- `dispositivos_shelly`: Gestión de dispositivos Shelly Cloud
- `dispositivos_hikvision`: Gestión de dispositivos HikVision

### 3. Acceso al Módulo

El módulo está disponible en el menú lateral solo para usuarios con rol `admin`:

- **Configuraciones Globales**: `/configuraciones`
- **Dispositivos IoT**: `/configuraciones/dispositivos`

## Características

### Configuraciones Globales

La vista de configuraciones está organizada en pestañas:

#### Sitio
- Nombre del sitio
- URL del logotipo

#### Email
- Correo remitente
- Servidor SMTP (host, puerto, seguridad)
- Credenciales de autenticación

#### Contacto
- Teléfonos de contacto
- WhatsApp
- Horarios de atención

#### Estilos
- Color primario (gradiente)
- Color secundario (gradiente)
- Color de acento
- Vista previa en tiempo real

#### PayPal
- Client ID
- Secret
- Modo (sandbox/live)

#### QR API
- API Key
- URL de la API

### Dispositivos IoT

#### Dispositivos Shelly Cloud
Configura dispositivos Shelly para control de acceso con:
- Token de autenticación
- Device ID
- Servidor cloud
- Canales de entrada/salida
- Duración de pulso
- Estados (habilitado, invertido, simultáneo)

#### Dispositivos HikVision
Configura cámaras LPR y lectores de código de barras:
- Tipo de dispositivo (LPR/Barcode)
- Credenciales API (Key, Secret)
- Endpoints y dominios
- Configuración ISAPI local (opcional)
- Verificación SSL

## Estructura de Archivos

```
app/
├── controllers/
│   └── ConfiguracionesController.php    # Controlador principal
├── views/
│   └── configuraciones/
│       ├── index.php                    # Vista de configuraciones
│       └── dispositivos.php             # Vista de dispositivos IoT
└── views/layouts/
    └── main.php                         # Actualizado con menú

config/
└── database.php                         # Configuración de BD

index.php                                # Rutas actualizadas
schema.sql                               # Schema completo actualizado
migration_configuraciones.sql            # Migración incremental
```

## Seguridad

- Solo usuarios con rol `admin` pueden acceder al módulo
- Las contraseñas se muestran ofuscadas en la UI
- Validación de permisos en todas las operaciones
- Prepared statements para prevenir SQL injection

## API Endpoints

### Configuraciones
- `POST /configuraciones/guardar` - Guardar configuraciones generales

### Dispositivos
- `POST /configuraciones/guardar-dispositivo` - Crear/actualizar dispositivo
- `GET /configuraciones/obtener-dispositivo?id={id}&tipo={tipo}` - Obtener datos de dispositivo
- `POST /configuraciones/eliminar-dispositivo` - Eliminar dispositivo

## Notas de Desarrollo

1. Las configuraciones se almacenan en formato clave-valor en la tabla `configuraciones_globales`
2. Los dispositivos son independientes y pueden ser gestionados por separado
3. La UI utiliza Tailwind CSS para mantener consistencia con el resto del sistema
4. Los colores del sistema se pueden cambiar dinámicamente (requiere recarga para aplicar)

## Soporte

Para reportar problemas o sugerencias, contactar al equipo de desarrollo.
