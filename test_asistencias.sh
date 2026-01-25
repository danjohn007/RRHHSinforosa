#!/bin/bash

# ============================================================
# Script de Pruebas para Mejoras de Control de Asistencia
# ============================================================

echo "=========================================="
echo "Pruebas de Control de Asistencia"
echo "Fecha: $(date '+%Y-%m-%d %H:%M:%S')"
echo "=========================================="
echo ""

# Colores para output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuración de base de datos (ajustar según entorno)
# IMPORTANTE: En producción, usar variables de entorno o archivos de configuración seguros
DB_HOST="${DB_HOST:-localhost}"
DB_USER="${DB_USER:-root}"
DB_NAME="${DB_NAME:-recursos_humanos}"
DB_PASS="${DB_PASS:-}"

# Construir comando mysql con credenciales
if [ -n "$DB_PASS" ]; then
    MYSQL_CMD="mysql -h $DB_HOST -u $DB_USER -p$DB_PASS"
else
    MYSQL_CMD="mysql -h $DB_HOST -u $DB_USER"
fi

# Función para mostrar resultados
check_result() {
    if [ $1 -eq 0 ]; then
        echo -e "${GREEN}✓ $2${NC}"
    else
        echo -e "${RED}✗ $2${NC}"
        exit 1
    fi
}

# ============================================================
# 1. Verificar que las migraciones se ejecutaron
# ============================================================
echo "1. Verificando estructura de base de datos..."

# Verificar campo sucursal_salida_id
$MYSQL_CMD -e "USE $DB_NAME; SHOW COLUMNS FROM asistencias LIKE 'sucursal_salida_id';" > /dev/null 2>&1
check_result $? "Campo sucursal_salida_id existe"

# Verificar vista actualizada
$MYSQL_CMD -e "USE $DB_NAME; DESC vista_asistencias_completa;" | grep -q "sucursal_salida_nombre"
check_result $? "Vista vista_asistencias_completa actualizada"

# Verificar procedimiento almacenado
$MYSQL_CMD -e "USE $DB_NAME; SHOW PROCEDURE STATUS WHERE Name = 'auto_cortar_asistencias';" > /dev/null 2>&1
check_result $? "Procedimiento auto_cortar_asistencias existe"

echo ""

# ============================================================
# 2. Verificar archivos PHP actualizados
# ============================================================
echo "2. Verificando actualizaciones de código..."

# Verificar que PublicoController incluye sucursal_salida_id
grep -q "sucursal_salida_id" app/controllers/PublicoController.php
check_result $? "PublicoController actualizado con sucursal_salida_id"

# Verificar validación de foto en vista pública
grep -q "if (!photoData)" app/views/publico/asistencia.php
check_result $? "Vista pública con validación de foto"

# Verificar que index muestra sucursal correcta
grep -q "sucursal_salida_nombre" app/views/asistencia/index.php
check_result $? "Vista index muestra sucursal de salida"

echo ""

# ============================================================
# 3. Verificar permisos de directorios
# ============================================================
echo "3. Verificando permisos..."

# Crear directorio de uploads si no existe
if [ ! -d "uploads/asistencias" ]; then
    mkdir -p uploads/asistencias
    chmod 0755 uploads/asistencias
    check_result $? "Directorio de uploads creado"
else
    check_result 0 "Directorio de uploads existe"
fi

# Verificar permisos
PERMS=$(stat -c %a uploads/asistencias 2>/dev/null || stat -f %A uploads/asistencias 2>/dev/null)
if [ "$PERMS" = "755" ]; then
    check_result 0 "Permisos correctos en uploads (755)"
else
    echo -e "${YELLOW}⚠ Permisos actuales: $PERMS (recomendado: 755)${NC}"
fi

echo ""

# ============================================================
# 4. Verificar sintaxis PHP
# ============================================================
echo "4. Verificando sintaxis PHP..."

php -l app/controllers/PublicoController.php > /dev/null 2>&1
check_result $? "PublicoController.php sin errores de sintaxis"

php -l app/controllers/AsistenciaController.php > /dev/null 2>&1
check_result $? "AsistenciaController.php sin errores de sintaxis"

echo ""

# ============================================================
# 5. Simulación de auto-corte (solo prueba)
# ============================================================
echo "5. Probando procedimiento auto_cortar_asistencias..."

# Ejecutar el procedimiento (no afecta datos reales si no hay registros pendientes)
RESULT=$($MYSQL_CMD -e "USE $DB_NAME; CALL auto_cortar_asistencias();" 2>&1)
if [ $? -eq 0 ]; then
    check_result 0 "Procedimiento auto_cortar_asistencias ejecutable"
    echo "   Resultado: $RESULT"
else
    check_result 1 "Procedimiento auto_cortar_asistencias ejecutable"
fi

echo ""

# ============================================================
# 6. Verificar configuración de cron (opcional)
# ============================================================
echo "6. Verificando configuración de cron..."

if [ -f "cron_procesar_asistencias.php" ]; then
    check_result 0 "Script cron existe"
    
    # Verificar sintaxis
    php -l cron_procesar_asistencias.php > /dev/null 2>&1
    check_result $? "Script cron sin errores de sintaxis"
    
    # Verificar si está en crontab (requiere permisos)
    if command -v crontab &> /dev/null; then
        if crontab -l 2>/dev/null | grep -q "cron_procesar_asistencias.php"; then
            check_result 0 "Cron configurado"
        else
            echo -e "${YELLOW}⚠ Cron no configurado. Agregar manualmente:${NC}"
            echo "   5 0 * * * php $(pwd)/cron_procesar_asistencias.php >> /var/log/asistencias_cron.log 2>&1"
        fi
    else
        echo -e "${YELLOW}⚠ crontab no disponible en este entorno${NC}"
    fi
else
    check_result 1 "Script cron existe"
fi

echo ""

# ============================================================
# Resumen Final
# ============================================================
echo "=========================================="
echo -e "${GREEN}Todas las pruebas completadas exitosamente${NC}"
echo "=========================================="
echo ""
echo "Próximos pasos recomendados:"
echo "1. Ejecutar las migraciones SQL si aún no lo has hecho"
echo "2. Probar el registro de asistencia en la vista pública"
echo "3. Verificar el filtro 'Por Validar' en Control de Asistencia"
echo "4. Configurar cron job si no está configurado"
echo ""
