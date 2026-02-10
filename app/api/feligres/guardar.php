<?php
require '../../config/db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $db = (new Database())->getConnection();

    // Captura y saneamiento (Hill: El control de los detalles define el éxito)
    $id = !empty($_POST['id_feligres']) ? intval($_POST['id_feligres']) : null;
    $nombre = strtoupper(trim($_POST['nombre_completo'] ?? ''));
    $fecha_nac = $_POST['fecha_nacimiento'] ?? null;
    $lugar = trim($_POST['lugar_nacimiento'] ?? '');
    $padre = strtoupper(trim($_POST['nombre_padre'] ?? ''));
    $madre = strtoupper(trim($_POST['nombre_madre'] ?? ''));

    /* ============================================================
       1. VALIDACIONES DE LÓGICA (Hill: Pensamiento Preciso)
    ============================================================ */

    if (empty($nombre)) {
        throw new Exception("La identidad del feligrés es un activo obligatorio.");
    }

    // Validar coherencia temporal
    if (!empty($fecha_nac)) {
        $fecha_actual = date('Y-m-d');
        if ($fecha_nac > $fecha_actual) {
            throw new Exception("Error cronológico: La fecha de nacimiento no puede ser una fecha futura.");
        }
    }

    // Validar integridad familiar (Cialdini: Autoridad en la coherencia de datos)
    if (!empty($padre) && !empty($madre) && $padre === $madre) {
        throw new Exception("Inconsistencia familiar: El nombre del padre y la madre no pueden ser idénticos.");
    }

    /* ============================================================
       2. PREVENCIÓN DE DUPLICADOS (Kiyosaki: Protegiendo el Activo)
       Evitar que se registre dos veces a la misma persona con los mismos padres.
    ============================================================ */
    $sql_check = "SELECT id_feligres FROM feligres WHERE nombre_completo = ? AND (fecha_nacimiento = ? OR nombre_padre = ?)";
    if ($id) { $sql_check .= " AND id_feligres != $id"; } // Si editamos, ignorar el ID actual
    
    $stmt_check = $db->prepare($sql_check);
    $stmt_check->execute([$nombre, $fecha_nac, $padre]);
    
    if ($stmt_check->fetch()) {
        throw new Exception("Principio de Escasez: Ya existe un registro con características idénticas en el sistema.");
    }

    /* ============================================================
       3. PROCESAMIENTO DE DATOS (Hill: Acción Organizada)
    ============================================================ */

    if ($id) {
        // ACTUALIZAR
        $stmt = $db->prepare("
            UPDATE feligres SET
                nombre_completo = ?,
                fecha_nacimiento = ?,
                lugar_nacimiento = ?,
                nombre_padre = ?,
                nombre_madre = ?
            WHERE id_feligres = ?
        ");
        $stmt->execute([$nombre, $fecha_nac, $lugar, $padre, $madre, $id]);
    } else {
        // INSERTAR
        $stmt = $db->prepare("
            INSERT INTO feligres 
            (nombre_completo, fecha_nacimiento, lugar_nacimiento, nombre_padre, nombre_madre)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$nombre, $fecha_nac, $lugar, $padre, $madre]);
    }

    echo json_encode(["success" => true, "message" => "Activo registrado con éxito"]);

} catch (Exception $e) {
    http_response_code(400); // Bad Request (Principio de Autoridad: El sistema sabe qué está mal)
    echo json_encode(["error" => $e->getMessage()]);
} catch (Throwable $t) {
    http_response_code(500);
    echo json_encode(["error" => "Fallo sistémico interno", "detalle" => $t->getMessage()]);
}