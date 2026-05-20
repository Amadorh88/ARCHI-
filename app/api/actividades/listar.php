<?php
require_once '../../config/db.php';
header('Content-Type: application/json');

try {
    $db = new Database();
    $pdo = $db->getConnection();

    $stmt = $pdo->prepare(" SELECT  
        a.id_actividad,
        a.id_usuario,
        a.accion,
        a.modulo,
        a.fecha,
        a.ip,
        u.nombre AS usuario_nombre,
        u.rol AS usuario_rol
    FROM 
        actividades a
    LEFT JOIN 
        usuarios u ON a.id_usuario = u.id
    WHERE 1=1");
    $stmt->execute();

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al listar actividades']);
}
