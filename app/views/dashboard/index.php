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

<!-- Stats Cards Grid - Nómina y Horas Extras -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Nómina Acumulada Card -->
    <a href="<?php echo BASE_URL; ?>nomina/procesar" class="block bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg shadow-lg p-6 text-white hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="flex items-center justify-between mb-4">
            <div class="h-12 w-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                <i class="fas fa-calculator text-2xl"></i>
            </div>
            <div class="h-8 w-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                <i class="fas fa-arrow-right text-sm"></i>
            </div>
        </div>
        <h3 class="text-sm font-semibold mb-2 flex items-center">
            <i class="fas fa-money-bill-wave mr-2"></i>
            Nómina Acumulada desde Último Corte
        </h3>
        <p class="text-3xl font-bold mb-2">$<?php echo number_format($nominaAcumulada, 2); ?></p>
        <p class="text-xs opacity-90">
            <i class="fas fa-info-circle mr-1"></i>
            Total de nóminas procesadas y pagadas
        </p>
        <!-- Mini Chart -->
        <div class="mt-4" style="height: 80px;">
            <canvas id="miniNominaChart"></canvas>
        </div>
    </a>
    
    <!-- Horas Extras Acumuladas Card -->
    <a href="<?php echo BASE_URL; ?>asistencia/incidencias?tipo=Hora%20Extra" class="block bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg shadow-lg p-6 text-white hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="flex items-center justify-between mb-4">
            <div class="h-12 w-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                <i class="fas fa-clock text-2xl"></i>
            </div>
            <div class="h-8 w-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                <i class="fas fa-arrow-right text-sm"></i>
            </div>
        </div>
        <h3 class="text-sm font-semibold mb-2 flex items-center">
            <i class="fas fa-business-time mr-2"></i>
            Horas Extras Acumuladas desde Último Corte
        </h3>
        <p class="text-3xl font-bold mb-2"><?php echo number_format($horasExtrasAcumuladas, 2); ?> hrs</p>
        <p class="text-xs opacity-90">
            <i class="fas fa-info-circle mr-1"></i>
            Total de horas extras aprobadas y procesadas
        </p>
        <!-- Mini Chart -->
        <div class="mt-4" style="height: 80px;">
            <canvas id="miniHorasChart"></canvas>
        </div>
    </a>
    
    <!-- Costo Horas Extras Card -->
    <a href="<?php echo BASE_URL; ?>asistencia/incidencias?tipo=Hora%20Extra" class="block bg-gradient-to-r from-purple-500 to-pink-600 rounded-lg shadow-lg p-6 text-white hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="flex items-center justify-between mb-4">
            <div class="h-12 w-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                <i class="fas fa-dollar-sign text-2xl"></i>
            </div>
            <div class="h-8 w-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                <i class="fas fa-arrow-right text-sm"></i>
            </div>
        </div>
        <h3 class="text-sm font-semibold mb-2 flex items-center">
            <i class="fas fa-hand-holding-usd mr-2"></i>
            Costo de Horas Extras Acumuladas
        </h3>
        <p class="text-3xl font-bold mb-2">$<?php echo number_format($costoHorasExtras, 2); ?></p>
        <p class="text-xs opacity-90">
            <i class="fas fa-info-circle mr-1"></i>
            Costo total de horas extras en el periodo
        </p>
        <!-- Mini Chart -->
        <div class="mt-4" style="height: 80px;">
            <canvas id="miniCostoChart"></canvas>
        </div>
    </a>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Empleados por Departamento -->
    <div class="bg-white rounded-lg shadow-md p-6 fade-in">
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
    <div class="bg-white rounded-lg shadow-md p-6 fade-in">
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

<!-- New Charts Row 1 -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Distribución por Género -->
    <div class="bg-white rounded-lg shadow-md p-6 fade-in">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-venus-mars text-pink-600 mr-2"></i>
            Distribución por Género
        </h3>
        <div class="relative flex items-center justify-center" style="height: 300px;">
            <canvas id="genderChart"></canvas>
        </div>
        <p class="text-xs text-gray-500 text-center mt-3">Empleados activos por género</p>
    </div>
    
    <!-- Contrataciones por Mes -->
    <div class="bg-white rounded-lg shadow-md p-6 fade-in">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-user-plus text-indigo-600 mr-2"></i>
            Contrataciones Mensuales
        </h3>
        <div class="relative" style="height: 300px;">
            <canvas id="hiringChart"></canvas>
        </div>
        <p class="text-xs text-gray-500 text-center mt-3">Nuevas contrataciones últimos 6 meses</p>
    </div>
</div>

<!-- New Charts Row 2 -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Resumen de Asistencias -->
    <div class="bg-white rounded-lg shadow-md p-6 fade-in">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-clipboard-check text-teal-600 mr-2"></i>
            Resumen de Asistencias
        </h3>
        <div class="relative" style="height: 300px;">
            <canvas id="incidenciasChart"></canvas>
        </div>
        <p class="text-xs text-gray-500 text-center mt-3">Incidencias del último mes</p>
    </div>
    
    <!-- Distribución Salarial -->
    <div class="bg-white rounded-lg shadow-md p-6 fade-in">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-dollar-sign text-green-600 mr-2"></i>
            Distribución Salarial
        </h3>
        <div class="relative" style="height: 300px;">
            <canvas id="salaryChart"></canvas>
        </div>
        <p class="text-xs text-gray-500 text-center mt-3">Rangos de salario mensual</p>
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
    
    // === NUEVAS GRÁFICAS ===
    
    // Chart: Distribución por Género
    const genderCtx = document.getElementById('genderChart');
    if (genderCtx) {
        const genderLabels = <?php echo json_encode($genderLabels); ?>;
        const genderCounts = <?php echo json_encode($genderCounts); ?>;
        
        console.log('Datos de género:', genderLabels, genderCounts);
        
        try {
            if (genderLabels.length > 0 && genderCounts.some(val => val > 0)) {
                new Chart(genderCtx, {
                    type: 'doughnut',
                    data: {
                        labels: genderLabels,
                        datasets: [{
                            data: genderCounts,
                            backgroundColor: ['#3b82f6', '#ec4899', '#8b5cf6'],
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
                console.log('✅ Gráfica de género creada');
            } else {
                console.log('No hay datos de género');
            }
        } catch (error) {
            console.error('❌ Error al crear gráfica de género:', error);
        }
    }
    
    // Chart: Contrataciones Mensuales
    const hiringCtx = document.getElementById('hiringChart');
    if (hiringCtx) {
        const hiringLabels = <?php echo json_encode($hiringLabels); ?>;
        const hiringCounts = <?php echo json_encode($hiringCounts); ?>;
        
        console.log('Datos de contrataciones:', hiringLabels, hiringCounts);
        
        try {
            new Chart(hiringCtx, {
                type: 'line',
                data: {
                    labels: hiringLabels.length > 0 ? hiringLabels : ['Sin datos'],
                    datasets: [{
                        label: 'Contrataciones',
                        data: hiringCounts.length > 0 ? hiringCounts : [0],
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointBackgroundColor: '#6366f1',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
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
            console.log('✅ Gráfica de contrataciones creada');
        } catch (error) {
            console.error('❌ Error al crear gráfica de contrataciones:', error);
        }
    }
    
    // Chart: Resumen de Asistencias
    const incidenciasCtx = document.getElementById('incidenciasChart');
    if (incidenciasCtx) {
        const incidenciasLabels = <?php echo json_encode($incidenciasLabels); ?>;
        const incidenciasCounts = <?php echo json_encode($incidenciasCounts); ?>;
        
        console.log('Datos de incidencias:', incidenciasLabels, incidenciasCounts);
        
        try {
            // Colores según el tipo de incidencia
            const incidenciasColors = incidenciasLabels.map(label => {
                switch(label) {
                    case 'Presente': return '#10b981';
                    case 'Retardo': return '#f59e0b';
                    case 'Falta': return '#ef4444';
                    case 'Permiso': return '#3b82f6';
                    case 'Vacaciones': return '#8b5cf6';
                    case 'Incapacidad': return '#ec4899';
                    default: return '#6b7280';
                }
            });
            
            new Chart(incidenciasCtx, {
                type: 'bar',
                data: {
                    labels: incidenciasLabels.length > 0 ? incidenciasLabels : ['Sin datos'],
                    datasets: [{
                        label: 'Incidencias',
                        data: incidenciasCounts.length > 0 ? incidenciasCounts : [0],
                        backgroundColor: incidenciasColors,
                        borderWidth: 0,
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
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
                                precision: 0
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
            console.log('✅ Gráfica de incidencias creada');
        } catch (error) {
            console.error('❌ Error al crear gráfica de incidencias:', error);
        }
    }
    
    // Chart: Distribución Salarial
    const salaryCtx = document.getElementById('salaryChart');
    if (salaryCtx) {
        const salaryLabels = <?php echo json_encode($salaryLabels); ?>;
        const salaryCounts = <?php echo json_encode($salaryCounts); ?>;
        
        console.log('Datos de salarios:', salaryLabels, salaryCounts);
        
        try {
            new Chart(salaryCtx, {
                type: 'bar',
                data: {
                    labels: salaryLabels.length > 0 ? salaryLabels : ['Sin datos'],
                    datasets: [{
                        label: 'Empleados',
                        data: salaryCounts.length > 0 ? salaryCounts : [0],
                        backgroundColor: '#10b981',
                        borderWidth: 0,
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
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
            console.log('✅ Gráfica de salarios creada');
        } catch (error) {
            console.error('❌ Error al crear gráfica de salarios:', error);
        }
    }
    
    // Mini Chart: Nómina Acumulada (últimos 3 meses)
    const miniNominaCtx = document.getElementById('miniNominaChart');
    if (miniNominaCtx) {
        try {
            new Chart(miniNominaCtx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($historicoNominaLabels); ?>,
                    datasets: [{
                        data: <?php echo json_encode($historicoNominaCounts); ?>,
                        borderColor: 'rgba(255, 255, 255, 0.8)',
                        backgroundColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 3,
                        pointBackgroundColor: 'rgba(255, 255, 255, 1)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return '$' + context.parsed.y.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            display: false,
                            beginAtZero: true
                        },
                        x: {
                            display: false
                        }
                    }
                }
            });
            console.log('✅ Mini gráfica de nómina creada');
        } catch (error) {
            console.error('❌ Error al crear mini gráfica de nómina:', error);
        }
    }
    
    // Mini Chart: Horas Extras (últimos 3 meses)
    const miniHorasCtx = document.getElementById('miniHorasChart');
    if (miniHorasCtx) {
        try {
            new Chart(miniHorasCtx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($historicoHorasLabels); ?>,
                    datasets: [{
                        data: <?php echo json_encode($historicoHorasCounts); ?>,
                        borderColor: 'rgba(255, 255, 255, 0.8)',
                        backgroundColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 3,
                        pointBackgroundColor: 'rgba(255, 255, 255, 1)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y.toFixed(2) + ' hrs';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            display: false,
                            beginAtZero: true
                        },
                        x: {
                            display: false
                        }
                    }
                }
            });
            console.log('✅ Mini gráfica de horas extras creada');
        } catch (error) {
            console.error('❌ Error al crear mini gráfica de horas extras:', error);
        }
    }
    
    // Mini Chart: Costo Horas Extras (últimos 3 meses)
    const miniCostoCtx = document.getElementById('miniCostoChart');
    if (miniCostoCtx) {
        try {
            new Chart(miniCostoCtx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($historicoCostoLabels); ?>,
                    datasets: [{
                        data: <?php echo json_encode($historicoCostoCounts); ?>,
                        borderColor: 'rgba(255, 255, 255, 0.8)',
                        backgroundColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 3,
                        pointBackgroundColor: 'rgba(255, 255, 255, 1)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return '$' + context.parsed.y.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            display: false,
                            beginAtZero: true
                        },
                        x: {
                            display: false
                        }
                    }
                }
            });
            console.log('✅ Mini gráfica de costo extras creada');
        } catch (error) {
            console.error('❌ Error al crear mini gráfica de costo extras:', error);
        }
    }
});
</script>
