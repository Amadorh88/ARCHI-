<?php
require '../../config/db.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    http_response_code(400);
    echo json_encode(["error" => "ID inválido"]);
    exit;
}

try {
    $db = (new Database())->getConnection();
    $id = $_GET['id'];
    
    $stmt = $db->prepare("SELECT id_feligres, nombre_completo, genero, fecha_nacimiento, lugar_nacimiento, nombre_padre, nombre_madre FROM feligres WHERE id_feligres = ?");
    $stmt->execute([$id]);
    $feligres = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode(["feligres" => $feligres]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error al obtener perfil"]);
}
?>