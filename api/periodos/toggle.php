<?php
require '../../config/db.php';
$db = (new Database())->getConnection();

$id = $_GET['id'] ?? null;
if (!$id) {
    echo json_encode(['success'=>false,'message'=>'ID no proporcionado']);
    exit;
}

try {
    $db->beginTransaction();

    // Obtener el periodo seleccionado
    $stmt = $db->prepare("SELECT * FROM periodo WHERE id_periodo = ?");
    $stmt->execute([$id]);
    $periodo = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$periodo) throw new Exception("Periodo no encontrado");

    if ($periodo['estado'] === 'activo') {
        // Desactivar
        $stmt = $db->prepare("UPDATE periodo SET estado = 'finalizado' WHERE id_periodo = ?");
        $stmt->execute([$id]);
    } else {
        // Activar: primero desactivar todos los demás
        $stmt = $db->prepare("UPDATE periodo SET estado = 'finalizado' WHERE estado = 'activo'");
        $stmt->execute();

        // Activar el seleccionado
        $stmt = $db->prepare("UPDATE periodo SET estado = 'activo' WHERE id_periodo = ?");
        $stmt->execute([$id]);
    }

    $db->commit();
    echo json_encode(['success'=>true]);

} catch (Exception $e) {
    $db->rollBack();
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
