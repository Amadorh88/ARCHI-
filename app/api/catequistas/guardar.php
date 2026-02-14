<?php
require '../../config/db.php';
$db = (new Database())->getConnection();

header('Content-Type: application/json');

// ---------- Funciones de validación ----------
function errorResponse(string $mensaje)
{
    echo json_encode([
        'success' => false,
        'error' => $mensaje
    ]);
    exit;
}

// ---------- Obtención y saneamiento ----------
$id = isset($_POST['id_catequista']) && is_numeric($_POST['id_catequista'])
    ? (int) $_POST['id_catequista']
    : null;

$nombre = trim($_POST['nombre'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$especialidad = trim($_POST['especialidad'] ?? '');

// ---------- Validaciones ----------
if ($nombre === '') {
    errorResponse('El nombre es obligatorio');
}

if (mb_strlen($nombre) < 3 || mb_strlen($nombre) > 100) {
    errorResponse('El nombre debe tener entre 3 y 100 caracteres');
}

if (!preg_match('/^[a-zA-ZÀ-ÿ\s\'-]+$/u', $nombre)) {
    errorResponse('El nombre contiene caracteres no válidos');
}

if ($telefono !== '') {
    if (!preg_match('/^\+?[0-9\s\-]{7,20}$/', $telefono)) {
        errorResponse('El teléfono no tiene un formato válido');
    }
} else {
    $telefono = null;
}

if ($especialidad !== '') {
    if (mb_strlen($especialidad) > 100) {
        errorResponse('La especialidad es demasiado larga');
    }
} else {
    $especialidad = null;
}

// ---------- Operación en BD ----------
try {
    // ---------- Validación de duplicados ----------
    $sqlDup = "
SELECT id_catequista 
FROM catequista 
WHERE LOWER(nombre) = LOWER(?)
";

    $params = [$nombre];

    if ($telefono !== null) {
        $sqlDup .= " OR telefono = ?";
        $params[] = $telefono;
    }

    if ($id !== null) {
        $sqlDup .= " AND id_catequista != ?";
        $params[] = $id;
    }

    $stmtDup = $db->prepare($sqlDup);
    $stmtDup->execute($params);

    if ($stmtDup->fetch()) {
        errorResponse('Ya existe un catequista registrado con el mismo nombre o teléfono');
    }

    $db->beginTransaction();

    if ($id !== null) {
        $sql = "UPDATE catequista 
                SET nombre = ?, telefono = ?, especialidad = ?
                WHERE id_catequista = ?";

        $stmt = $db->prepare($sql);
        $stmt->execute([$nombre, $telefono, $especialidad, $id]);

        if ($stmt->rowCount() === 0) {
            throw new Exception('No se encontró el catequista a actualizar');
        }
    } else {
        $sql = "INSERT INTO catequista (nombre, telefono, especialidad)
                VALUES (?, ?, ?)";

        $stmt = $db->prepare($sql);
        $stmt->execute([$nombre, $telefono, $especialidad]);
    }

    $db->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Operación realizada correctamente'
    ]);

} catch (Exception $e) {
    $db->rollBack();
    errorResponse('Error al guardar los datos. Intente nuevamente.');
}
