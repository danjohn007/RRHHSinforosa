<!-- Vista de Nómina -->

<div class="mb-6" data-aos="fade-down">
    <h1 class="text-2xl font-bold text-gray-800">Administración de Nómina</h1>
    <p class="text-gray-600 mt-1">Gestión y procesamiento de nómina</p>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6" data-aos="fade-up" data-aos-delay="0">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Períodos Registrados</p>
                <p class="text-2xl font-bold text-gray-800"><?php echo count($periodos); ?></p>
            </div>
            <i class="fas fa-calendar-alt text-3xl text-blue-500"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6" data-aos="fade-up" data-aos-delay="100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Período Activo</p>
                <p class="text-lg font-bold text-green-600">
                    <?php 
                    $activo = array_filter($periodos, fn($p) => $p['estatus'] === 'Abierto');
                    echo count($activo) > 0 ? 'Abierto' : 'Cerrado';
                    ?>
                </p>
            </div>
            <i class="fas fa-check-circle text-3xl text-green-500"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6" data-aos="fade-up" data-aos-delay="200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">En Proceso</p>
                <p class="text-2xl font-bold text-yellow-600">
                    <?php echo count(array_filter($periodos, fn($p) => $p['estatus'] === 'En Proceso')); ?>
                </p>
            </div>
            <i class="fas fa-spinner text-3xl text-yellow-500"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6" data-aos="fade-up" data-aos-delay="300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Procesados</p>
                <p class="text-2xl font-bold text-purple-600">
                    <?php echo count(array_filter($periodos, fn($p) => $p['estatus'] === 'Pagado')); ?>
                </p>
            </div>
            <i class="fas fa-money-bill-wave text-3xl text-purple-500"></i>
        </div>
    </div>
</div>

<!-- Acciones Rápidas -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <a href="<?php echo BASE_URL; ?>nomina/procesar" class="bg-gradient-sinforosa text-white rounded-lg p-6 hover:opacity-90 transition" data-aos="zoom-in" data-aos-delay="400">
        <i class="fas fa-calculator text-3xl mb-3"></i>
        <h3 class="text-lg font-semibold">Procesar Nómina</h3>
        <p class="text-sm text-purple-100 mt-2">Calcular y procesar período de nómina</p>
    </a>
    
    <a href="<?php echo BASE_URL; ?>nomina/recibos" class="bg-blue-600 text-white rounded-lg p-6 hover:bg-blue-700 transition" data-aos="zoom-in" data-aos-delay="500">
        <i class="fas fa-file-invoice text-3xl mb-3"></i>
        <h3 class="text-lg font-semibold">Recibos de Nómina</h3>
        <p class="text-sm text-blue-100 mt-2">Generar y consultar recibos</p>
    </a>
    
    <a href="<?php echo BASE_URL; ?>nomina/configuracion" class="bg-green-600 text-white rounded-lg p-6 hover:bg-green-700 transition" data-aos="zoom-in" data-aos-delay="600">
        <i class="fas fa-cog text-3xl mb-3"></i>
        <h3 class="text-lg font-semibold">Configuración</h3>
        <p class="text-sm text-green-100 mt-2">Percepciones y deducciones</p>
    </a>
</div>

<!-- Tabla de Períodos -->
<div class="bg-white rounded-lg shadow-md overflow-hidden" data-aos="fade-up" data-aos-delay="700">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800">Períodos de Nómina Recientes</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Período</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Pago</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Neto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estatus</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($periodos)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        No hay períodos de nómina registrados
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($periodos as $periodo): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm"><?php echo htmlspecialchars($periodo['tipo']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <?php echo date('d/m/Y', strtotime($periodo['fecha_inicio'])); ?> - 
                        <?php echo date('d/m/Y', strtotime($periodo['fecha_fin'])); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm"><?php echo date('d/m/Y', strtotime($periodo['fecha_pago'])); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">$<?php echo number_format($periodo['total_neto'], 2); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php
                        $colors = [
                            'Abierto' => 'bg-green-100 text-green-800',
                            'En Proceso' => 'bg-yellow-100 text-yellow-800',
                            'Procesado' => 'bg-blue-100 text-blue-800',
                            'Pagado' => 'bg-purple-100 text-purple-800',
                            'Cerrado' => 'bg-gray-100 text-gray-800'
                        ];
                        $color = $colors[$periodo['estatus']] ?? 'bg-gray-100 text-gray-800';
                        ?>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $color; ?>">
                            <?php echo htmlspecialchars($periodo['estatus']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <button onclick="verDetallePeriodo(<?php echo $periodo['id']; ?>)" class="text-blue-600 hover:text-blue-900 mr-3" title="Ver detalle">
                            <i class="fas fa-eye"></i>
                        </button>
                        <a href="<?php echo BASE_URL; ?>nomina/descargar?id=<?php echo $periodo['id']; ?>" class="text-green-600 hover:text-green-900" title="Descargar reporte" target="_blank">
                            <i class="fas fa-download"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal de Detalle de Período -->
<div id="modalDetallePeriodo" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-6xl w-full max-h-[90vh] overflow-y-auto">
        <div class="bg-gradient-to-r from-purple-600 to-purple-800 text-white p-6 rounded-t-lg">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-bold" id="modalTitulo">Detalle de Nómina</h2>
                    <p class="text-purple-100 mt-1" id="modalSubtitulo"></p>
                </div>
                <button onclick="cerrarModal()" class="text-white hover:text-gray-200">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
        </div>
        
        <div class="p-6" id="modalContenido">
            <div class="flex justify-center items-center py-8">
                <i class="fas fa-spinner fa-spin text-4xl text-purple-600"></i>
            </div>
        </div>
    </div>
</div>

<script>
function verDetallePeriodo(periodoId) {
    document.getElementById('modalDetallePeriodo').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Cargar detalle del período
    fetch(`<?php echo BASE_URL; ?>nomina/detalle?id=${periodoId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarDetallePeriodo(data);
            } else {
                document.getElementById('modalContenido').innerHTML = `
                    <div class="text-center py-8 text-red-600">
                        <i class="fas fa-exclamation-circle text-4xl mb-4"></i>
                        <p>${data.message}</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('modalContenido').innerHTML = `
                <div class="text-center py-8 text-red-600">
                    <i class="fas fa-exclamation-circle text-4xl mb-4"></i>
                    <p>Error al cargar el detalle</p>
                </div>
            `;
        });
}

function mostrarDetallePeriodo(data) {
    const periodo = data.periodo;
    const empleados = data.empleados || [];
    
    document.getElementById('modalTitulo').textContent = `Nómina ${periodo.tipo}`;
    document.getElementById('modalSubtitulo').textContent = 
        `Del ${formatearFecha(periodo.fecha_inicio)} al ${formatearFecha(periodo.fecha_fin)} - Pago: ${formatearFecha(periodo.fecha_pago)}`;
    
    let html = `
        <!-- Resumen -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                <p class="text-sm text-green-600 font-semibold">Total Percepciones</p>
                <p class="text-2xl font-bold text-green-700">$${parseFloat(periodo.total_percepciones || 0).toLocaleString('es-MX', {minimumFractionDigits: 2})}</p>
            </div>
            <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                <p class="text-sm text-red-600 font-semibold">Total Deducciones</p>
                <p class="text-2xl font-bold text-red-700">$${parseFloat(periodo.total_deducciones || 0).toLocaleString('es-MX', {minimumFractionDigits: 2})}</p>
            </div>
            <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                <p class="text-sm text-purple-600 font-semibold">Total Neto</p>
                <p class="text-2xl font-bold text-purple-700">$${parseFloat(periodo.total_neto || 0).toLocaleString('es-MX', {minimumFractionDigits: 2})}</p>
            </div>
        </div>
        
        <!-- Tabla de Empleados -->
        <div class="overflow-x-auto">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Detalle por Empleado</h3>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Empleado</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Percepciones</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deducciones</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Neto</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estatus</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
    `;
    
    if (empleados.length === 0) {
        html += `
            <tr>
                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                    No hay registros de nómina para este período
                </td>
            </tr>
        `;
    } else {
        empleados.forEach(emp => {
            html += `
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm">
                        <div class="font-medium text-gray-900">${emp.numero_empleado}</div>
                        <div class="text-gray-500">${emp.nombre_empleado}</div>
                    </td>
                    <td class="px-4 py-3 text-sm text-green-600 font-medium">
                        $${parseFloat(emp.total_percepciones || 0).toLocaleString('es-MX', {minimumFractionDigits: 2})}
                    </td>
                    <td class="px-4 py-3 text-sm text-red-600 font-medium">
                        $${parseFloat(emp.total_deducciones || 0).toLocaleString('es-MX', {minimumFractionDigits: 2})}
                    </td>
                    <td class="px-4 py-3 text-sm text-purple-600 font-bold">
                        $${parseFloat(emp.total_neto || 0).toLocaleString('es-MX', {minimumFractionDigits: 2})}
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold ${
                            emp.estatus === 'Pagado' ? 'bg-green-100 text-green-800' :
                            emp.estatus === 'Calculado' ? 'bg-blue-100 text-blue-800' :
                            'bg-gray-100 text-gray-800'
                        }">
                            ${emp.estatus}
                        </span>
                    </td>
                </tr>
            `;
        });
    }
    
    html += `
                </tbody>
            </table>
        </div>
    `;
    
    document.getElementById('modalContenido').innerHTML = html;
}

function cerrarModal() {
    document.getElementById('modalDetallePeriodo').classList.add('hidden');
    document.body.style.overflow = '';
}

function formatearFecha(fecha) {
    const d = new Date(fecha);
    const opciones = { year: 'numeric', month: 'long', day: 'numeric' };
    return d.toLocaleDateString('es-MX', opciones);
}

// Cerrar modal con ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModal();
    }
});
</script>
