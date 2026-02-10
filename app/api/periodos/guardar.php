<?php
require '../../config/db.php';
$db = (new Database())->getConnection();

header('Content-Type: application/json');

try {
    $db->beginTransaction();

    // --- RECIBIR DATOS ---
    $id_periodo = $_POST['id_periodo'] ?? null;
    $anio_inicio = isset($_POST['fecha_inicio']) ? (int) $_POST['fecha_inicio'] : null;
    $anio_fin = isset($_POST['fecha_fin']) ? (int) $_POST['fecha_fin'] : null;
    $estado = $_POST['estado'] ?? 'activo';

    $anioActual = (int) date("Y");

    // --- VALIDACIONES ---
    if (!$anio_inicio || !$anio_fin) {
        throw new Exception("Los años de inicio y fin son obligatorios.");
    }

    if ($anio_inicio > $anio_fin) {
        throw new Exception("El año de inicio no puede ser mayor que el año de fin.");
    }
    $intervalo = $anio_fin - $anio_inicio;
    if ($intervalo !== 1) {
        throw new Exception("El periodo debe tener exactamente 1 año de diferencia entre inicio y fin.");
    }

    if ($anio_inicio > $anioActual) {
        throw new Exception("El año de inicio no puede ser mayor al año actual.");
    }

    // =====================================================
    // 🔒 SOLO SE PUEDE ACTUALIZAR SI ESTÁ ACTIVO
    // =====================================================
    if ($id_periodo) {
        $stmtEstado = $db->prepare("SELECT estado FROM periodo WHERE id_periodo = ?");
        $stmtEstado->execute([$id_periodo]);
        $periodoActual = $stmtEstado->fetch(PDO::FETCH_ASSOC);

        if (!$periodoActual) {
            throw new Exception("El periodo no existe.");
        }

        if ($periodoActual['estado'] !== 'activo') {
            throw new Exception("No se puede actualizar un periodo que no está activo.");
        }
    }

    // =====================================================
    // 🔍 VALIDACIÓN LÓGICA DE DUPLICADOS
    // =====================================================
    if ($id_periodo) {
        $stmt = $db->prepare("
            SELECT COUNT(*) 
            FROM periodo 
            WHERE fecha_inicio = ? 
              AND fecha_fin = ? 
              AND id_periodo != ?
        ");
        $stmt->execute([$anio_inicio, $anio_fin, $id_periodo]);
    } else {
        $stmt = $db->prepare("
            SELECT COUNT(*) 
            FROM periodo 
            WHERE fecha_inicio = ? 
              AND fecha_fin = ?
        ");
        $stmt->execute([$anio_inicio, $anio_fin]);
    }

    if ($stmt->fetchColumn() > 0) {
        throw new Exception("Ya existe un periodo registrado con esos años.");
    }

    // =====================================================
    // 💾 GUARDAR
    // =====================================================
    if ($id_periodo) {
        $stmt = $db->prepare("
            UPDATE periodo 
            SET fecha_inicio = ?, fecha_fin = ?, estado = ?
            WHERE id_periodo = ?
        ");
        $stmt->execute([$anio_inicio, $anio_fin, $estado, $id_periodo]);
        $message = "Periodo actualizado correctamente.";
    } else {
        $stmt = $db->prepare("
            INSERT INTO periodo (fecha_inicio, fecha_fin, estado)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$anio_inicio, $anio_fin, $estado]);
        $message = "Periodo registrado correctamente.";
    }

    $db->commit();

    echo json_encode([
        'success' => true,
        'message' => $message
    ]);

} catch (PDOException $e) {
    $db->rollBack();
    if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) {
        echo json_encode([
            'success' => false,
            'message' => "Ya existe un periodo registrado con esos años."
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => "Error en la base de datos."
        ]);
    }
} catch (Exception $e) {
    $db->rollBack();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
