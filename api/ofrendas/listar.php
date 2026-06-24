<?php
require '../../config/db.php';
header('Content-Type: application/json');

$db = (new Database())->getConnection();

try {
    // Hill: La precisión en el reporte es la base de la confianza
    $sql = "SELECT p.*, f.nombre_completo as feligres_nombre 
            FROM pago p 
            LEFT JOIN feligres f ON p.id_feligres = f.id_feligres 
            ORDER BY p.id_pago DESC";
            
    $stmt = $db->query($sql);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}