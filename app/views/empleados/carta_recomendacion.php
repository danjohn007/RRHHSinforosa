<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carta de Recomendación - <?php echo APP_NAME; ?></title>
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
        <?php if (!empty($configs['sitio_logo'])): ?>
        <div style="text-align: center; margin-bottom: 20px;">
            <img src="<?php echo htmlspecialchars($configs['sitio_logo']); ?>" alt="Logo" style="max-height: 80px; max-width: 200px; margin: 0 auto;">
        </div>
        <?php endif; ?>
        <h1><?php echo htmlspecialchars($configs['sitio_nombre'] ?? 'Sinforosa Café'); ?></h1>
        <p>Sistema de Gestión de Recursos Humanos</p>
    </div>

    <div class="date">
        Querétaro, Qro., <?php echo date('d \d\e F \d\e Y'); ?>
    </div>

    <div class="content">
        <p><strong>A QUIEN CORRESPONDA:</strong></p>
        
        <p>
            Por medio de la presente, hacemos constar que el(la) Sr(a). <strong><?php echo htmlspecialchars($empleado['nombre_completo']); ?></strong>, 
            con número de empleado <strong><?php echo htmlspecialchars($empleado['numero_empleado']); ?></strong>, 
            laboró en nuestra empresa del <strong><?php echo date('d \d\e F \d\e Y', strtotime($empleado['fecha_ingreso'])); ?></strong> 
            <?php if ($empleado['fecha_baja']): ?>
            al <strong><?php echo date('d \d\e F \d\e Y', strtotime($empleado['fecha_baja'])); ?></strong>
            <?php else: ?>
            a la fecha
            <?php endif; ?>.
        </p>

        <p>
            Durante su tiempo en nuestra organización, se desempeñó en el puesto de <strong><?php echo htmlspecialchars($empleado['puesto']); ?></strong> 
            en el departamento de <strong><?php echo htmlspecialchars($empleado['departamento']); ?></strong>, 
            demostrando siempre responsabilidad, compromiso y profesionalismo en todas las tareas asignadas.
        </p>

        <p>
            Su desempeño fue siempre satisfactorio, destacándose por su puntualidad, dedicación y capacidad para trabajar en equipo. 
            Demostró gran habilidad en la resolución de problemas y una actitud proactiva que contribuyó positivamente al ambiente laboral.
        </p>

        <p>
            Es por lo anterior que recomendamos ampliamente sus servicios profesionales, 
            estamos seguros de que será un valioso activo para cualquier organización que tenga la oportunidad de contar con su colaboración.
        </p>

        <p>
            Se extiende la presente para los fines que al interesado convengan.
        </p>
    </div>

    <div class="signature">
        <div class="signature-line"></div>
        <p><strong>Departamento de Recursos Humanos</strong></p>
        <p>Sinforosa Café</p>
        <p style="font-size: 12px; color: #666;">
            Querétaro, Querétaro | Tel: (442) 123-4567 | rrhh@sinforosa.com
        </p>
    </div>
</body>
</html>
