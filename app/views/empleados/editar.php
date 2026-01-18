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
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">CURP</label>
                <input type="text" name="curp" maxlength="18" value="<?php echo htmlspecialchars($empleado['curp'] ?? ''); ?>"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 uppercase">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">RFC</label>
                <input type="text" name="rfc" maxlength="13" value="<?php echo htmlspecialchars($empleado['rfc'] ?? ''); ?>"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 uppercase">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">NSS</label>
                <input type="text" name="nss" maxlength="11" value="<?php echo htmlspecialchars($empleado['nss'] ?? ''); ?>"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Nacimiento</label>
                <input type="date" name="fecha_nacimiento" value="<?php echo htmlspecialchars($empleado['fecha_nacimiento'] ?? ''); ?>"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Género</label>
                <select name="genero" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <option value="">Seleccione...</option>
                    <option value="M" <?php echo ($empleado['genero'] ?? '') === 'M' ? 'selected' : ''; ?>>Masculino</option>
                    <option value="F" <?php echo ($empleado['genero'] ?? '') === 'F' ? 'selected' : ''; ?>>Femenino</option>
                    <option value="Otro" <?php echo ($empleado['genero'] ?? '') === 'Otro' ? 'selected' : ''; ?>>Otro</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estado Civil</label>
                <select name="estado_civil" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <option value="">Seleccione...</option>
                    <option value="Soltero" <?php echo ($empleado['estado_civil'] ?? '') === 'Soltero' ? 'selected' : ''; ?>>Soltero</option>
                    <option value="Casado" <?php echo ($empleado['estado_civil'] ?? '') === 'Casado' ? 'selected' : ''; ?>>Casado</option>
                    <option value="Divorciado" <?php echo ($empleado['estado_civil'] ?? '') === 'Divorciado' ? 'selected' : ''; ?>>Divorciado</option>
                    <option value="Viudo" <?php echo ($empleado['estado_civil'] ?? '') === 'Viudo' ? 'selected' : ''; ?>>Viudo</option>
                    <option value="Unión Libre" <?php echo ($empleado['estado_civil'] ?? '') === 'Unión Libre' ? 'selected' : ''; ?>>Unión Libre</option>
                </select>
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
    
    <!-- Dirección -->
    <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
            <i class="fas fa-map-marker-alt text-red-600 mr-2"></i>
            Dirección
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Calle</label>
                <input type="text" name="calle" value="<?php echo htmlspecialchars($empleado['calle'] ?? ''); ?>"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Núm. Exterior</label>
                    <input type="text" name="numero_exterior" value="<?php echo htmlspecialchars($empleado['numero_exterior'] ?? ''); ?>"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Núm. Interior</label>
                    <input type="text" name="numero_interior" value="<?php echo htmlspecialchars($empleado['numero_interior'] ?? ''); ?>"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Colonia</label>
                <input type="text" name="colonia" value="<?php echo htmlspecialchars($empleado['colonia'] ?? ''); ?>"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Código Postal</label>
                <input type="text" name="codigo_postal" maxlength="5" value="<?php echo htmlspecialchars($empleado['codigo_postal'] ?? ''); ?>"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Municipio</label>
                <input type="text" name="municipio" value="<?php echo htmlspecialchars($empleado['municipio'] ?? 'Querétaro'); ?>"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
        </div>
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
            <input type="text" name="estado" value="<?php echo htmlspecialchars($empleado['estado'] ?? 'Querétaro'); ?>"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
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
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Ingreso</label>
                <input type="date" name="fecha_ingreso" value="<?php echo htmlspecialchars($empleado['fecha_ingreso'] ?? ''); ?>"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Contrato</label>
                <select name="tipo_contrato" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <option value="">Seleccione...</option>
                    <option value="Planta" <?php echo ($empleado['tipo_contrato'] ?? '') === 'Planta' ? 'selected' : ''; ?>>Planta</option>
                    <option value="Eventual" <?php echo ($empleado['tipo_contrato'] ?? '') === 'Eventual' ? 'selected' : ''; ?>>Eventual</option>
                    <option value="Honorarios" <?php echo ($empleado['tipo_contrato'] ?? '') === 'Honorarios' ? 'selected' : ''; ?>>Honorarios</option>
                    <option value="Practicante" <?php echo ($empleado['tipo_contrato'] ?? '') === 'Practicante' ? 'selected' : ''; ?>>Practicante</option>
                </select>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Departamento *</label>
                <select name="departamento" required 
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <option value="">Seleccione un departamento...</option>
                    <?php if (!empty($departamentos)): ?>
                        <?php foreach ($departamentos as $depto): ?>
                            <option value="<?php echo htmlspecialchars($depto['nombre']); ?>"
                                    <?php echo ($empleado['departamento'] == $depto['nombre']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($depto['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Puesto *</label>
                <select name="puesto" required 
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <option value="">Seleccione un puesto...</option>
                    <?php if (!empty($puestos)): ?>
                        <?php foreach ($puestos as $puesto): ?>
                            <option value="<?php echo htmlspecialchars($puesto['nombre']); ?>"
                                    <?php echo ($empleado['puesto'] == $puesto['nombre']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($puesto['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sucursal</label>
                <select name="sucursal_id" 
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <option value="">Seleccione una sucursal...</option>
                    <?php if (!empty($sucursales)): ?>
                        <?php foreach ($sucursales as $sucursal): ?>
                            <option value="<?php echo $sucursal['id']; ?>"
                                    <?php echo ($empleado['sucursal_id'] == $sucursal['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($sucursal['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Turno</label>
                <select name="turno_id" 
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <option value="">Seleccione un turno...</option>
                    <?php if (!empty($turnos)): ?>
                        <?php foreach ($turnos as $turno): ?>
                            <option value="<?php echo $turno['id']; ?>"
                                    <?php echo ($empleado['turno_id'] == $turno['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($turno['nombre']); ?> 
                                (<?php echo date('H:i', strtotime($turno['hora_entrada'])); ?> - <?php echo date('H:i', strtotime($turno['hora_salida'])); ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Salario Diario</label>
                <input type="number" name="salario_diario" step="0.01" value="<?php echo htmlspecialchars($empleado['salario_diario'] ?? ''); ?>"
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
    
    <!-- Datos Bancarios -->
    <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
            <i class="fas fa-university text-indigo-600 mr-2"></i>
            Datos Bancarios
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Banco</label>
                <input type="text" name="banco" value="<?php echo htmlspecialchars($empleado['banco'] ?? ''); ?>"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Número de Cuenta</label>
                <input type="text" name="numero_cuenta" value="<?php echo htmlspecialchars($empleado['numero_cuenta'] ?? ''); ?>"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">CLABE Interbancaria</label>
                <input type="text" name="clabe_interbancaria" maxlength="18" value="<?php echo htmlspecialchars($empleado['clabe_interbancaria'] ?? ''); ?>"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
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
