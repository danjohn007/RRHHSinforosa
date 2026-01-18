<div class="fade-in">
    <!-- Header con tabs -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-4">Catálogos</h1>
        
        <!-- Tabs -->
        <div class="flex space-x-1 border-b border-gray-200">
            <a href="<?php echo BASE_URL; ?>catalogos/departamentos" 
               class="px-6 py-3 border-b-2 border-purple-600 text-purple-600 font-medium">
                Departamentos
            </a>
            <a href="<?php echo BASE_URL; ?>catalogos/puestos" 
               class="px-6 py-3 text-gray-600 hover:text-gray-900">
                Puestos
            </a>
        </div>
    </div>

    <!-- Botón Agregar -->
    <div class="mb-4 flex justify-end">
        <button onclick="abrirModal()" 
                class="bg-gradient-sinforosa text-white px-4 py-2 rounded-lg hover:opacity-90 transition">
            <i class="fas fa-plus mr-2"></i>Nuevo Departamento
        </button>
    </div>

    <!-- Tabla -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($departamentos)): ?>
                    <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">No hay departamentos</td></tr>
                <?php else: ?>
                    <?php foreach ($departamentos as $depto): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <?php echo htmlspecialchars($depto['nombre']); ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <?php echo htmlspecialchars($depto['descripcion'] ?? ''); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($depto['activo']): ?>
                                    <span class="px-2 text-xs font-semibold rounded-full bg-green-100 text-green-800">Activo</span>
                                <?php else: ?>
                                    <span class="px-2 text-xs font-semibold rounded-full bg-red-100 text-red-800">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <button onclick="editarDepartamento(<?php echo $depto['id']; ?>)" 
                                        class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="eliminarDepartamento(<?php echo $depto['id']; ?>, '<?php echo htmlspecialchars($depto['nombre'], ENT_QUOTES); ?>')" 
                                        class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div id="modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <h3 id="modal-title" class="text-lg font-semibold text-gray-900 mb-4">Nuevo Departamento</h3>
        <form id="form-departamento" onsubmit="guardar(event)">
            <input type="hidden" id="id" name="id">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nombre *</label>
                <input type="text" id="nombre" name="nombre" required 
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500"></textarea>
            </div>
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" id="activo" name="activo" checked 
                           class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                    <span class="ml-2 text-sm text-gray-700">Activo</span>
                </label>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="cerrarModal()" 
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-gradient-sinforosa text-white rounded-lg hover:opacity-90">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModal() {
    document.getElementById('modal-title').textContent = 'Nuevo Departamento';
    document.getElementById('form-departamento').reset();
    document.getElementById('id').value = '';
    document.getElementById('activo').checked = true;
    document.getElementById('modal').classList.remove('hidden');
}

function cerrarModal() {
    document.getElementById('modal').classList.add('hidden');
}

function editarDepartamento(id) {
    fetch(BASE_URL + 'catalogos/obtener-departamento?id=' + id)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('modal-title').textContent = 'Editar Departamento';
                document.getElementById('id').value = data.data.id;
                document.getElementById('nombre').value = data.data.nombre;
                document.getElementById('descripcion').value = data.data.descripcion || '';
                document.getElementById('activo').checked = data.data.activo == 1;
                document.getElementById('modal').classList.remove('hidden');
            }
        });
}

function guardar(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    
    fetch(BASE_URL + 'catalogos/guardar-departamento', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
}

function eliminarDepartamento(id, nombre) {
    if (confirm('¿Eliminar departamento "' + nombre + '"?')) {
        fetch(BASE_URL + 'catalogos/eliminar-departamento', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'id=' + id
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.success) location.reload();
        });
    }
}
</script>
