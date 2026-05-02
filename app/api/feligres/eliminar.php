<?php
declare(strict_types=1);

require_once '../../config/db.php';
session_start();

header('Content-Type: application/json; charset=utf-8');

function response(int $status, array $data): void
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    response(400, ['error' => 'ID inválido']);
}

$id = (int) $_GET['id'];

try {
    $db = new Database();
    $pdo = $db->getConnection();

    $pdo->beginTransaction();

    // verificar existencia
    $stmt = $pdo->prepare("
        SELECT id_feligres, nombre_completo
        FROM feligres
        WHERE id_feligres = :id
        LIMIT 1
    ");
    $stmt->execute([':id' => $id]);

    $feligres = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$feligres) {
        $pdo->rollBack();
        response(404, ['error' => 'Feligrés no encontrado']);
    }

    // validar dependencias
    $dependencias = [
        'bautismo',
        'catequesis',
        'comunion',
        'confirmacion',
        'matrimonio_feligres',
        'pago'
    ];

    foreach ($dependencias as $tabla) {

        $sql = "SELECT COUNT(*) FROM {$tabla} WHERE id_feligres = :id";
        $check = $pdo->prepare($sql);
        $check->execute([':id' => $id]);

        if ((int)$check->fetchColumn() > 0) {
            $pdo->rollBack();

            response(409, [
                'error' => 'No se puede eliminar. El feligrés tiene registros asociados en ' . $tabla
            ]);
        }
    }

    // eliminar
    $delete = $pdo->prepare("
        DELETE FROM feligres
        WHERE id_feligres = :id
    ");
    $delete->execute([':id' => $id]);

    // auditoría
    $log = $pdo->prepare("
        INSERT INTO actividades (id_usuario, accion, modulo, ip)
        VALUES (:id_usuario, :accion, :modulo, :ip)
    ");

    $log->execute([
        ':id_usuario' => $_SESSION['usuario_id'] ?? null,
        ':accion' => 'ELIMINÓ FELIGRÉS: ' . $feligres['nombre_completo'],
        ':modulo' => 'FELIGRES',
        ':ip' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'
    ]);

    $pdo->commit();

    response(200, [
        'success' => true,
        'message' => 'Feligrés eliminado correctamente'
    ]);

} catch (Throwable $e) {

    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log($e->getMessage());

    response(500, [
        'error' => 'Error interno del servidor'
    ]);
}