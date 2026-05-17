<?php
require '../../config/db.php';

$tipo = $_GET['tipo'] ?? '';
function limpiarTexto($tipo){
    $tipo = mb_strtolower($tipo,"UTF-8");
    $buscar = ["á", "é","í","ó","ú"];
    $reemplazar = ["a","e","i","o","u"];
    $tipo = str_replace($buscar,$reemplazar, $tipo);
    return $tipo;
}
$tipo =  limpiarTexto($tipo);
try {
    $db = (new Database())->getConnection();

    switch ($tipo) {
        case 'bautismo':
            $sql = "SELECT * FROM feligres f
                    WHERE NOT EXISTS (SELECT 1 FROM bautismo b WHERE b.id_feligres = f.id_feligres)";
            break;
        case 'comunion':           
             $sql = "SELECT f.* 
                    FROM feligres f
                    INNER JOIN bautismo b ON f.id_feligres = b.id_feligres
                    LEFT JOIN comunion c ON f.id_feligres = c.id_feligres
                    WHERE b.estado = 1 
                    AND (c.estado = 0 OR c.id_feligres IS NULL);";
            break;
        case 'confirmacion':
              $sql = " SELECT f.* 
                     FROM feligres f
                     INNER JOIN comunion c ON f.id_feligres = c.id_feligres
                     LEFT JOIN confirmacion co ON f.id_feligres = co.id_feligres
                     WHERE c.estado = 1 
                     AND (co.estado = 0 OR co.id_feligres IS NULL);
       ";
            break;
        case 'matrimonio':
             $sql = "SELECT f.* 
                    FROM feligres f
                    INNER JOIN confirmacion co ON f.id_feligres = co.id_feligres
                    INNER JOIN matrimonio_feligres mf ON f.id_feligres = mf.id_feligres
                    LEFT JOIN matrimonio m ON mf.id_matrimonio = m.id_matrimonio
                    WHERE co.estado = 1 
                    AND (m.estado = 0 OR m.id_matrimonio IS NULL);";        
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
