<?php
declare(strict_types=1);

require '../../config/db.php';
header('Content-Type: application/json');

try {

    $db = (new Database())->getConnection();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id = filter_input(INPUT_POST, 'id_ministro', FILTER_VALIDATE_INT);
    $nombre = trim($_POST['nombre_completo'] ?? '');
    $dip = trim($_POST['DIP'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $tipo = trim($_POST['tipo'] ?? '');

    $tiposValidos = ['Sacerdote','Diácono','Obispo','Catequista'];

    if(empty($nombre) || empty($tipo)){
        throw new Exception("Nombre y tipo son obligatorios.");
    }

    if(!in_array($tipo, $tiposValidos, true)){
        throw new Exception("Tipo de ministro inválido.");
    }

    // Validar duplicado DIP
    if(!empty($dip)){
        $stmt = $db->prepare("SELECT COUNT(*) FROM ministros WHERE DIP = ? AND id_ministro != ?");
        $stmt->execute([$dip, $id ?? 0]);

        if($stmt->fetchColumn() > 0){
            throw new Exception("Ya existe un ministro con ese DIP.");
        }
    }

    if(!$id){

        $stmt = $db->prepare("
            INSERT INTO ministros (nombre_completo, DIP, telefono, tipo)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([
            htmlspecialchars($nombre),
            $dip ?: null,
            $telefono ?: null,
            $tipo
        ]);

        $mensaje = "Ministro creado correctamente.";

    } else {

        $stmt = $db->prepare("
            UPDATE ministros
            SET nombre_completo=?, DIP=?, telefono=?, tipo=?
            WHERE id_ministro=?
        ");

        $stmt->execute([
            htmlspecialchars($nombre),
            $dip ?: null,
            $telefono ?: null,
            $tipo,
            $id
        ]);

        $mensaje = "Ministro actualizado correctamente.";
    }

    echo json_encode(["success"=>true,"message"=>$mensaje]);

} catch(Throwable $e){
    echo json_encode(["success"=>false,"error"=>$e->getMessage()]);
}
