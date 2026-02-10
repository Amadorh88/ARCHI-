<?php
require '../../config/db.php';
header('Content-Type: application/json');

$db = (new Database())->getConnection();
$tipoCatequesis = $_GET['tipo'] ?? '';

// Mapeo de tipo de catequesis a su respectiva tabla de sacramento
$mapaTablas = [
    'Pre-bautismal'    => 'bautismo',
    'Primera comunión' => 'comunion',
    'Confirmación'     => 'confirmacion',
    'Matrimonial'      => 'matrimonio'
];

$tablaSacramento = $mapaTablas[$tipoCatequesis] ?? null;

try {
    if (!$tablaSacramento) {
        throw new Exception("Tipo de catequesis no válido.");
    }

    /* EXPLICACIÓN TÉCNICA (Hill):
       1. Buscamos feligreses con catequesis aprobada en periodo activo.
       2. Hacemos LEFT JOIN con la tabla del sacramento (bautismo, comunion, etc).
       3. Filtramos "WHERE s.id_feligres IS NULL" para traer solo a los que NO lo han recibido.
       Nota: Para matrimonio, la lógica cambia ligeramente porque se valida en matrimonio_feligres.
    */
    
    if ($tablaSacramento === 'matrimonio') {
        $query = "SELECT f.id_feligres, f.nombre_completo 
                  FROM feligres f
                  INNER JOIN catequesis c ON f.id_feligres = c.id_feligres
                  INNER JOIN periodo p ON c.id_periodo = p.id_periodo
                  LEFT JOIN matrimonio_feligres mf ON f.id_feligres = mf.id_feligres
                  LEFT JOIN matrimonio m ON mf.id_matrimonio = m.id_matrimonio AND m.estado = 'activo'
                  WHERE c.tipo = :tipo 
                  AND p.estado = 'activo'
                  AND m.id_matrimonio IS NULL";
    } else {
        $query = "SELECT f.id_feligres, f.nombre_completo 
                  FROM feligres f
                  INNER JOIN catequesis c ON f.id_feligres = c.id_feligres
                  INNER JOIN periodo p ON c.id_periodo = p.id_periodo
                  LEFT JOIN $tablaSacramento s ON f.id_feligres = s.id_feligres
                  WHERE c.tipo = :tipo 
                  AND p.estado = 'activo'
                  AND s.id_feligres IS NULL";
    }
    
    $stmt = $db->prepare($query);
    $stmt->execute([':tipo' => $tipoCatequesis]);
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($res);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(["error" => $e->getMessage()]);
}