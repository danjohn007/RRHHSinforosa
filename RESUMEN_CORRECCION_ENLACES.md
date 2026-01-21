# RESUMEN DE CORRECCIÓN - ENLACES ROTOS EN DASHBOARD

## Problema Identificado

Se detectaron enlaces rotos en las tarjetas estadísticas del dashboard que causaban errores 404:

1. **Nómina Acumulada desde Último Corte**
2. **Horas Extras Acumuladas desde Último Corte**  
3. **Costo de Horas Extras Acumuladas**

## Causa del Problema

Los enlaces estaban usando rutas absolutas (`/nomina/procesamiento`, `/asistencia/incidencias`) en lugar de la constante `BASE_URL` que utiliza el resto de la aplicación.

Además, el primer enlace apuntaba a una ruta incorrecta:
- ❌ `/nomina/procesamiento` (archivo no existe)
- ✅ `nomina/procesar` (archivo correcto: `procesar.php`)

## Solución Implementada

### 1. Corrección de Enlaces en Dashboard

**Archivo modificado:** `app/views/dashboard/index.php`

#### Cambio 1: Tarjeta "Nómina Acumulada" (línea 73)
```php
<!-- ANTES -->
<a href="/nomina/procesamiento" class="block...">

<!-- DESPUÉS -->
<a href="<?php echo BASE_URL; ?>nomina/procesar" class="block...">
```

#### Cambio 2: Tarjeta "Horas Extras Acumuladas" (línea 98)
```php
<!-- ANTES -->
<a href="/asistencia/incidencias?tipo=Hora Extra" class="block...">

<!-- DESPUÉS -->
<a href="<?php echo BASE_URL; ?>asistencia/incidencias?tipo=Hora%20Extra" class="block...">
```

#### Cambio 3: Tarjeta "Costo de Horas Extras" (línea 123)
```php
<!-- ANTES -->
<a href="/asistencia/incidencias?tipo=Hora Extra" class="block...">

<!-- DESPUÉS -->
<a href="<?php echo BASE_URL; ?>asistencia/incidencias?tipo=Hora%20Extra" class="block...">
```

### 2. Archivo SQL con Datos de Ejemplo

**Archivo creado:** `sample_data_horas_extras.sql`

Este archivo incluye:

- ✅ **25+ registros de horas extras** para diferentes empleados
- ✅ **Fechas variadas** en el periodo actual (últimos 14 días)
- ✅ **Cálculo automático** del costo de horas extras (1.5x salario regular)
- ✅ **Diferentes estados**: Aprobado y Procesado
- ✅ **Descripciones realistas** de las actividades
- ✅ **Consultas de verificación** al final del script

#### Contenido del archivo:

1. **Creación de periodo activo** (si no existe)
2. **10 empleados con registros detallados** (2 registros cada uno)
3. **15+ empleados adicionales** con datos aleatorios
4. **Consultas de verificación** para validar los datos insertados

#### Rangos de datos:

- **Horas por registro:** 1 - 6 horas
- **Multiplicador:** 1.5x (tiempo y medio)
- **Periodo:** Últimos 14 días
- **Estados:** Aprobado (30%) / Procesado (70%)

## Cómo Usar el Archivo SQL

### Opción 1: Línea de comandos
```bash
mysql -u usuario -p nombre_base_datos < sample_data_horas_extras.sql
```

### Opción 2: phpMyAdmin
1. Acceder a phpMyAdmin
2. Seleccionar la base de datos
3. Ir a la pestaña "SQL"
4. Copiar y pegar el contenido del archivo
5. Ejecutar

### Opción 3: Consola MySQL
```sql
USE nombre_base_datos;
SOURCE /ruta/completa/sample_data_horas_extras.sql;
```

## Verificación de la Corrección

Después de aplicar los cambios, verificar que:

1. ✅ La tarjeta "Nómina Acumulada" ahora redirige a `/nomina/procesar`
2. ✅ Las tarjetas de "Horas Extras" redirigen a `/asistencia/incidencias?tipo=Hora%20Extra`
3. ✅ El parámetro de búsqueda filtra correctamente por "Hora Extra"
4. ✅ El dashboard muestra estadísticas actualizadas con los datos de ejemplo

## Consultas Útiles

### Ver todas las horas extras del periodo actual
```sql
SELECT 
    e.numero_empleado,
    CONCAT(e.nombres, ' ', e.apellido_paterno) as nombre,
    i.fecha_incidencia,
    i.cantidad as horas,
    i.monto as costo,
    i.descripcion,
    i.estatus
FROM incidencias_nomina i
INNER JOIN empleados e ON i.empleado_id = e.id
WHERE i.tipo_incidencia = 'Hora Extra'
AND i.periodo_id = (SELECT id FROM periodos_nomina WHERE estatus = 'Activo' LIMIT 1)
ORDER BY i.fecha_incidencia DESC;
```

### Resumen por empleado
```sql
SELECT 
    e.numero_empleado,
    CONCAT(e.nombres, ' ', e.apellido_paterno) as nombre,
    COUNT(i.id) as registros,
    SUM(i.cantidad) as total_horas,
    SUM(i.monto) as costo_total
FROM incidencias_nomina i
INNER JOIN empleados e ON i.empleado_id = e.id
WHERE i.tipo_incidencia = 'Hora Extra'
AND i.periodo_id = (SELECT id FROM periodos_nomina WHERE estatus = 'Activo' LIMIT 1)
GROUP BY e.id
ORDER BY total_horas DESC;
```

### Estadísticas generales
```sql
SELECT 
    COUNT(*) as total_registros,
    COUNT(DISTINCT empleado_id) as empleados_con_horas_extras,
    SUM(cantidad) as total_horas,
    SUM(monto) as costo_total,
    AVG(cantidad) as promedio_horas,
    AVG(monto) as promedio_costo
FROM incidencias_nomina
WHERE tipo_incidencia = 'Hora Extra'
AND periodo_id = (SELECT id FROM periodos_nomina WHERE estatus = 'Activo' LIMIT 1);
```

## Cambios Técnicos Detallados

### BASE_URL vs Rutas Absolutas

La constante `BASE_URL` se define en `config/config.php`:

```php
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $script = $_SERVER['SCRIPT_NAME'];
    $path = str_replace(basename($script), '', $script);
    return $protocol . '://' . $host . $path;
}

define('BASE_URL', getBaseUrl());
```

**Ventajas de usar BASE_URL:**
- ✅ Funciona en cualquier entorno (desarrollo, producción)
- ✅ Soporta subdirectorios
- ✅ Detecta automáticamente HTTP/HTTPS
- ✅ Consistente con el resto de la aplicación

### URL Encoding en Query Parameters

El espacio en "Hora Extra" se codifica como `%20`:
- ❌ `?tipo=Hora Extra` → Puede causar problemas
- ✅ `?tipo=Hora%20Extra` → Codificación correcta

## Impacto de los Cambios

### Archivos Modificados
- `app/views/dashboard/index.php` (3 líneas modificadas)

### Archivos Creados
- `sample_data_horas_extras.sql` (400+ líneas)

### Funcionalidad Afectada
- ✅ Dashboard: Enlaces ahora funcionan correctamente
- ✅ Navegación: Usuarios pueden acceder a reportes de horas extras
- ✅ Datos: Sistema tiene datos de prueba para visualización

## Notas Importantes

1. **No se modificó la lógica de negocio** - Solo se corrigieron enlaces
2. **Compatibilidad total** - Los cambios son retrocompatibles
3. **Sin impacto en base de datos** - El SQL es opcional para datos de prueba
4. **Minimal changes** - Solo 3 líneas de código modificadas en producción

## Testing Realizado

- ✅ Verificación de sintaxis PHP
- ✅ Code review automatizado (sin issues)
- ✅ Verificación de rutas absolutas restantes (ninguna encontrada)
- ✅ Validación de estructura SQL

## Próximos Pasos

1. Ejecutar el archivo SQL en el entorno de desarrollo
2. Verificar que los enlaces funcionen correctamente
3. Probar la navegación entre dashboard y módulos
4. Validar que las estadísticas se muestren correctamente

---

**Fecha de corrección:** 2026-01-21  
**Archivos modificados:** 1  
**Archivos creados:** 1  
**Líneas de código modificadas:** 3  
**Impacto:** Mínimo - Solo corrección de enlaces
