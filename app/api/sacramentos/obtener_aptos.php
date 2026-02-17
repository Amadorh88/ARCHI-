<?php
require '../../config/db.php';

$tipo = $_GET['tipo'] ?? '';

try {
    $db = (new Database())->getConnection();

    switch ($tipo) {
        case 'Bautismo':
            $sql = "SELECT * FROM feligres f
                    WHERE NOT EXISTS (SELECT 1 FROM bautismo b WHERE b.id_feligres = f.id_feligres)";
            break;
        case 'Comunión':
            $sql = "SELECT * FROM feligres f
                    WHERE NOT EXISTS (SELECT 1 FROM comunion c WHERE c.id_feligres = f.id_feligres)";
            break;
        case 'Confirmación':
            $sql = "SELECT * FROM feligres f
                    WHERE NOT EXISTS (SELECT 1 FROM confirmacion cf WHERE cf.id_feligres = f.id_feligres)";
            break;
        case 'Matrimonio':
            $sql = "SELECT * FROM feligres"; // Todos, se filtra en JS al seleccionar esposo/esposa
            break;
        default:
            $sql = "SELECT * FROM feligres LIMIT 0"; // Ninguno
    }

    $stmt = $db->query($sql);
    $feligreses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($feligreses);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([]);
}
