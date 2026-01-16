<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.html");
    exit();
}

include_once 'db.php';
$database = new Database();
$db = $database->getConnection();

$sacramento_id = $_GET['id'] ?? 0;

// Obtener datos del bautismo con información completa del feligrés
$query = "SELECT 
            s.*, 
            f.id as feligres_id,
            f.cedula, 
            f.telefono, 
            f.direccion,
            f.fecha_nacimiento,
            f.nombres,
            f.nombre_padre,
            f.nombre_madre,
            f.estado_civil,
            f.sexo
          FROM sacramento s 
          INNER JOIN feligres f ON s.feligres_id = f.id 
          WHERE s.id = :id AND s.tipo = 'bautismo'";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $sacramento_id);
$stmt->execute();
$bautismo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$bautismo) {
    die("Bautismo no encontrado");
}

// Obtener datos de otros sacramentos para el mismo feligrés
$query_otros_sacramentos = "SELECT 
                            s.*,
                            -- Datos específicos de confirmación
                            (SELECT nombre_completo FROM feligreses WHERE id = s.padrino_confirmacion_id) as padrino_confirmacion,
                            -- Datos específicos de matrimonio
                            (SELECT nombre_completo FROM feligreses WHERE id = s.conyuge_id) as nombre_conyuge,
                            s.testigo1_matrimonio,
                            s.testigo2_matrimonio,
                            s.lugar_matrimonio
                           FROM sacramentos s 
                           WHERE s.feligres_id = :feligres_id 
                           AND s.tipo != 'bautismo'
                           ORDER BY 
                             CASE s.tipo 
                               WHEN 'comunion' THEN 1 
                               WHEN 'confirmacion' THEN 2 
                               WHEN 'matrimonio' THEN 3 
                               ELSE 4 
                             END,
                             s.fecha_sacramento";
$stmt_otros = $db->prepare($query_otros_sacramentos);
$stmt_otros->bindParam(':feligres_id', $bautismo['feligres_id']);
$stmt_otros->execute();
$otros_sacramentos = $stmt_otros->fetchAll(PDO::FETCH_ASSOC);

// Función para formatear fecha en español
function formatFecha($fecha) {
    if (!$fecha) return '';
    
    $meses = [
        'January' => 'Enero', 'February' => 'Febrero', 'March' => 'Marzo',
        'April' => 'Abril', 'May' => 'Mayo', 'June' => 'Junio',
        'July' => 'Julio', 'August' => 'Agosto', 'September' => 'Septiembre',
        'October' => 'Octubre', 'November' => 'Noviembre', 'December' => 'Diciembre'
    ];
    
    $fecha_ingles = date('F d, Y', strtotime($fecha));
    return str_replace(array_keys($meses), array_values($meses), $fecha_ingles);
}

// Función para obtener nombre del sacramento
function getNombreSacramento($tipo) {
    $nombres = [
        'confirmacion' => 'Confirmación',
        'comunion' => 'Primera Comunión',
        'matrimonio' => 'Matrimonio',
        'bautismo' => 'Bautismo'
    ];
    return $nombres[$tipo] ?? ucfirst($tipo);
}

// Función para obtener datos específicos del sacramento
function getDatosSacramento($sacramento) {
    $datos = [];
    
    switch ($sacramento['tipo']) {
        case 'confirmacion':
            if (!empty($sacramento['padrino_confirmacion'])) {
                $datos[] = "<strong>Padrino/Madrina:</strong> " . htmlspecialchars($sacramento['padrino_confirmacion']);
            }
            if (!empty($sacramento['obispo_confirmacion'])) {
                $datos[] = "<strong>Obispo:</strong> " . htmlspecialchars($sacramento['obispo_confirmacion']);
            }
            break;
            
        case 'matrimonio':
            if (!empty($sacramento['nombre_conyuge'])) {
                $datos[] = "<strong>Cónyuge:</strong> " . htmlspecialchars($sacramento['nombre_conyuge']);
            }
            if (!empty($sacramento['testigo1_matrimonio'])) {
                $datos[] = "<strong>Testigo 1:</strong> " . htmlspecialchars($sacramento['testigo1_matrimonio']);
            }
            if (!empty($sacramento['testigo2_matrimonio'])) {
                $datos[] = "<strong>Testigo 2:</strong> " . htmlspecialchars($sacramento['testigo2_matrimonio']);
            }
            break;
            
        case 'comunion':
            if (!empty($sacramento['nombre_padrino'])) {
                $datos[] = "<strong>Padrino/Madrina:</strong> " . htmlspecialchars($sacramento['nombre_padrino']);
            }
            break;
    }
    
    return $datos;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cédula de Bautismo - Parroquia San Juan</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Source+Sans+Pro:wght@300;400;600&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Source Sans Pro', sans-serif;
            background: #f5f5f5;
            color: #333;
            line-height: 1.4;
        }
        
        .certificate-container {
            max-width: 580px;
            margin: 1rem auto;
            background: white;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        
        .certificate-border {
            border: 10px solid transparent;
            padding: 20px;
            position: relative;
            background: 
                linear-gradient(white, white) padding-box,
                repeating-linear-gradient(45deg, #cdaa7d 0px, #cdaa7d 8px, white 8px, white 16px) border-box;
        }
        
        .certificate-header {
            text-align: center;
            margin-bottom: 1rem;
            border-bottom: 2px double #cdaa7d;
            padding-bottom: 0.75rem;
        }
        
        .church-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            color: #8b4513;
            margin-bottom: 0.25rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .certificate-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            color: #2c3e50;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
        }
        
        .certificate-subtitle {
            font-size: 0.9rem;
            color: #7f8c8d;
            font-style: italic;
        }
        
        .certificate-content {
            margin: 1rem 0;
        }
        
        .certificate-text {
            font-size: 0.9rem;
            text-align: justify;
            margin-bottom: 1rem;
            line-height: 1.5;
        }
        
        .baptism-details {
            margin: 1rem 0;
            padding: 1rem;
            background: #f8f9fa;
            border-left: 3px solid #cdaa7d;
        }
        
        .detail-row {
            display: flex;
            margin-bottom: 0.5rem;
            padding: 0.15rem 0;
        }
        
        .detail-label {
            font-weight: 600;
            min-width: 130px;
            color: #2c3e50;
            font-size: 0.85rem;
        }
        
        .detail-value {
            flex: 1;
            color: #34495e;
            font-size: 0.85rem;
        }
        
        .signature-section {
            margin-top: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            width: 150px;
            text-align: center;
            padding-top: 0.3rem;
            font-size: 0.8rem;
        }
        
        .official-stamp {
            text-align: center;
            margin-top: 1rem;
            padding: 0.5rem;
            border: 1px dashed #cdaa7d;
            display: inline-block;
        }
        
        .stamp-text {
            font-family: 'Playfair Display', serif;
            font-size: 0.7rem;
            color: #8b4513;
            text-transform: uppercase;
        }
        
        .print-controls {
            text-align: center;
            margin: 0.5rem 0;
        }
        
        .btn-print {
            background: #2c3e50;
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 3px;
            cursor: pointer;
            font-size: 0.9rem;
            margin: 0 0.25rem;
            transition: background 0.3s;
        }
        
        .btn-print:hover {
            background: #34495e;
        }
        
        .btn-back {
            background: #7f8c8d;
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 3px;
            cursor: pointer;
            font-size: 0.9rem;
            margin: 0 0.25rem;
            text-decoration: none;
            display: inline-block;
        }
        
        /* Estilos para la segunda cara */
        .back-page {
            display: none;
        }
        
        .otros-sacramentos {
            margin-top: 2rem;
        }
        
        .sacramento-item {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #ecf0f1;
        }
        
        .sacramento-titulo {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            color: #8b4513;
            margin-bottom: 0.5rem;
        }
        
        .sacramento-detalle {
            font-size: 0.85rem;
            margin-bottom: 0.25rem;
        }
        
        .no-sacramentos {
            text-align: center;
            font-style: italic;
            color: #7f8c8d;
            margin: 2rem 0;
        }
        
        .sacramento-datos-especificos {
            background: #f8f9fa;
            padding: 0.5rem;
            border-radius: 3px;
            margin-top: 0.5rem;
            font-size: 0.8rem;
        }
        
        @media print {
            body {
                background: white;
            }
            
            .print-controls {
                display: none;
            }
            
            .certificate-container {
                margin: 0;
                box-shadow: none;
                max-width: 100%;
            }
            
            .certificate-border {
                border: 8px solid transparent;
                padding: 15px;
            }
            
            @page {
                size: A6;
                margin: 0;
            }
            
            /* Mostrar segunda cara en impresión */
            .back-page {
                display: block;
                page-break-before: always;
            }
            
            /* Asegurar que cada cara ocupe una página completa */
            .front-page, .back-page {
                height: 148mm;
                width: 105mm;
            }
        }
        
        .certificate-footer {
            text-align: center;
            margin-top: 1rem;
            padding-top: 0.5rem;
            border-top: 1px solid #ecf0f1;
            color: #7f8c8d;
            font-size: 0.7rem;
        }
        
        .important-note {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 0.75rem;
            margin: 0.75rem 0;
            border-radius: 3px;
            font-size: 0.75rem;
        }
        
        .page-indicator {
            text-align: center;
            font-size: 0.7rem;
            color: #7f8c8d;
            margin-top: 0.5rem;
            font-style: italic;
        }
        
        .info-feligres {
            background: #e8f4fd;
            border: 1px solid #b8daff;
            padding: 0.75rem;
            margin: 0.75rem 0;
            border-radius: 3px;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <div class="print-controls">
        <button class="btn-print" onclick="window.print()">
            <i class="fas fa-print"></i> Imprimir Cédula (Doble Cara)
        </button>
        <a href="dashboard.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> Volver al Sistema
        </a>
    </div>

    <!-- PRIMERA CARA - DATOS DEL BAUTISMO -->
    <div class="certificate-container front-page">
        <div class="certificate-border">
            <div class="certificate-header">
                <div class="church-name">Parroquia San Juan Bautista</div>
                <div class="certificate-title">Certificado de Bautismo</div>
                <div class="certificate-subtitle">"Yo te bautizo en el nombre del Padre, del Hijo y del Espíritu Santo"</div>
            </div>
            
            <div class="certificate-content">
                <p class="certificate-text">
                    El que suscribe, <?php echo htmlspecialchars($bautismo['ministro']); ?>, 
                    CURA PÁRROCO de la <strong>Parroquia San Juan Bautista</strong>, 
                    certifica que en el libro <?php echo htmlspecialchars($bautismo['libro_bautismo']); ?>, 
                    Folio <?php echo htmlspecialchars($bautismo['folio_bautismo']); ?>, 
                    Acta No. <?php echo htmlspecialchars($bautismo['numero_acta']); ?>, 
                    consta el siguiente bautismo:
                </p>
                
                <div class="info-feligres">
                    <strong>Información del Feligrés:</strong><br>
                    Cédula: <?php echo htmlspecialchars($bautismo['cedula']); ?> | 
                    Teléfono: <?php echo htmlspecialchars($bautismo['telefono']); ?>
                </div>
                
                <div class="baptism-details">
                    <div class="detail-row">
                        <div class="detail-label">Nombre del Bautizado:</div>
                        <div class="detail-value"><?php echo htmlspecialchars($bautismo['nombre_completo']); ?></div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Fecha de Nacimiento:</div>
                        <div class="detail-value"><?php echo formatFecha($bautismo['fecha_nacimiento']); ?></div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Lugar de Nacimiento:</div>
                        <div class="detail-value"><?php echo htmlspecialchars($bautismo['lugar_nacimiento']); ?></div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Fecha del Bautismo:</div>
                        <div class="detail-value"><?php echo formatFecha($bautismo['fecha_sacramento']); ?></div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Lugar del Bautismo:</div>
                        <div class="detail-value"><?php echo htmlspecialchars($bautismo['lugar']); ?></div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Padre:</div>
                        <div class="detail-value"><?php echo htmlspecialchars($bautismo['nombre_padre']); ?></div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Madre:</div>
                        <div class="detail-value"><?php echo htmlspecialchars($bautismo['nombre_madre']); ?></div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Padrino:</div>
                        <div class="detail-value"><?php echo htmlspecialchars($bautismo['nombre_padrino']); ?></div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Madrina:</div>
                        <div class="detail-value"><?php echo htmlspecialchars($bautismo['nombre_madrina']); ?></div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Ministro que Bautizó:</div>
                        <div class="detail-value"><?php echo htmlspecialchars($bautismo['ministro']); ?></div>
                    </div>
                </div>
                
                <?php if (!empty($bautismo['observaciones_bautismo'])): ?>
                <p class="certificate-text">
                    <strong>Observaciones:</strong> <?php echo htmlspecialchars($bautismo['observaciones_bautismo']); ?>
                </p>
                <?php endif; ?>
                
                <div class="important-note">
                    <strong>Nota:</strong> Este documento tiene valor legal y debe ser conservado cuidadosamente. 
                    Para cualquier trámite sacramental posterior será necesario presentar esta cédula.
                </div>
            </div>
            
            <div class="signature-section">
                <div class="signature-line">
                    _________________________<br>
                    <strong>Ministro que Bautizó</strong><br>
                    <?php echo htmlspecialchars($bautismo['ministro']); ?>
                </div>
                
                <div class="official-stamp">
                    <div class="stamp-text">Sello Oficial</div>
                    <div style="font-size: 0.6rem; margin-top: 0.25rem;">
                        Parroquia San Juan Bautista<br>
                        <?php echo date('d/m/Y'); ?>
                    </div>
                </div>
            </div>
            
            <div class="certificate-footer">
                <p>
                    <strong>Parroquia San Juan Bautista</strong><br>
                    Av. Central #100, Santo Domingo Este<br>
                    Teléfono: (809) 800-1000 | Email: info@sanjuan.com
                </p>
            </div>
            
            <div class="page-indicator">(Cara 1 de 2 - Certificado de Bautismo)</div>
        </div>
    </div>

    <!-- SEGUNDA CARA - OTROS SACRAMENTOS -->
    <div class="certificate-container back-page">
        <div class="certificate-border">
            <div class="certificate-header">
                <div class="church-name">Parroquia San Juan Bautista</div>
                <div class="certificate-title">Otros Sacramentos</div>
                <div class="certificate-subtitle">Registro de sacramentos posteriores al bautismo</div>
            </div>
            
            <div class="certificate-content">
                <p class="certificate-text">
                    A continuación se registran los demás sacramentos recibidos por 
                    <strong><?php echo htmlspecialchars($bautismo['nombre_completo']); ?></strong>
                    en esta Parroquia:
                </p>
                
                <div class="info-feligres">
                    <strong>Datos del Feligrés:</strong><br>
                    Cédula: <?php echo htmlspecialchars($bautismo['cedula']); ?> | 
                    Estado Civil: <?php echo htmlspecialchars($bautismo['estado_civil']); ?> | 
                    Sexo: <?php echo htmlspecialchars($bautismo['sexo']); ?>
                </div>
                
                <div class="otros-sacramentos">
                    <?php if (count($otros_sacramentos) > 0): ?>
                        <?php foreach ($otros_sacramentos as $sacramento): ?>
                            <div class="sacramento-item">
                                <div class="sacramento-titulo">
                                    <?php echo getNombreSacramento($sacramento['tipo']); ?>
                                </div>
                                
                                <div class="sacramento-detalle">
                                    <strong>Fecha:</strong> <?php echo formatFecha($sacramento['fecha_sacramento']); ?>
                                </div>
                                
                                <div class="sacramento-detalle">
                                    <strong>Lugar:</strong> <?php echo htmlspecialchars($sacramento['lugar']); ?>
                                </div>
                                
                                <div class="sacramento-detalle">
                                    <strong>Ministro:</strong> <?php echo htmlspecialchars($sacramento['ministro']); ?>
                                </div>
                                
                                <?php 
                                $datos_especificos = getDatosSacramento($sacramento);
                                if (!empty($datos_especificos)): 
                                ?>
                                    <div class="sacramento-datos-especificos">
                                        <?php foreach ($datos_especificos as $dato): ?>
                                            <div><?php echo $dato; ?></div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($sacramento['observaciones_bautismo'])): ?>
                                <div class="sacramento-detalle">
                                    <strong>Observaciones:</strong> <?php echo htmlspecialchars($sacramento['observaciones_bautismo']); ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-sacramentos">
                            No se registran otros sacramentos para esta persona.
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="important-note">
                    <strong>Importante:</strong> Este documento complementa el Certificado de Bautismo y 
                    debe conservarse junto con el mismo para futuras referencias.
                </div>
            </div>
            
            <div class="signature-section">
                <div class="official-stamp">
                    <div class="stamp-text">Sello Oficial</div>
                    <div style="font-size: 0.6rem; margin-top: 0.25rem;">
                        Parroquia San Juan Bautista<br>
                        <?php echo date('d/m/Y'); ?>
                    </div>
                </div>
            </div>
            
            <div class="certificate-footer">
                <p>
                    <strong>Parroquia San Juan Bautista</strong><br>
                    Av. Central #100, Santo Domingo Este<br>
                    Teléfono: (809) 800-1000
                </p>
            </div>
            
            <div class="page-indicator">(Cara 2 de 2 - Otros Sacramentos)</div>
        </div>
    </div>

    <script>
        // Auto-print opcional
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('autoprint') === 'true') {
            window.print();
        }
        
        // Mejorar la experiencia de impresión
        window.addEventListener('beforeprint', function() {
            document.querySelector('.print-controls').style.display = 'none';
        });
        
        window.addEventListener('afterprint', function() {
            document.querySelector('.print-controls').style.display = 'block';
        });
    </script>
</body>
</html>