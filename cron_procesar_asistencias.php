<?php
/**
 * Script para procesar asistencias pendientes con auto-corte
 * Ejecutar diariamente (preferiblemente a las 23:59 o 00:05)
 * 
 * Este script:
 * 1. Busca empleados que registraron entrada pero no salida en días anteriores
 * 2. Les asigna automáticamente la hora de salida según el horario de la sucursal
 * 3. Calcula las horas trabajadas y horas extras
 * 4. Cambia el estatus a "Por Validar" para revisión manual
 */

// Cargar configuración
require_once __DIR__ . '/config/config.php';

echo "========================================\n";
echo "Auto-Corte de Asistencias Pendientes\n";
echo "Fecha: " . date('Y-m-d H:i:s') . "\n";
echo "========================================\n\n";

try {
    $db = Database::getInstance()->getConnection();
    
    // Ejecutar el procedimiento almacenado de auto-corte
    echo "Ejecutando procedimiento de auto-corte...\n\n";
    
    $stmt = $db->query("CALL auto_cortar_asistencias()");
    $resultado = $stmt->fetch();
    
    if ($resultado && isset($resultado['registros_actualizados'])) {
        $registrosActualizados = $resultado['registros_actualizados'];
        echo "✓ Procedimiento ejecutado exitosamente\n";
        echo "  Registros procesados: $registrosActualizados\n";
        
        if ($registrosActualizados > 0) {
            echo "\nDetalles de registros procesados:\n";
            
            // Obtener detalles de los registros auto-cortados hoy
            $stmtDetalles = $db->prepare("
                SELECT 
                    e.numero_empleado,
                    CONCAT(e.nombres, ' ', e.apellido_paterno) as nombre_empleado,
                    s.nombre as sucursal,
                    a.fecha,
                    TIME(a.hora_entrada) as entrada,
                    TIME(a.hora_salida) as salida,
                    a.horas_trabajadas,
                    a.horas_extra
                FROM asistencias a
                INNER JOIN empleados e ON a.empleado_id = e.id
                LEFT JOIN sucursales s ON a.sucursal_id = s.id
                WHERE a.auto_cortado = 1
                AND a.estatus = 'Por Validar'
                AND a.fecha >= DATE_SUB(CURDATE(), INTERVAL 7 DAYS)
                ORDER BY a.fecha DESC, e.numero_empleado
                LIMIT 50
            ");
            $stmtDetalles->execute();
            $detalles = $stmtDetalles->fetchAll();
            
            foreach ($detalles as $detalle) {
                echo "\n  • {$detalle['numero_empleado']} - {$detalle['nombre_empleado']}\n";
                echo "    Sucursal: {$detalle['sucursal']}\n";
                echo "    Fecha: {$detalle['fecha']}\n";
                echo "    Entrada: {$detalle['entrada']} → Salida: {$detalle['salida']} (auto-cortado)\n";
                echo "    Horas trabajadas: {$detalle['horas_trabajadas']} hrs";
                if ($detalle['horas_extra'] > 0) {
                    echo " (+" . $detalle['horas_extra'] . " hrs extra)";
                }
                echo "\n";
            }
        }
    } else {
        echo "✓ Procedimiento ejecutado - No hay registros para procesar\n";
    }
    
    echo "\n========================================\n";
    echo "Proceso completado exitosamente\n";
    echo "========================================\n";
    
} catch (Exception $e) {
    echo "\n✗ ERROR FATAL: {$e->getMessage()}\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
