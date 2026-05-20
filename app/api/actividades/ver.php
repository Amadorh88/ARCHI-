<?php
require_once '../../config/db.php';
header('Content-Type: application/json');

// Corregido: Buscamos 'id' que es lo que manda el JavaScript
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    http_response_code(400);
    echo json_encode(['error' => 'ID inválido']);
    exit;
}

try {
    $db = new Database();
    $pdo = $db->getConnection();

    // Consulta corregida con INNER JOIN para traer el nombre real del usuario
    $stmt = $pdo->prepare("SELECT a.id_actividad, a.id_usuario, u.nombre as nombre_usuario, a.accion, a.modulo, a.fecha, a.ip 
                           FROM actividades a 
                           INNER JOIN usuarios u ON a.id_usuario = u.id 
                           WHERE a.id_actividad = :id_actividad LIMIT 1");
                           
    $stmt->bindParam(':id_actividad', $_GET['id'], PDO::PARAM_INT);
    $stmt->execute();

    $actividad = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$actividad) {
        http_response_code(404);
        echo json_encode(['error' => 'Actividad no encontrada']);
        exit;
    }

    echo json_encode($actividad);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener actividad: ' . $e->getMessage()]);
}