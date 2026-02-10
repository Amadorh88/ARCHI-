<?php
session_start();
ob_start();

require_once '../config/db.php';
require_once 'fpdf/fpdf.php';

/* =========================================================
   1. FUNCIONES DE APOYO (Hill: Precisión en el detalle)
========================================================= */
function validar($dato) {
    $dato = trim($dato);
    return (empty($dato) || $dato == '0000-00-00' || $dato == '0') ? "___________________________" : $dato;
}

function fechaL($fecha) {
    if (!$fecha || $fecha == '0000-00-00') return "___________________________";
    return date("d / m / Y", strtotime($fecha));
}

$database = new Database();
$db = $database->getConnection();
$id = $_GET['id'] ?? 0;

if (!$id) die("Error: ID de feligrés no proporcionado.");

/* =========================================================
   2. EXTRACCIÓN DE DATOS (Kiyosaki: Integridad del Activo)
========================================================= */
// Datos Feligrés
$stmt = $db->prepare("SELECT * FROM feligres WHERE id_feligres = ?");
$stmt->execute([$id]);
$f = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$f) die("Feligrés no encontrado.");

// Bautismo
$stmt = $db->prepare("SELECT b.*, m.nombre_completo as ministro, p.nombre as parroquia FROM bautismo b LEFT JOIN ministros m ON b.id_ministro = m.id_ministro LEFT JOIN parroquia p ON b.id_parroquia = p.id_parroquia WHERE b.id_feligres = ?");
$stmt->execute([$id]);
$bautismo = $stmt->fetch(PDO::FETCH_ASSOC);

// Comunion
$stmt = $db->prepare("SELECT c.*, m.nombre_completo as ministro, p.nombre as parroquia FROM comunion c LEFT JOIN ministros m ON c.id_ministro = m.id_ministro LEFT JOIN parroquia p ON c.id_parroquia = p.id_parroquia WHERE c.id_feligres = ?");
$stmt->execute([$id]);
$comunion = $stmt->fetch(PDO::FETCH_ASSOC);

// Confirmacion
$stmt = $db->prepare("SELECT c.*, m.nombre_completo as ministro, p.nombre as parroquia FROM confirmacion c LEFT JOIN ministros m ON c.id_ministro = m.id_ministro LEFT JOIN parroquia p ON c.id_parroquia = p.id_parroquia WHERE c.id_feligres = ?");
$stmt->execute([$id]);
$confirmacion = $stmt->fetch(PDO::FETCH_ASSOC);

// Matrimonio
$stmt = $db->prepare("
    SELECT m.*, mi.nombre_completo as ministro,
           (SELECT f2.nombre_completo FROM matrimonio_feligres mf2 
            JOIN feligres f2 ON mf2.id_feligres = f2.id_feligres 
            WHERE mf2.id_matrimonio = m.id_matrimonio AND mf2.id_feligres != ? LIMIT 1) as conyuge
    FROM matrimonio m
    INNER JOIN matrimonio_feligres mf ON m.id_matrimonio = mf.id_matrimonio
    LEFT JOIN ministros mi ON m.id_ministro = mi.id_ministro
    WHERE mf.id_feligres = ? AND m.estado = 'activo'
");
$stmt->execute([$id, $id]);
$matrimonio = $stmt->fetch(PDO::FETCH_ASSOC);

/* =========================================================
   3. CLASE PDF (Cialdini: Estética de Autoridad)
========================================================= */
class PDF_Premium extends FPDF {
    function Header() {
        // Doble Marco Institucional (Oro y Carbón)
        $this->SetDrawColor(160, 140, 40); 
        $this->SetLineWidth(0.8);
        $this->Rect(8, 8, 194, 132); 
        $this->SetDrawColor(40, 40, 40);
        $this->SetLineWidth(0.2);
        $this->Rect(10, 10, 190, 128);
    }

    function DibujarSeccion($x, $y, $titulo) {
        $this->SetXY($x, $y);
        $this->SetFont('Times', 'B', 10);
        $this->SetFillColor(240, 240, 230);
        $this->Cell(90, 6, "  " . strtoupper(utf8_decode($titulo)), 0, 1, 'L', true);
    }

    function RowDato($x, $label, $valor) {
        $this->SetX($x);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode($label . ":"), 0, 0);
        $this->SetFont('Arial', '', 8);
        $this->Cell(65, 5, utf8_decode(validar($valor)), 0, 1);
    }
}

// Formato A5 Horizontal
$pdf = new PDF_Premium('L', 'mm', array(148, 210));
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(false);

/* =========================================================
   CARA 1: IDENTIDAD Y FILIACIÓN
========================================================= */
$pdf->AddPage();
$pdf->SetY(20);
$pdf->SetFont('Times', 'B', 18);
$pdf->Cell(0, 10, utf8_decode("FE DE IDENTIDAD ECLESIÁSTICA"), 0, 1, 'C');
$pdf->SetFont('Times', 'I', 10);
$pdf->Cell(0, 5, utf8_decode("Certificación de Registros Parroquiales"), 0, 1, 'C');
$pdf->Ln(15);

$y_bloque = 55;
$pdf->DibujarSeccion(15, $y_bloque, "Titular de la Fe");
$pdf->Ln(2);
$pdf->RowDato(15, "NOMBRE", $f['nombre_completo']);
$pdf->RowDato(15, "NACIMIENTO", fechaL($f['fecha_nacimiento']));
$pdf->RowDato(15, "LUGAR", $f['lugar_nacimiento']);

$pdf->DibujarSeccion(110, $y_bloque, "Línea de Filiación");
$pdf->Ln(2);
$pdf->RowDato(110, "PADRE", $f['nombre_padre']);
$pdf->RowDato(110, "MADRE", $f['nombre_madre']);

$pdf->SetY(115);
$pdf->SetFont('Arial', 'I', 7);
$pdf->Cell(0, 5, utf8_decode("Este documento no tiene validez si presenta tachaduras o enmiendas."), 0, 1, 'C');

/* =========================================================
   CARA 2: HISTORIAL SACRAMENTAL (4 CUADRANTES)
========================================================= */
$pdf->AddPage();
$pdf->SetY(15);
$pdf->SetFont('Times', 'B', 14);
$pdf->Cell(0, 8, utf8_decode("HISTORIAL DE VIDA SACRAMENTAL"), 0, 1, 'C');

// 1. BAUTISMO (Superior Izquierda)
$pdf->DibujarSeccion(15, 30, "I. Bautismo");
$pdf->Ln(1);
$pdf->RowDato(15, "FECHA", fechaL($bautismo['fecha'] ?? ''));
$pdf->RowDato(15, "PARROQUIA", $bautismo['parroquia'] ?? '');
$pdf->RowDato(15, "MINISTRO", $bautismo['ministro'] ?? '');
$pdf->RowDato(15, "PADRINOS", ($bautismo['padrino'] ?? '')." / ".($bautismo['madrina'] ?? ''));
$pdf->SetX(15); 
$pdf->SetFont('Arial','I',6);
$pdf->Cell(90, 4, "Reg: ".($bautismo['registro'] ?? '_______'), 0, 1, 'R');

// 2. CONFIRMACIÓN (Superior Derecha)
$pdf->DibujarSeccion(110, 30, "II. Confirmación");
$pdf->Ln(1);
$pdf->RowDato(110, "FECHA", fechaL($confirmacion['fecha'] ?? ''));
$pdf->RowDato(110, "PARROQUIA", $confirmacion['parroquia'] ?? '');
$pdf->RowDato(110, "MINISTRO", $confirmacion['ministro'] ?? '');
$pdf->SetX(110); 
$pdf->SetFont('Arial','I',6);
$pdf->Cell(90, 4, "Reg: ".($confirmacion['registro'] ?? '_______'), 0, 1, 'R');

// 3. COMUNIÓN (Inferior Izquierda)
$pdf->DibujarSeccion(15, 75, "III. Primera Comunión");
$pdf->Ln(1);
$pdf->RowDato(15, "FECHA", fechaL($comunion['fecha'] ?? ''));
$pdf->RowDato(15, "PARROQUIA", $comunion['parroquia'] ?? '');
$pdf->RowDato(15, "MINISTRO", $comunion['ministro'] ?? '');
$pdf->SetX(15); 
$pdf->SetFont('Arial','I',6);
$pdf->Cell(90, 4, "Reg: ".($comunion['registro'] ?? '_______'), 0, 1, 'R');

// 4. MATRIMONIO / FIRMA (Inferior Derecha)
$pdf->DibujarSeccion(110, 75, "IV. Matrimonio / Validación");
$pdf->Ln(1);
if ($matrimonio) {
    $pdf->RowDato(110, "CONSORTE", $matrimonio['conyuge']);
    $pdf->RowDato(110, "FECHA", fechaL($matrimonio['fecha']));
    $pdf->RowDato(110, "LUGAR", $matrimonio['lugar']);
} else {
    $pdf->SetX(110); 
    $pdf->SetFont('Arial', 'I', 7);
    $pdf->Cell(90, 5, utf8_decode("  Sin registro de vínculo matrimonial."), 0, 1);
}

// Bloque de Firma Final (Cialdini: Autoridad Final)
$pdf->SetXY(110, 108);
$pdf->Cell(90, 0, "", 'T', 1);
$pdf->SetX(110);
$pdf->SetFont('Times', 'B', 9);
$pdf->Cell(90, 5, utf8_decode("Pbro. Encargado del Archivo"), 0, 1, 'C');
$pdf->SetX(110);
$pdf->SetFont('Arial', '', 6);
$pdf->Cell(90, 4, "Generado el: ".date('d/m/Y'), 0, 1, 'C');

ob_end_clean();
$pdf->Output("I", "Certificado_A5_Horizontal.pdf");