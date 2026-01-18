# Áreas de Trabajo en Sucursales - Documentación

## Resumen de Cambios

Esta actualización agrega un sistema completo de áreas de trabajo para las sucursales, mejoras en la gestión de dispositivos Shelly y correcciones importantes.

## Nuevas Funcionalidades

### 1. Sistema de Áreas de Trabajo

Cada sucursal ahora puede tener múltiples áreas de trabajo configuradas:

- **Áreas Predeterminadas**: Al crear una sucursal, se crean automáticamente dos áreas predeterminadas:
  - **Entrada**: Para registro de entrada de empleados
  - **Salida**: Para registro de salida de empleados

- **Áreas Personalizadas**: Los administradores pueden crear áreas adicionales según las necesidades de cada sucursal

- **Configuración por Área**:
  - Nombre descriptivo del área
  - Descripción opcional
  - Dispositivo Shelly asignado
  - Canal específico del dispositivo (0-3)
  - Estado activo/inactivo

### 2. Mejoras en Dispositivos Shelly

#### Edición de Dispositivos
- Cada dispositivo Shelly ahora tiene un botón de edición
- Se puede modificar toda la configuración sin necesidad de eliminar y recrear

#### Prueba de Canales
- Botones "Probar" junto a cada canal (Entrada y Salida)
- Permite verificar el funcionamiento de los canales antes de asignarlos
- Feedback visual inmediato del resultado de la prueba

#### Mejoras de Seguridad
- Botón para mostrar/ocultar el token de autenticación
- Mejor validación de datos al guardar

### 3. Corrección: Configuración del Sitio

Se corrigió el problema donde al subir un logo, el nombre del sitio se cambiaba por error a la ruta de la imagen:

- El campo "Nombre del Sitio" mantiene su valor al subir un nuevo logo
- Vista previa del logo mejorada con manejo de errores
- Mejor feedback visual cuando la imagen no carga

## Instalación

### Requisitos Previos
- Base de datos MySQL/MariaDB
- Acceso de administrador al sistema
- PHP 7.4 o superior
- Servidor web (Apache/Nginx)

### Configuración

1. **Configurar modo de producción** (IMPORTANTE para seguridad)
   
   Editar `config/config.php` y cambiar:
   ```php
   define('DEVELOPMENT_MODE', false); // Cambiar a false en producción
   ```
   
   Esto habilita la verificación de certificados SSL para conexiones con Shelly Cloud.

### Pasos de Migración

1. **Respaldar la base de datos actual** (¡IMPORTANTE!)
   ```bash
   mysqldump -u usuario -p recursos_humanos > backup_$(date +%Y%m%d).sql
   ```

2. **Ejecutar la migración de áreas de trabajo**
   ```bash
   mysql -u usuario -p recursos_humanos < migration_areas_trabajo.sql
   ```

3. **Verificar la migración**
   - Inicia sesión como administrador
   - Ve a "Sucursales" > "Editar" cualquier sucursal
   - Deberías ver la nueva sección "Áreas de Trabajo"
   - Verifica que existan las áreas "Entrada" y "Salida" predeterminadas

## Uso

### Configurar Áreas de Trabajo

1. **Acceder a la configuración**:
   - Navega a: `Sucursales` > Selecciona una sucursal > `Editar`
   - Desplázate a la sección "Áreas de Trabajo"

2. **Agregar nueva área**:
   - Click en "Agregar Área"
   - Completa el formulario:
     - Nombre: Ej. "Entrada Principal", "Salida Trasera"
     - Descripción: Opcional, información adicional
     - Dispositivo Shelly: Selecciona de la lista de dispositivos disponibles
     - Canal Asignado: Selecciona el canal (0-3) que activará esta área
     - Marcar como "Área activa"
   - Click en "Guardar"

3. **Editar área existente**:
   - Click en el icono de edición (lápiz) del área
   - Modifica los campos necesarios
   - Click en "Guardar"

4. **Eliminar área**:
   - Solo se pueden eliminar áreas personalizadas
   - Las áreas predeterminadas (Entrada/Salida) no se pueden eliminar
   - Click en el icono de eliminar (papelera)
   - Confirma la eliminación

### Configurar y Probar Dispositivos Shelly

1. **Editar dispositivo**:
   - Navega a: `Configuración` > `Dispositivos`
   - Click en el botón de edición (lápiz azul) en el dispositivo
   - Modifica la configuración necesaria
   - Click en "Guardar"

2. **Probar canales**:
   - En la lista de dispositivos, localiza el canal que deseas probar
   - Click en el botón "Probar" junto al canal
   - El sistema intentará activar el canal
   - Verás un mensaje confirmando si la activación fue exitosa o no

### Registro de Asistencia

El sistema de registro público ahora:

1. Busca el área de trabajo correspondiente (Entrada o Salida) para la sucursal
2. Si encuentra un área configurada, activa el dispositivo Shelly y canal asignados
3. Si no hay área configurada, usa el sistema de compatibilidad anterior (dispositivos asignados directamente)
4. Registra errores detallados si el dispositivo no responde

## Solución de Problemas

### Los dispositivos Shelly no se activan

1. **Verificar configuración del dispositivo**:
   - Asegúrate de que el Device ID, Token y Servidor Cloud sean correctos
   - Usa el botón "Probar" para verificar cada canal

2. **Revisar los logs del sistema**:
   - Los errores de Shelly se registran en el log del servidor
   - Busca mensajes que comiencen con "Advertencia: Shelly no activado"

3. **Verificar asignación de áreas**:
   - Confirma que el área de trabajo tenga un dispositivo asignado
   - Verifica que el canal seleccionado sea el correcto
   - Asegúrate de que el área esté marcada como "activa"

### El logo no se muestra correctamente

1. **Verificar la ruta del logo**:
   - El logo debe estar en la carpeta `uploads/logos/`
   - La ruta debe ser relativa: `uploads/logos/logo_xxxxx.ext`

2. **Permisos de archivos**:
   ```bash
   chmod 755 uploads/
   chmod 755 uploads/logos/
   chmod 644 uploads/logos/*
   ```

3. **Tamaño y formato**:
   - Máximo 2MB
   - Formatos permitidos: JPG, PNG, GIF, WEBP

### El nombre del sitio se cambió al subir el logo

Este problema ha sido corregido en esta actualización. Si persiste:

1. Restaura el nombre correcto en "Configuración" > "Sitio"
2. Asegúrate de estar usando la versión más reciente del código
3. Limpia la caché del navegador

## Estructura de Base de Datos

### Nueva Tabla: `sucursal_areas_trabajo`

```sql
CREATE TABLE sucursal_areas_trabajo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sucursal_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    dispositivo_shelly_id INT,
    canal_asignado INT DEFAULT 0,
    activo TINYINT(1) DEFAULT 1,
    es_predeterminada TINYINT(1) DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sucursal_id) REFERENCES sucursales(id) ON DELETE CASCADE,
    FOREIGN KEY (dispositivo_shelly_id) REFERENCES dispositivos_shelly(id) ON DELETE SET NULL
);
```

### Nuevas Columnas

- `sucursal_dispositivos.area_trabajo_id`: Referencia al área de trabajo
- `asistencias.area_trabajo_entrada_id`: Área donde se registró la entrada
- `asistencias.area_trabajo_salida_id`: Área donde se registró la salida

## API Endpoints

### Áreas de Trabajo

- **POST** `/sucursales/guardar-area-trabajo`: Crear o actualizar área
- **POST** `/sucursales/eliminar-area-trabajo`: Eliminar área

### Dispositivos Shelly

- **POST** `/configuraciones/test-shelly-channel`: Probar un canal del dispositivo

## Compatibilidad

Esta actualización es **compatible con versiones anteriores**:

- Las asignaciones de dispositivos existentes seguirán funcionando
- El sistema intenta usar áreas de trabajo primero, luego cae back al método anterior
- No es necesario reconfigurar dispositivos existentes (pero se recomienda usar el nuevo sistema)

## Recomendaciones

1. **Migrar a áreas de trabajo**: Aunque el sistema anterior sigue funcionando, se recomienda migrar a áreas de trabajo para mejor control y flexibilidad

2. **Probar canales regularmente**: Usa el botón "Probar" periódicamente para verificar que los dispositivos responden correctamente

3. **Monitorear logs**: Revisa los logs del sistema para detectar problemas de comunicación con dispositivos Shelly

4. **Documentar configuraciones**: Mantén un registro de qué canal controla qué acceso físico

## Soporte

Si encuentras problemas o tienes preguntas:

1. Revisa esta documentación
2. Consulta los logs del sistema
3. Verifica la configuración de dispositivos con el botón "Probar"
4. Contacta al administrador del sistema

---

**Fecha de última actualización**: 2026-01-18  
**Versión**: 1.1.0
