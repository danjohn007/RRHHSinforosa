<!-- Dashboard View -->

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Total Empleados -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 font-medium mb-1">Total Empleados</p>
                <p class="text-3xl font-bold text-gray-800"><?php echo $totalEmpleados; ?></p>
                <p class="text-xs text-green-600 mt-2">
                    <i class="fas fa-check-circle"></i> <?php echo $empleadosActivos; ?> activos
                </p>
            </div>
            <div class="h-12 w-12 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-users text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <!-- Nóminas -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 font-medium mb-1">Períodos de Nómina</p>
                <p class="text-3xl font-bold text-gray-800"><?php echo $nominaCount; ?></p>
                <p class="text-xs text-gray-500 mt-2">
                    <i class="fas fa-calendar"></i> Registrados
                </p>
            </div>
            <div class="h-12 w-12 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <!-- Vacaciones Pendientes -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 font-medium mb-1">Solicitudes Pendientes</p>
                <p class="text-3xl font-bold text-gray-800"><?php echo $vacacionesPendientes; ?></p>
                <p class="text-xs text-yellow-600 mt-2">
                    <i class="fas fa-clock"></i> Por aprobar
                </p>
            </div>
            <div class="h-12 w-12 bg-yellow-100 rounded-full flex items-center justify-center">
                <i class="fas fa-umbrella-beach text-yellow-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <!-- Candidatos -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 font-medium mb-1">Candidatos en Proceso</p>
                <p class="text-3xl font-bold text-gray-800"><?php echo $candidatosEnProceso; ?></p>
                <p class="text-xs text-purple-600 mt-2">
                    <i class="fas fa-user-check"></i> Evaluando
                </p>
            </div>
            <div class="h-12 w-12 bg-purple-100 rounded-full flex items-center justify-center">
                <i class="fas fa-user-plus text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Empleados por Departamento -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-chart-pie text-purple-600 mr-2"></i>
            Distribución por Departamento
        </h3>
        <div class="relative" style="height: 300px;">
            <canvas id="departmentChart"></canvas>
        </div>
    </div>
    
    <!-- Asistencia Semanal -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-chart-line text-blue-600 mr-2"></i>
            Asistencia Semanal
        </h3>
        <div class="relative" style="height: 300px;">
            <canvas id="attendanceChart"></canvas>
        </div>
    </div>
</div>

<!-- Cumpleaños y Alertas -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Cumpleaños del Mes -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-birthday-cake text-pink-600 mr-2"></i>
            Cumpleaños del Mes
        </h3>
        <?php if (empty($birthdays)): ?>
            <p class="text-gray-500 text-center py-4">No hay cumpleaños este mes</p>
        <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($birthdays as $birthday): ?>
                <div class="flex items-center justify-between p-3 bg-pink-50 rounded-lg border border-pink-200">
                    <div class="flex items-center space-x-3">
                        <div class="h-10 w-10 bg-pink-200 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-pink-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800"><?php echo htmlspecialchars($birthday['nombre_completo']); ?></p>
                            <p class="text-sm text-gray-600"><?php echo htmlspecialchars($birthday['departamento']); ?></p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-pink-600">
                            <?php echo date('d/m', strtotime($birthday['fecha_nacimiento'])); ?>
                        </p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Accesos Rápidos -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-bolt text-yellow-600 mr-2"></i>
            Accesos Rápidos
        </h3>
        <div class="grid grid-cols-2 gap-3">
            <a href="<?php echo BASE_URL; ?>empleados/crear" class="flex flex-col items-center justify-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition border border-blue-200">
                <i class="fas fa-user-plus text-2xl text-blue-600 mb-2"></i>
                <span class="text-sm font-medium text-gray-700">Nuevo Empleado</span>
            </a>
            
            <a href="<?php echo BASE_URL; ?>nomina/procesar" class="flex flex-col items-center justify-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition border border-green-200">
                <i class="fas fa-calculator text-2xl text-green-600 mb-2"></i>
                <span class="text-sm font-medium text-gray-700">Procesar Nómina</span>
            </a>
            
            <a href="<?php echo BASE_URL; ?>asistencia/registro" class="flex flex-col items-center justify-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition border border-purple-200">
                <i class="fas fa-clock text-2xl text-purple-600 mb-2"></i>
                <span class="text-sm font-medium text-gray-700">Registrar Asistencia</span>
            </a>
            
            <a href="<?php echo BASE_URL; ?>reportes" class="flex flex-col items-center justify-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition border border-orange-200">
                <i class="fas fa-file-alt text-2xl text-orange-600 mb-2"></i>
                <span class="text-sm font-medium text-gray-700">Ver Reportes</span>
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart: Empleados por Departamento
    const deptCtx = document.getElementById('departmentChart').getContext('2d');
    new Chart(deptCtx, {
        type: 'doughnut',
        data: {
            labels: <?php echo $departmentLabels; ?>,
            datasets: [{
                data: <?php echo $departmentData; ?>,
                backgroundColor: [
                    '#667eea', '#764ba2', '#f093fb', '#4facfe',
                    '#43e97b', '#fa709a', '#fee140', '#30cfd0'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12
                        }
                    }
                }
            }
        }
    });
    
    // Chart: Asistencia Semanal (datos de ejemplo)
    const attCtx = document.getElementById('attendanceChart').getContext('2d');
    new Chart(attCtx, {
        type: 'line',
        data: {
            labels: ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            datasets: [{
                label: 'Presente',
                data: [<?php echo $empleadosActivos * 0.95; ?>, <?php echo $empleadosActivos * 0.92; ?>, <?php echo $empleadosActivos * 0.97; ?>, <?php echo $empleadosActivos * 0.90; ?>, <?php echo $empleadosActivos * 0.93; ?>, <?php echo $empleadosActivos * 0.88; ?>],
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Ausente',
                data: [<?php echo $empleadosActivos * 0.05; ?>, <?php echo $empleadosActivos * 0.08; ?>, <?php echo $empleadosActivos * 0.03; ?>, <?php echo $empleadosActivos * 0.10; ?>, <?php echo $empleadosActivos * 0.07; ?>, <?php echo $empleadosActivos * 0.12; ?>],
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
});
</script>
