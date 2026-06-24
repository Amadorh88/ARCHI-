<?php
// api/catequesis/obtener_aptos.php
header('Content-Type: application/json');
require_once '../../config/db.php';

$tipo = $_GET['tipo'] ?? '';

if (empty($tipo)) {
    echo json_encode(['error' => 'Tipo de catequesis no especificado']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $sql = "";
    $mensaje = "";
    
    switch ($tipo) {
        case 'Bautismal':
            $sql = "SELECT f.id_feligres, f.nombre_completo, f.genero, f.fecha_nacimiento
                    FROM feligres f
                    WHERE f.id_feligres NOT IN (
                        SELECT DISTINCT c.id_feligres 
                        FROM catequesis c 
                        WHERE c.tipo = 'Bautismal'
                    )
                    ORDER BY f.nombre_completo";
            $mensaje = "Feligreses que NO han recibido catequesis bautismal";
            break;
            
        case 'Primera comunión':
            $sql = "SELECT f.id_feligres, f.nombre_completo, f.genero, f.fecha_nacimiento
                    FROM feligres f
                    INNER JOIN bautismo b ON f.id_feligres = b.id_feligres
                    WHERE b.estado = 1
                    AND f.id_feligres NOT IN (
                        SELECT DISTINCT c.id_feligres 
                        FROM catequesis c 
                        WHERE c.tipo = 'Primera comunión'
                    )
                    ORDER BY f.nombre_completo";
            $mensaje = "Feligreses bautizados que NO han recibido catequesis de Primera Comunión";
            break;
            
        case 'Confirmación':
            $sql = "SELECT f.id_feligres, f.nombre_completo, f.genero, f.fecha_nacimiento
                    FROM feligres f
                    INNER JOIN comunion com ON f.id_feligres = com.id_feligres
                    WHERE com.estado = 1
                    AND f.id_feligres NOT IN (
                        SELECT DISTINCT c.id_feligres 
                        FROM catequesis c 
                        WHERE c.tipo = 'Confirmación'
                    )
                    ORDER BY f.nombre_completo";
            $mensaje = "Feligreses que han hecho la Comunión y NO han recibido catequesis de Confirmación";
            break;
            
        case 'Matrimonial':
            $sql = "SELECT f.id_feligres, f.nombre_completo, f.genero, f.fecha_nacimiento
                    FROM feligres f
                    INNER JOIN confirmacion con ON f.id_feligres = con.id_feligres
                    WHERE con.estado = 1
                    AND f.id_feligres NOT IN (
                        SELECT DISTINCT c.id_feligres 
                        FROM catequesis c 
                        WHERE c.tipo = 'Matrimonial'
                    )
                    ORDER BY f.nombre_completo";
            $mensaje = "Feligreses confirmados que NO han recibido catequesis prematrimonial";
            break;
            
        default:
            $sql = "SELECT id_feligres, nombre_completo, genero, fecha_nacimiento FROM feligres WHERE 1=0";
            $mensaje = "Tipo de catequesis no válido";
    }

    $stmt = $db->prepare($sql);
    $stmt->execute();
    $feligreses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $feligreses,
        'mensaje' => $mensaje,
        'total' => count($feligreses)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'data' => []
    ]);
}
?>