# 📋 GUÍA DE REORGANIZACIÓN - QUÉ MOVER Y DÓNDE

## 🎯 ESTRUCTURA FINAL

```
RRHHSinforosa/
│
├── public/                    ← Accesible desde web (Document Root)
│   ├── index.php             ← MOVER AQUÍ
│   ├── .htaccess             ← MOVER AQUÍ
│   ├── assets/               ← MOVER AQUÍ
│   ├── api/                  ← MOVER AQUÍ
│   ├── Imagenes/             ← MOVER AQUÍ
│   └── favicon.ico           ← CREAR/MOVER SI EXISTE
│
├── app/                      ← YA EXISTE - DEJAR DONDE ESTÁ
├── config/                   ← YA EXISTE - DEJAR DONDE ESTÁ
│
├── tests/                    ← NUEVA - Archivos de prueba/desarrollo
│   ├── test-ajax.php         ← MOVER AQUÍ
│   ├── test-cloud-api.php    ← MOVER AQUÍ
│   ├── test-cloud-api-action.php ← MOVER AQUÍ
│   ├── test-compare.php      ← MOVER AQUÍ
│   ├── debug-url.php         ← MOVER AQUÍ
│   ├── insert-test-plates.php ← MOVER AQUÍ
│   └── get-units.php         ← MOVER AQUÍ (si es de prueba)
│
├── storage/                  ← NUEVA - Archivos privados/datos
│   ├── uploads/              ← MOVER la carpeta "uploads" AQUÍ
│   └── logs/                 ← Para logs futuros
│
├── uploads/                  ← MOVER A storage/uploads/
├── .htaccess                 ← MOVER A public/
├── index.php                 ← MOVER A public/
├── README.md                 ← DEJAR EN RAÍZ
└── test_connection.php       ← MOVER A tests/
```

---

## 📦 PASO A PASO - LOCAL (YA HECHO AQUÍ)

### ✅ Carpetas creadas:
- `public/`
- `tests/`
- `storage/`
- `storage/uploads/`
- `storage/logs/`

---

## 🚀 PASOS EN EL SERVIDOR (cPanel)

### 1️⃣ Crear carpetas en el servidor:
```
- public/
- tests/
- storage/
- storage/uploads/
- storage/logs/
```

### 2️⃣ Mover archivos A public/:
- `index.php` → `public/index.php`
- `.htaccess` → `public/.htaccess`
- carpeta `assets/` → `public/assets/`
- carpeta `api/` → `public/api/`
- carpeta `Imagenes/` → `public/Imagenes/`

### 3️⃣ Mover archivos A tests/:
- `test-ajax.php`
- `test-cloud-api.php`
- `test-cloud-api-action.php`
- `test-compare.php`
- `debug-url.php`
- `insert-test-plates.php`
- `test_connection.php`
- `get-units.php` (si es de prueba)

### 4️⃣ Mover archivos A storage/:
- carpeta `uploads/` → `storage/uploads/`

### 5️⃣ DEJAR en raíz:
- `app/` ✅
- `config/` ✅
- `README.md` ✅
- `schema.sql` ✅
- `recursos_humanos.txt` ✅
- `.gitignore` ✅
- `MEJORAS_IMPLEMENTADAS.md` ✅

---

## ⚙️ CONFIGURAR cPanel

### En cPanel > Dominios > Configuración del dominio:

**Document Root:** Cambiar de:
```
/home/usuario/public_html
```

A:
```
/home/usuario/public_html/public
```

O si está en subcarpeta:
```
/home/usuario/public_html/RRHHSinforosa/public
```

---

## 🔒 ARCHIVOS DE PROTECCIÓN A CREAR

### En /tests/.htaccess:
```apache
# Bloquear acceso desde internet
Order Deny,Allow
Deny from all
```

### En /storage/.htaccess:
```apache
# Bloquear acceso desde internet
Order Deny,Allow
Deny from all
```

### En /app/.htaccess:
```apache
# Bloquear acceso desde internet
Order Deny,Allow
Deny from all
```

### En /config/.htaccess:
```apache
# Bloquear acceso desde internet
Order Deny,Allow
Deny from all
```

---

## ✅ VERIFICACIÓN

Después de mover todo, verifica que funcione:

1. ✅ `http://tudominio.com/` → Debe cargar el sistema
2. ✅ `http://tudominio.com/login` → Debe funcionar
3. ❌ `http://tudominio.com/config/database.php` → Debe dar error 403
4. ❌ `http://tudominio.com/tests/debug-url.php` → Debe dar error 403

---

## 🎯 RESUMEN DE BENEFICIOS

✅ Solo `public/` accesible desde web
✅ Código y configuración protegidos
✅ Archivos de prueba bloqueados
✅ Estructura profesional y segura
✅ Mismos nombres de archivos (sin cambios)

---

## ⚠️ IMPORTANTE

- **Hacer backup antes de mover en producción**
- **Probar en desarrollo primero**
- **Verificar permisos de carpetas** (755 para directorios, 644 para archivos)
