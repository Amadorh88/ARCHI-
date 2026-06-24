<?php
require '../../config/db.php';
$db = (new Database())->getConnection();

$stmt = $db->query(" SELECT 
            id_parroquia,
            nombre,
            direccion,
            telefono
        FROM parroquia
        ORDER BY nombre ASC");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

 