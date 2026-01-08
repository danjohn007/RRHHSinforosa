<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Constancia de Trabajo - <?php echo APP_NAME; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            padding: 40px;
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .header h1 {
            color: #667eea;
            margin-bottom: 5px;
        }
        .date {
            text-align: right;
            margin-bottom: 30px;
        }
        .content {
            text-align: justify;
            margin-bottom: 40px;
        }
        .table-info {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .table-info td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .table-info td:first-child {
            background-color: #f5f5f5;
            font-weight: bold;
            width: 40%;
        }
        .signature {
            margin-top: 60px;
            text-align: center;
        }
        .signature-line {
            border-top: 2px solid #000;
            width: 300px;
            margin: 0 auto 10px;
        }
        @media print {
            body {
                padding: 20px;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="no-print" style="position: fixed; top: 20px; right: 20px; padding: 10px 20px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer;">
        <i class="fas fa-print"></i> Imprimir
    </button>

    <div class="header">
        <h1>Sinforosa Café</h1>
        <p>Sistema de Gestión de Recursos Humanos</p>
        <h2 style="color: #764ba2;">CONSTANCIA DE TRABAJO</h2>
    </div>

    <div class="date">
        Querétaro, Qro., <?php echo date('d \d\e F \d\e Y'); ?>
    </div>

    <div class="content">
        <p><strong>A QUIEN CORRESPONDA:</strong></p>
        
        <p>
            Por medio de la presente, se hace constar que el(la) Sr(a). 
            <strong><?php echo htmlspecialchars($empleado['nombre_completo']); ?></strong> 
            <?php if ($empleado['estatus'] === 'Activo'): ?>
                presta sus servicios
            <?php else: ?>
                prestó sus servicios
            <?php endif; ?>
            en esta empresa desde el <strong><?php echo date('d \d\e F \d\e Y', strtotime($empleado['fecha_ingreso'])); ?></strong>.
        </p>

        <table class="table-info">
            <tr>
                <td>Número de Empleado:</td>
                <td><?php echo htmlspecialchars($empleado['numero_empleado']); ?></td>
            </tr>
            <tr>
                <td>Puesto:</td>
                <td><?php echo htmlspecialchars($empleado['puesto']); ?></td>
            </tr>
            <tr>
                <td>Departamento:</td>
                <td><?php echo htmlspecialchars($empleado['departamento']); ?></td>
            </tr>
            <tr>
                <td>Tipo de Contrato:</td>
                <td><?php echo htmlspecialchars($empleado['tipo_contrato']); ?></td>
            </tr>
            <tr>
                <td>Fecha de Ingreso:</td>
                <td><?php echo date('d/m/Y', strtotime($empleado['fecha_ingreso'])); ?></td>
            </tr>
            <tr>
                <td>Antigüedad:</td>
                <td><?php echo $empleado['anios_antiguedad']; ?> años</td>
            </tr>
            <tr>
                <td>Estatus:</td>
                <td><?php echo htmlspecialchars($empleado['estatus']); ?></td>
            </tr>
        </table>

        <p>
            Se extiende la presente constancia a solicitud del(la) interesado(a) para los fines legales que así convengan.
        </p>
    </div>

    <div class="signature">
        <div class="signature-line"></div>
        <p><strong>Departamento de Recursos Humanos</strong></p>
        <p>Sinforosa Café</p>
        <p style="font-size: 12px; color: #666;">
            Querétaro, Querétaro<br>
            Tel: (442) 123-4567 | rrhh@sinforosa.com<br>
            www.sinforosa.com
        </p>
    </div>
</body>
</html>
