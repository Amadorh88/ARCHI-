<?php
require_once '../config/db.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    exit(json_encode(['success' => false, 'message' => 'No autorizado']));
}

$database = new Database();
$db = $database->getConnection();

$id = $_GET['id'] ?? 0;

if (!$id) {
    exit(json_encode(['success' => false, 'message' => 'ID requerido']));
}

// Generar nueva contraseña aleatoria
$newPassword = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
$password_hash = password_hash($newPassword, PASSWORD_DEFAULT);

$stmt = $db->prepare("UPDATE usuarios SET contraseña = ? WHERE id = ?");
$success = $stmt->execute([$password_hash, $id]);

if ($success) {
    // Registrar actividad
    $actividad = $db->prepare("INSERT INTO actividades (id_usuario, accion, modulo, ip) 
                              VALUES (?, ?, ?, ?)");
    $actividad->execute([
        $_SESSION['usuario_id'],
        "Reseteó contraseña del usuario ID: $id",
        'Usuarios',
        $_SERVER['REMOTE_ADDR']
    ]);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Contraseña reseteada exitosamente',
        'newPassword' => $newPassword // Solo para desarrollo, en producción no enviar
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al resetear contraseña']);
}
?>