<!-- Vista de Reclutamiento -->

<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Gestión de Candidatos</h1>
            <p class="text-gray-600 mt-1">Administra el proceso de reclutamiento y selección</p>
        </div>
        <button onclick="openCandidatoModal()" class="bg-gradient-sinforosa text-white px-6 py-3 rounded-lg hover:opacity-90 transition">
            <i class="fas fa-plus mr-2"></i> Nuevo Candidato
        </button>
    </div>
</div>

<!-- Estadísticas -->
<div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
    <?php
    $statusStats = array_column($stats, 'total', 'estatus');
    $statusInfo = [
        'Nuevo' => ['color' => 'blue', 'icon' => 'user-plus'],
        'En Revisión' => ['color' => 'yellow', 'icon' => 'search'],
        'Entrevista' => ['color' => 'purple', 'icon' => 'handshake'],
        'Seleccionado' => ['color' => 'green', 'icon' => 'check-circle'],
        'Rechazado' => ['color' => 'red', 'icon' => 'times-circle']
    ];
    
    foreach ($statusInfo as $status => $info):
        $count = $statusStats[$status] ?? 0;
    ?>
    <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-<?php echo $info['color']; ?>-500">
        <p class="text-sm text-gray-600 mb-1"><?php echo $status; ?></p>
        <div class="flex items-center justify-between">
            <p class="text-2xl font-bold text-<?php echo $info['color']; ?>-600"><?php echo $count; ?></p>
            <i class="fas fa-<?php echo $info['icon']; ?> text-<?php echo $info['color']; ?>-500 text-xl"></i>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Tabla de Candidatos -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-800">Candidatos Recientes</h2>
        <div class="flex space-x-2">
            <button onclick="toggleFiltros()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                <i class="fas fa-filter mr-2"></i>Filtrar
            </button>
            <button onclick="exportarCandidatos()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                <i class="fas fa-download mr-2"></i>Exportar
            </button>
        </div>
    </div>
    
    <!-- Panel de filtros -->
    <div id="filtrosPanel" class="hidden px-6 py-4 bg-gray-50 border-b border-gray-200">
        <form id="filtrosForm" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estatus</label>
                <select name="estatus" id="filtroEstatus" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">Todos</option>
                    <option value="Nuevo">Nuevo</option>
                    <option value="En Revisión">En Revisión</option>
                    <option value="Entrevista">Entrevista</option>
                    <option value="Seleccionado">Seleccionado</option>
                    <option value="Rechazado">Rechazado</option>
                    <option value="Contratado">Contratado</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Puesto</label>
                <input type="text" name="puesto" id="filtroPuesto" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="Buscar puesto...">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Experiencia mínima</label>
                <input type="number" name="experiencia" id="filtroExperiencia" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="Años" min="0">
            </div>
            <div class="flex items-end">
                <button type="button" onclick="aplicarFiltros()" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-search mr-2"></i>Buscar
                </button>
            </div>
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Candidato</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Puesto Deseado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Experiencia</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pretensión</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Aplicación</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estatus</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($candidatos)): ?>
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                        No hay candidatos registrados
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($candidatos as $candidato): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 font-semibold">
                                    <?php echo strtoupper(substr($candidato['nombres'], 0, 1) . substr($candidato['apellido_paterno'], 0, 1)); ?>
                                </span>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($candidato['nombres'] . ' ' . $candidato['apellido_paterno']); ?>
                                </div>
                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($candidato['email'] ?? 'Sin email'); ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <?php echo htmlspecialchars($candidato['puesto_deseado']); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?php echo $candidato['experiencia_anios'] ?? 0; ?> años
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        $<?php echo $candidato['pretension_salarial'] ? number_format($candidato['pretension_salarial'], 2) : '0.00'; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?php echo date('d/m/Y', strtotime($candidato['fecha_aplicacion'])); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php
                        $colors = [
                            'Nuevo' => 'bg-blue-100 text-blue-800',
                            'En Revisión' => 'bg-yellow-100 text-yellow-800',
                            'Entrevista' => 'bg-purple-100 text-purple-800',
                            'Evaluación' => 'bg-indigo-100 text-indigo-800',
                            'Seleccionado' => 'bg-green-100 text-green-800',
                            'Rechazado' => 'bg-red-100 text-red-800',
                            'Contratado' => 'bg-teal-100 text-teal-800'
                        ];
                        $color = $colors[$candidato['estatus']] ?? 'bg-gray-100 text-gray-800';
                        ?>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $color; ?>">
                            <?php echo htmlspecialchars($candidato['estatus']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2 <?php echo ($candidato['estatus'] === 'Rechazado' || $candidato['estatus'] === 'Contratado' || $candidato['estatus'] === 'Entrevista') ? 'justify-center' : ''; ?>">
                            <button class="text-blue-600 hover:text-blue-900" title="Ver perfil" onclick="verPerfil('<?php echo $candidato['id'] ?? ''; ?>')">
                                <i class="fas fa-eye"></i>
                            </button>
                            <?php if ($candidato['estatus'] !== 'Rechazado' && $candidato['estatus'] !== 'Contratado'): ?>
                                <?php if ($candidato['estatus'] !== 'Entrevista'): ?>
                                <button class="text-green-600 hover:text-green-900" title="Programar entrevista" onclick="programarEntrevista('<?php echo $candidato['id'] ?? ''; ?>')">
                                    <i class="fas fa-calendar-plus"></i>
                                </button>
                                <?php endif; ?>
                                <button class="text-purple-600 hover:text-purple-900" title="Contratar" onclick="contratarCandidato('<?php echo $candidato['id'] ?? ''; ?>')">
                                    <i class="fas fa-user-check"></i>
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

<!-- Modal para Nuevo Candidato -->
<div id="candidatoModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-gray-800">Nuevo Candidato</h3>
                <button onclick="closeCandidatoModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <form id="candidatoForm" class="p-6">
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombres</label>
                        <input type="text" id="candidatoNombres" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Apellidos</label>
                        <input type="text" id="candidatoApellidos" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" required>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" id="candidatoEmail" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                        <input type="tel" id="candidatoTelefono" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" required>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Puesto Deseado</label>
                        <input type="text" id="candidatoPuesto" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Años de Experiencia</label>
                        <input type="number" id="candidatoExperiencia" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" min="0" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pretensión Salarial</label>
                    <input type="number" id="candidatoSalario" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" step="0.01" min="0" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">CV / Resumen de Experiencia</label>
                    <textarea id="candidatoCV" rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" placeholder="Resumen de la experiencia profesional..."></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="closeCandidatoModal()" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="submit" class="bg-gradient-sinforosa text-white px-6 py-3 rounded-lg hover:opacity-90">
                    <i class="fas fa-save mr-2"></i>Guardar Candidato
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openCandidatoModal() {
    document.getElementById('candidatoModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeCandidatoModal() {
    document.getElementById('candidatoModal').classList.add('hidden');
    document.body.style.overflow = '';
}

function programarEntrevista(id) {
    const modalHTML = `
        <div id="modalEntrevista" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg max-w-2xl w-full">
                <div class="bg-blue-600 text-white p-6 rounded-t-lg">
                    <h2 class="text-2xl font-bold">
                        <i class="fas fa-calendar-plus mr-2"></i>
                        Programar Entrevista
                    </h2>
                </div>
                
                <form id="formEntrevista" class="p-6">
                    <input type="hidden" name="candidato_id" value="${id}">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Tipo de Entrevista <span class="text-red-500">*</span>
                            </label>
                            <select name="tipo" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccione...</option>
                                <option value="Telefónica">Telefónica</option>
                                <option value="Presencial">Presencial</option>
                                <option value="Virtual">Virtual</option>
                                <option value="Técnica">Técnica</option>
                                <option value="Final">Final</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Fecha <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="fecha_programada" required 
                                   min="${new Date().toISOString().split('T')[0]}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Hora <span class="text-red-500">*</span>
                            </label>
                            <input type="time" name="hora" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Duración (minutos)
                            </label>
                            <input type="number" name="duracion" value="60" min="15" max="240"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Ubicación / Link
                        </label>
                        <input type="text" name="ubicacion" 
                               placeholder="Ej: Oficina principal, Sala de juntas, https://meet.google.com/..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="mt-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Observaciones
                        </label>
                        <textarea name="observaciones" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                  placeholder="Instrucciones adicionales, temas a evaluar, etc."></textarea>
                    </div>
                    
                    <div class="mt-6 flex gap-3 justify-end">
                        <button type="button" onclick="cerrarModal('modalEntrevista')" 
                                class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                            <i class="fas fa-times mr-2"></i>Cancelar
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-save mr-2"></i>Programar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Manejar envío del formulario
    document.getElementById('formEntrevista').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch('<?php echo BASE_URL; ?>reclutamiento/programar-entrevista', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
                cerrarModal('modalEntrevista');
                location.reload();
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al programar la entrevista');
        });
    });
}

// Reagendar entrevista existente
function reagendarEntrevista(entrevistaId, candidatoId) {
    // Primero obtener los datos de la entrevista actual
    fetch(`<?php echo BASE_URL; ?>reclutamiento/obtener-entrevista?id=${entrevistaId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarModalReagendar(data.entrevista, candidatoId);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar la entrevista');
        });
}

function mostrarModalReagendar(entrevista, candidatoId) {
    // Extraer fecha y hora
    const fechaHora = new Date(entrevista.fecha_programada);
    const fecha = fechaHora.toISOString().split('T')[0];
    const hora = fechaHora.toTimeString().substring(0, 5);
    
    const modalHTML = `
        <div id="modalEntrevista" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg max-w-2xl w-full">
                <div class="bg-yellow-600 text-white p-6 rounded-t-lg">
                    <h2 class="text-2xl font-bold">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        Reagendar Entrevista
                    </h2>
                    <p class="text-yellow-100 mt-1">Modifique la fecha y hora de la entrevista existente</p>
                </div>
                
                <form id="formEntrevista" class="p-6">
                    <input type="hidden" name="entrevista_id" value="${entrevista.id}">
                    <input type="hidden" name="candidato_id" value="${candidatoId}">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Tipo de Entrevista <span class="text-red-500">*</span>
                            </label>
                            <select name="tipo" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500">
                                <option value="Telefónica" ${entrevista.tipo === 'Telefónica' ? 'selected' : ''}>Telefónica</option>
                                <option value="Presencial" ${entrevista.tipo === 'Presencial' ? 'selected' : ''}>Presencial</option>
                                <option value="Virtual" ${entrevista.tipo === 'Virtual' ? 'selected' : ''}>Virtual</option>
                                <option value="Técnica" ${entrevista.tipo === 'Técnica' ? 'selected' : ''}>Técnica</option>
                                <option value="Final" ${entrevista.tipo === 'Final' ? 'selected' : ''}>Final</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Fecha <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="fecha_programada" required value="${fecha}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Hora <span class="text-red-500">*</span>
                            </label>
                            <input type="time" name="hora" required value="${hora}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Duración (minutos)
                            </label>
                            <input type="number" name="duracion" value="${entrevista.duracion_minutos}" min="15" max="240"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500">
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Ubicación / Link
                        </label>
                        <input type="text" name="ubicacion" value="${entrevista.ubicacion || ''}"
                               placeholder="Ej: Oficina principal, Sala de juntas, https://meet.google.com/..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500">
                    </div>
                    
                    <div class="mt-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Observaciones
                        </label>
                        <textarea name="observaciones" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500"
                                  placeholder="Instrucciones adicionales, temas a evaluar, etc.">${entrevista.observaciones || ''}</textarea>
                    </div>
                    
                    <div class="mt-6 flex gap-3 justify-end">
                        <button type="button" onclick="cerrarModal('modalEntrevista')" 
                                class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                            <i class="fas fa-times mr-2"></i>Cancelar
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                            <i class="fas fa-save mr-2"></i>Reagendar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Manejar envío del formulario
    document.getElementById('formEntrevista').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch('<?php echo BASE_URL; ?>reclutamiento/reagendar-entrevista', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
                cerrarModal('modalEntrevista');
                location.reload();
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al reagendar la entrevista');
        });
    });
}

function contratarCandidato(id) {
    const modalHTML = `
        <div id="modalContratar" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg max-w-2xl w-full">
                <div class="bg-green-600 text-white p-6 rounded-t-lg">
                    <h2 class="text-2xl font-bold">
                        <i class="fas fa-user-check mr-2"></i>
                        Contratar Candidato
                    </h2>
                    <p class="text-green-100 mt-1">Complete los datos para crear el registro de empleado</p>
                </div>
                
                <form id="formContratar" class="p-6">
                    <input type="hidden" name="candidato_id" value="${id}">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Fecha de Ingreso <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="fecha_ingreso" required 
                                   value="${new Date().toISOString().split('T')[0]}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Tipo de Contrato <span class="text-red-500">*</span>
                            </label>
                            <select name="tipo_contrato" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                                <option value="Planta">Planta</option>
                                <option value="Eventual">Eventual</option>
                                <option value="Honorarios">Honorarios</option>
                                <option value="Practicante">Practicante</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Departamento <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="departamento" required 
                                   placeholder="Ej: Operaciones, Cocina, Ventas"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Puesto <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="puesto" required 
                                   placeholder="Ej: Barista, Supervisor"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Salario Diario <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">$</span>
                                <input type="number" name="salario_diario" required min="0" step="0.01"
                                       placeholder="0.00"
                                       class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            </div>
                            <p class="text-sm text-gray-500 mt-1">El salario mensual se calculará automáticamente (× 30 días)</p>
                        </div>
                    </div>
                    
                    <div class="mt-6 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <div class="flex">
                            <i class="fas fa-exclamation-triangle text-yellow-600 mt-1 mr-3"></i>
                            <div>
                                <p class="font-semibold text-yellow-800">Importante:</p>
                                <p class="text-sm text-yellow-700">
                                    Al contratar este candidato se creará un nuevo registro en el módulo de Empleados 
                                    y se actualizará su estatus a "Contratado".
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex gap-3 justify-end">
                        <button type="button" onclick="cerrarModal('modalContratar')" 
                                class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                            <i class="fas fa-times mr-2"></i>Cancelar
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            <i class="fas fa-check-circle mr-2"></i>Confirmar Contratación
                        </button>
                    </div>
                </form>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Manejar envío del formulario
    document.getElementById('formContratar').addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!confirm('¿Está seguro de contratar a este candidato? Se creará un nuevo empleado.')) {
            return;
        }
        
        const formData = new FormData(this);
        
        fetch('<?php echo BASE_URL; ?>reclutamiento/contratar-candidato', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message + '\nNúmero de empleado: ' + data.numero_empleado);
                cerrarModal('modalContratar');
                location.reload();
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al contratar candidato');
        });
    });
}

// Manejar envío del formulario
document.getElementById('candidatoForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    alert('Candidato registrado correctamente.\n\nEn una implementación completa, aquí se enviarían los datos al servidor.');
    closeCandidatoModal();
});

// Cerrar modal al presionar ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCandidatoModal();
        cerrarModal('modalPerfil');
        cerrarModal('modalEntrevista');
        cerrarModal('modalContratar');
    }
});

// Ver perfil completo del candidato
function verPerfil(id) {
    fetch(`<?php echo BASE_URL; ?>reclutamiento/obtener-perfil?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarModalPerfil(data.candidato, data.entrevistas);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar el perfil');
        });
}

// Mostrar modal con perfil completo
function mostrarModalPerfil(candidato, entrevistas) {
    const edad = candidato.fecha_nacimiento ? calcularEdad(candidato.fecha_nacimiento) : 'N/A';
    const direccion = [candidato.calle, candidato.colonia, candidato.municipio, candidato.estado, candidato.codigo_postal]
        .filter(v => v).join(', ') || 'No especificada';
    
    // Verificar si hay entrevista programada
    const entrevistaProgramada = entrevistas && entrevistas.find(e => e.estatus === 'Programada');
    
    let entrevistasHTML = '';
    if (entrevistas && entrevistas.length > 0) {
        entrevistasHTML = entrevistas.map(e => `
            <div class="border-l-4 border-blue-500 pl-3 py-2 mb-2">
                <div class="flex justify-between">
                    <span class="font-semibold">${e.tipo}</span>
                    <span class="text-sm ${getEstatusClass(e.estatus)}">${e.estatus}</span>
                </div>
                <div class="text-sm text-gray-600">
                    ${formatearFecha(e.fecha_programada)} • ${e.duracion_minutos} min
                </div>
                ${e.ubicacion ? `<div class="text-sm text-gray-500">📍 ${e.ubicacion}</div>` : ''}
            </div>
        `).join('');
    } else {
        entrevistasHTML = '<p class="text-gray-500 text-sm">No hay entrevistas programadas</p>';
    }
    
    const modalHTML = `
        <div id="modalPerfil" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white p-6 rounded-t-lg">
                    <div class="flex justify-between items-start">
                        <div>
                            <h2 class="text-2xl font-bold">${candidato.nombres} ${candidato.apellido_paterno} ${candidato.apellido_materno || ''}</h2>
                            <p class="text-blue-100 mt-1">${candidato.puesto_deseado || 'Puesto no especificado'}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm font-semibold ${getEstatusClass(candidato.estatus)}">
                            ${candidato.estatus}
                        </span>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Información Personal -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-user mr-2 text-blue-600"></i>
                                Información Personal
                            </h3>
                            <div class="space-y-3">
                                <div class="flex">
                                    <span class="font-semibold text-gray-600 w-32">Email:</span>
                                    <span class="text-gray-800">${candidato.email || 'No registrado'}</span>
                                </div>
                                <div class="flex">
                                    <span class="font-semibold text-gray-600 w-32">Teléfono:</span>
                                    <span class="text-gray-800">${candidato.telefono || 'N/A'}</span>
                                </div>
                                <div class="flex">
                                    <span class="font-semibold text-gray-600 w-32">Celular:</span>
                                    <span class="text-gray-800">${candidato.celular || 'N/A'}</span>
                                </div>
                                <div class="flex">
                                    <span class="font-semibold text-gray-600 w-32">Fecha Nac.:</span>
                                    <span class="text-gray-800">${candidato.fecha_nacimiento ? formatearFecha(candidato.fecha_nacimiento) + ' (' + edad + ' años)' : 'No registrada'}</span>
                                </div>
                                <div class="flex">
                                    <span class="font-semibold text-gray-600 w-32">Dirección:</span>
                                    <span class="text-gray-800 flex-1">${direccion}</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Información Profesional -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-briefcase mr-2 text-blue-600"></i>
                                Información Profesional
                            </h3>
                            <div class="space-y-3">
                                <div class="flex">
                                    <span class="font-semibold text-gray-600 w-32">Educación:</span>
                                    <span class="text-gray-800">${candidato.nivel_estudios || 'No especificado'}</span>
                                </div>
                                <div class="flex">
                                    <span class="font-semibold text-gray-600 w-32">Carrera:</span>
                                    <span class="text-gray-800">${candidato.carrera || 'N/A'}</span>
                                </div>
                                <div class="flex">
                                    <span class="font-semibold text-gray-600 w-32">Experiencia:</span>
                                    <span class="text-gray-800">${candidato.experiencia_anios ? candidato.experiencia_anios + ' años' : 'Sin experiencia'}</span>
                                </div>
                                <div class="flex">
                                    <span class="font-semibold text-gray-600 w-32">Pretensión:</span>
                                    <span class="text-gray-800 font-semibold text-green-600">
                                        $${candidato.pretension_salarial ? parseFloat(candidato.pretension_salarial).toLocaleString('es-MX', {minimumFractionDigits: 2}) : '0.00'}
                                    </span>
                                </div>
                                <div class="flex">
                                    <span class="font-semibold text-gray-600 w-32">Fuente:</span>
                                    <span class="text-gray-800">${candidato.fuente_reclutamiento || 'No especificada'}</span>
                                </div>
                                <div class="flex">
                                    <span class="font-semibold text-gray-600 w-32">Aplicó:</span>
                                    <span class="text-gray-800">${formatearFecha(candidato.fecha_aplicacion)}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Historial de Entrevistas -->
                    <div class="mt-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-calendar-alt mr-2 text-blue-600"></i>
                            Historial de Entrevistas
                        </h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            ${entrevistasHTML}
                        </div>
                    </div>
                    
                    <!-- Acciones -->
                    <div class="mt-6 flex flex-wrap gap-3 justify-end">
                        <button onclick="cerrarModal('modalPerfil')" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                            <i class="fas fa-times mr-2"></i>Cerrar
                        </button>
                        ${candidato.estatus !== 'Contratado' && candidato.estatus !== 'Rechazado' ? `
                            ${entrevistaProgramada ? `
                                <button onclick="cerrarModal('modalPerfil'); reagendarEntrevista(${entrevistaProgramada.id}, ${candidato.id});" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                                    <i class="fas fa-calendar-alt mr-2"></i>Reagendar Entrevista
                                </button>
                            ` : `
                                <button onclick="cerrarModal('modalPerfil'); programarEntrevista(${candidato.id});" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                    <i class="fas fa-calendar-plus mr-2"></i>Programar Entrevista
                                </button>
                            `}
                            <button onclick="cerrarModal('modalPerfil'); rechazarCandidato(${candidato.id});" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                <i class="fas fa-times-circle mr-2"></i>Rechazar
                            </button>
                            <button onclick="cerrarModal('modalPerfil'); contratarCandidato(${candidato.id});" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                <i class="fas fa-check-circle mr-2"></i>Contratar
                            </button>
                        ` : ''}
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
}

// Rechazar candidato
function rechazarCandidato(id) {
    if (!confirm('¿Está seguro de rechazar a este candidato?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('candidato_id', id);
    
    fetch('<?php echo BASE_URL; ?>reclutamiento/rechazar-candidato', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            location.reload();
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al rechazar candidato');
    });
}

// Cerrar modal
function cerrarModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.remove();
    }
}

// Funciones auxiliares
function calcularEdad(fechaNacimiento) {
    const hoy = new Date();
    const nacimiento = new Date(fechaNacimiento);
    let edad = hoy.getFullYear() - nacimiento.getFullYear();
    const mes = hoy.getMonth() - nacimiento.getMonth();
    if (mes < 0 || (mes === 0 && hoy.getDate() < nacimiento.getDate())) {
        edad--;
    }
    return edad;
}

function formatearFecha(fecha) {
    if (!fecha) return 'N/A';
    const d = new Date(fecha);
    const opciones = { year: 'numeric', month: 'long', day: 'numeric' };
    return d.toLocaleDateString('es-MX', opciones);
}

function getEstatusClass(estatus) {
    const clases = {
        'Nuevo': 'bg-blue-100 text-blue-800',
        'En Revisión': 'bg-yellow-100 text-yellow-800',
        'Entrevista': 'bg-purple-100 text-purple-800',
        'Evaluación': 'bg-indigo-100 text-indigo-800',
        'Seleccionado': 'bg-green-100 text-green-800',
        'Rechazado': 'bg-red-100 text-red-800',
        'Contratado': 'bg-green-200 text-green-900',
        'Programada': 'bg-blue-100 text-blue-800',
        'Realizada': 'bg-green-100 text-green-800',
        'Cancelada': 'bg-red-100 text-red-800',
        'Reprogramada': 'bg-yellow-100 text-yellow-800'
    };
    return clases[estatus] || 'bg-gray-100 text-gray-800';
}

// Función para toggle del panel de filtros
function toggleFiltros() {
    const panel = document.getElementById('filtrosPanel');
    if (panel.classList.contains('hidden')) {
        panel.classList.remove('hidden');
    } else {
        panel.classList.add('hidden');
    }
}

// Función para aplicar filtros
function aplicarFiltros() {
    const estatus = document.getElementById('filtroEstatus').value;
    const puesto = document.getElementById('filtroPuesto').value;
    const experiencia = document.getElementById('filtroExperiencia').value;
    
    // Construir URL con parámetros
    let url = '<?php echo BASE_URL; ?>reclutamiento?';
    const params = [];
    
    if (estatus) params.push('estatus=' + encodeURIComponent(estatus));
    if (puesto) params.push('puesto=' + encodeURIComponent(puesto));
    if (experiencia) params.push('experiencia=' + experiencia);
    
    if (params.length > 0) {
        url += params.join('&');
    }
    
    window.location.href = url;
}

// Función para exportar candidatos a CSV
function exportarCandidatos() {
    // Obtener los parámetros de filtro actuales si existen
    const urlParams = new URLSearchParams(window.location.search);
    const estatus = urlParams.get('estatus') || '';
    const puesto = urlParams.get('puesto') || '';
    const experiencia = urlParams.get('experiencia') || '';
    
    // Construir URL de exportación con los mismos filtros
    let url = '<?php echo BASE_URL; ?>reclutamiento/exportar-candidatos?';
    const params = [];
    
    if (estatus) params.push('estatus=' + encodeURIComponent(estatus));
    if (puesto) params.push('puesto=' + encodeURIComponent(puesto));
    if (experiencia) params.push('experiencia=' + experiencia);
    
    if (params.length > 0) {
        url += params.join('&');
    }
    
    // Abrir en nueva ventana para descargar
    window.location.href = url;
}
</script>
