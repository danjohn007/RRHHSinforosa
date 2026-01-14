<!-- Vista de Gestión de Incidencias -->

<div class="mb-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="<?php echo BASE_URL; ?>asistencia" class="text-gray-600 hover:text-gray-900 mr-4">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Gestión de Incidencias</h1>
                <p class="text-gray-600 mt-1">Administra faltas, retardos, permisos y otras incidencias</p>
            </div>
        </div>
        <button onclick="openIncidenciaModal()" class="bg-gradient-sinforosa text-white px-4 py-2 rounded-lg hover:opacity-90">
            <i class="fas fa-plus mr-2"></i>Nueva Incidencia
        </button>
    </div>
</div>

<!-- Filtros -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <form method="GET" action="<?php echo BASE_URL; ?>asistencia/incidencias" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                <select name="tipo" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500">
                    <option value="">Todos los tipos</option>
                    <option value="Falta" <?php echo (isset($filtros['tipo']) && $filtros['tipo'] === 'Falta') ? 'selected' : ''; ?>>Falta</option>
                    <option value="Retardo" <?php echo (isset($filtros['tipo']) && $filtros['tipo'] === 'Retardo') ? 'selected' : ''; ?>>Retardo</option>
                    <option value="Incapacidad" <?php echo (isset($filtros['tipo']) && $filtros['tipo'] === 'Incapacidad') ? 'selected' : ''; ?>>Incapacidad</option>
                    <option value="Permiso" <?php echo (isset($filtros['tipo']) && $filtros['tipo'] === 'Permiso') ? 'selected' : ''; ?>>Permiso</option>
                    <option value="Vacaciones" <?php echo (isset($filtros['tipo']) && $filtros['tipo'] === 'Vacaciones') ? 'selected' : ''; ?>>Vacaciones</option>
                    <option value="Hora Extra" <?php echo (isset($filtros['tipo']) && $filtros['tipo'] === 'Hora Extra') ? 'selected' : ''; ?>>Hora Extra</option>
                    <option value="Bono" <?php echo (isset($filtros['tipo']) && $filtros['tipo'] === 'Bono') ? 'selected' : ''; ?>>Bono</option>
                    <option value="Descuento" <?php echo (isset($filtros['tipo']) && $filtros['tipo'] === 'Descuento') ? 'selected' : ''; ?>>Descuento</option>
                    <option value="Otro" <?php echo (isset($filtros['tipo']) && $filtros['tipo'] === 'Otro') ? 'selected' : ''; ?>>Otro</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estatus</label>
                <select name="estatus" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500">
                    <option value="">Todos los estatus</option>
                    <option value="Pendiente" <?php echo (isset($filtros['estatus']) && $filtros['estatus'] === 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                    <option value="Revisado" <?php echo (isset($filtros['estatus']) && $filtros['estatus'] === 'Revisado') ? 'selected' : ''; ?>>Revisado</option>
                    <option value="Aprobado" <?php echo (isset($filtros['estatus']) && $filtros['estatus'] === 'Aprobado') ? 'selected' : ''; ?>>Aprobado</option>
                    <option value="Rechazado" <?php echo (isset($filtros['estatus']) && $filtros['estatus'] === 'Rechazado') ? 'selected' : ''; ?>>Rechazado</option>
                    <option value="Procesado" <?php echo (isset($filtros['estatus']) && $filtros['estatus'] === 'Procesado') ? 'selected' : ''; ?>>Procesado</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" value="<?php echo isset($filtros['fecha_inicio']) ? htmlspecialchars($filtros['fecha_inicio']) : ''; ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Fin</label>
                <input type="date" name="fecha_fin" value="<?php echo isset($filtros['fecha_fin']) ? htmlspecialchars($filtros['fecha_fin']) : ''; ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Buscar Empleado</label>
                <input type="text" name="busqueda" value="<?php echo isset($filtros['busqueda']) ? htmlspecialchars($filtros['busqueda']) : ''; ?>" placeholder="Nombre o número..." class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500">
            </div>
        </div>
        
        <div class="flex justify-end space-x-3">
            <a href="<?php echo BASE_URL; ?>asistencia/incidencias" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Limpiar Filtros
            </a>
            <button type="submit" class="bg-gradient-sinforosa text-white px-6 py-2 rounded-lg hover:opacity-90">
                <i class="fas fa-filter mr-2"></i>Aplicar Filtros
            </button>
        </div>
    </form>
</div>

<!-- Tabla de Incidencias -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Empleado</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estatus</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php if (empty($incidencias)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2"></i>
                        <p>No se encontraron incidencias</p>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($incidencias as $incidencia): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($incidencia['nombre_empleado']); ?></span>
                            <span class="text-xs text-gray-500"><?php echo htmlspecialchars($incidencia['numero_empleado']); ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php
                        $colores = [
                            'Falta' => 'bg-red-100 text-red-800',
                            'Retardo' => 'bg-yellow-100 text-yellow-800',
                            'Incapacidad' => 'bg-blue-100 text-blue-800',
                            'Permiso' => 'bg-green-100 text-green-800',
                            'Vacaciones' => 'bg-purple-100 text-purple-800',
                            'Hora Extra' => 'bg-indigo-100 text-indigo-800',
                            'Bono' => 'bg-green-100 text-green-800',
                            'Descuento' => 'bg-orange-100 text-orange-800',
                            'Otro' => 'bg-gray-100 text-gray-800'
                        ];
                        $color = $colores[$incidencia['tipo_incidencia']] ?? 'bg-gray-100 text-gray-800';
                        ?>
                        <span class="px-2 py-1 text-xs rounded-full <?php echo $color; ?>">
                            <?php echo htmlspecialchars($incidencia['tipo_incidencia']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <?php echo date('d/m/Y', strtotime($incidencia['fecha_incidencia'])); ?>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        <?php echo htmlspecialchars(substr($incidencia['descripcion'], 0, 50)) . (strlen($incidencia['descripcion']) > 50 ? '...' : ''); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap"><?php
                        $estatusColores = [
                            'Pendiente' => 'bg-yellow-100 text-yellow-800',
                            'Revisado' => 'bg-cyan-100 text-cyan-800',
                            'Aprobado' => 'bg-green-100 text-green-800',
                            'Rechazado' => 'bg-red-100 text-red-800',
                            'Procesado' => 'bg-blue-100 text-blue-800'
                        ];
                        $estatusColor = $estatusColores[$incidencia['estatus']] ?? 'bg-gray-100 text-gray-800';
                        ?>
                        <span class="px-2 py-1 text-xs rounded-full <?php echo $estatusColor; ?>">
                            <?php echo htmlspecialchars($incidencia['estatus']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <div class="flex <?php echo ($incidencia['estatus'] === 'Revisado') ? 'justify-center' : ''; ?>">
                            <button onclick="verIncidencia(<?php echo $incidencia['id']; ?>)" class="text-blue-600 hover:text-blue-900 mr-2" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </button>
                            <?php if ($incidencia['estatus'] === 'Pendiente'): ?>
                                <button onclick="editarIncidencia(<?php echo $incidencia['id']; ?>)" class="text-purple-600 hover:text-purple-900 mr-2" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="marcarRevisado(<?php echo $incidencia['id']; ?>)" class="text-green-600 hover:text-green-900 mr-2" title="Marcar como Revisado">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button onclick="eliminarIncidencia(<?php echo $incidencia['id']; ?>)" class="text-red-600 hover:text-red-900" title="Eliminar">
                                    <i class="fas fa-trash"></i>
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

<!-- Modal para Nueva/Editar Incidencia -->
<div id="incidenciaModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="bg-gradient-sinforosa p-6 border-b">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-white" id="incidenciaModalTitle">Nueva Incidencia</h3>
                <button onclick="closeIncidenciaModal()" class="text-white hover:text-gray-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <form id="incidenciaForm" class="p-6">
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Empleado *</label>
                        <select id="empleado_id" name="empleado_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($empleados as $empleado): ?>
                                <option value="<?php echo $empleado['id']; ?>">
                                    <?php echo htmlspecialchars($empleado['numero_empleado'] . ' - ' . $empleado['nombre_completo']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo *</label>
                        <select id="tipo_incidencia" name="tipo_incidencia" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" required>
                            <option value="">Seleccione...</option>
                            <option value="Falta">Falta</option>
                            <option value="Retardo">Retardo</option>
                            <option value="Incapacidad">Incapacidad</option>
                            <option value="Permiso">Permiso</option>
                            <option value="Vacaciones">Vacaciones</option>
                            <option value="Hora Extra">Hora Extra</option>
                            <option value="Bono">Bono</option>
                            <option value="Descuento">Descuento</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha *</label>
                    <input type="date" id="fecha_incidencia" name="fecha_incidencia" value="<?php echo date('Y-m-d'); ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                    <textarea id="descripcion" name="descripcion" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" placeholder="Detalles de la incidencia..."></textarea>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="closeIncidenciaModal()" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="submit" class="bg-gradient-sinforosa text-white px-6 py-3 rounded-lg hover:opacity-90">
                    <i class="fas fa-save mr-2"></i>Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let incidenciaIdActual = null;

function openIncidenciaModal() {
    incidenciaIdActual = null;
    document.getElementById('incidenciaModal').classList.remove('hidden');
    document.getElementById('incidenciaModalTitle').textContent = 'Nueva Incidencia';
    document.getElementById('incidenciaForm').reset();
    document.getElementById('fecha_incidencia').value = '<?php echo date('Y-m-d'); ?>';
    document.body.style.overflow = 'hidden';
}

function closeIncidenciaModal() {
    document.getElementById('incidenciaModal').classList.add('hidden');
    document.body.style.overflow = '';
}

async function verIncidencia(id) {
    try {
        const response = await fetch('<?php echo BASE_URL; ?>asistencia/obtener-incidencia?id=' + id);
        const data = await response.json();
        
        if (data.success) {
            const inc = data.incidencia;
            alert(`Detalle de Incidencia #${id}\n\n` +
                  `Empleado: ${inc.nombre_empleado} (${inc.numero_empleado})\n` +
                  `Tipo: ${inc.tipo_incidencia}\n` +
                  `Fecha: ${inc.fecha_incidencia}\n` +
                  `Cantidad: ${inc.cantidad}\n` +
                  `Monto: $${parseFloat(inc.monto).toFixed(2)}\n` +
                  `Estatus: ${inc.estatus}\n` +
                  `Descripción: ${inc.descripcion || 'Sin descripción'}`);
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        alert('Error al cargar la incidencia: ' + error.message);
    }
}

async function editarIncidencia(id) {
    try {
        const response = await fetch('<?php echo BASE_URL; ?>asistencia/obtener-incidencia?id=' + id);
        const data = await response.json();
        
        if (data.success) {
            incidenciaIdActual = id;
            const inc = data.incidencia;
            
            document.getElementById('incidenciaModalTitle').textContent = 'Editar Incidencia';
            document.getElementById('empleado_id').value = inc.empleado_id;
            document.getElementById('tipo_incidencia').value = inc.tipo_incidencia;
            document.getElementById('fecha_incidencia').value = inc.fecha_incidencia;
            document.getElementById('descripcion').value = inc.descripcion || '';
            
            document.getElementById('incidenciaModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        alert('Error al cargar la incidencia: ' + error.message);
    }
}

async function eliminarIncidencia(id) {
    if (!confirm('¿Está seguro de que desea eliminar esta incidencia?')) {
        return;
    }
    
    try {
        const response = await fetch('<?php echo BASE_URL; ?>asistencia/eliminar-incidencia', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: id })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        alert('Error al eliminar la incidencia: ' + error.message);
    }
}

async function marcarRevisado(id) {
    if (!confirm('¿Desea marcar esta incidencia como Revisada?')) {
        return;
    }
    
    try {
        const response = await fetch('<?php echo BASE_URL; ?>asistencia/marcar-revisado', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: id })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        alert('Error al marcar la incidencia: ' + error.message);
    }
}

// Manejar envío del formulario
document.getElementById('incidenciaForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = {
        id: incidenciaIdActual,
        empleado_id: document.getElementById('empleado_id').value,
        tipo_incidencia: document.getElementById('tipo_incidencia').value,
        fecha_incidencia: document.getElementById('fecha_incidencia').value,
        cantidad: 1,
        monto: 0,
        descripcion: document.getElementById('descripcion').value
    };
    
    try {
        const response = await fetch('<?php echo BASE_URL; ?>asistencia/guardar-incidencia', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        alert('Error al guardar la incidencia: ' + error.message);
    }
});

// Cerrar modal con ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeIncidenciaModal();
    }
});
</script>
