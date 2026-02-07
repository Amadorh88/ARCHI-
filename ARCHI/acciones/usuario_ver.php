<?php

session_start();
require_once '../config/db.php';

$database = new Database();
$db = $database->getConnection();

$id = (int)$_GET['id'];

$stmt = $db->prepare("
    SELECT nombre, dni, usuario, rol, estado, fecha_registro
    FROM usuarios
    WHERE id = ?
");
$stmt->execute([$id]);

echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
