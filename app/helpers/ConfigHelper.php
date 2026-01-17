<?php
/**
 * Helper para Configuraciones Globales
 */

class ConfigHelper {
    private static $instance = null;
    private $configs = [];
    
    private function __construct() {
        $this->loadConfigurations();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Cargar todas las configuraciones de la base de datos
     */
    private function loadConfigurations() {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->query("SELECT clave, valor FROM configuraciones_globales");
            $result = $stmt->fetchAll();
            
            foreach ($result as $row) {
                $this->configs[$row['clave']] = $row['valor'];
            }
        } catch (Exception $e) {
            // Si falla, usar valores por defecto
            $this->configs = $this->getDefaults();
        }
    }
    
    /**
     * Obtener valor de una configuración
     */
    public function get($clave, $default = null) {
        return $this->configs[$clave] ?? $default;
    }
    
    /**
     * Obtener todas las configuraciones
     */
    public function getAll() {
        return $this->configs;
    }
    
    /**
     * Obtener nombre del sitio
     */
    public function getSiteName() {
        return $this->get('sitio_nombre', 'Sistema RRHH Sinforosa Café');
    }
    
    /**
     * Obtener logo del sitio
     */
    public function getLogo() {
        return $this->get('sitio_logo', '');
    }
    
    /**
     * Obtener color primario
     */
    public function getColorPrimario() {
        return $this->get('estilo_color_primario', '#667eea');
    }
    
    /**
     * Obtener color secundario
     */
    public function getColorSecundario() {
        return $this->get('estilo_color_secundario', '#764ba2');
    }
    
    /**
     * Obtener color de acento
     */
    public function getColorAccent() {
        return $this->get('estilo_color_acento', '#f59e0b');
    }
    
    /**
     * Generar CSS con colores personalizados
     */
    public function generateCustomCSS() {
        $colorPrimario = $this->getColorPrimario();
        $colorSecundario = $this->getColorSecundario();
        $colorAccent = $this->getColorAccent();
        
        return "
        <style>
            :root {
                --color-primario: {$colorPrimario};
                --color-secundario: {$colorSecundario};
                --color-acento: {$colorAccent};
            }
            
            .bg-gradient-sinforosa {
                background: linear-gradient(135deg, {$colorPrimario} 0%, {$colorSecundario} 100%);
            }
            
            .text-primary {
                color: {$colorPrimario};
            }
            
            .bg-primary {
                background-color: {$colorPrimario};
            }
            
            .bg-secondary {
                background-color: {$colorSecundario};
            }
            
            .border-primary {
                border-color: {$colorPrimario};
            }
            
            .hover\\:bg-primary:hover {
                background-color: {$colorPrimario};
            }
            
            /* Botones personalizados */
            .btn-primary {
                background: linear-gradient(135deg, {$colorPrimario} 0%, {$colorSecundario} 100%);
                color: white;
                border: none;
            }
            
            .btn-primary:hover {
                opacity: 0.9;
            }
            
            /* Override Tailwind purple colors with custom colors */
            .bg-purple-600 {
                background-color: {$colorPrimario} !important;
            }
            
            .bg-purple-700 {
                background-color: {$colorSecundario} !important;
            }
            
            .text-purple-600 {
                color: {$colorPrimario} !important;
            }
            
            .text-purple-700 {
                color: {$colorSecundario} !important;
            }
            
            .border-purple-500 {
                border-color: {$colorPrimario} !important;
            }
            
            .focus\\:ring-purple-500:focus {
                --tw-ring-color: {$colorPrimario} !important;
            }
            
            .focus\\:border-purple-500:focus {
                border-color: {$colorPrimario} !important;
            }
            
            /* Hover states */
            .hover\\:bg-purple-700:hover {
                background-color: {$colorSecundario} !important;
            }
            
            .hover\\:text-purple-800:hover {
                color: {$colorSecundario} !important;
            }
        </style>
        ";
    }
    
    /**
     * Generar HTML del logo
     */
    public function renderLogo($classes = 'h-10 w-10') {
        $logo = $this->getLogo();
        $siteName = $this->getSiteName();
        
        if (!empty($logo)) {
            // Si hay logo, mostrarlo
            $logoUrl = (strpos($logo, 'http') === 0) ? $logo : BASE_URL . $logo;
            return '<img src="' . htmlspecialchars($logoUrl) . '" alt="' . htmlspecialchars($siteName) . '" class="' . $classes . '">';
        } else {
            // Si no hay logo, mostrar icono por defecto
            return '
            <div class="' . $classes . ' bg-white rounded-lg flex items-center justify-center">
                <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>';
        }
    }
    
    /**
     * Valores por defecto
     */
    private function getDefaults() {
        return [
            'sitio_nombre' => 'Sistema RRHH Sinforosa Café',
            'sitio_logo' => '',
            'estilo_color_primario' => '#667eea',
            'estilo_color_secundario' => '#764ba2',
            'estilo_color_acento' => '#f59e0b'
        ];
    }
}
