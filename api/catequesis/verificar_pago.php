<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/db.php';
header('Content-Type: application/json');

try {
    $db = (new Database())->getConnection();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id_feligres = filter_input(INPUT_GET, 'id_feligres', FILTER_VALIDATE_INT);
    $concepto = trim($_GET['concepto'] ?? '');

    if (!$id_feligres) {
        throw new Exception('ID de feligrés no proporcionado');
    }

    // Buscar en la tabla pago
    $stmt = $db->prepare("
        SELECT COUNT(*) as total, SUM(cantidad) as total_monto 
        FROM pago 
        WHERE id_feligres = ? 
        AND concepto LIKE ?
        AND DATE(fecha) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    ");
    
    $conceptoBusqueda = '%' . $concepto . '%';
    $stmt->execute([$id_feligres, $conceptoBusqueda]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'tiene_pago' => ($result['total'] ?? 0) > 0,
        'cantidad_pagos' => $result['total'] ?? 0,
        'total_monto' => $result['total_monto'] ?? 0
    ]);

} catch (Throwable $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}