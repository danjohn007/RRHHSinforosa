<!-- Vista de Procesar Nómina -->

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
        
        <form class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Nómina</label>
                    <select class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500">
                        <option>Seleccione...</option>
                        <option>Semanal</option>
                        <option>Quincenal</option>
                        <option>Mensual</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Pago</label>
                    <input type="date" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio</label>
                    <input type="date" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Fin</label>
                    <input type="date" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500">
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4">
                <a href="<?php echo BASE_URL; ?>nomina" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="button" class="bg-gradient-sinforosa text-white px-6 py-3 rounded-lg hover:opacity-90">
                    <i class="fas fa-calculator mr-2"></i>
                    Calcular Nómina
                </button>
            </div>
        </form>
    </div>
    
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
                        <li>Selecciona el tipo de período (semanal, quincenal o mensual)</li>
                        <li>Define las fechas del período y la fecha de pago</li>
                        <li>El sistema calculará automáticamente percepciones y deducciones</li>
                        <li>Revisa y aprueba los cálculos antes de finalizar</li>
                        <li>Genera los recibos y archivos de dispersión</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
