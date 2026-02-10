<?php
require '../../config/db.php';
$db = (new Database())->getConnection();

$id = $_GET['id'];

$stmt = $db->prepare("SELECT * FROM feligres WHERE id_feligres=?");
$stmt->execute([$id]);
$feligres = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    "feligres"=>$feligres
]);
