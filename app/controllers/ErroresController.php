<?php
/**
 * Controlador de Visualización de Errores
 */
class ErroresController {
    
    public function index() {
        $errorLogPath = BASE_PATH . 'error_log';
        $errores = [];
        
        if (file_exists($errorLogPath)) {
            $contenido = file_get_contents($errorLogPath);
            $lineas = explode("\n", $contenido);
            
            // Parsear errores
            $errorActual = null;
            foreach ($lineas as $linea) {
                if (empty(trim($linea))) continue;
                
                // Detectar inicio de nuevo error con timestamp
                if (preg_match('/^\[(.*?)\]/', $linea, $matches)) {
                    if ($errorActual) {
                        $errores[] = $errorActual;
                    }
                    
                    $errorActual = [
                        'fecha' => $matches[1],
                        'linea_completa' => $linea,
                        'tipo' => $this->detectarTipo($linea),
                        'mensaje' => $this->extraerMensaje($linea),
                        'archivo' => $this->extraerArchivo($linea),
                        'linea_num' => $this->extraerLineaNum($linea)
                    ];
                } else {
                    // Línea continuación del error anterior
                    if ($errorActual) {
                        $errorActual['mensaje'] .= "\n" . $linea;
                    }
                }
            }
            
            // Agregar último error
            if ($errorActual) {
                $errores[] = $errorActual;
            }
            
            // Mostrar más recientes primero
            $errores = array_reverse($errores);
            
            // Limitar a últimos 100 errores
            $errores = array_slice($errores, 0, 100);
        }
        
        // Estadísticas
        $estadisticas = $this->calcularEstadisticas($errores);
        
        view('errores/index', [
            'errores' => $errores,
            'estadisticas' => $estadisticas,
            'archivo_existe' => file_exists($errorLogPath),
            'tamano_archivo' => file_exists($errorLogPath) ? filesize($errorLogPath) : 0
        ]);
    }
    
    public function limpiar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errorLogPath = BASE_PATH . 'error_log';
            
            if (file_exists($errorLogPath)) {
                // Hacer backup antes de limpiar
                $backupPath = BASE_PATH . 'error_log_backup_' . date('Y-m-d_H-i-s') . '.txt';
                copy($errorLogPath, $backupPath);
                
                // Limpiar archivo
                file_put_contents($errorLogPath, '');
                
                $_SESSION['mensaje'] = 'Archivo de errores limpiado. Backup guardado.';
                $_SESSION['tipo_mensaje'] = 'success';
            }
        }
        
        redirect('errores');
    }
    
    public function descargar() {
        $errorLogPath = BASE_PATH . 'error_log';
        
        if (file_exists($errorLogPath)) {
            header('Content-Type: text/plain');
            header('Content-Disposition: attachment; filename="error_log_' . date('Y-m-d_H-i-s') . '.txt"');
            header('Content-Length: ' . filesize($errorLogPath));
            readfile($errorLogPath);
            exit;
        } else {
            die('Archivo no encontrado');
        }
    }
    
    public function obtenerJson() {
        header('Content-Type: application/json');
        
        $errorLogPath = BASE_PATH . 'error_log';
        $errores = [];
        
        if (file_exists($errorLogPath)) {
            $contenido = file_get_contents($errorLogPath);
            $lineas = explode("\n", $contenido);
            
            // Parsear errores
            $errorActual = null;
            foreach ($lineas as $linea) {
                if (empty(trim($linea))) continue;
                
                // Detectar inicio de nuevo error con timestamp
                if (preg_match('/^\[(.*?)\]/', $linea, $matches)) {
                    if ($errorActual) {
                        $errores[] = $errorActual;
                    }
                    
                    $errorActual = [
                        'fecha' => $matches[1],
                        'linea_completa' => $linea,
                        'tipo' => $this->detectarTipo($linea),
                        'mensaje' => $this->extraerMensaje($linea),
                        'archivo' => $this->extraerArchivo($linea),
                        'linea_num' => $this->extraerLineaNum($linea)
                    ];
                } else {
                    // Línea continuación del error anterior
                    if ($errorActual) {
                        $errorActual['mensaje'] .= "\n" . $linea;
                    }
                }
            }
            
            // Agregar último error
            if ($errorActual) {
                $errores[] = $errorActual;
            }
            
            // Mostrar más recientes primero
            $errores = array_reverse($errores);
            
            // Limitar a últimos 100 errores
            $errores = array_slice($errores, 0, 100);
        }
        
        // Estadísticas
        $estadisticas = $this->calcularEstadisticas($errores);
        
        echo json_encode([
            'success' => true,
            'errores' => $errores,
            'estadisticas' => $estadisticas,
            'archivo_existe' => file_exists($errorLogPath),
            'tamano_archivo' => file_exists($errorLogPath) ? filesize($errorLogPath) : 0,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit;
    }
    
    private function detectarTipo($linea) {
        if (stripos($linea, 'Fatal error') !== false) return 'fatal';
        if (stripos($linea, 'Warning') !== false) return 'warning';
        if (stripos($linea, 'Notice') !== false) return 'notice';
        if (stripos($linea, 'Parse error') !== false) return 'parse';
        if (stripos($linea, 'Deprecated') !== false) return 'deprecated';
        if (stripos($linea, 'Error') !== false) return 'error';
        return 'info';
    }
    
    private function extraerMensaje($linea) {
        // Remover timestamp
        $mensaje = preg_replace('/^\[.*?\]\s*/', '', $linea);
        // Remover "PHP Warning:", "PHP Fatal error:", etc.
        $mensaje = preg_replace('/PHP\s+(Warning|Fatal error|Notice|Parse error|Deprecated|Error):\s*/', '', $mensaje);
        return $mensaje;
    }
    
    private function extraerArchivo($linea) {
        if (preg_match('/in\s+(.+?)\s+on\s+line/', $linea, $matches)) {
            return basename($matches[1]);
        }
        if (preg_match('/in\s+(.+?):\d+/', $linea, $matches)) {
            return basename($matches[1]);
        }
        return null;
    }
    
    private function extraerLineaNum($linea) {
        if (preg_match('/on\s+line\s+(\d+)/', $linea, $matches)) {
            return $matches[1];
        }
        if (preg_match('/:(\d+)$/', $linea, $matches)) {
            return $matches[1];
        }
        return null;
    }
    
    private function calcularEstadisticas($errores) {
        $stats = [
            'total' => count($errores),
            'fatal' => 0,
            'warning' => 0,
            'notice' => 0,
            'error' => 0,
            'otros' => 0
        ];
        
        foreach ($errores as $error) {
            switch ($error['tipo']) {
                case 'fatal':
                case 'parse':
                    $stats['fatal']++;
                    break;
                case 'warning':
                    $stats['warning']++;
                    break;
                case 'notice':
                case 'deprecated':
                    $stats['notice']++;
                    break;
                case 'error':
                    $stats['error']++;
                    break;
                default:
                    $stats['otros']++;
            }
        }
        
        return $stats;
    }
}
