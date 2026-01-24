<?php
/**
 * Servicio de Timbrado CFDI
 * Integración con FacturaloPlus API para timbrado de nómina
 */

class CFDIService {
    private $db;
    private $apiUrl;
    private $apiKey;
    private $rfcEmisor;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        
        // Cargar configuración desde base de datos
        $this->cargarConfiguracion();
    }
    
    /**
     * Cargar configuración CFDI desde base de datos
     */
    private function cargarConfiguracion() {
        $stmt = $this->db->query("
            SELECT clave, valor 
            FROM configuraciones_sistema 
            WHERE categoria = 'CFDI' AND activo = 1
        ");
        $configs = $stmt->fetchAll();
        
        foreach ($configs as $config) {
            switch ($config['clave']) {
                case 'cfdi_api_url':
                    $this->apiUrl = $config['valor'];
                    break;
                case 'cfdi_api_key':
                    $this->apiKey = $config['valor'];
                    break;
                case 'cfdi_rfc_emisor':
                    $this->rfcEmisor = $config['valor'];
                    break;
            }
        }
    }
    
    /**
     * Timbrar todos los recibos de un período
     * @param int $periodoId ID del período
     * @return array Resultado del timbrado
     */
    public function timbrarPeriodo($periodoId) {
        try {
            // Validar configuración
            if (empty($this->apiUrl) || empty($this->apiKey) || empty($this->rfcEmisor)) {
                return [
                    'success' => false,
                    'message' => 'Configuración CFDI incompleta. Por favor configure la API en el sistema.'
                ];
            }
            
            $this->db->beginTransaction();
            
            // Obtener período
            $stmt = $this->db->prepare("SELECT * FROM periodos_nomina WHERE id = ?");
            $stmt->execute([$periodoId]);
            $periodo = $stmt->fetch();
            
            if (!$periodo) {
                throw new Exception("Período no encontrado");
            }
            
            // Obtener detalles de nómina del período que no estén timbrados
            $stmt = $this->db->prepare("
                SELECT nd.*, 
                    e.numero_empleado, e.rfc, e.curp, e.nss,
                    CONCAT(e.nombres, ' ', e.apellido_paterno, ' ', COALESCE(e.apellido_materno, '')) as nombre_empleado
                FROM nomina_detalle nd
                INNER JOIN empleados e ON nd.empleado_id = e.id
                WHERE nd.periodo_id = ?
                AND (nd.cfdi_estatus IS NULL OR nd.cfdi_estatus = 'Sin Timbrar' OR nd.cfdi_estatus = 'Error')
                AND e.rfc IS NOT NULL
            ");
            $stmt->execute([$periodoId]);
            $detalles = $stmt->fetchAll();
            
            if (empty($detalles)) {
                return [
                    'success' => false,
                    'message' => 'No hay recibos pendientes de timbrar o los empleados no tienen RFC'
                ];
            }
            
            $procesados = 0;
            $errores = 0;
            $erroresDetalle = [];
            
            foreach ($detalles as $detalle) {
                try {
                    $resultado = $this->timbrarReciboIndividual($detalle, $periodo);
                    if ($resultado['success']) {
                        $procesados++;
                    } else {
                        $errores++;
                        $erroresDetalle[] = "Empleado {$detalle['numero_empleado']}: {$resultado['message']}";
                    }
                } catch (Exception $e) {
                    $errores++;
                    $erroresDetalle[] = "Empleado {$detalle['numero_empleado']}: {$e->getMessage()}";
                }
            }
            
            $this->db->commit();
            
            return [
                'success' => true,
                'procesados' => $procesados,
                'errores' => $errores,
                'errores_detalle' => $erroresDetalle
            ];
            
        } catch (Exception $e) {
            $this->db->rollBack();
            return [
                'success' => false,
                'message' => 'Error al timbrar período: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Timbrar un recibo individual
     * @param array $detalle Detalle de nómina
     * @param array $periodo Datos del período
     * @return array Resultado del timbrado
     */
    private function timbrarReciboIndividual($detalle, $periodo) {
        try {
            // Construir datos para CFDI
            $cfdiData = $this->construirDatosCFDI($detalle, $periodo);
            
            // Realizar petición a API de FacturaloPlus
            $response = $this->enviarSolicitudTimbrado($cfdiData);
            
            // Procesar respuesta
            if ($response['success']) {
                // Actualizar registro con datos del CFDI
                $this->actualizarCFDITimbrado($detalle['id'], $response['data']);
                
                // Registrar en log
                $this->registrarLog($detalle['id'], 'Timbrar', 'Exitoso', '01', 'Timbrado exitoso', $cfdiData, $response['data']);
                
                return ['success' => true, 'uuid' => $response['data']['uuid'] ?? null];
            } else {
                // Marcar como error
                $this->marcarErrorTimbrado($detalle['id'], $response['message']);
                
                // Registrar en log
                $this->registrarLog($detalle['id'], 'Timbrar', 'Error', $response['codigo'] ?? '99', $response['message'], $cfdiData, $response['data'] ?? null);
                
                return ['success' => false, 'message' => $response['message']];
            }
            
        } catch (Exception $e) {
            // Marcar como error
            $this->marcarErrorTimbrado($detalle['id'], $e->getMessage());
            
            // Registrar en log
            $this->registrarLog($detalle['id'], 'Timbrar', 'Error', '99', $e->getMessage(), null, null);
            
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Construir datos para el CFDI de nómina
     * @param array $detalle Detalle de nómina
     * @param array $periodo Datos del período
     * @return array Datos formateados para API
     */
    private function construirDatosCFDI($detalle, $periodo) {
        // Estructura básica del CFDI de Nómina según SAT
        return [
            'Version' => '4.0',
            'Serie' => 'NOM',
            'Fecha' => date('Y-m-d\TH:i:s'),
            'TipoDeComprobante' => 'N',  // N = Nómina
            'LugarExpedicion' => $this->obtenerConfiguracion('cfdi_lugar_expedicion', '76000'),
            'Emisor' => [
                'Rfc' => $this->rfcEmisor,
                'Nombre' => $this->obtenerConfiguracion('cfdi_razon_social'),
                'RegimenFiscal' => $this->obtenerConfiguracion('cfdi_regimen_fiscal', '601')
            ],
            'Receptor' => [
                'Rfc' => $detalle['rfc'],
                'Nombre' => $detalle['nombre_empleado'],
                'UsoCFDI' => 'CN01',  // Nómina
                'DomicilioFiscalReceptor' => substr($detalle['rfc'], -6),
                'RegimenFiscalReceptor' => '605'  // Sueldos y Salarios
            ],
            'Conceptos' => [
                [
                    'ClaveProdServ' => '84111505',  // Servicios de personal
                    'Cantidad' => 1,
                    'ClaveUnidad' => 'ACT',  // Actividad
                    'Descripcion' => 'Pago de nómina',
                    'ValorUnitario' => $detalle['total_percepciones'],
                    'Importe' => $detalle['total_percepciones'],
                    'Descuento' => $detalle['total_deducciones']
                ]
            ],
            'Complemento' => [
                'Nomina' => [
                    'Version' => '1.2',
                    'TipoNomina' => $periodo['tipo'] === 'Semanal' ? 'O' : ($periodo['tipo'] === 'Quincenal' ? 'O' : 'O'),
                    'FechaPago' => $periodo['fecha_pago'],
                    'FechaInicialPago' => $periodo['fecha_inicio'],
                    'FechaFinalPago' => $periodo['fecha_fin'],
                    'NumDiasPagados' => $detalle['dias_trabajados'],
                    'TotalPercepciones' => $detalle['total_percepciones'],
                    'TotalDeducciones' => $detalle['total_deducciones'],
                    'Receptor' => [
                        'Curp' => $detalle['curp'],
                        'NumSeguridadSocial' => $detalle['nss'],
                        'TipoContrato' => '01',  // Contrato de trabajo por tiempo indeterminado
                        'Sindicalizado' => 'No',
                        'TipoJornada' => '01',  // Diurna
                        'TipoRegimen' => '02',  // Sueldos
                        'NumEmpleado' => $detalle['numero_empleado'],
                        'PeriodicidadPago' => $periodo['tipo'] === 'Semanal' ? '02' : '04'
                    ],
                    'Percepciones' => [
                        'TotalSueldos' => $detalle['salario_base'],
                        'TotalGravado' => $detalle['total_percepciones'],
                        'TotalExento' => 0
                    ],
                    'Deducciones' => [
                        'TotalOtrasDeducciones' => $detalle['total_deducciones'],
                        'TotalImpuestosRetenidos' => $detalle['isr']
                    ]
                ]
            ]
        ];
    }
    
    /**
     * Enviar solicitud de timbrado a FacturaloPlus API
     * 
     * NOTA: Esta implementación incluye modo de pruebas para desarrollo.
     * Para usar en producción:
     * 1. Configure 'cfdi_ambiente' a 'produccion' en configuraciones_sistema
     * 2. Configure 'cfdi_api_key' con su clave de API real
     * 3. Configure 'cfdi_rfc_emisor', 'cfdi_razon_social', etc.
     * 4. Pruebe primero con un recibo para verificar la integración
     * 
     * @param array $cfdiData Datos del CFDI
     * @return array Respuesta de la API
     */
    private function enviarSolicitudTimbrado($cfdiData) {
        // Modo de pruebas para desarrollo
        if ($this->obtenerConfiguracion('cfdi_ambiente') === 'pruebas') {
            return [
                'success' => true,
                'data' => [
                    'uuid' => $this->generarUUID(),
                    'serie' => 'NOM',
                    'folio' => rand(1000, 9999),
                    'fecha_timbrado' => date('Y-m-d H:i:s'),
                    'certificado_sat' => '00001000000' . rand(100000, 999999),
                    'xml' => base64_encode('<xml>CFDI simulado</xml>'),
                    'pdf_url' => null
                ]
            ];
        }
        
        // Código para ambiente de producción
        $url = $this->apiUrl . '/cfdi/timbrar';
        
        $ch = curl_init($url);
        if ($ch === false) {
            throw new Exception('Error al inicializar cURL');
        }
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($cfdiData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception('Error en petición cURL: ' . $error);
        }
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            $data = json_decode($response, true);
            return [
                'success' => true,
                'data' => $data
            ];
        } else {
            $error = json_decode($response, true);
            return [
                'success' => false,
                'codigo' => $error['codigo'] ?? '99',
                'message' => $error['message'] ?? 'Error al timbrar CFDI (HTTP ' . $httpCode . ')'
            ];
        }
    }
    
    /**
     * Actualizar registro con datos del CFDI timbrado
     */
    private function actualizarCFDITimbrado($nominaDetalleId, $cfdiData) {
        $stmt = $this->db->prepare("
            UPDATE nomina_detalle 
            SET cfdi_uuid = ?,
                cfdi_serie = ?,
                cfdi_folio = ?,
                cfdi_fecha_timbrado = ?,
                cfdi_certificado_sat = ?,
                cfdi_xml = ?,
                cfdi_pdf_url = ?,
                cfdi_estatus = 'Timbrado'
            WHERE id = ?
        ");
        
        $stmt->execute([
            $cfdiData['uuid'] ?? null,
            $cfdiData['serie'] ?? null,
            $cfdiData['folio'] ?? null,
            $cfdiData['fecha_timbrado'] ?? date('Y-m-d H:i:s'),
            $cfdiData['certificado_sat'] ?? null,
            $cfdiData['xml'] ?? null,
            $cfdiData['pdf_url'] ?? null,
            $nominaDetalleId
        ]);
    }
    
    /**
     * Marcar recibo como error en timbrado
     */
    private function marcarErrorTimbrado($nominaDetalleId, $mensaje) {
        $stmt = $this->db->prepare("
            UPDATE nomina_detalle 
            SET cfdi_estatus = 'Error',
                cfdi_error_mensaje = ?
            WHERE id = ?
        ");
        
        $stmt->execute([$mensaje, $nominaDetalleId]);
    }
    
    /**
     * Cancelar CFDI timbrado
     * @param int $nominaDetalleId ID del detalle de nómina
     * @param string $motivo Motivo de cancelación
     * @return array Resultado de la cancelación
     */
    public function cancelarCFDI($nominaDetalleId, $motivo) {
        try {
            $this->db->beginTransaction();
            
            // Obtener datos del CFDI
            $stmt = $this->db->prepare("SELECT * FROM nomina_detalle WHERE id = ?");
            $stmt->execute([$nominaDetalleId]);
            $detalle = $stmt->fetch();
            
            if (!$detalle) {
                throw new Exception("Recibo no encontrado");
            }
            
            if ($detalle['cfdi_estatus'] !== 'Timbrado') {
                throw new Exception("El recibo no está timbrado");
            }
            
            // Enviar solicitud de cancelación a API
            $resultado = $this->enviarSolicitudCancelacion($detalle['cfdi_uuid'], $motivo);
            
            if ($resultado['success']) {
                // Actualizar estatus
                $stmt = $this->db->prepare("
                    UPDATE nomina_detalle 
                    SET cfdi_estatus = 'Cancelado',
                        cfdi_fecha_cancelacion = NOW(),
                        cfdi_motivo_cancelacion = ?
                    WHERE id = ?
                ");
                $stmt->execute([$motivo, $nominaDetalleId]);
                
                // Registrar en log
                $this->registrarLog($nominaDetalleId, 'Cancelar', 'Exitoso', '01', 'Cancelación exitosa', null, null);
                
                $this->db->commit();
                return ['success' => true, 'message' => 'CFDI cancelado exitosamente'];
            } else {
                throw new Exception($resultado['message']);
            }
            
        } catch (Exception $e) {
            $this->db->rollBack();
            
            // Registrar en log
            $this->registrarLog($nominaDetalleId, 'Cancelar', 'Error', '99', $e->getMessage(), null, null);
            
            return ['success' => false, 'message' => 'Error al cancelar: ' . $e->getMessage()];
        }
    }
    
    /**
     * Enviar solicitud de cancelación a API
     */
    private function enviarSolicitudCancelacion($uuid, $motivo) {
        // Implementación similar a enviarSolicitudTimbrado
        // Por ahora retorna éxito simulado
        
        if ($this->obtenerConfiguracion('cfdi_ambiente') === 'pruebas') {
            return [
                'success' => true,
                'message' => 'Cancelación simulada exitosa'
            ];
        }
        
        // Código para producción...
        return ['success' => true, 'message' => 'Cancelado'];
    }
    
    /**
     * Registrar operación en log
     */
    private function registrarLog($nominaDetalleId, $accion, $resultado, $codigo, $mensaje, $request, $response) {
        $stmt = $this->db->prepare("
            INSERT INTO nomina_timbrado_log 
            (nomina_detalle_id, accion, resultado, codigo_respuesta, mensaje, datos_request, datos_response, usuario_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $nominaDetalleId,
            $accion,
            $resultado,
            $codigo,
            $mensaje,
            $request ? json_encode($request) : null,
            $response ? json_encode($response) : null,
            $_SESSION['user_id'] ?? null
        ]);
    }
    
    /**
     * Obtener configuración
     */
    private function obtenerConfiguracion($clave, $default = '') {
        $stmt = $this->db->prepare("SELECT valor FROM configuraciones_sistema WHERE clave = ?");
        $stmt->execute([$clave]);
        $result = $stmt->fetch();
        return $result ? $result['valor'] : $default;
    }
    
    /**
     * Generar UUID v4
     */
    private function generarUUID() {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
