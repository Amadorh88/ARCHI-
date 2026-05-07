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

    $sql = "DELETE FROM pago WHERE id_pago = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$id]);

    echo json_encode([
        'success' => true,
        'message' => 'Pago eliminado correctamente'
    ]);

} catch (Throwable $e) {

    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}