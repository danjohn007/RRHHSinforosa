<!-- Vista de Control de Asistencia -->

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Control de Asistencia</h1>
    <p class="text-gray-600 mt-1">Registro y seguimiento de asistencia</p>
</div>

<!-- Filtros de Búsqueda y Fechas -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">
        <i class="fas fa-filter text-purple-600 mr-2"></i>
        Filtros y Búsqueda
    </h3>
    
    <form method="GET" action="<?php echo BASE_URL; ?>asistencia" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" 
                       value="<?php echo htmlspecialchars($filtros['fecha_inicio']); ?>"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Fin</label>
                <input type="date" name="fecha_fin" 
                       value="<?php echo htmlspecialchars($filtros['fecha_fin']); ?>"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500">
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Buscar Empleado</label>
                <input type="text" name="busqueda" 
                       value="<?php echo htmlspecialchars($filtros['busqueda']); ?>"
                       placeholder="Nombre, Email, Teléfono o No. Empleado"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500">
            </div>
        </div>
        
        <div class="flex justify-between items-center pt-2">
            <div class="text-sm text-gray-600">
                <?php 
                $totalRegistros = count($asistencias);
                echo "Mostrando {$totalRegistros} registro(s)";
                if ($filtros['fecha_inicio'] !== $filtros['fecha_fin']) {
                    echo " del " . date('d/m/Y', strtotime($filtros['fecha_inicio'])) . 
                         " al " . date('d/m/Y', strtotime($filtros['fecha_fin']));
                } else {
                    echo " del " . date('d/m/Y', strtotime($filtros['fecha_inicio']));
                }
                ?>
            </div>
            <div class="flex space-x-2">
                <a href="<?php echo BASE_URL; ?>asistencia" 
                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-times mr-1"></i> Limpiar
                </a>
                <button type="submit" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-search mr-1"></i> Filtrar
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Stats del Día -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Presentes</p>
                <p class="text-3xl font-bold text-green-600">
                    <?php echo count(array_filter($asistencias, fn($a) => $a['estatus'] === 'Presente')); ?>
                </p>
            </div>
            <i class="fas fa-check-circle text-3xl text-green-500"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Retardos</p>
                <p class="text-3xl font-bold text-yellow-600">
                    <?php echo count(array_filter($asistencias, fn($a) => $a['estatus'] === 'Retardo')); ?>
                </p>
            </div>
            <i class="fas fa-clock text-3xl text-yellow-500"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Faltas</p>
                <p class="text-3xl font-bold text-red-600">
                    <?php echo count(array_filter($asistencias, fn($a) => $a['estatus'] === 'Falta')); ?>
                </p>
            </div>
            <i class="fas fa-times-circle text-3xl text-red-500"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Total Registros</p>
                <p class="text-3xl font-bold text-blue-600"><?php echo count($asistencias); ?></p>
            </div>
            <i class="fas fa-users text-3xl text-blue-500"></i>
        </div>
    </div>
</div>

<!-- Acciones Rápidas -->
<div class="grid grid-cols-1 md:grid-cols-<?php echo (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin') ? '4' : '3'; ?> gap-4 mb-6">
    <a href="<?php echo BASE_URL; ?>asistencia/registro" class="bg-gradient-sinforosa text-white rounded-lg p-4 hover:opacity-90 transition text-center">
        <i class="fas fa-user-clock text-2xl mb-2"></i>
        <p class="font-semibold">Registrar Asistencia</p>
    </a>
    
    <a href="<?php echo BASE_URL; ?>asistencia/vacaciones" class="bg-blue-600 text-white rounded-lg p-4 hover:bg-blue-700 transition text-center">
        <i class="fas fa-umbrella-beach text-2xl mb-2"></i>
        <p class="font-semibold">Vacaciones</p>
    </a>
    
    <a href="<?php echo BASE_URL; ?>asistencia/turnos" class="bg-green-600 text-white rounded-lg p-4 hover:bg-green-700 transition text-center">
        <i class="fas fa-business-time text-2xl mb-2"></i>
        <p class="font-semibold">Turnos</p>
    </a>
    
    <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
    <a href="<?php echo BASE_URL; ?>asistencia/incidencias" class="bg-orange-600 text-white rounded-lg p-4 hover:bg-orange-700 transition text-center">
        <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
        <p class="font-semibold">Incidencias</p>
    </a>
    <?php endif; ?>
</div>


<!-- Tabla de Asistencias del Día -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-800">Registros de Asistencia</h2>
        <button onclick="exportarReporte()" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
            <i class="fas fa-download mr-2"></i>
            Exportar Reporte
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Empleado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Empleado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Departamento</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entrada</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Salida</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Horas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estatus</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($asistencias)): ?>
                <tr>
                    <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                        No hay registros de asistencia para los filtros seleccionados
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($asistencias as $asistencia): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <?php echo date('d/m/Y', strtotime($asistencia['fecha'])); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <?php echo htmlspecialchars($asistencia['numero_empleado'] ?? ''); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($asistencia['nombre_empleado']); ?></div>
                        <?php if (!empty($asistencia['email'])): ?>
                        <div class="text-xs text-gray-500"><?php echo htmlspecialchars($asistencia['email']); ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?php echo htmlspecialchars($asistencia['departamento'] ?? ''); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <?php echo $asistencia['hora_entrada'] ? date('H:i', strtotime($asistencia['hora_entrada'])) : '-'; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <?php echo $asistencia['hora_salida'] ? date('H:i', strtotime($asistencia['hora_salida'])) : '-'; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <?php echo $asistencia['horas_trabajadas'] ? number_format($asistencia['horas_trabajadas'], 2) : '-'; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php
                        $colors = [
                            'Presente' => 'bg-green-100 text-green-800',
                            'Retardo' => 'bg-yellow-100 text-yellow-800',
                            'Falta' => 'bg-red-100 text-red-800',
                            'Permiso' => 'bg-blue-100 text-blue-800',
                            'Vacaciones' => 'bg-purple-100 text-purple-800',
                            'Incapacidad' => 'bg-orange-100 text-orange-800'
                        ];
                        $color = $colors[$asistencia['estatus']] ?? 'bg-gray-100 text-gray-800';
                        ?>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $color; ?>">
                            <?php echo htmlspecialchars($asistencia['estatus']); ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function exportarReporte() {
    // Obtener los parámetros de filtro actuales
    const urlParams = new URLSearchParams(window.location.search);
    const fechaInicio = urlParams.get('fecha_inicio') || '<?php echo $filtros['fecha_inicio']; ?>';
    const fechaFin = urlParams.get('fecha_fin') || '<?php echo $filtros['fecha_fin']; ?>';
    const busqueda = urlParams.get('busqueda') || '<?php echo $filtros['busqueda']; ?>';
    
    // Construir URL con parámetros
    let exportUrl = '<?php echo BASE_URL; ?>asistencia/exportar?fecha_inicio=' + fechaInicio + '&fecha_fin=' + fechaFin;
    if (busqueda) {
        exportUrl += '&busqueda=' + encodeURIComponent(busqueda);
    }
    
    // Descargar el archivo
    window.location.href = exportUrl;
}
</script>
