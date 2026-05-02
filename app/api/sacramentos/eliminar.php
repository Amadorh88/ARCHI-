<?php
require '../../config/db.php';
header('Content-Type: application/json');

$conexion = (new Database())->getConnection();

$id = $_POST['id'] ?? $_GET['id'] ?? null;
$tipo = strtolower(trim($_POST['tipo'] ?? $_GET['tipo'] ?? ''));

if (!$id || !$tipo) {
    echo json_encode([
        'success' => false,
        'error' => 'Datos incompletos'
    ]);
    exit;
}

try {

    switch ($tipo) {

        case 'bautismo':
            $table = "bautismo";
            $idField = "id_bautismo";
            break;

        case 'comunion':
        case 'primera_comunion':
        case 'primera comunión':
            $table = "comunion";
            $idField = "id_comunion";
            break;

        case 'confirmacion':
        case 'confirmación':
            $table = "confirmacion";
            $idField = "id_confirmacion";
            break;

        case 'matrimonio':
            $table = "matrimonio";
            $idField = "id_matrimonio";
            break;

        default:
            throw new Exception("Tipo no válido");
    }

    // 🔎 1. Verificar estado
    $check = $conexion->prepare("SELECT estado FROM $table WHERE $idField = ?");
    $check->execute([$id]);
    $row = $check->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        throw new Exception("Registro no encontrado");
    }

    // 🔒 regla: solo borrar si estado = 0
    $estado = $row['estado'];

    if ($estado != 0 && $estado != 'inactivo') {
        throw new Exception("No se puede eliminar: el registro está activo");
    }

    // 🧨 2. DELETE real
    $sql = "DELETE FROM $table WHERE $idField = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$id]);

    echo json_encode([
        'success' => true,
        'message' => 'Registro eliminado permanentemente'
    ]);

} catch (Throwable $e) {

    http_response_code(400);

    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}