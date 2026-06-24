<?php
require '../../config/db.php';
header('Content-Type: application/json');

$id = $_GET['id'];
$tipo = strtolower($_GET['tipo']); // bautismo, comunion, confirmacion, matrimonio
$db = (new Database())->getConnection();

if ($tipo === 'matrimonio') {
    // Obtener datos del matrimonio y sus participantes
    $stmt = $db->prepare("SELECT * FROM matrimonio WHERE id_matrimonio = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        $stmtMF = $db->prepare("SELECT id_feligres, rol FROM matrimonio_feligres WHERE id_matrimonio = ?");
        $stmtMF->execute([$id]);
        $data['participantes'] = $stmtMF->fetchAll(PDO::FETCH_ASSOC);
    }
} else {
    // Bautismo, Comunion, Confirmacion
    $stmt = $db->prepare("SELECT * FROM $tipo WHERE id_$tipo = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
}

echo json_encode($data);