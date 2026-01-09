<!-- Vista de Gestión de Vacaciones -->

<div class="mb-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="<?php echo BASE_URL; ?>asistencia" class="text-gray-600 hover:text-gray-900 mr-4">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Gestión de Vacaciones</h1>
                <p class="text-gray-600 mt-1">Administra solicitudes y días de vacaciones</p>
            </div>
        </div>
        <button onclick="openVacacionModal()" class="bg-gradient-sinforosa text-white px-4 py-2 rounded-lg hover:opacity-90">
            <i class="fas fa-plus mr-2"></i>Nueva Solicitud
        </button>
    </div>
</div>

<!-- Estadísticas -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-md p-4">
        <p class="text-sm text-gray-600">Solicitudes Pendientes</p>
        <p class="text-3xl font-bold text-yellow-600"><?php echo count(array_filter($solicitudes, fn($s) => $s['estatus'] === 'Pendiente')); ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-4">
        <p class="text-sm text-gray-600">Aprobadas</p>
        <p class="text-3xl font-bold text-green-600"><?php echo count(array_filter($solicitudes, fn($s) => $s['estatus'] === 'Aprobada')); ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-4">
        <p class="text-sm text-gray-600">Rechazadas</p>
        <p class="text-3xl font-bold text-red-600"><?php echo count(array_filter($solicitudes, fn($s) => $s['estatus'] === 'Rechazada')); ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-4">
        <p class="text-sm text-gray-600">Total Solicitudes</p>
        <p class="text-3xl font-bold text-blue-600"><?php echo count($solicitudes); ?></p>
    </div>
</div>

<!-- Tabla de Solicitudes -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Solicitudes de Vacaciones</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Empleado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Departamento</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Inicio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Fin</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Días</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estatus</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($solicitudes)): ?>
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                        No hay solicitudes de vacaciones
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($solicitudes as $solicitud): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        <?php echo htmlspecialchars($solicitud['nombre_empleado']); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?php echo htmlspecialchars($solicitud['departamento']); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <?php echo date('d/m/Y', strtotime($solicitud['fecha_inicio'])); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <?php echo date('d/m/Y', strtotime($solicitud['fecha_fin'])); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <?php echo $solicitud['dias_solicitados']; ?> días
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php
                        $colors = [
                            'Pendiente' => 'bg-yellow-100 text-yellow-800',
                            'Aprobada' => 'bg-green-100 text-green-800',
                            'Rechazada' => 'bg-red-100 text-red-800',
                            'Cancelada' => 'bg-gray-100 text-gray-800'
                        ];
                        $color = $colors[$solicitud['estatus']] ?? 'bg-gray-100 text-gray-800';
                        ?>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $color; ?>">
                            <?php echo htmlspecialchars($solicitud['estatus']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <?php if ($solicitud['estatus'] === 'Pendiente'): ?>
                        <button class="text-green-600 hover:text-green-900 mr-2" title="Aprobar" onclick="aprobarSolicitud('<?php echo $solicitud['id'] ?? ''; ?>')">
                            <i class="fas fa-check"></i>
                        </button>
                        <button class="text-red-600 hover:text-red-900" title="Rechazar" onclick="rechazarSolicitud('<?php echo $solicitud['id'] ?? ''; ?>')">
                            <i class="fas fa-times"></i>
                        </button>
                        <?php else: ?>
                        <button class="text-blue-600 hover:text-blue-900" title="Ver detalles" onclick="verDetalles('<?php echo $solicitud['id'] ?? ''; ?>')">
                            <i class="fas fa-eye"></i>
                        </button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para Nueva Solicitud de Vacaciones -->
<div id="vacacionModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-gray-800">Nueva Solicitud de Vacaciones</h3>
                <button onclick="closeVacacionModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <form id="vacacionForm" class="p-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Empleado</label>
                    <select id="vacacionEmpleado" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" required>
                        <option value="">Seleccione un empleado...</option>
                        <!-- Aquí se cargarían los empleados dinámicamente -->
                    </select>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Inicio</label>
                        <input type="date" id="vacacionFechaInicio" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Fin</label>
                        <input type="date" id="vacacionFechaFin" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Días Solicitados</label>
                    <input type="number" id="vacacionDias" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" min="1" readonly>
                    <p class="text-xs text-gray-500 mt-1">Se calculará automáticamente según las fechas</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Motivo/Comentarios</label>
                    <textarea id="vacacionMotivo" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" placeholder="Comentarios adicionales..."></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="closeVacacionModal()" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="submit" class="bg-gradient-sinforosa text-white px-6 py-3 rounded-lg hover:opacity-90">
                    <i class="fas fa-paper-plane mr-2"></i>Enviar Solicitud
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openVacacionModal() {
    document.getElementById('vacacionModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeVacacionModal() {
    document.getElementById('vacacionModal').classList.add('hidden');
    document.body.style.overflow = '';
}

function aprobarSolicitud(id) {
    if (confirm('¿Está seguro de que desea aprobar esta solicitud de vacaciones?')) {
        alert('Solicitud aprobada correctamente.\n\nEn una implementación completa, aquí se actualizaría el estado en la base de datos.');
    }
}

function rechazarSolicitud(id) {
    const motivo = prompt('Ingrese el motivo del rechazo:');
    if (motivo) {
        alert('Solicitud rechazada.\nMotivo: ' + motivo + '\n\nEn una implementación completa, aquí se actualizaría el estado en la base de datos.');
    }
}

function verDetalles(id) {
    alert('Ver detalles de la solicitud ID: ' + id + '\n\nEn una implementación completa, aquí se mostraría un modal con todos los detalles de la solicitud.');
}

// Calcular días automáticamente
document.getElementById('vacacionFechaInicio')?.addEventListener('change', calcularDias);
document.getElementById('vacacionFechaFin')?.addEventListener('change', calcularDias);

function calcularDias() {
    const inicio = document.getElementById('vacacionFechaInicio').value;
    const fin = document.getElementById('vacacionFechaFin').value;
    
    if (inicio && fin) {
        const fechaInicio = new Date(inicio);
        const fechaFin = new Date(fin);
        const dias = Math.ceil((fechaFin - fechaInicio) / (1000 * 60 * 60 * 24)) + 1;
        
        if (dias > 0) {
            document.getElementById('vacacionDias').value = dias;
        } else {
            document.getElementById('vacacionDias').value = '';
            alert('La fecha de fin debe ser posterior a la fecha de inicio');
        }
    }
}

// Manejar envío del formulario
document.getElementById('vacacionForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    alert('Solicitud de vacaciones enviada correctamente.\n\nEn una implementación completa, aquí se enviarían los datos al servidor.');
    closeVacacionModal();
});

// Cerrar modal al presionar ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeVacacionModal();
    }
});
</script>
