<!-- Vista de Lista de Sucursales -->

<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Gestión de Sucursales</h1>
            <p class="text-gray-600 mt-1">Administra las sucursales del sistema</p>
        </div>
        <a href="<?php echo BASE_URL; ?>sucursales/crear" 
           class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-medium inline-flex items-center shadow-lg transition">
            <i class="fas fa-plus mr-2"></i>
            Nueva Sucursal
        </a>
    </div>
</div>

<!-- Lista de Sucursales -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if (empty($sucursales)): ?>
        <div class="col-span-full bg-white rounded-lg shadow-md p-8 text-center">
            <i class="fas fa-building text-gray-300 text-6xl mb-4"></i>
            <p class="text-gray-500 text-lg">No hay sucursales registradas</p>
            <a href="<?php echo BASE_URL; ?>sucursales/crear" class="text-purple-600 hover:text-purple-800 font-medium mt-2 inline-block">
                Crear la primera sucursal
            </a>
        </div>
    <?php else: ?>
        <?php foreach ($sucursales as $sucursal): ?>
            <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition overflow-hidden">
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-4">
                    <h3 class="text-white text-xl font-bold"><?php echo htmlspecialchars($sucursal['nombre']); ?></h3>
                    <p class="text-purple-100 text-sm"><?php echo htmlspecialchars($sucursal['codigo']); ?></p>
                </div>
                
                <div class="p-4">
                    <!-- Información -->
                    <?php if (!empty($sucursal['direccion'])): ?>
                        <div class="flex items-start mb-3">
                            <i class="fas fa-map-marker-alt text-gray-400 mt-1 mr-3"></i>
                            <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($sucursal['direccion']); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($sucursal['telefono'])): ?>
                        <div class="flex items-center mb-3">
                            <i class="fas fa-phone text-gray-400 mr-3"></i>
                            <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($sucursal['telefono']); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($sucursal['url_publica'])): ?>
                        <div class="flex items-center mb-3">
                            <i class="fas fa-link text-gray-400 mr-3"></i>
                            <a href="<?php echo BASE_URL; ?>publico/asistencia/<?php echo htmlspecialchars($sucursal['url_publica']); ?>" 
                               target="_blank"
                               class="text-purple-600 hover:text-purple-800 text-sm">
                                <?php echo htmlspecialchars($sucursal['url_publica']); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Estadísticas -->
                    <div class="grid grid-cols-3 gap-2 mt-4 pt-4 border-t">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-purple-600"><?php echo $sucursal['total_empleados']; ?></p>
                            <p class="text-xs text-gray-500">Empleados</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-blue-600"><?php echo $sucursal['total_gerentes']; ?></p>
                            <p class="text-xs text-gray-500">Gerentes</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-green-600"><?php echo $sucursal['total_dispositivos']; ?></p>
                            <p class="text-xs text-gray-500">Dispositivos</p>
                        </div>
                    </div>
                    
                    <!-- Estado -->
                    <div class="mt-4">
                        <?php if ($sucursal['activo']): ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i> Activa
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1"></i> Inactiva
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Acciones -->
                    <div class="mt-4 flex gap-2">
                        <a href="<?php echo BASE_URL; ?>sucursales/editar?id=<?php echo $sucursal['id']; ?>" 
                           class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 rounded-lg text-sm font-medium transition">
                            <i class="fas fa-edit mr-1"></i> Editar
                        </a>
                        <button onclick="eliminarSucursal(<?php echo $sucursal['id']; ?>)" 
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
function eliminarSucursal(id) {
    if (!confirm('¿Estás seguro de que deseas eliminar esta sucursal? Esta acción no se puede deshacer.')) {
        return;
    }
    
    fetch('<?php echo BASE_URL; ?>sucursales/eliminar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id=' + id
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al eliminar la sucursal');
    });
}
</script>
