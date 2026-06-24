<?php
require_once '../config/db.php';

// Verificar que se reciba el ID del feligrés
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    die('ID de feligrés no válido');
}

$id = $_GET['id'];
$db = (new Database())->getConnection();

// Obtener datos del feligrés
$stmt = $db->prepare("SELECT * FROM feligres WHERE id_feligres = ?");
$stmt->execute([$id]);
$feligres = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$feligres) {
    die('Feligrés no encontrado');
}

// Obtener sacramentos (Bautismo, Comunión, Confirmación)
$stmt = $db->prepare("
    SELECT 'BAUTISMO' AS sacramento, fecha, padrino, madrina, 
           (SELECT nombre_completo FROM ministros WHERE id_ministro = b.id_ministro) AS ministro
    FROM bautismo b WHERE id_feligres = ?
    UNION ALL
    SELECT 'COMUNIÓN', fecha, NULL, NULL,
           (SELECT nombre_completo FROM ministros WHERE id_ministro = c.id_ministro)
    FROM comunion c WHERE id_feligres = ?
    UNION ALL
    SELECT 'CONFIRMACIÓN', fecha, NULL, NULL,
           (SELECT nombre_completo FROM ministros WHERE id_ministro = cf.id_ministro)
    FROM confirmacion cf WHERE id_feligres = ?
");
$stmt->execute([$id, $id, $id]);
$sacramentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ordenar por fecha
usort($sacramentos, function($a, $b) {
    return strtotime($a['fecha']) - strtotime($b['fecha']);
});

// Obtener catequesis (solo la más reciente)
$stmt = $db->prepare("
    SELECT 
        c.tipo,
        cu.nombre AS curso_nombre,
        cat.nombre AS catequista_nombre
    FROM catequesis c
    LEFT JOIN curso cu ON c.id_curso = cu.id_curso
    LEFT JOIN catequista cat ON cu.id_catequista = cat.id_catequista
    WHERE c.id_feligres = ? AND c.estado = 1
    ORDER BY c.id_catequesis DESC
    LIMIT 1
");
$stmt->execute([$id]);
$catequesis = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener matrimonio si está casado
$stmt = $db->prepare("
    SELECT m.fecha, m.registro, m.lugar,
           (SELECT nombre_completo FROM ministros WHERE id_ministro = m.id_ministro) AS ministro,
           mf.rol
    FROM matrimonio m
    INNER JOIN matrimonio_feligres mf ON m.id_matrimonio = mf.id_matrimonio
    WHERE mf.id_feligres = ? AND mf.rol IN ('esposo', 'esposa')
    LIMIT 1
");
$stmt->execute([$id]);
$matrimonio = $stmt->fetch(PDO::FETCH_ASSOC);

// Calcular edad
$edad = '';
if ($feligres['fecha_nacimiento']) {
    $fecha_nac = new DateTime($feligres['fecha_nacimiento']);
    $hoy = new DateTime();
    $edad = $hoy->diff($fecha_nac)->y;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha Canónica - <?php echo htmlspecialchars($feligres['nombre_completo']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* Configuración para papel A5 (148mm x 210mm) */
        @page {
            size: A5;
            margin: 8mm 6mm;
        }
        
        @media print {
            body {
                background: white;
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .ficha-container {
                box-shadow: none;
                padding: 0;
                margin: 0;
            }
            .card-ficha {
                padding: 0;
            }
            .header-ficha {
                margin-bottom: 8px;
                padding-bottom: 6px;
            }
            .section-title {
                margin: 8px 0 6px 0;
                padding: 4px 8px;
                font-size: 10pt;
            }
            .info-table td {
                padding: 3px 4px;
                font-size: 8pt;
            }
            .sacramento-item {
                padding: 4px 8px;
                margin-bottom: 5px;
            }
            .footer-ficha {
                margin-top: 10px;
                padding-top: 5px;
            }
            .small-text {
                font-size: 7pt;
            }
        }
        
        /* Estilos para pantalla (vista previa) */
        body {
            background: #e9ecef;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* Contenedor simula tamaño A5 */
        .ficha-container {
            max-width: 148mm;
            width: 100%;
            margin: 0 auto;
            background: white;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            border-radius: 4px;
        }
        
        .card-ficha {
            background: white;
            padding: 10px 12px;
        }
        
        /* Encabezado */
        .header-ficha {
            text-align: center;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        
        .header-ficha h3 {
            font-size: 12pt;
            margin-bottom: 3px;
            font-weight: bold;
            color: #0d6efd;
        }
        
        .header-ficha p {
            font-size: 8pt;
            margin: 0;
            color: #6c757d;
        }
        
        /* Títulos de sección */
        .section-title {
            font-size: 10pt;
            font-weight: bold;
            background: #0d6efd;
            color: white;
            padding: 5px 10px;
            margin: 10px 0 6px 0;
            border-radius: 3px;
        }
        
        .section-title i {
            margin-right: 5px;
            font-size: 9pt;
        }
        
        /* Tabla de datos */
        .info-table {
            width: 100%;
            margin-bottom: 5px;
        }
        
        .info-table td {
            padding: 4px 5px;
            border-bottom: 1px solid #e9ecef;
            vertical-align: top;
            font-size: 8.5pt;
        }
        
        .info-table td:first-child {
            font-weight: 600;
            width: 38%;
            color: #495057;
        }
        
        .info-table td:last-child {
            color: #212529;
        }
        
        /* Badges sacramentos */
        .sacramento-item {
            background: #f8f9fa;
            border-left: 3px solid #0d6efd;
            padding: 5px 10px;
            margin-bottom: 6px;
            font-size: 8pt;
        }
        
        .sacramento-titulo {
            font-weight: bold;
            color: #0d6efd;
            font-size: 9pt;
            margin-bottom: 3px;
        }
        
        .sacramento-item div {
            font-size: 8pt;
            margin-bottom: 2px;
        }
        
        /* Línea divisoria */
        .divider {
            height: 1px;
            background: #dee2e6;
            margin: 10px 0;
        }
        
        /* Pie de página */
        .footer-ficha {
            margin-top: 12px;
            padding-top: 6px;
            text-align: center;
            font-size: 6.5pt;
            color: #adb5bd;
            border-top: 1px solid #dee2e6;
        }
        
        /* Botón impresión */
        .btn-print {
            background: #0d6efd;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 30px;
            font-weight: 500;
            margin: 10px auto;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 10pt;
        }
        
        .btn-print:hover {
            background: #0b5ed7;
            transform: scale(1.02);
        }
        
        .text-center {
            text-align: center;
        }
        
        .d-block {
            display: block;
        }
        
        .mb-1 {
            margin-bottom: 3px;
        }
        
        .mt-2 {
            margin-top: 6px;
        }
        
        .text-muted {
            color: #6c757d;
        }
        
        /* Para pantalla */
        @media screen {
            .ficha-container {
                box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            }
        }
    </style>
</head>
<body>
<div class="ficha-container">
    <!-- Botón de impresión solo visible en pantalla -->
    <div class="no-print text-center pt-2">
        <button onclick="window.print();" class="btn-print">
            <i class="bi bi-printer-fill"></i> Imprimir Ficha (A5)
        </button>
        <button onclick="window.close();" class="btn-print" style="background: #6c757d; margin-left: 10px;">
            <i class="bi bi-x-lg"></i> Cerrar
        </button>
    </div>
    
    <!-- FICHA PARA IMPRIMIR -->
    <div class="card-ficha">
        
        <!-- ENCABEZADO -->
        <div class="header-ficha">
            <h3>PARROQUIA INMACULADO CORAZÓN DE MARÍA</h3>
            <p>Ficha Canónica del Feligrés | Expediente #<?php echo str_pad($feligres['id_feligres'], 6, '0', STR_PAD_LEFT); ?></p>
            <p class="mb-0">Fecha de emisión: <?php echo date('d/m/Y'); ?></p>
        </div>
        
        <!-- DATOS PERSONALES -->
        <div class="section-title">
            <i class="bi bi-person-fill"></i> DATOS PERSONALES
        </div>
        <table class="info-table">
            <tr>
                <td>Nombre completo:</td>
                <td><strong><?php echo htmlspecialchars($feligres['nombre_completo']); ?></strong></td>
            </tr>
            <tr>
                <td>Género:</td>
                <td><?php echo htmlspecialchars($feligres['genero']); ?></td>
            </tr>
            <tr>
                <td>Fecha de nacimiento:</td>
                <td><?php echo $feligres['fecha_nacimiento'] ? date('d/m/Y', strtotime($feligres['fecha_nacimiento'])) : '---'; ?>
                <?php if ($edad): ?> (<?php echo $edad; ?> años)<?php endif; ?></td>
            </tr>
            <tr>
                <td>Lugar de nacimiento:</td>
                <td><?php echo htmlspecialchars($feligres['lugar_nacimiento'] ?? '---'); ?></td>
            </tr>
            <tr>
                <td>Nombre del padre:</td>
                <td><?php echo htmlspecialchars($feligres['nombre_padre'] ?? '---'); ?></td>
            </tr>
            <tr>
                <td>Nombre de la madre:</td>
                <td><?php echo htmlspecialchars($feligres['nombre_madre'] ?? '---'); ?></td>
            </tr>
        </table>
        
        <!-- SACRAMENTOS -->
        <div class="section-title">
            <i class="bi bi-cup-straw"></i> SACRAMENTOS
        </div>
        <?php if (empty($sacramentos)): ?>
            <div class="text-center text-muted small py-1">--- Sin sacramentos registrados ---</div>
        <?php else: ?>
            <?php foreach ($sacramentos as $s): ?>
                <div class="sacramento-item">
                    <div class="sacramento-titulo">
                        ✓ <?php echo htmlspecialchars($s['sacramento']); ?>
                    </div>
                    <div>📅 Fecha: <?php echo $s['fecha'] ? date('d/m/Y', strtotime($s['fecha'])) : 'Pendiente'; ?></div>
                    <?php if (!empty($s['padrino'])): ?>
                        <div>👨 Padrino: <?php echo htmlspecialchars($s['padrino']); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($s['madrina'])): ?>
                        <div>👩 Madrina: <?php echo htmlspecialchars($s['madrina']); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($s['ministro'])): ?>
                        <div>⛪ Ministro: <?php echo htmlspecialchars($s['ministro']); ?></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <!-- MATRIMONIO (si aplica) -->
        <?php if ($matrimonio): ?>
        <div class="section-title">
            <i class="bi bi-heart-fill"></i> MATRIMONIO
        </div>
        <div class="sacramento-item">
            <div>📅 Fecha: <?php echo date('d/m/Y', strtotime($matrimonio['fecha'])); ?></div>
            <div>📋 Registro: <?php echo htmlspecialchars($matrimonio['registro'] ?? '---'); ?></div>
            <div>📍 Lugar: <?php echo htmlspecialchars($matrimonio['lugar'] ?? '---'); ?></div>
            <div>⛪ Ministro: <?php echo htmlspecialchars($matrimonio['ministro'] ?? '---'); ?></div>
            <div>👰 Rol: <?php echo ucfirst(htmlspecialchars($matrimonio['rol'])); ?></div>
        </div>
        <?php endif; ?>
        
        <!-- FORMACIÓN CATEQUÉTICA -->
        <div class="section-title">
            <i class="bi bi-book-half"></i> FORMACIÓN CATEQUÉTICA
        </div>
        <?php if (!$catequesis): ?>
            <div class="text-center text-muted small py-1">--- Sin formación registrada ---</div>
        <?php else: ?>
            <table class="info-table">
                <tr>
                    <td>Tipo:</td>
                    <td><?php echo htmlspecialchars($catequesis['tipo'] ?? '---'); ?></td>
                </tr>
                <tr>
                    <td>Curso:</td>
                    <td><?php echo htmlspecialchars($catequesis['curso_nombre'] ?? '---'); ?></td>
                </tr>
                <tr>
                    <td>Catequista:</td>
                    <td><?php echo htmlspecialchars($catequesis['catequista_nombre'] ?? '---'); ?></td>
                </tr>
            </table>
        <?php endif; ?>
        
        <!-- NOTA CANÓNICA -->
        <div class="divider"></div>
        <div class="small-text text-center text-muted">
            <i class="bi bi-shield-check"></i> Este documento certifica los registros sacramentales y de formación<br>
            conforme a los libros parroquiales. Valor oficial para trámites eclesiásticos.
        </div>
        
        <!-- PIE DE FICHA -->
        <div class="footer-ficha">
            <i class="bi bi-qr-code"></i> Código: <?php echo substr(md5($feligres['id_feligres'] . date('Ymd')), 0, 12); ?>
            <br>
            Generado el <?php echo date('d/m/Y H:i:s'); ?>
        </div>
    </div>
</div>

<script>
    // Impresión automática al cargar (opcional - descomentar si se desea)
    // window.onload = function() { setTimeout(function() { window.print(); }, 500); };
</script>
</body>
</html>