<?php
require_once '../config/db.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    exit(json_encode(['success' => false, 'message' => 'No autorizado']));
}

$database = new Database();
$db = $database->getConnection();

// Obtener estadísticas
$totalUsuarios = $db->query("SELECT COUNT(*) as total FROM usuarios")->fetchColumn();
$usuariosActivos = $db->query("SELECT COUNT(*) as total FROM usuarios WHERE estado = 1")->fetchColumn();
$usuariosInactivos = $totalUsuarios - $usuariosActivos;
$usuariosRecientes = $db->query("SELECT COUNT(*) as total FROM usuarios WHERE fecha_registro >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();

// Distribución por rol
$distribucionQuery = $db->query("SELECT rol, COUNT(*) as cantidad FROM usuarios WHERE estado = 1 GROUP BY rol");
$distribucion = [];
$totalActivos = $usuariosActivos;

while ($row = $distribucionQuery->fetch(PDO::FETCH_ASSOC)) {
    $porcentaje = $totalActivos > 0 ? round(($row['cantidad'] / $totalActivos) * 100, 1) : 0;
    $distribucion[] = [
        'rol' => $row['rol'],
        'cantidad' => (int)$row['cantidad'],
        'porcentaje' => $porcentaje
    ];
}

// Retornar estadísticas
echo json_encode([
    'success' => true,
    'total' => (int)$totalUsuarios,
    'activos' => (int)$usuariosActivos,
    'inactivos' => (int)$usuariosInactivos,
    'recientes' => (int)$usuariosRecientes,
    'distribucion' => $distribucion,
    'timestamp' => date('Y-m-d H:i:s')
]);
?>