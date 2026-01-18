<!-- Vista de Editar Sucursal -->

<div class="mb-6">
    <div class="flex items-center">
        <a href="<?php echo BASE_URL; ?>sucursales" class="text-gray-600 hover:text-gray-900 mr-4">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Editar Sucursal</h1>
            <p class="text-gray-600 mt-1"><?php echo htmlspecialchars($sucursal['codigo']); ?> - <?php echo htmlspecialchars($sucursal['nombre']); ?></p>
        </div>
    </div>
</div>

<?php if (!empty($error)): ?>
<div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded">
    <p class="text-red-700"><?php echo htmlspecialchars($error); ?></p>
</div>
<?php endif; ?>

<?php if (!empty($success)): ?>
<div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 rounded">
    <p class="text-green-700"><?php echo htmlspecialchars($success); ?></p>
</div>
<?php endif; ?>

<form method="POST" action="<?php echo BASE_URL; ?>sucursales/editar?id=<?php echo $sucursal['id']; ?>" class="bg-white rounded-lg shadow-md p-6 mb-6">
    
    <!-- Información de la Sucursal -->
    <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
            <i class="fas fa-building text-purple-600 mr-2"></i>
            Información de la Sucursal
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nombre *</label>
                <input type="text" name="nombre" required 
                       value="<?php echo htmlspecialchars($sucursal['nombre']); ?>"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Código *</label>
                <input type="text" name="codigo" required 
                       value="<?php echo htmlspecialchars($sucursal['codigo']); ?>"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
        </div>
        
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Dirección</label>
            <textarea name="direccion" rows="3"
                      class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500"><?php echo htmlspecialchars($sucursal['direccion'] ?? ''); ?></textarea>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                <input type="text" name="telefono" 
                       value="<?php echo htmlspecialchars($sucursal['telefono'] ?? ''); ?>"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">URL Pública</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm"><?php echo BASE_URL; ?>publico/asistencia/</span>
                    </div>
                    <input type="text" name="url_publica" 
                           value="<?php echo htmlspecialchars($sucursal['url_publica'] ?? ''); ?>"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                           style="padding-left: <?php echo strlen(BASE_URL . 'publico/asistencia/') * 7; ?>px;"
                           pattern="[a-zA-Z0-9\-_]+"
                           title="Solo letras, números, guiones y guiones bajos">
                </div>
                <?php if (!empty($sucursal['url_publica'])): ?>
                <p class="text-xs text-blue-600 mt-1 flex items-center">
                    <i class="fas fa-link mr-1"></i>
                    URL Completa: 
                    <a href="<?php echo BASE_URL; ?>publico/asistencia/<?php echo htmlspecialchars($sucursal['url_publica']); ?>" 
                       target="_blank" 
                       class="ml-1 underline hover:text-blue-800">
                        <?php echo BASE_URL; ?>publico/asistencia/<?php echo htmlspecialchars($sucursal['url_publica']); ?>
                    </a>
                    <button type="button" 
                            onclick="navigator.clipboard.writeText('<?php echo BASE_URL; ?>publico/asistencia/<?php echo htmlspecialchars($sucursal['url_publica']); ?>')"
                            class="ml-2 text-gray-600 hover:text-gray-900"
                            title="Copiar URL">
                        <i class="fas fa-copy"></i>
                    </button>
                </p>
                <?php endif; ?>
                <p class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-info-circle"></i>
                    Identificador único para el registro de asistencia pública. Solo letras, números, guiones y guiones bajos.
                </p>
            </div>
        </div>
        
        <div class="mt-4">
            <label class="flex items-center space-x-2">
                <input type="checkbox" name="activo" value="1" 
                       <?php echo (!empty($sucursal['activo'])) ? 'checked' : ''; ?>
                       class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                <span class="text-sm font-medium text-gray-700">Sucursal activa</span>
            </label>
        </div>
    </div>
    
    <!-- Botones -->
    <div class="flex justify-end space-x-4">
        <a href="<?php echo BASE_URL; ?>sucursales" 
           class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
            Cancelar
        </a>
        <button type="submit" 
                class="bg-gradient-sinforosa text-white px-6 py-3 rounded-lg hover:opacity-90 transition">
            <i class="fas fa-save mr-2"></i>
            Guardar Cambios
        </button>
    </div>
</form>

<!-- Gerentes Asignados -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex items-center justify-between mb-4 border-b pb-2">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-user-tie text-blue-600 mr-2"></i>
            Gerentes Asignados
        </h3>
        <button type="button" onclick="mostrarModalAgregarGerente()" 
                class="text-sm bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">
            <i class="fas fa-plus mr-2"></i>
            Agregar Gerente
        </button>
    </div>
    
    <div id="lista-gerentes">
        <?php if (!empty($gerentes)): ?>
            <div class="space-y-2">
                <?php foreach ($gerentes as $gerente): ?>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold mr-3">
                                <?php echo strtoupper(substr($gerente['nombre_completo'], 0, 1)); ?>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800"><?php echo htmlspecialchars($gerente['nombre_completo']); ?></p>
                                <p class="text-sm text-gray-600"><?php echo htmlspecialchars($gerente['numero_empleado']); ?> - <?php echo htmlspecialchars($gerente['puesto'] ?? 'N/A'); ?></p>
                            </div>
                        </div>
                        <button type="button" onclick="eliminarGerente(<?php echo $gerente['id']; ?>)"
                                class="text-red-600 hover:text-red-800 transition">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-500 text-center py-4">No hay gerentes asignados a esta sucursal</p>
        <?php endif; ?>
    </div>
</div>

<!-- Áreas de Trabajo -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex items-center justify-between mb-4 border-b pb-2">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-door-open text-indigo-600 mr-2"></i>
            Áreas de Trabajo
        </h3>
        <button type="button" onclick="mostrarModalAreaTrabajo()" 
                class="text-sm bg-indigo-500 text-white px-4 py-2 rounded-lg hover:bg-indigo-600 transition">
            <i class="fas fa-plus mr-2"></i>
            Agregar Área
        </button>
    </div>
    
    <p class="text-sm text-gray-600 mb-4">
        Configura áreas de trabajo con dispositivos Shelly y canales específicos para controlar accesos.
    </p>
    
    <div id="lista-areas-trabajo">
        <?php if (!empty($areasTrabajo)): ?>
            <div class="space-y-3">
                <?php foreach ($areasTrabajo as $area): ?>
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-sm transition">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <h4 class="font-semibold text-gray-800 text-lg"><?php echo htmlspecialchars($area['nombre']); ?></h4>
                                    <?php if ($area['es_predeterminada']): ?>
                                        <span class="ml-2 px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded-full">
                                            Predeterminada
                                        </span>
                                    <?php endif; ?>
                                    <?php if (!$area['activo']): ?>
                                        <span class="ml-2 px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded-full">
                                            Inactiva
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if (!empty($area['descripcion'])): ?>
                                    <p class="text-sm text-gray-600 mb-3"><?php echo htmlspecialchars($area['descripcion']); ?></p>
                                <?php endif; ?>
                                
                                <div class="grid grid-cols-2 gap-4 mt-3">
                                    <div>
                                        <label class="text-xs font-medium text-gray-600">Dispositivo Shelly</label>
                                        <p class="text-sm text-gray-800 mt-1">
                                            <?php if (!empty($area['dispositivo_nombre'])): ?>
                                                <i class="fas fa-microchip text-green-600 mr-1"></i>
                                                <?php echo htmlspecialchars($area['dispositivo_nombre']); ?>
                                                <span class="text-xs text-gray-500 ml-1">(<?php echo htmlspecialchars($area['device_id']); ?>)</span>
                                            <?php else: ?>
                                                <span class="text-gray-400">No asignado</span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-gray-600">Canal Asignado</label>
                                        <p class="text-sm text-gray-800 mt-1">
                                            <?php if (!empty($area['dispositivo_nombre'])): ?>
                                                <i class="fas fa-plug text-indigo-600 mr-1"></i>
                                                Canal <?php echo htmlspecialchars($area['canal_asignado']); ?>
                                            <?php else: ?>
                                                <span class="text-gray-400">-</span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2 ml-4">
                                <button type="button" onclick="editarAreaTrabajo(<?php echo htmlspecialchars(json_encode($area)); ?>)"
                                        class="text-blue-600 hover:text-blue-800 p-2 rounded-lg hover:bg-blue-50 transition"
                                        title="Editar área">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <?php if (!$area['es_predeterminada']): ?>
                                    <button type="button" onclick="eliminarAreaTrabajo(<?php echo $area['id']; ?>)"
                                            class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50 transition"
                                            title="Eliminar área">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-500 text-center py-4">No hay áreas de trabajo configuradas</p>
        <?php endif; ?>
    </div>
</div>

<!-- Dispositivos Shelly Asignados -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex items-center justify-between mb-4 border-b pb-2">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-wifi text-green-600 mr-2"></i>
            Dispositivos Shelly
        </h3>
        <button type="button" onclick="mostrarModalAgregarDispositivo()" 
                class="text-sm bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition">
            <i class="fas fa-plus mr-2"></i>
            Agregar Dispositivo
        </button>
    </div>
    
    <div id="lista-dispositivos">
        <?php if (!empty($dispositivos)): ?>
            <div class="space-y-2">
                <?php foreach ($dispositivos as $dispositivo): ?>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white mr-3">
                                <i class="fas fa-microchip"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800"><?php echo htmlspecialchars($dispositivo['nombre']); ?></p>
                                <p class="text-sm text-gray-600">IP: <?php echo htmlspecialchars($dispositivo['ip'] ?? ''); ?></p>
                            </div>
                        </div>
                        <button type="button" onclick="eliminarDispositivo(<?php echo $dispositivo['id']; ?>)"
                                class="text-red-600 hover:text-red-800 transition">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-500 text-center py-4">No hay dispositivos Shelly asignados a esta sucursal</p>
        <?php endif; ?>
    </div>
</div>

<!-- Empleados de la Sucursal -->
<div class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
        <i class="fas fa-users text-purple-600 mr-2"></i>
        Empleados de la Sucursal
    </h3>
    
    <?php if (!empty($empleados)): ?>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Puesto</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departamento</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($empleados as $empleado): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                <?php echo htmlspecialchars($empleado['numero_empleado']); ?>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <a href="<?php echo BASE_URL; ?>empleados/ver?id=<?php echo $empleado['id']; ?>" 
                                   class="text-sm text-purple-600 hover:text-purple-800 font-medium">
                                    <?php echo htmlspecialchars($empleado['nombre_completo']); ?>
                                </a>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                <?php echo htmlspecialchars($empleado['puesto'] ?? 'N/A'); ?>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                <?php echo htmlspecialchars($empleado['departamento'] ?? 'N/A'); ?>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <?php if (isset($empleado['estatus']) && $empleado['estatus'] === 'Activo'): ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Activo
                                    </span>
                                <?php else: ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Inactivo
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-gray-500 text-center py-4">No hay empleados asignados a esta sucursal</p>
    <?php endif; ?>
</div>

<!-- Modal Agregar Gerente -->
<div id="modal-agregar-gerente" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Agregar Gerente</h3>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Seleccionar Empleado</label>
                <select id="empleado-gerente" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500">
                    <option value="">Seleccione un empleado...</option>
                    <?php if (!empty($empleadosDisponibles)): ?>
                        <?php foreach ($empleadosDisponibles as $emp): ?>
                            <option value="<?php echo $emp['id']; ?>">
                                <?php echo htmlspecialchars($emp['numero_empleado']); ?> - <?php echo htmlspecialchars($emp['nombre_completo']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="cerrarModalAgregarGerente()" 
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="button" onclick="agregarGerente()" 
                        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    Agregar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Agregar Dispositivo -->
<div id="modal-agregar-dispositivo" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Agregar Dispositivo Shelly</h3>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Seleccionar Dispositivo</label>
                <select id="dispositivo-shelly" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500">
                    <option value="">Seleccione un dispositivo...</option>
                    <?php if (!empty($dispositivosDisponibles)): ?>
                        <?php foreach ($dispositivosDisponibles as $disp): ?>
                            <option value="<?php echo $disp['id']; ?>">
                                <?php echo htmlspecialchars($disp['nombre']); ?> - <?php echo htmlspecialchars($disp['ip'] ?? ''); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="cerrarModalAgregarDispositivo()" 
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="button" onclick="agregarDispositivo()" 
                        class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                    Agregar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Área de Trabajo -->
<div id="modal-area-trabajo" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-semibold text-gray-900 mb-4" id="modal-area-titulo">Agregar Área de Trabajo</h3>
            <form id="form-area-trabajo">
                <input type="hidden" id="area-id" name="area_id">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Área *</label>
                    <input type="text" id="area-nombre" name="nombre" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500"
                           placeholder="Ej: Entrada Principal, Salida Trasera">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                    <textarea id="area-descripcion" name="descripcion" rows="2"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500"
                              placeholder="Descripción del área de trabajo"></textarea>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Dispositivo Shelly</label>
                        <select id="area-dispositivo" name="dispositivo_shelly_id"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                            <option value="">Sin dispositivo asignado</option>
                            <?php if (!empty($dispositivosDisponibles)): ?>
                                <?php foreach ($dispositivosDisponibles as $disp): ?>
                                    <option value="<?php echo $disp['id']; ?>">
                                        <?php echo htmlspecialchars($disp['nombre']); ?>
                                        <?php if (!empty($disp['device_id'])): ?>
                                            (<?php echo htmlspecialchars($disp['device_id']); ?>)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Canal Asignado</label>
                        <select id="area-canal" name="canal_asignado"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                            <option value="0">Canal 0</option>
                            <option value="1">Canal 1</option>
                            <option value="2">Canal 2</option>
                            <option value="3">Canal 3</option>
                        </select>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" id="area-activo" name="activo" checked
                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm font-medium text-gray-700">Área activa</span>
                    </label>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" onclick="cerrarModalAreaTrabajo()" 
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="button" onclick="guardarAreaTrabajo()" 
                            class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const sucursalId = <?php echo $sucursal['id']; ?>;
const baseUrl = '<?php echo BASE_URL; ?>';

// Modal Gerente
function mostrarModalAgregarGerente() {
    document.getElementById('modal-agregar-gerente').classList.remove('hidden');
}

function cerrarModalAgregarGerente() {
    document.getElementById('modal-agregar-gerente').classList.add('hidden');
    document.getElementById('empleado-gerente').value = '';
}

function agregarGerente() {
    const empleadoId = document.getElementById('empleado-gerente').value;
    
    if (!empleadoId) {
        alert('Por favor seleccione un empleado');
        return;
    }
    
    fetch(baseUrl + 'sucursales/asignar-gerente', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            sucursal_id: sucursalId,
            empleado_id: empleadoId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error al agregar gerente');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al agregar gerente');
    });
}

function eliminarGerente(empleadoId) {
    if (!confirm('¿Está seguro de eliminar este gerente de la sucursal?')) {
        return;
    }
    
    fetch(baseUrl + 'sucursales/remover-gerente', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            sucursal_id: sucursalId,
            empleado_id: empleadoId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error al eliminar gerente');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al eliminar gerente');
    });
}

// Modal Dispositivo
function mostrarModalAgregarDispositivo() {
    document.getElementById('modal-agregar-dispositivo').classList.remove('hidden');
}

function cerrarModalAgregarDispositivo() {
    document.getElementById('modal-agregar-dispositivo').classList.add('hidden');
    document.getElementById('dispositivo-shelly').value = '';
}

function agregarDispositivo() {
    const dispositivoId = document.getElementById('dispositivo-shelly').value;
    
    if (!dispositivoId) {
        alert('Por favor seleccione un dispositivo');
        return;
    }
    
    fetch(baseUrl + 'sucursales/asignar-dispositivo', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            sucursal_id: sucursalId,
            dispositivo_id: dispositivoId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error al agregar dispositivo');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al agregar dispositivo');
    });
}

function eliminarDispositivo(dispositivoId) {
    if (!confirm('¿Está seguro de eliminar este dispositivo de la sucursal?')) {
        return;
    }
    
    fetch(baseUrl + 'sucursales/remover-dispositivo', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            sucursal_id: sucursalId,
            dispositivo_id: dispositivoId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error al eliminar dispositivo');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al eliminar dispositivo');
    });
}

// Modal Área de Trabajo
function mostrarModalAreaTrabajo() {
    document.getElementById('modal-area-titulo').textContent = 'Agregar Área de Trabajo';
    document.getElementById('form-area-trabajo').reset();
    document.getElementById('area-id').value = '';
    document.getElementById('area-activo').checked = true;
    document.getElementById('modal-area-trabajo').classList.remove('hidden');
}

function cerrarModalAreaTrabajo() {
    document.getElementById('modal-area-trabajo').classList.add('hidden');
    document.getElementById('form-area-trabajo').reset();
}

function editarAreaTrabajo(area) {
    document.getElementById('modal-area-titulo').textContent = 'Editar Área de Trabajo';
    document.getElementById('area-id').value = area.id;
    document.getElementById('area-nombre').value = area.nombre;
    document.getElementById('area-descripcion').value = area.descripcion || '';
    document.getElementById('area-dispositivo').value = area.dispositivo_shelly_id || '';
    document.getElementById('area-canal').value = area.canal_asignado || 0;
    document.getElementById('area-activo').checked = area.activo == 1;
    document.getElementById('modal-area-trabajo').classList.remove('hidden');
}

function guardarAreaTrabajo() {
    const areaId = document.getElementById('area-id').value;
    const nombre = document.getElementById('area-nombre').value;
    const descripcion = document.getElementById('area-descripcion').value;
    const dispositivoId = document.getElementById('area-dispositivo').value;
    const canal = document.getElementById('area-canal').value;
    const activo = document.getElementById('area-activo').checked;
    
    if (!nombre) {
        alert('Por favor ingrese el nombre del área');
        return;
    }
    
    const data = {
        sucursal_id: sucursalId,
        nombre: nombre,
        descripcion: descripcion,
        dispositivo_shelly_id: dispositivoId || null,
        canal_asignado: parseInt(canal),
        activo: activo ? 1 : 0
    };
    
    if (areaId) {
        data.area_id = parseInt(areaId);
    }
    
    fetch(baseUrl + 'sucursales/guardar-area-trabajo', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error al guardar área');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al guardar área');
    });
}

function eliminarAreaTrabajo(areaId) {
    if (!confirm('¿Está seguro de eliminar esta área de trabajo?')) {
        return;
    }
    
    fetch(baseUrl + 'sucursales/eliminar-area-trabajo', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            area_id: areaId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error al eliminar área');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al eliminar área');
    });
}
</script>
