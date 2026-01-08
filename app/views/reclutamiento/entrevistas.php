<!-- Vista de Gestión de Entrevistas -->

<div class="mb-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="<?php echo BASE_URL; ?>reclutamiento" class="text-gray-600 hover:text-gray-900 mr-4">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Gestión de Entrevistas</h1>
                <p class="text-gray-600 mt-1">Programa y administra entrevistas</p>
            </div>
        </div>
        <button class="bg-gradient-sinforosa text-white px-4 py-2 rounded-lg hover:opacity-90">
            <i class="fas fa-plus mr-2"></i>Nueva Entrevista
        </button>
    </div>
</div>

<!-- Calendario de Entrevistas -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Lista de Entrevistas -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Entrevistas Programadas</h3>
            </div>
            <div class="divide-y divide-gray-200">
                <?php if (empty($entrevistas)): ?>
                    <div class="p-6 text-center text-gray-500">
                        No hay entrevistas programadas
                    </div>
                <?php else: ?>
                    <?php foreach ($entrevistas as $entrevista): ?>
                    <div class="p-6 hover:bg-gray-50 transition">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <div class="h-10 w-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-user text-purple-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-800">
                                            <?php echo htmlspecialchars($entrevista['nombre_candidato']); ?>
                                        </h4>
                                        <p class="text-xs text-gray-500"><?php echo htmlspecialchars($entrevista['puesto_deseado']); ?></p>
                                    </div>
                                </div>
                                
                                <div class="mt-2 flex items-center text-sm text-gray-600 space-x-4">
                                    <span>
                                        <i class="fas fa-calendar-alt mr-1 text-blue-500"></i>
                                        <?php echo date('d/m/Y', strtotime($entrevista['fecha_programada'])); ?>
                                    </span>
                                    <span>
                                        <i class="fas fa-clock mr-1 text-green-500"></i>
                                        <?php echo date('H:i', strtotime($entrevista['fecha_programada'])); ?>
                                    </span>
                                    <span>
                                        <i class="fas fa-hourglass-half mr-1 text-yellow-500"></i>
                                        <?php echo $entrevista['duracion_minutos']; ?> min
                                    </span>
                                </div>
                                
                                <div class="mt-2">
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                        <?php echo htmlspecialchars($entrevista['tipo']); ?>
                                    </span>
                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 ml-2">
                                        <?php echo htmlspecialchars($entrevista['estatus']); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="flex flex-col space-y-2">
                                <button class="text-blue-600 hover:text-blue-900 text-sm">
                                    <i class="fas fa-edit mr-1"></i>Editar
                                </button>
                                <button class="text-green-600 hover:text-green-900 text-sm">
                                    <i class="fas fa-check mr-1"></i>Completar
                                </button>
                                <button class="text-red-600 hover:text-red-900 text-sm">
                                    <i class="fas fa-times mr-1"></i>Cancelar
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Resumen -->
    <div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Resumen de Entrevistas</h3>
            
            <div class="space-y-4">
                <div class="p-3 bg-blue-50 rounded-lg">
                    <p class="text-sm text-gray-600">Programadas</p>
                    <p class="text-2xl font-bold text-blue-600">
                        <?php echo count(array_filter($entrevistas, fn($e) => $e['estatus'] === 'Programada')); ?>
                    </p>
                </div>
                
                <div class="p-3 bg-green-50 rounded-lg">
                    <p class="text-sm text-gray-600">Realizadas</p>
                    <p class="text-2xl font-bold text-green-600">
                        <?php echo count(array_filter($entrevistas, fn($e) => $e['estatus'] === 'Realizada')); ?>
                    </p>
                </div>
                
                <div class="p-3 bg-red-50 rounded-lg">
                    <p class="text-sm text-gray-600">Canceladas</p>
                    <p class="text-2xl font-bold text-red-600">
                        <?php echo count(array_filter($entrevistas, fn($e) => $e['estatus'] === 'Cancelada')); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
