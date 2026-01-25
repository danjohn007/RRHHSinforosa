<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Asistencia - <?php echo htmlspecialchars($sucursal['nombre']); ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, <?php echo $configs['estilo_color_primario'] ?? '#3B82F6'; ?> 0%, <?php echo $configs['estilo_color_secundario'] ?? '#1E40AF'; ?> 100%);
            min-height: 100vh;
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }
        
        .btn-primary {
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }
        
        .pulse-animation {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: .5;
            }
        }
        
        .camera-preview {
            width: 100%;
            max-width: 640px;
            height: auto;
            border-radius: 12px;
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body class="flex items-center justify-center p-4">
    <div class="w-full max-w-4xl">
        <!-- Main Card -->
        <div class="glass-effect rounded-3xl p-8 md:p-12">
            <!-- Logo -->
            <?php if (!empty($configs['sitio_logo'])): ?>
            <div class="text-center mb-8">
                <?php 
                // Build correct logo URL
                $logoUrl = $configs['sitio_logo'];
                if (strpos($logoUrl, 'http') !== 0 && strpos($logoUrl, '//') !== 0) {
                    // It's a relative path, prepend BASE_URL
                    $logoUrl = BASE_URL . ltrim($logoUrl, '/');
                }
                ?>
                <img src="<?php echo htmlspecialchars($logoUrl); ?>" 
                     alt="Logo" 
                     class="mx-auto h-20 md:h-24 object-contain">
            </div>
            <?php endif; ?>
            
            <!-- Branch Name -->
            <h1 class="text-3xl md:text-4xl font-bold text-center text-gray-800 mb-2">
                <?php echo htmlspecialchars($sucursal['nombre']); ?>
            </h1>
            <p class="text-center text-gray-600 mb-8 text-lg">
                <i class="fas fa-clock mr-2"></i>Registro de Asistencia
            </p>
            
            <!-- Form -->
            <div id="formSection" class="space-y-6">
                <!-- Employee Code Input -->
                <div>
                    <label for="codigoEmpleado" class="block text-lg font-semibold text-gray-700 mb-3">
                        <i class="fas fa-id-card mr-2"></i>Código de Empleado
                    </label>
                    <input type="text" 
                           id="codigoEmpleado" 
                           maxlength="6" 
                           pattern="[0-9]*" 
                           inputmode="numeric"
                           class="w-full px-6 py-4 text-2xl text-center font-bold border-2 border-gray-300 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-200 transition-all"
                           placeholder="000000"
                           autofocus>
                </div>
                
                <!-- Manager Code (Hidden by default) -->
                <div id="managerCodeSection" class="hidden">
                    <label for="codigoGerente" class="block text-lg font-semibold text-gray-700 mb-3">
                        <i class="fas fa-user-tie mr-2"></i>Código de Gerente/Supervisor
                    </label>
                    <input type="text" 
                           id="codigoGerente" 
                           maxlength="6" 
                           pattern="[0-9]*" 
                           inputmode="numeric"
                           class="w-full px-6 py-4 text-2xl text-center font-bold border-2 border-gray-300 rounded-xl focus:outline-none focus:border-yellow-500 focus:ring-4 focus:ring-yellow-200 transition-all"
                           placeholder="000000">
                    <p class="mt-2 text-sm text-yellow-700">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Este empleado pertenece a otra sucursal. Se requiere autorización.
                    </p>
                </div>
                
                <!-- Action Buttons -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-8">
                    <button type="button" 
                            id="btnEntrada" 
                            class="btn-primary bg-green-600 hover:bg-green-700 text-white font-bold py-6 px-8 rounded-xl text-xl shadow-lg">
                        <i class="fas fa-sign-in-alt mr-3"></i>Registrar Entrada
                    </button>
                    <button type="button" 
                            id="btnSalida" 
                            class="btn-primary bg-red-600 hover:bg-red-700 text-white font-bold py-6 px-8 rounded-xl text-xl shadow-lg">
                        <i class="fas fa-sign-out-alt mr-3"></i>Registrar Salida
                    </button>
                </div>
            </div>
            
            <!-- Success Message -->
            <div id="successMessage" class="hidden fade-in">
                <div class="bg-green-50 border-2 border-green-500 rounded-xl p-6">
                    <div class="flex items-center mb-4">
                        <div class="bg-green-500 rounded-full p-3 mr-4">
                            <i class="fas fa-check text-white text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-green-800" id="successTitle">¡Registro Exitoso!</h3>
                            <p class="text-green-600" id="successSubtitle"></p>
                        </div>
                    </div>
                    <div class="space-y-3 text-lg">
                        <div class="flex justify-between border-b border-green-200 pb-2">
                            <span class="font-semibold text-gray-700">Empleado:</span>
                            <span class="text-gray-900" id="displayNombre"></span>
                        </div>
                        <div class="flex justify-between border-b border-green-200 pb-2">
                            <span class="font-semibold text-gray-700">Hora:</span>
                            <span class="text-gray-900" id="displayHora"></span>
                        </div>
                        <div class="flex justify-between border-b border-green-200 pb-2">
                            <span class="font-semibold text-gray-700">Tipo:</span>
                            <span class="text-gray-900" id="displayTipo"></span>
                        </div>
                        <div id="horasTrabajadasSection" class="hidden">
                            <div class="flex justify-between border-b border-green-200 pb-2">
                                <span class="font-semibold text-gray-700">Horas Trabajadas:</span>
                                <span class="text-gray-900 font-bold" id="displayHorasTrabajadas"></span>
                            </div>
                        </div>
                        <div id="horasExtrasSection" class="hidden">
                            <div class="flex justify-between bg-blue-50 p-3 rounded-lg">
                                <span class="font-semibold text-blue-700">
                                    <i class="fas fa-clock mr-2"></i>Horas Extras Acumuladas (Periodo):
                                </span>
                                <span class="text-blue-900 font-bold text-xl" id="displayHorasExtras"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Error Message -->
            <div id="errorMessage" class="hidden fade-in">
                <div class="bg-red-50 border-2 border-red-500 rounded-xl p-6">
                    <div class="flex items-center">
                        <div class="bg-red-500 rounded-full p-3 mr-4">
                            <i class="fas fa-times text-white text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-red-800">Error</h3>
                            <p class="text-red-600 text-lg" id="errorText"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="text-center mt-6 text-white text-sm">
            <p class="opacity-80">
                <i class="far fa-clock mr-2"></i>
                <span id="currentTime"></span>
            </p>
        </div>
    </div>
    
    <!-- Camera Modal -->
    <div id="cameraModal" class="hidden fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-2xl p-6 max-w-2xl w-full">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-camera mr-2"></i>Capturar Foto
                </h3>
                <button id="closeCamera" class="text-gray-500 hover:text-gray-700 text-2xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="space-y-4">
                <div id="cameraPreview" class="bg-gray-900 rounded-xl overflow-hidden flex items-center justify-center min-h-[300px]">
                    <video id="video" class="camera-preview hidden" autoplay playsinline></video>
                    <canvas id="canvas" class="camera-preview hidden"></canvas>
                    <div id="cameraLoading" class="text-white text-center pulse-animation">
                        <i class="fas fa-spinner fa-spin text-4xl mb-3"></i>
                        <p class="text-lg">Iniciando cámara...</p>
                    </div>
                </div>
                
                <div class="flex gap-3">
                    <button id="captureBtn" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-xl text-lg hidden">
                        <i class="fas fa-camera mr-2"></i>Capturar
                    </button>
                    <button id="retakeBtn" class="flex-1 bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-4 px-6 rounded-xl text-lg hidden">
                        <i class="fas fa-redo mr-2"></i>Repetir
                    </button>
                    <button id="confirmBtn" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-6 rounded-xl text-lg hidden">
                        <i class="fas fa-check mr-2"></i>Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 text-center">
            <i class="fas fa-spinner fa-spin text-5xl text-blue-600 mb-4"></i>
            <p class="text-xl font-semibold text-gray-800">Procesando...</p>
        </div>
    </div>

    <script>
        const BASE_URL = '<?php echo BASE_URL; ?>';
        const URL_PUBLICA = '<?php echo htmlspecialchars($url_publica ?? ''); ?>';
        
        let stream = null;
        let photoData = null;
        let currentTipo = null;
        
        // Elements
        const codigoEmpleadoInput = document.getElementById('codigoEmpleado');
        const codigoGerenteInput = document.getElementById('codigoGerente');
        const managerCodeSection = document.getElementById('managerCodeSection');
        const btnEntrada = document.getElementById('btnEntrada');
        const btnSalida = document.getElementById('btnSalida');
        const formSection = document.getElementById('formSection');
        const successMessage = document.getElementById('successMessage');
        const errorMessage = document.getElementById('errorMessage');
        const cameraModal = document.getElementById('cameraModal');
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const cameraLoading = document.getElementById('cameraLoading');
        const captureBtn = document.getElementById('captureBtn');
        const retakeBtn = document.getElementById('retakeBtn');
        const confirmBtn = document.getElementById('confirmBtn');
        const closeCamera = document.getElementById('closeCamera');
        const loadingOverlay = document.getElementById('loadingOverlay');
        
        // Update clock
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleString('es-ES', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            document.getElementById('currentTime').textContent = timeString;
        }
        updateClock();
        setInterval(updateClock, 1000);
        
        // Auto-format employee code
        codigoEmpleadoInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '');
        });
        
        codigoGerenteInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '');
        });
        
        // Button click handlers
        btnEntrada.addEventListener('click', function() {
            handleRegistration('entrada');
        });
        
        btnSalida.addEventListener('click', function() {
            handleRegistration('salida');
        });
        
        function handleRegistration(tipo) {
            const codigo = codigoEmpleadoInput.value.trim();
            
            if (codigo.length !== 6) {
                showError('Por favor ingrese un código de empleado válido (6 dígitos)');
                return;
            }
            
            currentTipo = tipo;
            openCamera();
        }
        
        // Camera functions
        async function openCamera() {
            cameraModal.classList.remove('hidden');
            photoData = null;
            
            try {
                stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        facingMode: 'user',
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    } 
                });
                
                video.srcObject = stream;
                video.classList.remove('hidden');
                cameraLoading.classList.add('hidden');
                captureBtn.classList.remove('hidden');
                
            } catch (err) {
                console.error('Error accessing camera:', err);
                showError('No se pudo acceder a la cámara. Por favor verifique los permisos.');
                closeCamera.click();
            }
        }
        
        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
        }
        
        captureBtn.addEventListener('click', function() {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            
            const context = canvas.getContext('2d');
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            photoData = canvas.toDataURL('image/jpeg', 0.8);
            
            video.classList.add('hidden');
            canvas.classList.remove('hidden');
            captureBtn.classList.add('hidden');
            retakeBtn.classList.remove('hidden');
            confirmBtn.classList.remove('hidden');
            
            stopCamera();
        });
        
        retakeBtn.addEventListener('click', function() {
            canvas.classList.add('hidden');
            retakeBtn.classList.add('hidden');
            confirmBtn.classList.add('hidden');
            photoData = null;
            openCamera();
        });
        
        confirmBtn.addEventListener('click', function() {
            if (!photoData) {
                alert('Por favor capture una foto antes de confirmar');
                return;
            }
            cameraModal.classList.add('hidden');
            submitRegistration();
        });
        
        closeCamera.addEventListener('click', function() {
            stopCamera();
            cameraModal.classList.add('hidden');
            video.classList.add('hidden');
            canvas.classList.add('hidden');
            cameraLoading.classList.remove('hidden');
            captureBtn.classList.add('hidden');
            retakeBtn.classList.add('hidden');
            confirmBtn.classList.add('hidden');
        });
        
        // Submit registration
        async function submitRegistration() {
            const codigo = codigoEmpleadoInput.value.trim();
            const codigoGerente = codigoGerenteInput.value.trim();
            
            loadingOverlay.classList.remove('hidden');
            
            try {
                const formData = new FormData();
                formData.append('codigo_empleado', codigo);
                formData.append('tipo_registro', currentTipo);
                formData.append('url_publica', URL_PUBLICA);
                formData.append('foto', photoData);
                
                if (managerCodeSection.classList.contains('hidden') === false && codigoGerente) {
                    formData.append('codigo_gerente', codigoGerente);
                }
                
                const response = await fetch(BASE_URL + 'publico/registrar-asistencia', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showSuccess(data);
                } else {
                    if (data.requiere_gerente) {
                        managerCodeSection.classList.remove('hidden');
                        codigoGerenteInput.focus();
                    }
                    showError(data.message || 'Error al registrar asistencia');
                }
                
            } catch (error) {
                console.error('Error:', error);
                showError('Error de conexión. Por favor intente nuevamente.');
            } finally {
                loadingOverlay.classList.add('hidden');
            }
        }
        
        function showSuccess(data) {
            hideMessages();
            
            const tipoTexto = currentTipo === 'entrada' ? 'Entrada' : 'Salida';
            const iconClass = currentTipo === 'entrada' ? 'fa-sign-in-alt' : 'fa-sign-out-alt';
            
            document.getElementById('successTitle').innerHTML = 
                `<i class="fas ${iconClass} mr-2"></i>¡Registro de ${tipoTexto} Exitoso!`;
            document.getElementById('successSubtitle').textContent = 
                `${tipoTexto} registrada correctamente`;
            document.getElementById('displayNombre').textContent = data.nombre_empleado || '';
            document.getElementById('displayHora').textContent = data.hora || '';
            document.getElementById('displayTipo').textContent = tipoTexto;
            
            // Show hours worked for exit
            if (currentTipo === 'salida' && data.horas_trabajadas) {
                document.getElementById('horasTrabajadasSection').classList.remove('hidden');
                document.getElementById('displayHorasTrabajadas').textContent = data.horas_trabajadas;
            } else {
                document.getElementById('horasTrabajadasSection').classList.add('hidden');
            }
            
            // Show overtime hours
            if (data.horas_extras_acumuladas !== undefined) {
                document.getElementById('horasExtrasSection').classList.remove('hidden');
                document.getElementById('displayHorasExtras').textContent = 
                    parseFloat(data.horas_extras_acumuladas).toFixed(2) + ' hrs';
            } else {
                document.getElementById('horasExtrasSection').classList.add('hidden');
            }
            
            formSection.classList.add('hidden');
            successMessage.classList.remove('hidden');
            
            // Auto-clear after 5 seconds
            setTimeout(resetForm, 5000);
        }
        
        function showError(message) {
            hideMessages();
            document.getElementById('errorText').textContent = message;
            errorMessage.classList.remove('hidden');
            
            // Auto-clear after 5 seconds
            setTimeout(() => {
                errorMessage.classList.add('hidden');
            }, 5000);
        }
        
        function hideMessages() {
            successMessage.classList.add('hidden');
            errorMessage.classList.add('hidden');
        }
        
        function resetForm() {
            codigoEmpleadoInput.value = '';
            codigoGerenteInput.value = '';
            managerCodeSection.classList.add('hidden');
            formSection.classList.remove('hidden');
            hideMessages();
            photoData = null;
            currentTipo = null;
            codigoEmpleadoInput.focus();
        }
        
        // Auto-focus on load
        codigoEmpleadoInput.focus();
        
        // Enter key handler
        codigoEmpleadoInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && this.value.length === 6) {
                btnEntrada.click();
            }
        });
    </script>
</body>
</html>
