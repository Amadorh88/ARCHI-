<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    exit(json_encode(['success' => false, 'message' => 'Acceso denegado']));
}

include_once '../config/db.php';
$database = new Database();
$bd = $database->getConnection();

$tipo = $_GET['tipo'] ?? '';
$id_feligres = $_GET['id_feligres'] ?? 0;

$cumple_prerrequisito = false;

switch($tipo) {
    case 'bautismo':
        // Verificar catequesis prebautismal completada
        $query = "SELECT 1 FROM prebautismal_catequesis 
                 WHERE id_feligres = :id AND estado = 'completado'";
        break;
    case 'comunion':
        // Verificar bautismo y catequesis de comunión completada
        $query = "SELECT 1 FROM bautismo b 
                 INNER JOIN comunion_catequesis cc ON cc.id_feligres = b.id_feligres
                 WHERE b.id_feligres = :id AND cc.estado = 'completado'";
        break;
    case 'confirmacion':
        // Verificar comunión y catequesis de confirmación completada
        $query = "SELECT 1 FROM comunion c 
                 INNER JOIN confirmacion_catequesis cc ON cc.id_feligres = c.id_feligres
                 WHERE c.id_feligres = :id AND cc.estado = 'completado'";
        break;
    case 'matrimonio':
        // Verificar confirmación y catequesis prematrimonial completada
        $query = "SELECT 1 FROM confirmacion conf 
                 INNER JOIN prematrimonial_catequesis pc ON pc.id_feligres = conf.id_feligres
                 WHERE conf.id_feligres = :id AND pc.estado = 'completado'";
        break;
    default:
        $query = "";
}

if (!empty($query)) {
    $stmt = $bd->prepare($query);
    $stmt->bindParam(':id', $id_feligres);
    $stmt->execute();
    $cumple_prerrequisito = $stmt->rowCount() > 0;
}

echo json_encode(['cumple_prerrequisito' => $cumple_prerrequisito]);
?>