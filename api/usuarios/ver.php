<?php
require_once '../../config/db.php';
header('Content-Type: application/json');

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    http_response_code(400);
    echo json_encode(['error' => 'ID inválido']);
    exit;
}

try {
    $db = new Database();
    $pdo = $db->getConnection();

    $stmt = $pdo->prepare("SELECT id, nombre, dni, usuario, rol, estado, fecha_registro FROM usuarios WHERE id = :id LIMIT 1");
    $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
    $stmt->execute();

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        http_response_code(404);
        echo json_encode(['error' => 'Usuario no encontrado']);
        exit;
    }

    echo json_encode($usuario);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener usuario']);
}
