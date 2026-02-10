<?php
require '../../config/db.php';
$db = (new Database())->getConnection();

$sql = "SELECT c.id_curso, c.nombre, c.duracion, c.observaciones, ca.nombre AS nombre_catequista
        FROM curso c
        LEFT JOIN catequista ca ON ca.id_catequista = c.id_catequista
        ORDER BY c.nombre ASC";

$stmt = $db->prepare($sql);
$stmt->execute();
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));