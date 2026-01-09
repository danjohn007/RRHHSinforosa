<?php
/**
 * Controlador de Notificaciones
 */
class NotificacionesController {
    
    public function index() {
        // En producción, estas notificaciones vendrían de la base de datos
        $notificaciones = [
            [
                'id' => 1,
                'tipo' => 'solicitud',
                'titulo' => 'Nueva solicitud de vacaciones',
                'mensaje' => 'Juan Pérez ha solicitado vacaciones del 15 al 20 de enero',
                'fecha' => '2026-01-09 10:30:00',
                'leida' => false,
                'icono' => 'fa-umbrella-beach',
                'color' => 'blue'
            ],
            [
                'id' => 2,
                'tipo' => 'nomina',
                'titulo' => 'Nómina procesada',
                'mensaje' => 'La nómina quincenal ha sido procesada correctamente',
                'fecha' => '2026-01-09 09:15:00',
                'leida' => false,
                'icono' => 'fa-money-bill-wave',
                'color' => 'green'
            ],
            [
                'id' => 3,
                'tipo' => 'empleado',
                'titulo' => 'Nuevo empleado registrado',
                'mensaje' => 'María González ha sido agregada al sistema',
                'fecha' => '2026-01-08 16:45:00',
                'leida' => false,
                'icono' => 'fa-user-plus',
                'color' => 'purple'
            ],
            [
                'id' => 4,
                'tipo' => 'asistencia',
                'titulo' => 'Retraso registrado',
                'mensaje' => 'Carlos Martínez llegó 15 minutos tarde hoy',
                'fecha' => '2026-01-08 08:45:00',
                'leida' => true,
                'icono' => 'fa-clock',
                'color' => 'orange'
            ],
            [
                'id' => 5,
                'tipo' => 'sistema',
                'titulo' => 'Respaldo completado',
                'mensaje' => 'El respaldo automático de la base de datos se completó exitosamente',
                'fecha' => '2026-01-07 23:00:00',
                'leida' => true,
                'icono' => 'fa-database',
                'color' => 'gray'
            ]
        ];
        
        view('notificaciones/index', [
            'notificaciones' => $notificaciones
        ]);
    }
}
