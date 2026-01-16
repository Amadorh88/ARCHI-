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

$stmt = $db->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($usuario) {
    // Obtener última actividad
    $actividad = $db->prepare("SELECT accion, fecha FROM actividades 
                              WHERE id_usuario = ? 
                              ORDER BY fecha DESC LIMIT 1");
    $actividad->execute([$id]);
    $ultima = $actividad->fetch(PDO::FETCH_ASSOC);
    
    $usuario['ultima_actividad'] = $ultima ? $ultima['accion'] . ' - ' . $ultima['fecha'] : null;
    
    echo json_encode(['success' => true, 'usuario' => $usuario]);
} else {
    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
}
?>