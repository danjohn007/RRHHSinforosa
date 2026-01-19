<?php
$title = 'Registro de Errores';

// Funciones helper para la vista
function getColorBorder($tipo) {
    switch ($tipo) {
        case 'fatal':
        case 'parse':
            return 'border-red-600';
        case 'warning':
            return 'border-orange-500';
        case 'notice':
        case 'deprecated':
            return 'border-yellow-500';
        case 'error':
            return 'border-red-500';
        default:
            return 'border-blue-500';
    }
}

function getBadgeClass($tipo) {
    switch ($tipo) {
        case 'fatal':
        case 'parse':
            return 'bg-red-100 text-red-800';
        case 'warning':
            return 'bg-orange-100 text-orange-800';
        case 'notice':
        case 'deprecated':
            return 'bg-yellow-100 text-yellow-800';
        case 'error':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-blue-100 text-blue-800';
    }
}

function getIconoTipo($tipo) {
    switch ($tipo) {
        case 'fatal':
        case 'parse':
            return '<i class="fas fa-times-circle mr-1"></i>';
        case 'warning':
            return '<i class="fas fa-exclamation-triangle mr-1"></i>';
        case 'notice':
        case 'deprecated':
            return '<i class="fas fa-info-circle mr-1"></i>';
        case 'error':
            return '<i class="fas fa-exclamation-circle mr-1"></i>';
        default:
            return '<i class="fas fa-info mr-1"></i>';
    }
}

ob_start();
?>

<!-- Header -->
<div class="mb-6">
    <div class="flex justify-between items-center mb-4" data-aos="fade-down">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-bug text-red-500 mr-2"></i>
                Registro de Errores
            </h1>
            <p class="text-gray-600 mt-1">Monitor de errores del sistema en tiempo real</p>
            <p class="text-gray-500 text-sm mt-1" id="ultima-actualizacion">Última actualización: <?= date('H:i:s') ?></p>
        </div>
        <div class="flex gap-2">
            <button onclick="actualizarErrores()" id="btn-actualizar"
                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg inline-flex items-center gap-2 transition">
                <i class="fas fa-sync-alt" id="icono-actualizar"></i>
                Actualizar
            </button>
            <a href="<?= BASE_URL ?>errores/descargar" 
               class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg inline-flex items-center gap-2 transition">
                <i class="fas fa-download"></i>
                Descargar Log
            </a>
            <button onclick="confirmarLimpiar()" 
                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg inline-flex items-center gap-2 transition">
                <i class="fas fa-trash-alt"></i>
                Limpiar Log
            </button>
        </div>
    </div>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?= $_SESSION['mensaje'] ?>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-gray-500" data-aos="fade-up" data-aos-delay="0">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Errores</p>
                    <p class="text-2xl font-bold text-gray-900" data-stat="total"><?= $estadisticas['total'] ?></p>
                </div>
                <i class="fas fa-list text-gray-400 text-3xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-600" data-aos="fade-up" data-aos-delay="100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Fatal / Parse</p>
                    <p class="text-2xl font-bold text-red-600" data-stat="fatal"><?= $estadisticas['fatal'] ?></p>
                </div>
                <i class="fas fa-times-circle text-red-400 text-3xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-orange-500" data-aos="fade-up" data-aos-delay="200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Warnings</p>
                    <p class="text-2xl font-bold text-orange-600" data-stat="warning"><?= $estadisticas['warning'] ?></p>
                </div>
                <i class="fas fa-exclamation-triangle text-orange-400 text-3xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500" data-aos="fade-up" data-aos-delay="300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Notice / Deprecated</p>
                    <p class="text-2xl font-bold text-yellow-600" data-stat="notice"><?= $estadisticas['notice'] ?></p>
                </div>
                <i class="fas fa-info-circle text-yellow-400 text-3xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500" data-aos="fade-up" data-aos-delay="400">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Tamaño Log</p>
                    <p class="text-2xl font-bold text-blue-600"><?= number_format($tamano_archivo / 1024, 1) ?> KB</p>
                </div>
                <i class="fas fa-file-alt text-blue-400 text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow p-4 mb-6" data-aos="fade-up" data-aos-delay="500">
        <div class="flex flex-wrap gap-2 items-center">
            <button onclick="filtrarTipo('todos')" class="filtro-btn active px-4 py-2 rounded-lg transition" data-tipo="todos">
                <i class="fas fa-list mr-1"></i> Todos (<?= $estadisticas['total'] ?>)
            </button>
            <button onclick="filtrarTipo('fatal')" class="filtro-btn px-4 py-2 rounded-lg transition" data-tipo="fatal">
                <i class="fas fa-times-circle mr-1"></i> Fatal (<?= $estadisticas['fatal'] ?>)
            </button>
            <button onclick="filtrarTipo('warning')" class="filtro-btn px-4 py-2 rounded-lg transition" data-tipo="warning">
                <i class="fas fa-exclamation-triangle mr-1"></i> Warning (<?= $estadisticas['warning'] ?>)
            </button>
            <button onclick="filtrarTipo('notice')" class="filtro-btn px-4 py-2 rounded-lg transition" data-tipo="notice">
                <i class="fas fa-info-circle mr-1"></i> Notice (<?= $estadisticas['notice'] ?>)
            </button>
            <div class="ml-auto flex items-center gap-3">
                <select id="items-por-pagina" onchange="cambiarItemsPorPagina()" class="border rounded-lg px-3 py-2 text-sm">
                    <option value="10">10 por página</option>
                    <option value="20" selected>20 por página</option>
                    <option value="50">50 por página</option>
                    <option value="100">100 por página</option>
                </select>
                <input type="text" id="buscar" placeholder="Buscar en errores..." 
                       class="border rounded-lg px-4 py-2 w-64" onkeyup="buscarError()">
            </div>
        </div>
    </div>

    <!-- Paginación Superior -->
    <div id="paginacion-superior" class="bg-white rounded-lg shadow p-4 mb-4 hidden">
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-600" id="info-paginacion-superior">
                <!-- Se llenará con JavaScript -->
            </div>
            <div class="flex gap-2" id="controles-paginacion-superior">
                <!-- Se llenará con JavaScript -->
            </div>
        </div>
    </div>

    <?php if (!$archivo_existe): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-8 rounded text-center">
            <i class="fas fa-check-circle text-5xl mb-4"></i>
            <h3 class="text-xl font-bold mb-2">¡No hay errores registrados!</h3>
            <p>El archivo error_log no existe o está vacío.</p>
        </div>
    <?php elseif (empty($errores)): ?>
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-6 py-8 rounded text-center">
            <i class="fas fa-info-circle text-5xl mb-4"></i>
            <h3 class="text-xl font-bold mb-2">Log limpio</h3>
            <p>No se encontraron errores en el archivo actual.</p>
        </div>
    <?php else: ?>
        <!-- Lista de Errores -->
        <div class="space-y-3" id="lista-errores">
            <?php foreach ($errores as $index => $error): ?>
                <div class="error-item bg-white rounded-lg shadow hover:shadow-md transition border-l-4 <?= getColorBorder($error['tipo']) ?>" 
                     data-tipo="<?= $error['tipo'] ?>">
                    <div class="p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <!-- Badge de tipo -->
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= getBadgeClass($error['tipo']) ?> mr-2">
                                    <?= getIconoTipo($error['tipo']) ?>
                                    <?= strtoupper($error['tipo']) ?>
                                </span>
                                
                                <!-- Fecha -->
                                <span class="text-gray-500 text-sm">
                                    <i class="far fa-clock mr-1"></i>
                                    <?= $error['fecha'] ?>
                                </span>
                                
                                <?php if ($error['archivo']): ?>
                                    <span class="text-gray-500 text-sm ml-3">
                                        <i class="far fa-file-code mr-1"></i>
                                        <?= $error['archivo'] ?>
                                        <?php if ($error['linea_num']): ?>
                                            <span class="text-blue-600">:<?= $error['linea_num'] ?></span>
                                        <?php endif; ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <button onclick="toggleDetalle(<?= $index ?>)" class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-chevron-down" id="icono-<?= $index ?>"></i>
                            </button>
                        </div>
                        
                        <!-- Mensaje resumido -->
                        <div class="mt-2">
                            <p class="text-gray-800 font-mono text-sm line-clamp-2" id="mensaje-corto-<?= $index ?>">
                                <?= htmlspecialchars(substr($error['mensaje'], 0, 200)) ?>
                                <?php if (strlen($error['mensaje']) > 200): ?>...<?php endif; ?>
                            </p>
                        </div>
                        
                        <!-- Mensaje completo (oculto) -->
                        <div class="mt-3 hidden" id="detalle-<?= $index ?>">
                            <div class="bg-gray-100 rounded p-3 font-mono text-xs text-gray-800 overflow-x-auto">
                                <pre class="whitespace-pre-wrap"><?= htmlspecialchars($error['mensaje']) ?></pre>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div id="sin-resultados" class="hidden bg-yellow-100 border border-yellow-400 text-yellow-700 px-6 py-8 rounded text-center mt-6">
            <i class="fas fa-search text-5xl mb-4"></i>
            <h3 class="text-xl font-bold mb-2">No se encontraron resultados</h3>
            <p>No hay errores que coincidan con tu búsqueda o filtro.</p>
        </div>

        <!-- Paginación Inferior -->
        <div id="paginacion-inferior" class="bg-white rounded-lg shadow p-4 mt-4 hidden">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600" id="info-paginacion-inferior">
                    <!-- Se llenará con JavaScript -->
                </div>
                <div class="flex gap-2" id="controles-paginacion-inferior">
                    <!-- Se llenará con JavaScript -->
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require_once BASE_PATH . 'app/views/layouts/main.php';
?>

<script>
let tipoActivo = 'todos';
let datosActuales = null;
let paginaActual = 1;
let itemsPorPagina = 20;
let erroresFiltrados = [];

function actualizarErrores() {
    const btnActualizar = document.getElementById('btn-actualizar');
    const iconoActualizar = document.getElementById('icono-actualizar');
    
    // Animación de carga
    btnActualizar.disabled = true;
    iconoActualizar.classList.add('fa-spin');
    
    fetch('<?= BASE_URL ?>errores/obtener-json')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                datosActuales = data;
                renderizarErrores(data);
                actualizarEstadisticas(data.estadisticas);
                document.getElementById('ultima-actualizacion').textContent = 
                    `Última actualización: ${new Date().toLocaleTimeString()}`;
                Notificacion.exito('Datos actualizados correctamente');
            }
        })
        .catch(error => {
            console.error('Error al actualizar:', error);
            Notificacion.error('Error al actualizar los datos');
        })
        .finally(() => {
            btnActualizar.disabled = false;
            iconoActualizar.classList.remove('fa-spin');
        });
}

function renderizarErrores(data) {
    const listaErrores = document.getElementById('lista-errores');
    const sinResultados = document.getElementById('sin-resultados');
    
    if (!data.archivo_existe || data.errores.length === 0) {
        listaErrores.innerHTML = '';
        listaErrores.innerHTML = data.archivo_existe ? 
            `<div class="bg-blue-100 border border-blue-400 text-blue-700 px-6 py-8 rounded text-center">
                <i class="fas fa-info-circle text-5xl mb-4"></i>
                <h3 class="text-xl font-bold mb-2">Log limpio</h3>
                <p>No se encontraron errores en el archivo actual.</p>
            </div>` :
            `<div class="bg-green-100 border border-green-400 text-green-700 px-6 py-8 rounded text-center">
                <i class="fas fa-check-circle text-5xl mb-4"></i>
                <h3 class="text-xl font-bold mb-2">¡No hay errores registrados!</h3>
                <p>El archivo error_log no existe o está vacío.</p>
            </div>`;
        return;
    }
    
    let html = '';
    data.errores.forEach((error, index) => {
        const colorBorder = getColorBorderJS(error.tipo);
        const badgeClass = getBadgeClassJS(error.tipo);
        const iconoTipo = getIconoTipoJS(error.tipo);
        const mensajeCorto = error.mensaje.length > 200 ? 
            error.mensaje.substring(0, 200) + '...' : error.mensaje;
        
        html += `
            <div class="error-item bg-white rounded-lg shadow hover:shadow-md transition border-l-4 ${colorBorder}" 
                 data-tipo="${error.tipo}">
                <div class="p-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${badgeClass} mr-2">
                                ${iconoTipo}
                                ${error.tipo.toUpperCase()}
                            </span>
                            <span class="text-gray-500 text-sm">
                                <i class="far fa-clock mr-1"></i>
                                ${error.fecha}
                            </span>
                            ${error.archivo ? `
                                <span class="text-gray-500 text-sm ml-3">
                                    <i class="far fa-file-code mr-1"></i>
                                    ${error.archivo}
                                    ${error.linea_num ? `<span class="text-blue-600">:${error.linea_num}</span>` : ''}
                                </span>
                            ` : ''}
                        </div>
                        <button onclick="toggleDetalle(${index})" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-chevron-down" id="icono-${index}"></i>
                        </button>
                    </div>
                    <div class="mt-2">
                        <p class="text-gray-800 font-mono text-sm line-clamp-2" id="mensaje-corto-${index}">
                            ${escapeHtml(mensajeCorto)}
                        </p>
                    </div>
                    <div class="mt-3 hidden" id="detalle-${index}">
                        <div class="bg-gray-100 rounded p-3 font-mono text-xs text-gray-800 overflow-x-auto">
                            <pre class="whitespace-pre-wrap">${escapeHtml(error.mensaje)}</pre>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    listaErrores.innerHTML = html;
    aplicarFiltros();
}

function actualizarEstadisticas(stats) {
    document.querySelector('[data-stat="total"]').textContent = stats.total || 0;
    document.querySelector('[data-stat="fatal"]').textContent = stats.fatal || 0;
    document.querySelector('[data-stat="warning"]').textContent = stats.warning || 0;
    document.querySelector('[data-stat="notice"]').textContent = stats.notice || 0;
    
    // Actualizar botones de filtro
    document.querySelector('[data-tipo="todos"]').innerHTML = 
        `<i class="fas fa-list mr-1"></i> Todos (${stats.total || 0})`;
    document.querySelector('[data-tipo="fatal"]').innerHTML = 
        `<i class="fas fa-times-circle mr-1"></i> Fatal (${stats.fatal || 0})`;
    document.querySelector('[data-tipo="warning"]').innerHTML = 
        `<i class="fas fa-exclamation-triangle mr-1"></i> Warning (${stats.warning || 0})`;
    document.querySelector('[data-tipo="notice"]').innerHTML = 
        `<i class="fas fa-info-circle mr-1"></i> Notice (${stats.notice || 0})`;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function getColorBorderJS(tipo) {
    switch (tipo) {
        case 'fatal':
        case 'parse':
            return 'border-red-600';
        case 'warning':
            return 'border-orange-500';
        case 'notice':
        case 'deprecated':
            return 'border-yellow-500';
        case 'error':
            return 'border-red-500';
        default:
            return 'border-blue-500';
    }
}

function getBadgeClassJS(tipo) {
    switch (tipo) {
        case 'fatal':
        case 'parse':
            return 'bg-red-100 text-red-800';
        case 'warning':
            return 'bg-orange-100 text-orange-800';
        case 'notice':
        case 'deprecated':
            return 'bg-yellow-100 text-yellow-800';
        case 'error':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-blue-100 text-blue-800';
    }
}

function getIconoTipoJS(tipo) {
    switch (tipo) {
        case 'fatal':
        case 'parse':
            return '<i class="fas fa-times-circle mr-1"></i>';
        case 'warning':
            return '<i class="fas fa-exclamation-triangle mr-1"></i>';
        case 'notice':
        case 'deprecated':
            return '<i class="fas fa-info-circle mr-1"></i>';
        case 'error':
            return '<i class="fas fa-exclamation-circle mr-1"></i>';
        default:
            return '<i class="fas fa-info mr-1"></i>';
    }
}

function filtrarTipo(tipo) {
    tipoActivo = tipo;
    
    // Actualizar botones
    document.querySelectorAll('.filtro-btn').forEach(btn => {
        btn.classList.remove('active', 'bg-blue-500', 'text-white');
        btn.classList.add('bg-gray-100', 'text-gray-700');
    });
    
    const btnActivo = document.querySelector(`[data-tipo="${tipo}"]`);
    btnActivo.classList.remove('bg-gray-100', 'text-gray-700');
    btnActivo.classList.add('active', 'bg-blue-500', 'text-white');
    
    aplicarFiltros();
}

function buscarError() {
    aplicarFiltros();
}

function aplicarFiltros() {
    const busqueda = document.getElementById('buscar').value.toLowerCase();
    const items = document.querySelectorAll('.error-item');
    
    // Filtrar items
    erroresFiltrados = [];
    items.forEach(item => {
        const tipo = item.dataset.tipo;
        const texto = item.textContent.toLowerCase();
        
        const coincideTipo = tipoActivo === 'todos' || tipo === tipoActivo || 
                            (tipoActivo === 'fatal' && tipo === 'parse') ||
                            (tipoActivo === 'notice' && tipo === 'deprecated');
        const coincideBusqueda = busqueda === '' || texto.includes(busqueda);
        
        if (coincideTipo && coincideBusqueda) {
            erroresFiltrados.push(item);
        }
    });
    
    // Resetear a página 1 cuando se filtra
    paginaActual = 1;
    
    // Mostrar sin resultados si no hay items
    document.getElementById('sin-resultados').classList.toggle('hidden', erroresFiltrados.length > 0);
    
    // Aplicar paginación
    aplicarPaginacion();
}

function aplicarPaginacion() {
    const totalItems = erroresFiltrados.length;
    const totalPaginas = Math.ceil(totalItems / itemsPorPagina);
    
    // Ocultar todos los items primero
    document.querySelectorAll('.error-item').forEach(item => {
        item.classList.add('hidden');
    });
    
    // Mostrar solo items de la página actual
    const inicio = (paginaActual - 1) * itemsPorPagina;
    const fin = inicio + itemsPorPagina;
    
    erroresFiltrados.slice(inicio, fin).forEach(item => {
        item.classList.remove('hidden');
    });
    
    // Actualizar controles de paginación
    actualizarPaginacion(totalItems, totalPaginas);
}

function actualizarPaginacion(totalItems, totalPaginas) {
    const mostrarPaginacion = totalItems > itemsPorPagina;
    
    document.getElementById('paginacion-superior').classList.toggle('hidden', !mostrarPaginacion);
    document.getElementById('paginacion-inferior').classList.toggle('hidden', !mostrarPaginacion);
    
    if (!mostrarPaginacion) return;
    
    const inicio = (paginaActual - 1) * itemsPorPagina + 1;
    const fin = Math.min(paginaActual * itemsPorPagina, totalItems);
    
    const infoHTML = `Mostrando ${inicio} a ${fin} de ${totalItems} errores`;
    document.getElementById('info-paginacion-superior').innerHTML = infoHTML;
    document.getElementById('info-paginacion-inferior').innerHTML = infoHTML;
    
    const controlesHTML = generarControlesPaginacion(totalPaginas);
    document.getElementById('controles-paginacion-superior').innerHTML = controlesHTML;
    document.getElementById('controles-paginacion-inferior').innerHTML = controlesHTML;
}

function generarControlesPaginacion(totalPaginas) {
    let html = '';
    
    // Botón anterior
    html += `<button onclick="irAPagina(${paginaActual - 1})" 
                     class="px-3 py-1 rounded border ${paginaActual === 1 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white hover:bg-gray-50'}"
                     ${paginaActual === 1 ? 'disabled' : ''}>
                <i class="fas fa-chevron-left"></i>
             </button>`;
    
    // Números de página
    const rango = 2; // Mostrar 2 páginas a cada lado de la actual
    let inicio = Math.max(1, paginaActual - rango);
    let fin = Math.min(totalPaginas, paginaActual + rango);
    
    // Primera página
    if (inicio > 1) {
        html += `<button onclick="irAPagina(1)" class="px-3 py-1 rounded border bg-white hover:bg-gray-50">1</button>`;
        if (inicio > 2) {
            html += `<span class="px-2 text-gray-400">...</span>`;
        }
    }
    
    // Páginas del rango
    for (let i = inicio; i <= fin; i++) {
        html += `<button onclick="irAPagina(${i})" 
                         class="px-3 py-1 rounded border ${i === paginaActual ? 'bg-blue-500 text-white' : 'bg-white hover:bg-gray-50'}">
                    ${i}
                 </button>`;
    }
    
    // Última página
    if (fin < totalPaginas) {
        if (fin < totalPaginas - 1) {
            html += `<span class="px-2 text-gray-400">...</span>`;
        }
        html += `<button onclick="irAPagina(${totalPaginas})" class="px-3 py-1 rounded border bg-white hover:bg-gray-50">${totalPaginas}</button>`;
    }
    
    // Botón siguiente
    html += `<button onclick="irAPagina(${paginaActual + 1})" 
                     class="px-3 py-1 rounded border ${paginaActual === totalPaginas ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white hover:bg-gray-50'}"
                     ${paginaActual === totalPaginas ? 'disabled' : ''}>
                <i class="fas fa-chevron-right"></i>
             </button>`;
    
    return html;
}

function irAPagina(pagina) {
    const totalPaginas = Math.ceil(erroresFiltrados.length / itemsPorPagina);
    
    if (pagina < 1 || pagina > totalPaginas) return;
    
    paginaActual = pagina;
    aplicarPaginacion();
    
    // Scroll al inicio de la lista
    document.getElementById('lista-errores').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function cambiarItemsPorPagina() {
    itemsPorPagina = parseInt(document.getElementById('items-por-pagina').value);
    paginaActual = 1;
    aplicarPaginacion();
}

function toggleDetalle(index) {
    const detalle = document.getElementById(`detalle-${index}`);
    const icono = document.getElementById(`icono-${index}`);
    
    detalle.classList.toggle('hidden');
    icono.classList.toggle('fa-chevron-down');
    icono.classList.toggle('fa-chevron-up');
}

function confirmarLimpiar() {
    Modal.confirmar({
        titulo: '¿Limpiar archivo de errores?',
        mensaje: 'Se creará un backup antes de limpiar. Esta acción no se puede deshacer.',
        textoConfirmar: 'Sí, limpiar',
        textoConfirmar: 'Cancelar',
        onConfirmar: () => {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= BASE_URL ?>errores/limpiar';
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Inicializar paginación al cargar
document.addEventListener('DOMContentLoaded', function() {
    aplicarFiltros();
});

// Auto-refrescar cada 30 segundos (sin recargar página)
setInterval(() => {
    actualizarErrores();
}, 30000);
</script>

<style>
.filtro-btn.active {
    @apply bg-blue-500 text-white;
}
.filtro-btn:not(.active) {
    @apply bg-gray-100 text-gray-700 hover:bg-gray-200;
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
