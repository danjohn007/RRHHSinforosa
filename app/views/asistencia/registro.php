<!-- Vista de Registro de Asistencia -->

<div class="mb-6">
    <div class="flex items-center">
        <a href="<?php echo BASE_URL; ?>asistencia" class="text-gray-600 hover:text-gray-900 mr-4">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Registro Manual de Asistencia</h1>
            <p class="text-gray-600 mt-1">Registra entradas y salidas manualmente</p>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-clock text-blue-600 mr-2"></i>
            Nuevo Registro
        </h3>
        
        <form class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Empleado</label>
                    <select class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500">
                        <option>Seleccione un empleado...</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha</label>
                    <input type="date" value="<?php echo date('Y-m-d'); ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hora de Entrada</label>
                    <input type="time" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hora de Salida</label>
                    <input type="time" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notas</label>
                    <textarea rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500" placeholder="Notas adicionales..."></textarea>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4">
                <a href="<?php echo BASE_URL; ?>asistencia" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="button" class="bg-gradient-sinforosa text-white px-6 py-3 rounded-lg hover:opacity-90">
                    <i class="fas fa-save mr-2"></i>
                    Guardar Registro
                </button>
            </div>
        </form>
    </div>
</div>
