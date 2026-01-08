<!-- Vista de Nómina -->

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Administración de Nómina</h1>
    <p class="text-gray-600 mt-1">Gestión y procesamiento de nómina</p>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Períodos Registrados</p>
                <p class="text-2xl font-bold text-gray-800"><?php echo count($periodos); ?></p>
            </div>
            <i class="fas fa-calendar-alt text-3xl text-blue-500"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
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
    
    <div class="bg-white rounded-lg shadow-md p-6">
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
    
    <div class="bg-white rounded-lg shadow-md p-6">
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
    <a href="<?php echo BASE_URL; ?>nomina/procesar" class="bg-gradient-sinforosa text-white rounded-lg p-6 hover:opacity-90 transition">
        <i class="fas fa-calculator text-3xl mb-3"></i>
        <h3 class="text-lg font-semibold">Procesar Nómina</h3>
        <p class="text-sm text-purple-100 mt-2">Calcular y procesar período de nómina</p>
    </a>
    
    <a href="<?php echo BASE_URL; ?>nomina/recibos" class="bg-blue-600 text-white rounded-lg p-6 hover:bg-blue-700 transition">
        <i class="fas fa-file-invoice text-3xl mb-3"></i>
        <h3 class="text-lg font-semibold">Recibos de Nómina</h3>
        <p class="text-sm text-blue-100 mt-2">Generar y consultar recibos</p>
    </a>
    
    <a href="<?php echo BASE_URL; ?>nomina/configuracion" class="bg-green-600 text-white rounded-lg p-6 hover:bg-green-700 transition">
        <i class="fas fa-cog text-3xl mb-3"></i>
        <h3 class="text-lg font-semibold">Configuración</h3>
        <p class="text-sm text-green-100 mt-2">Percepciones y deducciones</p>
    </a>
</div>

<!-- Tabla de Períodos -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
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
                        <button class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="text-green-600 hover:text-green-900">
                            <i class="fas fa-download"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
