<?php
/**
 * Script para procesar asistencias pendientes
 * Ejecutar diariamente (preferiblemente a las 23:59)
 * 
 * Este script:
 * 1. Busca empleados que registraron entrada pero no salida en el día actual
 * 2. Les asigna automáticamente sus horas normales de trabajo según turno
 * 3. Marca la hora de salida esperada según su turno
 */

// Cargar configuración
require_once __DIR__ . '/config/config.php';

echo "========================================\n";
echo "Procesamiento de Asistencias Pendientes\n";
echo "Fecha: " . date('Y-m-d H:i:s') . "\n";
echo "========================================\n\n";

try {
    $db = Database::getInstance()->getConnection();
    
    // Obtener fecha del día anterior (para procesar asistencias del día que terminó)
    $fechaProcesar = date('Y-m-d', strtotime('-1 day'));
    
    echo "Procesando asistencias del: $fechaProcesar\n\n";
    
    // Buscar asistencias con entrada pero sin salida
    $stmt = $db->prepare("
        SELECT a.id, a.empleado_id, a.hora_entrada, a.fecha,
               e.numero_empleado, 
               CONCAT(e.nombres, ' ', e.apellido_paterno) as nombre_empleado,
               t.hora_salida, t.horas_laborales
        FROM asistencias a
        INNER JOIN empleados e ON a.empleado_id = e.id
        LEFT JOIN turnos t ON e.turno_id = t.id
        WHERE a.fecha = ?
        AND a.hora_entrada IS NOT NULL
        AND a.hora_salida IS NULL
        AND e.estatus = 'Activo'
    ");
    $stmt->execute([$fechaProcesar]);
    $asistenciasPendientes = $stmt->fetchAll();
    
    if (empty($asistenciasPendientes)) {
        echo "No hay asistencias pendientes para procesar.\n";
        exit(0);
    }
    
    echo "Asistencias pendientes encontradas: " . count($asistenciasPendientes) . "\n\n";
    
    $procesadas = 0;
    $errores = 0;
    
    foreach ($asistenciasPendientes as $asistencia) {
        echo "Procesando: {$asistencia['numero_empleado']} - {$asistencia['nombre_empleado']}\n";
        
        try {
            // Calcular hora de salida esperada
            if (!empty($asistencia['hora_salida'])) {
                // Usar hora de salida del turno
                $horaSalidaEsperada = $asistencia['fecha'] . ' ' . $asistencia['hora_salida'];
            } else {
                // Si no tiene turno asignado, usar 8 horas después de la entrada
                $horaEntrada = new DateTime($asistencia['hora_entrada']);
                $horaEntrada->modify('+8 hours');
                $horaSalidaEsperada = $horaEntrada->format('Y-m-d H:i:s');
            }
            
            // Usar horas normales del turno, o 8 horas por defecto
            $horasNormales = $asistencia['horas_laborales'] ?? 8.0;
            
            // Actualizar asistencia
            $stmtUpdate = $db->prepare("
                UPDATE asistencias SET
                    hora_salida = ?,
                    horas_trabajadas = ?,
                    horas_extra = 0,
                    notas = CONCAT(
                        IFNULL(notas, ''), 
                        IF(notas IS NOT NULL AND notas != '', '\n', ''),
                        'Salida auto-asignada por sistema el ', ?, ' - Horas normales aplicadas'
                    )
                WHERE id = ?
            ");
            
            $stmtUpdate->execute([
                $horaSalidaEsperada,
                $horasNormales,
                date('Y-m-d H:i:s'),
                $asistencia['id']
            ]);
            
            echo "  ✓ Procesado: Salida asignada a {$horaSalidaEsperada}, {$horasNormales} horas trabajadas\n";
            $procesadas++;
            
        } catch (Exception $e) {
            echo "  ✗ Error: {$e->getMessage()}\n";
            $errores++;
        }
    }
    
    echo "\n========================================\n";
    echo "Resumen:\n";
    echo "  Total encontradas: " . count($asistenciasPendientes) . "\n";
    echo "  Procesadas exitosamente: $procesadas\n";
    echo "  Errores: $errores\n";
    echo "========================================\n";
    
} catch (Exception $e) {
    echo "\n✗ ERROR FATAL: {$e->getMessage()}\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
