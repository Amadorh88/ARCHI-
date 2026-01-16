<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$database = new Database();
$bd = $database->getConnection();

$id = $_GET['id'] ?? 0;

try {
    $query = "SELECT * FROM feligreses WHERE id = :id";
    $stmt = $bd->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    $feligres = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($feligres) {
        echo json_encode(['success' => true, 'feligres' => $feligres]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Feligrés no encontrado']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos']);
}
?>