<!-- Listado de Empleados -->

<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Gestión de Empleados</h1>
            <p class="text-gray-600 mt-1">Administra la información de los colaboradores</p>
        </div>
        <a href="<?php echo BASE_URL; ?>empleados/crear" class="bg-gradient-sinforosa text-white px-6 py-3 rounded-lg hover:opacity-90 transition flex items-center">
            <i class="fas fa-plus mr-2"></i> Nuevo Empleado
        </a>
    </div>
</div>

<!-- Filtros -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
    <form method="GET" action="<?php echo BASE_URL; ?>empleados" class="flex flex-wrap gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Estatus</label>
            <select name="estatus" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                <option value="">Todos</option>
                <option value="Activo" <?php echo ($filters['estatus'] ?? '') === 'Activo' ? 'selected' : ''; ?>>Activo</option>
                <option value="Baja" <?php echo ($filters['estatus'] ?? '') === 'Baja' ? 'selected' : ''; ?>>Baja</option>
                <option value="Suspendido" <?php echo ($filters['estatus'] ?? '') === 'Suspendido' ? 'selected' : ''; ?>>Suspendido</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Departamento</label>
            <select name="departamento" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                <option value="">Todos</option>
                <?php foreach ($departamentos as $dept): ?>
                    <option value="<?php echo htmlspecialchars($dept); ?>" <?php echo ($filters['departamento'] ?? '') === $dept ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($dept); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="flex items-end">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-filter mr-2"></i> Filtrar
            </button>
        </div>
        
        <?php if (!empty($filters)): ?>
        <div class="flex items-end">
            <a href="<?php echo BASE_URL; ?>empleados" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-times mr-2"></i> Limpiar
            </a>
        </div>
        <?php endif; ?>
    </form>
</div>

<!-- Tabla de Empleados -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        No. Empleado
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Nombre Completo
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Departamento
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Puesto
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Antigüedad
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Estatus
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($empleados)): ?>
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                        No se encontraron empleados
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($empleados as $empleado): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($empleado['numero_empleado']); ?></span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 bg-purple-100 rounded-full flex items-center justify-center">
                                <span class="text-purple-600 font-semibold">
                                    <?php echo strtoupper(substr($empleado['nombres'], 0, 1) . substr($empleado['apellido_paterno'], 0, 1)); ?>
                                </span>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($empleado['nombre_completo']); ?></div>
                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($empleado['email_personal'] ?? 'Sin email'); ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-900"><?php echo htmlspecialchars($empleado['departamento']); ?></span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-900"><?php echo htmlspecialchars($empleado['puesto']); ?></span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-900"><?php echo $empleado['anios_antiguedad']; ?> años</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php
                        $statusColors = [
                            'Activo' => 'bg-green-100 text-green-800',
                            'Baja' => 'bg-red-100 text-red-800',
                            'Suspendido' => 'bg-yellow-100 text-yellow-800',
                            'Vacaciones' => 'bg-blue-100 text-blue-800'
                        ];
                        $color = $statusColors[$empleado['estatus']] ?? 'bg-gray-100 text-gray-800';
                        ?>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $color; ?>">
                            <?php echo htmlspecialchars($empleado['estatus']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <a href="<?php echo BASE_URL; ?>empleados/ver?id=<?php echo $empleado['id']; ?>" 
                               class="text-blue-600 hover:text-blue-900" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?php echo BASE_URL; ?>empleados/editar?id=<?php echo $empleado['id']; ?>" 
                               class="text-green-600 hover:text-green-900" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="<?php echo BASE_URL; ?>empleados/carta-recomendacion?id=<?php echo $empleado['id']; ?>" 
                               class="text-purple-600 hover:text-purple-900" title="Carta de recomendación" target="_blank">
                                <i class="fas fa-file-alt"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Estadísticas rápidas -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-600">Total Empleados</p>
        <p class="text-2xl font-bold text-gray-800"><?php echo count($empleados); ?></p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-600">Activos</p>
        <p class="text-2xl font-bold text-green-600">
            <?php echo count(array_filter($empleados, fn($e) => $e['estatus'] === 'Activo')); ?>
        </p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-600">De Baja</p>
        <p class="text-2xl font-bold text-red-600">
            <?php echo count(array_filter($empleados, fn($e) => $e['estatus'] === 'Baja')); ?>
        </p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-600">Departamentos</p>
        <p class="text-2xl font-bold text-blue-600"><?php echo count($departamentos); ?></p>
    </div>
</div>
