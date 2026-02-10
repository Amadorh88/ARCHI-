<?php
require '../../config/db.php';
$db = (new Database())->getConnection();

$id = $_GET['id'];

$stmt = $db->prepare("SELECT * FROM feligres WHERE id_feligres=?");
$stmt->execute([$id]);

echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
