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
            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                <i class="fas fa-filter mr-2"></i>Filtrar
            </button>
            <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                <i class="fas fa-download mr-2"></i>Exportar
            </button>
        </div>
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
                        $<?php echo number_format($candidato['pretension_salarial'], 2); ?>
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
                        <div class="flex space-x-2">
                            <button class="text-blue-600 hover:text-blue-900" title="Ver perfil" onclick="verPerfil('<?php echo $candidato['id'] ?? ''; ?>')">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="text-green-600 hover:text-green-900" title="Programar entrevista" onclick="programarEntrevista('<?php echo $candidato['id'] ?? ''; ?>')">
                                <i class="fas fa-calendar-plus"></i>
                            </button>
                            <button class="text-purple-600 hover:text-purple-900" title="Contratar" onclick="contratarCandidato('<?php echo $candidato['id'] ?? ''; ?>')">
                                <i class="fas fa-user-check"></i>
                            </button>
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

function verPerfil(id) {
    alert('Ver perfil completo del candidato ID: ' + id + '\n\nEn una implementación completa, aquí se mostraría el perfil detallado del candidato.');
}

function programarEntrevista(id) {
    if (confirm('¿Desea programar una entrevista para este candidato?')) {
        alert('Redirigiendo a programación de entrevista...\n\nEn una implementación completa, aquí se abriría el formulario de programación de entrevista.');
        // window.location.href = '<?php echo BASE_URL; ?>reclutamiento/entrevistas?candidato=' + id;
    }
}

function contratarCandidato(id) {
    if (confirm('¿Está seguro de que desea marcar este candidato como contratado?')) {
        alert('Candidato marcado como contratado.\n\nEn una implementación completa, aquí se actualizaría el estado y se iniciaría el proceso de contratación.');
    }
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
    }
});
</script>
