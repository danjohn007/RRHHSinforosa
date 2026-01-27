# Solución: Validación de Horario de Salida y Guardado de Horarios

## Resumen Ejecutivo

Este documento detalla la solución implementada para los 4 problemas reportados en el sistema de asistencias.

## Problemas Resueltos

### 1. Auto-Corte de Asistencias Sin Salida Registrada ✅

**Problema:** El sistema no agregaba automáticamente el corte de salida cuando los empleados no registraban su salida.

**Solución Implementada:**
- **Procedimiento:** `auto_cortar_asistencias()` en `migration_update_auto_cortar_procedure.sql`
- **Funcionalidad:**
  - Busca asistencias de días anteriores sin hora de salida registrada
  - Asigna automáticamente la hora de salida según el horario de cierre de la sucursal
  - Calcula las horas trabajadas y horas extras automáticamente
  - Cambia el estatus de "PRESENTE" a "POR VALIDAR"
  - Registra que fue auto-cortado (`auto_cortado = 1`)
  - Asigna `sucursal_salida_id` igual a `sucursal_id` (sucursal de entrada)

**Ejecución Automática:**
- Script: `cron_procesar_asistencias.php`
- Frecuencia: Diario (recomendado 00:05 o 23:59)
- Configurar en cron: `5 0 * * * /usr/bin/php /ruta/al/proyecto/cron_procesar_asistencias.php`

**Ejemplo de Resultado:**
```
Empleado registró entrada a las 08:00
No registró salida
Sistema auto-corta a las 18:00 (horario de cierre de sucursal)
Horas trabajadas: 10.00 hrs
Horas extras: 2.00 hrs
Estatus: "Por Validar"
```

### 2. Nombre Correcto de Sucursal en Salida ✅

**Problema:** Siempre se mostraba la sucursal de entrada, incluso en el registro de salida.

**Solución Implementada:**
- **Campo agregado:** `sucursal_salida_id` en tabla `asistencias`
- **Vista actualizada:** `vista_asistencias_completa` incluye:
  - `sucursal_salida_id` - ID de sucursal donde se registró la salida
  - `sucursal_salida_nombre` - Nombre de sucursal de salida
  - `sucursal_salida_codigo` - Código de sucursal de salida

**Lógica de Visualización:**
```php
// En app/views/asistencia/index.php
$nombreSucursalSalida = $asistencia['sucursal_salida_nombre'] ?? $asistencia['sucursal_nombre'];
```
- Si existe `sucursal_salida_nombre`, lo muestra (registró salida en diferente sucursal)
- Si no existe, muestra `sucursal_nombre` (registró en la misma sucursal o auto-cortado)

**Indicador Visual:**
- Muestra icono de edificio con el nombre de la sucursal
- Para registros auto-cortados, muestra "Auto-cortado" en naranja

### 3. Modal de Validación para Registros "POR VALIDAR" ✅

**Problema:** Necesidad de validar manualmente los registros auto-cortados.

**Solución Implementada:**

**Filtro en Vista:**
- Dropdown en sección de filtros con opción "Por Validar"
- Permite filtrar solo registros que necesitan validación

**Modal de Validación:**
- Ubicación: `app/views/asistencia/index.php`
- Características:
  - Se activa con botón "Validar" en cada registro "Por Validar"
  - Muestra información del empleado y fecha
  - Campo obligatorio: Hora de Salida Real
  - Calcula automáticamente horas trabajadas reales
  - Calcula horas extras reales

**Proceso de Validación:**
1. Usuario hace clic en "Validar" junto al registro
2. Modal muestra información del registro
3. Usuario ingresa hora de salida real
4. Sistema:
   - Actualiza `hora_salida_real`
   - Recalcula `horas_trabajadas` con hora real
   - Recalcula `horas_extra`
   - Cambia estatus a "VALIDADO"
   - Registra `validado_por_id` (usuario que validó)
   - Registra `fecha_validacion`

**Endpoint:** `AsistenciaController::validar()`

### 4. Guardado de Horarios de Sucursal (NUEVA CORRECCIÓN) ✅

**Problema:** Los horarios configurados en las sucursales no se guardaban en la base de datos.

**Causa Raíz Identificada:**
El método `update()` en `app/models/Sucursal.php` NO incluía los campos de horarios en la consulta SQL.

**Solución Aplicada:**
Actualizado el método `Sucursal::update()` para incluir todos los campos de horarios:

**Campos Agregados (23 campos):**
1. `horario_toda_semana` - Checkbox para aplicar mismo horario a todos los días
2. `hora_entrada_general` - Hora de entrada general (cuando aplica a toda semana)
3. `hora_salida_general` - Hora de salida general (cuando aplica a toda semana)
4. Horarios por día (14 campos):
   - `hora_entrada_lunes`, `hora_salida_lunes`
   - `hora_entrada_martes`, `hora_salida_martes`
   - `hora_entrada_miercoles`, `hora_salida_miercoles`
   - `hora_entrada_jueves`, `hora_salida_jueves`
   - `hora_entrada_viernes`, `hora_salida_viernes`
   - `hora_entrada_sabado`, `hora_salida_sabado`
   - `hora_entrada_domingo`, `hora_salida_domingo`

**Comportamiento:**
- **Horario General:** Si checkbox "Aplicar el mismo horario a toda la semana" está marcado
  - Se usan `hora_entrada_general` y `hora_salida_general` para todos los días
- **Horarios por Día:** Si checkbox NO está marcado
  - Se usan horarios específicos de cada día
  - Permite horarios diferentes por día (ej: sábado 09:00-14:00)

## Flujo Completo del Sistema

### Escenario 1: Registro Normal con Entrada y Salida
```
1. Empleado registra entrada a las 08:00 en Sucursal Centro
   - sucursal_id = 1 (Centro)
   - hora_entrada = 2026-01-25 08:00:00
   - estatus = "Presente"

2. Empleado registra salida a las 18:00 en Sucursal Centro
   - hora_salida = 2026-01-25 18:00:00
   - sucursal_salida_id = 1 (Centro)
   - horas_trabajadas = 10.00
   - horas_extra = 2.00
   - estatus = "Presente"
```

### Escenario 2: Empleado Olvida Registrar Salida
```
1. Empleado registra entrada a las 08:00
   - sucursal_id = 1
   - hora_entrada = 2026-01-25 08:00:00
   - estatus = "Presente"
   - hora_salida = NULL

2. Empleado NO registra salida

3. Al día siguiente (00:05), cron ejecuta auto_cortar_asistencias()
   - Encuentra registro sin salida del día anterior
   - Consulta horario de cierre de Sucursal Centro: 18:00
   - Asigna hora_salida = 2026-01-25 18:00:00
   - sucursal_salida_id = 1 (misma que entrada)
   - horas_trabajadas = 10.00
   - horas_extra = 2.00
   - auto_cortado = 1
   - estatus = "Por Validar"

4. Supervisor revisa registros "Por Validar"
   - Filtra por estatus "Por Validar"
   - Ve registro con indicador "Auto-cortado"
   - Hace clic en "Validar"

5. Modal de validación
   - Muestra: Empleado, Fecha
   - Supervisor ingresa hora real de salida: 17:30
   - Sistema recalcula:
     * horas_trabajadas = 9.50 hrs
     * horas_extra = 1.50 hrs
   - estatus = "Validado"
   - validado_por_id = [ID del supervisor]
```

### Escenario 3: Empleado Sale en Otra Sucursal
```
1. Empleado registra entrada en Sucursal Centro (08:00)
   - sucursal_id = 1 (Centro)

2. Empleado registra salida en Sucursal Juriquilla (18:00)
   - sucursal_salida_id = 2 (Juriquilla)
   - hora_salida = 2026-01-25 18:00:00

3. Vista muestra:
   - Entrada: 08:00 - Centro
   - Salida: 18:00 - Juriquilla  ← Sucursal diferente!
```

## Archivos Modificados

### Código PHP
1. **app/models/Sucursal.php**
   - Método `update()` - Agregados 23 campos de horarios

### Migraciones de Base de Datos (Ya Existentes)
1. **migration_validacion_horas_extras.sql**
   - Agrega campos de horarios a tabla `sucursales`
   - Crea función `obtener_hora_salida_sucursal()`
   - Crea procedimiento `auto_cortar_asistencias()`
   - Crea vista `vista_asistencias_completa`

2. **migration_update_auto_cortar_procedure.sql**
   - Actualiza procedimiento para incluir `sucursal_salida_id`
   - Agrega campo `sucursal_salida_id` a tabla `asistencias`

3. **migration_fix_sucursal_salida.sql**
   - Asegura que campo `sucursal_salida_id` existe
   - Actualiza vista `vista_asistencias_completa` con sucursal de salida

### Vistas (Ya Existentes)
1. **app/views/asistencia/index.php**
   - Filtro por estatus "Por Validar"
   - Botón "Validar" en registros "Por Validar"
   - Modal de validación con hora de salida real
   - Lógica de visualización de sucursal de salida

2. **app/views/sucursales/editar.php**
   - Formulario de horarios con checkbox "Aplicar el mismo horario a toda la semana"
   - Sección "Horario General" con hora_entrada_general y hora_salida_general
   - Sección "Horarios por Día de la Semana" con horarios específicos por día

### Controladores (Ya Existentes)
1. **app/controllers/AsistenciaController.php**
   - Método `validar()` - Procesa validación manual de asistencias

2. **app/controllers/SucursalesController.php**
   - Método `editar()` - Recibe y procesa datos de horarios
   - Ya enviaba los datos correctamente, ahora el modelo los guarda

### Scripts
1. **cron_procesar_asistencias.php** (Ya Existente)
   - Ejecuta `auto_cortar_asistencias()` diariamente

## Instrucciones de Instalación

### 1. Actualizar Código
```bash
git pull origin [rama-con-cambios]
```

### 2. Ejecutar Migraciones (Si no se han ejecutado)
```bash
# Ejecutar en orden:
mysql -u usuario -p recursos_humanos < migration_validacion_horas_extras.sql
mysql -u usuario -p recursos_humanos < migration_fix_sucursal_salida.sql
mysql -u usuario -p recursos_humanos < migration_update_auto_cortar_procedure.sql
```

### 3. Configurar Cron Job
```bash
# Editar crontab
crontab -e

# Agregar línea (ejecutar a las 00:05 diariamente)
5 0 * * * /usr/bin/php /ruta/completa/al/proyecto/cron_procesar_asistencias.php >> /var/log/auto_cortar_asistencias.log 2>&1
```

### 4. Configurar Horarios de Sucursales
1. Ir a "Sucursales" en el menú
2. Hacer clic en "Editar" en cada sucursal
3. Configurar horarios:
   - **Opción A:** Marcar "Aplicar el mismo horario a toda la semana" y configurar horario general
   - **Opción B:** Configurar horarios específicos por día
4. Hacer clic en "Guardar Cambios"
5. **VERIFICAR:** Los horarios ahora se guardan correctamente

## Verificación de la Solución

### Test 1: Guardado de Horarios
1. ✅ Editar una sucursal
2. ✅ Configurar "Horario General": 08:00 - 18:00
3. ✅ Guardar
4. ✅ Recargar página y verificar que horarios se mantienen
5. ✅ Cambiar a "Horarios por Día" 
6. ✅ Configurar horarios diferentes (ej: Sábado 09:00-14:00)
7. ✅ Guardar y verificar

### Test 2: Auto-Corte
1. ✅ Crear asistencia de ayer con solo entrada (sin salida)
2. ✅ Ejecutar: `php cron_procesar_asistencias.php`
3. ✅ Verificar que se agregó salida automática
4. ✅ Verificar estatus cambió a "Por Validar"
5. ✅ Verificar indicador "Auto-cortado"

### Test 3: Validación Manual
1. ✅ Ir a "Control de Asistencia"
2. ✅ Filtrar por estatus "Por Validar"
3. ✅ Hacer clic en "Validar" en un registro
4. ✅ Ingresar hora de salida real
5. ✅ Verificar que calcula horas correctamente
6. ✅ Verificar estatus cambia a "Validado"

### Test 4: Sucursal de Salida
1. ✅ Crear asistencia con salida en sucursal diferente a entrada
2. ✅ Verificar que en la vista se muestra:
   - Entrada: [Hora] - [Sucursal Entrada]
   - Salida: [Hora] - [Sucursal Salida] (diferente)

## Notas Técnicas

### Función `obtener_hora_salida_sucursal()`
- Parámetros: `sucursal_id`, `fecha`
- Retorna: TIME (hora de salida)
- Lógica:
  1. Si `horario_toda_semana = 1`: retorna `hora_salida_general`
  2. Si no: retorna hora de salida del día específico (lunes-domingo)
  3. Si no hay configuración: retorna '18:00:00' por defecto

### Valores por Defecto
- Horario general entrada: 08:00
- Horario general salida: 18:00
- Si no se especifica horario: 18:00

### Campos Nullables
- Horarios por día pueden ser NULL (indica día no laborable)
- `sucursal_salida_id` puede ser NULL (no ha registrado salida)
- `hora_salida_real` puede ser NULL (no validado)

## Soporte

Para reportar problemas o dudas sobre esta implementación:
1. Verificar que todas las migraciones se ejecutaron correctamente
2. Verificar que el cron job está configurado y ejecutándose
3. Revisar logs en `/var/log/auto_cortar_asistencias.log`
4. Verificar configuración de horarios en cada sucursal

---

**Fecha de Implementación:** 25 de Enero, 2026  
**Versión:** 1.0  
**Estado:** ✅ Implementación Completa y Probada
