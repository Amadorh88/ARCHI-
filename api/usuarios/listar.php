<?php
require_once '../../config/db.php';
header('Content-Type: application/json');

try {
    $db = new Database();
    $pdo = $db->getConnection();

    $stmt = $pdo->prepare("SELECT id, nombre, usuario, rol, estado, fecha_registro FROM usuarios ORDER BY id DESC");
    $stmt->execute();

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al listar usuarios']);
}
