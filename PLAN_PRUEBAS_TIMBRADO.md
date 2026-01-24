# Plan de Pruebas - Módulo de Timbrado de Nómina

## Fecha: 2026-01-24

---

## 1. Preparación del Ambiente

### 1.1 Base de Datos
```bash
# Ejecutar script de migración
mysql -u [usuario] -p recursos_humanos < migration_timbrado_nomina_config.sql

# Verificar que las configuraciones se insertaron correctamente
mysql -u [usuario] -p -e "USE recursos_humanos; SELECT * FROM configuraciones_globales WHERE grupo='timbrado';"
```

**Resultado Esperado**: Deben aparecer 11 registros con grupo='timbrado'

### 1.2 Directorio de Archivos
```bash
# Crear directorio para archivos e.firma
mkdir -p uploads/efirma
chmod 755 uploads/efirma

# Verificar permisos
ls -la uploads/
```

**Resultado Esperado**: Directorio `efirma` debe existir con permisos 755

---

## 2. Pruebas Funcionales

### 2.1 Módulo de Timbrado de Nómina

#### Prueba 2.1.1: Acceso a la Pestaña
**Pasos:**
1. Iniciar sesión como usuario con rol `admin`
2. Navegar a: Configuraciones > Configuraciones Globales
3. Hacer clic en la pestaña "Timbrado de Nómina"

**Resultado Esperado:**
- ✅ La pestaña debe ser visible y clicable
- ✅ Debe mostrarse el formulario con todos los campos
- ✅ El icono `fa-file-invoice` debe aparecer en la pestaña

**Resultado Actual:**
_Pendiente de prueba_

---

#### Prueba 2.1.2: Validación de Campos
**Pasos:**
1. En la pestaña "Timbrado de Nómina"
2. Completar los campos de texto:
   - RFC del Emisor: `ABC123456XYZ`
   - Razón Social: `Empresa de Prueba S.A. de C.V.`
3. Hacer clic en "Guardar Configuraciones"

**Resultado Esperado:**
- ✅ Los campos deben guardarse en la base de datos
- ✅ Debe aparecer mensaje de éxito: "Configuraciones guardadas exitosamente"
- ✅ Al recargar la página, los valores deben persistir

**Resultado Actual:**
_Pendiente de prueba_

---

#### Prueba 2.1.3: Carga de Certificado (.cer)
**Pasos:**
1. Preparar un archivo de prueba con extensión .cer (puede ser un archivo vacío para prueba)
2. En la pestaña "Timbrado de Nómina", en el campo "Certificado (.cer)"
3. Seleccionar el archivo .cer
4. Hacer clic en "Guardar Configuraciones"

**Resultado Esperado:**
- ✅ El archivo debe cargarse sin errores
- ✅ Debe aparecer confirmación: "Archivo actual: efirma_certificado_[timestamp].cer"
- ✅ El archivo debe existir en: `uploads/efirma/efirma_certificado_[timestamp].cer`
- ✅ Los permisos del archivo deben ser 644

**Resultado Actual:**
_Pendiente de prueba_

---

#### Prueba 2.1.4: Carga de Llave Privada (.key)
**Pasos:**
1. Preparar un archivo de prueba con extensión .key
2. En el campo "Llave Privada (.key)"
3. Seleccionar el archivo .key
4. Hacer clic en "Guardar Configuraciones"

**Resultado Esperado:**
- ✅ El archivo debe cargarse sin errores
- ✅ Debe aparecer confirmación: "Archivo actual: efirma_llave_[timestamp].key"
- ✅ El archivo debe existir en: `uploads/efirma/efirma_llave_[timestamp].key`

**Resultado Actual:**
_Pendiente de prueba_

---

#### Prueba 2.1.5: Validación de Tipo de Archivo Incorrecto
**Pasos:**
1. Intentar cargar un archivo .pdf en el campo "Certificado (.cer)"
2. Hacer clic en "Guardar Configuraciones"

**Resultado Esperado:**
- ✅ Debe rechazarse el archivo
- ✅ Debe aparecer error: "Tipo de archivo no permitido para certificado. Use archivo .cer"

**Resultado Actual:**
_Pendiente de prueba_

---

#### Prueba 2.1.6: Validación de Tamaño de Archivo
**Pasos:**
1. Crear un archivo de prueba mayor a 5MB
2. Intentar cargarlo en cualquier campo de e.firma
3. Hacer clic en "Guardar Configuraciones"

**Resultado Esperado:**
- ✅ Debe rechazarse el archivo
- ✅ Debe aparecer error: "El archivo es muy grande (máximo 5MB)"

**Resultado Actual:**
_Pendiente de prueba_

---

#### Prueba 2.1.7: Configuración de API
**Pasos:**
1. Completar los campos:
   - URL de API de Timbrado: `https://api.test.com/timbrar`
   - Usuario API: `usuario_test`
   - Contraseña API: `password123`
   - Token: `Bearer token123`
2. Guardar configuraciones

**Resultado Esperado:**
- ✅ Todos los campos deben guardarse correctamente
- ✅ La contraseña debe mostrarse como `••••••••` en el campo
- ✅ Al recargar, todos los valores deben persistir

**Resultado Actual:**
_Pendiente de prueba_

---

#### Prueba 2.1.8: Selección de Modo de Operación
**Pasos:**
1. En el campo "Ambiente", seleccionar "Producción"
2. Guardar configuraciones
3. Recargar la página

**Resultado Esperado:**
- ✅ El dropdown debe mostrar "Producción" seleccionado
- ✅ El valor debe ser 'produccion' en la base de datos

**Resultado Actual:**
_Pendiente de prueba_

---

#### Prueba 2.1.9: Tabla de Códigos de Error
**Pasos:**
1. Verificar que la tabla de códigos de error sea visible
2. Verificar que contenga las 4 filas esperadas

**Resultado Esperado:**
- ✅ Debe mostrarse la tabla con fondo azul
- ✅ Debe contener 4 códigos: 01, 02, 03, 04 con sus descripciones

**Resultado Actual:**
_Pendiente de prueba_

---

### 2.2 Descarga de Plantilla de Empleados

#### Prueba 2.2.1: Acceso a Importación
**Pasos:**
1. Navegar a: Empleados > Gestión de Empleados
2. Buscar el botón para importar empleados
3. Hacer clic en "Importar Listado de Colaboradores"

**Resultado Esperado:**
- ✅ Debe abrirse un modal con instrucciones de importación
- ✅ Debe ser visible el botón "Descargar plantilla de ejemplo"

**Resultado Actual:**
_Pendiente de prueba_

---

#### Prueba 2.2.2: Descarga de Plantilla
**Pasos:**
1. En el modal de importación
2. Hacer clic en "Descargar plantilla de ejemplo"

**Resultado Esperado:**
- ✅ Debe iniciarse la descarga de un archivo CSV
- ✅ El nombre del archivo debe ser: `plantilla_importacion_empleados.csv`
- ✅ El archivo debe contener:
  - Encabezados: nombres, apellido_paterno, apellido_materno, curp, rfc, nss, fecha_nacimiento, genero, email_personal, celular, fecha_ingreso, tipo_contrato, departamento, puesto, salario_mensual
  - 2 filas de ejemplo con datos de prueba
- ✅ El archivo debe estar codificado en UTF-8 con BOM

**Resultado Actual:**
_Pendiente de prueba_

---

#### Prueba 2.2.3: Contenido de Plantilla
**Pasos:**
1. Abrir el archivo CSV descargado en Excel o un editor de texto
2. Verificar que los datos de ejemplo sean legibles

**Resultado Esperado:**
- ✅ Los acentos y caracteres especiales deben mostrarse correctamente
- ✅ Las columnas deben estar separadas por comas
- ✅ Los datos de ejemplo deben ser coherentes y útiles

**Resultado Actual:**
_Pendiente de prueba_

---

### 2.3 Selección de Gerentes en Sucursales

#### Prueba 2.3.1: Verificar Datos de Prueba
**Pasos:**
1. En la base de datos, verificar que existan empleados con diferentes puestos:
```sql
SELECT id, nombres, apellido_paterno, puesto, estatus 
FROM empleados 
WHERE estatus = 'Activo' 
ORDER BY puesto;
```

**Resultado Esperado:**
- ✅ Debe haber al menos un empleado con puesto = 'Gerente General'
- ✅ Debe haber empleados con otros puestos (Contador, Cajero, etc.)

**Resultado Actual:**
_Pendiente de prueba_

---

#### Prueba 2.3.2: Dropdown de Selección de Gerente
**Pasos:**
1. Navegar a: Sucursales
2. Hacer clic en "Editar" en cualquier sucursal
3. En la sección "Gerentes Asignados", hacer clic en "Agregar Gerente"
4. Observar el dropdown de "Seleccionar Empleado"

**Resultado Esperado:**
- ✅ El dropdown debe mostrar SOLO empleados con puesto = 'Gerente General'
- ✅ NO deben aparecer empleados con otros puestos (Contador, Cajero, etc.)
- ✅ Todos los empleados listados deben tener estatus = 'Activo'
- ✅ Los nombres deben mostrarse en formato: "EMPXXX - Nombre Completo"

**Resultado Actual:**
_Pendiente de prueba_

---

#### Prueba 2.3.3: Asignación de Gerente
**Pasos:**
1. Seleccionar un empleado del dropdown
2. Hacer clic en "Guardar Cambios"

**Resultado Esperado:**
- ✅ El gerente debe asignarse exitosamente
- ✅ Debe aparecer en la lista de "Gerentes Asignados"
- ✅ El empleado debe mostrarse con su información completa

**Resultado Actual:**
_Pendiente de prueba_

---

## 3. Pruebas de Seguridad

### 3.1 Acceso No Autorizado
**Pasos:**
1. Iniciar sesión como usuario con rol `empleado` o `gerente`
2. Intentar acceder a: `/configuraciones`

**Resultado Esperado:**
- ✅ Debe redirigir a `/dashboard`
- ✅ NO debe permitir ver las configuraciones

**Resultado Actual:**
_Pendiente de prueba_

---

### 3.2 Inyección de Archivos Maliciosos
**Pasos:**
1. Preparar un archivo PHP malicioso con extensión .cer
2. Intentar cargarlo en el campo de certificado

**Resultado Esperado:**
- ✅ Debe validar que sea un archivo .cer real (no basado solo en extensión)
- ✅ En el peor caso, el archivo debe ser rechazado

**Resultado Actual:**
_Pendiente de prueba_

---

## 4. Pruebas de Compatibilidad

### 4.1 Navegadores
Probar en los siguientes navegadores:
- [ ] Chrome/Edge (último)
- [ ] Firefox (último)
- [ ] Safari (último)

**Resultado Esperado:**
- ✅ El formulario debe funcionar correctamente en todos
- ✅ Los estilos deben verse consistentes

---

### 4.2 Dispositivos
Probar en:
- [ ] Desktop (1920x1080)
- [ ] Tablet (768x1024)
- [ ] Móvil (375x667)

**Resultado Esperado:**
- ✅ El formulario debe ser responsive
- ✅ Los campos deben ser accesibles y usables

---

## 5. Pruebas de Integración

### 5.1 Persistencia de Datos
**Pasos:**
1. Configurar completamente el módulo de Timbrado
2. Cerrar sesión
3. Iniciar sesión nuevamente
4. Navegar a Configuraciones > Timbrado de Nómina

**Resultado Esperado:**
- ✅ Todos los valores deben estar guardados
- ✅ Los archivos deben seguir existiendo
- ✅ Las rutas de archivos deben ser correctas

**Resultado Actual:**
_Pendiente de prueba_

---

### 5.2 Actualización de Configuraciones
**Pasos:**
1. Modificar el RFC del emisor
2. Cargar un nuevo archivo de certificado (reemplazar el anterior)
3. Guardar

**Resultado Esperado:**
- ✅ Los valores antiguos deben actualizarse
- ✅ El archivo antiguo puede permanecer (no se borra automáticamente)
- ✅ El nuevo archivo debe usarse

**Resultado Actual:**
_Pendiente de prueba_

---

## 6. Checklist Final

### Antes del Despliegue
- [ ] Ejecutar script de migración SQL
- [ ] Crear directorio `uploads/efirma/` con permisos correctos
- [ ] Verificar que existe al menos un empleado con puesto "Gerente General"
- [ ] Hacer backup de la base de datos
- [ ] Verificar que todos los archivos modificados están en el repositorio

### Después del Despliegue
- [ ] Verificar que la pestaña "Timbrado de Nómina" es visible
- [ ] Probar carga de archivos .cer y .key
- [ ] Verificar descarga de plantilla de empleados
- [ ] Verificar filtro de gerentes en sucursales
- [ ] Revisar logs del sistema por errores
- [ ] Documentar cualquier comportamiento inesperado

---

## 7. Reporte de Problemas

Si se encuentra algún problema durante las pruebas:

1. Registrar:
   - Fecha y hora
   - Usuario que realizó la prueba
   - Navegador y versión
   - Pasos exactos para reproducir
   - Resultado esperado vs resultado actual
   - Screenshots si es posible

2. Verificar:
   - Logs de PHP: `/var/log/apache2/error.log` o similar
   - Logs del sistema: revisar errores en consola del navegador
   - Base de datos: verificar que las configuraciones existen

3. Acciones:
   - Reportar el problema con toda la información recopilada
   - Si es crítico, revertir los cambios
   - Si es menor, documentar como issue conocido

---

## 8. Criterios de Aceptación

El sistema se considera exitoso cuando:

✅ **Módulo de Timbrado:**
- La pestaña es accesible y funcional
- Se pueden cargar archivos .cer y .key correctamente
- Todos los campos se guardan y persisten
- Las validaciones funcionan correctamente

✅ **Descarga de Plantilla:**
- El enlace funciona sin error 404
- El archivo CSV se descarga correctamente
- El contenido es útil y está bien formateado

✅ **Selección de Gerentes:**
- Solo aparecen empleados con puesto "Gerente General"
- La asignación funciona correctamente
- No hay empleados incorrectos en el dropdown

✅ **General:**
- No hay errores en logs
- No hay regresiones en funcionalidad existente
- El rendimiento es aceptable
- La seguridad está mantenida

---

## Responsables

- **Desarrollo**: Copilot Agent
- **Pruebas**: [A asignar]
- **Aprobación**: [A asignar]
- **Despliegue**: [A asignar]

---

## Notas Adicionales

- Los archivos de e.firma son sensibles y deben manejarse con cuidado
- En producción, considerar encriptación adicional de contraseñas
- Mantener respaldos de archivos de e.firma
- Documentar el proceso de renovación de certificados
