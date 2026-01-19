<div class="fade-in">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between" data-aos="fade-down">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestión de Usuarios</h1>
            <p class="mt-1 text-sm text-gray-600">Administra los usuarios del sistema</p>
        </div>
        <a href="<?php echo BASE_URL; ?>usuarios/crear" 
           class="inline-flex items-center px-4 py-2 bg-gradient-sinforosa text-white rounded-lg hover:opacity-90 transition">
            <i class="fas fa-plus mr-2"></i>
            Nuevo Usuario
        </a>
    </div>

    <!-- Tabla de usuarios -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden" data-aos="fade-up" data-aos-delay="100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Usuario
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Email
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Rol
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Empleado Relacionado
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Último Acceso
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($usuarios)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-users text-4xl mb-3 text-gray-400"></i>
                                <p>No hay usuarios registrados</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-full bg-gradient-sinforosa flex items-center justify-center text-white font-semibold">
                                            <?php echo strtoupper(substr($usuario['nombre'], 0, 2)); ?>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($usuario['nombre']); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?php echo htmlspecialchars($usuario['email']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $roles = [
                                        'admin' => ['text' => 'Administrador', 'color' => 'red'],
                                        'rrhh' => ['text' => 'RRHH', 'color' => 'blue'],
                                        'gerente' => ['text' => 'Gerente', 'color' => 'purple'],
                                        'empleado' => ['text' => 'Empleado', 'color' => 'green'],
                                        'socio' => ['text' => 'Socio', 'color' => 'yellow'],
                                        'empleado_confianza' => ['text' => 'Empleado de Confianza', 'color' => 'indigo']
                                    ];
                                    $rolInfo = $roles[$usuario['rol']] ?? ['text' => $usuario['rol'], 'color' => 'gray'];
                                    ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-<?php echo $rolInfo['color']; ?>-100 text-<?php echo $rolInfo['color']; ?>-800">
                                        <?php echo $rolInfo['text']; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php if ($usuario['empleado_id']): ?>
                                        <div class="flex flex-col">
                                            <span class="font-medium"><?php echo htmlspecialchars($usuario['empleado_nombre'] ?? 'N/A'); ?></span>
                                            <span class="text-xs text-gray-500"><?php echo htmlspecialchars($usuario['codigo_empleado'] ?? ''); ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-gray-400 italic">Sin relación</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($usuario['activo']): ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Activo
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Inactivo
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php 
                                    if ($usuario['ultimo_acceso']) {
                                        echo date('d/m/Y H:i', strtotime($usuario['ultimo_acceso']));
                                    } else {
                                        echo '<span class="text-gray-400">Nunca</span>';
                                    }
                                    ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="<?php echo BASE_URL; ?>usuarios/editar?id=<?php echo $usuario['id']; ?>" 
                                       class="text-blue-600 hover:text-blue-900 mr-3" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($usuario['id'] != $_SESSION['user_id']): ?>
                                        <button onclick="eliminarUsuario(<?php echo $usuario['id']; ?>, '<?php echo htmlspecialchars($usuario['nombre'], ENT_QUOTES); ?>')" 
                                                class="text-red-600 hover:text-red-900" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function eliminarUsuario(id, nombre) {
    if (confirm('¿Está seguro de eliminar al usuario "' + nombre + '"?')) {
        fetch(BASE_URL + 'usuarios/eliminar', {
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
            alert('Error al eliminar el usuario');
        });
    }
}
</script>
