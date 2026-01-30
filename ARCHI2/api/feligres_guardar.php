<?php
header('Content-Type: application/json');
session_start();
require '../config/db.php';

if (!isset($_SESSION['usuario_id'])) {
    exit(json_encode(['success'=>false,'message'=>'Sesión inválida']));
}

$accion = $_POST['accion'] ?? '';
$id     = $_POST['id'] ?? null;

$nombre = trim($_POST['nombre_completo'] ?? '');
$padre  = trim($_POST['nombre_padre'] ?? '');
$madre  = trim($_POST['nombre_madre'] ?? '');
$fecha  = $_POST['fecha_nacimiento'] ?: null;
$lugar  = trim($_POST['lugar_nacimiento'] ?? '');

if ($nombre === '') {
    exit(json_encode(['success'=>false,'message'=>'Nombre obligatorio']));
}

try {

    if ($accion === 'crear') {

        $stmt = $bd->prepare("
            INSERT INTO feligres
            (nombre_completo, nombre_padre, nombre_madre, fecha_nacimiento, lugar_nacimiento)
            VALUES (?,?,?,?,?)
        ");
        $stmt->execute([$nombre,$padre,$madre,$fecha,$lugar]);
    }

    if ($accion === 'editar') {

        if (!$id) {
            exit(json_encode(['success'=>false,'message'=>'ID inválido']));
        }

        $stmt = $bd->prepare("
            UPDATE feligres SET
                nombre_completo=?,
                nombre_padre=?,
                nombre_madre=?,
                fecha_nacimiento=?,
                lugar_nacimiento=?
            WHERE id_feligres=?
        ");
        $stmt->execute([$nombre,$padre,$madre,$fecha,$lugar,$id]);
    }

    echo json_encode(['success'=>true]);

} catch (PDOException $e) {
    echo json_encode([
        'success'=>false,
        'message'=>'Error BD',
        'error'=>$e->getMessage()
    ]);
}
