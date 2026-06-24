<?php
require '../../config/db.php';
header('Content-Type: application/json');

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if(!$id) {
    echo json_encode(["error" => "ID no válido"]);
    exit;
}

$db = (new Database())->getConnection();

// Cialdini: La autoridad de los datos precisos evita malentendidos
$stmt = $db->prepare("SELECT * FROM pago WHERE id_pago = ?");
$stmt->execute([$id]);

$pago = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode($pago ? $pago : ["error" => "No encontrado"]);