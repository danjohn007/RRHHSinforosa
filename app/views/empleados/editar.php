<!-- Vista de Editar Empleado -->

<div class="mb-6">
    <div class="flex items-center">
        <a href="<?php echo BASE_URL; ?>empleados/ver?id=<?php echo $empleado['id']; ?>" class="text-gray-600 hover:text-gray-900 mr-4">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Editar Empleado</h1>
            <p class="text-gray-600 mt-1"><?php echo htmlspecialchars($empleado['numero_empleado']); ?> - <?php echo htmlspecialchars($empleado['nombre_completo']); ?></p>
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

<form method="POST" action="<?php echo BASE_URL; ?>empleados/editar?id=<?php echo $empleado['id']; ?>" class="bg-white rounded-lg shadow-md p-6">
    
    <!-- Información Personal -->
    <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
            <i class="fas fa-user text-purple-600 mr-2"></i>
            Información Personal
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nombres *</label>
                <input type="text" name="nombres" required value="<?php echo htmlspecialchars($empleado['nombres']); ?>"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Apellido Paterno *</label>
                <input type="text" name="apellido_paterno" required value="<?php echo htmlspecialchars($empleado['apellido_paterno']); ?>"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Apellido Materno</label>
                <input type="text" name="apellido_materno" value="<?php echo htmlspecialchars($empleado['apellido_materno'] ?? ''); ?>"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
        </div>
    </div>
    
    <!-- Información de Contacto -->
    <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
            <i class="fas fa-phone text-blue-600 mr-2"></i>
            Contacto
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email Personal</label>
                <input type="email" name="email_personal" value="<?php echo htmlspecialchars($empleado['email_personal'] ?? ''); ?>"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                <input type="text" name="telefono" value="<?php echo htmlspecialchars($empleado['telefono'] ?? ''); ?>"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Celular</label>
                <input type="text" name="celular" value="<?php echo htmlspecialchars($empleado['celular'] ?? ''); ?>"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
        </div>
    </div>
    
    <!-- Información Laboral -->
    <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
            <i class="fas fa-briefcase text-green-600 mr-2"></i>
            Información Laboral
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Departamento *</label>
                <input type="text" name="departamento" required value="<?php echo htmlspecialchars($empleado['departamento']); ?>"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                       list="departamentos">
                <datalist id="departamentos">
                    <option value="Administración">
                    <option value="Operaciones">
                    <option value="Recursos Humanos">
                    <option value="Ventas">
                    <option value="Cocina">
                    <option value="Mantenimiento">
                </datalist>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Puesto *</label>
                <input type="text" name="puesto" required value="<?php echo htmlspecialchars($empleado['puesto']); ?>"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Salario Mensual *</label>
                <input type="number" name="salario_mensual" step="0.01" required value="<?php echo htmlspecialchars($empleado['salario_mensual']); ?>"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estatus *</label>
                <select name="estatus" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <option value="Activo" <?php echo $empleado['estatus'] === 'Activo' ? 'selected' : ''; ?>>Activo</option>
                    <option value="Baja" <?php echo $empleado['estatus'] === 'Baja' ? 'selected' : ''; ?>>Baja</option>
                    <option value="Suspendido" <?php echo $empleado['estatus'] === 'Suspendido' ? 'selected' : ''; ?>>Suspendido</option>
                    <option value="Vacaciones" <?php echo $empleado['estatus'] === 'Vacaciones' ? 'selected' : ''; ?>>Vacaciones</option>
                </select>
            </div>
        </div>
    </div>
    
    <!-- Botones -->
    <div class="flex justify-end space-x-4">
        <a href="<?php echo BASE_URL; ?>empleados/ver?id=<?php echo $empleado['id']; ?>" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
            Cancelar
        </a>
        <button type="submit" class="bg-gradient-sinforosa text-white px-6 py-3 rounded-lg hover:opacity-90 transition">
            <i class="fas fa-save mr-2"></i>
            Guardar Cambios
        </button>
    </div>
</form>
