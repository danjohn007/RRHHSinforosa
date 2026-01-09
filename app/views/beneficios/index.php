<!-- Vista de Beneficios -->

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Gestión de Beneficios</h1>
    <p class="text-gray-600 mt-1">Administra préstamos, bonos y apoyos especiales</p>
</div>

<!-- Tabs -->
<div class="mb-6">
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
            <button onclick="showTab('prestamos')" id="tab-prestamos" class="tab-button active border-b-2 border-purple-500 py-4 px-1 text-center font-medium text-sm text-purple-600">
                <i class="fas fa-hand-holding-usd mr-2"></i>
                Préstamos
            </button>
            <button onclick="showTab('bonos')" id="tab-bonos" class="tab-button border-b-2 border-transparent py-4 px-1 text-center font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                <i class="fas fa-gift mr-2"></i>
                Bonos y Apoyos
            </button>
        </nav>
    </div>
</div>

<!-- Préstamos -->
<div id="content-prestamos" class="tab-content">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-sm text-gray-600">Préstamos Activos</p>
            <p class="text-3xl font-bold text-blue-600"><?php echo count(array_filter($prestamos, fn($p) => $p['estatus'] === 'Activo')); ?></p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-sm text-gray-600">Total Prestado</p>
            <p class="text-3xl font-bold text-purple-600">
                $<?php echo number_format(array_sum(array_column($prestamos, 'monto_total')), 2); ?>
            </p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-sm text-gray-600">Total Pendiente</p>
            <p class="text-3xl font-bold text-yellow-600">
                $<?php echo number_format(array_sum(array_column($prestamos, 'monto_pendiente')), 2); ?>
            </p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Préstamos Activos</h2>
            <button onclick="openPrestamoModal()" class="bg-gradient-sinforosa text-white px-4 py-2 rounded-lg hover:opacity-90">
                <i class="fas fa-plus mr-2"></i>Nuevo Préstamo
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Empleado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Departamento</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pendiente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pagos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estatus</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($prestamos)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            No hay préstamos registrados
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($prestamos as $prestamo): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <?php echo htmlspecialchars($prestamo['nombre_empleado']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo htmlspecialchars($prestamo['departamento']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            $<?php echo number_format($prestamo['monto_total'], 2); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-600 font-semibold">
                            $<?php echo number_format($prestamo['monto_pendiente'], 2); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo $prestamo['pagos_realizados']; ?>/<?php echo $prestamo['numero_pagos']; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo date('d/m/Y', strtotime($prestamo['fecha_otorgamiento'])); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php
                            $colors = [
                                'Activo' => 'bg-blue-100 text-blue-800',
                                'Pagado' => 'bg-green-100 text-green-800',
                                'Cancelado' => 'bg-red-100 text-red-800'
                            ];
                            $color = $colors[$prestamo['estatus']] ?? 'bg-gray-100 text-gray-800';
                            ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $color; ?>">
                                <?php echo htmlspecialchars($prestamo['estatus']); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Bonos -->
<div id="content-bonos" class="tab-content hidden">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-sm text-gray-600">Bonos Otorgados</p>
            <p class="text-3xl font-bold text-green-600"><?php echo count($bonos); ?></p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-sm text-gray-600">Total en Bonos</p>
            <p class="text-3xl font-bold text-purple-600">
                $<?php echo number_format(array_sum(array_column($bonos, 'monto')), 2); ?>
            </p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-sm text-gray-600">Este Mes</p>
            <p class="text-3xl font-bold text-blue-600">
                <?php 
                $thisMonth = array_filter($bonos, fn($b) => date('Y-m', strtotime($b['fecha_otorgamiento'])) === date('Y-m'));
                echo count($thisMonth);
                ?>
            </p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Bonos y Apoyos Recientes</h2>
            <button onclick="openBonoModal()" class="bg-gradient-sinforosa text-white px-4 py-2 rounded-lg hover:opacity-90">
                <i class="fas fa-plus mr-2"></i>Nuevo Bono
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Empleado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Departamento</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo de Bono</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($bonos)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            No hay bonos registrados
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($bonos as $bono): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <?php echo htmlspecialchars($bono['nombre_empleado']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo htmlspecialchars($bono['departamento']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                <?php echo htmlspecialchars($bono['tipo_bono']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                            $<?php echo number_format($bono['monto'], 2); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo date('d/m/Y', strtotime($bono['fecha_otorgamiento'])); ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <?php echo htmlspecialchars($bono['descripcion'] ?? '-'); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function showTab(tab) {
    // Hide all contents
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-button').forEach(el => {
        el.classList.remove('active', 'border-purple-500', 'text-purple-600');
        el.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected content
    document.getElementById('content-' + tab).classList.remove('hidden');
    const btn = document.getElementById('tab-' + tab);
    btn.classList.add('active', 'border-purple-500', 'text-purple-600');
    btn.classList.remove('border-transparent', 'text-gray-500');
}
</script>

<!-- Modal para Nuevo Préstamo -->
<div id="prestamoModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-gray-800">Nuevo Préstamo</h3>
                <button onclick="closePrestamoModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <form id="prestamoForm" class="p-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Empleado</label>
                    <select id="prestamoEmpleado" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" required>
                        <option value="">Seleccione un empleado...</option>
                        <!-- Aquí se cargarían los empleados dinámicamente -->
                    </select>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Monto del Préstamo</label>
                        <input type="number" id="prestamoMonto" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" step="0.01" min="0" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Número de Pagos</label>
                        <input type="number" id="prestamoPagos" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" min="1" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tasa de Interés (%)</label>
                    <input type="number" id="prestamoTasa" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" step="0.01" min="0" value="0">
                    <p class="text-xs text-gray-500 mt-1">Dejar en 0 para préstamo sin intereses</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Otorgamiento</label>
                    <input type="date" id="prestamoFecha" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Motivo/Descripción</label>
                    <textarea id="prestamoDescripcion" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" placeholder="Motivo del préstamo..."></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="closePrestamoModal()" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="submit" class="bg-gradient-sinforosa text-white px-6 py-3 rounded-lg hover:opacity-90">
                    <i class="fas fa-hand-holding-usd mr-2"></i>Otorgar Préstamo
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para Nuevo Bono -->
<div id="bonoModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-gray-800">Nuevo Bono</h3>
                <button onclick="closeBonoModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <form id="bonoForm" class="p-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Empleado</label>
                    <select id="bonoEmpleado" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" required>
                        <option value="">Seleccione un empleado...</option>
                        <!-- Aquí se cargarían los empleados dinámicamente -->
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Bono</label>
                    <select id="bonoTipo" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" required>
                        <option value="">Seleccione...</option>
                        <option value="Productividad">Productividad</option>
                        <option value="Puntualidad">Puntualidad</option>
                        <option value="Desempeño">Desempeño</option>
                        <option value="Aguinaldo">Aguinaldo</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Monto</label>
                    <input type="number" id="bonoMonto" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" step="0.01" min="0" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Otorgamiento</label>
                    <input type="date" id="bonoFecha" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                    <textarea id="bonoDescripcion" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" placeholder="Descripción del bono..."></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="closeBonoModal()" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="submit" class="bg-gradient-sinforosa text-white px-6 py-3 rounded-lg hover:opacity-90">
                    <i class="fas fa-gift mr-2"></i>Otorgar Bono
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openPrestamoModal() {
    document.getElementById('prestamoModal').classList.remove('hidden');
    document.getElementById('prestamoFecha').valueAsDate = new Date();
    document.body.style.overflow = 'hidden';
}

function closePrestamoModal() {
    document.getElementById('prestamoModal').classList.add('hidden');
    document.body.style.overflow = '';
}

function openBonoModal() {
    document.getElementById('bonoModal').classList.remove('hidden');
    document.getElementById('bonoFecha').valueAsDate = new Date();
    document.body.style.overflow = 'hidden';
}

function closeBonoModal() {
    document.getElementById('bonoModal').classList.add('hidden');
    document.body.style.overflow = '';
}

// Manejar envío del formulario de préstamo
document.getElementById('prestamoForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    alert('Préstamo otorgado correctamente.\n\nEn una implementación completa, aquí se enviarían los datos al servidor y se generaría el calendario de pagos.');
    closePrestamoModal();
});

// Manejar envío del formulario de bono
document.getElementById('bonoForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    alert('Bono otorgado correctamente.\n\nEn una implementación completa, aquí se enviarían los datos al servidor.');
    closeBonoModal();
});

// Cerrar modales al presionar ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePrestamoModal();
        closeBonoModal();
    }
});
</script>
