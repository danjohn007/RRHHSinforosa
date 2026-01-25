# Mejoras al Control de Asistencia

## Fecha: 2026-01-25

## Resumen de Cambios

Este documento detalla las mejoras implementadas en el módulo de Control de Asistencia del sistema RRHH Sinforosa, específicamente para resolver los problemas identificados en el issue de "Horas extras en Registros de Asistencia".

## Problemas Resueltos

### 1. ✅ Sucursal de Salida Incorrecta

**Problema:** El sistema mostraba la sucursal de entrada en lugar de la sucursal de salida cuando un empleado registraba su salida.

**Solución Implementada:**
- Agregado nuevo campo `sucursal_salida_id` a la tabla `asistencias`
- Actualizado `PublicoController.php` para guardar la sucursal donde se registra la salida
- Actualizada vista `asistencia/index.php` para mostrar correctamente la sucursal de salida
- Actualizado procedimiento almacenado `auto_cortar_asistencias()` para establecer `sucursal_salida_id` en auto-cortes

**Archivos Modificados:**
- `migration_fix_sucursal_salida.sql` - Nueva migración con campo y vista actualizada
- `app/controllers/PublicoController.php` - Línea 226: Agregado parámetro sucursal_salida_id
- `app/views/asistencia/index.php` - Línea 228: Muestra sucursal de salida o entrada
- `migration_update_auto_cortar_procedure.sql` - Procedimiento actualizado

### 2. ✅ Captura de Fotografías Mejorada

**Problema:** Las fotografías no se guardaban correctamente cuando se usaban las opciones "Repetir" o "Confirmar" en la activación de la cámara.

**Solución Implementada:**
- Agregada validación en el botón "Confirmar" para verificar que existe una foto capturada
- Mejorada la función `guardarFoto()` con validaciones adicionales:
  - Verificación de datos recibidos
  - Validación de decodificación base64
  - Verificación de bytes escritos
  - Mejores mensajes de error en logs

**Archivos Modificados:**
- `app/views/publico/asistencia.php` - Línea 402: Validación antes de confirmar
- `app/controllers/PublicoController.php` - Línea 276-328: Función guardarFoto mejorada

### 3. ✅ Filtro de Estatus "POR VALIDAR"

**Problema:** Necesidad de filtrar fácilmente las asistencias que están "POR VALIDAR".

**Estado:** Ya implementado correctamente en versión anterior.

**Verificación:**
- `app/controllers/AsistenciaController.php` - Línea 17, 43-46: Filtro de estatus implementado
- `app/views/asistencia/index.php` - Línea 32-44: Dropdown con opción "Por Validar"

### 4. ✅ Auto-Corte de Asistencias

**Problema:** Asegurar que el auto-corte de asistencias funciona correctamente y registra la sucursal de salida.

**Solución Implementada:**
- Actualizado procedimiento almacenado `auto_cortar_asistencias()`
- Agregada actualización masiva para registros históricos sin sucursal_salida_id
- El procedimiento ahora:
  - Establece `sucursal_salida_id` igual a `sucursal_id` (misma sucursal)
  - Calcula horas trabajadas y extras correctamente
  - Cambia estatus a "Por Validar"
  - Marca el registro como `auto_cortado = 1`

**Archivos Modificados:**
- `migration_update_auto_cortar_procedure.sql` - Procedimiento actualizado

## Instrucciones de Instalación

### 1. Ejecutar Migraciones

Ejecutar las siguientes migraciones en orden:

```sql
-- 1. Agregar campo sucursal_salida_id y actualizar vista
SOURCE migration_fix_sucursal_salida.sql;

-- 2. Actualizar procedimiento almacenado
SOURCE migration_update_auto_cortar_procedure.sql;
```

### 2. Verificar Cambios

Después de ejecutar las migraciones, verificar:

```sql
-- Verificar que el campo existe
SHOW COLUMNS FROM asistencias LIKE 'sucursal_salida_id';

-- Verificar que la vista incluye los nuevos campos
DESC vista_asistencias_completa;

-- Verificar que el procedimiento existe
SHOW PROCEDURE STATUS WHERE Name = 'auto_cortar_asistencias';
```

### 3. Configurar Cron Job (si no existe)

El archivo `cron_procesar_asistencias.php` debe ejecutarse diariamente. Agregar al crontab:

```bash
# Ejecutar auto-corte de asistencias todos los días a las 00:05
5 0 * * * php /ruta/al/proyecto/cron_procesar_asistencias.php >> /var/log/asistencias_cron.log 2>&1
```

## Funcionalidades Agregadas/Mejoradas

### Registro de Asistencia (Vista Pública)
- ✅ Guarda correctamente la sucursal donde se registra la salida
- ✅ Validación mejorada de captura de fotografías
- ✅ Mejor manejo de errores al guardar fotos

### Control de Asistencia (Vista Administrativa)
- ✅ Muestra correctamente la sucursal de entrada y salida
- ✅ Filtro de estatus funcional (incluye "Por Validar")
- ✅ Indicador visual cuando fue auto-cortado

### Validación de Asistencias
- ✅ Modal para validar asistencias con estatus "Por Validar"
- ✅ Solicita hora real de salida
- ✅ Calcula horas trabajadas y extras correctamente
- ✅ Actualiza estatus a "Validado"

## Estructura de Base de Datos

### Tabla: `asistencias`

Nuevos campos agregados:
- `sucursal_salida_id` (INT, NULL) - ID de sucursal donde se registró la salida
- FK: `fk_asistencia_sucursal_salida` - Referencia a tabla sucursales

### Vista: `vista_asistencias_completa`

Campos agregados:
- `sucursal_salida_id` - ID de sucursal de salida
- `sucursal_salida_nombre` - Nombre de sucursal de salida
- `sucursal_salida_codigo` - Código de sucursal de salida

## Flujo de Trabajo

### Registro Normal de Asistencia

1. Empleado ingresa código en terminal de sucursal
2. Captura foto (puede usar Capturar/Repetir/Confirmar)
3. Sistema guarda:
   - Entrada: `sucursal_id` = sucursal actual
   - Salida: `sucursal_salida_id` = sucursal actual

### Auto-Corte de Asistencia

1. Cron ejecuta diariamente a las 00:05
2. Busca asistencias sin salida del día anterior
3. Para cada registro:
   - Obtiene horario de salida de la sucursal
   - Calcula hora de salida basada en horario
   - Calcula horas trabajadas y extras
   - Establece `sucursal_salida_id = sucursal_id`
   - Marca como `auto_cortado = 1`
   - Cambia estatus a "Por Validar"

### Validación de Asistencia

1. Usuario con permisos filtra por "Por Validar"
2. Click en botón "Validar" en la fila
3. Ingresa hora real de salida
4. Sistema:
   - Actualiza `hora_salida_real`
   - Recalcula horas trabajadas y extras
   - Cambia estatus a "Validado"
   - Guarda usuario y fecha de validación

## Pruebas Recomendadas

### 1. Prueba de Sucursal de Salida
- [ ] Registrar entrada en Sucursal A
- [ ] Registrar salida en Sucursal A (verificar muestra Sucursal A)
- [ ] Registrar entrada en Sucursal B con código de gerente
- [ ] Registrar salida en Sucursal B (verificar muestra Sucursal B, no A)

### 2. Prueba de Fotografías
- [ ] Capturar foto con botón "Capturar" - verificar se guarda
- [ ] Capturar foto, click "Repetir", capturar nueva - verificar se guarda
- [ ] Capturar foto, click "Confirmar" - verificar se guarda

### 3. Prueba de Filtros
- [ ] Filtrar por estatus "Por Validar" - verificar muestra solo esos registros
- [ ] Filtrar por fecha y estatus combinados
- [ ] Limpiar filtros

### 4. Prueba de Auto-Corte
- [ ] Ejecutar manualmente: `php cron_procesar_asistencias.php`
- [ ] Verificar registros sin salida del día anterior se auto-cortaron
- [ ] Verificar tienen `sucursal_salida_id` establecido
- [ ] Verificar estatus cambió a "Por Validar"

### 5. Prueba de Validación
- [ ] Filtrar por "Por Validar"
- [ ] Click en "Validar" en un registro auto-cortado
- [ ] Ingresar hora real de salida
- [ ] Verificar estatus cambió a "Validado"
- [ ] Verificar horas se recalcularon correctamente

## Notas Técnicas

### Compatibilidad con Versiones Anteriores
- Los registros existentes sin `sucursal_salida_id` seguirán funcionando
- La vista muestra `sucursal_nombre` si no hay `sucursal_salida_nombre`
- La migración actualiza registros históricos automáticamente

### Seguridad
- Validación de datos en cliente y servidor
- Fotos guardadas con nombres únicos
- Directorio de fotos creado con permisos 0755
- Validación de base64 antes de decodificar

### Rendimiento
- Vista optimizada con LEFT JOIN para sucursal_salida
- Índice en `sucursal_salida_id` para consultas rápidas
- Límite de 500 registros en vista principal

## Soporte y Mantenimiento

### Logs
- Errores de guardado de fotos: `error_log` PHP
- Ejecución de cron: `/var/log/asistencias_cron.log`

### Troubleshooting

**Fotos no se guardan:**
- Verificar permisos del directorio `uploads/asistencias/`
- Verificar espacio en disco
- Revisar error_log de PHP

**Auto-corte no funciona:**
- Verificar que cron está configurado
- Verificar que el procedimiento existe en BD
- Ejecutar manualmente para ver errores

**Sucursal de salida es NULL:**
- Normal si aún no ha registrado salida
- Si ya registró salida, ejecutar la actualización masiva del migration

## Conclusión

Todos los problemas identificados en el issue han sido resueltos:
1. ✅ Sucursal de salida se registra correctamente
2. ✅ Fotografías se guardan en todos los escenarios
3. ✅ Filtro de estatus "Por Validar" funcional
4. ✅ Auto-corte de asistencias actualizado

El sistema ahora proporciona un control más preciso y completo de las asistencias, facilitando la validación y seguimiento de horas extras.
