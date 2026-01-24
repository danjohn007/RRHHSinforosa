# Implementación del Módulo de Timbrado de Nómina

## Fecha: 2026-01-24

---

## Resumen de Cambios

Este documento detalla las actualizaciones realizadas al sistema RRHH Sinforosa para agregar el módulo de "Timbrado de Nómina" (CFDI) y resolver problemas identificados.

---

## 1. Módulo de Timbrado de Nómina

### Descripción
Se agregó un nuevo módulo en la sección de **Configuraciones Globales** que permite configurar el timbrado de comprobantes fiscales digitales (CFDI) de nómina.

### Características Implementadas

#### 1.1 Nueva Pestaña en Configuraciones Globales
- **Ubicación**: Configuraciones > Timbrado de Nómina
- **Icono**: `fa-file-invoice`
- **Acceso**: Solo usuarios con rol `admin`

#### 1.2 Campos de Configuración

**Datos del Emisor:**
- RFC del Emisor (requerido)
- Razón Social / Nombre (requerido)

**E.firma (Certificado Digital):**
- Archivo de Certificado (.cer) - hasta 5MB
- Archivo de Llave Privada (.key) - hasta 5MB
- Contraseña de Llave Privada

**Configuración de API:**
- URL de API de Timbrado
- Usuario de API
- Contraseña de API
- Token de Autenticación

**Configuración de Cancelación:**
- URL de API de Cancelación

**Modo de Operación:**
- Ambiente: Pruebas (Sandbox) / Producción

#### 1.3 Códigos de Error del PAC
Se incluye una tabla de referencia con los códigos de error estándar:

| Clave | Descripción |
|-------|-------------|
| 01 | Comprobante emitido con errores con relación |
| 02 | Comprobante emitido con errores sin relación |
| 03 | No se llevó a cabo la operación |
| 04 | Operación nominativa relacionada en una factura global |

### Archivos Modificados

1. **app/controllers/ConfiguracionesController.php**
   - Se agregó el método `subirEfirma()` para manejar la carga de archivos .cer y .key
   - Se actualizó el método `guardar()` para procesar los archivos de e.firma
   - Validaciones: tipo de archivo (.cer, .key), tamaño máximo (5MB)

2. **app/views/configuraciones/index.php**
   - Se agregó la pestaña "Timbrado de Nómina"
   - Se creó el formulario completo con todos los campos necesarios
   - Se incluyó indicador visual de archivos ya subidos
   - Se agregó tabla de referencia de códigos de error

3. **migration_timbrado_nomina_config.sql**
   - Script SQL para agregar las nuevas configuraciones a la base de datos
   - 11 nuevas entradas en la tabla `configuraciones_globales` con grupo='timbrado'
   - Compatible con datos existentes (usa `INSERT IGNORE`)

### Directorio de Archivos
Los archivos de e.firma se almacenan en:
```
uploads/efirma/
  ├── efirma_certificado_[timestamp].cer
  └── efirma_llave_[timestamp].key
```

---

## 2. Corrección: Descarga de Plantilla de Empleados

### Problema Identificado
El botón "Descargar plantilla de ejemplo" en la importación de empleados generaba un error de "Página no encontrada".

### Solución Implementada
Se agregó la ruta faltante en el archivo `index.php` para manejar las peticiones a:
- `empleados/descargar-plantilla`
- `empleados/descargarPlantilla`

### Archivo Modificado
**index.php** (líneas 111-114)
```php
} elseif ($parts[1] === 'descargar-plantilla' || $parts[1] === 'descargarPlantilla') {
    $controller->descargarPlantilla();
} elseif ($parts[1] === 'importar') {
    $controller->importar();
```

### Resultado
Ahora los usuarios pueden descargar correctamente la plantilla CSV con los siguientes campos:
- nombres, apellido_paterno, apellido_materno
- curp, rfc, nss
- fecha_nacimiento, genero
- email_personal, celular
- fecha_ingreso, tipo_contrato
- departamento, puesto, salario_mensual

---

## 3. Corrección: Selección de Gerentes en Sucursales

### Problema Identificado
Al asignar un gerente a una sucursal, aparecían empleados que no tenían el puesto de "Gerente General", incluyendo empleados de otros puestos.

### Solución Implementada
Se modificó la consulta SQL para filtrar únicamente empleados con el puesto exacto de "Gerente General".

### Archivo Modificado
**app/controllers/SucursalesController.php** (líneas 159-180)

**Consulta Original:**
```sql
WHERE e.estatus = 'Activo'
AND (
    u.rol IN ('gerente', 'admin', 'rrhh')
    OR e.puede_ser_gerente = 1
)
```

**Consulta Actualizada:**
```sql
WHERE e.estatus = 'Activo'
AND e.puesto = 'Gerente General'
```

### Resultado
Ahora solo se muestran empleados que tienen el puesto "Gerente General" en el dropdown de selección.

---

## 4. Script de Migración SQL

### Ubicación
`migration_timbrado_nomina_config.sql`

### Contenido
- Inserta 11 nuevas configuraciones en la tabla `configuraciones_globales`
- Todas las configuraciones pertenecen al grupo `'timbrado'`
- Usa `INSERT IGNORE` para evitar duplicados
- Incluye valores por defecto seguros (vacíos para datos sensibles)

### Ejecución
```bash
mysql -u [usuario] -p recursos_humanos < migration_timbrado_nomina_config.sql
```

---

## 5. Validaciones de Seguridad

### Validación de Archivos E.firma
1. **Tipo de archivo**: Solo se permiten .cer para certificados y .key para llaves privadas
2. **Tamaño máximo**: 5MB por archivo
3. **Almacenamiento**: Los archivos se renombran con timestamp único
4. **Ubicación**: Carpeta dedicada `uploads/efirma/` con permisos 0755

### Validación de Acceso
- Solo usuarios con rol `admin` pueden acceder a configuraciones
- Los archivos sensibles están fuera del webroot público
- Las contraseñas se almacenan en la base de datos (considerar encriptación adicional en producción)

---

## 6. Pruebas Realizadas

### Pruebas de Sintaxis PHP
✅ `index.php` - Sin errores de sintaxis
✅ `app/controllers/ConfiguracionesController.php` - Sin errores de sintaxis
✅ `app/controllers/SucursalesController.php` - Sin errores de sintaxis
✅ `app/views/configuraciones/index.php` - Sin errores de sintaxis

### Validación de SQL
✅ Script de migración validado
✅ Estructura compatible con tabla existente `configuraciones_globales`

---

## 7. Próximos Pasos Recomendados

### Después del Despliegue
1. Ejecutar el script de migración SQL en el servidor de producción
2. Crear el directorio `uploads/efirma/` con permisos adecuados
3. Probar la carga de archivos .cer y .key
4. Verificar que la descarga de plantilla funciona correctamente
5. Validar que la selección de gerentes muestra solo "Gerente General"

### Mejoras Futuras Sugeridas
1. **Seguridad**:
   - Encriptar contraseñas sensibles en la base de datos
   - Implementar logs de auditoría para cambios en configuraciones
   - Agregar verificación adicional de archivos (validar que sean certificados válidos)

2. **Funcionalidad**:
   - Integración completa con el API del PAC (Proveedor Autorizado de Certificación)
   - Implementar proceso de timbrado automático
   - Agregar historial de CFDIs generados
   - Implementar proceso de cancelación de CFDIs

3. **Interfaz**:
   - Agregar validación de formulario en frontend
   - Implementar preview de certificado cargado
   - Agregar indicadores de validación de conexión con API

---

## 8. Documentación de API Relacionada

### Archivos de Referencia Proporcionados
1. `FacturaloPlus-API_cancelacion-cfdi.postman_collection.json` - Colección de Postman para cancelación
2. `Guia_de_implementacionREST+.pdf` - Guía de implementación de la API REST
3. `40FacturaloPlus-API_timbrado-cfdi.postman_collection.json` - Colección de Postman para timbrado

Estos archivos deben ser utilizados como referencia para la implementación del servicio de timbrado.

---

## 9. Soporte y Contacto

Para dudas o problemas con la implementación:
- Revisar los logs del sistema en el servidor
- Verificar permisos de carpetas y archivos
- Consultar la documentación del PAC utilizado

---

## Conclusión

Se han implementado exitosamente los tres requerimientos principales:
1. ✅ Módulo de Timbrado de Nómina con configuración completa de e.firma y API
2. ✅ Corrección del error "Página no encontrada" en descarga de plantilla
3. ✅ Filtro correcto de gerentes por puesto "Gerente General"

El sistema está listo para ser desplegado y probado en el ambiente de producción.
