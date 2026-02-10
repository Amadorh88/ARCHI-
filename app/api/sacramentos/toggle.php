<?php
require '../../config/db.php';
header('Content-Type: application/json');

$db = (new Database())->getConnection();
$id = $_GET['id'] ?? null;

if (!$id) exit(json_encode(["error" => "ID faltante"]));

try {
    // Obtenemos estado actual
    $stmt = $db->prepare("SELECT estado FROM matrimonio WHERE id_matrimonio = ?");
    $stmt->execute([$id]);
    $actual = $stmt->fetchColumn();

    $nuevo = ($actual === 'activo') ? 'inactivo' : 'activo';

    $upd = $db->prepare("UPDATE matrimonio SET estado = ? WHERE id_matrimonio = ?");
    $upd->execute([$nuevo, $id]);

    echo json_encode(["success" => true, "nuevo_estado" => $nuevo]);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}