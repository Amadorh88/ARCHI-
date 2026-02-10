<?php
header('Content-Type: application/json');
require_once '../../config/db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(["error" => "ID no proporcionado"]);
    exit;
}
$db = (new Database())->getConnection();
$id = intval($_GET['id']);

$sql = "SELECT * FROM bautismo WHERE id_bautismo = ?";
$stmt = $conexion->prepare($sql);

if (!$stmt) {
    echo json_encode(["error" => "Error en la consulta"]);
    exit;
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["error" => "Registro no encontrado"]);
    exit;
}

$data = $result->fetch_assoc();

echo json_encode($data);

$stmt->close();
$conexion->close();
?>
