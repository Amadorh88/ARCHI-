<?php
session_start();
require '../../config/db.php';
header('Content-Type: application/json');

// Validar sesión
if (!isset($_SESSION['rol'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

$db = (new Database())->getConnection();

$query = "
    SELECT 
        b.id_bautismo AS id,
        'Bautismo' AS tipo,
        f.nombre_completo AS feligres,
        b.fecha,
        b.registro,
        'N/A' AS estado
    FROM bautismo b
    INNER JOIN feligres f ON b.id_feligres = f.id_feligres

    UNION ALL

    SELECT 
        c.id_comunion AS id,
        'Comunión' AS tipo,
        f.nombre_completo AS feligres,
        c.fecha,
        c.registro,
        'N/A' AS estado
    FROM comunion c
    INNER JOIN feligres f ON c.id_feligres = f.id_feligres

    UNION ALL

    SELECT 
        m.id_matrimonio AS id,
        'Matrimonio' AS tipo,
        'Pareja' AS feligres,
        m.fecha,
        m.registro,
        m.estado
    FROM matrimonio m
";

$stmt = $db->query($query);

$response = [
    'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
    'rol'  => $_SESSION['rol']
];

echo json_encode($response);
