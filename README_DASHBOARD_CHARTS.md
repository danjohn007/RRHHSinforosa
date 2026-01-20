# Actualización Dashboard - Nuevas Gráficas y Cálculo de Nómina

## Descripción de Cambios

Se han agregado **4 nuevas gráficas** y un **widget de cálculo de nómina acumulada** al Dashboard del sistema de Recursos Humanos Sinforosa.

### Nuevas Funcionalidades

#### 1. Widget de Nómina Acumulada
- **Ubicación**: Parte superior del dashboard, antes de las gráficas
- **Funcionalidad**: Muestra el total de nómina acumulada desde el último periodo cerrado
- **Cálculo**: Suma de `total_neto` de todos los periodos con estatus 'Procesado' o 'Pagado' desde el último periodo con estatus 'Cerrado'
- **Diseño**: Card con degradado verde, icono de calculadora, formato monetario

#### 2. Gráfica de Distribución por Género
- **Tipo**: Gráfica de dona (doughnut)
- **Datos**: Empleados activos agrupados por género (Masculino, Femenino, Otro)
- **Colores**: Azul (#3b82f6), Rosa (#ec4899), Púrpura (#8b5cf6)
- **Tooltip**: Muestra número y porcentaje de empleados por género

#### 3. Gráfica de Contrataciones Mensuales
- **Tipo**: Gráfica de línea
- **Datos**: Nuevas contrataciones por mes (últimos 6 meses)
- **Período**: Últimos 6 meses desde la fecha actual
- **Color**: Índigo (#6366f1)
- **Diseño**: Línea suave con relleno, puntos destacados

#### 4. Gráfica de Resumen de Asistencias
- **Tipo**: Gráfica de barras
- **Datos**: Incidencias del último mes agrupadas por estatus
- **Categorías**: Presente, Retardo, Falta, Permiso, Vacaciones, Incapacidad
- **Colores**: Código de colores por tipo de incidencia
  - Presente: Verde (#10b981)
  - Retardo: Naranja (#f59e0b)
  - Falta: Rojo (#ef4444)
  - Permiso: Azul (#3b82f6)
  - Vacaciones: Púrpura (#8b5cf6)
  - Incapacidad: Rosa (#ec4899)

#### 5. Gráfica de Distribución Salarial
- **Tipo**: Gráfica de barras
- **Datos**: Empleados activos agrupados por rango salarial
- **Rangos**:
  - Menos de $5,000
  - $5,000 - $10,000
  - $10,000 - $15,000
  - $15,000 - $20,000
  - Más de $20,000
- **Color**: Verde (#10b981)

## Archivos Modificados

### 1. `/app/controllers/DashboardController.php`
**Cambios realizados:**
- Agregadas 5 nuevas consultas SQL para obtener datos de las gráficas
- Cálculo de nómina acumulada desde el último corte
- Obtención de distribución por género
- Obtención de contrataciones mensuales (últimos 6 meses)
- Obtención de resumen de asistencias (último mes)
- Obtención de distribución salarial por rangos
- Todos los nuevos datos se pasan a la vista en el array `$data`

**Consultas SQL agregadas:**
```php
// Nómina acumulada
SELECT COALESCE(SUM(total_neto), 0) as total_acumulado
FROM periodos_nomina
WHERE estatus IN ('Procesado', 'Pagado')
AND fecha_inicio >= (
    SELECT COALESCE(MAX(fecha_fin), DATE_SUB(NOW(), INTERVAL 3 MONTH))
    FROM periodos_nomina
    WHERE estatus = 'Cerrado'
)

// Distribución por género
SELECT genero, COUNT(*) as total
FROM empleados
WHERE estatus = 'Activo'
GROUP BY genero

// Contrataciones mensuales
SELECT 
    DATE_FORMAT(fecha_ingreso, '%Y-%m') as mes,
    DATE_FORMAT(fecha_ingreso, '%b') as mes_nombre,
    COUNT(*) as total
FROM empleados
WHERE fecha_ingreso >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
GROUP BY mes, mes_nombre
ORDER BY mes ASC

// Resumen de asistencias
SELECT estatus, COUNT(*) as total
FROM asistencias
WHERE fecha >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY estatus

// Distribución salarial
SELECT 
    CASE
        WHEN salario_mensual < 5000 THEN 'Menos de $5,000'
        WHEN salario_mensual >= 5000 AND salario_mensual < 10000 THEN '$5,000 - $10,000'
        WHEN salario_mensual >= 10000 AND salario_mensual < 15000 THEN '$10,000 - $15,000'
        WHEN salario_mensual >= 15000 AND salario_mensual < 20000 THEN '$15,000 - $20,000'
        ELSE 'Más de $20,000'
    END as rango,
    COUNT(*) as total
FROM empleados
WHERE estatus = 'Activo' AND salario_mensual > 0
GROUP BY rango
```

### 2. `/app/views/dashboard/index.php`
**Cambios realizados:**
- Agregado widget de nómina acumulada antes de las gráficas existentes
- Agregadas 4 nuevas gráficas organizadas en 2 filas de 2 columnas
- Código JavaScript para renderizar las nuevas gráficas con Chart.js
- Todas las gráficas tienen diseño consistente con las existentes
- Manejo de casos sin datos (placeholders)

**Estructura HTML:**
```html
<!-- Widget de Nómina Acumulada -->
<div class="bg-gradient-to-r from-green-500 to-emerald-600">
    <!-- Contenido del widget -->
</div>

<!-- Fila de gráficas nuevas 1 -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Distribución por Género -->
    <!-- Contrataciones Mensuales -->
</div>

<!-- Fila de gráficas nuevas 2 -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Resumen de Asistencias -->
    <!-- Distribución Salarial -->
</div>
```

### 3. `/sample_data_dashboard.sql` (NUEVO)
**Contenido:**
- Datos de ejemplo completos para probar las nuevas funcionalidades
- 14 empleados con diferentes características:
  - Variedad de géneros (M, F)
  - Diferentes fechas de ingreso (últimos 12 meses)
  - Diferentes departamentos y puestos
  - Diferentes rangos salariales
- 6 departamentos de ejemplo
- 9 puestos de ejemplo
- 6 periodos de nómina (1 cerrado, 4 procesados/pagados, 1 en proceso)
- Asistencias del último mes con variedad de estatus:
  - Presente
  - Retardo
  - Falta
  - Permiso
  - Vacaciones
  - Incapacidad
- 2 solicitudes de vacaciones pendientes
- 3 candidatos en proceso
- Conceptos de nómina necesarios

## Instalación y Pruebas

### 1. Aplicar los Cambios
Los cambios ya están aplicados en los archivos del sistema. No se requiere instalación adicional.

### 2. Cargar Datos de Ejemplo (Opcional)
Para probar las nuevas funcionalidades con datos de muestra:

```bash
mysql -u recursos_humanos -p recursos_humanos < sample_data_dashboard.sql
```

### 3. Verificar el Dashboard
1. Iniciar sesión en el sistema
2. Navegar a `/dashboard`
3. Verificar que se muestren:
   - Widget de nómina acumulada en la parte superior
   - 6 gráficas en total (2 existentes + 4 nuevas)
   - Todas las gráficas deben renderizarse correctamente
   - Los datos deben mostrarse sin errores

### 4. Verificación de Funcionalidad Existente
- [x] Las 4 tarjetas superiores funcionan correctamente
- [x] La gráfica de "Distribución por Departamento" funciona
- [x] La gráfica de "Asistencia Semanal" funciona
- [x] La sección de "Cumpleaños del Mes" funciona
- [x] La sección de "Accesos Rápidos" funciona

## Compatibilidad

- **PHP**: 7.4+
- **MySQL**: 5.7+
- **Chart.js**: 3.x (ya incluido en el sistema)
- **Tailwind CSS**: (ya incluido en el sistema)
- **Font Awesome**: (ya incluido en el sistema)

## Notas Técnicas

1. **Rendimiento**: Todas las consultas están optimizadas y utilizan índices existentes
2. **Manejo de Datos Vacíos**: Las gráficas manejan correctamente casos sin datos
3. **Responsive Design**: Todas las gráficas son responsivas y se adaptan a diferentes tamaños de pantalla
4. **Seguridad**: Todas las consultas usan preparación de statements para prevenir SQL injection
5. **Logging**: Se mantiene el logging de Chart.js en consola para debugging

## Mantenimiento

### Modificar Rangos Salariales
Para cambiar los rangos de la distribución salarial, editar en `DashboardController.php`:
```php
CASE
    WHEN salario_mensual < 5000 THEN 'Menos de $5,000'
    // Modificar aquí los rangos según necesidades
END as rango
```

### Cambiar Período de Contrataciones
Para ver más o menos meses de contrataciones, modificar en `DashboardController.php`:
```php
WHERE fecha_ingreso >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
// Cambiar el número de meses según necesidad
```

### Cambiar Período de Asistencias
Para ver más o menos días de asistencias, modificar en `DashboardController.php`:
```php
WHERE fecha >= DATE_SUB(NOW(), INTERVAL 30 DAY)
// Cambiar el número de días según necesidad
```

## Soporte

Para cualquier duda o problema con las nuevas funcionalidades, contactar al equipo de desarrollo.

## Versión

**Versión**: 1.1.0
**Fecha**: Enero 2026
**Autor**: Sistema RRHH Sinforosa
