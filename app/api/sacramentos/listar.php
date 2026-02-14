<?php
session_start();
$rol = $_SESSION['rol'];
require '../../config/db.php';
header('Content-Type: application/json');

$db = (new Database())->getConnection();

$query = "
    SELECT id_bautismo as id, 'Bautismo' as tipo, f.nombre_completo as feligres, fecha, registro, 'N/A' as estado 
    FROM bautismo b JOIN feligres f ON b.id_feligres = f.id_feligres
    UNION
    SELECT id_comunion, 'Comunión', f.nombre_completo, fecha, registro, 'N/A'
    FROM comunion c JOIN feligres f ON c.id_feligres = f.id_feligres
    UNION
    SELECT id_matrimonio, 'Matrimonio', 'Pareja' as feligres, fecha, registro, estado
    FROM matrimonio
";

$stmt = $db->query($query);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));