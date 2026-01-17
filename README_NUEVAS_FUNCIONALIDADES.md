# Sistema RRHH - Módulo de Sucursales y Asistencia Pública

## Nuevas Funcionalidades Implementadas

### 1. Módulo de Sucursales
Sistema completo de gestión de sucursales con:
- CRUD de sucursales
- Asignación de gerentes por sucursal
- Asignación de dispositivos Shelly por sucursal
- URL pública única para cada sucursal

**Acceso:** Menú lateral → Personal → Sucursales

### 2. Vista Pública de Asistencias
Vista pública sin autenticación para registro de entrada/salida de empleados:
- Registro mediante código único de 6 dígitos
- Captura automática de foto con cámara
- Visualización de horas extras acumuladas
- Activación automática de dispositivos Shelly
- Validación de gerente para acceso a sucursales diferentes

**Acceso:** `https://tu-dominio.com/publico/asistencia/{url_sucursal}`

### 3. Mejoras en Empleados
- Campo obligatorio de sucursal al crear empleado
- Asignación de turno/horario
- Generación automática de código único de 6 dígitos
- Los códigos se generan automáticamente mediante trigger de base de datos

### 4. Personalización Global
- Colores personalizables del sistema (primario, secundario, acento)
- Upload y visualización de logo en admin, login y vistas públicas
- CSS dinámico basado en configuraciones de la BD

## Instalación y Configuración

### 1. Ejecutar Migración de Base de Datos

```bash
# Conectar a MySQL
mysql -u usuario -p nombre_base_datos < migration_sucursales_asistencia_publica.sql
```

Esta migración creará:
- Tabla `sucursales`
- Tabla `sucursal_gerentes`
- Tabla `sucursal_dispositivos`
- Campos nuevos en `empleados`: `sucursal_id`, `turno_id`, `codigo_empleado`
- Campos nuevos en `asistencias`: `foto_entrada`, `foto_salida`, `sucursal_id`, `gerente_autorizador_id`
- Función `generar_codigo_empleado()`
- Trigger para auto-generar códigos de empleados
- Datos de ejemplo de 3 sucursales

### 2. Configurar Permisos de Directorios

```bash
# Crear y dar permisos a directorio de uploads
mkdir -p uploads/asistencias uploads/logos
chmod 755 uploads
chmod 755 uploads/asistencias uploads/logos
```

### 3. Configurar Cron Job para Asistencias

Para auto-completar asistencias sin salida registrada, configurar cron job:

```bash
# Editar crontab
crontab -e

# Agregar línea para ejecutar diariamente a las 23:59
59 23 * * * /usr/bin/php /ruta/completa/al/proyecto/cron_procesar_asistencias.php >> /ruta/logs/asistencias_cron.log 2>&1
```

Este script:
- Se ejecuta diariamente a las 23:59
- Busca asistencias del día anterior sin hora de salida
- Asigna automáticamente las horas normales según turno del empleado
- Registra la hora de salida esperada según horario

### 4. Configurar Dispositivos Shelly (Opcional)

Si usas dispositivos Shelly para control de acceso:

1. Ir a **Configuraciones → Dispositivos IoT**
2. Agregar dispositivos Shelly con:
   - Device ID
   - Token de autenticación
   - Servidor cloud
   - Canal a activar
3. En cada sucursal, asignar los dispositivos Shelly correspondientes

## Uso del Sistema

### Crear una Sucursal

1. Ir a **Personal → Sucursales**
2. Click en "Nueva Sucursal"
3. Llenar:
   - Nombre de la sucursal
   - Código único
   - Dirección y teléfono
   - **URL Pública**: slug único para la vista pública (ej: "centro", "juriquilla")
4. Guardar

### Asignar Gerentes y Dispositivos

1. En la lista de sucursales, click en "Editar"
2. En la sección "Gerentes":
   - Click en "Agregar Gerente"
   - Seleccionar empleado
   - Guardar
3. En la sección "Dispositivos Shelly":
   - Click en "Agregar Dispositivo"
   - Seleccionar dispositivo y tipo de acción
   - Guardar

### Crear Empleados

Al crear un nuevo empleado:
- **Sucursal**: Campo obligatorio - seleccionar sucursal de trabajo
- **Turno/Horario**: Seleccionar turno asignado (Matutino, Vespertino, etc.)
- El sistema generará automáticamente un código único de 6 dígitos

### Registro de Asistencias (Vista Pública)

1. Acceder a: `https://tu-dominio.com/publico/asistencia/{url_sucursal}`
2. Ingresar código de 6 dígitos del empleado
3. Permitir acceso a la cámara
4. Click en "Registrar Entrada" o "Registrar Salida"
5. El sistema:
   - Captura automáticamente la foto
   - Registra la asistencia
   - Activa el dispositivo Shelly (si está configurado)
   - Muestra horas extras acumuladas

**Nota:** Si el empleado intenta registrarse en una sucursal diferente a la asignada, deberá ingresar el código del gerente de esa sucursal para autorizar el acceso.

### Personalizar Apariencia

1. Ir a **Configuraciones → Estilos**
2. Seleccionar colores:
   - Color Primario: Color principal del sistema
   - Color Secundario: Color para gradientes
   - Color de Acento: Color para elementos destacados
3. Ir a **Configuraciones → Sitio**
4. Cambiar nombre del sistema
5. Subir logo (formatos: JPG, PNG, GIF, WEBP - máx 2MB)
6. Guardar cambios

Los cambios se aplicarán inmediatamente en:
- Sidebar del admin
- Página de login
- Vistas públicas
- Todos los botones del sistema

## Estructura de la Base de Datos

### Nuevas Tablas

- **sucursales**: Información de sucursales
- **sucursal_gerentes**: Relación sucursal-gerentes
- **sucursal_dispositivos**: Relación sucursal-dispositivos Shelly

### Campos Agregados

**empleados:**
- `codigo_empleado` VARCHAR(6): Código único auto-generado
- `sucursal_id` INT: Sucursal de trabajo
- `turno_id` INT: Turno asignado

**asistencias:**
- `foto_entrada` VARCHAR(255): Ruta de foto de entrada
- `foto_salida` VARCHAR(255): Ruta de foto de salida
- `sucursal_id` INT: Sucursal donde se registró
- `gerente_autorizador_id` INT: Gerente que autorizó (si aplica)

## API de Asistencias

### Endpoint: POST /publico/registrar-asistencia

**Parámetros:**
- `codigo_empleado`: Código de 6 dígitos
- `tipo_registro`: "entrada" o "salida"
- `url_publica`: URL de la sucursal
- `foto`: Imagen en base64
- `codigo_gerente`: (Opcional) Código del gerente autorizador

**Respuesta exitosa:**
```json
{
  "success": true,
  "message": "Entrada registrada exitosamente",
  "tipo": "entrada",
  "empleado": "Juan Pérez",
  "hora": "08:30:00",
  "horas_extras_acumuladas": 5.5,
  "activacion_shelly": {
    "activado": true,
    "mensaje": "Dispositivo activado correctamente"
  }
}
```

## Seguridad

- Las vistas públicas NO requieren autenticación
- Los códigos de empleado son únicos y aleatorios
- Las fotos se almacenan en servidor con nombres únicos
- Validación de sucursal y autorización de gerente
- Upload de logos con validación de tipo y tamaño

## Troubleshooting

### Los colores no se aplican
- Verificar que la tabla `configuraciones_globales` tenga datos
- Limpiar caché del navegador
- Verificar que ConfigHelper se esté cargando correctamente

### Las fotos no se guardan
- Verificar permisos del directorio `uploads/asistencias`
- Verificar que PHP tenga permisos de escritura
- Verificar espacio en disco

### El código de empleado no se genera
- Verificar que el trigger `before_empleado_insert` existe
- Verificar que la función `generar_codigo_empleado()` existe
- Ejecutar manualmente: `UPDATE empleados SET codigo_empleado = generar_codigo_empleado() WHERE codigo_empleado IS NULL;`

### Dispositivo Shelly no se activa
- Verificar configuración del dispositivo en Dispositivos IoT
- Verificar que el dispositivo esté asignado a la sucursal
- Verificar conectividad con Shelly Cloud API
- Revisar logs de error de PHP

## Mantenimiento

### Limpieza de Fotos Antiguas

Crear script para limpiar fotos de más de 90 días:

```bash
find /ruta/uploads/asistencias -type f -mtime +90 -delete
```

### Backup de Base de Datos

```bash
mysqldump -u usuario -p nombre_bd > backup_$(date +%Y%m%d).sql
```

## Soporte

Para reportar bugs o solicitar funcionalidades adicionales, crear un issue en el repositorio.
