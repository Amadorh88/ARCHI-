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
    response(400, ['success' => false, 'message' => 'ID inválido']);
}

$id = (int) $_GET['id'];

try {
    $db = new Database();
    $pdo = $db->getConnection();

    $pdo->beginTransaction();

    // Verificar existencia del usuario
    $stmt = $pdo->prepare("SELECT id, nombre, rol FROM usuarios WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        $pdo->rollBack();
        response(404, ['success' => false, 'message' => 'Usuario no encontrado']);
    }

    // Evitar auto eliminación (opcional)
    if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $id) {
        $pdo->rollBack();
        response(403, ['success' => false, 'message' => 'No puedes eliminar tu propio usuario']);
    }

    // Eliminar usuario
    $delete = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
    $delete->execute([':id' => $id]);

    // Registrar actividad
    $log = $pdo->prepare("
        INSERT INTO actividades (id_usuario, accion, modulo, ip)
        VALUES (:id_usuario, :accion, :modulo, :ip)
    ");

    $log->execute([
        ':id_usuario' => $_SESSION['usuario_id'] ?? null,
        ':accion'     => 'ELIMINAR USUARIO ID ' . $id,
        ':modulo'     => 'USUARIOS',
        ':ip'         => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'
    ]);

    $pdo->commit();

    response(200, [
        'success' => true,
        'message' => 'Usuario eliminado correctamente'
    ]);

} catch (Throwable $e) {

    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log($e->getMessage());

    response(500, [
        'success' => false,
        'message' => 'Error interno del servidor'
    ]);
}