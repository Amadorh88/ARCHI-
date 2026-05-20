<?php
require '../../config/db.php';
header('Content-Type: application/json; charset=utf-8'); 

// Quitamos espacios extras pero MANTENEMOS mayúsculas, minúsculas y tildes originales
function limpiarTexto($tipo){
    return trim($tipo);
}

$database = new Database();
$db = $database->getConnection();

// Asegurar conexión UTF-8 para tildes y eñes
$db->exec("SET NAMES utf8mb4"); 

$id_editar = $_POST['id_editar'] ?? null;

try {
    $db->beginTransaction();

    $tipoOriginal = $_POST['tipo_sacramento'];
    $registro = $_POST['registro'];
    $fecha = $_POST['fecha'];
    $id_ministro = $_POST['id_ministro'];
    $id_parroquia = $_POST['id_parroquia'] ?? null;
    
    // Guardamos el texto limpio conservando su formato original
    $tipoOriginal = limpiarTexto($tipoOriginal);
    // Creamos una versión en minúsculas SÓLO para las comparaciones del IF
    $tipoEvaluar = mb_strtolower($tipoOriginal, "UTF-8");

    // --- MODO: EDICIÓN (UPDATE) ---
    if (!empty($id_editar)) {
        
        if (in_array($tipoEvaluar, ['matrimonio'])) {
            $stmt = $db->prepare("UPDATE matrimonio SET registro=?, fecha=?, id_ministro=? WHERE id_matrimonio=?");
            $stmt->execute([$registro, $fecha, $id_ministro, $id_editar]);

            // Refrescar participantes
            $db->prepare("DELETE FROM matrimonio_feligres WHERE id_matrimonio=?")->execute([$id_editar]);
            
            $esposo = $_POST['id_esposo'];
            $esposa = $_POST['id_esposa'];
            $testigos = $_POST['testigos'] ?? [];

            $insRel = $db->prepare("INSERT INTO matrimonio_feligres (id_matrimonio, id_feligres, rol) VALUES (?, ?, ?)");
            $insRel->execute([$id_editar, $esposo, 'esposo']);
            $insRel->execute([$id_editar, $esposa, 'esposa']);

            foreach ($testigos as $id_testigo) {
                if (!empty($id_testigo)) {
                    $insRel->execute([$id_editar, $id_testigo, 'testigo']);
                }
            }
        } else {
            // Normalizamos el nombre de la tabla destino
            $tabla = (in_array($tipoEvaluar, ['comunion', 'comunión'])) ? 'comunion' : 
                     ((in_array($tipoEvaluar, ['confirmacion', 'confirmación'])) ? 'confirmacion' : 'bautismo');

            if ($tabla === 'bautismo') {
                $stmt = $db->prepare("UPDATE bautismo SET registro=?, id_feligres=?, fecha=?, padrino=?, madrina=?, id_ministro=?, id_parroquia=? WHERE id_bautismo=?");
                $stmt->execute([$registro, $_POST['id_feligres'], $fecha, $_POST['padrino'], $_POST['madrina'], $id_ministro, $id_parroquia, $id_editar]);
            } else {
                $stmt = $db->prepare("UPDATE $tabla SET registro=?, id_feligres=?, fecha=?, id_ministro=?, id_parroquia=? WHERE id_{$tabla}=?");
                $stmt->execute([$registro, $_POST['id_feligres'], $fecha, $id_ministro, $id_parroquia, $id_editar]);
            }
        }

    // --- MODO: NUEVO REGISTRO (INSERT) ---
    } else {
        
        if (in_array($tipoEvaluar, ['bautismo', 'comunion', 'comunión', 'confirmacion', 'confirmación'])) {
            $id_feligres = $_POST['id_feligres'];
            
            $tabla = (in_array($tipoEvaluar, ['comunion', 'comunión'])) ? 'comunion' : 
                     ((in_array($tipoEvaluar, ['confirmacion', 'confirmación'])) ? 'confirmacion' : 'bautismo');  
            
            $check = $db->prepare("SELECT COUNT(*) FROM $tabla WHERE id_feligres = ?");
            $check->execute([$id_feligres]);
            if ($check->fetchColumn() > 0) {
                throw new Exception("El feligrés ya cuenta con un registro de " . ucfirst($tabla));
            }

            if ($tabla === 'bautismo') {
                $stmt = $db->prepare("INSERT INTO bautismo (registro, id_feligres, fecha, padrino, madrina, id_ministro, id_parroquia, estado) VALUES (?, ?, ?, ?, ?, ?, ?, 'activo')");
                $stmt->execute([$registro, $id_feligres, $fecha, $_POST['padrino'], $_POST['madrina'], $id_ministro, $id_parroquia]);
            } else {
                $stmt = $db->prepare("INSERT INTO $tabla (registro, id_feligres, fecha, id_ministro, id_parroquia, estado) VALUES (?, ?, ?, ?, ?, 'activo')");
                $stmt->execute([$registro, $id_feligres, $fecha, $id_ministro, $id_parroquia]);
            }
        }

        if ($tipoEvaluar === 'matrimonio') {
            $esposo = $_POST['id_esposo'];
            $esposa = $_POST['id_esposa'];
            $testigos = $_POST['testigos'] ?? [];

            $sqlCheck = "SELECT COUNT(*) FROM matrimonio m 
                         JOIN matrimonio_feligres mf ON m.id_matrimonio = mf.id_matrimonio 
                         WHERE mf.id_feligres = ? AND m.estado = 'activo'";

            $checkEsposo = $db->prepare($sqlCheck);
            $checkEsposo->execute([$esposo]);
            if ($checkEsposo->fetchColumn() > 0) throw new Exception("El esposo tiene un matrimonio activo vigente.");

            $checkEsposa = $db->prepare($sqlCheck);
            $checkEsposa->execute([$esposa]);
            if ($checkEsposa->fetchColumn() > 0) throw new Exception("La esposa tiene un matrimonio activo vigente.");

            $stmt = $db->prepare("INSERT INTO matrimonio (registro, fecha, id_ministro, lugar, estado) VALUES (?, ?, ?, ?, 'activo')");
            $stmt->execute([$registro, $fecha, $id_ministro, $_POST['lugar'] ?? 'Parroquia Local']);
            $id_mat = $db->lastInsertId();

            $insRel = $db->prepare("INSERT INTO matrimonio_feligres (id_matrimonio, id_feligres, rol) VALUES (?, ?, ?)");
            $insRel->execute([$id_mat, $esposo, 'esposo']);
            $insRel->execute([$id_mat, $esposa, 'esposa']);

            foreach ($testigos as $id_testigo) {
                if (!empty($id_testigo)) {
                    $insRel->execute([$id_mat, $id_testigo, 'testigo']);
                }
            }
        }
    }

    $db->commit();
    echo json_encode(["success" => true, "message" => "Registro guardado correctamente"]);

} catch (Exception $e) {
    $db->rollBack();
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>