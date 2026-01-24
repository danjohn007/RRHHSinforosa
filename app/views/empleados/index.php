<!-- Listado de Empleados -->

<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Gestión de Empleados</h1>
            <p class="text-gray-600 mt-1">Administra la información de los colaboradores</p>
        </div>
        <div class="flex gap-3">
            <button onclick="mostrarModalImportar()" class="bg-teal-600 text-white px-6 py-3 rounded-lg hover:bg-teal-700 transition flex items-center">
                <i class="fas fa-file-import mr-2"></i> Importar Colaboradores
            </button>
            <a href="<?php echo BASE_URL; ?>empleados/crear" class="bg-gradient-sinforosa text-white px-6 py-3 rounded-lg hover:opacity-90 transition flex items-center">
                <i class="fas fa-plus mr-2"></i> Nuevo Empleado
            </a>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
    <form method="GET" action="<?php echo BASE_URL; ?>empleados" class="space-y-4">
        <!-- Buscador -->
        <div class="w-full">
            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
            <input type="text" name="search" 
                   placeholder="Buscar por nombre, email, teléfono o No. Empleado..." 
                   value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
        </div>
        
        <!-- Filtros en línea -->
        <div class="flex flex-wrap gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estatus</label>
                <select name="estatus" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <option value="">Todos</option>
                    <option value="Activo" <?php echo ($filters['estatus'] ?? '') === 'Activo' ? 'selected' : ''; ?>>Activo</option>
                    <option value="Baja" <?php echo ($filters['estatus'] ?? '') === 'Baja' ? 'selected' : ''; ?>>Baja</option>
                    <option value="Suspendido" <?php echo ($filters['estatus'] ?? '') === 'Suspendido' ? 'selected' : ''; ?>>Suspendido</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Departamento</label>
                <select name="departamento" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <option value="">Todos</option>
                    <?php foreach ($departamentos as $dept): ?>
                        <option value="<?php echo htmlspecialchars($dept); ?>" <?php echo ($filters['departamento'] ?? '') === $dept ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($dept); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sucursal</label>
                <select name="sucursal" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <option value="">Todas</option>
                    <?php foreach ($sucursales as $suc): ?>
                        <option value="<?php echo $suc['id']; ?>" <?php echo ($filters['sucursal'] ?? '') == $suc['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($suc['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-filter mr-2"></i> Filtrar
                </button>
            </div>
            
            <?php if (!empty($filters)): ?>
            <div class="flex items-end">
                <a href="<?php echo BASE_URL; ?>empleados" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-times mr-2"></i> Limpiar
                </a>
            </div>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Tabla de Empleados -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        No. Empleado
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Nombre Completo
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Sucursal
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Departamento
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Puesto
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Antigüedad
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Estatus
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($empleados)): ?>
                <tr>
                    <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                        No se encontraron empleados
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($empleados as $empleado): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($empleado['numero_empleado']); ?></span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 bg-purple-100 rounded-full flex items-center justify-center">
                                <span class="text-purple-600 font-semibold">
                                    <?php echo strtoupper(substr($empleado['nombres'], 0, 1) . substr($empleado['apellido_paterno'], 0, 1)); ?>
                                </span>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($empleado['nombre_completo']); ?></div>
                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($empleado['email_personal'] ?? 'Sin email'); ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-900"><?php echo htmlspecialchars($empleado['sucursal_nombre'] ?? 'Sin asignar'); ?></span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-900"><?php echo htmlspecialchars($empleado['departamento']); ?></span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-900"><?php echo htmlspecialchars($empleado['puesto']); ?></span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-900"><?php echo $empleado['anios_antiguedad']; ?> años</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php
                        $statusColors = [
                            'Activo' => 'bg-green-100 text-green-800',
                            'Baja' => 'bg-red-100 text-red-800',
                            'Suspendido' => 'bg-yellow-100 text-yellow-800',
                            'Vacaciones' => 'bg-blue-100 text-blue-800'
                        ];
                        $color = $statusColors[$empleado['estatus']] ?? 'bg-gray-100 text-gray-800';
                        ?>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $color; ?>">
                            <?php echo htmlspecialchars($empleado['estatus']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <a href="<?php echo BASE_URL; ?>empleados/ver?id=<?php echo $empleado['id']; ?>" 
                               class="text-blue-600 hover:text-blue-900" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?php echo BASE_URL; ?>empleados/editar?id=<?php echo $empleado['id']; ?>" 
                               class="text-green-600 hover:text-green-900" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="abrirCalculoRapido(<?php echo $empleado['id']; ?>)" 
                                    class="text-purple-600 hover:text-purple-900" title="Cálculo rápido de nómina">
                                <i class="fas fa-calculator"></i>
                            </button>
                            <a href="<?php echo BASE_URL; ?>empleados/carta-recomendacion?id=<?php echo $empleado['id']; ?>" 
                               class="text-orange-600 hover:text-orange-900" title="Carta de recomendación" target="_blank">
                                <i class="fas fa-file-alt"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Estadísticas rápidas -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-600">Total Empleados</p>
        <p class="text-2xl font-bold text-gray-800"><?php echo count($empleados); ?></p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-600">Activos</p>
        <p class="text-2xl font-bold text-green-600">
            <?php echo count(array_filter($empleados, fn($e) => $e['estatus'] === 'Activo')); ?>
        </p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-600">De Baja</p>
        <p class="text-2xl font-bold text-red-600">
            <?php echo count(array_filter($empleados, fn($e) => $e['estatus'] === 'Baja')); ?>
        </p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-600">Departamentos</p>
        <p class="text-2xl font-bold text-blue-600"><?php echo count($departamentos); ?></p>
    </div>
</div>

<!-- Modal de Cálculo Rápido de Nómina -->
<div id="calculoModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Cálculo Rápido de Nómina</h3>
            <button onclick="cerrarCalculoModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div id="calculoLoading" class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-4xl text-purple-600"></i>
            <p class="mt-4 text-gray-600">Calculando...</p>
        </div>
        
        <div id="calculoError" class="hidden bg-red-50 border-l-4 border-red-400 p-4 rounded mb-4">
            <p class="text-red-700"></p>
        </div>
        
        <div id="calculoContenido" class="hidden">
            <!-- Información del Empleado -->
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <h4 class="font-semibold text-gray-800 mb-2">Empleado</h4>
                <p id="empleadoNombre" class="text-gray-700"></p>
            </div>
            
            <!-- Periodo -->
            <div class="bg-blue-50 rounded-lg p-4 mb-4">
                <h4 class="font-semibold text-gray-800 mb-2">Periodo Calculado</h4>
                <p id="periodoFechas" class="text-gray-700"></p>
            </div>
            
            <!-- Asistencias -->
            <div class="bg-white border rounded-lg p-4 mb-4">
                <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                    <i class="fas fa-calendar-check text-green-600 mr-2"></i>
                    Asistencias
                </h4>
                <div class="grid grid-cols-3 gap-4 mb-3">
                    <div>
                        <p class="text-sm text-gray-600">Días Trabajados</p>
                        <p id="diasTrabajados" class="text-xl font-bold text-green-600">0</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Horas Normales</p>
                        <p id="horasNormales" class="text-xl font-bold text-blue-600">0</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Horas Extras</p>
                        <p id="horasExtras" class="text-xl font-bold text-orange-600">0</p>
                    </div>
                </div>
            </div>
            
            <!-- Incidencias -->
            <div class="bg-white border rounded-lg p-4 mb-4">
                <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                    Incidencias
                </h4>
                <div id="incidenciasList" class="text-sm text-gray-600">
                    No hay incidencias registradas
                </div>
            </div>
            
            <!-- Deducciones -->
            <div class="bg-white border rounded-lg p-4 mb-4">
                <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                    <i class="fas fa-minus-circle text-red-600 mr-2"></i>
                    Deducciones
                </h4>
                <div id="deduccionesList"></div>
            </div>
            
            <!-- Resumen de Cálculos -->
            <div class="bg-gradient-to-r from-purple-50 to-blue-50 rounded-lg p-4">
                <h4 class="font-semibold text-gray-800 mb-3">Resumen Financiero</h4>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-700">Salario Base:</span>
                        <span id="salarioBase" class="font-semibold text-gray-800">$0.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-700">Pago Horas Extras:</span>
                        <span id="pagoHorasExtras" class="font-semibold text-gray-800">$0.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-700">Bonos:</span>
                        <span id="bonos" class="font-semibold text-gray-800">$0.00</span>
                    </div>
                    <div class="flex justify-between border-t pt-2">
                        <span class="font-semibold text-gray-700">Total Percepciones:</span>
                        <span id="totalPercepciones" class="font-bold text-green-600">$0.00</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">ISR:</span>
                        <span id="isrCalc" class="text-gray-700">$0.00</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">IMSS:</span>
                        <span id="imssCalc" class="text-gray-700">$0.00</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Otros Descuentos:</span>
                        <span id="descuentosCalc" class="text-gray-700">$0.00</span>
                    </div>
                    <div class="flex justify-between border-t pt-2">
                        <span class="font-semibold text-gray-700">Total Deducciones:</span>
                        <span id="totalDeducciones" class="font-bold text-red-600">$0.00</span>
                    </div>
                    <div class="flex justify-between border-t-2 border-purple-600 pt-2">
                        <span class="font-bold text-gray-800 text-lg">NETO A PAGAR:</span>
                        <span id="totalNeto" class="font-bold text-purple-600 text-xl">$0.00</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Importación de Empleados -->
<div id="modalImportarEmpleados" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-3xl w-full p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-file-import text-teal-600 mr-2"></i>
                Importar Listado de Colaboradores
            </h2>
            <button onclick="cerrarModalImportar()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        
        <!-- Instrucciones -->
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 rounded">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                <div>
                    <h3 class="font-semibold text-blue-800 mb-2">Instrucciones de Importación</h3>
                    <p class="text-sm text-blue-700 mb-2">El archivo CSV debe tener las siguientes columnas (en este orden):</p>
                    <ul class="list-disc list-inside text-sm text-blue-700 space-y-1">
                        <li><strong>nombres</strong> (requerido): Nombre(s) del empleado</li>
                        <li><strong>apellido_paterno</strong> (requerido): Apellido paterno</li>
                        <li><strong>apellido_materno</strong>: Apellido materno</li>
                        <li><strong>curp</strong>: CURP del empleado</li>
                        <li><strong>rfc</strong>: RFC del empleado</li>
                        <li><strong>nss</strong>: Número de Seguro Social</li>
                        <li><strong>fecha_nacimiento</strong>: Fecha (formato: YYYY-MM-DD)</li>
                        <li><strong>genero</strong>: M, F u Otro</li>
                        <li><strong>email_personal</strong>: Correo electrónico</li>
                        <li><strong>celular</strong>: Número de celular (10 dígitos)</li>
                        <li><strong>fecha_ingreso</strong> (requerido): Fecha (formato: YYYY-MM-DD)</li>
                        <li><strong>tipo_contrato</strong>: Planta, Eventual, Honorarios, Practicante</li>
                        <li><strong>departamento</strong> (requerido): Departamento</li>
                        <li><strong>puesto</strong> (requerido): Puesto</li>
                        <li><strong>salario_mensual</strong> (requerido): Salario mensual (número)</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Botón de descarga de plantilla -->
        <div class="mb-6 text-center">
            <button onclick="descargarPlantilla()" class="text-sm bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition">
                <i class="fas fa-download mr-2"></i>
                Descargar plantilla de ejemplo
            </button>
        </div>
        
        <!-- Formulario de importación -->
        <form id="formImportar" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Archivo CSV *</label>
                <input type="file" 
                       id="archivoImportar" 
                       name="archivo" 
                       accept=".csv" 
                       required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500">
                <p class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-info-circle"></i>
                    Formato: CSV separado por comas con codificación UTF-8. Máximo 5MB.
                </p>
            </div>
            
            <!-- Advertencia -->
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mt-1 mr-3"></i>
                    <div>
                        <p class="text-sm text-yellow-700">
                            <strong>Advertencia:</strong> La importación creará nuevos empleados. Los empleados existentes no serán modificados. 
                            Asegúrese de revisar el archivo antes de importar.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="cerrarModalImportar()" 
                        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancelar
                </button>
                <button type="button" onclick="importarEmpleados()" 
                        id="btnImportar"
                        class="bg-teal-600 text-white px-6 py-2 rounded-lg hover:bg-teal-700 transition">
                    <i class="fas fa-upload mr-2"></i>
                    Importar Empleados
                </button>
            </div>
        </form>
    </div>
</div>

<script>
async function abrirCalculoRapido(empleadoId) {
    const modal = document.getElementById('calculoModal');
    const loading = document.getElementById('calculoLoading');
    const error = document.getElementById('calculoError');
    const contenido = document.getElementById('calculoContenido');
    
    // Mostrar modal y loading
    modal.classList.remove('hidden');
    loading.classList.remove('hidden');
    error.classList.add('hidden');
    contenido.classList.add('hidden');
    
    try {
        const response = await fetch('<?php echo BASE_URL; ?>empleados/calculo-rapido-nomina?id=' + empleadoId);
        const data = await response.json();
        
        if (data.success) {
            // Llenar información del empleado
            document.getElementById('empleadoNombre').textContent = 
                data.empleado.nombre + ' - ' + data.empleado.numero + ' - ' + data.empleado.puesto;
            
            // Periodo
            document.getElementById('periodoFechas').textContent = 
                'Desde ' + data.periodo.fecha_inicio + ' hasta ' + data.periodo.fecha_fin;
            
            // Asistencias
            document.getElementById('diasTrabajados').textContent = data.asistencias.dias_trabajados;
            document.getElementById('horasNormales').textContent = data.asistencias.horas_normales;
            document.getElementById('horasExtras').textContent = data.asistencias.horas_extras;
            
            // Incidencias
            const incidenciasList = document.getElementById('incidenciasList');
            if (data.incidencias && data.incidencias.length > 0) {
                incidenciasList.innerHTML = '<ul class="space-y-1">' + 
                    data.incidencias.map(inc => 
                        '<li>• ' + inc.fecha + ' - ' + inc.tipo_incidencia + 
                        (inc.descripcion ? ': ' + inc.descripcion : '') + 
                        (inc.monto ? ' ($' + parseFloat(inc.monto).toFixed(2) + ')' : '') + '</li>'
                    ).join('') + '</ul>';
            } else {
                incidenciasList.innerHTML = '<p class="text-gray-500">No hay incidencias registradas</p>';
            }
            
            // Deducciones
            const deduccionesList = document.getElementById('deduccionesList');
            if (data.deducciones && data.deducciones.length > 0) {
                deduccionesList.innerHTML = '<ul class="space-y-1">' + 
                    data.deducciones.map(ded => 
                        '<li class="flex justify-between"><span>• ' + ded.concepto + '</span>' +
                        '<span class="font-semibold">$' + parseFloat(ded.monto).toFixed(2) + '</span></li>'
                    ).join('') + '</ul>';
            } else {
                deduccionesList.innerHTML = '<p class="text-gray-500">No hay deducciones adicionales</p>';
            }
            
            // Cálculos
            document.getElementById('salarioBase').textContent = '$' + parseFloat(data.calculos.salario_base).toFixed(2);
            document.getElementById('pagoHorasExtras').textContent = '$' + parseFloat(data.calculos.pago_horas_extras).toFixed(2);
            document.getElementById('bonos').textContent = '$' + parseFloat(data.calculos.bonos).toFixed(2);
            document.getElementById('totalPercepciones').textContent = '$' + parseFloat(data.calculos.total_percepciones).toFixed(2);
            document.getElementById('isrCalc').textContent = '$' + parseFloat(data.calculos.isr).toFixed(2);
            document.getElementById('imssCalc').textContent = '$' + parseFloat(data.calculos.imss).toFixed(2);
            document.getElementById('descuentosCalc').textContent = '$' + parseFloat(data.calculos.descuentos).toFixed(2);
            document.getElementById('totalDeducciones').textContent = '$' + parseFloat(data.calculos.total_deducciones).toFixed(2);
            document.getElementById('totalNeto').textContent = '$' + parseFloat(data.calculos.total_neto).toFixed(2);
            
            // Mostrar contenido
            loading.classList.add('hidden');
            contenido.classList.remove('hidden');
        } else {
            loading.classList.add('hidden');
            error.querySelector('p').textContent = data.message;
            error.classList.remove('hidden');
        }
    } catch (err) {
        loading.classList.add('hidden');
        error.querySelector('p').textContent = 'Error de conexión. Por favor intente nuevamente.';
        error.classList.remove('hidden');
    }
}

function cerrarCalculoModal() {
    document.getElementById('calculoModal').classList.add('hidden');
}

// Funciones para importar empleados
function mostrarModalImportar() {
    document.getElementById('modalImportarEmpleados').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function cerrarModalImportar() {
    document.getElementById('modalImportarEmpleados').classList.add('hidden');
    document.body.style.overflow = '';
    document.getElementById('formImportar').reset();
}

function descargarPlantilla() {
    window.location.href = '<?php echo BASE_URL; ?>empleados/descargar-plantilla';
}

function importarEmpleados() {
    const form = document.getElementById('formImportar');
    const fileInput = document.getElementById('archivoImportar');
    const file = fileInput.files[0];
    
    if (!file) {
        alert('Por favor seleccione un archivo CSV');
        return;
    }
    
    // Validar extensión
    if (!file.name.endsWith('.csv')) {
        alert('El archivo debe ser formato CSV');
        return;
    }
    
    // Validar tamaño (máximo 5MB)
    if (file.size > 5 * 1024 * 1024) {
        alert('El archivo no debe exceder 5MB');
        return;
    }
    
    // Mostrar loading
    document.getElementById('btnImportar').disabled = true;
    document.getElementById('btnImportar').innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Procesando...';
    
    // Enviar archivo
    const formData = new FormData(form);
    
    fetch('<?php echo BASE_URL; ?>empleados/importar', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`✓ Importación completada\n\n${data.registros_exitosos} empleados importados\n${data.registros_errores} errores`);
            cerrarModalImportar();
            location.reload();
        } else {
            alert('✗ Error en la importación: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al procesar la importación');
    })
    .finally(() => {
        document.getElementById('btnImportar').disabled = false;
        document.getElementById('btnImportar').innerHTML = '<i class="fas fa-upload mr-2"></i> Importar Empleados';
    });
}
</script>
