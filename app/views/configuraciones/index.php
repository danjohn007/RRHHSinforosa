<!-- Vista de Configuraciones Globales -->

<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Configuraciones Globales</h1>
            <p class="text-gray-600 mt-1">Administra las configuraciones del sistema</p>
        </div>
    </div>
</div>

<!-- Tabs -->
<div class="mb-6">
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8 overflow-x-auto">
            <button onclick="showConfigTab('sitio')" id="tab-sitio" class="config-tab active border-b-2 border-purple-500 py-4 px-1 text-center font-medium text-sm text-purple-600 whitespace-nowrap">
                <i class="fas fa-globe mr-2"></i>Sitio
            </button>
            <button onclick="showConfigTab('email')" id="tab-email" class="config-tab border-b-2 border-transparent py-4 px-1 text-center font-medium text-sm text-gray-500 hover:text-gray-700 whitespace-nowrap">
                <i class="fas fa-envelope mr-2"></i>Email
            </button>
            <button onclick="showConfigTab('contacto')" id="tab-contacto" class="config-tab border-b-2 border-transparent py-4 px-1 text-center font-medium text-sm text-gray-500 hover:text-gray-700 whitespace-nowrap">
                <i class="fas fa-phone mr-2"></i>Contacto
            </button>
            <button onclick="showConfigTab('estilo')" id="tab-estilo" class="config-tab border-b-2 border-transparent py-4 px-1 text-center font-medium text-sm text-gray-500 hover:text-gray-700 whitespace-nowrap">
                <i class="fas fa-palette mr-2"></i>Estilos
            </button>
            <button onclick="showConfigTab('paypal')" id="tab-paypal" class="config-tab border-b-2 border-transparent py-4 px-1 text-center font-medium text-sm text-gray-500 hover:text-gray-700 whitespace-nowrap">
                <i class="fab fa-paypal mr-2"></i>PayPal
            </button>
            <button onclick="showConfigTab('qr')" id="tab-qr" class="config-tab border-b-2 border-transparent py-4 px-1 text-center font-medium text-sm text-gray-500 hover:text-gray-700 whitespace-nowrap">
                <i class="fas fa-qrcode mr-2"></i>QR API
            </button>
        </nav>
    </div>
</div>

<form id="form-configuraciones" enctype="multipart/form-data">
    <!-- Sitio -->
    <div id="content-sitio" class="config-content">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-globe text-purple-600 mr-2"></i>Configuración del Sitio
            </h2>
            <p class="text-gray-600 mb-6">Personaliza el nombre y logotipo de tu sistema</p>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Sitio</label>
                    <input type="text" 
                           name="configuraciones[sitio_nombre]" 
                           id="sitio-nombre-input"
                           value="<?php echo htmlspecialchars($configs['sitio'][0]['valor'] ?? ''); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="Sistema RRHH Sinforosa Café">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Logotipo</label>
                    
                    <?php if (!empty($configs['sitio'][1]['valor'])): ?>
                        <div class="mb-3">
                            <p class="text-sm text-gray-600 mb-2">Logo actual:</p>
                            <?php 
                            $logoUrl = (strpos($configs['sitio'][1]['valor'], 'http') === 0 || strpos($configs['sitio'][1]['valor'], '//') === 0) 
                                ? $configs['sitio'][1]['valor'] 
                                : BASE_URL . ltrim($configs['sitio'][1]['valor'], '/');
                            ?>
                            <img id="logo-preview-current" 
                                 src="<?php echo htmlspecialchars($logoUrl); ?>" 
                                 alt="Logo actual" 
                                 class="h-16 object-contain border border-gray-200 rounded p-2">
                        </div>
                    <?php endif; ?>
                    
                    <!-- Preview for new logo -->
                    <div id="logo-preview-new-container" class="mb-3 hidden">
                        <p class="text-sm text-gray-600 mb-2">Vista previa del nuevo logo:</p>
                        <img id="logo-preview-new" 
                             src="" 
                             alt="Vista previa" 
                             class="h-16 object-contain border border-gray-200 rounded p-2">
                    </div>
                    
                    <input type="file" 
                           name="logo" 
                           id="logo-upload"
                           accept="image/jpeg,image/png,image/gif,image/webp"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <p class="text-sm text-gray-500 mt-1">
                        Formatos permitidos: JPG, PNG, GIF, WEBP (máx. 2MB)
                    </p>
                    
                    <!-- Campo oculto para mantener el valor actual si no se sube nuevo archivo -->
                    <input type="hidden" 
                           name="configuraciones[sitio_logo]" 
                           id="sitio-logo-hidden"
                           value="<?php echo htmlspecialchars($configs['sitio'][1]['valor'] ?? ''); ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- Email -->
    <div id="content-email" class="config-content hidden">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-envelope text-purple-600 mr-2"></i>Configuración de Email
            </h2>
            <p class="text-gray-600 mb-6">Configura el servidor de correo para enviar notificaciones del sistema</p>
            
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Correo Remitente</label>
                        <input type="email" name="configuraciones[email_remitente]" 
                            value="<?php echo htmlspecialchars($configs['email'][0]['valor'] ?? ''); ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="sistema@empresa.com">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Remitente</label>
                        <input type="text" name="configuraciones[email_remitente_nombre]" 
                            value="<?php echo htmlspecialchars($configs['email'][1]['valor'] ?? ''); ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="Sistema RRHH">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Servidor SMTP</label>
                        <input type="text" name="configuraciones[email_smtp_host]" 
                            value="<?php echo htmlspecialchars($configs['email'][2]['valor'] ?? ''); ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="smtp.gmail.com">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Puerto</label>
                        <input type="number" name="configuraciones[email_smtp_puerto]" 
                            value="<?php echo htmlspecialchars($configs['email'][3]['valor'] ?? ''); ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="587">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Seguridad</label>
                        <select name="configuraciones[email_smtp_seguridad]" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <option value="tls" <?php echo ($configs['email'][6]['valor'] ?? '') === 'tls' ? 'selected' : ''; ?>>TLS</option>
                            <option value="ssl" <?php echo ($configs['email'][6]['valor'] ?? '') === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Usuario SMTP</label>
                        <input type="text" name="configuraciones[email_smtp_usuario]" 
                            value="<?php echo htmlspecialchars($configs['email'][4]['valor'] ?? ''); ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="usuario@gmail.com">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contraseña SMTP</label>
                        <input type="password" name="configuraciones[email_smtp_password]" 
                            value="<?php echo htmlspecialchars($configs['email'][5]['valor'] ?? ''); ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="••••••••">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contacto -->
    <div id="content-contacto" class="config-content hidden">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-phone text-purple-600 mr-2"></i>Información de Contacto
            </h2>
            <p class="text-gray-600 mb-6">Define los teléfonos de contacto y horarios de atención</p>
            
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono Principal</label>
                        <input type="text" name="configuraciones[contacto_telefono1]" 
                            value="<?php echo htmlspecialchars($configs['contacto'][0]['valor'] ?? ''); ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="(442) 123 4567">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono Secundario</label>
                        <input type="text" name="configuraciones[contacto_telefono2]" 
                            value="<?php echo htmlspecialchars($configs['contacto'][1]['valor'] ?? ''); ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="(442) 123 4568">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">WhatsApp</label>
                        <input type="text" name="configuraciones[contacto_whatsapp]" 
                            value="<?php echo htmlspecialchars($configs['contacto'][2]['valor'] ?? ''); ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="52 442 123 4567">
                    </div>
                </div>
                
                <div class="border-t pt-4 mt-4">
                    <h3 class="text-md font-semibold text-gray-700 mb-4">Horarios de Atención</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Hora de Inicio</label>
                            <input type="time" name="configuraciones[contacto_horario_inicio]" 
                                value="<?php echo htmlspecialchars($configs['contacto'][3]['valor'] ?? ''); ?>"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Hora de Fin</label>
                            <input type="time" name="configuraciones[contacto_horario_fin]" 
                                value="<?php echo htmlspecialchars($configs['contacto'][4]['valor'] ?? ''); ?>"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Días de Atención</label>
                            <input type="text" name="configuraciones[contacto_dias_atencion]" 
                                value="<?php echo htmlspecialchars($configs['contacto'][5]['valor'] ?? ''); ?>"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                placeholder="Lunes a Viernes">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estilos -->
    <div id="content-estilo" class="config-content hidden">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-palette text-purple-600 mr-2"></i>Personalización de Estilos
            </h2>
            <p class="text-gray-600 mb-6">Cambia los colores principales del sistema</p>
            
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Color Primario</label>
                        <div class="flex items-center space-x-2">
                            <input type="color" name="configuraciones[estilo_color_primario]" 
                                value="<?php echo htmlspecialchars($configs['estilo'][0]['valor'] ?? '#667eea'); ?>"
                                class="h-10 w-20 border border-gray-300 rounded cursor-pointer">
                            <input type="text" 
                                value="<?php echo htmlspecialchars($configs['estilo'][0]['valor'] ?? '#667eea'); ?>"
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                readonly>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">Color principal del gradiente</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Color Secundario</label>
                        <div class="flex items-center space-x-2">
                            <input type="color" name="configuraciones[estilo_color_secundario]" 
                                value="<?php echo htmlspecialchars($configs['estilo'][1]['valor'] ?? '#764ba2'); ?>"
                                class="h-10 w-20 border border-gray-300 rounded cursor-pointer">
                            <input type="text" 
                                value="<?php echo htmlspecialchars($configs['estilo'][1]['valor'] ?? '#764ba2'); ?>"
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                readonly>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">Color secundario del gradiente</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Color de Acento</label>
                        <div class="flex items-center space-x-2">
                            <input type="color" name="configuraciones[estilo_color_acento]" 
                                value="<?php echo htmlspecialchars($configs['estilo'][2]['valor'] ?? '#f59e0b'); ?>"
                                class="h-10 w-20 border border-gray-300 rounded cursor-pointer">
                            <input type="text" 
                                value="<?php echo htmlspecialchars($configs['estilo'][2]['valor'] ?? '#f59e0b'); ?>"
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                readonly>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">Color para elementos destacados</p>
                    </div>
                </div>
                
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Vista Previa</h4>
                    <div class="h-20 rounded-lg" style="background: linear-gradient(135deg, <?php echo $configs['estilo'][0]['valor'] ?? '#667eea'; ?> 0%, <?php echo $configs['estilo'][1]['valor'] ?? '#764ba2'; ?> 100%);"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- PayPal -->
    <div id="content-paypal" class="config-content hidden">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fab fa-paypal text-purple-600 mr-2"></i>Configuración de PayPal
            </h2>
            <p class="text-gray-600 mb-6">Configura la cuenta de PayPal para procesamiento de pagos</p>
            
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Client ID</label>
                        <input type="text" name="configuraciones[paypal_client_id]" 
                            value="<?php echo htmlspecialchars($configs['paypal'][0]['valor'] ?? ''); ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="Client ID de PayPal">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Secret</label>
                        <input type="password" name="configuraciones[paypal_secret]" 
                            value="<?php echo htmlspecialchars($configs['paypal'][1]['valor'] ?? ''); ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="Secret de PayPal">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Modo</label>
                    <select name="configuraciones[paypal_modo]" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="sandbox" <?php echo ($configs['paypal'][2]['valor'] ?? '') === 'sandbox' ? 'selected' : ''; ?>>Sandbox (Pruebas)</option>
                        <option value="live" <?php echo ($configs['paypal'][2]['valor'] ?? '') === 'live' ? 'selected' : ''; ?>>Live (Producción)</option>
                    </select>
                    <p class="text-sm text-gray-500 mt-1">Usa "Sandbox" para pruebas y "Live" para producción</p>
                </div>
            </div>
        </div>
    </div>

    <!-- QR API -->
    <div id="content-qr" class="config-content hidden">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-qrcode text-purple-600 mr-2"></i>API de Códigos QR
            </h2>
            <p class="text-gray-600 mb-6">Configura la API para generación masiva de códigos QR</p>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">API Key</label>
                    <input type="text" name="configuraciones[qr_api_key]" 
                        value="<?php echo htmlspecialchars($configs['qr'][0]['valor'] ?? ''); ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="API Key para generación de QR">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">URL de la API</label>
                    <input type="text" name="configuraciones[qr_api_url]" 
                        value="<?php echo htmlspecialchars($configs['qr'][1]['valor'] ?? ''); ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="https://api.qrcode.com/v1">
                    <p class="text-sm text-gray-500 mt-1">Endpoint de la API para generación de códigos QR</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Botón Guardar (fijo para todos los tabs) -->
    <div class="mt-6 flex justify-end">
        <button type="submit" class="bg-gradient-sinforosa text-white px-6 py-3 rounded-lg hover:opacity-90 transition flex items-center">
            <i class="fas fa-save mr-2"></i>Guardar Configuraciones
        </button>
    </div>
</form>

<script>
function showConfigTab(tab) {
    // Ocultar todos los contenidos
    document.querySelectorAll('.config-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remover clase active de todos los tabs
    document.querySelectorAll('.config-tab').forEach(tabBtn => {
        tabBtn.classList.remove('active', 'border-purple-500', 'text-purple-600');
        tabBtn.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Mostrar el contenido seleccionado
    document.getElementById('content-' + tab).classList.remove('hidden');
    
    // Activar el tab seleccionado
    const activeTab = document.getElementById('tab-' + tab);
    activeTab.classList.add('active', 'border-purple-500', 'text-purple-600');
    activeTab.classList.remove('border-transparent', 'text-gray-500');
}

// Manejar envío del formulario
document.getElementById('form-configuraciones').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch(BASE_URL + 'configuraciones/guardar', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Configuraciones guardadas exitosamente');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al guardar las configuraciones');
    }
});

// Sincronizar color pickers con inputs de texto
document.querySelectorAll('input[type="color"]').forEach(colorInput => {
    colorInput.addEventListener('change', function() {
        const textInput = this.parentElement.querySelector('input[type="text"]');
        if (textInput) {
            textInput.value = this.value;
        }
    });
});

// Preview del logo antes de subir
document.getElementById('logo-upload').addEventListener('change', function(e) {
    const MAX_FILE_SIZE_BYTES = 2 * 1024 * 1024; // 2MB
    const VALID_FILE_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    
    const file = e.target.files[0];
    if (file) {
        // Validar tamaño
        if (file.size > MAX_FILE_SIZE_BYTES) {
            alert('El archivo es muy grande. El tamaño máximo es 2MB.');
            this.value = '';
            return;
        }
        
        // Validar tipo
        if (!VALID_FILE_TYPES.includes(file.type)) {
            alert('Tipo de archivo no permitido. Use JPG, PNG, GIF o WEBP.');
            this.value = '';
            return;
        }
        
        // Mostrar preview
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewImg = document.getElementById('logo-preview-new');
            const previewContainer = document.getElementById('logo-preview-new-container');
            previewImg.src = e.target.result;
            previewContainer.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
});
</script>
