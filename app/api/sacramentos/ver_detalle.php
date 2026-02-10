<?php
require '../../config/db.php';
header('Content-Type: application/json');

$id = $_GET['id'];
$tipo = strtolower($_GET['tipo']);
$db = (new Database())->getConnection();

// Query Base con Ministros y Parroquias
$sql = "";
if ($tipo === 'matrimonio') {
    $sql = "SELECT m.*, min.nombre_completo as ministro_nombre, min.tipo as ministro_rango,
            (SELECT GROUP_CONCAT(CONCAT(f.nombre_completo, ' (', mf.rol, ')') SEPARATOR '|') 
             FROM matrimonio_feligres mf 
             JOIN feligres f ON mf.id_feligres = f.id_feligres 
             WHERE mf.id_matrimonio = m.id_matrimonio) as participantes
            FROM matrimonio m
            LEFT JOIN ministros min ON m.id_ministro = min.id_ministro
            WHERE m.id_matrimonio = ?";
} else {
    $tabla = $tipo;
    $sql = "SELECT t.*, f.nombre_completo as feligres_nombre, f.nombre_padre, f.nombre_madre, 
            f.fecha_nacimiento, f.lugar_nacimiento,
            min.nombre_completo as ministro_nombre, min.tipo as ministro_rango,
            p.nombre as parroquia_nombre, p.direccion as parroquia_dir
            FROM $tabla t
            JOIN feligres f ON t.id_feligres = f.id_feligres
            LEFT JOIN ministros min ON t.id_ministro = min.id_ministro
            LEFT JOIN parroquia p ON t.id_parroquia = p.id_parroquia
            WHERE t.id_$tabla = ?";
}

$stmt = $db->prepare($sql);
$stmt->execute([$id]);
echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));