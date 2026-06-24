<?php
// api/actividades/ver.php
require '../../config/db.php';
header('Content-Type: application/json; charset=utf-8');

$database = new Database();
$db = $database->getConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    echo json_encode(['error' => 'ID de actividad no válido']);
    exit;
}

try {
    // Consulta con JOIN para obtener el nombre del usuario
    $query = "
        SELECT 
            a.id_actividad,
            a.id_usuario,
            u.nombre as nombre_usuario,
            u.usuario as usuario,
            u.rol as rol_usuario,
            a.accion,
            a.modulo,
            a.fecha
        FROM actividades a
        LEFT JOIN usuarios u ON a.id_usuario = u.id
        WHERE a.id_actividad = ?
    ";

    $stmt = $db->prepare($query);
    $stmt->execute([$id]);
    $actividad = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$actividad) {
        echo json_encode(['error' => 'Actividad no encontrada']);
        exit;
    }

    // Formatear la fecha para mejor visualización
    if (isset($actividad['fecha'])) {
        $fecha = new DateTime($actividad['fecha']);
        $actividad['fecha_formateada'] = $fecha->format('d/m/Y H:i:s');
        $actividad['fecha_humana'] = $fecha->format('l, d \\d\\e F \\d\\e Y \\a \\l\\a\\s H:i');
    }

    echo json_encode($actividad);

} catch (PDOException $e) {
    echo json_encode([
        'error' => 'Error al obtener la actividad',
        'message' => $e->getMessage()
    ]);
}
?>