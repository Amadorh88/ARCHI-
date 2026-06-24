<?php
require '../../config/db.php';
header('Content-Type: application/json');

$db = (new Database())->getConnection();

$id_pago = filter_input(INPUT_POST, 'id_pago', FILTER_VALIDATE_INT);
$concepto = $_POST['concepto'] ?? '';
$cantidad = $_POST['cantidad'] ?? 0;
$recibido = $_POST['recibido'] ?? 0;
$cambio   = $_POST['cambio'] ?? 0;
$id_feligres = !empty($_POST['id_feligres']) ? $_POST['id_feligres'] : null;

try {
    if ($id_pago) {
        // ACTUALIZACIÓN
        $sql = "UPDATE pago SET concepto=?, cantidad=?, recibido=?, cambio=?, id_feligres=? WHERE id_pago=?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$concepto, $cantidad, $recibido, $cambio, $id_feligres, $id_pago]);
    } else {
        // NUEVO REGISTRO
        $sql = "INSERT INTO pago (concepto, cantidad, recibido, cambio, id_feligres) VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$concepto, $cantidad, $recibido, $cambio, $id_feligres]);
    }

    echo json_encode(["success" => true]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}