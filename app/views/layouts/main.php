<?php
/**
 * Layout principal del sistema
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Dashboard'; ?> - <?php echo APP_NAME; ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Scripts del sistema -->
    <script>
        // Definir BASE_URL para JavaScript
        const BASE_URL = '<?php echo BASE_URL; ?>';
    </script>
    <script src="<?php echo BASE_URL; ?>assets/js/validaciones.js" defer></script>
    <script src="<?php echo BASE_URL; ?>assets/js/api-client.js" defer></script>
    
    <style>
        .bg-gradient-sinforosa {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        .sidebar-item.active {
            background-color: rgba(255, 255, 255, 0.15);
            border-left: 4px solid white;
        }
        
        /* Asegurar que los canvas de Chart.js se muestren correctamente */
        canvas {
            max-width: 100%;
            height: auto !important;
        }
        
        /* Animaciones suaves */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        <!-- Mobile Overlay -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden"></div>
        
        <!-- Sidebar -->
        <aside id="sidebar" class="w-64 bg-gradient-sinforosa text-white flex-shrink-0 fixed md:static inset-y-0 left-0 z-50 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out md:flex flex-col">
            <!-- Logo -->
            <div class="p-6 border-b border-white border-opacity-20">
                <div class="flex items-center space-x-3">
                    <div class="h-10 w-10 bg-white rounded-lg flex items-center justify-center">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="font-bold text-lg">Sinforosa</h1>
                        <p class="text-xs text-purple-200">Sistema RRHH</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto p-4">
                <div class="space-y-1">
                    <a href="<?php echo BASE_URL; ?>dashboard" class="sidebar-item flex items-center px-4 py-3 rounded-lg transition">
                        <i class="fas fa-home w-5"></i>
                        <span class="ml-3">Dashboard</span>
                    </a>
                    
                    <!-- Gestión de Personal -->
                    <div class="mt-4">
                        <p class="px-4 text-xs font-semibold text-purple-200 uppercase tracking-wider mb-2">Personal</p>
                        <a href="<?php echo BASE_URL; ?>empleados" class="sidebar-item flex items-center px-4 py-3 rounded-lg transition">
                            <i class="fas fa-users w-5"></i>
                            <span class="ml-3">Empleados</span>
                        </a>
                    </div>
                    
                    <!-- Nómina -->
                    <div class="mt-4">
                        <p class="px-4 text-xs font-semibold text-purple-200 uppercase tracking-wider mb-2">Nómina</p>
                        <a href="<?php echo BASE_URL; ?>nomina" class="sidebar-item flex items-center px-4 py-3 rounded-lg transition">
                            <i class="fas fa-money-bill-wave w-5"></i>
                            <span class="ml-3">Procesamiento</span>
                        </a>
                        <a href="<?php echo BASE_URL; ?>nomina/configuracion" class="sidebar-item flex items-center px-4 py-3 rounded-lg transition">
                            <i class="fas fa-cog w-5"></i>
                            <span class="ml-3">Configuración</span>
                        </a>
                    </div>
                    
                    <!-- Asistencia -->
                    <div class="mt-4">
                        <p class="px-4 text-xs font-semibold text-purple-200 uppercase tracking-wider mb-2">Asistencia</p>
                        <a href="<?php echo BASE_URL; ?>asistencia" class="sidebar-item flex items-center px-4 py-3 rounded-lg transition">
                            <i class="fas fa-clock w-5"></i>
                            <span class="ml-3">Registro</span>
                        </a>
                        <a href="<?php echo BASE_URL; ?>asistencia/vacaciones" class="sidebar-item flex items-center px-4 py-3 rounded-lg transition">
                            <i class="fas fa-umbrella-beach w-5"></i>
                            <span class="ml-3">Vacaciones</span>
                        </a>
                        <a href="<?php echo BASE_URL; ?>asistencia/incidencias" class="sidebar-item flex items-center px-4 py-3 rounded-lg transition">
                            <i class="fas fa-exclamation-triangle w-5"></i>
                            <span class="ml-3">Incidencias</span>
                        </a>
                    </div>
                    
                    <!-- Reclutamiento -->
                    <div class="mt-4">
                        <p class="px-4 text-xs font-semibold text-purple-200 uppercase tracking-wider mb-2">Reclutamiento</p>
                        <a href="<?php echo BASE_URL; ?>reclutamiento" class="sidebar-item flex items-center px-4 py-3 rounded-lg transition">
                            <i class="fas fa-user-plus w-5"></i>
                            <span class="ml-3">Candidatos</span>
                        </a>
                        <a href="<?php echo BASE_URL; ?>reclutamiento/entrevistas" class="sidebar-item flex items-center px-4 py-3 rounded-lg transition">
                            <i class="fas fa-handshake w-5"></i>
                            <span class="ml-3">Entrevistas</span>
                        </a>
                    </div>
                    
                    <!-- Beneficios -->
                    <div class="mt-4">
                        <p class="px-4 text-xs font-semibold text-purple-200 uppercase tracking-wider mb-2">Beneficios</p>
                        <a href="<?php echo BASE_URL; ?>beneficios" class="sidebar-item flex items-center px-4 py-3 rounded-lg transition">
                            <i class="fas fa-gift w-5"></i>
                            <span class="ml-3">Préstamos y Bonos</span>
                        </a>
                    </div>
                    
                    <!-- Reportes -->
                    <div class="mt-4">
                        <p class="px-4 text-xs font-semibold text-purple-200 uppercase tracking-wider mb-2">Reportes</p>
                        <a href="<?php echo BASE_URL; ?>reportes" class="sidebar-item flex items-center px-4 py-3 rounded-lg transition">
                            <i class="fas fa-chart-bar w-5"></i>
                            <span class="ml-3">Análisis</span>
                        </a>
                        <a href="<?php echo BASE_URL; ?>errores" class="sidebar-item flex items-center px-4 py-3 rounded-lg transition">
                            <i class="fas fa-bug w-5"></i>
                            <span class="ml-3">Errores del Sistema</span>
                        </a>
                    </div>
                </div>
            </nav>

            <!-- User info -->
            <div class="p-4 border-t border-white border-opacity-20">
                <div class="flex items-center space-x-3">
                    <div class="h-10 w-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate"><?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?></p>
                        <p class="text-xs text-purple-200 truncate"><?php echo htmlspecialchars($_SESSION['user_rol'] ?? ''); ?></p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top bar -->
            <header class="bg-white shadow-sm z-10">
                <div class="px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <button id="mobile-menu-button" class="md:hidden text-gray-600 hover:text-gray-900 focus:outline-none">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <h2 class="text-xl font-semibold text-gray-800"><?php echo $title ?? 'Dashboard'; ?></h2>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <div class="relative">
                            <button id="notificaciones-btn" class="relative text-gray-600 hover:text-gray-800 transition">
                                <i class="fas fa-bell text-xl"></i>
                                <span id="badge-notificaciones" class="absolute -top-1 -right-1 h-5 w-5 bg-red-500 rounded-full text-xs text-white flex items-center justify-center font-bold">0</span>
                            </button>
                            
                            <!-- Dropdown de notificaciones -->
                            <div id="dropdown-notificaciones" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
                                <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                                    <h3 class="font-semibold text-gray-900">Notificaciones</h3>
                                    <button onclick="marcarTodasLeidas()" class="text-xs text-blue-600 hover:text-blue-800">
                                        Marcar todas como leídas
                                    </button>
                                </div>
                                
                                <div id="lista-notificaciones" class="max-h-96 overflow-y-auto">
                                    <!-- Se llenará con JavaScript -->
                                </div>
                                
                                <div class="p-3 border-t border-gray-200 text-center">
                                    <a href="<?php echo BASE_URL; ?>notificaciones" class="text-sm text-blue-600 hover:text-blue-800">
                                        Ver todas las notificaciones
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- User menu -->
                        <div class="relative">
                            <a href="<?php echo BASE_URL; ?>logout" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900">
                                <span class="text-sm">Cerrar Sesión</span>
                                <i class="fas fa-sign-out-alt"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page content -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-6">
                <?php echo $content ?? ''; ?>
            </main>
        </div>
    </div>

    <script>
        // Sistema de notificaciones con localStorage
        let notificaciones = [];
        
        function cargarNotificaciones() {
            // Intentar cargar desde localStorage
            const stored = localStorage.getItem('notificaciones_sistema');
            
            if (stored) {
                notificaciones = JSON.parse(stored);
            } else {
                // Notificaciones iniciales
                notificaciones = [
                    {
                        id: 1,
                        tipo: 'solicitud',
                        titulo: 'Nueva solicitud de vacaciones',
                        mensaje: 'Juan Pérez ha solicitado vacaciones del 15 al 20 de enero',
                        fecha: '2026-01-09 10:30:00',
                        leida: false,
                        icono: 'fa-umbrella-beach',
                        color: 'text-blue-600'
                    },
                    {
                        id: 2,
                        tipo: 'nomina',
                        titulo: 'Nómina procesada',
                        mensaje: 'La nómina quincenal ha sido procesada correctamente',
                        fecha: '2026-01-09 09:15:00',
                        leida: false,
                        icono: 'fa-money-bill-wave',
                        color: 'text-green-600'
                    },
                    {
                        id: 3,
                        tipo: 'empleado',
                        titulo: 'Nuevo empleado registrado',
                        mensaje: 'María González ha sido agregada al sistema',
                        fecha: '2026-01-08 16:45:00',
                        leida: false,
                        icono: 'fa-user-plus',
                        color: 'text-purple-600'
                    }
                ];
                guardarNotificaciones();
            }
            
            actualizarNotificaciones();
        }
        
        function guardarNotificaciones() {
            localStorage.setItem('notificaciones_sistema', JSON.stringify(notificaciones));
        }
        
        function actualizarNotificaciones() {
            const noLeidas = notificaciones.filter(n => !n.leida).length;
            const badge = document.getElementById('badge-notificaciones');
            const lista = document.getElementById('lista-notificaciones');
            
            // Actualizar badge
            badge.textContent = noLeidas;
            badge.classList.toggle('hidden', noLeidas === 0);
            
            // Renderizar lista
            if (notificaciones.length === 0) {
                lista.innerHTML = `
                    <div class="p-8 text-center text-gray-500">
                        <i class="fas fa-bell-slash text-4xl mb-3 text-gray-400"></i>
                        <p>No tienes notificaciones</p>
                    </div>
                `;
            } else {
                lista.innerHTML = notificaciones.map(n => {
                    const fecha = new Date(n.fecha);
                    const ahora = new Date();
                    const diff = Math.floor((ahora - fecha) / 1000 / 60); // minutos
                    
                    let tiempoTexto;
                    if (diff < 1) tiempoTexto = 'Hace un momento';
                    else if (diff < 60) tiempoTexto = `Hace ${diff} min`;
                    else if (diff < 1440) tiempoTexto = `Hace ${Math.floor(diff / 60)} h`;
                    else tiempoTexto = `Hace ${Math.floor(diff / 1440)} días`;
                    
                    return `
                        <div class="p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition ${!n.leida ? 'bg-blue-50' : ''}"
                             onclick="marcarLeida(${n.id})">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                                        <i class="fas ${n.icono} ${n.color}"></i>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 ${!n.leida ? 'font-semibold' : ''}">
                                        ${n.titulo}
                                        ${!n.leida ? '<span class="ml-2 inline-block h-2 w-2 bg-blue-600 rounded-full"></span>' : ''}
                                    </p>
                                    <p class="text-xs text-gray-600 mt-1">${n.mensaje}</p>
                                    <p class="text-xs text-gray-400 mt-1">${tiempoTexto}</p>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
            }
        }
        
        function marcarLeida(id) {
            const notif = notificaciones.find(n => n.id === id);
            if (notif) {
                notif.leida = true;
                guardarNotificaciones();
                actualizarNotificaciones();
                // Disparar evento para sincronizar con otras vistas
                window.dispatchEvent(new CustomEvent('notificacionesActualizadas'));
            }
        }
        
        function marcarTodasLeidas() {
            notificaciones.forEach(n => n.leida = true);
            guardarNotificaciones();
            actualizarNotificaciones();
            // Disparar evento para sincronizar con otras vistas
            window.dispatchEvent(new CustomEvent('notificacionesActualizadas'));
        }
        
        // Toggle dropdown de notificaciones
        document.addEventListener('DOMContentLoaded', function() {
            const btnNotificaciones = document.getElementById('notificaciones-btn');
            const dropdown = document.getElementById('dropdown-notificaciones');
            
            if (btnNotificaciones && dropdown) {
                btnNotificaciones.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dropdown.classList.toggle('hidden');
                });
                
                // Cerrar al hacer click fuera
                document.addEventListener('click', function(e) {
                    if (!dropdown.contains(e.target) && !btnNotificaciones.contains(e.target)) {
                        dropdown.classList.add('hidden');
                    }
                });
            }
            
            // Cargar notificaciones al iniciar
            cargarNotificaciones();
            
            // Actualizar cada 60 segundos
            setInterval(cargarNotificaciones, 60000);
            
            // Escuchar cambios desde otras vistas
            window.addEventListener('notificacionesActualizadas', function() {
                cargarNotificaciones();
            });
        });
        
        // Marcar elemento activo en el menú
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            const menuItems = document.querySelectorAll('.sidebar-item');
            
            menuItems.forEach(item => {
                const href = item.getAttribute('href');
                if (href) {
                    const menuPath = href.replace(BASE_URL, '');
                    // Verificar coincidencia exacta para rutas anidadas
                    if (currentPath.includes(menuPath) && href.includes(currentPath.split('/').filter(p => p).pop())) {
                        item.classList.add('active');
                    }
                }
            });
            
            // Mobile menu toggle
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            if (mobileMenuButton && sidebar && overlay) {
                // Open sidebar
                mobileMenuButton.addEventListener('click', function() {
                    sidebar.classList.remove('-translate-x-full');
                    overlay.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                });
                
                // Close sidebar when clicking overlay
                overlay.addEventListener('click', function() {
                    sidebar.classList.add('-translate-x-full');
                    overlay.classList.add('hidden');
                    document.body.style.overflow = '';
                });
                
                // Close sidebar when clicking on a menu item (mobile only)
                menuItems.forEach(item => {
                    item.addEventListener('click', function() {
                        if (window.innerWidth < 768) {
                            sidebar.classList.add('-translate-x-full');
                            overlay.classList.add('hidden');
                            document.body.style.overflow = '';
                        }
                    });
                });
            }
        });
    </script>
</body>
</html>
