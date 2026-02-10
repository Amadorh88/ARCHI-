<?php
header('Content-Type: application/json; charset=utf-8');
require '../../config/db.php';

try {
    $db = (new Database())->getConnection();

    $sql = "SELECT 
                ca.id_catequesis, 
                f.nombre_completo AS nombre_feligres, 
                IFNULL(CONCAT(pe.fecha_inicio,'-' , pe.fecha_fin), 'Sin Periodo') AS anio,
                IFNULL(cu.nombre, 'No asignado') AS nombre_curso, 
                IFNULL(pa.nombre, '') AS nombre_parroquia,
                ca.tipo,
                ca.estado,
                ca.id_feligres,
                ca.id_curso,
                ca.id_periodo,
                ca.id_parroquia
            FROM catequesis ca
            INNER JOIN feligres f ON ca.id_feligres = f.id_feligres
            LEFT JOIN periodo pe ON ca.id_periodo = pe.id_periodo
            LEFT JOIN curso cu ON ca.id_curso = cu.id_curso
            LEFT JOIN parroquia pa ON ca.id_parroquia = pa.id_parroquia
            ORDER BY pe.fecha_inicio DESC, f.nombre_completo ASC";

    $stmt = $db->query($sql);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($resultados);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "error" => true,
        "message" => $e->getMessage()
    ]);
}