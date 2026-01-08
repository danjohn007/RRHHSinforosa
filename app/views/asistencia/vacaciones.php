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
        <button class="bg-gradient-sinforosa text-white px-4 py-2 rounded-lg hover:opacity-90">
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
                        <button class="text-green-600 hover:text-green-900 mr-2" title="Aprobar">
                            <i class="fas fa-check"></i>
                        </button>
                        <button class="text-red-600 hover:text-red-900" title="Rechazar">
                            <i class="fas fa-times"></i>
                        </button>
                        <?php else: ?>
                        <button class="text-blue-600 hover:text-blue-900" title="Ver detalles">
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
