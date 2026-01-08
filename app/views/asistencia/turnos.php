<!-- Vista de Gestión de Turnos -->

<div class="mb-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="<?php echo BASE_URL; ?>asistencia" class="text-gray-600 hover:text-gray-900 mr-4">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Gestión de Turnos</h1>
                <p class="text-gray-600 mt-1">Administra horarios y turnos de trabajo</p>
            </div>
        </div>
        <button class="bg-gradient-sinforosa text-white px-4 py-2 rounded-lg hover:opacity-90">
            <i class="fas fa-plus mr-2"></i>Nuevo Turno
        </button>
    </div>
</div>

<!-- Tarjetas de Turnos -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <?php if (empty($turnos)): ?>
        <p class="text-gray-500">No hay turnos configurados</p>
    <?php else: ?>
        <?php foreach ($turnos as $turno): ?>
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($turno['nombre']); ?></h3>
                <i class="fas fa-clock text-2xl text-purple-600"></i>
            </div>
            
            <div class="space-y-2 text-sm text-gray-600">
                <div class="flex justify-between">
                    <span>Entrada:</span>
                    <span class="font-medium text-gray-800"><?php echo date('H:i', strtotime($turno['hora_entrada'])); ?></span>
                </div>
                <div class="flex justify-between">
                    <span>Salida:</span>
                    <span class="font-medium text-gray-800"><?php echo date('H:i', strtotime($turno['hora_salida'])); ?></span>
                </div>
                <div class="flex justify-between">
                    <span>Horas:</span>
                    <span class="font-medium text-gray-800"><?php echo number_format($turno['horas_laborales'], 2); ?> hrs</span>
                </div>
                <div class="flex justify-between">
                    <span>Tolerancia:</span>
                    <span class="font-medium text-gray-800"><?php echo $turno['minutos_tolerancia']; ?> min</span>
                </div>
            </div>
            
            <div class="mt-4 pt-4 border-t border-gray-200 flex justify-between">
                <button class="text-blue-600 hover:text-blue-900">
                    <i class="fas fa-edit mr-1"></i>Editar
                </button>
                <button class="text-red-600 hover:text-red-900">
                    <i class="fas fa-trash mr-1"></i>Eliminar
                </button>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
