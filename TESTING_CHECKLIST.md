# Checklist de Pruebas - Áreas de Trabajo en Sucursales

## Pre-requisitos
- [ ] Base de datos respaldada
- [ ] Migración `migration_areas_trabajo.sql` ejecutada
- [ ] Acceso como administrador al sistema
- [ ] Al menos una sucursal configurada
- [ ] Al menos un dispositivo Shelly configurado

## 1. Pruebas de Base de Datos

### Verificar Migración
- [ ] La tabla `sucursal_areas_trabajo` existe
- [ ] Todas las sucursales existentes tienen 2 áreas predeterminadas
  ```sql
  SELECT s.nombre, COUNT(sat.id) as areas 
  FROM sucursales s 
  LEFT JOIN sucursal_areas_trabajo sat ON s.id = sat.sucursal_id 
  GROUP BY s.id;
  ```
- [ ] Las áreas "Entrada" y "Salida" están marcadas como `es_predeterminada = 1`

## 2. Gestión de Áreas de Trabajo

### Visualización
- [ ] Navegar a "Sucursales" > Seleccionar una > "Editar"
- [ ] La sección "Áreas de Trabajo" aparece después de "Gerentes Asignados"
- [ ] Se muestran las áreas predeterminadas "Entrada" y "Salida"
- [ ] Las áreas muestran badge "Predeterminada"
- [ ] El botón "Agregar Área" está visible

### Crear Área Personalizada
- [ ] Click en "Agregar Área"
- [ ] El modal se abre correctamente
- [ ] Completar formulario:
  - Nombre: "Entrada Trasera"
  - Descripción: "Acceso para personal de mantenimiento"
  - Dispositivo Shelly: Seleccionar uno de la lista
  - Canal: Seleccionar Canal 2
  - Marcar "Área activa"
- [ ] Click en "Guardar"
- [ ] El área aparece en la lista
- [ ] Refrescar página y verificar que el área persiste

### Editar Área
- [ ] Click en botón de edición (lápiz) de un área
- [ ] El modal se abre con datos pre-cargados
- [ ] Modificar nombre a "Entrada Trasera - Modificada"
- [ ] Cambiar canal a Canal 3
- [ ] Click en "Guardar"
- [ ] Los cambios se reflejan en la lista
- [ ] Refrescar y verificar persistencia

### Eliminar Área
- [ ] Intentar eliminar área "Entrada" predeterminada
  - [ ] El botón de eliminar NO aparece (o no está disponible)
- [ ] Eliminar área personalizada creada
  - [ ] Click en botón de eliminar
  - [ ] Confirmar eliminación
  - [ ] El área desaparece de la lista
  - [ ] Refrescar y verificar eliminación

## 3. Dispositivos Shelly - Edición

### Acceder a Dispositivos
- [ ] Navegar a "Configuración" > "Dispositivos"
- [ ] Tab "Dispositivos Shelly Cloud" está activo
- [ ] Se muestra lista de dispositivos

### Editar Dispositivo
- [ ] Click en botón de edición (lápiz azul) de un dispositivo
- [ ] El modal se abre con título "Editar Dispositivo Shelly"
- [ ] Todos los campos están pre-cargados con valores actuales:
  - [ ] Token de Autenticación
  - [ ] Device ID
  - [ ] Servidor Cloud
  - [ ] Acción
  - [ ] Área
  - [ ] Nombre del Dispositivo
  - [ ] Canal Entrada
  - [ ] Canal Salida
  - [ ] Duración Pulso
  - [ ] Checkboxes (habilitado, invertido, simultáneo)
- [ ] Modificar "Duración Pulso" de 600 a 5000
- [ ] Click en "Guardar"
- [ ] El dispositivo muestra el nuevo valor
- [ ] Refrescar y verificar persistencia

### Toggle de Visibilidad de Token
- [ ] Verificar que el token aparece como contraseña (*****)
- [ ] Click en el icono del ojo
- [ ] El token se muestra en texto plano
- [ ] El icono cambia a ojo tachado
- [ ] Click nuevamente
- [ ] El token vuelve a mostrarse como contraseña

## 4. Prueba de Canales

### Probar Canal de Entrada
- [ ] Localizar un dispositivo configurado
- [ ] Click en botón "Probar" junto a "Canal de Entrada"
- [ ] El botón muestra "Probando..." con spinner
- [ ] Esperar respuesta
- [ ] Si exitoso:
  - [ ] Alert muestra "✓ Canal activado exitosamente"
  - [ ] El botón cambia de color temporalmente
  - [ ] El dispositivo físico se activó (verificar visualmente/auditivamente)
- [ ] Si falla:
  - [ ] Alert muestra "✗ Error al probar canal" con mensaje de error
  - [ ] Revisar logs del servidor para más detalles

### Probar Canal de Salida
- [ ] Click en botón "Probar" junto a "Canal de Salida"
- [ ] Mismas verificaciones que canal de entrada

### Probar con Configuración Incorrecta
- [ ] Editar dispositivo y cambiar Device ID a valor inválido
- [ ] Guardar cambios
- [ ] Probar canal
- [ ] Verificar mensaje de error apropiado
- [ ] Restaurar Device ID correcto

## 5. Registro de Asistencia con Áreas

### Configuración Previa
- [ ] Asignar dispositivo Shelly al área "Entrada" de una sucursal
- [ ] Seleccionar Canal 0
- [ ] Asignar diferente dispositivo/canal al área "Salida"
- [ ] Tener un empleado asignado a esa sucursal

### Registro de Entrada
- [ ] Navegar a URL pública de asistencia de la sucursal
- [ ] Ingresar código de empleado
- [ ] Capturar foto
- [ ] Click en "Registrar Entrada"
- [ ] Verificar:
  - [ ] Mensaje de éxito aparece
  - [ ] Dispositivo del área "Entrada" se activó
  - [ ] Canal correcto fue activado
  - [ ] No hay errores en logs

### Registro de Salida
- [ ] Mismo empleado, mismo día
- [ ] Click en "Registrar Salida"
- [ ] Verificar:
  - [ ] Mensaje de éxito con horas trabajadas
  - [ ] Dispositivo del área "Salida" se activó
  - [ ] Canal correcto fue activado
  - [ ] Horas trabajadas y extras calculadas correctamente

### Verificar Logs
- [ ] Revisar logs del servidor
- [ ] No debe haber mensajes "Advertencia: Shelly no activado"
- [ ] Logs muestran activación exitosa con detalles

### Caso sin Área Configurada
- [ ] Crear nueva sucursal sin configurar áreas
- [ ] Asignar dispositivo Shelly a la sucursal (método antiguo)
- [ ] Intentar registro de asistencia
- [ ] Verificar:
  - [ ] Sistema usa fallback al método anterior
  - [ ] Registro se completa exitosamente
  - [ ] Dispositivo se activa correctamente

## 6. Configuración del Sitio - Logo

### Estado Inicial
- [ ] Navegar a "Configuración" > "Sitio"
- [ ] Anotar el valor actual de "Nombre del Sitio"
- [ ] Verificar que el logo actual se muestra (si hay)

### Subir Nuevo Logo
- [ ] Preparar imagen de prueba (JPG, PNG o GIF, < 2MB)
- [ ] Click en campo de archivo logo
- [ ] Seleccionar imagen
- [ ] Verificar que aparece "Vista previa del nuevo logo"
- [ ] La vista previa muestra la imagen correctamente
- [ ] Click en "Guardar Configuraciones"
- [ ] Verificar:
  - [ ] Mensaje "Configuraciones guardadas exitosamente"
  - [ ] Refrescar página
  - [ ] El logo nuevo se muestra en "Logo actual"
  - [ ] **IMPORTANTE**: El "Nombre del Sitio" NO cambió
  - [ ] El nombre sigue siendo el valor anotado inicialmente

### Probar con Logo Grande
- [ ] Intentar subir imagen > 2MB
- [ ] Verificar mensaje de error "El archivo es muy grande"
- [ ] Archivo no se sube

### Probar con Formato Inválido
- [ ] Intentar subir archivo .txt o .pdf
- [ ] Verificar mensaje de error "Tipo de archivo no permitido"
- [ ] Archivo no se sube

### Logo Corrupto/No Válido
- [ ] Si el logo no carga:
  - [ ] Verificar que aparece mensaje "No se pudo cargar la imagen del logo"
  - [ ] La interfaz no se rompe

## 7. Pruebas de Integración

### Flujo Completo
- [ ] Crear nueva sucursal "Sucursal Prueba"
- [ ] Verificar áreas predeterminadas creadas automáticamente
- [ ] Crear área personalizada "Acceso VIP"
- [ ] Asignar dispositivo Shelly y canal a cada área
- [ ] Probar canales de todos los dispositivos
- [ ] Crear empleado asignado a "Sucursal Prueba"
- [ ] Registrar entrada (verificar activación área "Entrada")
- [ ] Registrar salida (verificar activación área "Salida")
- [ ] Verificar registro en base de datos
- [ ] Editar área "Acceso VIP" para cambiar canal
- [ ] Eliminar área "Acceso VIP"
- [ ] Verificar que áreas predeterminadas permanecen intactas

## 8. Pruebas de Errores y Edge Cases

### Red/Conexión
- [ ] Desconectar red del servidor Shelly Cloud
- [ ] Intentar activar canal
- [ ] Verificar mensaje de error apropiado
- [ ] Verificar que registro de asistencia continúa (con warning)
- [ ] Reconectar red
- [ ] Verificar funcionamiento normal

### Configuración Incompleta
- [ ] Crear área sin asignar dispositivo
- [ ] Intentar registro de asistencia
- [ ] Verificar:
  - [ ] Registro continúa
  - [ ] Warning en logs sobre dispositivo no configurado

### Múltiples Áreas Activas
- [ ] Crear 2 áreas "Entrada" personalizadas activas
- [ ] Registrar entrada
- [ ] Verificar que usa la primera área encontrada
- [ ] Sin errores críticos

## 9. Rendimiento

- [ ] Listar sucursales con muchas áreas (10+)
  - [ ] Carga rápida (< 2 segundos)
- [ ] Editar sucursal con muchos empleados (50+)
  - [ ] Carga aceptable (< 3 segundos)
- [ ] Probar canal múltiples veces seguidas
  - [ ] No hay bloqueos ni timeouts

## 10. Seguridad

- [ ] Intentar acceder a endpoints de áreas sin autenticación
  - [ ] Redirige a login o muestra error 401
- [ ] Intentar editar área como usuario no-admin
  - [ ] Muestra error "No autorizado"
- [ ] Intentar eliminar área predeterminada
  - [ ] Devuelve error apropiado
- [ ] Intentar inyectar código en campos de texto
  - [ ] Contenido se escapa correctamente (XSS prevention)

## Resumen de Resultados

### Funcionalidades Probadas
- [ ] Total de pruebas: _____
- [ ] Pruebas exitosas: _____
- [ ] Pruebas fallidas: _____

### Problemas Encontrados
1. _____________________________________________
2. _____________________________________________
3. _____________________________________________

### Notas Adicionales
_____________________________________________
_____________________________________________
_____________________________________________

### Firma de Aprobación
- Probado por: ________________
- Fecha: ________________
- Estado: [ ] Aprobado  [ ] Requiere correcciones
