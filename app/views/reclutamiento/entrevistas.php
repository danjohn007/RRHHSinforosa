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
        <button onclick="openEntrevistaModal()" class="bg-gradient-sinforosa text-white px-4 py-2 rounded-lg hover:opacity-90">
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
                                <button class="text-blue-600 hover:text-blue-900 text-sm" onclick="editarEntrevista('<?php echo $entrevista['id'] ?? ''; ?>')">
                                    <i class="fas fa-edit mr-1"></i>Editar
                                </button>
                                <button class="text-green-600 hover:text-green-900 text-sm" onclick="completarEntrevista('<?php echo $entrevista['id'] ?? ''; ?>')">
                                    <i class="fas fa-check mr-1"></i>Completar
                                </button>
                                <button class="text-red-600 hover:text-red-900 text-sm" onclick="cancelarEntrevista('<?php echo $entrevista['id'] ?? ''; ?>')">
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

<!-- Modal para Nueva Entrevista -->
<div id="entrevistaModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-gray-800">Nueva Entrevista</h3>
                <button onclick="closeEntrevistaModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <form id="entrevistaForm" class="p-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Candidato</label>
                    <select id="entrevistaCandidato" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" required>
                        <option value="">Seleccione un candidato...</option>
                        <?php if (!empty($candidatos)): ?>
                            <?php foreach ($candidatos as $candidato): ?>
                                <option value="<?php echo $candidato['id']; ?>">
                                    <?php echo htmlspecialchars($candidato['nombre_completo']); ?> - <?php echo htmlspecialchars($candidato['puesto_deseado']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Entrevista</label>
                    <select id="entrevistaTipo" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" required>
                        <option value="">Seleccione...</option>
                        <option value="Telefónica">Telefónica</option>
                        <option value="Presencial">Presencial</option>
                        <option value="Virtual">Virtual</option>
                        <option value="Técnica">Técnica</option>
                    </select>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha</label>
                        <input type="date" id="entrevistaFecha" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hora</label>
                        <input type="time" id="entrevistaHora" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Duración (minutos)</label>
                    <input type="number" id="entrevistaDuracion" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" min="15" step="15" value="60" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Entrevistador</label>
                    <input type="text" id="entrevistaEntrevistador" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notas/Observaciones</label>
                    <textarea id="entrevistaNotas" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" placeholder="Notas adicionales sobre la entrevista..."></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="closeEntrevistaModal()" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="submit" class="bg-gradient-sinforosa text-white px-6 py-3 rounded-lg hover:opacity-90">
                    <i class="fas fa-calendar-check mr-2"></i>Programar Entrevista
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let editandoEntrevistaId = null;

function openEntrevistaModal() {
    editandoEntrevistaId = null;
    document.getElementById('entrevistaForm').reset();
    document.querySelector('#entrevistaModal h3').textContent = 'Nueva Entrevista';
    document.getElementById('entrevistaModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeEntrevistaModal() {
    editandoEntrevistaId = null;
    document.getElementById('entrevistaModal').classList.add('hidden');
    document.body.style.overflow = '';
}

async function editarEntrevista(id) {
    try {
        const response = await fetch('<?php echo BASE_URL; ?>reclutamiento/obtener-entrevista?id=' + id);
        const data = await response.json();
        
        if (data.success) {
            const ent = data.entrevista;
            editandoEntrevistaId = id;
            
            // Cambiar título del modal
            document.querySelector('#entrevistaModal h3').textContent = 'Editar Entrevista';
            
            // Llenar formulario con los datos
            document.getElementById('entrevistaCandidato').value = ent.candidato_id;
            document.getElementById('entrevistaTipo').value = ent.tipo;
            
            // Separar fecha y hora
            const fechaHora = new Date(ent.fecha_programada);
            const fecha = fechaHora.toISOString().split('T')[0];
            const hora = fechaHora.toTimeString().slice(0, 5);
            
            document.getElementById('entrevistaFecha').value = fecha;
            document.getElementById('entrevistaHora').value = hora;
            document.getElementById('entrevistaDuracion').value = ent.duracion_minutos;
            document.getElementById('entrevistaEntrevistador').value = ent.ubicacion || '';
            document.getElementById('entrevistaNotas').value = ent.observaciones || '';
            
            // Abrir modal
            document.getElementById('entrevistaModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        alert('Error al cargar la entrevista: ' + error.message);
    }
}

async function completarEntrevista(id) {
    if (confirm('¿Está seguro de que desea marcar esta entrevista como completada?')) {
        try {
            const formData = new FormData();
            formData.append('entrevista_id', id);
            
            const response = await fetch('<?php echo BASE_URL; ?>reclutamiento/completar-entrevista', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                alert(data.message || 'Entrevista marcada como completada exitosamente');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            alert('Error al completar la entrevista: ' + error.message);
        }
    }
}

async function cancelarEntrevista(id) {
    const motivo = prompt('Ingrese el motivo de la cancelación:');
    if (motivo) {
        try {
            const formData = new FormData();
            formData.append('entrevista_id', id);
            formData.append('motivo', motivo);
            
            const response = await fetch('<?php echo BASE_URL; ?>reclutamiento/cancelar-entrevista', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                alert(data.message || 'Entrevista cancelada exitosamente');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            alert('Error al cancelar la entrevista: ' + error.message);
        }
    }
}

// Manejar envío del formulario
document.getElementById('entrevistaForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const candidatoId = document.getElementById('entrevistaCandidato').value;
    const tipo = document.getElementById('entrevistaTipo').value;
    const fecha = document.getElementById('entrevistaFecha').value;
    const hora = document.getElementById('entrevistaHora').value;
    const duracion = document.getElementById('entrevistaDuracion').value;
    const entrevistador = document.getElementById('entrevistaEntrevistador').value;
    const notas = document.getElementById('entrevistaNotas').value;
    
    if (!candidatoId || !tipo || !fecha || !hora) {
        alert('Por favor complete todos los campos requeridos');
        return;
    }
    
    try {
        const formData = new FormData();
        
        if (editandoEntrevistaId) {
            // Modo edición
            formData.append('entrevista_id', editandoEntrevistaId);
            formData.append('tipo', tipo);
            formData.append('fecha_programada', fecha);
            formData.append('hora', hora);
            formData.append('duracion', duracion);
            formData.append('ubicacion', entrevistador);
            formData.append('observaciones', notas);
            
            const response = await fetch('<?php echo BASE_URL; ?>reclutamiento/reagendar-entrevista', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                alert(data.message || 'Entrevista actualizada exitosamente');
                closeEntrevistaModal();
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        } else {
            // Modo creación
            formData.append('candidato_id', candidatoId);
            formData.append('tipo', tipo);
            formData.append('fecha_programada', fecha);
            formData.append('hora', hora);
            formData.append('duracion', duracion);
            formData.append('ubicacion', entrevistador);
            formData.append('observaciones', notas);
            
            const response = await fetch('<?php echo BASE_URL; ?>reclutamiento/programar-entrevista', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                alert(data.message || 'Entrevista programada exitosamente');
                closeEntrevistaModal();
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        }
    } catch (error) {
        alert('Error al guardar la entrevista: ' + error.message);
    }
});

// Cerrar modal al presionar ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeEntrevistaModal();
    }
});
</script>
