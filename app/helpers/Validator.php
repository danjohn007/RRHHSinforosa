<?php
/**
 * Clase de Validaciones - Backend PHP
 * Contiene todas las validaciones del lado del servidor
 */

class Validator {
    
    private $errors = [];
    
    /**
     * Validar teléfono (10 dígitos)
     */
    public function validarTelefono($telefono, $campo = 'Teléfono') {
        $telefono = preg_replace('/\D/', '', $telefono); // Eliminar todo excepto números
        
        if (!empty($telefono) && !preg_match('/^[0-9]{10}$/', $telefono)) {
            $this->errors[$campo] = "$campo debe tener exactamente 10 dígitos";
            return false;
        }
        return true;
    }
    
    /**
     * Validar email
     */
    public function validarEmail($email, $campo = 'Email') {
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$campo] = "$campo no es válido";
            return false;
        }
        return true;
    }
    
    /**
     * Validar CURP (18 caracteres)
     */
    public function validarCURP($curp, $campo = 'CURP') {
        $curp = strtoupper($curp);
        
        if (!empty($curp) && !preg_match('/^[A-Z]{4}[0-9]{6}[HM][A-Z]{5}[0-9A-Z][0-9]$/', $curp)) {
            $this->errors[$campo] = "$campo no es válido (debe tener 18 caracteres)";
            return false;
        }
        return true;
    }
    
    /**
     * Validar RFC
     */
    public function validarRFC($rfc, $campo = 'RFC') {
        $rfc = strtoupper($rfc);
        
        if (!empty($rfc)) {
            // Persona física (13) o moral (12)
            if (!preg_match('/^[A-ZÑ&]{3,4}[0-9]{6}[A-Z0-9]{3}$/', $rfc)) {
                $this->errors[$campo] = "$campo no es válido";
                return false;
            }
        }
        return true;
    }
    
    /**
     * Validar NSS (11 dígitos)
     */
    public function validarNSS($nss, $campo = 'NSS') {
        if (!empty($nss) && !preg_match('/^[0-9]{11}$/', $nss)) {
            $this->errors[$campo] = "$campo debe tener 11 dígitos";
            return false;
        }
        return true;
    }
    
    /**
     * Validar código postal (5 dígitos)
     */
    public function validarCodigoPostal($cp, $campo = 'Código Postal') {
        if (!empty($cp) && !preg_match('/^[0-9]{5}$/', $cp)) {
            $this->errors[$campo] = "$campo debe tener 5 dígitos";
            return false;
        }
        return true;
    }
    
    /**
     * Validar CLABE interbancaria (18 dígitos)
     */
    public function validarCLABE($clabe, $campo = 'CLABE') {
        if (!empty($clabe) && !preg_match('/^[0-9]{18}$/', $clabe)) {
            $this->errors[$campo] = "$campo debe tener 18 dígitos";
            return false;
        }
        return true;
    }
    
    /**
     * Validar que un campo sea requerido
     */
    public function requerido($valor, $campo) {
        if (empty(trim($valor))) {
            $this->errors[$campo] = "$campo es requerido";
            return false;
        }
        return true;
    }
    
    /**
     * Validar longitud mínima
     */
    public function longitudMinima($valor, $minimo, $campo) {
        if (!empty($valor) && strlen($valor) < $minimo) {
            $this->errors[$campo] = "$campo debe tener al menos $minimo caracteres";
            return false;
        }
        return true;
    }
    
    /**
     * Validar longitud máxima
     */
    public function longitudMaxima($valor, $maximo, $campo) {
        if (!empty($valor) && strlen($valor) > $maximo) {
            $this->errors[$campo] = "$campo no debe exceder $maximo caracteres";
            return false;
        }
        return true;
    }
    
    /**
     * Validar que sea un número
     */
    public function esNumero($valor, $campo) {
        if (!empty($valor) && !is_numeric($valor)) {
            $this->errors[$campo] = "$campo debe ser un número";
            return false;
        }
        return true;
    }
    
    /**
     * Validar que sea un número positivo
     */
    public function numeroPositivo($valor, $campo) {
        if (!empty($valor) && (!is_numeric($valor) || $valor <= 0)) {
            $this->errors[$campo] = "$campo debe ser un número positivo";
            return false;
        }
        return true;
    }
    
    /**
     * Validar edad mínima
     */
    public function edadMinima($fechaNacimiento, $edadMinima = 18, $campo = 'Fecha de nacimiento') {
        if (!empty($fechaNacimiento)) {
            $fecha = new DateTime($fechaNacimiento);
            $hoy = new DateTime();
            $edad = $hoy->diff($fecha)->y;
            
            if ($edad < $edadMinima) {
                $this->errors[$campo] = "La edad mínima es de $edadMinima años";
                return false;
            }
        }
        return true;
    }
    
    /**
     * Validar fecha (formato YYYY-MM-DD)
     */
    public function validarFecha($fecha, $campo = 'Fecha') {
        if (!empty($fecha)) {
            $d = DateTime::createFromFormat('Y-m-d', $fecha);
            if (!$d || $d->format('Y-m-d') !== $fecha) {
                $this->errors[$campo] = "$campo no es válida";
                return false;
            }
        }
        return true;
    }
    
    /**
     * Validar que fecha1 sea menor que fecha2
     */
    public function fechaMenorQue($fecha1, $fecha2, $campo1, $campo2) {
        if (!empty($fecha1) && !empty($fecha2)) {
            $d1 = new DateTime($fecha1);
            $d2 = new DateTime($fecha2);
            
            if ($d1 >= $d2) {
                $this->errors[$campo1] = "$campo1 debe ser menor que $campo2";
                return false;
            }
        }
        return true;
    }
    
    /**
     * Validar que solo contenga letras y espacios
     */
    public function soloLetras($valor, $campo) {
        if (!empty($valor) && !preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $valor)) {
            $this->errors[$campo] = "$campo solo debe contener letras";
            return false;
        }
        return true;
    }
    
    /**
     * Validar datos de empleado
     */
    public function validarEmpleado($data) {
        $this->errors = []; // Limpiar errores anteriores
        
        // Campos requeridos
        $this->requerido($data['nombres'] ?? '', 'Nombres');
        $this->requerido($data['apellido_paterno'] ?? '', 'Apellido Paterno');
        $this->requerido($data['fecha_ingreso'] ?? '', 'Fecha de Ingreso');
        $this->requerido($data['departamento'] ?? '', 'Departamento');
        $this->requerido($data['puesto'] ?? '', 'Puesto');
        $this->requerido($data['tipo_contrato'] ?? '', 'Tipo de Contrato');
        
        // Validaciones específicas
        $this->soloLetras($data['nombres'] ?? '', 'Nombres');
        $this->soloLetras($data['apellido_paterno'] ?? '', 'Apellido Paterno');
        $this->soloLetras($data['apellido_materno'] ?? '', 'Apellido Materno');
        
        $this->validarTelefono($data['telefono'] ?? '', 'Teléfono');
        $this->validarTelefono($data['celular'] ?? '', 'Celular');
        $this->validarEmail($data['email_personal'] ?? '', 'Email Personal');
        
        $this->validarCURP($data['curp'] ?? '', 'CURP');
        $this->validarRFC($data['rfc'] ?? '', 'RFC');
        $this->validarNSS($data['nss'] ?? '', 'NSS');
        
        $this->validarCodigoPostal($data['codigo_postal'] ?? '', 'Código Postal');
        
        if (!empty($data['fecha_nacimiento'])) {
            $this->validarFecha($data['fecha_nacimiento'], 'Fecha de Nacimiento');
            $this->edadMinima($data['fecha_nacimiento'], 18, 'Edad');
        }
        
        $this->validarFecha($data['fecha_ingreso'] ?? '', 'Fecha de Ingreso');
        
        $this->numeroPositivo($data['salario_mensual'] ?? 0, 'Salario Mensual');
        
        return empty($this->errors);
    }
    
    /**
     * Obtener todos los errores
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Verificar si hay errores
     */
    public function hasErrors() {
        return !empty($this->errors);
    }
    
    /**
     * Obtener primer error
     */
    public function getFirstError() {
        return !empty($this->errors) ? reset($this->errors) : null;
    }
    
    /**
     * Limpiar errores
     */
    public function clearErrors() {
        $this->errors = [];
    }
    
    /**
     * Sanitizar string
     */
    public static function sanitize($string) {
        return htmlspecialchars(trim($string), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Limpiar teléfono (solo números)
     */
    public static function limpiarTelefono($telefono) {
        return preg_replace('/\D/', '', $telefono);
    }
    
    /**
     * Validar y limpiar datos de entrada
     */
    public static function sanitizeArray($data) {
        $clean = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $clean[$key] = self::sanitizeArray($value);
            } else {
                $clean[$key] = self::sanitize($value);
            }
        }
        return $clean;
    }
}
