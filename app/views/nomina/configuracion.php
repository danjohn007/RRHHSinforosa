<!-- Vista de Configuración de Nómina -->

<div class="mb-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="<?php echo BASE_URL; ?>nomina" class="text-gray-600 hover:text-gray-900 mr-4">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Configuración de Nómina</h1>
                <p class="text-gray-600 mt-1">Administra percepciones y deducciones</p>
            </div>
        </div>
        <button onclick="openConceptModal()" class="bg-gradient-sinforosa text-white px-4 py-2 rounded-lg hover:opacity-90">
            <i class="fas fa-plus mr-2"></i>Nuevo Concepto
        </button>
    </div>
</div>

<!-- Tabs -->
<div class="mb-6">
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
            <button onclick="showTab('percepciones')" id="tab-percepciones" class="config-tab active border-b-2 border-purple-500 py-4 px-1 text-center font-medium text-sm text-purple-600">
                Percepciones
            </button>
            <button onclick="showTab('deducciones')" id="tab-deducciones" class="config-tab border-b-2 border-transparent py-4 px-1 text-center font-medium text-sm text-gray-500 hover:text-gray-700">
                Deducciones
            </button>
        </nav>
    </div>
</div>

<!-- Percepciones -->
<div id="content-percepciones" class="config-content">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Clave</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoría</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Afecta IMSS</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Afecta ISR</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php 
                $percepciones = array_filter($conceptos, fn($c) => $c['tipo'] === 'Percepción');
                foreach ($percepciones as $concepto): 
                ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        <?php echo htmlspecialchars($concepto['clave']); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <?php echo htmlspecialchars($concepto['nombre']); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                            <?php echo htmlspecialchars($concepto['categoria']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                        <?php echo $concepto['afecta_imss'] ? '<i class="fas fa-check text-green-600"></i>' : '<i class="fas fa-times text-gray-400"></i>'; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                        <?php echo $concepto['afecta_isr'] ? '<i class="fas fa-check text-green-600"></i>' : '<i class="fas fa-times text-gray-400"></i>'; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full <?php echo $concepto['activo'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                            <?php echo $concepto['activo'] ? 'Activo' : 'Inactivo'; ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <button class="text-blue-600 hover:text-blue-900 mr-2" onclick="editConcept('<?php echo $concepto['clave']; ?>')">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="text-red-600 hover:text-red-900" onclick="deleteConcept('<?php echo $concepto['clave']; ?>')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Deducciones -->
<div id="content-deducciones" class="config-content hidden">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Clave</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoría</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Afecta IMSS</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Afecta ISR</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php 
                $deducciones = array_filter($conceptos, fn($c) => $c['tipo'] === 'Deducción');
                foreach ($deducciones as $concepto): 
                ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        <?php echo htmlspecialchars($concepto['clave']); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <?php echo htmlspecialchars($concepto['nombre']); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                            <?php echo htmlspecialchars($concepto['categoria']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                        <?php echo $concepto['afecta_imss'] ? '<i class="fas fa-check text-green-600"></i>' : '<i class="fas fa-times text-gray-400"></i>'; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                        <?php echo $concepto['afecta_isr'] ? '<i class="fas fa-check text-green-600"></i>' : '<i class="fas fa-times text-gray-400"></i>'; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full <?php echo $concepto['activo'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                            <?php echo $concepto['activo'] ? 'Activo' : 'Inactivo'; ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <button class="text-blue-600 hover:text-blue-900 mr-2" onclick="editConcept('<?php echo $concepto['clave']; ?>')">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="text-red-600 hover:text-red-900" onclick="deleteConcept('<?php echo $concepto['clave']; ?>')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para Nuevo/Editar Concepto -->
<div id="conceptModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-gray-800" id="conceptModalTitle">Nuevo Concepto</h3>
                <button onclick="closeConceptModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <form id="conceptForm" class="p-6">
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                        <select id="conceptTipo" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" required>
                            <option value="">Seleccione...</option>
                            <option value="Percepción">Percepción</option>
                            <option value="Deducción">Deducción</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Clave</label>
                        <input type="text" id="conceptClave" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" placeholder="Ej: P001" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre</label>
                    <input type="text" id="conceptNombre" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" placeholder="Nombre del concepto" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Categoría</label>
                    <select id="conceptCategoria" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" required>
                        <option value="">Seleccione...</option>
                        <option value="Fijo">Fijo</option>
                        <option value="Variable">Variable</option>
                    </select>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-center">
                        <input type="checkbox" id="conceptAfectaIMSS" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                        <label for="conceptAfectaIMSS" class="ml-2 block text-sm text-gray-700">Afecta IMSS</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="conceptAfectaISR" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                        <label for="conceptAfectaISR" class="ml-2 block text-sm text-gray-700">Afecta ISR</label>
                    </div>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="conceptActivo" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded" checked>
                    <label for="conceptActivo" class="ml-2 block text-sm text-gray-700">Activo</label>
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="closeConceptModal()" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
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
let claveOriginal = null;

function showTab(tab) {
    document.querySelectorAll('.config-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.config-tab').forEach(el => {
        el.classList.remove('active', 'border-purple-500', 'text-purple-600');
        el.classList.add('border-transparent', 'text-gray-500');
    });
    
    document.getElementById('content-' + tab).classList.remove('hidden');
    const btn = document.getElementById('tab-' + tab);
    btn.classList.add('active', 'border-purple-500', 'text-purple-600');
    btn.classList.remove('border-transparent', 'text-gray-500');
}

function openConceptModal() {
    claveOriginal = null;
    document.getElementById('conceptModal').classList.remove('hidden');
    document.getElementById('conceptModalTitle').textContent = 'Nuevo Concepto';
    document.getElementById('conceptForm').reset();
    document.getElementById('conceptClave').disabled = false;
    document.body.style.overflow = 'hidden';
}

function closeConceptModal() {
    document.getElementById('conceptModal').classList.add('hidden');
    document.body.style.overflow = '';
}

async function editConcept(clave) {
    try {
        const response = await fetch('<?php echo BASE_URL; ?>nomina/obtener-concepto?clave=' + encodeURIComponent(clave));
        const data = await response.json();
        
        if (data.success) {
            claveOriginal = clave;
            const concepto = data.concepto;
            
            document.getElementById('conceptModalTitle').textContent = 'Editar Concepto';
            document.getElementById('conceptTipo').value = concepto.tipo;
            document.getElementById('conceptClave').value = concepto.clave;
            document.getElementById('conceptClave').disabled = true; // No permitir cambiar la clave
            document.getElementById('conceptNombre').value = concepto.nombre;
            document.getElementById('conceptCategoria').value = concepto.categoria;
            document.getElementById('conceptAfectaIMSS').checked = concepto.afecta_imss == 1;
            document.getElementById('conceptAfectaISR').checked = concepto.afecta_isr == 1;
            document.getElementById('conceptActivo').checked = concepto.activo == 1;
            
            document.getElementById('conceptModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        } else {
            alert('Error al cargar el concepto: ' + data.message);
        }
    } catch (error) {
        alert('Error al cargar el concepto: ' + error.message);
    }
}

async function deleteConcept(clave) {
    if (!confirm('¿Está seguro de que desea eliminar el concepto ' + clave + '?')) {
        return;
    }
    
    try {
        const response = await fetch('<?php echo BASE_URL; ?>nomina/eliminar-concepto', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ clave: clave })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        alert('Error al eliminar el concepto: ' + error.message);
    }
}

// Manejar envío del formulario
document.getElementById('conceptForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = {
        clave: document.getElementById('conceptClave').value,
        clave_original: claveOriginal,
        nombre: document.getElementById('conceptNombre').value,
        tipo: document.getElementById('conceptTipo').value,
        categoria: document.getElementById('conceptCategoria').value,
        afecta_imss: document.getElementById('conceptAfectaIMSS').checked ? 1 : 0,
        afecta_isr: document.getElementById('conceptAfectaISR').checked ? 1 : 0,
        activo: document.getElementById('conceptActivo').checked ? 1 : 0
    };
    
    try {
        const response = await fetch('<?php echo BASE_URL; ?>nomina/guardar-concepto', {
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
        alert('Error al guardar el concepto: ' + error.message);
    }
});

// Cerrar modal al presionar ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeConceptModal();
    }
});
</script>
