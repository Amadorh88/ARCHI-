<?php
require '../../config/db.php';
header('Content-Type: application/json');

$conexion = (new Database())->getConnection();

$id = $_POST['id'] ?? $_GET['id'] ?? null;

if (!$id) {
    echo json_encode([
        'success' => false,
        'error' => 'ID requerido'
    ]);
    exit;
}

try {

    // Verificar relaciones activas
    $tablas = [
        'bautismo',
        'catequesis',
        'comunion',
        'confirmacion'
    ];

    foreach ($tablas as $tabla) {

        $sqlCheck = "SELECT COUNT(*) FROM $tabla 
                     WHERE id_parroquia = ? AND estado = 1";

        $stmtCheck = $conexion->prepare($sqlCheck);
        $stmtCheck->execute([$id]);

        $total = $stmtCheck->fetchColumn();

        if ($total > 0) {
            throw new Exception(
                "No se puede eliminar: existen registros activos en $tabla"
            );
        }
    }

    // Si no hay relaciones activas → eliminar
    $sqlDelete = "DELETE FROM parroquia WHERE id_parroquia = ?";
    $stmtDelete = $conexion->prepare($sqlDelete);
    $stmtDelete->execute([$id]);

    echo json_encode([
        'success' => true,
        'message' => 'Parroquia eliminada correctamente'
    ]);

} catch (Throwable $e) {

    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}