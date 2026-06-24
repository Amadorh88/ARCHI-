<?php
require '../../config/db.php';
header('Content-Type: application/json');

$db = (new Database())->getConnection();

$stmt = $db->query("SELECT * FROM ministros ORDER BY nombre_completo ASC");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
