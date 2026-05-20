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
             /* $sql = "SELECT f.* 
                    FROM feligres f
                    INNER JOIN confirmacion co ON f.id_feligres = co.id_feligres
                    INNER JOIN matrimonio_feligres mf ON f.id_feligres = mf.id_feligres
                    LEFT JOIN matrimonio m ON mf.id_matrimonio = m.id_matrimonio
                    WHERE co.estado = 1 
                    AND (m.estado = 0 OR m.id_matrimonio IS NULL);";   */      
             $sql = "SELECT DISTINCT f.*
FROM feligres f
-- 1. Obligatorio: Debe tener la confirmación activa
INNER JOIN confirmacion co ON f.id_feligres = co.id_feligres AND co.estado = 1
-- 2. Traemos su participación en matrimonios (si existe)
LEFT JOIN matrimonio_feligres mf ON f.id_feligres = mf.id_feligres
LEFT JOIN matrimonio m ON mf.id_matrimonio = m.id_matrimonio 
WHERE 
    -- Caso A: Nunca ha estado en la tabla de matrimonios (está libre)
    mf.id_feligres IS NULL 
    OR 
    -- Caso B: Si aparece en la tabla, su rol DEBE ser testigo y NO puede ser esposo/esposa en un matrimonio activo
    (mf.rol = 'testigo' AND f.id_feligres NOT IN (
        SELECT id_feligres 
        FROM matrimonio_feligres mf2
        INNER JOIN matrimonio m2 ON mf2.id_matrimonio = m2.id_matrimonio
        WHERE mf2.rol IN ('esposo', 'esposa') AND m2.estado = 'activo' ))
        OR 
        
        ((mf.rol = 'esposo' OR mf.rol = 'esposa') AND f.id_feligres  IN (
        SELECT id_feligres 
        FROM matrimonio_feligres mf3
        INNER JOIN matrimonio m3 ON mf3.id_matrimonio = m3.id_matrimonio
        WHERE  m3.estado = 'inactivo'))
        ;";        
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
