<?php
require '../../config/db.php';
header('Content-Type: application/json');

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if(!$id) exit;

$db = (new Database())->getConnection();

$stmt = $db->prepare("SELECT * FROM ministros WHERE id_ministro = ?");
$stmt->execute([$id]);

echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
