<!-- Vista de Control de Asistencia -->

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Control de Asistencia</h1>
    <p class="text-gray-600 mt-1">Registro y seguimiento de asistencia del día <?php echo date('d/m/Y'); ?></p>
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
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
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
    
    <button onclick="exportarReporte()" class="bg-purple-600 text-white rounded-lg p-4 hover:bg-purple-700 transition text-center">
        <i class="fas fa-download text-2xl mb-2"></i>
        <p class="font-semibold">Exportar Reporte</p>
    </button>
</div>

<!-- Tabla de Asistencias del Día -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800">Registros del Día</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
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
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        No hay registros de asistencia para hoy
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($asistencias as $asistencia): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($asistencia['nombre_empleado']); ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?php echo htmlspecialchars($asistencia['departamento']); ?>
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
    // Mostrar opciones de exportación
    const formato = prompt('Seleccione el formato de exportación:\n1 - PDF\n2 - Excel\n3 - CSV\n\nIngrese el número (1, 2 o 3):', '1');
    
    if (formato) {
        let nombreFormato = '';
        switch(formato) {
            case '1':
                nombreFormato = 'PDF';
                break;
            case '2':
                nombreFormato = 'Excel';
                break;
            case '3':
                nombreFormato = 'CSV';
                break;
            default:
                alert('Formato no válido');
                return;
        }
        
        alert('Generando reporte en formato ' + nombreFormato + '...\n\nEn una implementación completa, aquí se generaría y descargaría el archivo.');
        // En una implementación real, aquí se haría una petición al servidor para generar el reporte
        // window.location.href = '<?php echo BASE_URL; ?>asistencia/exportar?formato=' + formato;
    }
}
</script>
