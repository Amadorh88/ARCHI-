<?php
require '../../config/db.php';
$db = (new Database())->getConnection();

// Obtener ID del periodo
$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(['error' => true, 'message' => 'ID no proporcionado']);
    exit;
}

try {
    // 1️⃣ Obtener datos básicos del periodo
    $stmt = $db->prepare("SELECT * FROM periodo WHERE id_periodo = ?");
    $stmt->execute([$id]);
    $periodo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$periodo) {
        echo json_encode(['error' => true, 'message' => 'Periodo no encontrado']);
        exit;
    }

    // 2️⃣ Obtener catequesis vinculadas
    $stmt = $db->prepare("
        SELECT c.*, f.nombre_completo AS feligres_nombre, cu.nombre AS curso_nombre
        FROM catequesis c
        LEFT JOIN feligres f ON f.id_feligres = c.id_feligres
        LEFT JOIN curso cu ON cu.id_curso = c.id_curso
        WHERE c.id_periodo = ?
    ");
    $stmt->execute([$id]);
    $catequesis = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3️⃣ Obtener bautismos vinculados
    $stmt = $db->prepare("
        SELECT b.*, f.nombre_completo AS feligres_nombre
        FROM bautismo b
        LEFT JOIN feligres f ON f.id_feligres = b.id_feligres
        WHERE b.fecha BETWEEN ? AND ?
    ");
    $stmt->execute([$periodo['fecha_inicio'], $periodo['fecha_fin']]);
    $bautismos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 4️⃣ Obtener comuniones vinculadas
    $stmt = $db->prepare("
        SELECT co.*, f.nombre_completo AS feligres_nombre
        FROM comunion co
        LEFT JOIN feligres f ON f.id_feligres = co.id_feligres
        WHERE co.fecha BETWEEN ? AND ?
    ");
    $stmt->execute([$periodo['fecha_inicio'], $periodo['fecha_fin']]);
    $comuniones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 5️⃣ Obtener confirmaciones vinculadas
    $stmt = $db->prepare("
        SELECT cf.*, f.nombre_completo AS feligres_nombre
        FROM confirmacion cf
        LEFT JOIN feligres f ON f.id_feligres = cf.id_feligres
        WHERE cf.fecha BETWEEN ? AND ?
    ");
    $stmt->execute([$periodo['fecha_inicio'], $periodo['fecha_fin']]);
    $confirmaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 6️⃣ Obtener matrimonios vinculados
    $stmt = $db->prepare("
        SELECT m.*, GROUP_CONCAT(f.nombre_completo SEPARATOR ', ') AS esposos
        FROM matrimonio m
        LEFT JOIN matrimonio_feligres mf ON mf.id_matrimonio = m.id_matrimonio
        LEFT JOIN feligres f ON f.id_feligres = mf.id_feligres
        WHERE m.fecha BETWEEN ? AND ?
        GROUP BY m.id_matrimonio
    ");
    $stmt->execute([$periodo['fecha_inicio'], $periodo['fecha_fin']]);
    $matrimonios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 7️⃣ Retornar todo en JSON
    echo json_encode([
        'error' => false,
        'periodo' => $periodo,
        'eventos' => [
            'catequesis' => $catequesis,
            'bautismos' => $bautismos,
            'comuniones' => $comuniones,
            'confirmaciones' => $confirmaciones,
            'matrimonios' => $matrimonios
        ]
    ]);

} catch (PDOException $e) {
    echo json_encode(['error' => true, 'message' => 'Error al obtener periodo: ' . $e->getMessage()]);
}
