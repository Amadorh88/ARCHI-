<?php
require '../../config/db.php';
$db = (new Database())->getConnection();

$id            = $_POST['id_curso'] ?? null;
$nombre        = trim($_POST['nombre'] ?? '');
$duracion      = $_POST['duracion'] ?? null;
$id_catequista = $_POST['id_catequista'] ?? null;
$observaciones = $_POST['observaciones'] ?? null;

if ($nombre === '') {
    echo json_encode(['error' => 'El nombre del curso es obligatorio']);
    exit;
}

if ($id) {
    // EDITAR
    $sql = "UPDATE curso
            SET nombre = ?, duracion = ?, id_catequista = ?, observaciones = ?
            WHERE id_curso = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        $nombre,
        $duracion,
        $id_catequista,
        $observaciones,
        $id
    ]);
} else {
    // CREAR
    $sql = "INSERT INTO curso (nombre, duracion, id_catequista, observaciones)
            VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        $nombre,
        $duracion,
        $id_catequista,
        $observaciones
    ]);
}

echo json_encode(['success' => true]);
