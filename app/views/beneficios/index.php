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
            <button class="bg-gradient-sinforosa text-white px-4 py-2 rounded-lg hover:opacity-90">
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
            <button class="bg-gradient-sinforosa text-white px-4 py-2 rounded-lg hover:opacity-90">
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
