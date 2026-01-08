<!-- Vista de Reclutamiento -->

<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Gestión de Candidatos</h1>
            <p class="text-gray-600 mt-1">Administra el proceso de reclutamiento y selección</p>
        </div>
        <button class="bg-gradient-sinforosa text-white px-6 py-3 rounded-lg hover:opacity-90 transition">
            <i class="fas fa-plus mr-2"></i> Nuevo Candidato
        </button>
    </div>
</div>

<!-- Estadísticas -->
<div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
    <?php
    $statusStats = array_column($stats, 'total', 'estatus');
    $statusInfo = [
        'Nuevo' => ['color' => 'blue', 'icon' => 'user-plus'],
        'En Revisión' => ['color' => 'yellow', 'icon' => 'search'],
        'Entrevista' => ['color' => 'purple', 'icon' => 'handshake'],
        'Seleccionado' => ['color' => 'green', 'icon' => 'check-circle'],
        'Rechazado' => ['color' => 'red', 'icon' => 'times-circle']
    ];
    
    foreach ($statusInfo as $status => $info):
        $count = $statusStats[$status] ?? 0;
    ?>
    <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-<?php echo $info['color']; ?>-500">
        <p class="text-sm text-gray-600 mb-1"><?php echo $status; ?></p>
        <div class="flex items-center justify-between">
            <p class="text-2xl font-bold text-<?php echo $info['color']; ?>-600"><?php echo $count; ?></p>
            <i class="fas fa-<?php echo $info['icon']; ?> text-<?php echo $info['color']; ?>-500 text-xl"></i>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Tabla de Candidatos -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-800">Candidatos Recientes</h2>
        <div class="flex space-x-2">
            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                <i class="fas fa-filter mr-2"></i>Filtrar
            </button>
            <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                <i class="fas fa-download mr-2"></i>Exportar
            </button>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Candidato</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Puesto Deseado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Experiencia</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pretensión</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Aplicación</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estatus</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($candidatos)): ?>
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                        No hay candidatos registrados
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($candidatos as $candidato): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 font-semibold">
                                    <?php echo strtoupper(substr($candidato['nombres'], 0, 1) . substr($candidato['apellido_paterno'], 0, 1)); ?>
                                </span>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($candidato['nombres'] . ' ' . $candidato['apellido_paterno']); ?>
                                </div>
                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($candidato['email'] ?? 'Sin email'); ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <?php echo htmlspecialchars($candidato['puesto_deseado']); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?php echo $candidato['experiencia_anios'] ?? 0; ?> años
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        $<?php echo number_format($candidato['pretension_salarial'], 2); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?php echo date('d/m/Y', strtotime($candidato['fecha_aplicacion'])); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php
                        $colors = [
                            'Nuevo' => 'bg-blue-100 text-blue-800',
                            'En Revisión' => 'bg-yellow-100 text-yellow-800',
                            'Entrevista' => 'bg-purple-100 text-purple-800',
                            'Evaluación' => 'bg-indigo-100 text-indigo-800',
                            'Seleccionado' => 'bg-green-100 text-green-800',
                            'Rechazado' => 'bg-red-100 text-red-800',
                            'Contratado' => 'bg-teal-100 text-teal-800'
                        ];
                        $color = $colors[$candidato['estatus']] ?? 'bg-gray-100 text-gray-800';
                        ?>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $color; ?>">
                            <?php echo htmlspecialchars($candidato['estatus']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <button class="text-blue-600 hover:text-blue-900" title="Ver perfil">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="text-green-600 hover:text-green-900" title="Programar entrevista">
                                <i class="fas fa-calendar-plus"></i>
                            </button>
                            <button class="text-purple-600 hover:text-purple-900" title="Contratar">
                                <i class="fas fa-user-check"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
