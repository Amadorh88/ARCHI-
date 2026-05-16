<?php
require '../../config/db.php';
header('Content-Type: application/json');

$db = (new Database())->getConnection();
$id = $_GET['id'] ?? null;
$tipo = $_GET['tipo'] ?? null;
function limpiarTexto($tipo){
    $tipo = mb_strtolower($tipo,"UTF-8");
    $buscar = ["á", "é","í","ó","ú"];
    $reemplazar = ["a","e","i","o","u"];
    $tipo = str_replace($buscar,$reemplazar, $tipo);
    return $tipo;
}
if (!$tipo) exit(json_encode(["error" => "Tipo faltante"]));
if (!$id) exit(json_encode(["error" => "ID Tipo faltante"]));

try { 
    // actualizar tipo de sacramento, limpiando caracteres 
    $tipo = limpiarTexto($tipo);
/* cambiar estado de bautismo */
    if($tipo == "bautismo"){
        $stmt = $db->prepare("SELECT estado FROM bautismo WHERE id_bautismo = ?");
           $stmt->execute([$id]);
            $actual = $stmt->fetchColumn();
            $nuevo = ($actual === 1) ? 0 : 1;
            $upd = $db->prepare("UPDATE bautismo SET estado = ? WHERE id_bautismo = ?");
            $upd->execute([$nuevo, $id]); 
                        }
/* cambiar estado de comunion */
        if($tipo == "comunion") {
            $stmt = $db->prepare("SELECT estado FROM comunion WHERE id_comunion = ?");
        $stmt->execute([$id]);
    $actual = $stmt->fetchColumn();
    $nuevo = ($actual === 1) ? 0 : 1;
    $upd = $db->prepare("UPDATE comunion SET estado = ? WHERE id_comunion = ?");
    $upd->execute([$nuevo, $id]); 
            }
/* cambiar estado de confirmacion */
        if($tipo == "confirmacion") {
            $stmt = $db->prepare("SELECT estado FROM confirmacion WHERE id_confirmacion = ?");
        $stmt->execute([$id]);
    $actual = $stmt->fetchColumn();
    $nuevo = ($actual === 1) ? 0 : 1;
    $upd = $db->prepare("UPDATE confirmacion SET estado = ? WHERE id_confirmacion = ?");
    $upd->execute([$nuevo, $id]); 
            }

            /* cambiar estado de matrimonio */
        if($tipo == "matrimonio") {
            $stmt = $db->prepare("SELECT estado FROM matrimonio WHERE id_matrimonio = ?");
         $stmt->execute([$id]);
    $actual = $stmt->fetchColumn();
    $nuevo = ($actual === "activo") ? "inactivo" : "activo";
    $upd = $db->prepare("UPDATE matrimonio SET estado = ? WHERE id_matrimonio = ?");
    $upd->execute([$nuevo, $id]); 
            } 
 

  
    echo json_encode(["success" => true, "nuevo_estado" => $nuevo]);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}