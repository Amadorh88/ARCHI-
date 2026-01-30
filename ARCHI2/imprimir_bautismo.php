<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.html");
    exit;
}

require_once 'config/db.php';
$database = new Database();
$db = $database->getConnection();

$id_bautismo = $_GET['id'] ?? 0;

/* ===============================
   CONSULTA BAUTISMO + FELIGRÉS
================================ */
$query = "
    SELECT 
        b.id_bautismo,
        b.registro,
        b.fecha AS fecha_bautismo,
        b.padrino,
        b.madrina,
        f.nombre_completo,
        f.nombre_padre,
        f.nombre_madre,
        f.fecha_nacimiento,
        f.lugar_nacimiento
    FROM bautismo b
    INNER JOIN feligres f ON b.id_feligres = f.id_feligres
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
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Certificado de Bautismo</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Source+Sans+Pro:wght@400;600&display=swap" rel="stylesheet">

<style>
:root{
    --gold: #bfa76a;
    --blue: #2c3e50;
    --gray: #7f8c8d;
    --paper: #fdfbf7;
}

body{
    background:#eaeaea;
    font-family:'Source Sans Pro', sans-serif;
}

.print-controls{
    text-align:center;
    margin:20px;
}

.print-controls button{
    background:var(--blue);
    color:white;
    border:none;
    padding:10px 20px;
    border-radius:4px;
    cursor:pointer;
    font-size:14px;
}

.certificate{
    width:600px;
    margin:0 auto;
    background:var(--paper);
    padding:35px;
    border:10px solid transparent;
    background:
        linear-gradient(var(--paper),var(--paper)) padding-box,
        linear-gradient(135deg,var(--gold),#e8dcc2,var(--gold)) border-box;
    box-shadow:0 10px 30px rgba(0,0,0,.15);
}

.header{
    text-align:center;
    border-bottom:2px solid var(--gold);
    padding-bottom:15px;
    margin-bottom:25px;
}

.header h1{
    font-family:'Playfair Display', serif;
    color:var(--blue);
    font-size:26px;
    margin-bottom:5px;
    letter-spacing:1px;
}

.header h2{
    font-size:16px;
    color:var(--gray);
    font-style:italic;
}

.section{
    margin-bottom:20px;
}

.section-title{
    font-family:'Playfair Display', serif;
    color:var(--gold);
    font-size:18px;
    margin-bottom:10px;
}

.row{
    display:flex;
    margin-bottom:8px;
}

.label{
    width:200px;
    font-weight:600;
    color:var(--blue);
}

.value{
    color:#333;
}

.divider{
    border-top:1px dashed var(--gold);
    margin:20px 0;
}

.footer{
    margin-top:30px;
    text-align:center;
    font-size:12px;
    color:var(--gray);
}

.signature{
    margin-top:40px;
    display:flex;
    justify-content:space-between;
}

.signature div{
    text-align:center;
    width:45%;
}

.signature-line{
    border-top:1px solid #333;
    margin-top:30px;
    padding-top:5px;
    font-size:13px;
}

@media print{
    body{ background:white; }
    .print-controls{ display:none; }
    .certificate{
        box-shadow:none;
        width:100%;
        border-width:8px;
    }
}
</style>
</head>

<body>

<div class="print-controls">
    <button onclick="window.print()">🖨 Imprimir Certificado</button>
</div>

<div class="certificate">

    <div class="header">
        <h1>Parroquia San Juan Bautista</h1>
        <h2>Certificado Oficial de Bautismo</h2>
    </div>

    <div class="section">
        <div class="section-title">Datos del Bautizado</div>

        <div class="row">
            <div class="label">Nombre completo:</div>
            <div class="value"><?= htmlspecialchars($bautismo['nombre_completo']) ?></div>
        </div>

        <div class="row">
            <div class="label">Fecha de nacimiento:</div>
            <div class="value"><?= formatFecha($bautismo['fecha_nacimiento']) ?></div>
        </div>

        <div class="row">
            <div class="label">Lugar de nacimiento:</div>
            <div class="value"><?= htmlspecialchars($bautismo['lugar_nacimiento']) ?></div>
        </div>

        <div class="row">
            <div class="label">Padre:</div>
            <div class="value"><?= htmlspecialchars($bautismo['nombre_padre']) ?></div>
        </div>

        <div class="row">
            <div class="label">Madre:</div>
            <div class="value"><?= htmlspecialchars($bautismo['nombre_madre']) ?></div>
        </div>
    </div>

    <div class="divider"></div>

    <div class="section">
        <div class="section-title">Datos del Bautismo</div>

        <div class="row">
            <div class="label">Fecha del bautismo:</div>
            <div class="value"><?= formatFecha($bautismo['fecha_bautismo']) ?></div>
        </div>

        <div class="row">
            <div class="label">Registro:</div>
            <div class="value"><?= htmlspecialchars($bautismo['registro']) ?></div>
        </div>

        <div class="row">
            <div class="label">Padrino:</div>
            <div class="value"><?= htmlspecialchars($bautismo['padrino']) ?></div>
        </div>

        <div class="row">
            <div class="label">Madrina:</div>
            <div class="value"><?= htmlspecialchars($bautismo['madrina']) ?></div>
        </div>
    </div>

    <div class="signature">
        <div>
            <div class="signature-line">Ministro</div>
        </div>
        <div>
            <div class="signature-line">Sello Parroquial</div>
        </div>
    </div>

    <div class="footer">
        Certificado emitido el <?= date('d/m/Y') ?><br>
        Documento válido para fines eclesiásticos
    </div>

</div>

</body>
</html>

