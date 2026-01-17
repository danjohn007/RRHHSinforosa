<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php 
    $configHelper = ConfigHelper::getInstance();
    $siteName = $configHelper->getSiteName();
    ?>
    <title>Login - <?php echo $siteName; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- CSS personalizado con colores del sistema -->
    <?php echo $configHelper->generateCustomCSS(); ?>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Logo y título -->
            <div class="text-center">
                <div class="mx-auto mb-4 flex items-center justify-center">
                    <?php 
                    $logo = $configHelper->getLogo();
                    if (!empty($logo)) {
                        // Mostrar logo personalizado
                        $logoUrl = (strpos($logo, 'http') === 0) ? $logo : BASE_URL . $logo;
                        echo '<img src="' . htmlspecialchars($logoUrl) . '" alt="' . htmlspecialchars($siteName) . '" class="h-20 w-20 object-contain">';
                    } else {
                        // Mostrar logo por defecto
                        echo '<div class="h-20 w-20 bg-gradient-sinforosa rounded-full flex items-center justify-center">
                            <svg class="h-12 w-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>';
                    }
                    ?>
                </div>
                <h2 class="text-3xl font-extrabold text-gray-900">
                    <?php echo htmlspecialchars($siteName); ?>
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Sistema de Gestión de RRHH
                </p>
            </div>

            <!-- Formulario de login -->
            <div class="bg-white shadow-xl rounded-lg px-8 py-10">
                <?php if (!empty($error)): ?>
                <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700"><?php echo htmlspecialchars($error); ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo BASE_URL; ?>login" class="space-y-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Correo Electrónico
                        </label>
                        <input 
                            id="email" 
                            name="email" 
                            type="email" 
                            required 
                            class="appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm transition"
                            placeholder="usuario@sinforosa.com"
                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                        >
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Contraseña
                        </label>
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            required 
                            class="appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm transition"
                            placeholder="••••••••"
                        >
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input 
                                id="remember" 
                                name="remember" 
                                type="checkbox" 
                                class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded cursor-pointer"
                            >
                            <label for="remember" class="ml-2 block text-sm text-gray-700 cursor-pointer">
                                Recordarme
                            </label>
                        </div>
                    </div>

                    <div>
                        <button 
                            type="submit" 
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-sinforosa hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200 transform hover:scale-[1.02]"
                        >
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            Iniciar Sesión
                        </button>
                    </div>
                </form>

                <!-- Información de usuarios demo -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-xs text-gray-500 text-center mb-2">Usuarios de demostración:</p>
                    <div class="text-xs text-gray-600 space-y-1">
                        <p><strong>Admin:</strong> admin@sinforosa.com</p>
                        <p><strong>RRHH:</strong> rrhh@sinforosa.com</p>
                        <p><strong>Gerente:</strong> gerente@sinforosa.com</p>
                        <p class="text-purple-600 font-medium mt-2">Contraseña: password</p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <p class="text-center text-xs text-gray-500">
                © 2024 Sinforosa Café. Sistema de RRHH v<?php echo APP_VERSION; ?>
            </p>
        </div>
    </div>
</body>
</html>
