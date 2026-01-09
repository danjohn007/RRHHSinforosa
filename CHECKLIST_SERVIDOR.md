# ✅ CHECKLIST - Reorganización en Servidor

Imprime esto o tenlo abierto mientras trabajas en cPanel.

---

## 📋 PASO 1: Crear carpetas nuevas

En cPanel File Manager, crear:

- [ ] `public/`
- [ ] `tests/`
- [ ] `storage/`
- [ ] `storage/uploads/`
- [ ] `storage/logs/`

---

## 📋 PASO 2: Mover a public/

Mover estos archivos/carpetas **DESDE LA RAÍZ** a `public/`:

- [ ] `index.php` → `public/index.php`
- [ ] `.htaccess` → `public/.htaccess`
- [ ] `assets/` → `public/assets/`
- [ ] `api/` → `public/api/`
- [ ] `Imagenes/` → `public/Imagenes/`

---

## 📋 PASO 3: Mover a tests/

Mover estos archivos **DESDE LA RAÍZ** a `tests/`:

- [ ] `test-ajax.php`
- [ ] `test-cloud-api.php`
- [ ] `test-cloud-api-action.php`
- [ ] `test-compare.php`
- [ ] `debug-url.php`
- [ ] `insert-test-plates.php`
- [ ] `test_connection.php`
- [ ] `get-units.php` (si existe)
- [ ] `add-more-detections.php` (si es de prueba)
- [ ] `error_log` (si existe)

---

## 📋 PASO 4: Mover a storage/

- [ ] `uploads/` → `storage/uploads/` (mover contenido, no crear subcarpeta)

---

## 📋 PASO 5: Crear archivos .htaccess de protección

### En `/tests/.htaccess`:
```apache
Order Deny,Allow
Deny from all
```
- [ ] Creado

### En `/storage/.htaccess`:
```apache
Order Deny,Allow
Deny from all
```
- [ ] Creado

### En `/app/.htaccess`:
```apache
Order Deny,Allow
Deny from all
```
- [ ] Creado

### En `/config/.htaccess`:
```apache
Order Deny,Allow
Deny from all
```
- [ ] Creado

---

## 📋 PASO 6: Configurar Document Root

En cPanel > Dominios > Tu dominio > Editar:

**Document Root:** Cambiar a:
```
/home/TUUSUARIO/public_html/public
```

O si está en subcarpeta:
```
/home/TUUSUARIO/public_html/RRHHSinforosa/public
```

- [ ] Document Root actualizado

---

## 📋 PASO 7: Verificar permisos

Asegurarse de que los permisos sean correctos:

- [ ] Carpetas: `755` (rwxr-xr-x)
- [ ] Archivos PHP: `644` (rw-r--r--)
- [ ] Archivos .htaccess: `644` (rw-r--r--)
- [ ] `storage/uploads/`: `755` (debe ser escribible)
- [ ] `storage/logs/`: `755` (debe ser escribible)

---

## 📋 PASO 8: Probar el sistema

Verificar que todo funciona:

- [ ] `http://tudominio.com/` → Carga el login
- [ ] `http://tudominio.com/dashboard` → Funciona
- [ ] `http://tudominio.com/empleados` → Funciona

Verificar que está protegido:

- [ ] `http://tudominio.com/config/database.php` → Error 403 ✅
- [ ] `http://tudominio.com/tests/debug-url.php` → Error 403 ✅
- [ ] `http://tudominio.com/app/controllers/AuthController.php` → Error 403 ✅

---

## 📋 PASO 9: Hacer backup

- [ ] Descargar backup completo del sitio
- [ ] Exportar base de datos

---

## 🎯 ESTRUCTURA FINAL

```
RRHHSinforosa/
├── public/              ← Document Root
│   ├── index.php
│   ├── .htaccess
│   ├── assets/
│   ├── api/
│   └── Imagenes/
├── app/
├── config/
├── tests/
├── storage/
│   ├── uploads/
│   └── logs/
├── README.md
└── schema.sql
```

---

## ⚠️ SI ALGO FALLA

1. Restaurar backup
2. Revisar error_log en cPanel
3. Verificar permisos de archivos
4. Asegurarte de que Document Root esté correcto

---

## ✅ COMPLETADO

Fecha: __________
Hora: __________
Todo funcionando: [ ]
