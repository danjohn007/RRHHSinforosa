<!-- Formulario Crear Empleado -->

<div class="mb-6">
    <div class="flex items-center">
        <a href="<?php echo BASE_URL; ?>empleados" class="text-gray-600 hover:text-gray-900 mr-4">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Nuevo Empleado</h1>
            <p class="text-gray-600 mt-1">Registra un nuevo colaborador en el sistema</p>
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
    <p class="text-green-600 text-sm mt-2">Redirigiendo...</p>
</div>
<?php endif; ?>

<form method="POST" action="<?php echo BASE_URL; ?>empleados/crear" class="bg-white rounded-lg shadow-md p-6">
    <!-- Información Personal -->
    <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
            <i class="fas fa-user text-purple-600 mr-2"></i>
            Información Personal
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nombres *</label>
                <input type="text" name="nombres" required 
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Apellido Paterno *</label>
                <input type="text" name="apellido_paterno" required 
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Apellido Materno</label>
                <input type="text" name="apellido_materno" 
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">CURP</label>
                <input type="text" name="curp" maxlength="18" 
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">RFC</label>
                <input type="text" name="rfc" maxlength="13" 
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">NSS</label>
                <input type="text" name="nss" maxlength="11" 
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Nacimiento</label>
                <input type="date" name="fecha_nacimiento" 
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Género</label>
                <select name="genero" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <option value="">Seleccione</option>
                    <option value="M">Masculino</option>
                    <option value="F">Femenino</option>
                    <option value="Otro">Otro</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estado Civil</label>
                <select name="estado_civil" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <option value="">Seleccione</option>
                    <option value="Soltero">Soltero</option>
                    <option value="Casado">Casado</option>
                    <option value="Divorciado">Divorciado</option>
                    <option value="Viudo">Viudo</option>
                    <option value="Unión Libre">Unión Libre</option>
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
                <input type="email" name="email_personal" 
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                <input type="text" name="telefono" 
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Celular</label>
                <input type="text" name="celular" 
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
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Calle</label>
                <input type="text" name="calle" 
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Número Exterior</label>
                <input type="text" name="numero_exterior" 
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Número Interior</label>
                <input type="text" name="numero_interior" 
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Colonia</label>
                <input type="text" name="colonia" 
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Código Postal</label>
                <input type="text" name="codigo_postal" maxlength="5" 
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Municipio</label>
                <input type="text" name="municipio" value="Querétaro" 
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
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Ingreso *</label>
                <input type="date" name="fecha_ingreso" required 
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Contrato *</label>
                <select name="tipo_contrato" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <option value="">Seleccione</option>
                    <option value="Planta">Planta</option>
                    <option value="Eventual">Eventual</option>
                    <option value="Honorarios">Honorarios</option>
                    <option value="Practicante">Practicante</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Departamento *</label>
                <input type="text" name="departamento" required 
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
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Puesto *</label>
                <input type="text" name="puesto" required 
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Salario Diario</label>
                <input type="number" name="salario_diario" step="0.01" 
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Salario Mensual *</label>
                <input type="number" name="salario_mensual" step="0.01" required 
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
        </div>
    </div>
    
    <!-- Botones -->
    <div class="flex justify-end space-x-4">
        <a href="<?php echo BASE_URL; ?>empleados" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
            Cancelar
        </a>
        <button type="submit" class="bg-gradient-sinforosa text-white px-6 py-3 rounded-lg hover:opacity-90 transition">
            <i class="fas fa-save mr-2"></i>
            Guardar Empleado
        </button>
    </div>
</form>
