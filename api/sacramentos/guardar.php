<?php
require '../../config/db.php';
header('Content-Type: application/json; charset=utf-8'); 

function limpiarTexto($tipo){
    return trim($tipo);
}

$database = new Database();
$db = $database->getConnection();
$db->exec("SET NAMES utf8mb4"); 

$id_editar = $_POST['id_editar'] ?? null;

// Variables para la respuesta
$nombreFeligres = '';
$tipoSacramento = '';
$esEdicion = !empty($id_editar);

try {
    $db->beginTransaction();

    $tipoOriginal = $_POST['tipo_sacramento'];
    $registro = $_POST['registro'];
    $fecha = $_POST['fecha'];
    $id_ministro = $_POST['id_ministro'];
    $id_parroquia = $_POST['id_parroquia'] ?? null;
    
    // Datos de pago
    $id_pago = $_POST['id_pago'] ?? null;
    $monto_pago = $_POST['monto_pago'] ?? 0;
    
    $tipoOriginal = limpiarTexto($tipoOriginal);
    $tipoEvaluar = mb_strtolower($tipoOriginal, "UTF-8");
    
    // Mapeo de tipos para la respuesta
    $mapaTipos = [
        'bautismo' => 'Bautismo',
        'comunion' => 'Primera Comunión',
        'comunión' => 'Primera Comunión',
        'confirmacion' => 'Confirmación',
        'confirmación' => 'Confirmación',
        'matrimonio' => 'Matrimonio'
    ];
    $tipoSacramento = $mapaTipos[$tipoEvaluar] ?? ucfirst($tipoOriginal);

    // --- MODO: EDICIÓN (UPDATE) ---
    if ($esEdicion) {
        
        if ($tipoEvaluar === 'matrimonio') {
            // Actualizar matrimonio
            $stmt = $db->prepare("UPDATE matrimonio SET registro=?, fecha=?, id_ministro=?, lugar=? WHERE id_matrimonio=?");
            $stmt->execute([$registro, $fecha, $id_ministro, $_POST['lugar'] ?? 'Parroquia Local', $id_editar]);

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
            
            // Obtener nombres de los esposos
            $stmtNombre = $db->prepare("SELECT nombre_completo FROM feligres WHERE id_feligres = ?");
            $stmtNombre->execute([$esposo]);
            $nombreEsposo = $stmtNombre->fetchColumn();
            $stmtNombre->execute([$esposa]);
            $nombreEsposa = $stmtNombre->fetchColumn();
            $nombreFeligres = ($nombreEsposo ?: 'Desconocido') . ' y ' . ($nombreEsposa ?: 'Desconocida');
            
        } else {
            // Normalizamos el nombre de la tabla destino
            if (in_array($tipoEvaluar, ['comunion', 'comunión'])) {
                $tabla = 'comunion';
            } elseif (in_array($tipoEvaluar, ['confirmacion', 'confirmación'])) {
                $tabla = 'confirmacion';
            } else {
                $tabla = 'bautismo';
            }

            $id_feligres = $_POST['id_feligres'] ?? null;
            
            if ($tabla === 'bautismo') {
                $stmt = $db->prepare("UPDATE bautismo SET registro=?, id_feligres=?, fecha=?, padrino=?, madrina=?, id_ministro=?, id_parroquia=? WHERE id_bautismo=?");
                $padrino = $_POST['padrino'] ?? '';
                $madrina = $_POST['madrina'] ?? '';
                $stmt->execute([$registro, $id_feligres, $fecha, $padrino, $madrina, $id_ministro, $id_parroquia, $id_editar]);
            } else {
                $stmt = $db->prepare("UPDATE $tabla SET registro=?, id_feligres=?, fecha=?, id_ministro=?, id_parroquia=? WHERE id_{$tabla}=?");
                $stmt->execute([$registro, $id_feligres, $fecha, $id_ministro, $id_parroquia, $id_editar]);
            }
            
            // Obtener nombre del feligrés
            if ($id_feligres) {
                $stmtNombre = $db->prepare("SELECT nombre_completo FROM feligres WHERE id_feligres = ?");
                $stmtNombre->execute([$id_feligres]);
                $nombreFeligres = $stmtNombre->fetchColumn() ?: 'Feligrés no encontrado';
            }
        }

    // --- MODO: NUEVO REGISTRO (INSERT) ---
    } else {
        
        if (in_array($tipoEvaluar, ['bautismo', 'comunion', 'comunión', 'confirmacion', 'confirmación'])) {
            $id_feligres = $_POST['id_feligres'];
            
            // Obtener nombre del feligrés
            $stmtNombre = $db->prepare("SELECT nombre_completo FROM feligres WHERE id_feligres = ?");
            $stmtNombre->execute([$id_feligres]);
            $nombreFeligres = $stmtNombre->fetchColumn() ?: 'Feligrés no encontrado';
            
            if (in_array($tipoEvaluar, ['comunion', 'comunión'])) {
                $tabla = 'comunion';
            } elseif (in_array($tipoEvaluar, ['confirmacion', 'confirmación'])) {
                $tabla = 'confirmacion';
            } else {
                $tabla = 'bautismo';
            }
            
            $check = $db->prepare("SELECT COUNT(*) FROM $tabla WHERE id_feligres = ?");
            $check->execute([$id_feligres]);
            if ($check->fetchColumn() > 0) {
                throw new Exception("El feligrés ya cuenta con un registro de " . ucfirst($tabla));
            }

            if ($tabla === 'bautismo') {
                $stmt = $db->prepare("INSERT INTO bautismo (registro, id_feligres, fecha, padrino, madrina, id_ministro, id_parroquia, estado, id_pago, monto_pago) VALUES (?, ?, ?, ?, ?, ?, ?, 'activo', ?, ?)");
                $padrino = $_POST['padrino'] ?? '';
                $madrina = $_POST['madrina'] ?? '';
                $stmt->execute([$registro, $id_feligres, $fecha, $padrino, $madrina, $id_ministro, $id_parroquia, $id_pago, $monto_pago]);
                $id_editar = $db->lastInsertId();
            } else {
                $stmt = $db->prepare("INSERT INTO $tabla (registro, id_feligres, fecha, id_ministro, id_parroquia, estado, id_pago, monto_pago) VALUES (?, ?, ?, ?, ?, 'activo', ?, ?)");
                $stmt->execute([$registro, $id_feligres, $fecha, $id_ministro, $id_parroquia, $id_pago, $monto_pago]);
                $id_editar = $db->lastInsertId();
            }
        }

        if ($tipoEvaluar === 'matrimonio') {
            $esposo = $_POST['id_esposo'];
            $esposa = $_POST['id_esposa'];
            $testigos = $_POST['testigos'] ?? [];

            // Obtener nombres de los esposos
            $stmtNombre = $db->prepare("SELECT nombre_completo FROM feligres WHERE id_feligres = ?");
            $stmtNombre->execute([$esposo]);
            $nombreEsposo = $stmtNombre->fetchColumn() ?: 'Desconocido';
            $stmtNombre->execute([$esposa]);
            $nombreEsposa = $stmtNombre->fetchColumn() ?: 'Desconocida';
            $nombreFeligres = $nombreEsposo . ' y ' . $nombreEsposa;

            $sqlCheck = "SELECT COUNT(*) FROM matrimonio m 
                         JOIN matrimonio_feligres mf ON m.id_matrimonio = mf.id_matrimonio 
                         WHERE mf.id_feligres = ? AND m.estado = 'activo'";

            $checkEsposo = $db->prepare($sqlCheck);
            $checkEsposo->execute([$esposo]);
            if ($checkEsposo->fetchColumn() > 0) {
                throw new Exception("El esposo tiene un matrimonio activo vigente.");
            }

            $checkEsposa = $db->prepare($sqlCheck);
            $checkEsposa->execute([$esposa]);
            if ($checkEsposa->fetchColumn() > 0) {
                throw new Exception("La esposa tiene un matrimonio activo vigente.");
            }

            $stmt = $db->prepare("INSERT INTO matrimonio (registro, fecha, id_ministro, lugar, estado, id_pago, monto_pago) VALUES (?, ?, ?, ?, 'activo', ?, ?)");
            $stmt->execute([$registro, $fecha, $id_ministro, $_POST['lugar'] ?? 'Parroquia Local', $id_pago, $monto_pago]);
            $id_editar = $db->lastInsertId();

            $insRel = $db->prepare("INSERT INTO matrimonio_feligres (id_matrimonio, id_feligres, rol) VALUES (?, ?, ?)");
            $insRel->execute([$id_editar, $esposo, 'esposo']);
            $insRel->execute([$id_editar, $esposa, 'esposa']);

            foreach ($testigos as $id_testigo) {
                if (!empty($id_testigo)) {
                    $insRel->execute([$id_editar, $id_testigo, 'testigo']);
                }
            }
        }
    }

    $db->commit();
    
    // Respuesta exitosa con datos completos
    echo json_encode([
        "success" => true, 
        "message" => $esEdicion ? "Registro actualizado correctamente" : "Registro guardado correctamente",
        "isEdit" => $esEdicion,
        "nombreFeligres" => $nombreFeligres,
        "tipoSacramento" => $tipoSacramento,
        "id" => $id_editar,
        "registro" => $registro
    ]);

} catch (Exception $e) {
    $db->rollBack();
    echo json_encode([
        "success" => false, 
        "error" => $e->getMessage()
    ]);
}
?>