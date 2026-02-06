<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('HTTP/1.1 403 Forbidden');
    exit();
}

include_once 'db.php';
$database = new Database();
$db = $database->getConnection();

$feligres_id = $_GET['feligres_id'] ?? 0;

$query = "SELECT id FROM sacramentos WHERE feligres_id = :feligres_id AND tipo = 'bautismo' LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(':feligres_id', $feligres_id);
$stmt->execute();

$bautismo = $stmt->fetch(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode([
    'bautismo_id' => $bautismo ? $bautismo['id'] : null
]);
?>