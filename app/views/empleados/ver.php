<!-- Vista de Detalles del Empleado -->

<div class="mb-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="<?php echo BASE_URL; ?>empleados" class="text-gray-600 hover:text-gray-900 mr-4">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($empleado['nombre_completo']); ?></h1>
                <p class="text-gray-600 mt-1"><?php echo htmlspecialchars($empleado['numero_empleado']); ?> - <?php echo htmlspecialchars($empleado['puesto']); ?></p>
            </div>
        </div>
        <div class="flex space-x-2">
            <a href="<?php echo BASE_URL; ?>empleados/editar?id=<?php echo $empleado['id']; ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                <i class="fas fa-edit mr-2"></i>Editar
            </a>
            <a href="<?php echo BASE_URL; ?>empleados/carta-recomendacion?id=<?php echo $empleado['id']; ?>" target="_blank" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">
                <i class="fas fa-file-alt mr-2"></i>Carta
            </a>
            <a href="<?php echo BASE_URL; ?>empleados/constancia?id=<?php echo $empleado['id']; ?>" target="_blank" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                <i class="fas fa-certificate mr-2"></i>Constancia
            </a>
        </div>
    </div>
</div>

<!-- Información Principal -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    
    <!-- Tarjeta de Perfil -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="text-center">
            <div class="inline-block h-24 w-24 bg-gradient-sinforosa rounded-full flex items-center justify-center mb-4">
                <span class="text-white text-3xl font-bold">
                    <?php echo strtoupper(substr($empleado['nombres'], 0, 1) . substr($empleado['apellido_paterno'], 0, 1)); ?>
                </span>
            </div>
            <h3 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($empleado['nombre_completo']); ?></h3>
            <p class="text-gray-600"><?php echo htmlspecialchars($empleado['puesto']); ?></p>
            <div class="mt-4">
                <?php
                $statusColors = [
                    'Activo' => 'bg-green-100 text-green-800',
                    'Baja' => 'bg-red-100 text-red-800',
                    'Suspendido' => 'bg-yellow-100 text-yellow-800',
                    'Vacaciones' => 'bg-blue-100 text-blue-800'
                ];
                $color = $statusColors[$empleado['estatus']] ?? 'bg-gray-100 text-gray-800';
                ?>
                <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full <?php echo $color; ?>">
                    <?php echo htmlspecialchars($empleado['estatus']); ?>
                </span>
            </div>
        </div>
        
        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="space-y-3">
                <div class="flex items-center text-gray-600">
                    <i class="fas fa-envelope w-5"></i>
                    <span class="ml-3 text-sm"><?php echo htmlspecialchars($empleado['email_personal'] ?? 'No disponible'); ?></span>
                </div>
                <div class="flex items-center text-gray-600">
                    <i class="fas fa-phone w-5"></i>
                    <span class="ml-3 text-sm"><?php echo htmlspecialchars($empleado['celular'] ?? 'No disponible'); ?></span>
                </div>
                <div class="flex items-center text-gray-600">
                    <i class="fas fa-building w-5"></i>
                    <span class="ml-3 text-sm"><?php echo htmlspecialchars($empleado['departamento']); ?></span>
                </div>
                <div class="flex items-center text-gray-600">
                    <i class="fas fa-calendar w-5"></i>
                    <span class="ml-3 text-sm">Ingreso: <?php echo date('d/m/Y', strtotime($empleado['fecha_ingreso'])); ?></span>
                </div>
                <div class="flex items-center text-gray-600">
                    <i class="fas fa-clock w-5"></i>
                    <span class="ml-3 text-sm">Antigüedad: <?php echo $empleado['anios_antiguedad']; ?> años</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Información Detallada -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Datos Personales -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
                <i class="fas fa-user text-purple-600 mr-2"></i>
                Datos Personales
            </h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">CURP</p>
                    <p class="font-medium text-gray-800"><?php echo htmlspecialchars($empleado['curp'] ?? 'No disponible'); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">RFC</p>
                    <p class="font-medium text-gray-800"><?php echo htmlspecialchars($empleado['rfc'] ?? 'No disponible'); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">NSS</p>
                    <p class="font-medium text-gray-800"><?php echo htmlspecialchars($empleado['nss'] ?? 'No disponible'); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Fecha de Nacimiento</p>
                    <p class="font-medium text-gray-800">
                        <?php echo $empleado['fecha_nacimiento'] ? date('d/m/Y', strtotime($empleado['fecha_nacimiento'])) : 'No disponible'; ?>
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Género</p>
                    <p class="font-medium text-gray-800"><?php echo htmlspecialchars($empleado['genero'] ?? 'No especificado'); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Estado Civil</p>
                    <p class="font-medium text-gray-800"><?php echo htmlspecialchars($empleado['estado_civil'] ?? 'No especificado'); ?></p>
                </div>
            </div>
        </div>
        
        <!-- Información Laboral -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
                <i class="fas fa-briefcase text-blue-600 mr-2"></i>
                Información Laboral
            </h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Tipo de Contrato</p>
                    <p class="font-medium text-gray-800"><?php echo htmlspecialchars($empleado['tipo_contrato']); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Salario Mensual</p>
                    <p class="font-medium text-green-600">$<?php echo number_format($empleado['salario_mensual'], 2); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Salario Diario</p>
                    <p class="font-medium text-gray-800">$<?php echo number_format($empleado['salario_diario'], 2); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Banco</p>
                    <p class="font-medium text-gray-800"><?php echo htmlspecialchars($empleado['banco'] ?? 'No disponible'); ?></p>
                </div>
            </div>
        </div>
        
        <!-- Dirección -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
                <i class="fas fa-map-marker-alt text-red-600 mr-2"></i>
                Dirección
            </h3>
            <p class="text-gray-800">
                <?php 
                echo htmlspecialchars($empleado['calle'] ?? '') . ' ';
                echo htmlspecialchars($empleado['numero_exterior'] ?? '') . ' ';
                echo ($empleado['numero_interior'] ? 'Int. ' . htmlspecialchars($empleado['numero_interior']) : '') . ', ';
                echo htmlspecialchars($empleado['colonia'] ?? '') . ', ';
                echo 'CP ' . htmlspecialchars($empleado['codigo_postal'] ?? '') . ', ';
                echo htmlspecialchars($empleado['municipio'] ?? '') . ', ';
                echo htmlspecialchars($empleado['estado'] ?? '');
                ?>
            </p>
        </div>
        
    </div>
</div>

<!-- Historial Laboral -->
<div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-history text-purple-600 mr-2"></i>
            Historial Laboral
        </h3>
    </div>
    <div class="p-6">
        <?php if (empty($historial)): ?>
            <p class="text-gray-500 text-center py-4">No hay registros en el historial</p>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($historial as $evento): ?>
                <div class="flex items-start border-l-4 border-purple-500 pl-4 py-2">
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($evento['tipo_evento']); ?></p>
                            <span class="text-sm text-gray-500"><?php echo date('d/m/Y', strtotime($evento['fecha_evento'])); ?></span>
                        </div>
                        <?php if ($evento['motivo']): ?>
                            <p class="text-sm text-gray-600 mt-1"><?php echo htmlspecialchars($evento['motivo']); ?></p>
                        <?php endif; ?>
                        <?php if ($evento['puesto_nuevo']): ?>
                            <p class="text-sm text-gray-500 mt-1">
                                Puesto: <?php echo htmlspecialchars($evento['puesto_nuevo']); ?>
                                <?php if ($evento['salario_nuevo']): ?>
                                    | Salario: $<?php echo number_format($evento['salario_nuevo'], 2); ?>
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Documentos -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-folder text-blue-600 mr-2"></i>
            Documentos
        </h3>
        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm">
            <i class="fas fa-upload mr-2"></i>Subir Documento
        </button>
    </div>
    <div class="p-6">
        <?php if (empty($documentos)): ?>
            <p class="text-gray-500 text-center py-4">No hay documentos registrados</p>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($documentos as $doc): ?>
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-2">
                        <i class="fas fa-file-alt text-3xl text-blue-500"></i>
                        <button class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                    <p class="font-medium text-gray-800 text-sm"><?php echo htmlspecialchars($doc['tipo_documento']); ?></p>
                    <p class="text-xs text-gray-500 mt-1"><?php echo date('d/m/Y', strtotime($doc['fecha_subida'])); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
