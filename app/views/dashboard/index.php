<!-- Dashboard View -->

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Total Empleados -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500" data-aos="fade-up" data-aos-delay="0">
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
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500" data-aos="fade-up" data-aos-delay="100">
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
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500" data-aos="fade-up" data-aos-delay="200">
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
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500" data-aos="fade-up" data-aos-delay="300">
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
    <div class="bg-white rounded-lg shadow-md p-6 fade-in" data-aos="fade-right" data-aos-delay="400">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-chart-pie text-purple-600 mr-2"></i>
            Distribución por Departamento
        </h3>
        <div class="relative flex items-center justify-center" style="height: 300px;">
            <canvas id="departmentChart"></canvas>
            <div id="deptChartLoading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-90">
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin text-3xl text-purple-600 mb-2"></i>
                    <p class="text-sm text-gray-500">Cargando gráfica...</p>
                </div>
            </div>
        </div>
        <p class="text-xs text-gray-500 text-center mt-3">Total de empleados por área</p>
    </div>
    
    <!-- Asistencia Semanal -->
    <div class="bg-white rounded-lg shadow-md p-6 fade-in" data-aos="fade-left" data-aos-delay="400">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-chart-line text-blue-600 mr-2"></i>
            Asistencia Semanal (Proyección)
        </h3>
        <div class="relative" style="height: 300px;">
            <canvas id="attendanceChart"></canvas>
            <div id="attChartLoading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-90">
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin text-3xl text-blue-600 mb-2"></i>
                    <p class="text-sm text-gray-500">Cargando gráfica...</p>
                </div>
            </div>
        </div>
        <p class="text-xs text-gray-500 text-center mt-3">Datos estimados de la semana actual</p>
    </div>
</div>

<!-- Cumpleaños y Alertas -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Cumpleaños del Mes -->
    <div class="bg-white rounded-lg shadow-md p-6" data-aos="zoom-in" data-aos-delay="500">
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
    <div class="bg-white rounded-lg shadow-md p-6" data-aos="zoom-in" data-aos-delay="600">
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
    console.log('🚀 Iniciando dashboard...');
    
    // Verificar que Chart.js esté cargado
    if (typeof Chart === 'undefined') {
        console.error('❌ Chart.js no está cargado');
        const deptLoading = document.getElementById('deptChartLoading');
        const attLoading = document.getElementById('attChartLoading');
        if (deptLoading) deptLoading.innerHTML = '<p class="text-red-500 text-sm">Error: Chart.js no cargó</p>';
        if (attLoading) attLoading.innerHTML = '<p class="text-red-500 text-sm">Error: Chart.js no cargó</p>';
        return;
    }
    
    console.log('✅ Chart.js cargado correctamente');
    
    // Chart: Empleados por Departamento
    let deptLabels = <?php echo json_encode($departmentLabels); ?>;
    let deptData = <?php echo json_encode($departmentData); ?>;
    
    console.log('📊 Datos de departamentos:', deptLabels, deptData);
    
    const deptCtx = document.getElementById('departmentChart');
    const deptLoading = document.getElementById('deptChartLoading');
    
    if (deptCtx) {
        const ctx = deptCtx.getContext('2d');
        // Si no hay datos, mostrar mensaje o datos de ejemplo
        const hasData = deptData && deptData.length > 0 && deptData.some(val => val > 0);
        
        try {
            if (!hasData) {
                console.log('No hay datos de departamentos, mostrando placeholder');
                // Mostrar datos de ejemplo cuando no hay datos reales
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Sin datos aún'],
                        datasets: [{
                            data: [1],
                            backgroundColor: ['#e5e7eb'],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    font: { size: 12 },
                                    color: '#9ca3af'
                                }
                            },
                            tooltip: {
                                enabled: false
                            }
                        }
                    }
                });
                
                // Agregar mensaje sobre el canvas
                const parentDiv = deptCtx.parentElement;
                const message = document.createElement('div');
                message.className = 'absolute inset-0 flex items-center justify-center pointer-events-none z-10';
                message.innerHTML = '<p class="text-gray-400 text-sm font-medium">Agrega empleados para ver estadísticas</p>';
                parentDiv.appendChild(message);
            } else {
                console.log('Creando gráfica de departamentos con datos reales');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: deptLabels,
                        datasets: [{
                            data: deptData,
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
                                display: true,
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    font: { size: 12 }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return label + ': ' + value + ' (' + percentage + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            }
            
            // Ocultar loading
            if (deptLoading) deptLoading.style.display = 'none';
            console.log('✅ Gráfica de departamentos creada');
        } catch (error) {
            console.error('❌ Error al crear gráfica de departamentos:', error);
            if (deptLoading) deptLoading.innerHTML = '<p class="text-red-500 text-sm">Error: ' + error.message + '</p>';
        }
    }
    
    // Chart: Asistencia Semanal
    const attCtx = document.getElementById('attendanceChart');
    const attLoading = document.getElementById('attChartLoading');
    
    if (attCtx) {
        const ctx = attCtx.getContext('2d');
        const empleadosActivos = <?php echo (int)$empleadosActivos; ?>;
        
        console.log('Empleados activos:', empleadosActivos);
        
        // Generar datos de ejemplo basados en empleados activos
        const presenteData = [
            Math.round(empleadosActivos * 0.95),
            Math.round(empleadosActivos * 0.92),
            Math.round(empleadosActivos * 0.97),
            Math.round(empleadosActivos * 0.90),
            Math.round(empleadosActivos * 0.93),
            Math.round(empleadosActivos * 0.88)
        ];
        
        const ausenteData = [
            Math.round(empleadosActivos * 0.05),
            Math.round(empleadosActivos * 0.08),
            Math.round(empleadosActivos * 0.03),
            Math.round(empleadosActivos * 0.10),
            Math.round(empleadosActivos * 0.07),
            Math.round(empleadosActivos * 0.12)
        ];
        
        console.log('Datos de asistencia:', presenteData, ausenteData);
        
        try {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
                    datasets: [{
                        label: 'Presente',
                        data: presenteData,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }, {
                        label: 'Ausente',
                        data: ausenteData,
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#ef4444',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 15,
                                font: { size: 12 }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: { size: 14 },
                            bodyFont: { size: 13 }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                stepSize: 1
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
            
            // Ocultar loading
            if (attLoading) attLoading.style.display = 'none';
            console.log('✅ Gráfica de asistencia creada');
        } catch (error) {
            console.error('❌ Error al crear gráfica de asistencia:', error);
            if (attLoading) attLoading.innerHTML = '<p class="text-red-500 text-sm">Error: ' + error.message + '</p>';
        }
    }
    
    console.log('✅ Dashboard inicializado correctamente');
});
</script>
