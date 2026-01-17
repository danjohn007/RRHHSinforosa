<!-- Formulario Crear Sucursal -->

<div class="mb-6">
    <div class="flex items-center">
        <a href="<?php echo BASE_URL; ?>sucursales" class="text-gray-600 hover:text-gray-900 mr-4">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Nueva Sucursal</h1>
            <p class="text-gray-600 mt-1">Registra una nueva sucursal en el sistema</p>
        </div>
    </div>
</div>

<?php if (!empty($error)): ?>
<div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded">
    <p class="text-red-700"><?php echo htmlspecialchars($error); ?></p>
</div>
<?php endif; ?>

<?php if (!empty($success)): ?>
<div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 rounded">
    <p class="text-green-700"><?php echo htmlspecialchars($success); ?></p>
</div>
<?php endif; ?>

<form method="POST" action="<?php echo BASE_URL; ?>sucursales/crear" class="bg-white rounded-lg shadow-md p-6">
    
    <!-- Información de la Sucursal -->
    <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
            <i class="fas fa-building text-purple-600 mr-2"></i>
            Información de la Sucursal
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nombre *</label>
                <input type="text" name="nombre" required 
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                       placeholder="Ej: Sucursal Centro">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Código *</label>
                <input type="text" name="codigo" required 
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                       placeholder="Ej: SUC-001">
            </div>
        </div>
        
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Dirección</label>
            <textarea name="direccion" rows="3"
                      class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                      placeholder="Dirección completa de la sucursal"></textarea>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                <input type="text" name="telefono" 
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                       placeholder="Ej: 442-123-4567">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">URL Pública</label>
                <input type="text" name="url_publica" 
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                       placeholder="Ej: sucursal-centro">
                <p class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-info-circle"></i>
                    Identificador único para el sistema de asistencia pública. Solo letras, números y guiones.
                </p>
            </div>
        </div>
        
        <div class="mt-4">
            <label class="flex items-center space-x-2">
                <input type="checkbox" name="activo" value="1" checked
                       class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                <span class="text-sm font-medium text-gray-700">Sucursal activa</span>
            </label>
        </div>
    </div>
    
    <!-- Botones -->
    <div class="flex justify-end space-x-4">
        <a href="<?php echo BASE_URL; ?>sucursales" 
           class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
            Cancelar
        </a>
        <button type="submit" 
                class="bg-gradient-sinforosa text-white px-6 py-3 rounded-lg hover:opacity-90 transition">
            <i class="fas fa-save mr-2"></i>
            Guardar Sucursal
        </button>
    </div>
</form>
