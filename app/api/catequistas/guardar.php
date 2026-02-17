<?php
require '../../config/db.php';
$db = (new Database())->getConnection();

header('Content-Type: application/json');

// ---------- Función de error ----------
function errorResponse(string $mensaje, string $debug = '')
{
    echo json_encode([
        'success' => false,
        'error'   => $mensaje,
        'debug'   => $debug
    ]);
    exit;
}

// ---------- Datos ----------
$id = isset($_POST['id_catequista']) && is_numeric($_POST['id_catequista'])
    ? (int) $_POST['id_catequista']
    : null;

$nombre       = trim($_POST['nombre'] ?? '');
$telefono     = trim($_POST['telefono'] ?? '');
$especialidad = trim($_POST['especialidad'] ?? '');

// ---------- Validaciones ----------
if ($nombre === '') {
    errorResponse('El nombre es obligatorio', 'nombre vacío');
}

if (mb_strlen($nombre) < 3 || mb_strlen($nombre) > 100) {
    errorResponse('El nombre debe tener entre 3 y 100 caracteres', 'longitud inválida');
}

if (!preg_match('/^[a-zA-ZÀ-ÿ\s\'-]+$/u', $nombre)) {
    errorResponse('El nombre contiene caracteres no válidos', 'regex nombre');
}

if ($telefono !== '') {
    if (!preg_match('/^\+?[0-9\s\-]{7,20}$/', $telefono)) {
        errorResponse('El teléfono no tiene un formato válido', 'regex teléfono');
    }
} else {
    $telefono = null;
}

if ($especialidad !== '') {
    if (mb_strlen($especialidad) > 100) {
        errorResponse('La especialidad es demasiado larga', 'especialidad > 100');
    }
} else {
    $especialidad = null;
}

// ---------- BD ----------
try {
    // Duplicados (con paréntesis para agrupar correctamente)
    $sqlDup = "SELECT id_catequista FROM catequista WHERE (LOWER(nombre) = LOWER(?)";
    $params = [$nombre];

    if ($telefono !== null) {
        $sqlDup .= " OR telefono = ?";
        $params[] = $telefono;
    }

    $sqlDup .= ")"; // cerrar paréntesis del OR

    if ($id !== null) {
        $sqlDup .= " AND id_catequista != ?";
        $params[] = $id;
    }

    $stmtDup = $db->prepare($sqlDup);
    $stmtDup->execute($params);

    if ($stmtDup->fetch()) {
        errorResponse(
            'Ya existe un catequista con el mismo nombre o teléfono',
            'duplicado detectado'
        );
    }

    $db->beginTransaction();

    if ($id !== null) {
        // UPDATE
        $stmt = $db->prepare(
            "UPDATE catequista
             SET nombre = ?, telefono = ?, especialidad = ?
             WHERE id_catequista = ?"
        );

        $stmt->execute([$nombre, $telefono, $especialidad, $id]);

        if ($stmt->rowCount() === 0) {
            throw new Exception('UPDATE sin filas afectadas');
        }

        $mensaje = 'Catequista actualizado correctamente';

    } else {
        // INSERT
        $stmt = $db->prepare(
            "INSERT INTO catequista (nombre, telefono, especialidad)
             VALUES (?, ?, ?)"
        );

        $stmt->execute([$nombre, $telefono, $especialidad]);

        $mensaje = 'Catequista registrado correctamente';
    }

    $db->commit();

    echo json_encode([
        'success' => true,
        'message' => $mensaje
    ]);

} catch (Throwable $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }

    errorResponse(
        'Error interno al guardar los datos',
        $e->getMessage()
    );
}
