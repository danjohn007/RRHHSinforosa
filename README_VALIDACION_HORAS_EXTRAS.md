# Validación de Horas Extras - Documentación

Este documento describe las nuevas funcionalidades implementadas para la validación automática de horas extras y el control mejorado de asistencias.

## 📋 Tabla de Contenidos

1. [Cambios en Base de Datos](#cambios-en-base-de-datos)
2. [Horarios de Sucursales](#horarios-de-sucursales)
3. [Auto-Corte de Asistencias](#auto-corte-de-asistencias)
4. [Validación de Asistencias](#validación-de-asistencias)
5. [Mejoras en Interfaz](#mejoras-en-interfaz)
6. [Instalación](#instalación)
7. [Uso](#uso)

## 🗄️ Cambios en Base de Datos

### Nueva Migración SQL

Ejecutar el archivo `migration_validacion_horas_extras.sql` que incluye:

#### Tabla `sucursales` - Nuevos campos:
- `horario_toda_semana` - Flag para aplicar mismo horario a todos los días
- `hora_entrada_general` / `hora_salida_general` - Horario general
- `hora_entrada_lunes` hasta `hora_entrada_domingo` - Horarios por día
- `hora_salida_lunes` hasta `hora_salida_domingo` - Horarios por día

#### Tabla `asistencias` - Nuevos campos:
- `estatus` - Ahora incluye: 'Por Validar' y 'Validado'
- `hora_salida_real` - Hora de salida real cuando se valida manualmente
- `auto_cortado` - Flag que indica si la salida fue auto-cortada
- `validado_por_id` - Usuario que validó la asistencia
- `fecha_validacion` - Fecha y hora de validación

#### Nuevas Funciones:
- `obtener_hora_salida_sucursal(p_sucursal_id, p_fecha)` - Obtiene horario de salida por día
- `obtener_hora_entrada_sucursal(p_sucursal_id, p_fecha)` - Obtiene horario de entrada por día

#### Nuevos Procedimientos:
- `auto_cortar_asistencias()` - Procesa automáticamente asistencias sin salida

#### Nueva Vista:
- `vista_asistencias_completa` - Vista con información completa de asistencias, empleados y sucursales

## 🏢 Horarios de Sucursales

### Configuración

1. Ir a **Sucursales** → Seleccionar sucursal → **Editar**
2. En la sección **"Horarios de la Sucursal"**:

#### Opción 1: Horario único para toda la semana
- ✅ Marcar "Aplicar el mismo horario a toda la semana"
- Configurar hora de entrada y salida general
- Ejemplo: 08:00 - 18:00 todos los días

#### Opción 2: Horarios específicos por día
- ⬜ Desmarcar "Aplicar el mismo horario a toda la semana"
- Configurar horario individual para cada día de la semana
- Para días cerrados (ej. Domingo), dejar los campos vacíos

### Ejemplo de Configuración:

**Sucursal Centro** - Horario toda la semana:
- Lunes a Domingo: 08:00 - 18:00

**Sucursal Juriquilla** - Horario por día:
- Lunes a Viernes: 08:00 - 18:00
- Sábado: 09:00 - 14:00
- Domingo: Cerrado (campos vacíos)

## ⏰ Auto-Corte de Asistencias

### ¿Qué hace?

El sistema automáticamente "corta" las asistencias donde:
- El empleado registró entrada pero NO registró salida
- La fecha es anterior al día actual

### ¿Cómo funciona?

1. **Detección**: Identifica asistencias sin salida de días anteriores
2. **Cálculo**: 
   - Obtiene el horario de salida de la sucursal para ese día de la semana
   - Asigna esa hora como salida automática
3. **Horas trabajadas**: Calcula las horas entre entrada y salida
4. **Horas extras**: Calcula automáticamente si trabajó más de 8 horas
5. **Cambio de estatus**: Cambia de "Presente" a **"Por Validar"**

### Configuración del CRON Job

Para ejecutar el auto-corte automáticamente cada día:

```bash
# Editar crontab
crontab -e

# Agregar línea para ejecutar a las 00:05 (5 minutos después de medianoche)
5 0 * * * cd /ruta/al/proyecto && /usr/bin/php cron_procesar_asistencias.php >> /var/log/asistencias_cron.log 2>&1
```

#### Ejecución manual:
```bash
php cron_procesar_asistencias.php
```

## ✅ Validación de Asistencias

### Proceso de Validación

1. **Identificar asistencias por validar**:
   - En **Control de Asistencia**, filtrar por estatus: **"Por Validar"**
   - Estas son asistencias que fueron auto-cortadas

2. **Validar una asistencia**:
   - Click en el botón **"Validar"** junto al estatus
   - Se abrirá un modal con la información del empleado
   - **Ingresar la hora de salida real** del empleado
   - Click en **"Validar"**

3. **Resultado**:
   - El sistema recalcula las horas trabajadas y horas extras
   - Cambia el estatus a **"Validado"**
   - Muestra un resumen con las horas finales

### Estados de Asistencia

- **Presente**: Asistencia normal con entrada y salida registradas
- **Por Validar**: Asistencia auto-cortada que requiere confirmación
- **Validado**: Asistencia confirmada manualmente después del auto-corte
- **Retardo**: Llegada tarde
- **Falta**: Ausencia
- **Permiso**: Ausencia justificada
- **Vacaciones**: Vacaciones autorizadas
- **Incapacidad**: Incapacidad médica

## 🎨 Mejoras en Interfaz

### Control de Asistencia

#### Nuevos Filtros:
- **Estatus**: Filtrar por cualquier estado (especialmente útil para "Por Validar")
- Combinación de filtros: fecha + empleado + estatus

#### Tabla Mejorada:
- **Nombre del Empleado**: Ahora es un link a su perfil (/empleados/ver?id=x)
- **Sucursal**: Se muestra junto a las horas de entrada/salida
- **Horas trabajadas**: 
  - En **naranja** si tiene horas extras (>8 hrs)
  - Muestra detalle de horas extras debajo
- **Fotos**: Nueva columna con links a fotos de entrada/salida
- **Auto-cortado**: Indicador visual cuando la salida fue automática
- **Botón Validar**: Aparece para asistencias con estatus "Por Validar"

### Editar Sucursal

#### Nueva Sección: Horarios
- Toggle para horario único vs. horarios por día
- Interfaz intuitiva con campos de tiempo (HH:MM)
- Indicadores visuales por día de la semana
- Soporte para días cerrados

## 📥 Instalación

### 1. Ejecutar Migración SQL

```bash
mysql -u usuario -p nombre_base_datos < migration_validacion_horas_extras.sql
```

O desde phpMyAdmin:
1. Importar → Seleccionar `migration_validacion_horas_extras.sql`
2. Ejecutar

### 2. Verificar instalación

```sql
-- Verificar nuevos campos en sucursales
DESCRIBE sucursales;

-- Verificar nuevos campos en asistencias
DESCRIBE asistencias;

-- Verificar vista
DESCRIBE vista_asistencias_completa;

-- Verificar funciones
SHOW FUNCTION STATUS WHERE Name LIKE 'obtener_hora%';

-- Verificar procedimiento
SHOW PROCEDURE STATUS WHERE Name = 'auto_cortar_asistencias';
```

### 3. Configurar horarios iniciales

1. Ir a cada sucursal
2. Editar y configurar horarios
3. Guardar cambios

### 4. Configurar CRON (opcional pero recomendado)

Ver sección [Auto-Corte de Asistencias](#auto-corte-de-asistencias)

## 📖 Uso

### Flujo de Trabajo Diario

1. **Durante el día**:
   - Empleados registran entrada normalmente
   - Empleados registran salida normalmente

2. **Fin del día**:
   - Sistema identifica asistencias sin salida
   
3. **Noche (automático con CRON)**:
   - A las 00:05, ejecuta auto-corte
   - Asigna hora de salida según horario de sucursal
   - Calcula horas trabajadas y extras
   - Cambia estatus a "Por Validar"

4. **Día siguiente**:
   - Supervisor/RRHH revisa asistencias "Por Validar"
   - Valida con hora real de salida
   - Sistema recalcula y marca como "Validado"

### Casos de Uso

#### Caso 1: Empleado olvidó registrar salida
```
Entrada: 08:30
Salida: (no registrada)
Horario sucursal: 08:00 - 18:00

Auto-corte:
- Hora salida: 18:00 (del horario)
- Horas trabajadas: 9.5 hrs
- Horas extra: 1.5 hrs
- Estatus: Por Validar

Validación manual:
- Hora real: 19:30
- Horas trabajadas: 11 hrs
- Horas extra: 3 hrs
- Estatus: Validado
```

#### Caso 2: Diferentes horarios por día
```
Sucursal con horarios variables:
- Lunes-Viernes: 08:00 - 18:00
- Sábado: 09:00 - 14:00
- Domingo: Cerrado

Un sábado, empleado registra entrada 09:15:
- Auto-corte usará: 14:00 (horario de sábado)
- Horas trabajadas: 4.75 hrs
- Horas extra: 0 hrs (no supera 8)
```

## 🔧 Troubleshooting

### La migración falla con error de sintaxis
- Verificar que la base de datos sea MySQL 5.7+ o MariaDB 10.2+
- Ejecutar línea por línea si hay problemas

### El auto-corte no funciona
- Verificar que el procedimiento existe: `SHOW PROCEDURE STATUS LIKE 'auto_cortar%'`
- Ejecutar manualmente: `CALL auto_cortar_asistencias();`
- Revisar logs del CRON job

### No aparece el botón "Validar"
- Verificar que el estatus sea "Por Validar"
- Limpiar caché del navegador
- Verificar permisos de usuario

### Las horas extras no se calculan correctamente
- Verificar horarios de sucursal configurados
- Las horas extras se calculan sobre 8 horas, no sobre el horario de la sucursal
- Ejemplo: Si trabaja 10 horas, tiene 2 horas extra (independiente del horario configurado)

## 📝 Notas Importantes

1. **Horarios de sucursal**: Son para determinar la hora de auto-corte, NO para calcular horas extra
2. **Horas extra**: SIEMPRE se calculan sobre jornada de 8 horas
3. **Validación**: Es obligatoria para asistencias auto-cortadas
4. **CRON**: Importante configurarlo para automatización completa
5. **Permisos**: Solo usuarios con rol admin/rrhh pueden validar asistencias

## 🆘 Soporte

Para problemas o dudas:
1. Revisar este documento
2. Verificar logs del sistema
3. Contactar al equipo de desarrollo

---

**Versión**: 1.0.0  
**Fecha**: 25 de Enero de 2026  
**Sistema**: RRHH Sinforosa
