<?php
require '../../config/db.php';
$db = (new Database())->getConnection();

$stmt = $db->query("
    SELECT 
        fecha_inicio, id_periodo,
        fecha_fin, 
        estado, 
        CONCAT(fecha_inicio, ' - ', fecha_fin) AS anio 
    FROM periodo 
    ORDER BY fecha_inicio DESC
");


echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));


/* $stmt = $db->query("
    SELECT 
        fecha_inicio, 
        fecha_fin, 
        estado,
        CASE 
            WHEN estado = 'activo' 
            THEN CONCAT(YEAR(fecha_inicio), ' - ', YEAR(fecha_fin))
            ELSE NULL
        END AS anio
    FROM periodo 
    ORDER BY fecha_inicio DESC
");
 */