<!-- Vista de Recibos de Nómina -->

<div class="mb-6">
    <div class="flex items-center">
        <a href="<?php echo BASE_URL; ?>nomina" class="text-gray-600 hover:text-gray-900 mr-4">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Recibos de Nómina</h1>
            <p class="text-gray-600 mt-1">Genera y consulta recibos de pago</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">
        <i class="fas fa-file-invoice text-blue-600 mr-2"></i>
        Generar Recibos
    </h3>
    
    <form class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Período</label>
                <select class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    <option>Seleccione un período...</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Empleado</label>
                <select class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    <option>Todos los empleados</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="button" class="w-full bg-gradient-sinforosa text-white px-4 py-2 rounded-lg hover:opacity-90">
                    <i class="fas fa-download mr-2"></i>
                    Generar Recibos
                </button>
            </div>
        </div>
    </form>
    
    <div class="mt-6 text-center text-gray-500">
        <i class="fas fa-file-pdf text-6xl mb-3"></i>
        <p>Selecciona un período para generar los recibos de nómina</p>
    </div>
</div>
