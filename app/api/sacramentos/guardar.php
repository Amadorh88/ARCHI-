<?php
require '../../config/db.php';
header('Content-Type: application/json');

$db = (new Database())->getConnection();
$id_editar = $_POST['id_editar'] ?? null;
try {
    $db->beginTransaction();

    $tipo = $_POST['tipo_sacramento'];
    $registro = $_POST['registro'];
    $fecha = $_POST['fecha'];
    $id_ministro = $_POST['id_ministro'];
    $id_parroquia = $_POST['id_parroquia'] ?? null;

    // --- REGLA 1: VALIDACIÓN PARA SACRAMENTOS ÚNICOS ---
    if (in_array($tipo, ['bautismo', 'comunion', 'confirmacion'])) {
        $id_feligres = $_POST['id_feligres'];
        $tabla = ($tipo === 'comunion') ? 'comunion' : $tipo;

        $check = $db->prepare("SELECT COUNT(*) FROM $tabla WHERE id_feligres = ?");
        $check->execute([$id_feligres]);
        if ($check->fetchColumn() > 0) {
            throw new Exception("El feligrés ya cuenta con un registro de " . ucfirst($tipo));
        }

        // Inserción específica
        if ($tipo === 'bautismo') {
            $stmt = $db->prepare("INSERT INTO bautismo (registro, id_feligres, fecha, padrino, madrina, id_ministro, id_parroquia) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$registro, $id_feligres, $fecha, $_POST['padrino'], $_POST['madrina'], $id_ministro, $id_parroquia]);
        } else {
            $stmt = $db->prepare("INSERT INTO $tabla (registro, id_feligres, fecha, id_ministro, id_parroquia) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$registro, $id_feligres, $fecha, $id_ministro, $id_parroquia]);
        }
    }

    // --- REGLA 2: VALIDACIÓN PARA MATRIMONIO ---
    if ($tipo === 'matrimonio') {
        $esposo = $_POST['id_esposo'];
        $esposa = $_POST['id_esposa'];
        $testigos = $_POST['testigos'] ?? []; // Array de IDs de feligreses

        // Validar que esposo y esposa no tengan matrimonios activos
        $sqlCheck = "SELECT COUNT(*) FROM matrimonio m 
                     JOIN matrimonio_feligres mf ON m.id_matrimonio = mf.id_matrimonio 
                     WHERE mf.id_feligres = ? AND m.estado = 'activo'";

        $checkEsposo = $db->prepare($sqlCheck);
        $checkEsposo->execute([$esposo]);
        if ($checkEsposo->fetchColumn() > 0)
            throw new Exception("El esposo tiene un matrimonio activo vigente.");

        $checkEsposa = $db->prepare($sqlCheck);
        $checkEsposa->execute([$esposa]);
        if ($checkEsposa->fetchColumn() > 0)
            throw new Exception("La esposa tiene un matrimonio activo vigente.");

        // Inserción en tabla Matrimonio
        $stmt = $db->prepare("INSERT INTO matrimonio (registro, fecha, id_ministro, lugar, estado) VALUES (?, ?, ?, ?, 'activo')");
        $stmt->execute([$registro, $fecha, $id_ministro, $_POST['lugar'] ?? 'Parroquia Local']);
        $id_mat = $db->lastInsertId();

        // Inserción de la Pareja y Testigos en matrimonio_feligres
        $insRel = $db->prepare("INSERT INTO matrimonio_feligres (id_matrimonio, id_feligres, rol) VALUES (?, ?, ?)");

        $insRel->execute([$id_mat, $esposo, 'esposo']);
        $insRel->execute([$id_mat, $esposa, 'esposa']);

        foreach ($testigos as $id_testigo) {
            if (!empty($id_testigo)) {
                $insRel->execute([$id_mat, $id_testigo, 'testigo']);
            }
        }
    }

    // Actualizar
  

    if (!empty($id_editar)) {
        // LÓGICA DE UPDATE
        if ($tipo === 'matrimonio') {
            $stmt = $db->prepare("UPDATE matrimonio SET registro=?, fecha=?, id_ministro=? WHERE id_matrimonio=?");
            $stmt->execute([$registro, $fecha, $id_ministro, $id_editar]);

            // Refrescar participantes: Borrar y volver a insertar es lo más limpio en relacionales
            $db->prepare("DELETE FROM matrimonio_feligres WHERE id_matrimonio=?")->execute([$id_editar]);
            // ... (aquí sigue el código de inserción de esposo, esposa y testigos que ya tenías)
        } else {
            $tabla = ($tipo === 'comunion') ? 'comunion' : (($tipo === 'confirmacion') ? 'confirmacion' : 'bautismo');
            if ($tipo === 'bautismo') {
                $stmt = $db->prepare("UPDATE bautismo SET registro=?, id_feligres=?, fecha=?, padrino=?, madrina=?, id_ministro=?, id_parroquia=? WHERE id_bautismo=?");
                $stmt->execute([$registro, $_POST['id_feligres'], $fecha, $_POST['padrino'], $_POST['madrina'], $id_ministro, $id_parroquia, $id_editar]);
            } else {
                $stmt = $db->prepare("UPDATE $tabla SET registro=?, id_feligres=?, fecha=?, id_ministro=?, id_parroquia=? WHERE id_$tabla=?");
                $stmt->execute([$registro, $_POST['id_feligres'], $fecha, $id_ministro, $id_parroquia, $id_editar]);
            }
        }
    } else {
        // LÓGICA DE INSERT (El código que ya tenías anteriormente)
    }
    $db->commit();
    echo json_encode(["success" => true, "message" => "Registro guardado correctamente"]);

} catch (Exception $e) {
    $db->rollBack();
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}