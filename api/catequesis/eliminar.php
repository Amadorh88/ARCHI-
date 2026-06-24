<?php
require '../../config/db.php';
header('Content-Type: application/json');

$conexion = (new Database())->getConnection();

$id = $_POST['id'] ?? null;

if (!$id) {
    echo json_encode([
        'success' => false,
        'error' => 'ID requerido'
    ]);
    exit;
}

try {

    $sql = "DELETE FROM catequesis WHERE id_catequesis = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$id]);

    echo json_encode([
        'success' => true,
        'message' => 'Catequesis eliminado correctamente'
    ]);

} catch (PDOException $e) {

    // Error 23000 = foreign key constraint
    if ($e->getCode() == 23000) {
        echo json_encode([
            'success' => false,
            'error' => 'No se puede eliminar: el Catequesis está asociado a Curso'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}