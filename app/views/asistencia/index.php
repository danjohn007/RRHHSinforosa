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
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
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
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estatus</label>
                <select name="estatus" 
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500">
                    <option value="">Todos</option>
                    <option value="Presente" <?php echo ($filtros['estatus'] === 'Presente') ? 'selected' : ''; ?>>Presente</option>
                    <option value="Por Validar" <?php echo ($filtros['estatus'] === 'Por Validar') ? 'selected' : ''; ?>>Por Validar</option>
                    <option value="Validado" <?php echo ($filtros['estatus'] === 'Validado') ? 'selected' : ''; ?>>Validado</option>
                    <option value="Retardo" <?php echo ($filtros['estatus'] === 'Retardo') ? 'selected' : ''; ?>>Retardo</option>
                    <option value="Falta" <?php echo ($filtros['estatus'] === 'Falta') ? 'selected' : ''; ?>>Falta</option>
                    <option value="Permiso" <?php echo ($filtros['estatus'] === 'Permiso') ? 'selected' : ''; ?>>Permiso</option>
                    <option value="Vacaciones" <?php echo ($filtros['estatus'] === 'Vacaciones') ? 'selected' : ''; ?>>Vacaciones</option>
                    <option value="Incapacidad" <?php echo ($filtros['estatus'] === 'Incapacidad') ? 'selected' : ''; ?>>Incapacidad</option>
                </select>
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fotos</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estatus</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($asistencias)): ?>
                <tr>
                    <td colspan="9" class="px-6 py-8 text-center text-gray-500">
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
                        <a href="<?php echo BASE_URL; ?>empleados/ver?id=<?php echo $asistencia['empleado_id']; ?>" 
                           class="text-sm font-medium text-blue-600 hover:text-blue-800 hover:underline">
                            <?php echo htmlspecialchars($asistencia['empleado_nombre']); ?>
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?php echo htmlspecialchars($asistencia['departamento'] ?? ''); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <?php if ($asistencia['hora_entrada']): ?>
                            <div><?php echo date('H:i', strtotime($asistencia['hora_entrada'])); ?></div>
                            <?php if ($asistencia['sucursal_nombre']): ?>
                                <div class="text-xs text-gray-500">
                                    <i class="fas fa-building text-xs"></i>
                                    <?php echo htmlspecialchars($asistencia['sucursal_nombre']); ?>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <?php if ($asistencia['hora_salida']): ?>
                            <div><?php echo date('H:i', strtotime($asistencia['hora_salida'])); ?></div>
                            <?php if ($asistencia['auto_cortado']): ?>
                                <div class="text-xs text-orange-500">
                                    <i class="fas fa-clock text-xs"></i> Auto-cortado
                                </div>
                            <?php endif; ?>
                            <?php if ($asistencia['sucursal_nombre']): ?>
                                <div class="text-xs text-gray-500">
                                    <i class="fas fa-building text-xs"></i>
                                    <?php echo htmlspecialchars($asistencia['sucursal_nombre']); ?>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <?php if ($asistencia['horas_trabajadas']): ?>
                            <?php
                            $horasTrabajadas = (float)$asistencia['horas_trabajadas'];
                            $horasExtra = (float)($asistencia['horas_extra'] ?? 0);
                            $colorHoras = ($horasTrabajadas > 8) ? 'text-orange-600 font-bold' : 'text-gray-900';
                            ?>
                            <div class="<?php echo $colorHoras; ?>">
                                <?php echo number_format($horasTrabajadas, 2); ?> hrs
                            </div>
                            <?php if ($horasExtra > 0): ?>
                                <div class="text-xs text-orange-600 font-semibold">
                                    <i class="fas fa-plus-circle"></i> +<?php echo number_format($horasExtra, 2); ?> hrs extra
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                        <div class="flex space-x-2 justify-center">
                            <?php if (!empty($asistencia['foto_entrada'])): ?>
                                <a href="<?php echo BASE_URL . $asistencia['foto_entrada']; ?>" 
                                   target="_blank"
                                   class="text-blue-600 hover:text-blue-800" 
                                   title="Ver foto de entrada">
                                    <i class="fas fa-camera"></i> Entrada
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($asistencia['foto_salida'])): ?>
                                <a href="<?php echo BASE_URL . $asistencia['foto_salida']; ?>" 
                                   target="_blank"
                                   class="text-green-600 hover:text-green-800" 
                                   title="Ver foto de salida">
                                    <i class="fas fa-camera"></i> Salida
                                </a>
                            <?php endif; ?>
                            <?php if (empty($asistencia['foto_entrada']) && empty($asistencia['foto_salida'])): ?>
                                <span class="text-gray-400">-</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php
                        $colors = [
                            'Presente' => 'bg-green-100 text-green-800',
                            'Por Validar' => 'bg-orange-100 text-orange-800',
                            'Validado' => 'bg-blue-100 text-blue-800',
                            'Retardo' => 'bg-yellow-100 text-yellow-800',
                            'Falta' => 'bg-red-100 text-red-800',
                            'Permiso' => 'bg-indigo-100 text-indigo-800',
                            'Vacaciones' => 'bg-purple-100 text-purple-800',
                            'Incapacidad' => 'bg-pink-100 text-pink-800'
                        ];
                        $color = $colors[$asistencia['estatus']] ?? 'bg-gray-100 text-gray-800';
                        ?>
                        <div class="flex flex-col space-y-1">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $color; ?>">
                                <?php echo htmlspecialchars($asistencia['estatus']); ?>
                            </span>
                            <?php if ($asistencia['estatus'] === 'Por Validar'): ?>
                                <button onclick="mostrarModalValidar(<?php echo $asistencia['id']; ?>, '<?php echo $asistencia['empleado_nombre']; ?>', '<?php echo $asistencia['fecha']; ?>')"
                                        class="text-xs text-blue-600 hover:text-blue-800 hover:underline">
                                    <i class="fas fa-check"></i> Validar
                                </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal de Validación -->
<div id="modal-validar" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-check-circle text-blue-600 mr-2"></i>
                Validar Asistencia
            </h3>
            
            <div id="validar-info" class="mb-4 p-3 bg-gray-50 rounded">
                <p class="text-sm text-gray-700"><strong>Empleado:</strong> <span id="validar-empleado"></span></p>
                <p class="text-sm text-gray-700"><strong>Fecha:</strong> <span id="validar-fecha"></span></p>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Hora de Salida Real *
                </label>
                <input type="time" 
                       id="hora-salida-real" 
                       required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">
                    Ingrese la hora de salida real del empleado
                </p>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" 
                        onclick="cerrarModalValidar()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancelar
                </button>
                <button type="button" 
                        onclick="validarAsistencia()"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-check mr-1"></i>
                    Validar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let asistenciaIdParaValidar = null;

function mostrarModalValidar(asistenciaId, empleadoNombre, fecha) {
    asistenciaIdParaValidar = asistenciaId;
    document.getElementById('validar-empleado').textContent = empleadoNombre;
    document.getElementById('validar-fecha').textContent = formatearFecha(fecha);
    document.getElementById('modal-validar').classList.remove('hidden');
}

function cerrarModalValidar() {
    document.getElementById('modal-validar').classList.add('hidden');
    document.getElementById('hora-salida-real').value = '';
    asistenciaIdParaValidar = null;
}

function formatearFecha(fecha) {
    const partes = fecha.split('-');
    return partes[2] + '/' + partes[1] + '/' + partes[0];
}

function validarAsistencia() {
    const horaSalidaReal = document.getElementById('hora-salida-real').value;
    
    if (!horaSalidaReal) {
        alert('Por favor ingrese la hora de salida real');
        return;
    }
    
    if (!asistenciaIdParaValidar) {
        alert('Error: No se ha seleccionado ninguna asistencia');
        return;
    }
    
    // Enviar solicitud al servidor
    const formData = new FormData();
    formData.append('asistencia_id', asistenciaIdParaValidar);
    formData.append('hora_salida_real', horaSalidaReal);
    
    fetch('<?php echo BASE_URL; ?>asistencia/validar', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Asistencia validada correctamente\n' +
                  'Horas trabajadas: ' + data.horas_trabajadas + ' hrs\n' +
                  (data.horas_extra > 0 ? 'Horas extra: ' + data.horas_extra + ' hrs' : ''));
            cerrarModalValidar();
            location.reload();
        } else {
            alert('Error al validar: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al validar la asistencia');
    });
}

function exportarReporte() {
    // Obtener los parámetros de filtro actuales
    const urlParams = new URLSearchParams(window.location.search);
    const fechaInicio = urlParams.get('fecha_inicio') || '<?php echo $filtros['fecha_inicio']; ?>';
    const fechaFin = urlParams.get('fecha_fin') || '<?php echo $filtros['fecha_fin']; ?>';
    const busqueda = urlParams.get('busqueda') || '<?php echo $filtros['busqueda']; ?>';
    const estatus = urlParams.get('estatus') || '<?php echo $filtros['estatus'] ?? ''; ?>';
    
    // Construir URL con parámetros
    let exportUrl = '<?php echo BASE_URL; ?>asistencia/exportar?fecha_inicio=' + fechaInicio + '&fecha_fin=' + fechaFin;
    if (busqueda) {
        exportUrl += '&busqueda=' + encodeURIComponent(busqueda);
    }
    if (estatus) {
        exportUrl += '&estatus=' + encodeURIComponent(estatus);
    }
    
    // Descargar el archivo
    window.location.href = exportUrl;
}
</script>
