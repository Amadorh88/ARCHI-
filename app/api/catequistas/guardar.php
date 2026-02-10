<?php
require '../../config/db.php';
$db = (new Database())->getConnection();

$id = $_POST['id_catequista'] ?? null;
$nombre = trim($_POST['nombre'] ?? '');
$telefono = $_POST['telefono'] ?? null;
$especialidad = $_POST['especialidad'] ?? null;

if ($nombre === '') {
    echo json_encode(['success' => false, 'error' => 'El nombre es obligatorio']);
    exit;
}

if ($id) {
    $sql = "UPDATE catequista SET nombre=?, telefono=?, especialidad=? WHERE id_catequista=?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$nombre, $telefono, $especialidad, $id]);
} else {
    $sql = "INSERT INTO catequista (nombre, telefono, especialidad) VALUES (?,?,?)";
    $stmt = $db->prepare($sql);
    $stmt->execute([$nombre, $telefono, $especialidad]);
}
echo json_encode(['success' => true]);