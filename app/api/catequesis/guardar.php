<?php
declare(strict_types=1);

require '../../config/db.php';
header('Content-Type: application/json');

try {

    $db = (new Database())->getConnection();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ===============================
    // 1️⃣ SANITIZACIÓN Y VALIDACIÓN
    // ===============================

    $id_catequesis = filter_input(INPUT_POST, 'id_catequesis', FILTER_VALIDATE_INT);
    $id_feligres   = filter_input(INPUT_POST, 'id_feligres', FILTER_VALIDATE_INT);
    $id_curso      = filter_input(INPUT_POST, 'id_curso', FILTER_VALIDATE_INT);
    $id_parroquia  = filter_input(INPUT_POST, 'id_parroquia', FILTER_VALIDATE_INT);
    $id_periodo    = filter_input(INPUT_POST, 'id_periodo', FILTER_VALIDATE_INT);

    $tipo = trim($_POST['tipo'] ?? '');
    $estado = 0;

    $tiposValidos = [
        'Pre-bautismal',
        'Primera comunión',
        'Confirmación',
        'Matrimonial'
    ];

    if (!$id_feligres || !$id_periodo  || !$id_parroquia  || empty($tipo)) {
        throw new Exception('Campos obligatorios incompletos.');
    }

    if (!in_array($tipo, $tiposValidos, true)) {
        throw new Exception('Tipo de catequesis inválido.');
    }

    // ===============================
    // 2️⃣ VALIDAR EXISTENCIA FELIGRÉS
    // ===============================

    $stmt = $db->prepare("SELECT COUNT(*) FROM feligres WHERE id_feligres = ?");
    $stmt->execute([$id_feligres]);

    if ($stmt->fetchColumn() == 0) {
        throw new Exception('El feligrés no existe.');
    }

    // ===============================
    // 3️⃣ VALIDAR PERIODO ACTIVO
    // ===============================

    $stmt = $db->prepare("
        SELECT COUNT(*) 
        FROM periodo 
        WHERE id_periodo = ? 
        AND estado = 'activo'
    ");
    $stmt->execute([$id_periodo]);

    if ($stmt->fetchColumn() == 0) {
        throw new Exception('El período no existe o no está activo.');
    }

    // ===============================
    // 4️⃣ EVITAR DUPLICAR CATEQUESIS ACTIVA
    // ===============================

    if (!$id_catequesis) {

        $stmt = $db->prepare("
            SELECT COUNT(*) 
            FROM catequesis
            WHERE id_feligres = ?
            AND tipo = ?
            AND id_periodo = ?
        ");

        $stmt->execute([$id_feligres, $tipo, $id_periodo]);

        if ($stmt->fetchColumn() > 0) {
            throw new Exception('El feligrés ya está inscrito en esta catequesis en el período actual.');
        }
    }

    // ===============================
    // 5️⃣ VALIDAR JERARQUÍA SACRAMENTAL REAL
    // ===============================

    // Bautismo requerido para todo lo demás
    if ($tipo !== 'Pre-bautismal') {

        $stmt = $db->prepare("SELECT COUNT(*) FROM bautismo WHERE id_feligres = ?");
        $stmt->execute([$id_feligres]);

        if ($stmt->fetchColumn() == 0) {
            throw new Exception('Debe tener registrado el Bautismo antes de continuar.');
        }
    }

    // Comunión antes de Confirmación
    if ($tipo === 'Confirmación') {

        $stmt = $db->prepare("SELECT COUNT(*) FROM comunion WHERE id_feligres = ?");
        $stmt->execute([$id_feligres]);

        if ($stmt->fetchColumn() == 0) {
            throw new Exception('Debe haber recibido la Primera Comunión.');
        }
    }

    // Confirmación antes de Matrimonio
    if ($tipo === 'Matrimonial') {

        $stmt = $db->prepare("SELECT COUNT(*) FROM confirmacion WHERE id_feligres = ?");
        $stmt->execute([$id_feligres]);

        if ($stmt->fetchColumn() == 0) {
            throw new Exception('Debe estar confirmado antes del Matrimonio.');
        }
    }

    // ===============================
    // 6️⃣ TRANSACCIÓN
    // ===============================

    $db->beginTransaction();

    if (!$id_catequesis) {

        $stmt = $db->prepare("
            INSERT INTO catequesis
            (id_feligres, estado, id_curso, id_parroquia, id_periodo, tipo)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $id_feligres,
           $estado,
            $id_curso ?: null,
            $id_parroquia,
            $id_periodo,
            $tipo
        ]);

        $mensaje = 'Catequesis registrada correctamente.';
    } else {

        $stmt = $db->prepare("
            UPDATE catequesis SET
                estado = ?,
                id_curso = ?,
                id_parroquia = ?,
                id_periodo = ?,
                tipo = ?
            WHERE id_catequesis = ?
        ");

        $stmt->execute([
            $estado,
            $id_curso ?: null,
            $id_parroquia,
            $id_periodo,
            $tipo,
            $id_catequesis
        ]);

        $mensaje = 'Catequesis actualizada correctamente.';
    }

    $db->commit();

    echo json_encode([
        'success' => true,
        'message' => $mensaje
    ]);

} catch (Throwable $e) {

    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
