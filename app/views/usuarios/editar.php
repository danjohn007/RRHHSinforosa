<div class="fade-in max-w-3xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center space-x-3 mb-2">
            <a href="<?php echo BASE_URL; ?>usuarios" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Editar Usuario</h1>
        </div>
        <p class="text-sm text-gray-600">Modifique los datos del usuario del sistema</p>
    </div>

    <!-- Mensajes de error/éxito -->
    <?php if (!empty($error)): ?>
    <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded">
        <div class="flex">
            <i class="fas fa-exclamation-circle text-red-400 mr-3"></i>
            <p class="text-sm text-red-700"><?php echo htmlspecialchars($error); ?></p>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
    <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded">
        <div class="flex">
            <i class="fas fa-check-circle text-green-400 mr-3"></i>
            <p class="text-sm text-green-700"><?php echo htmlspecialchars($success); ?></p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Formulario -->
    <form method="POST" class="bg-white rounded-lg shadow-sm p-6 space-y-6">
        
        <!-- Información Básica -->
        <div class="border-b border-gray-200 pb-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                <i class="fas fa-user mr-2 text-purple-600"></i>
                Información del Usuario
            </h3>
            
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre Completo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="nombre" 
                           name="nombre" 
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                           placeholder="Ej: Juan Pérez García"
                           value="<?php echo htmlspecialchars($_POST['nombre'] ?? $usuario['nombre']); ?>">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Correo Electrónico <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                           placeholder="usuario@sinforosa.com"
                           value="<?php echo htmlspecialchars($_POST['email'] ?? $usuario['email']); ?>">
                </div>

                <div>
                    <label for="rol" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipo de Usuario (Rol) <span class="text-red-500">*</span>
                    </label>
                    <select id="rol" 
                            name="rol" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="">Seleccione un rol...</option>
                        <?php 
                        $rolActual = $_POST['rol'] ?? $usuario['rol'];
                        ?>
                        <option value="admin" <?php echo ($rolActual == 'admin') ? 'selected' : ''; ?>>Administrador</option>
                        <option value="rrhh" <?php echo ($rolActual == 'rrhh') ? 'selected' : ''; ?>>RRHH</option>
                        <option value="gerente" <?php echo ($rolActual == 'gerente') ? 'selected' : ''; ?>>Gerente</option>
                        <option value="empleado" <?php echo ($rolActual == 'empleado') ? 'selected' : ''; ?>>Empleado</option>
                        <option value="socio" <?php echo ($rolActual == 'socio') ? 'selected' : ''; ?>>Socio</option>
                        <option value="empleado_confianza" <?php echo ($rolActual == 'empleado_confianza') ? 'selected' : ''; ?>>Empleado de Confianza</option>
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Este campo es obligatorio y define los permisos del usuario</p>
                </div>

                <div>
                    <label for="empleado_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Relacionar con Empleado <span class="text-gray-400">(Opcional)</span>
                    </label>
                    <select id="empleado_id" 
                            name="empleado_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="">Sin relación con empleado</option>
                        <?php 
                        $empleadoIdActual = $_POST['empleado_id'] ?? $usuario['empleado_id'];
                        foreach ($empleadosDisponibles as $empleado): 
                        ?>
                            <option value="<?php echo $empleado['id']; ?>" 
                                    <?php echo ($empleadoIdActual == $empleado['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($empleado['nombre_completo']); ?> 
                                (<?php echo htmlspecialchars($empleado['codigo_empleado']); ?>) - 
                                <?php echo htmlspecialchars($empleado['puesto']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Opcionalmente puede relacionar este usuario con un empleado existente</p>
                </div>
            </div>
        </div>

        <!-- Contraseña -->
        <div class="border-b border-gray-200 pb-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                <i class="fas fa-lock mr-2 text-purple-600"></i>
                Cambiar Contraseña <span class="text-gray-400 text-sm font-normal">(Dejar en blanco para mantener la actual)</span>
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Nueva Contraseña
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           minlength="6"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                           placeholder="Mínimo 6 caracteres">
                    <p class="mt-1 text-xs text-gray-500">Solo complete si desea cambiar la contraseña</p>
                </div>

                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirmar Nueva Contraseña
                    </label>
                    <input type="password" 
                           id="confirm_password" 
                           name="confirm_password" 
                           minlength="6"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                           placeholder="Repita la contraseña">
                </div>
            </div>
        </div>

        <!-- Estado -->
        <div>
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" 
                       name="activo" 
                       id="activo"
                       <?php echo (isset($_POST['activo']) ? 'checked' : ($usuario['activo'] ? 'checked' : '')); ?>
                       class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                <span class="ml-2 text-sm text-gray-700">Usuario activo</span>
            </label>
            <p class="mt-1 text-xs text-gray-500">Los usuarios inactivos no pueden iniciar sesión</p>
        </div>

        <!-- Botones -->
        <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
            <a href="<?php echo BASE_URL; ?>usuarios" 
               class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                Cancelar
            </a>
            <button type="submit" 
                    class="px-6 py-2 bg-gradient-sinforosa text-white rounded-lg hover:opacity-90 transition">
                <i class="fas fa-save mr-2"></i>
                Guardar Cambios
            </button>
        </div>
    </form>
</div>
