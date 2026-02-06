<?php
session_start();
require_once '../config/db.php'; // Ajusta la ruta a tu conexión

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['usuario_id'])) {
    $database = new Database();
    $db = $database->getConnection();
    
    $id = $_POST['id'];
    $estado = $_POST['estado'];

    $query = "UPDATE usuarios SET estado = :estado WHERE id = :id";
    $stmt = $db->prepare($query);
    
    $stmt->bindParam(':estado', $estado, PDO::PARAM_INT);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se pudo actualizar el estado']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
}