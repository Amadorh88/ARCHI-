<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require '../../config/db.php';

try {

   /*  if (!isset($_SESSION['usuario_id'])) {
        throw new Exception("No autorizado");
    } */

    $db = (new Database())->getConnection();

    // --- Recuperar datos desde FormData ---
    if (!isset($_POST['id_catequesis'], $_POST['estado'])) {
        throw new Exception("Datos incompletos");
    }

    $id = filter_var($_POST['id_catequesis'], FILTER_VALIDATE_INT);
    $estado = filter_var($_POST['estado'], FILTER_VALIDATE_INT);

    if ($id === false || !in_array($estado, [0,1])) {
        throw new Exception("Datos inválidos");
    }

    // --- Actualizar estado ---
    $stmt = $db->prepare("
        UPDATE catequesis
        SET estado = :estado
        WHERE id_catequesis = :id
    ");

    $stmt->bindParam(':estado', $estado, PDO::PARAM_INT);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if (!$stmt->execute()) {
        throw new Exception("No se pudo actualizar");
    }

    echo json_encode([
        "success" => true,
        "message" => "Estado actualizado correctamente"
    ]);

} catch (Exception $e) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
