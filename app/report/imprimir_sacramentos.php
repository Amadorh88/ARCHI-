<?php
session_start();
ob_start();

require_once '../config/db.php';

$database = new Database();
$db = $database->getConnection();

$id_bautismo = $_GET['id'] ?? 0;

/* ===============================
   CONSULTA BAUTISMO + FELIGRÉS + MINISTRO + PADRINOS
================================ */
$query = "
    SELECT 
        b.id_bautismo,
        b.registro,
        b.fecha AS fecha_bautismo,
        b.padrino,
        b.madrina,
        p.nombre AS nombre_parroquia,
        p.direccion AS direccion_parroquia,
        f.id_feligres,
        f.nombre_completo,
        f.nombre_padre,
        f.nombre_madre,
        f.fecha_nacimiento,
        f.lugar_nacimiento,
        m.nombre_completo AS nombre_ministro,
        m.tipo AS tipo_ministro
    FROM bautismo b
    INNER JOIN feligres f ON b.id_feligres = f.id_feligres
    LEFT JOIN ministros m ON b.id_ministro = m.id_ministro
    LEFT JOIN parroquia p ON b.id_parroquia = p.id_parroquia
    WHERE b.id_bautismo = :id
";

$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id_bautismo, PDO::PARAM_INT);
$stmt->execute();
$bautismo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$bautismo) {
    die('Registro de bautismo no encontrado');
}

/* ===============================
   FUNCIONES
================================ */
function formatFecha($fecha) {
    if (!$fecha) return '';
    setlocale(LC_TIME, 'es_ES.UTF-8');
    return strftime('%d de %B de %Y', strtotime($fecha));
}

function formatFechaCorta($fecha) {
    if (!$fecha) return '';
    $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    $f = date_create($fecha);
    return date_format($f, 'd') . ' de ' . $meses[date_format($f, 'n')-1] . ' de ' . date_format($f, 'Y');
}

// Obtener el ID del feligrés para consultas posteriores
$feligres_id = $bautismo['id_feligres'];

// Generar número de libro, número y folio basados en el registro
$libro = '1'; // Por defecto, puedes ajustar según necesites
$numero = $bautismo['registro'] ?? str_pad($id_bautismo, 3, '0', STR_PAD_LEFT);
$folio = str_pad($id_bautismo, 3, '0', STR_PAD_LEFT);

// Ruta del logo
$logo_path = 'img/logo_parroquia.png';

// Obtener datos de Comunión
$query_comunion = "SELECT 
                    c.*,
                    m.nombre_completo as nombre_ministro,
                    p.nombre as nombre_parroquia
                  FROM comunion c
                  LEFT JOIN ministros m ON c.id_ministro = m.id_ministro
                  LEFT JOIN parroquia p ON c.id_parroquia = p.id_parroquia
                  WHERE c.id_feligres = :id_feligres";
$stmt_comunion = $db->prepare($query_comunion);
$stmt_comunion->bindParam(':id_feligres', $feligres_id);
$stmt_comunion->execute();
$comunion = $stmt_comunion->fetch(PDO::FETCH_ASSOC);

// Obtener datos de Confirmación
$query_confirmacion = "SELECT 
                        conf.*,
                        m.nombre_completo as nombre_ministro,
                        p.nombre as nombre_parroquia
                      FROM confirmacion conf
                      LEFT JOIN ministros m ON conf.id_ministro = m.id_ministro
                      LEFT JOIN parroquia p ON conf.id_parroquia = p.id_parroquia
                      WHERE conf.id_feligres = :id_feligres";
$stmt_confirmacion = $db->prepare($query_confirmacion);
$stmt_confirmacion->bindParam(':id_feligres', $feligres_id);
$stmt_confirmacion->execute();
$confirmacion = $stmt_confirmacion->fetch(PDO::FETCH_ASSOC);

// Obtener datos de Matrimonio
$query_matrimonio = "SELECT 
                      mat.*,
                      m.nombre_completo as nombre_ministro,
                      p.nombre as nombre_parroquia,
                      f_esposo.nombre_completo as nombre_esposo,
                      f_esposa.nombre_completo as nombre_esposa
                    FROM matrimonio_feligres mf
                    INNER JOIN matrimonio mat ON mf.id_matrimonio = mat.id_matrimonio
                    LEFT JOIN ministros m ON mat.id_ministro = m.id_ministro
                    LEFT JOIN parroquia p ON mat.lugar = p.nombre
                    -- Obtener esposo
                    LEFT JOIN matrimonio_feligres mf_esposo ON mat.id_matrimonio = mf_esposo.id_matrimonio 
                      AND mf_esposo.rol = 'esposo'
                    LEFT JOIN feligres f_esposo ON mf_esposo.id_feligres = f_esposo.id_feligres
                    -- Obtener esposa
                    LEFT JOIN matrimonio_feligres mf_esposa ON mat.id_matrimonio = mf_esposa.id_matrimonio 
                      AND mf_esposa.rol = 'esposa'
                    LEFT JOIN feligres f_esposa ON mf_esposa.id_feligres = f_esposa.id_feligres
                    WHERE mf.id_feligres = :id_feligres 
                    AND mf.rol IN ('esposo', 'esposa')
                    LIMIT 1";
$stmt_matrimonio = $db->prepare($query_matrimonio);
$stmt_matrimonio->bindParam(':id_feligres', $feligres_id);
$stmt_matrimonio->execute();
$matrimonio = $stmt_matrimonio->fetch(PDO::FETCH_ASSOC);

// Obtener testigos del matrimonio
$testigos = [];
if ($matrimonio) {
    $query_testigos = "SELECT 
                        f.nombre_completo
                      FROM matrimonio_feligres mf
                      INNER JOIN feligres f ON mf.id_feligres = f.id_feligres
                      WHERE mf.id_matrimonio = :id_matrimonio 
                      AND mf.rol = 'testigo'";
    $stmt_testigos = $db->prepare($query_testigos);
    $stmt_testigos->bindParam(':id_matrimonio', $matrimonio['id_matrimonio']);
    $stmt_testigos->execute();
    $testigos = $stmt_testigos->fetchAll(PDO::FETCH_ASSOC);
}

// Función para formatear fecha en formato día/mes/año
function formatFechaSimple($fecha) {
    if (!$fecha || $fecha == '0000-00-00') return '';
    return date('d/m/Y', strtotime($fecha));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Certificado de Bautismo</title>

<style>
/* [Todo el CSS se mantiene igual] */

:root{
    --gold: #8b7355;
    --brown: #5d4037;
    --gray: #666;
    --paper: #f9f5eb;
}

body{
    background:#f0f0f0;
    font-family: 'Georgia', 'Times New Roman', serif;
    margin: 0;
    padding: 10px;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    min-height: 100vh;
}

.print-controls{
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 1000;
}

.print-controls button{
    background:var(--brown);
    color:white;
    border:none;
    padding:8px 16px;
    border-radius:4px;
    cursor:pointer;
    font-size:12px;
    margin: 3px;
}
.btn-back {
    background:var(--brown);
    color:white;
    border:none;
    padding:8px 16px;
    border-radius:4px;
    cursor:pointer;
    font-size:12px;
    margin: 3px;
        }

.certificate{
    width: 400px;
    height: 280px;
    margin: 60px auto 20px;
    background: var(--paper);
    padding: 15px 25px;
    border: 1px solid var(--gold);
    position: relative;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    background-image: 
        linear-gradient(var(--paper) 20px, transparent 1px),
        linear-gradient(90deg, transparent 99%, rgba(139,115,85,0.05) 1%);
    background-size: 100% 20px, 70px 100%;
    background-position: 0 0, 0 0;
    overflow: hidden;
    box-sizing: border-box;
}

/* Logo de la parroquia */
.parish-logo {
    position: absolute;
    top: 12px;
    left: 20px;
    z-index: 10;
    text-align: center;
}

.parish-logo img {
    max-width: 35px;
    height: auto;
    margin-bottom: 1px;
}

.parish-logo .logo-text {
    font-size: 7px;
    color: var(--gray);
    font-style: italic;
    line-height: 1;
}

/* Información de la parroquia al lado del logo */
.parish-info {
    position: absolute;
    top: 10px;
    left: 65px;
    text-align: left;
    max-width: 160px;
}

.parish-info h2 {
    font-size: 11px;
    color: var(--brown);
    margin: 0 0 1px 0;
    font-weight: bold;
    line-height: 1.1;
}

.parish-info .address {
    font-size: 8px;
    color: var(--gray);
    margin: 0;
    line-height: 1;
}

.parish-info .phone {
    font-size: 7px;
    color: var(--gray);
    margin: 0;
    font-style: italic;
}

/* Número de registro */
.record-number {
    position: absolute;
    top: 12px;
    right: 20px;
    font-size: 10px;
    color: var(--brown);
    font-weight: bold;
    text-align: right;
    line-height: 1.2;
}

/* Título del certificado */
.book-header {
    text-align: center;
    margin-bottom: 12px;
    padding-bottom: 5px;
    border-bottom: 1px solid var(--gold);
    margin-top: 40px;
}

.book-header h1 {
    font-size: 14px;
    color: var(--brown);
    margin: 0;
    font-weight: normal;
    letter-spacing: 0.5px;
}

.book-header .subtitle {
    font-size: 9px;
    color: var(--gray);
    margin-top: 1px;
    font-style: italic;
}

/* Contenido principal */
.main-content {
    font-size: 11px;
    line-height: 1.3;
}

.highlight-name {
    font-size: 12px;
    font-weight: bold;
    text-align: center;
    margin: 6px 0;
    color: var(--brown);
    text-transform: uppercase;
    letter-spacing: 0.3px;
    padding: 3px;
    border-top: 1px dashed var(--gold);
    border-bottom: 1px dashed var(--gold);
    line-height: 1.2;
}

.parent-line {
    display: flex;
    align-items: center;
    margin: 3px 0;
}

.parent-label {
    min-width: 60px;
    font-weight: bold;
    color: var(--brown);
    font-size: 10px;
}

.parent-names {
    flex: 1;
    border-bottom: 1px dotted #999;
    padding-bottom: 0;
    margin-left: 3px;
    font-size: 10px;
    min-height: 12px;
}

.details {
    display: flex;
    justify-content: space-between;
    margin: 6px 0;
    font-size: 10px;
    gap: 5px;
}

.details div {
    flex: 1;
    padding: 0 2px;
    min-height: 25px;
}

.details strong {
    font-size: 9px;
    display: block;
    margin-bottom: 1px;
}

/* Divider lines */
.small-divider {
    border-top: 0.5px dashed var(--gold);
    margin: 5px 0;
}

.large-divider {
    border-top: 1px solid var(--gold);
    margin: 8px 0;
}

/* Firma y sello */
.signature-section {
    margin-top: 15px;
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    gap: 10px;
}

.signature-box {
    text-align: center;
    width: 45%;
    flex-shrink: 0;
}

.signature-line {
    border-top: 1px solid #333;
    margin-top: 20px;
    padding-top: 1px;
    font-size: 9px;
    width: 100%;
}

.ministro-name {
    font-size: 9px;
    color: var(--brown);
    margin-top: 1px;
    font-weight: bold;
    line-height: 1.1;
}

.parish-stamp {
    text-align: center;
    font-size: 9px;
    color: var(--gray);
    width: 45%;
    flex-shrink: 0;
}

.parish-name {
    font-size: 10px;
    font-weight: bold;
    color: var(--brown);
    margin-bottom: 1px;
    line-height: 1.1;
}

/* Pie de página */
.page-number {
    position: absolute;
    bottom: 8px;
    right: 20px;
    font-size: 8px;
    color: var(--gray);
}

/* Campos con datos */
.data-field {
    font-weight: bold;
    color: #000;
    font-size: 10px;
    display: block;
    min-height: 12px;
    word-break: break-word;
    line-height: 1.1;
}

@media print {
    body {
        background: white;
        padding: 0;
        margin: 0;
        display: block;
    }
    .print-controls {
        display: none;
    }
    
    .certificate {
        width: 95mm;
        height: 65mm;
        margin: 0;
        padding: 10px 15px;
        box-shadow: none;
        border: 0.5mm solid var(--gold);
        page-break-after: always;
        position: relative;
        left: 0;
        top: 0;
        transform: none;
    }
    
    @page {
        size: A4;
        margin: 10mm;
    }
    
    .parish-logo img {
        max-width: 30px;
    }
    
    .parish-info h2 { font-size: 10px; }
    .parish-info .address { font-size: 7px; }
    .parish-info .phone { font-size: 6px; }
    .record-number { font-size: 9px; }
    .book-header h1 { font-size: 12px; }
    .book-header .subtitle { font-size: 8px; }
    .main-content { font-size: 10px; }
    .highlight-name { font-size: 11px; }
    .parent-label, .parent-names { font-size: 9px; }
    .details { font-size: 9px; }
    .data-field { font-size: 9px; }
}

/* Estilos para el segundo certificado (sacramentos) */
.sacramento-section {
    margin-bottom: 8px;
    font-size: 10px;
    line-height: 1.3;
}

.sacramento-title {
    font-size: 11px;
    font-weight: bold;
    text-align: center;
    margin: 4px 0;
    color: var(--brown);
    text-transform: uppercase;
    letter-spacing: 0.3px;
    border-top: 1px dashed var(--gold);
    border-bottom: 1px dashed var(--gold);
    padding: 2px 0;
}

.sacramento-line {
    display: flex;
    align-items: center;
    margin: 2px 0;
}

.line-label {
    min-width: 60px;
    font-weight: bold;
    color: var(--brown);
    font-size: 9px;
}

.line-value {
    flex: 1;
    border-bottom: 1px dotted #999;
    padding-bottom: 0;
    margin-left: 3px;
    font-size: 9px;
    min-height: 12px;
    color: #000;
    font-weight: bold;
}

/* Estilos específicos para matrimonio */
.matrimonio-details {
    margin: 4px 0;
}

.conyuges-line {
    display: flex;
    align-items: center;
    margin: 2px 0;
}

.conyuge-label {
    min-width: 40px;
    font-weight: bold;
    color: var(--brown);
    font-size: 9px;
}

.testigos-section {
    margin-top: 4px;
    padding-left: 10px;
}

.testigo-line {
    display: flex;
    align-items: center;
    margin: 1px 0;
}

.testigo-label {
    min-width: 50px;
    font-weight: bold;
    color: var(--brown);
    font-size: 9px;
}

.empty-line {
    flex: 1;
    border-bottom: 1px dotted #999;
    padding-bottom: 0;
    margin-left: 3px;
    min-height: 12px;
}

/* Firma y fecha para segundo certificado */
.signature-section-second {
    margin-top: 10px;
    display: flex;
    justify-content: center;
}

.signature-line-second {
    border-top: 1px solid #333;
    width: 60%;
    text-align: center;
    padding-top: 2px;
    font-size: 9px;
    color: var(--gray);
}

.date-line {
    text-align: center;
    font-size: 9px;
    margin-top: 5px;
    color: var(--brown);
    font-weight: bold;
}

.footer {
    text-align: center;
    font-size: 6px;
    margin-top: 5px;
    color: var(--gray);
    font-style: italic;
}

/* Para organizar múltiples certificados en vista previa */
.certificate-container {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    grid-gap: 10px;
    margin-top: 80px;
}

@media screen and (max-width: 900px) {
    .certificate-container {
        grid-template-columns: 1fr;
    }
}
</style>
</head>

<body>

<div class="print-controls">
    <button onclick="window.print()">🖨 Imprimir Certificado</button>
    <a href="dashboard.php" class="btn-back">
            ← Volver 
        </a>
</div>

<!-- Contenedor para vista previa de múltiples certificados -->
<div class="certificate-container">

<!-- Primer certificado -->
<div class="certificate">

    <!-- Logo de la parroquia en la parte superior izquierda -->
    <div class="parish-logo">
        <?php if (file_exists($logo_path)): ?>
            <img src="<?= $logo_path ?>" alt="Logo Parroquia" onerror="this.style.display='none'">
        <?php else: ?>
            <!-- Logo de ejemplo si no existe el archivo -->
            <div style="width: 35px; height: 35px; background: var(--gold); color: white; display: flex; align-items: center; justify-content: center; font-size: 8px; border-radius: 50%;">
                PARROQUIA
            </div>
        <?php endif; ?>
    </div>

    <!-- Información de la parroquia al lado del logo -->
    <div class="parish-info">
        <h2><?= htmlspecialchars($bautismo['nombre_parroquia'] ?? 'Parroquia Inmaculado Corazón de María') ?></h2>
        <p class="address"><?= htmlspecialchars($bautismo['direccion_parroquia'] ?? 'Avda de la Independencia') ?></p>
    </div>

    <!-- Número de registro (en la parte superior derecha) -->
    <div class="record-number">
        Libro: <?= htmlspecialchars($libro) ?><br>
        Registro: <?= htmlspecialchars($bautismo['registro'] ?? 'N/A') ?><br>
        Folio: <?= htmlspecialchars($folio) ?>
    </div>

    <!-- Título del certificado -->
    <div class="book-header">
        <h1>CERTIFICADO DE BAUTISMO</h1>
    </div>

    <!-- Contenido principal -->
    <div class="main-content">
        
        <!-- Nombre principal destacado -->
        <div class="highlight-name">
            <?= htmlspecialchars($bautismo['nombre_completo']) ?>
        </div>

        <!-- Información de padres -->
        <div class="parent-line">
            <div class="parent-label">Hijo/a de D.</div>
            <div class="parent-names data-field"><?= htmlspecialchars($bautismo['nombre_padre'] ?? '') ?></div>
        </div>
        
        <div class="parent-line">
            <div class="parent-label">y de Dña.</div>
            <div class="parent-names data-field"><?= htmlspecialchars($bautismo['nombre_madre'] ?? '') ?></div>
        </div>

        <div class="small-divider"></div>

        <!-- Detalles de nacimiento -->
        <div class="details">
            <div>
                <strong>Nació en:</strong>
                <span class="data-field"><?= htmlspecialchars($bautismo['lugar_nacimiento'] ?? '') ?></span>
            </div>
            <div>
                <strong>El:</strong>
                <span class="data-field"><?= $bautismo['fecha_nacimiento'] ? formatFechaCorta($bautismo['fecha_nacimiento']) : '' ?></span>
            </div>
        </div>

        <div class="small-divider"></div>

        <!-- Detalles de bautismo -->
        <div class="details">
            <div>
                <strong>Bautizado en:</strong>
                <span class="data-field"><?= htmlspecialchars($bautismo['nombre_parroquia'] ?? 'Parroquia Inmaculado Corazón de María') ?></span>
            </div>
            <div>
                <strong>Por el Rvdo.</strong>
                <span class="data-field"><?= htmlspecialchars($bautismo['nombre_ministro'] ?? '') ?></span>
            </div>
            <div>
                <strong>Día:</strong>
                <span class="data-field"><?= $bautismo['fecha_bautismo'] ? formatFechaCorta($bautismo['fecha_bautismo']) : '' ?></span>
            </div>
        </div>

        <div class="large-divider"></div>

        <!-- Padrinos (Ahora usando los campos padrino y madrina de la tabla bautismo) -->
        <div class="details">
            <div>
                <strong>Padrino:</strong>
                <span class="data-field"><?= htmlspecialchars($bautismo['padrino'] ?? '') ?></span>
            </div>
            <div>
                <strong>Madrina:</strong>
                <span class="data-field"><?= htmlspecialchars($bautismo['madrina'] ?? '') ?></span>
            </div>
        </div>

        <!-- Firma y sello -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="ministro-name"><?= htmlspecialchars($bautismo['nombre_ministro'] ?? '') ?></div>
                <div style="font-size: 7px;"><?= htmlspecialchars($bautismo['tipo_ministro'] ?? '') ?></div>
            </div>
            
            <div class="parish-stamp">
                <div class="parish-name"><?= htmlspecialchars($bautismo['nombre_parroquia'] ?? 'INMACULADO CORAZÓN DE MARÍA') ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Segundo certificado (sacramentos) -->
<div class="certificate">
   
    <!-- Sección de Comunión -->
    <?php if ($comunion): ?>
    <div class="sacramento-section">
        <div class="sacramento-title">COMUNIÓN</div>
        
        <div class="sacramento-line">
            <span class="line-label">Comunión el</span>
            <span class="line-value"><?= formatFechaSimple($comunion['fecha']) ?></span>
        </div>
        
        <div class="sacramento-line">
            <span class="line-label">En</span>
            <span class="line-value"><?= htmlspecialchars($comunion['nombre_parroquia'] ?? '') ?></span>
        </div>
        
        <div class="sacramento-line">
            <span class="line-label">Registro</span>
            <span class="line-value"><?= htmlspecialchars($comunion['registro'] ?? '') ?></span>
        </div>
        
        <div class="sacramento-line">
            <span class="line-label">Ministro</span>
            <span class="line-value"><?= htmlspecialchars($comunion['nombre_ministro'] ?? '') ?></span>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Sección de Confirmación -->
    <?php if ($confirmacion): ?>
    <div class="sacramento-section">
        <div class="sacramento-title">CONFIRMACIÓN</div>
        
        <div class="sacramento-line">
            <span class="line-label">Confirmación el</span>
            <span class="line-value"><?= formatFechaSimple($confirmacion['fecha']) ?></span>
        </div>
        
        <div class="sacramento-line">
            <span class="line-label">En</span>
            <span class="line-value"><?= htmlspecialchars($confirmacion['nombre_parroquia'] ?? '') ?></span>
        </div>
        
        <div class="sacramento-line">
            <span class="line-label">Registro</span>
            <span class="line-value"><?= htmlspecialchars($confirmacion['registro'] ?? '') ?></span>
        </div>
        
        <div class="sacramento-line">
            <span class="line-label">Ministro</span>
            <span class="line-value"><?= htmlspecialchars($confirmacion['nombre_ministro'] ?? '') ?></span>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Sección de Matrimonio -->
    <?php if ($matrimonio): ?>
    <div class="sacramento-section">
        <div class="sacramento-title">MATRIMONIO</div>
        
        <div class="sacramento-line">
            <span class="line-label">Matrimonio el</span>
            <span class="line-value"><?= formatFechaSimple($matrimonio['fecha']) ?></span>
        </div>
        
        <div class="sacramento-line">
            <span class="line-label">En</span>
            <span class="line-value"><?= htmlspecialchars($matrimonio['lugar'] ?? '') ?></span>
        </div>
        
        <div class="sacramento-line">
            <span class="line-label">Registro</span>
            <span class="line-value"><?= htmlspecialchars($matrimonio['registro'] ?? '') ?></span>
        </div>
        
        <div class="matrimonio-details">
            <?php if ($matrimonio['nombre_esposo'] && $matrimonio['nombre_esposa']): ?>
            <div class="conyuges-line">
                <span class="conyuge-label">Con</span>
                <span class="line-value">
                    <?php 
                    // Mostrar el cónyuge
                    if ($bautismo['nombre_completo'] == $matrimonio['nombre_esposo']) {
                        echo htmlspecialchars($matrimonio['nombre_esposa']);
                    } else {
                        echo htmlspecialchars($matrimonio['nombre_esposo']);
                    }
                    ?>
                </span>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($testigos)): ?>
            <div class="testigos-section">
                <div class="testigo-line">
                    <span class="testigo-label">Testigos:</span>
                    <span class="line-value">
                        <?php 
                        $nombres_testigos = array_map(function($t) {
                            return $t['nombre_completo'];
                        }, $testigos);
                        echo htmlspecialchars(implode(' y ', $nombres_testigos));
                        ?>
                    </span>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="sacramento-line">
                <span class="line-label">Ministro</span>
                <span class="line-value"><?= htmlspecialchars($matrimonio['nombre_ministro'] ?? '') ?></span>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if (!$comunion && !$confirmacion && !$matrimonio): ?>
    <div class="sacramento-section">
        <p style="text-align: center; color: var(--gray);">No hay otros sacramentos registrados</p>
    </div>
    <?php endif; ?>
    
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="ministro-name">Párroco</div>
        </div>
        
        <div class="parish-stamp">
            <div class="signature-line"></div>
            <div class="parish-name">Sello Parroquial</div>
        </div>
    </div>
    
    <div class="date-line">
        <?= date('d/m/Y') ?>
    </div>
    
    <div class="footer">
        Parroquia Inmaculado Corazón de María
    </div>
</div>

</div> <!-- Cierre del certificate-container -->

</body>
</html>
$pdf->Output("I", "Certificado_A5_Horizontal.pdf");
