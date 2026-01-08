# Sistema de GESTIÓN INTEGRAL DE TALENTO Y NÓMINA
## Sinforosa Café - Sistema RRHH

Sistema integral de gestión de recursos humanos y nómina desarrollado con tecnologías open source para Sinforosa Café.

![PHP](https://img.shields.io/badge/PHP-7.4+-blue)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.0+-38bdf8)
![License](https://img.shields.io/badge/License-MIT-green)

## 📋 Características Principales

### Módulos Implementados

#### 1. **Gestión de Personal**
- ✅ Administración completa de colaboradores (CRUD)
- ✅ Registro de historial laboral
- ✅ Gestión de expedientes digitales
- ✅ Control de bajas y finiquitos
- ✅ Reportes de personal
- ✅ Generación automática de cartas de recomendación y constancias

#### 2. **Administración de Nómina**
- ✅ Configuración de percepciones y deducciones
- ✅ Procesamiento de incidencias
- ✅ Cálculo automatizado de nómina
- ✅ Gestión de nómina de eventuales
- ✅ Generación de archivos de dispersión
- ✅ Emisión de recibos de nómina
- ✅ Liquidación de impuestos y cuotas IMSS/INFONAVIT

#### 3. **Control de Tiempos y Asistencia**
- ✅ Gestión de turnos y horarios
- ✅ Registro de asistencia
- ✅ Procesamiento de horas extra y retardos
- ✅ Administración de vacaciones
- ✅ Calculadora de antigüedad laboral
- ✅ Solicitudes y aprobaciones de vacaciones

#### 4. **Reclutamiento y Selección**
- ✅ Gestión de candidatos
- ✅ Seguimiento de procesos de selección
- ✅ Programación de entrevistas
- ✅ Flujo de aprobación de contrataciones
- ✅ Conversión de candidato a empleado

#### 5. **Gestión de Beneficios e Incidencias**
- ✅ Administración de préstamos y descuentos
- ✅ Registro de bonos y apoyos especiales
- ✅ Notificaciones de cumpleaños y eventos

#### 6. **Análisis y Reporting**
- ✅ Dashboard ejecutivo de RRHH con gráficas
- ✅ Reportes de nómina
- ✅ Reportes de personal y antigüedad
- ✅ Reportes de vacaciones y ausentismo
- ✅ Reportes de costos laborales

#### 7. **Integración con Dispositivos**
- ✅ API para dispositivos HikVision
- ✅ Soporte para múltiples dispositivos de control de acceso

## 🛠 Tecnologías Utilizadas

- **Backend:** PHP puro (sin frameworks)
- **Base de Datos:** MySQL 5.7+
- **Frontend:** HTML5, CSS3, JavaScript
- **Estilos:** Tailwind CSS 3.0+ (vía CDN)
- **Gráficas:** Chart.js
- **Iconos:** Font Awesome 6.4
- **Arquitectura:** MVC (Model-View-Controller)
- **Seguridad:** Password hashing con `password_hash()`, sesiones seguras
- **URL Rewriting:** Apache mod_rewrite (.htaccess)

## 📦 Requisitos del Sistema

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Apache 2.4+ con mod_rewrite habilitado
- Extensiones PHP requeridas:
  - PDO
  - PDO_MySQL
  - session
  - mbstring

## 🚀 Instalación

### 1. Clonar o descargar el repositorio

```bash
git clone https://github.com/danjohn007/RRHHSinforosa.git
cd RRHHSinforosa
```

### 2. Configurar la base de datos

```bash
# Crear la base de datos
mysql -u root -p -e "CREATE DATABASE rrhh_sinforosa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Importar el esquema y datos de ejemplo
mysql -u root -p rrhh_sinforosa < schema.sql
```

### 3. Configurar credenciales de base de datos

Edita el archivo `config/database.php` con tus credenciales:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'rrhh_sinforosa');
define('DB_USER', 'root');          // Tu usuario MySQL
define('DB_PASS', '');              // Tu contraseña MySQL
```

### 4. Configurar Apache

#### Opción A: Instalación en el directorio raíz
```apache
DocumentRoot "/ruta/a/RRHHSinforosa"
<Directory "/ruta/a/RRHHSinforosa">
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```

#### Opción B: Instalación en subdirectorio
El sistema detecta automáticamente la URL base, por lo que funciona en cualquier subdirectorio.

Ejemplo: `http://localhost/RRHHSinforosa/`

### 5. Verificar la instalación

Accede a: `http://tu-servidor/test_connection.php`

Este archivo verificará:
- ✅ Conexión a la base de datos
- ✅ URL base detectada correctamente
- ✅ Estructura de directorios
- ✅ Extensiones PHP necesarias

### 6. Acceder al sistema

URL: `http://tu-servidor/`

**Usuarios de demostración:**

| Usuario | Email | Contraseña | Rol |
|---------|-------|------------|-----|
| Admin | admin@sinforosa.com | password | Administrador |
| RRHH | rrhh@sinforosa.com | password | Recursos Humanos |
| Gerente | gerente@sinforosa.com | password | Gerente |

## 📂 Estructura del Proyecto

```
RRHHSinforosa/
├── app/
│   ├── controllers/        # Controladores MVC
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── EmpleadosController.php
│   │   ├── NominaController.php
│   │   ├── AsistenciaController.php
│   │   ├── ReclutamientoController.php
│   │   ├── BeneficiosController.php
│   │   └── ReportesController.php
│   ├── models/             # Modelos de datos
│   │   ├── Usuario.php
│   │   └── Empleado.php
│   └── views/              # Vistas HTML/PHP
│       ├── layouts/
│       ├── auth/
│       ├── dashboard/
│       ├── empleados/
│       ├── nomina/
│       ├── asistencia/
│       ├── reclutamiento/
│       ├── beneficios/
│       └── reportes/
├── config/
│   ├── config.php          # Configuración general
│   └── database.php        # Configuración de BD
├── public/
│   ├── css/               # Estilos personalizados
│   ├── js/                # Scripts JavaScript
│   └── assets/            # Imágenes, archivos
├── .htaccess              # Configuración Apache
├── index.php              # Punto de entrada y router
├── schema.sql             # Esquema de base de datos
├── test_connection.php    # Test de configuración
└── README.md              # Este archivo
```

## 🔧 Configuración Adicional

### URL Amigables

El sistema usa `.htaccess` para reescribir URLs amigables:

```
http://tu-servidor/empleados          → Lista de empleados
http://tu-servidor/empleados/crear    → Crear empleado
http://tu-servidor/nomina              → Gestión de nómina
http://tu-servidor/asistencia          → Control de asistencia
```

### URL Base Automática

La URL base se detecta automáticamente, permitiendo instalar el sistema en cualquier directorio sin modificar código.

### Integración con HikVision

El sistema incluye soporte para dispositivos de control de acceso HikVision. Configura los dispositivos en la tabla `dispositivos_hikvision`:

```sql
INSERT INTO dispositivos_hikvision (nombre, ip, puerto, usuario, password, ubicacion) 
VALUES ('Entrada Principal', '192.168.1.100', 80, 'admin', 'password', 'Planta Baja');
```

## 📊 Dashboard y Análisis

El dashboard principal incluye:
- **Métricas en tiempo real:** Empleados activos, nóminas, vacaciones pendientes
- **Gráficas interactivas:** Distribución por departamento, asistencia semanal
- **Cumpleaños del mes:** Notificaciones de colaboradores
- **Accesos rápidos:** A las funciones más utilizadas

## 🔐 Seguridad

- Contraseñas hasheadas con `password_hash()` (bcrypt)
- Sesiones seguras con configuración HttpOnly
- Protección contra SQL Injection con PDO Prepared Statements
- Validación de roles y permisos
- Variables de entorno para credenciales sensibles

## 📝 Datos de Ejemplo

El sistema incluye datos de ejemplo del estado de Querétaro:
- 8 empleados de ejemplo
- Departamentos: Administración, Operaciones, RRHH, Ventas, Cocina, Mantenimiento
- Conceptos de nómina preconfigurados
- Turnos de trabajo
- Candidatos de prueba

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Por favor:

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 📧 Contacto

Para soporte o consultas:
- **Email:** admin@sinforosa.com
- **GitHub:** [@danjohn007](https://github.com/danjohn007)

## 🙏 Agradecimientos

- Tailwind CSS por el framework de estilos
- Chart.js por las gráficas interactivas
- Font Awesome por los iconos
- Comunidad PHP por las mejores prácticas

---

**Desarrollado con ❤️ para Sinforosa Café**

*Sistema de Gestión Integral de Talento y Nómina v1.0.0*
