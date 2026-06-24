<?php
require '../../config/db.php';

header('Content-Type: application/json');

try {

    $db = (new Database())->getConnection();

    // Validar ID
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'ID inválido'
        ]);
        exit;
    }

    $id = (int) $_GET['id'];

    $stmt = $db->prepare("
        SELECT 
            c.*,
            f.nombre_completo AS nombre_feligres,
            p.nombre AS nombre_periodo,
            cu.nombre AS nombre_curso,
            pa.nombre AS nombre_parroquia
        FROM catequesis c
        LEFT JOIN feligres f ON c.id_feligres = f.id_feligres
        LEFT JOIN periodo p ON c.id_periodo = p.id_periodo
        LEFT JOIN curso cu ON c.id_curso = cu.id_curso
        LEFT JOIN parroquia pa ON c.id_parroquia = pa.id_parroquia
        WHERE c.id_catequesis = :id
        LIMIT 1
    ");

    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        echo json_encode([
            'success' => false,
            'message' => 'Registro no encontrado'
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'data' => $data
    ]);

} catch (Exception $e) {

    echo json_encode([
        'success' => false,
        'message' => 'Error del servidor',
        'error' => $e->getMessage() // quitar en producción si quieres
    ]);
}
