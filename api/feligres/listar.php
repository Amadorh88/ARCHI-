<?php
require '../../config/db.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $db = (new Database())->getConnection();
    
    $stmt = $db->query("SELECT id_feligres, nombre_completo, genero, fecha_nacimiento, lugar_nacimiento, nombre_padre, nombre_madre FROM feligres ORDER BY id_feligres DESC");
    $feligres = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($feligres);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error al listar feligreses"]);
}
?>