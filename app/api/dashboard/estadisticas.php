<?php
header('Content-Type: application/json');
require_once "../../config/db.php";

$database = new Database();
$db = $database->getConnection();

$response = [
    "bautismos" => 0,
    "comuniones" => 0,
    "confirmaciones" => 0,
    "matrimonios" => 0
];

try {
    // Conteo de Bautismos
    $stmt = $db->query("SELECT COUNT(*) as total FROM bautismo");
    $response['bautismos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Conteo de Comuniones
    $stmt = $db->query("SELECT COUNT(*) as total FROM comunion");
    $response['comuniones'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Conteo de Confirmaciones
    $stmt = $db->query("SELECT COUNT(*) as total FROM confirmacion");
    $response['confirmaciones'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Conteo de Matrimonios (Solo los activos según Kiyosaki)
    $stmt = $db->query("SELECT COUNT(*) as total FROM matrimonio WHERE estado = 'activo'");
    $response['matrimonios'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    echo json_encode($response);

} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>