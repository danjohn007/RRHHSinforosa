<!-- Vista de Centro de Reportes -->

<div class="mb-6" data-aos="fade-down">
    <h1 class="text-2xl font-bold text-gray-800">Centro de Reportes y Análisis</h1>
    <p class="text-gray-600 mt-1">Genera y consulta reportes del sistema de RRHH</p>
</div>

<!-- Categorías de Reportes -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    
    <!-- Reportes de Personal -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden" data-aos="fade-up" data-aos-delay="0">
        <div class="bg-gradient-sinforosa p-4">
            <i class="fas fa-users text-white text-3xl mb-2"></i>
            <h3 class="text-white text-xl font-semibold">Reportes de Personal</h3>
        </div>
        <div class="p-4">
            <ul class="space-y-2">
                <li>
                    <a href="<?php echo BASE_URL; ?>reportes/personal" class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                        <span class="text-gray-700">Listado de Empleados</span>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" onclick="generarReporte('Antigüedad Laboral')" class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                        <span class="text-gray-700">Antigüedad Laboral</span>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" onclick="generarReporte('Por Departamento')" class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                        <span class="text-gray-700">Por Departamento</span>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" onclick="generarReporte('Bajas y Finiquitos')" class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                        <span class="text-gray-700">Bajas y Finiquitos</span>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Reportes de Nómina -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden" data-aos="fade-up" data-aos-delay="100">
        <div class="bg-green-600 p-4">
            <i class="fas fa-money-bill-wave text-white text-3xl mb-2"></i>
            <h3 class="text-white text-xl font-semibold">Reportes de Nómina</h3>
        </div>
        <div class="p-4">
            <ul class="space-y-2">
                <li>
                    <a href="<?php echo BASE_URL; ?>reportes/nomina" class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                        <span class="text-gray-700">Resumen de Nómina</span>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" onclick="generarReporte('Costos Laborales')" class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                        <span class="text-gray-700">Costos Laborales</span>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" onclick="generarReporte('Percepciones y Deducciones')" class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                        <span class="text-gray-700">Percepciones y Deducciones</span>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" onclick="generarReporte('Dispersión Bancaria')" class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                        <span class="text-gray-700">Dispersión Bancaria</span>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Reportes de Asistencia -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-blue-600 p-4">
            <i class="fas fa-clock text-white text-3xl mb-2"></i>
            <h3 class="text-white text-xl font-semibold">Reportes de Asistencia</h3>
        </div>
        <div class="p-4">
            <ul class="space-y-2">
                <li>
                    <a href="javascript:void(0)" onclick="generarReporte('Asistencia Diaria')" class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                        <span class="text-gray-700">Asistencia Diaria</span>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" onclick="generarReporte('Retardos y Faltas')" class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                        <span class="text-gray-700">Retardos y Faltas</span>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" onclick="generarReporte('Horas Extra')" class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                        <span class="text-gray-700">Horas Extra</span>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>reportes/vacaciones" class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                        <span class="text-gray-700">Vacaciones</span>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Reportes de Reclutamiento -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-purple-600 p-4">
            <i class="fas fa-user-plus text-white text-3xl mb-2"></i>
            <h3 class="text-white text-xl font-semibold">Reportes de Reclutamiento</h3>
        </div>
        <div class="p-4">
            <ul class="space-y-2">
                <li>
                    <a href="javascript:void(0)" onclick="generarReporte('Candidatos por Estatus')" class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                        <span class="text-gray-700">Candidatos por Estatus</span>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" onclick="generarReporte('Vacantes Activas')" class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                        <span class="text-gray-700">Vacantes Activas</span>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" onclick="generarReporte('Fuentes de Reclutamiento')" class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                        <span class="text-gray-700">Fuentes de Reclutamiento</span>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" onclick="generarReporte('Tiempo de Contratación')" class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                        <span class="text-gray-700">Tiempo de Contratación</span>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Reportes de Beneficios -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-yellow-500 p-4">
            <i class="fas fa-gift text-white text-3xl mb-2"></i>
            <h3 class="text-white text-xl font-semibold">Reportes de Beneficios</h3>
        </div>
        <div class="p-4">
            <ul class="space-y-2">
                <li>
                    <a href="javascript:void(0)" onclick="generarReporte('Préstamos Activos')" class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                        <span class="text-gray-700">Préstamos Activos</span>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" onclick="generarReporte('Bonos Otorgados')" class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                        <span class="text-gray-700">Bonos Otorgados</span>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" onclick="generarReporte('Descuentos Aplicados')" class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                        <span class="text-gray-700">Descuentos Aplicados</span>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" onclick="generarReporte('Apoyos Especiales')" class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                        <span class="text-gray-700">Apoyos Especiales</span>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Reportes Ejecutivos -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-red-600 p-4">
            <i class="fas fa-chart-line text-white text-3xl mb-2"></i>
            <h3 class="text-white text-xl font-semibold">Reportes Ejecutivos</h3>
        </div>
        <div class="p-4">
            <ul class="space-y-2">
                <li>
                    <a href="javascript:void(0)" onclick="generarReporte('Dashboard Ejecutivo')" class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                        <span class="text-gray-700">Dashboard Ejecutivo</span>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" onclick="generarReporte('Indicadores KPI')" class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                        <span class="text-gray-700">Indicadores KPI</span>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" onclick="generarReporte('Rotación de Personal')" class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                        <span class="text-gray-700">Rotación de Personal</span>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" onclick="generarReporte('Análisis de Costos')" class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                        <span class="text-gray-700">Análisis de Costos</span>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>

</div>

<!-- Exportación -->
<div class="mt-8 bg-white rounded-lg shadow-md p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">
        <i class="fas fa-download text-blue-600 mr-2"></i>
        Exportar Reportes
    </h3>
    <p class="text-gray-600 mb-4">Descarga reportes en diferentes formatos</p>
    <div class="flex flex-wrap gap-3">
        <button onclick="exportarReporteFormato('PDF')" class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
            <i class="fas fa-file-pdf mr-2"></i>PDF
        </button>
        <button onclick="exportarReporteFormato('Excel')" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
            <i class="fas fa-file-excel mr-2"></i>Excel
        </button>
        <button onclick="exportarReporteFormato('CSV')" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-file-csv mr-2"></i>CSV
        </button>
    </div>
</div>

<script>
function generarReporte(nombreReporte) {
    alert('Generando reporte: ' + nombreReporte + '\n\nEn una implementación completa, aquí se mostraría el reporte con los datos correspondientes.');
    // En una implementación real, redirigir a la página del reporte o mostrar modal con filtros
    // window.location.href = '<?php echo BASE_URL; ?>reportes/generar?tipo=' + encodeURIComponent(nombreReporte);
}

function exportarReporteFormato(formato) {
    alert('Exportando reportes en formato ' + formato + '...\n\nEn una implementación completa, aquí se generaría y descargaría el archivo en el formato seleccionado.');
    // En una implementación real, iniciar descarga del reporte
    // window.location.href = '<?php echo BASE_URL; ?>reportes/exportar?formato=' + formato;
}
</script>
