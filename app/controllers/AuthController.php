<?php
/**
 * Controlador de Autenticación
 */

class AuthController {
    
    /**
     * Mostrar página de login
     */
    public function login() {
        // Si ya está logueado, redirigir al dashboard
        if (isset($_SESSION['user_id'])) {
            redirect('dashboard');
        }
        
        $error = '';
        
        // Procesar login
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if (empty($email) || empty($password)) {
                $error = 'Por favor ingrese email y contraseña';
            } else {
                $userModel = new Usuario();
                $user = $userModel->login($email, $password);
                
                if ($user) {
                    // Guardar en sesión
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['nombre'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_rol'] = $user['rol'];
                    
                    redirect('dashboard');
                } else {
                    $error = 'Credenciales incorrectas';
                }
            }
        }
        
        view('auth/login', ['error' => $error]);
    }
    
    /**
     * Cerrar sesión
     */
    public function logout() {
        session_destroy();
        redirect('login');
    }
    
    /**
     * Verificar si el usuario está autenticado
     */
    public static function check() {
        if (!isset($_SESSION['user_id'])) {
            redirect('login');
        }
    }
    
    /**
     * Verificar rol del usuario
     */
    public static function checkRole($roles = []) {
        self::check();
        
        if (!empty($roles) && !in_array($_SESSION['user_rol'], $roles)) {
            http_response_code(403);
            die('Acceso denegado');
        }
    }
}
