<!-- Vista de Procesar Nómina -->

<?php if (!empty($success)): ?>
<div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded">
    <div class="flex">
        <i class="fas fa-check-circle text-green-400 text-xl mr-3"></i>
        <p class="text-green-700"><?php echo htmlspecialchars($success); ?></p>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($error)): ?>
<div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded">
    <div class="flex">
        <i class="fas fa-exclamation-circle text-red-400 text-xl mr-3"></i>
        <p class="text-red-700"><?php echo htmlspecialchars($error); ?></p>
    </div>
</div>
<?php endif; ?>

<div class="mb-6">
    <div class="flex items-center">
        <a href="<?php echo BASE_URL; ?>nomina" class="text-gray-600 hover:text-gray-900 mr-4">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Procesar Nómina</h1>
            <p class="text-gray-600 mt-1">Calcula y procesa un nuevo período de nómina</p>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto">
    <!-- Formulario de Nuevo Período -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-calendar-plus text-purple-600 mr-2"></i>
            Nuevo Período de Nómina
        </h3>
        
        <form method="POST" id="formNuevoPeriodo" class="space-y-4">
            <input type="hidden" name="crear_periodo" value="1">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Nómina *</label>
                    <select name="tipo" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500">
                        <option value="">Seleccione...</option>
                        <option value="Semanal">Semanal</option>
                        <option value="Quincenal" selected>Quincenal</option>
                        <option value="Mensual">Mensual</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Pago *</label>
                    <input type="date" name="fecha_pago" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio *</label>
                    <input type="date" name="fecha_inicio" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Fin *</label>
                    <input type="date" name="fecha_fin" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500">
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4">
                <a href="<?php echo BASE_URL; ?>nomina" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>
                    Crear Período
                </button>
            </div>
        </form>
    </div>
    
    <!-- Procesar Período Existente -->
    <?php if (!empty($periodosDisponibles)): ?>
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-calculator text-green-600 mr-2"></i>
            Procesar Período Existente
        </h3>
        
        <form method="POST" id="formProcesarNomina" onsubmit="return confirm('¿Está seguro de procesar este período de nómina? Esta acción calculará la nómina de todos los empleados activos.')">
            <input type="hidden" name="procesar_nomina" value="1">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Seleccione Período *</label>
                <select name="periodo_id" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500">
                    <option value="">Seleccione un período...</option>
                    <?php foreach ($periodosDisponibles as $periodo): ?>
                        <option value="<?php echo $periodo['id']; ?>">
                            <?php echo $periodo['tipo']; ?> - 
                            <?php echo date('d/m/Y', strtotime($periodo['fecha_inicio'])); ?> al 
                            <?php echo date('d/m/Y', strtotime($periodo['fecha_fin'])); ?>
                            (Pago: <?php echo date('d/m/Y', strtotime($periodo['fecha_pago'])); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="reprocesar" value="1" 
                           class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                    <span class="text-sm text-gray-700">
                        <i class="fas fa-exclamation-triangle text-yellow-500 mr-1"></i>
                        Reprocesar (eliminar cálculos anteriores y volver a calcular)
                    </span>
                </label>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="bg-gradient-sinforosa text-white px-6 py-3 rounded-lg hover:opacity-90">
                    <i class="fas fa-calculator mr-2"></i>
                    Procesar Nómina
                </button>
            </div>
        </form>
    </div>
    <?php endif; ?>
    
    <!-- Resultado del procesamiento -->
    <?php if ($resultado && $resultado['success']): ?>
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold text-green-600 mb-4">
            <i class="fas fa-check-circle mr-2"></i>
            Resultado del Procesamiento
        </h3>
        
        <div class="space-y-3">
            <div class="flex justify-between items-center p-3 bg-green-50 rounded">
                <span class="font-medium">Empleados procesados:</span>
                <span class="text-xl font-bold text-green-600"><?php echo $resultado['procesados']; ?></span>
            </div>
            
            <?php if (!empty($resultado['errores'])): ?>
            <div class="mt-4">
                <h4 class="font-medium text-red-600 mb-2">Errores encontrados:</h4>
                <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                    <?php foreach ($resultado['errores'] as $err): ?>
                        <li><?php echo htmlspecialchars($err); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <div class="mt-6 flex space-x-3">
                <a href="<?php echo BASE_URL; ?>nomina" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 inline-block">
                    <i class="fas fa-list mr-2"></i>
                    Ver Nóminas
                </a>
                <a href="<?php echo BASE_URL; ?>nomina/recibos" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 inline-block">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Generar Recibos
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Instrucciones -->
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Pasos para procesar nómina</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ol class="list-decimal list-inside space-y-1">
                        <li>Crea un nuevo período o selecciona uno existente</li>
                        <li>El sistema calculará automáticamente percepciones y deducciones (ISR, IMSS)</li>
                        <li>Aplica incidencias, bonos y préstamos registrados</li>
                        <li>Genera los recibos y archivos de dispersión bancaria</li>
                        <li>Los cálculos incluyen: ISR 2026, IMSS, subsidio al empleo</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Validación de fechas
document.getElementById('formNuevoPeriodo')?.addEventListener('submit', function(e) {
    const fechaInicio = document.querySelector('[name="fecha_inicio"]').value;
    const fechaFin = document.querySelector('[name="fecha_fin"]').value;
    const fechaPago = document.querySelector('[name="fecha_pago"]').value;
    
    if (fechaInicio && fechaFin && fechaInicio > fechaFin) {
        e.preventDefault();
        alert('La fecha de inicio no puede ser mayor que la fecha de fin');
        return false;
    }
    
    if (fechaPago && fechaFin && fechaPago < fechaFin) {
        if (!confirm('La fecha de pago es anterior a la fecha de fin del período. ¿Desea continuar?')) {
            e.preventDefault();
            return false;
        }
    }
});
</script>
