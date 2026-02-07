<?php

session_start();
require_once '../config/db.php';

$database = new Database();
$db = $database->getConnection();

$id = (int)$_GET['id'];

$stmt = $db->prepare("
    SELECT 
        f.id_feligres,
        f.nombre_completo,
        f.nombre_padre,
        f.nombre_madre,
        f.fecha_nacimiento,
        f.lugar_nacimiento,
        b.id_bautismo, b.registro AS bautismo_registro, b.fecha AS bautismo_fecha,
        c1.id_catequesis, c1.nombre_catequesis, c1.tipo AS catequesis_tipo,
        com.id_comunion, com.fecha AS com_fecha,
        conf.id_confirmacion, conf.fecha AS conf_fecha,
        m.id_matrimonio, m.fecha AS mat_fecha, m.lugar AS mat_lugar
    FROM feligres f
    LEFT JOIN bautismo b ON b.id_feligres = f.id_feligres
    LEFT JOIN catequesis c1 ON c1.id_feligres = f.id_feligres
    LEFT JOIN comunion com ON com.id_feligres = f.id_feligres
    LEFT JOIN confirmacion conf ON conf.id_feligres = f.id_feligres
    LEFT JOIN matrimonio_feligres mf ON mf.id_feligres = f.id_feligres
    LEFT JOIN matrimonio m ON m.id_matrimonio = mf.id_matrimonio
    WHERE f.id_feligres = ?
");

$stmt->execute([$id]);

echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
