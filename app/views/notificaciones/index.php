<?php
$title = 'Notificaciones';
ob_start();
?>

<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-bell text-purple-600 mr-2"></i>
            Notificaciones
        </h1>
        <p class="text-gray-600 mt-1">Centro de notificaciones del sistema</p>
    </div>

    <!-- Filtros rápidos -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="flex gap-2 flex-wrap">
            <button onclick="filtrarNotificaciones('todas')" class="filtro-notif-btn active px-4 py-2 rounded-lg transition" data-filtro="todas">
                <i class="fas fa-list mr-1"></i> Todas
            </button>
            <button onclick="filtrarNotificaciones('no-leidas')" class="filtro-notif-btn px-4 py-2 rounded-lg transition" data-filtro="no-leidas">
                <i class="fas fa-circle mr-1"></i> No leídas
            </button>
            <button onclick="filtrarNotificaciones('leidas')" class="filtro-notif-btn px-4 py-2 rounded-lg transition" data-filtro="leidas">
                <i class="far fa-circle mr-1"></i> Leídas
            </button>
            <div class="ml-auto">
                <button onclick="marcarTodasLeidasPagina()" class="text-blue-600 hover:text-blue-800 text-sm px-4 py-2">
                    <i class="fas fa-check-double mr-1"></i> Marcar todas como leídas
                </button>
            </div>
        </div>
    </div>

    <!-- Lista de notificaciones -->
    <div class="space-y-3">
        <?php foreach ($notificaciones as $notif): ?>
            <?php
                $colorClasses = [
                    'blue' => 'bg-blue-50 text-blue-600',
                    'green' => 'bg-green-50 text-green-600',
                    'purple' => 'bg-purple-50 text-purple-600',
                    'orange' => 'bg-orange-50 text-orange-600',
                    'gray' => 'bg-gray-50 text-gray-600'
                ];
                $colorClass = $colorClasses[$notif['color']] ?? 'bg-gray-50 text-gray-600';
                
                $fecha = new DateTime($notif['fecha']);
                $ahora = new DateTime();
                $diff = $ahora->getTimestamp() - $fecha->getTimestamp();
                $diffMinutos = floor($diff / 60);
                
                if ($diffMinutos < 1) $tiempoTexto = 'Hace un momento';
                elseif ($diffMinutos < 60) $tiempoTexto = "Hace $diffMinutos min";
                elseif ($diffMinutos < 1440) $tiempoTexto = 'Hace ' . floor($diffMinutos / 60) . ' h';
                else $tiempoTexto = 'Hace ' . floor($diffMinutos / 1440) . ' días';
            ?>
            
            <div class="notif-item bg-white rounded-lg shadow hover:shadow-md transition p-4 <?= !$notif['leida'] ? 'border-l-4 border-blue-500' : '' ?>"
                 data-leida="<?= $notif['leida'] ? 'true' : 'false' ?>">
                <div class="flex items-start gap-4">
                    <!-- Icono -->
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 rounded-full <?= $colorClass ?> flex items-center justify-center">
                            <i class="fas <?= $notif['icono'] ?> text-xl"></i>
                        </div>
                    </div>
                    
                    <!-- Contenido -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between">
                            <h3 class="text-base font-semibold text-gray-900 <?= !$notif['leida'] ? 'font-bold' : '' ?>">
                                <?= htmlspecialchars($notif['titulo']) ?>
                                <?php if (!$notif['leida']): ?>
                                    <span class="ml-2 inline-block h-2 w-2 bg-blue-600 rounded-full"></span>
                                <?php endif; ?>
                            </h3>
                            <span class="text-xs text-gray-500 ml-2 whitespace-nowrap"><?= $tiempoTexto ?></span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1"><?= htmlspecialchars($notif['mensaje']) ?></p>
                        <div class="flex items-center gap-3 mt-3">
                            <span class="text-xs text-gray-400">
                                <i class="far fa-calendar mr-1"></i>
                                <?= $fecha->format('d/m/Y H:i') ?>
                            </span>
                            <?php if (!$notif['leida']): ?>
                                <button onclick="marcarComoLeida(this)" class="text-xs text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-check mr-1"></i> Marcar como leída
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Mensaje vacío -->
    <?php if (empty($notificaciones)): ?>
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <i class="fas fa-bell-slash text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No hay notificaciones</h3>
            <p class="text-gray-500">Cuando recibas notificaciones aparecerán aquí</p>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require_once BASE_PATH . 'app/views/layouts/main.php';
?>

<script>
let filtroActivo = 'todas';
let notificacionesData = [];

// Cargar notificaciones desde localStorage
function cargarNotificacionesLocal() {
    const stored = localStorage.getItem('notificaciones_sistema');
    if (stored) {
        notificacionesData = JSON.parse(stored);
        renderizarNotificaciones();
    }
}

function guardarNotificacionesLocal() {
    localStorage.setItem('notificaciones_sistema', JSON.stringify(notificacionesData));
    window.dispatchEvent(new CustomEvent('notificacionesActualizadas'));
}

function renderizarNotificaciones() {
    // Actualizar el DOM con las notificaciones actuales
    notificacionesData.forEach(notif => {
        const items = document.querySelectorAll('.notif-item');
        items.forEach(item => {
            const titulo = item.querySelector('h3');
            if (titulo && titulo.textContent.includes(notif.titulo)) {
                item.dataset.leida = notif.leida ? 'true' : 'false';
                
                if (notif.leida) {
                    item.classList.remove('border-l-4', 'border-blue-500');
                    titulo.classList.remove('font-bold');
                    const punto = titulo.querySelector('.bg-blue-600');
                    if (punto) punto.remove();
                    const btn = item.querySelector('button');
                    if (btn) btn.remove();
                }
            }
        });
    });
}

function filtrarNotificaciones(filtro) {
    filtroActivo = filtro;
    
    // Actualizar botones
    document.querySelectorAll('.filtro-notif-btn').forEach(btn => {
        btn.classList.remove('active', 'bg-blue-500', 'text-white');
        btn.classList.add('bg-gray-100', 'text-gray-700');
    });
    
    const btnActivo = document.querySelector(`[data-filtro="${filtro}"]`);
    btnActivo.classList.remove('bg-gray-100', 'text-gray-700');
    btnActivo.classList.add('active', 'bg-blue-500', 'text-white');
    
    // Filtrar items
    const items = document.querySelectorAll('.notif-item');
    items.forEach(item => {
        const leida = item.dataset.leida === 'true';
        
        if (filtro === 'todas') {
            item.classList.remove('hidden');
        } else if (filtro === 'no-leidas') {
            item.classList.toggle('hidden', leida);
        } else if (filtro === 'leidas') {
            item.classList.toggle('hidden', !leida);
        }
    });
}

function marcarComoLeida(btn) {
    const item = btn.closest('.notif-item');
    const titulo = item.querySelector('h3').textContent.trim();
    
    // Actualizar en localStorage
    const notif = notificacionesData.find(n => titulo.includes(n.titulo));
    if (notif) {
        notif.leida = true;
        guardarNotificacionesLocal();
    }
    
    item.dataset.leida = 'true';
    item.classList.remove('border-l-4', 'border-blue-500');
    
    // Remover punto azul y botón
    const tituloElement = item.querySelector('h3');
    tituloElement.classList.remove('font-bold');
    const punto = tituloElement.querySelector('.bg-blue-600');
    if (punto) punto.remove();
    
    btn.remove();
    
    Notificacion.exito('Notificación marcada como leída');
}

function marcarTodasLeidasPagina() {
    const items = document.querySelectorAll('.notif-item[data-leida="false"]');
    
    // Actualizar en localStorage
    notificacionesData.forEach(n => n.leida = true);
    guardarNotificacionesLocal();
    
    items.forEach(item => {
        item.dataset.leida = 'true';
        item.classList.remove('border-l-4', 'border-blue-500');
        
        const titulo = item.querySelector('h3');
        titulo.classList.remove('font-bold');
        const punto = titulo.querySelector('.bg-blue-600');
        if (punto) punto.remove();
        
        const btn = item.querySelector('button');
        if (btn) btn.remove();
    });
    
    if (items.length > 0) {
        Notificacion.exito(`${items.length} notificación(es) marcadas como leídas`);
    }
}

// Cargar notificaciones al iniciar
document.addEventListener('DOMContentLoaded', function() {
    cargarNotificacionesLocal();
});
</script>

<style>
.filtro-notif-btn.active {
    @apply bg-blue-500 text-white;
}
.filtro-notif-btn:not(.active) {
    @apply bg-gray-100 text-gray-700 hover:bg-gray-200;
}
</style>
