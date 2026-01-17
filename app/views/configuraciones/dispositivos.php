<!-- Vista de Dispositivos IoT -->

<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Dispositivos</h1>
            <p class="text-gray-600 mt-1">Administre los dispositivos IoT del sistema</p>
        </div>
    </div>
</div>

<!-- Tabs -->
<div class="mb-6">
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
            <button onclick="showDeviceTab('shelly')" id="tab-shelly" class="device-tab active border-b-2 border-purple-500 py-4 px-1 text-center font-medium text-sm text-purple-600 whitespace-nowrap">
                <i class="fas fa-cloud mr-2"></i>Dispositivos Shelly Cloud
            </button>
            <button onclick="showDeviceTab('hikvision')" id="tab-hikvision" class="device-tab border-b-2 border-transparent py-4 px-1 text-center font-medium text-sm text-gray-500 hover:text-gray-700 whitespace-nowrap">
                <i class="fas fa-camera mr-2"></i>Dispositivos HikVision
            </button>
        </nav>
    </div>
</div>

<!-- Dispositivos Shelly Cloud -->
<div id="content-shelly" class="device-content">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-cloud text-orange-500 mr-2"></i>Dispositivos Shelly Cloud
                </h2>
                <p class="text-gray-600 text-sm mt-1">Configure múltiples dispositivos Shelly para control de acceso. Cada dispositivo puede tener canales independientes y acciones configurables.</p>
            </div>
            <button onclick="openShellyModal()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-plus mr-2"></i>Nuevo dispositivo +
            </button>
        </div>

        <div id="shelly-list" class="space-y-4">
            <?php foreach ($dispositivos_shelly as $dispositivo): ?>
            <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-4">
                            <div class="grid grid-cols-2 gap-8 flex-1">
                                <div>
                                    <label class="text-xs text-gray-600 font-medium">Token de Autenticación</label>
                                    <div class="flex items-center space-x-2 mt-1">
                                        <input type="password" value="<?php echo htmlspecialchars($dispositivo['token_autenticacion']); ?>" readonly class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm font-mono">
                                        <button class="text-gray-600 hover:text-gray-900">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600 font-medium">Device ID</label>
                                    <div class="mt-1">
                                        <input type="text" value="<?php echo htmlspecialchars($dispositivo['device_id']); ?>" readonly class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm font-mono">
                                    </div>
                                </div>
                            </div>
                            <button onclick="deleteShellyDevice(<?php echo $dispositivo['id']; ?>)" class="ml-4 text-red-600 hover:text-red-800 bg-red-50 rounded-full p-2 w-8 h-8 flex items-center justify-center">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div class="grid grid-cols-2 gap-8 mb-4">
                            <div>
                                <label class="text-xs text-gray-600 font-medium">Servidor Cloud</label>
                                <div class="mt-1">
                                    <input type="text" value="<?php echo htmlspecialchars($dispositivo['servidor_cloud']); ?>" readonly class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm">
                                    <p class="text-xs text-gray-500 mt-1">Sin https:// ni puerto</p>
                                </div>
                            </div>
                            <div>
                                <label class="text-xs text-gray-600 font-medium">Acción</label>
                                <div class="mt-1 relative">
                                    <select disabled class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm appearance-none">
                                        <option><?php echo htmlspecialchars($dispositivo['accion']); ?></option>
                                    </select>
                                    <i class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="text-xs text-gray-600 font-medium">Área</label>
                            <div class="mt-1">
                                <input type="text" value="<?php echo htmlspecialchars($dispositivo['area']); ?>" readonly class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-6 mb-4">
                            <div>
                                <label class="text-xs text-gray-600 font-medium">Canal de Entrada (Apertura)</label>
                                <div class="mt-1">
                                    <select disabled class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm">
                                        <option>Canal <?php echo $dispositivo['canal_entrada']; ?></option>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">Pulso de 5 segundos al entrar</p>
                                </div>
                            </div>
                            <div>
                                <label class="text-xs text-gray-600 font-medium">Canal de Salida (Cierre)</label>
                                <div class="mt-1">
                                    <select disabled class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm">
                                        <option>Canal <?php echo $dispositivo['canal_salida']; ?></option>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">Activación al salir</p>
                                </div>
                            </div>
                            <div>
                                <label class="text-xs text-gray-600 font-medium">Duración Pulso (ms)</label>
                                <div class="mt-1">
                                    <input type="text" value="<?php echo htmlspecialchars($dispositivo['duracion_pulso']); ?>" readonly class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm">
                                    <p class="text-xs text-gray-500 mt-1">Por defecto: 5000 ms. Máximo: 10 seg</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center space-x-6">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" <?php echo $dispositivo['habilitado'] ? 'checked' : ''; ?> disabled class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                <span class="text-sm text-gray-700">Dispositivo habilitado</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" <?php echo $dispositivo['invertido'] ? 'checked' : ''; ?> disabled class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                <span class="text-sm text-gray-700">Invertido (off → on)</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" <?php echo $dispositivo['simultaneo'] ? 'checked' : ''; ?> disabled class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                <span class="text-sm text-gray-700">Dispositivo simultáneo</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if (empty($dispositivos_shelly)): ?>
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-cloud text-4xl mb-4"></i>
                <p>No hay dispositivos Shelly configurados</p>
            </div>
            <?php endif; ?>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <button onclick="location.href='<?php echo BASE_URL; ?>configuraciones'" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                <i class="fas fa-times mr-2"></i>Cancelar
            </button>
            <button onclick="alert('Los dispositivos se guardan automáticamente al agregar/editar'); location.href='<?php echo BASE_URL; ?>configuraciones'" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-check mr-2"></i>Listo
            </button>
        </div>
    </div>
</div>

<!-- Dispositivos HikVision -->
<div id="content-hikvision" class="device-content hidden">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-camera text-blue-600 mr-2"></i>Dispositivos HikVision
                </h2>
                <p class="text-gray-600 text-sm mt-1">Configure dispositivos HikVision para lectura de placas (LPR) y lectores de código de barras. Los dispositivos se utilizarán para registro automático y control de acceso.</p>
            </div>
            <button onclick="openHikVisionModal()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-plus mr-2"></i>Nuevo dispositivo +
            </button>
        </div>

        <div id="hikvision-list" class="space-y-4">
            <?php foreach ($dispositivos_hikvision as $dispositivo): ?>
            <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <div class="grid grid-cols-2 gap-6 mb-4">
                            <div>
                                <label class="text-xs text-gray-600 font-medium">Nombre del Dispositivo <span class="text-red-500">*</span></label>
                                <div class="mt-1">
                                    <input type="text" value="<?php echo htmlspecialchars($dispositivo['nombre']); ?>" readonly class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="text-xs text-gray-600 font-medium">Tipo de Dispositivo <span class="text-red-500">*</span></label>
                                <div class="mt-1">
                                    <select disabled class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm">
                                        <option value="LPR" <?php echo ($dispositivo['tipo_dispositivo'] ?? 'LPR') === 'LPR' ? 'selected' : ''; ?>>Cámara LPR (Lectura de Placas)</option>
                                        <option value="Barcode" <?php echo ($dispositivo['tipo_dispositivo'] ?? '') === 'Barcode' ? 'selected' : ''; ?>>Lector de Código de Barras</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6 mb-4">
                            <div>
                                <label class="text-xs text-gray-600 font-medium">Api Key</label>
                                <div class="mt-1">
                                    <input type="text" value="<?php echo htmlspecialchars($dispositivo['api_key'] ?? ''); ?>" readonly class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm font-mono">
                                </div>
                            </div>
                            <div>
                                <label class="text-xs text-gray-600 font-medium">Api Secret</label>
                                <div class="flex items-center space-x-2 mt-1">
                                    <input type="password" value="<?php echo htmlspecialchars($dispositivo['api_secret'] ?? ''); ?>" readonly class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm font-mono">
                                    <button class="text-gray-600 hover:text-gray-900">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="text-xs text-gray-600 font-medium">Endpoint (Token)</label>
                            <div class="mt-1">
                                <input type="text" value="<?php echo htmlspecialchars($dispositivo['endpoint_token'] ?? ''); ?>" readonly class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm">
                                <p class="text-xs text-gray-500 mt-1">URL para obtener token de autenticación</p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="text-xs text-gray-600 font-medium">Area Domain</label>
                            <div class="mt-1">
                                <input type="text" value="<?php echo htmlspecialchars($dispositivo['area_domain'] ?? ''); ?>" readonly class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm">
                                <p class="text-xs text-gray-500 mt-1">Dominio del área para consultas API</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6 mb-4">
                            <div>
                                <label class="text-xs text-gray-600 font-medium">Device Index Code / Serial</label>
                                <div class="mt-1">
                                    <input type="text" value="<?php echo htmlspecialchars($dispositivo['device_index_code'] ?? ''); ?>" readonly class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="text-xs text-gray-600 font-medium">Área / Ubicación</label>
                                <div class="mt-1">
                                    <input type="text" value="<?php echo htmlspecialchars($dispositivo['area_ubicacion'] ?? ''); ?>" readonly class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm">
                                </div>
                            </div>
                        </div>

                        <!-- Configuración ISAPI Local -->
                        <div class="border-t pt-4 mt-4">
                            <div class="flex items-center mb-3">
                                <i class="fas fa-network-wired text-purple-600 mr-2"></i>
                                <h3 class="text-sm font-semibold text-gray-700">Configuración ISAPI Local (Opcional)</h3>
                            </div>

                            <div class="mb-3">
                                <label class="text-xs text-gray-600 font-medium">URL de API (ISAPI)</label>
                                <div class="mt-1">
                                    <input type="text" value="<?php echo htmlspecialchars($dispositivo['isapi_url'] ?? ''); ?>" readonly class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm">
                                    <p class="text-xs text-gray-500 mt-1">Solo para modo ISAPI local (no usar con Cloud)</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-6 mb-4">
                                <div>
                                    <label class="text-xs text-gray-600 font-medium">Usuario (ISAPI)</label>
                                    <div class="mt-1">
                                        <input type="text" value="<?php echo htmlspecialchars($dispositivo['isapi_usuario'] ?? ''); ?>" readonly class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm">
                                    </div>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600 font-medium">Contraseña (ISAPI)</label>
                                    <div class="flex items-center space-x-2 mt-1">
                                        <input type="password" value="<?php echo htmlspecialchars($dispositivo['isapi_password'] ?? ''); ?>" readonly class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm">
                                        <button class="text-gray-600 hover:text-gray-900">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center space-x-6">
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" <?php echo ($dispositivo['isapi_habilitado'] ?? 0) ? 'checked' : ''; ?> disabled class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                    <span class="text-sm text-gray-700">Dispositivo habilitado</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" <?php echo ($dispositivo['verificar_ssl'] ?? 0) ? 'checked' : ''; ?> disabled class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                    <span class="text-sm text-gray-700">Verificar certificado SSL</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <button onclick="deleteHikVisionDevice(<?php echo $dispositivo['id']; ?>)" class="ml-4 text-red-600 hover:text-red-800 bg-red-50 rounded-full p-2 w-8 h-8 flex items-center justify-center">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if (empty($dispositivos_hikvision)): ?>
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-camera text-4xl mb-4"></i>
                <p>No hay dispositivos HikVision configurados</p>
            </div>
            <?php endif; ?>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <button onclick="location.href='<?php echo BASE_URL; ?>configuraciones'" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                <i class="fas fa-times mr-2"></i>Cancelar
            </button>
            <button onclick="alert('Los dispositivos se guardan automáticamente al agregar/editar'); location.href='<?php echo BASE_URL; ?>configuraciones'" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-check mr-2"></i>Listo
            </button>
        </div>
    </div>
</div>

<!-- Modal Shelly -->
<div id="modal-shelly" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800">Configurar Dispositivo Shelly</h3>
        </div>
        <form id="form-shelly" class="p-6">
            <input type="hidden" name="id" id="shelly-id">
            <input type="hidden" name="tipo" value="shelly">
            
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Token de Autenticación *</label>
                        <input type="text" name="token_autenticacion" id="shelly-token" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Device ID *</label>
                        <input type="text" name="device_id" id="shelly-device-id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Servidor Cloud *</label>
                        <input type="text" name="servidor_cloud" id="shelly-servidor" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Acción</label>
                        <select name="accion" id="shelly-accion" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            <option value="Abrir/Cerrar">Abrir/Cerrar</option>
                            <option value="Activar">Activar</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Área *</label>
                    <input type="text" name="area" id="shelly-area" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Dispositivo</label>
                    <input type="text" name="nombre" id="shelly-nombre" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
                
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Canal Entrada</label>
                        <input type="number" name="canal_entrada" id="shelly-canal-entrada" value="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Canal Salida</label>
                        <input type="number" name="canal_salida" id="shelly-canal-salida" value="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Duración Pulso (ms)</label>
                        <input type="number" name="duracion_pulso" id="shelly-duracion" value="600" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
                
                <div class="flex items-center space-x-6">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="habilitado" id="shelly-habilitado" checked class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span class="text-sm text-gray-700">Dispositivo habilitado</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="invertido" id="shelly-invertido" class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span class="text-sm text-gray-700">Invertido</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="simultaneo" id="shelly-simultaneo" class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span class="text-sm text-gray-700">Simultáneo</span>
                    </label>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="closeShellyModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal HikVision -->
<div id="modal-hikvision" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800">Configurar Dispositivo HikVision</h3>
        </div>
        <form id="form-hikvision" class="p-6">
            <input type="hidden" name="id" id="hikvision-id">
            <input type="hidden" name="tipo" value="hikvision">
            
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Dispositivo *</label>
                        <input type="text" name="nombre" id="hikvision-nombre" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Dispositivo *</label>
                        <select name="tipo_dispositivo" id="hikvision-tipo" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            <option value="LPR">Cámara LPR (Lectura de Placas)</option>
                            <option value="Barcode">Lector de Código de Barras</option>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">API Key *</label>
                        <input type="text" name="api_key" id="hikvision-api-key" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">API Secret *</label>
                        <input type="password" name="api_secret" id="hikvision-api-secret" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Endpoint (Token) *</label>
                    <input type="text" name="endpoint_token" id="hikvision-endpoint" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Area Domain *</label>
                    <input type="text" name="area_domain" id="hikvision-area-domain" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Device Index Code / Serial *</label>
                        <input type="text" name="device_index_code" id="hikvision-device-code" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Área / Ubicación</label>
                        <input type="text" name="area_ubicacion" id="hikvision-ubicacion" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
                
                <div class="border-t pt-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Configuración ISAPI Local (Opcional)</h4>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">URL de API (ISAPI)</label>
                            <input type="text" name="isapi_url" id="hikvision-isapi-url" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Usuario (ISAPI)</label>
                                <input type="text" name="isapi_usuario" id="hikvision-isapi-user" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Contraseña (ISAPI)</label>
                                <input type="password" name="isapi_password" id="hikvision-isapi-pass" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-6">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="isapi_habilitado" id="hikvision-isapi-enabled" class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                <span class="text-sm text-gray-700">ISAPI Habilitado</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="verificar_ssl" id="hikvision-verify-ssl" checked class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                <span class="text-sm text-gray-700">Verificar SSL</span>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="habilitado" id="hikvision-habilitado" checked class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span class="text-sm text-gray-700">Dispositivo habilitado</span>
                    </label>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="closeHikVisionModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showDeviceTab(tab) {
    document.querySelectorAll('.device-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    document.querySelectorAll('.device-tab').forEach(tabBtn => {
        tabBtn.classList.remove('active', 'border-purple-500', 'text-purple-600');
        tabBtn.classList.add('border-transparent', 'text-gray-500');
    });
    
    document.getElementById('content-' + tab).classList.remove('hidden');
    
    const activeTab = document.getElementById('tab-' + tab);
    activeTab.classList.add('active', 'border-purple-500', 'text-purple-600');
    activeTab.classList.remove('border-transparent', 'text-gray-500');
}

// Shelly Modal
function openShellyModal() {
    document.getElementById('modal-shelly').classList.remove('hidden');
    document.getElementById('form-shelly').reset();
    document.getElementById('shelly-id').value = '';
}

function closeShellyModal() {
    document.getElementById('modal-shelly').classList.add('hidden');
}

document.getElementById('form-shelly').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch(BASE_URL + 'configuraciones/guardar-dispositivo', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Dispositivo guardado exitosamente');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al guardar el dispositivo');
    }
});

function deleteShellyDevice(id) {
    if (!confirm('¿Está seguro de eliminar este dispositivo?')) return;
    
    const formData = new FormData();
    formData.append('id', id);
    formData.append('tipo', 'shelly');
    
    fetch(BASE_URL + 'configuraciones/eliminar-dispositivo', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Dispositivo eliminado');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al eliminar el dispositivo');
    });
}

// HikVision Modal
function openHikVisionModal() {
    document.getElementById('modal-hikvision').classList.remove('hidden');
    document.getElementById('form-hikvision').reset();
    document.getElementById('hikvision-id').value = '';
}

function closeHikVisionModal() {
    document.getElementById('modal-hikvision').classList.add('hidden');
}

document.getElementById('form-hikvision').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch(BASE_URL + 'configuraciones/guardar-dispositivo', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Dispositivo guardado exitosamente');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al guardar el dispositivo');
    }
});

function deleteHikVisionDevice(id) {
    if (!confirm('¿Está seguro de eliminar este dispositivo?')) return;
    
    const formData = new FormData();
    formData.append('id', id);
    formData.append('tipo', 'hikvision');
    
    fetch(BASE_URL + 'configuraciones/eliminar-dispositivo', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Dispositivo eliminado');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al eliminar el dispositivo');
    });
}
</script>
