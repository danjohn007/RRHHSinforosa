# Mapeo de Índices de Configuración - Timbrado de Nómina

## Fecha: 2026-01-24

---

## Importante: Orden Alfabético en la Base de Datos

El controlador `ConfiguracionesController` consulta las configuraciones con:
```php
$stmt = $db->query("SELECT * FROM configuraciones_globales ORDER BY grupo, clave");
```

Esto significa que **dentro de cada grupo**, las configuraciones se ordenan **alfabéticamente por su clave**.

---

## Mapeo de Índices para grupo='timbrado'

Las configuraciones del grupo 'timbrado' se ordenan alfabéticamente y se acceden en la vista mediante índices numéricos:

| Índice | Clave                          | Descripción                           |
|--------|--------------------------------|---------------------------------------|
| 0      | timbrado_api_cancelacion_url   | URL de API para cancelación de CFDI   |
| 1      | timbrado_api_password          | Contraseña de API de timbrado          |
| 2      | timbrado_api_token             | Token de autenticación de API          |
| 3      | timbrado_api_url               | URL de API de timbrado                 |
| 4      | timbrado_api_usuario           | Usuario de API de timbrado             |
| 5      | timbrado_certificado           | Ruta del archivo .cer                  |
| 6      | timbrado_llave_privada         | Ruta del archivo .key                  |
| 7      | timbrado_modo                  | Ambiente (pruebas/produccion)          |
| 8      | timbrado_password_llave        | Contraseña de llave privada            |
| 9      | timbrado_razon_social          | Razón social del emisor                |
| 10     | timbrado_rfc_emisor            | RFC del emisor                         |

---

## Uso en la Vista

En el archivo `app/views/configuraciones/index.php`, las configuraciones se acceden así:

```php
<!-- RFC del Emisor (índice 10) -->
<input type="text" name="configuraciones[timbrado_rfc_emisor]" 
    value="<?php echo htmlspecialchars($configs['timbrado'][10]['valor'] ?? ''); ?>">

<!-- Razón Social (índice 9) -->
<input type="text" name="configuraciones[timbrado_razon_social]" 
    value="<?php echo htmlspecialchars($configs['timbrado'][9]['valor'] ?? ''); ?>">

<!-- Certificado (índice 5) -->
<input type="hidden" name="configuraciones[timbrado_certificado]" 
    value="<?php echo htmlspecialchars($configs['timbrado'][5]['valor'] ?? ''); ?>">

<!-- Llave Privada (índice 6) -->
<input type="hidden" name="configuraciones[timbrado_llave_privada]" 
    value="<?php echo htmlspecialchars($configs['timbrado'][6]['valor'] ?? ''); ?>">

<!-- Contraseña de Llave (índice 8) -->
<input type="password" name="configuraciones[timbrado_password_llave]" 
    value="<?php echo htmlspecialchars($configs['timbrado'][8]['valor'] ?? ''); ?>">

<!-- URL de API (índice 3) -->
<input type="text" name="configuraciones[timbrado_api_url]" 
    value="<?php echo htmlspecialchars($configs['timbrado'][3]['valor'] ?? ''); ?>">

<!-- Usuario de API (índice 4) -->
<input type="text" name="configuraciones[timbrado_api_usuario]" 
    value="<?php echo htmlspecialchars($configs['timbrado'][4]['valor'] ?? ''); ?>">

<!-- Contraseña de API (índice 1) -->
<input type="password" name="configuraciones[timbrado_api_password]" 
    value="<?php echo htmlspecialchars($configs['timbrado'][1]['valor'] ?? ''); ?>">

<!-- Token de API (índice 2) -->
<input type="text" name="configuraciones[timbrado_api_token]" 
    value="<?php echo htmlspecialchars($configs['timbrado'][2]['valor'] ?? ''); ?>">

<!-- URL de Cancelación (índice 0) -->
<input type="text" name="configuraciones[timbrado_api_cancelacion_url]" 
    value="<?php echo htmlspecialchars($configs['timbrado'][0]['valor'] ?? ''); ?>">

<!-- Modo de Operación (índice 7) -->
<select name="configuraciones[timbrado_modo]">
    <option value="pruebas" <?php echo ($configs['timbrado'][7]['valor'] ?? '') === 'pruebas' ? 'selected' : ''; ?>>
    <option value="produccion" <?php echo ($configs['timbrado'][7]['valor'] ?? '') === 'produccion' ? 'selected' : ''; ?>>
</select>
```

---

## Guardado de Configuraciones

Cuando se envía el formulario, los nombres de los inputs (ej: `configuraciones[timbrado_rfc_emisor]`) se usan para actualizar las configuraciones por su **clave**, no por índice:

```php
foreach ($configuraciones as $clave => $valor) {
    $stmt = $db->prepare("UPDATE configuraciones_globales SET valor = ? WHERE clave = ?");
    $stmt->execute([$valor, $clave]);
}
```

Esto significa que el guardado es **independiente del orden** y siempre funciona correctamente.

---

## Nota Importante para Mantenimiento

### ⚠️ Si se agregan nuevas configuraciones al grupo 'timbrado':

1. Asegúrate de que la clave esté en orden alfabético en el script de migración
2. Recalcula los índices basándote en el orden alfabético completo
3. Actualiza la vista para usar los índices correctos
4. Actualiza este documento de mapeo

### ✅ Ejemplo: Agregar una nueva configuración

Si se agrega: `timbrado_ambiente_sat` (URL del ambiente SAT)

**Nuevo orden alfabético:**
```
0. timbrado_ambiente_sat         <- NUEVA (entre 'api_cancelacion' y 'api_password')
1. timbrado_api_cancelacion_url  <- antes era índice 0
2. timbrado_api_password         <- antes era índice 1
3. timbrado_api_token            <- antes era índice 2
... (todos los demás se desplazan +1)
```

**Impacto:** Habría que actualizar TODOS los índices en la vista.

---

## Mejora Sugerida para el Futuro

Para evitar dependencia de índices numéricos, se podría modificar el controlador para crear un array asociativo:

```php
// En ConfiguracionesController.php
$configs = [];
foreach ($configuraciones as $config) {
    if (!isset($configs[$config['grupo']])) {
        $configs[$config['grupo']] = [];
    }
    $configs[$config['grupo']][$config['clave']] = $config;
}
```

Esto permitiría acceder por clave en la vista:
```php
value="<?php echo htmlspecialchars($configs['timbrado']['timbrado_rfc_emisor']['valor'] ?? ''); ?>"
```

Sin embargo, esto requeriría modificar **todas** las configuraciones existentes (sitio, email, contacto, etc.) para mantener consistencia.

---

## Verificación

Para verificar que los índices son correctos, ejecuta en la base de datos:

```sql
SELECT 
    (@row_number:=@row_number + 1) - 1 AS indice,
    clave,
    valor,
    descripcion
FROM configuraciones_globales, (SELECT @row_number:=0) AS t
WHERE grupo = 'timbrado'
ORDER BY clave;
```

El resultado debe coincidir con la tabla de mapeo arriba.

---

## Conclusión

El sistema actual usa índices numéricos basados en orden alfabético. Esto funciona correctamente siempre que:
1. El script de migración inserte las configuraciones en cualquier orden (no importa)
2. La consulta SQL incluya `ORDER BY grupo, clave`
3. Los índices en la vista coincidan con el orden alfabético de las claves

Este documento sirve como referencia para mantenimiento futuro.
