<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode([]);
    exit;
}

$database = new Database();
$db = $database->getConnection();

$busqueda = $_POST['busqueda'] ?? '';
$rol = $_POST['rol'] ?? '';
$estado = $_POST['estado'] ?? '';

$query = "SELECT id, nombre, dni, usuario, rol, estado, fecha_registro
          FROM usuarios
          WHERE 1=1";
$params = [];

if ($busqueda !== '') {
    $query .= " AND (nombre LIKE :b OR usuario LIKE :b OR dni LIKE :b)";
    $params[':b'] = "%$busqueda%";
}

if ($rol !== '') {
    $query .= " AND rol = :rol";
    $params[':rol'] = $rol;
}

if ($estado !== '') {
    $query .= " AND estado = :estado";
    $params[':estado'] = $estado;
}

$query .= " ORDER BY id DESC LIMIT 50";

$stmt = $db->prepare($query);
$stmt->execute($params);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
