<?php
require '../../config/db.php';
$db = (new Database())->getConnection();

$stmt = $db->prepare("SELECT * FROM feligres ORDER BY id_feligres DESC");
$stmt->execute();

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
