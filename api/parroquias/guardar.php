<?php
declare(strict_types=1);

require '../../config/db.php';
header('Content-Type: application/json');

try {

    $db = (new Database())->getConnection();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id = filter_input(INPUT_POST, 'id_parroquia', FILTER_VALIDATE_INT);
    $nombre = trim($_POST['nombre'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');

    if(empty($nombre)){
        throw new Exception("El nombre es obligatorio.");
    }

    // Evitar duplicados
    $stmt = $db->prepare("SELECT COUNT(*) FROM parroquia WHERE nombre = ? AND id_parroquia != ?");
    $stmt->execute([$nombre, $id ?? 0]);

    if($stmt->fetchColumn() > 0){
        throw new Exception("Ya existe una parroquia con ese nombre.");
    }

    if(!$id){

        $stmt = $db->prepare("
            INSERT INTO parroquia (nombre, direccion, telefono)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$nombre, $direccion ?: null, $telefono ?: null]);

        $mensaje = "Parroquia creada correctamente.";

    } else {

        $stmt = $db->prepare("
            UPDATE parroquia
            SET nombre = ?, direccion = ?, telefono = ?
            WHERE id_parroquia = ?
        ");
        $stmt->execute([$nombre, $direccion ?: null, $telefono ?: null, $id]);

        $mensaje = "Parroquia actualizada correctamente.";
    }

    echo json_encode(["success"=>true,"message"=>$mensaje]);

} catch(Throwable $e){
    echo json_encode(["success"=>false,"error"=>$e->getMessage()]);
}
