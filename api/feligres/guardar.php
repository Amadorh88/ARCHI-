<?php
require '../../config/db.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $db = (new Database())->getConnection();
    $db->exec("SET NAMES utf8mb4");
    
    // Captura y saneamiento
    $id = !empty($_POST['id_feligres']) ? intval($_POST['id_feligres']) : null;
    $nombre = trim($_POST['nombre_completo'] ?? '');
    $genero = trim($_POST['genero'] ?? '');
    $fecha_nac = $_POST['fecha_nacimiento'] ?? null;
    $lugar = trim($_POST['lugar_nacimiento'] ?? '');
    $padre = trim($_POST['nombre_padre'] ?? '');
    $madre = trim($_POST['nombre_madre'] ?? '');

    /* ============================================================
       1. VALIDACIONES DE LÓGICA
    ============================================================ */

    if (empty($nombre)) {
        throw new Exception("La identidad del feligrés es un activo obligatorio.");
    }
    
    if (empty($genero)) {
        throw new Exception("El género es obligatorio.");
    }
    
    if (!in_array($genero, ['Masculino', 'Femenino'])) {
        throw new Exception("Género no válido.");
    }

    // Validar coherencia temporal
    if (!empty($fecha_nac)) {
        $fecha_actual = date('Y-m-d');
        if ($fecha_nac > $fecha_actual) {
            throw new Exception("Error cronológico: La fecha de nacimiento no puede ser una fecha futura.");
        }
    }

    // Validar integridad familiar
    if (!empty($padre) && !empty($madre) && $padre === $madre) {
        throw new Exception("Inconsistencia familiar: El nombre del padre y la madre no pueden ser idénticos.");
    }

    /* ============================================================
       2. PREVENCIÓN DE DUPLICADOS
    ============================================================ */
    $sql_check = "SELECT id_feligres FROM feligres WHERE nombre_completo = ? AND genero = ? AND (fecha_nacimiento = ? OR nombre_padre = ?)";
    if ($id) { $sql_check .= " AND id_feligres != $id"; }
    
    $stmt_check = $db->prepare($sql_check);
    $stmt_check->execute([$nombre, $genero, $fecha_nac, $padre]);
    
    if ($stmt_check->fetch()) {
        throw new Exception("Ya existe un registro con características idénticas en el sistema.");
    }

    /* ============================================================
       3. PROCESAMIENTO DE DATOS
    ============================================================ */

    if ($id) {
        // ACTUALIZAR
        $stmt = $db->prepare("
            UPDATE feligres SET
                nombre_completo = ?,
                genero = ?,
                fecha_nacimiento = ?,
                lugar_nacimiento = ?,
                nombre_padre = ?,
                nombre_madre = ?
            WHERE id_feligres = ?
        ");
        $stmt->execute([$nombre, $genero, $fecha_nac, $lugar, $padre, $madre, $id]);
        $message = "Feligrés actualizado con éxito";
    } else {
        // INSERTAR
        $stmt = $db->prepare("
            INSERT INTO feligres 
            (nombre_completo, genero, fecha_nacimiento, lugar_nacimiento, nombre_padre, nombre_madre)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$nombre, $genero, $fecha_nac, $lugar, $padre, $madre]);
        $message = "Feligrés registrado con éxito";
    }

    echo json_encode(["success" => true, "message" => $message]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(["error" => $e->getMessage()]);
} catch (Throwable $t) {
    http_response_code(500);
    echo json_encode(["error" => "Fallo interno"]);
}