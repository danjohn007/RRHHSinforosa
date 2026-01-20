<?php
/**
 * Servicio de Nómina - Cálculos y Procesamiento
 * Contiene toda la lógica de negocio para cálculos de ISR, IMSS y procesamiento de nómina
 */

class NominaService {
    private $db;
    
    // Constante para días base mensual (estándar en cálculos laborales México)
    const DIAS_MES_BASE = 30;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Calcular ISR mensual según tablas 2026
     * @param float $ingreso Ingreso mensual gravable
     * @return float ISR a retener
     */
    public function calcularISR($ingreso) {
        // Tabla de ISR Mensual 2026 (artículo 96 LISR)
        $tablasISR = [
            ['limite_inferior' => 0.01, 'limite_superior' => 746.04, 'cuota_fija' => 0.00, 'porcentaje' => 1.92],
            ['limite_inferior' => 746.05, 'limite_superior' => 6332.05, 'cuota_fija' => 14.32, 'porcentaje' => 6.40],
            ['limite_inferior' => 6332.06, 'limite_superior' => 11128.01, 'cuota_fija' => 371.83, 'porcentaje' => 10.88],
            ['limite_inferior' => 11128.02, 'limite_superior' => 12935.82, 'cuota_fija' => 893.63, 'porcentaje' => 16.00],
            ['limite_inferior' => 12935.83, 'limite_superior' => 15487.71, 'cuota_fija' => 1182.88, 'porcentaje' => 17.92],
            ['limite_inferior' => 15487.72, 'limite_superior' => 31236.49, 'cuota_fija' => 1640.18, 'porcentaje' => 21.36],
            ['limite_inferior' => 31236.50, 'limite_superior' => 49233.00, 'cuota_fija' => 5004.12, 'porcentaje' => 23.52],
            ['limite_inferior' => 49233.01, 'limite_superior' => 93993.90, 'cuota_fija' => 9236.89, 'porcentaje' => 30.00],
            ['limite_inferior' => 93993.91, 'limite_superior' => 125325.20, 'cuota_fija' => 22665.17, 'porcentaje' => 32.00],
            ['limite_inferior' => 125325.21, 'limite_superior' => 375975.61, 'cuota_fija' => 32691.18, 'porcentaje' => 34.00],
            ['limite_inferior' => 375975.62, 'limite_superior' => PHP_FLOAT_MAX, 'cuota_fija' => 117912.32, 'porcentaje' => 35.00]
        ];
        
        $isr = 0;
        
        foreach ($tablasISR as $rango) {
            if ($ingreso >= $rango['limite_inferior'] && $ingreso <= $rango['limite_superior']) {
                $excedente = $ingreso - $rango['limite_inferior'];
                $isr = $rango['cuota_fija'] + ($excedente * ($rango['porcentaje'] / 100));
                break;
            }
        }
        
        return round($isr, 2);
    }
    
    /**
     * Calcular subsidio al empleo mensual 2026
     * @param float $ingreso Ingreso mensual
     * @return float Subsidio al empleo
     */
    public function calcularSubsidioEmpleo($ingreso) {
        $tablaSubsidio = [
            ['limite_inferior' => 0.01, 'limite_superior' => 1768.96, 'subsidio' => 407.02],
            ['limite_inferior' => 1768.97, 'limite_superior' => 2653.38, 'subsidio' => 406.83],
            ['limite_inferior' => 2653.39, 'limite_superior' => 3472.84, 'subsidio' => 406.62],
            ['limite_inferior' => 3472.85, 'limite_superior' => 3537.87, 'subsidio' => 392.77],
            ['limite_inferior' => 3537.88, 'limite_superior' => 4446.15, 'subsidio' => 382.46],
            ['limite_inferior' => 4446.16, 'limite_superior' => 4717.18, 'subsidio' => 354.23],
            ['limite_inferior' => 4717.19, 'limite_superior' => 5335.42, 'subsidio' => 324.87],
            ['limite_inferior' => 5335.43, 'limite_superior' => 6224.67, 'subsidio' => 294.63],
            ['limite_inferior' => 6224.68, 'limite_superior' => 7113.90, 'subsidio' => 253.54],
            ['limite_inferior' => 7113.91, 'limite_superior' => 7382.33, 'subsidio' => 217.61],
            ['limite_inferior' => 7382.34, 'limite_superior' => PHP_FLOAT_MAX, 'subsidio' => 0.00]
        ];
        
        $subsidio = 0;
        
        foreach ($tablaSubsidio as $rango) {
            if ($ingreso >= $rango['limite_inferior'] && $ingreso <= $rango['limite_superior']) {
                $subsidio = $rango['subsidio'];
                break;
            }
        }
        
        return round($subsidio, 2);
    }
    
    /**
     * Calcular IMSS - Cuota obrera
     * @param float $salarioBase Salario base de cotización
     * @param float $uma Valor de la UMA (Unidad de Medida y Actualización) 2026 = 108.57
     * @return array Desglose de cuotas IMSS
     */
    public function calcularIMSS($salarioBase, $uma = 108.57) {
        $umaMensual = $uma * 30.4; // UMA mensual = 3,300.53
        $topeSalario = $umaMensual * 25; // Tope de salario = 82,513.25
        
        // Limitar salario al tope
        $salarioCotizacion = min($salarioBase, $topeSalario);
        
        // Calcular excedente de 3 UMAs
        $treUMAs = $umaMensual * 3;
        $excedente = max(0, $salarioCotizacion - $treUMAs);
        
        // Cuotas IMSS (porcentajes 2026)
        $cuotas = [
            'enfermedad_maternidad' => ($treUMAs * 0.004) + ($excedente * 0.004), // 0.4%
            'invalidez_vida' => $salarioCotizacion * 0.00625, // 0.625%
            'cesantia_vejez' => $salarioCotizacion * 0.01125, // 1.125%
            'total' => 0
        ];
        
        $cuotas['total'] = $cuotas['enfermedad_maternidad'] + $cuotas['invalidez_vida'] + $cuotas['cesantia_vejez'];
        
        // Redondear cada concepto
        foreach ($cuotas as $key => $valor) {
            $cuotas[$key] = round($valor, 2);
        }
        
        return $cuotas;
    }
    
    /**
     * Calcular cuotas patronales IMSS
     * @param float $salarioBase Salario base de cotización
     * @param float $uma Valor de la UMA 2026
     * @return array Desglose de cuotas patronales
     */
    public function calcularIMSSPatronal($salarioBase, $uma = 108.57) {
        $umaMensual = $uma * 30.4;
        $topeSalario = $umaMensual * 25;
        $salarioCotizacion = min($salarioBase, $topeSalario);
        
        $treUMAs = $umaMensual * 3;
        $excedente = max(0, $salarioCotizacion - $treUMAs);
        
        // Cuotas patronales (porcentajes 2026)
        $cuotasPatronales = [
            'enfermedad_maternidad_fija' => $treUMAs * 0.0204, // 20.4% sobre 3 UMAs
            'enfermedad_maternidad_excedente' => $excedente * 0.0110, // 1.1% sobre excedente
            'invalidez_vida' => $salarioCotizacion * 0.0175, // 1.75%
            'guarderias' => $salarioCotizacion * 0.0100, // 1%
            'riesgo_trabajo' => $salarioCotizacion * 0.00540, // 0.540% (clase I mínima)
            'cesantia_vejez' => $salarioCotizacion * 0.0315, // 3.15%
            'total' => 0
        ];
        
        $cuotasPatronales['total'] = array_sum($cuotasPatronales);
        
        foreach ($cuotasPatronales as $key => $valor) {
            $cuotasPatronales[$key] = round($valor, 2);
        }
        
        return $cuotasPatronales;
    }
    
    /**
     * Procesar nómina de un período completo
     * @param int $periodoId ID del período de nómina
     * @return array Resultado del procesamiento
     */
    public function procesarNomina($periodoId) {
        try {
            $this->db->beginTransaction();
            
            // Obtener período
            $stmt = $this->db->prepare("SELECT * FROM periodos_nomina WHERE id = ?");
            $stmt->execute([$periodoId]);
            $periodo = $stmt->fetch();
            
            if (!$periodo) {
                throw new Exception("Período no encontrado");
            }
            
            // Obtener empleados activos
            $stmt = $this->db->query("
                SELECT id, numero_empleado, salario_mensual, salario_diario 
                FROM empleados 
                WHERE estatus = 'Activo'
            ");
            $empleados = $stmt->fetchAll();
            
            // Validar que haya empleados activos
            if (empty($empleados)) {
                throw new Exception("No hay empleados activos para procesar");
            }
            
            $procesados = 0;
            $errores = [];
            
            foreach ($empleados as $empleado) {
                try {
                    $this->procesarNominaEmpleado($periodoId, $empleado);
                    $procesados++;
                } catch (Exception $e) {
                    $errores[] = "Error en empleado {$empleado['numero_empleado']}: " . $e->getMessage();
                }
            }
            
            // Actualizar totales del período
            $this->actualizarTotalesPeriodo($periodoId);
            
            // Cambiar estatus del período
            $stmt = $this->db->prepare("
                UPDATE periodos_nomina 
                SET estatus = 'Procesado', 
                    fecha_proceso = NOW(),
                    usuario_proceso_id = ?
                WHERE id = ?
            ");
            $stmt->execute([$_SESSION['user_id'] ?? 1, $periodoId]);
            
            $this->db->commit();
            
            return [
                'success' => true,
                'procesados' => $procesados,
                'errores' => $errores
            ];
            
        } catch (Exception $e) {
            $this->db->rollBack();
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Procesar nómina individual de un empleado
     * @param int $periodoId ID del período
     * @param array $empleado Datos del empleado
     */
    private function procesarNominaEmpleado($periodoId, $empleado) {
        $salarioMensual = $empleado['salario_mensual'];
        $salarioDiario = $empleado['salario_diario'];
        $empleadoId = $empleado['id'];
        
        // Validar que el salario sea válido
        if ((!$salarioMensual || $salarioMensual <= 0) && (!$salarioDiario || $salarioDiario <= 0)) {
            throw new Exception("El empleado {$empleado['numero_empleado']} no tiene un salario válido");
        }
        
        // Obtener días trabajados según el periodo
        $diasTrabajados = $this->calcularDiasTrabajados($empleadoId, $periodoId);
        
        // Calcular salario base proporcional a los días trabajados
        if ($salarioDiario > 0) {
            $salarioBase = $salarioDiario * $diasTrabajados;
        } else {
            // Si no hay salario diario, calcular desde el mensual
            $salarioDiario = $salarioMensual / self::DIAS_MES_BASE;
            $salarioBase = $salarioDiario * $diasTrabajados;
        }
        
        // Calcular incidencias
        $incidencias = $this->obtenerIncidencias($empleadoId, $periodoId);
        
        // Calcular percepciones
        $percepciones = [
            'sueldo_base' => $salarioBase,
            'horas_extra' => $incidencias['horas_extra'] ?? 0,
            'bonos' => $incidencias['bonos'] ?? 0
        ];
        
        $totalPercepciones = array_sum($percepciones);
        
        // Calcular deducciones proporcionalmente
        // ISR se calcula sobre el total de percepciones
        $isr = $this->calcularISR($totalPercepciones);
        $subsidio = $this->calcularSubsidioEmpleo($totalPercepciones);
        $isrNeto = max(0, $isr - $subsidio);
        
        // IMSS se calcula proporcionalmente según días trabajados
        $cuotasIMSSMensual = $this->calcularIMSS($salarioMensual);
        $imssProporcionado = ($cuotasIMSSMensual['total'] / self::DIAS_MES_BASE) * $diasTrabajados;
        
        $deducciones = [
            'isr' => $isrNeto,
            'imss' => $imssProporcionado,
            'prestamos' => $incidencias['prestamos'] ?? 0,
            'otros_descuentos' => $incidencias['descuentos'] ?? 0
        ];
        
        $totalDeducciones = array_sum($deducciones);
        
        // Calcular neto
        $subtotal = $totalPercepciones;
        $totalNeto = $subtotal - $totalDeducciones;
        
        // Verificar si ya existe registro
        $stmt = $this->db->prepare("
            SELECT id FROM nomina_detalle 
            WHERE periodo_id = ? AND empleado_id = ?
        ");
        $stmt->execute([$periodoId, $empleadoId]);
        $existe = $stmt->fetch();
        
        if ($existe) {
            // Actualizar
            $stmt = $this->db->prepare("
                UPDATE nomina_detalle SET
                    dias_trabajados = ?,
                    salario_base = ?,
                    total_percepciones = ?,
                    total_deducciones = ?,
                    subtotal = ?,
                    isr = ?,
                    imss = ?,
                    total_neto = ?,
                    estatus = 'Calculado'
                WHERE id = ?
            ");
            $stmt->execute([
                $diasTrabajados,
                $salarioBase,
                $totalPercepciones,
                $totalDeducciones,
                $subtotal,
                $isrNeto,
                $imssProporcionado,
                $totalNeto,
                $existe['id']
            ]);
            $nominaDetalleId = $existe['id'];
        } else {
            // Insertar
            $stmt = $this->db->prepare("
                INSERT INTO nomina_detalle (
                    periodo_id, empleado_id, dias_trabajados, salario_base,
                    total_percepciones, total_deducciones, subtotal,
                    isr, imss, total_neto, estatus
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Calculado')
            ");
            $stmt->execute([
                $periodoId,
                $empleadoId,
                $diasTrabajados,
                $salarioBase,
                $totalPercepciones,
                $totalDeducciones,
                $subtotal,
                $isrNeto,
                $imssProporcionado,
                $totalNeto
            ]);
            $nominaDetalleId = $this->db->lastInsertId();
        }
        
        // Guardar conceptos detallados
        $this->guardarConceptosNomina($nominaDetalleId, $percepciones, $deducciones);
    }
    
    /**
     * Calcular días trabajados en el período
     */
    private function calcularDiasTrabajados($empleadoId, $periodoId) {
        $stmt = $this->db->prepare("
            SELECT fecha_inicio, fecha_fin, tipo FROM periodos_nomina WHERE id = ?
        ");
        $stmt->execute([$periodoId]);
        $periodo = $stmt->fetch();
        
        if (!$periodo) {
            return 0;
        }
        
        // Calcular días totales del periodo
        $fechaInicio = new DateTime($periodo['fecha_inicio']);
        $fechaFin = new DateTime($periodo['fecha_fin']);
        $diasPeriodo = $fechaInicio->diff($fechaFin)->days + 1; // +1 para incluir ambos días
        
        // Contar días de asistencia
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as dias FROM asistencias 
            WHERE empleado_id = ? 
            AND fecha BETWEEN ? AND ?
            AND estatus IN ('Presente', 'Retardo')
        ");
        $stmt->execute([$empleadoId, $periodo['fecha_inicio'], $periodo['fecha_fin']]);
        $result = $stmt->fetch();
        $diasAsistidos = $result['dias'];
        
        // Si hay registros de asistencia, usar esos días
        // Si no hay registros, asumir días completos del periodo
        if ($diasAsistidos > 0) {
            return $diasAsistidos;
        } else {
            // Si no hay registros de asistencia, asumir días según tipo de periodo
            return $diasPeriodo;
        }
    }
    
    /**
     * Obtener incidencias del empleado en el período
     */
    private function obtenerIncidencias($empleadoId, $periodoId) {
        $stmt = $this->db->prepare("
            SELECT tipo_incidencia, SUM(monto) as total
            FROM incidencias_nomina
            WHERE empleado_id = ? AND periodo_id = ? AND estatus = 'Aprobado'
            GROUP BY tipo_incidencia
        ");
        $stmt->execute([$empleadoId, $periodoId]);
        $incidencias = $stmt->fetchAll();
        
        $resultado = [
            'horas_extra' => 0,
            'bonos' => 0,
            'prestamos' => 0,
            'descuentos' => 0
        ];
        
        foreach ($incidencias as $inc) {
            switch ($inc['tipo_incidencia']) {
                case 'Hora Extra':
                    $resultado['horas_extra'] += $inc['total'];
                    break;
                case 'Bono':
                    $resultado['bonos'] += $inc['total'];
                    break;
                case 'Descuento':
                    $resultado['descuentos'] += $inc['total'];
                    break;
            }
        }
        
        // Obtener descuentos de préstamos
        $stmt = $this->db->prepare("
            SELECT SUM(monto_pago) as total FROM prestamos
            WHERE empleado_id = ? AND estatus = 'Activo'
        ");
        $stmt->execute([$empleadoId]);
        $prestamo = $stmt->fetch();
        if ($prestamo && $prestamo['total']) {
            $resultado['prestamos'] = $prestamo['total'];
        }
        
        return $resultado;
    }
    
    /**
     * Guardar conceptos detallados de nómina
     */
    private function guardarConceptosNomina($nominaDetalleId, $percepciones, $deducciones) {
        // Eliminar conceptos anteriores
        $stmt = $this->db->prepare("DELETE FROM nomina_conceptos WHERE nomina_detalle_id = ?");
        $stmt->execute([$nominaDetalleId]);
        
        // Insertar percepciones
        foreach ($percepciones as $concepto => $monto) {
            if ($monto > 0) {
                $conceptoId = $this->obtenerConceptoId($concepto, 'Percepción');
                if ($conceptoId) {
                    $stmt = $this->db->prepare("
                        INSERT INTO nomina_conceptos (nomina_detalle_id, concepto_id, cantidad, monto)
                        VALUES (?, ?, 1, ?)
                    ");
                    $stmt->execute([$nominaDetalleId, $conceptoId, $monto]);
                }
            }
        }
        
        // Insertar deducciones
        foreach ($deducciones as $concepto => $monto) {
            if ($monto > 0) {
                $conceptoId = $this->obtenerConceptoId($concepto, 'Deducción');
                if ($conceptoId) {
                    $stmt = $this->db->prepare("
                        INSERT INTO nomina_conceptos (nomina_detalle_id, concepto_id, cantidad, monto)
                        VALUES (?, ?, 1, ?)
                    ");
                    $stmt->execute([$nominaDetalleId, $conceptoId, $monto]);
                }
            }
        }
    }
    
    /**
     * Obtener ID de concepto por nombre
     */
    private function obtenerConceptoId($nombre, $tipo) {
        $mapeo = [
            'sueldo_base' => 'P001',
            'horas_extra' => 'P004',
            'bonos' => 'P003',
            'isr' => 'D002',
            'imss' => 'D001',
            'prestamos' => 'D003',
            'otros_descuentos' => 'D004'
        ];
        
        $clave = $mapeo[$nombre] ?? null;
        if (!$clave) return null;
        
        $stmt = $this->db->prepare("SELECT id FROM conceptos_nomina WHERE clave = ?");
        $stmt->execute([$clave]);
        $result = $stmt->fetch();
        
        return $result ? $result['id'] : null;
    }
    
    /**
     * Actualizar totales del período
     */
    private function actualizarTotalesPeriodo($periodoId) {
        $stmt = $this->db->prepare("
            SELECT 
                SUM(total_percepciones) as total_percepciones,
                SUM(total_deducciones) as total_deducciones,
                SUM(total_neto) as total_neto
            FROM nomina_detalle
            WHERE periodo_id = ?
        ");
        $stmt->execute([$periodoId]);
        $totales = $stmt->fetch();
        
        $stmt = $this->db->prepare("
            UPDATE periodos_nomina SET
                total_percepciones = ?,
                total_deducciones = ?,
                total_neto = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $totales['total_percepciones'] ?? 0,
            $totales['total_deducciones'] ?? 0,
            $totales['total_neto'] ?? 0,
            $periodoId
        ]);
    }
}
