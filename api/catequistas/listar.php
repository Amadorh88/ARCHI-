<?php
require '../../config/db.php';
$db = (new Database())->getConnection();

$stmt = $db->query("SELECT id_catequista, nombre, telefono, especialidad FROM catequista ORDER BY nombre ASC");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));