<?php
require '../../config/db.php';
$db = (new Database())->getConnection();

$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(['error' => 'ID no proporcionado']);
    exit;
}

$stmt = $db->prepare("SELECT * FROM curso WHERE id_curso = ?");
$stmt->execute([$id]);

echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
