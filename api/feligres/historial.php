<?php
require_once '../../config/db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
        http_response_code(400);
        echo json_encode(['error' => 'ID de feligrés no válido']);
        exit;
    }

    $db = (new Database())->getConnection();
    $id = $_GET['id'];

    // 1. DATOS MAESTROS (incluyendo género)
    $stmt = $db->prepare("SELECT * FROM feligres WHERE id_feligres=?");
    $stmt->execute([$id]);
    $f = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$f) {
        http_response_code(404);
        echo json_encode(['error' => 'Registro no encontrado']);
        exit;
    }

    // 2. FORMACIÓN (CATEQUESIS)
    $stmt = $db->prepare("
        SELECT 
            c.tipo, 
            cu.nombre AS curso_nombre,
            ca.nombre AS catequista_nombre
        FROM catequesis c
        LEFT JOIN curso cu ON c.id_curso = cu.id_curso
        LEFT JOIN catequista ca ON cu.id_catequista = ca.id_catequista
        WHERE c.id_feligres = ?
    ");
    $stmt->execute([$id]);
    $catequesis = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $htmlCatequesis = "<h6 class='mb-2'>📚 Formación Catequística</h6><p class='text-muted small'>Sin formación registrada</p>";
    if ($catequesis) {
        $htmlCatequesis = "<h6 class='mb-2'>📚 Formación Catequística</h6><ul class='list-group list-group-flush border rounded shadow-sm'>";
        foreach ($catequesis as $c) {
            $htmlCatequesis .= "
            <li class='list-group-item'>
                <div class='fw-bold text-primary small text-uppercase'>".($c['tipo'] ?? 'Catequesis')."</div>
                <div class='text-dark'>".($c['curso_nombre'] ?? 'Curso no especificado')."</div>
                <div class='small text-muted'><i class='bi bi-person'></i> Catequista: ".($c['catequista_nombre'] ?? 'No asignado')."</div>
            </li>";
        }
        $htmlCatequesis .= "</ul>";
    }

    // 3. FLUJO DE CAJA (PAGOS)
    $stmt = $db->prepare("SELECT concepto, cantidad, recibido, cambio FROM pago WHERE id_feligres=? ORDER BY id_pago DESC");
    $stmt->execute([$id]);
    $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $htmlPagos = "<h6 class='mb-2 mt-3'>💰 Aportaciones</h6><p class='text-muted small'>No hay registro de aportaciones</p>";
    if ($pagos) {
        $htmlPagos = "<h6 class='mb-2 mt-3'>💰 Aportaciones</h6><div class='table-responsive'><table class='table table-sm table-hover border'>
            <thead class='table-dark small text-uppercase'>
                <tr><th>Concepto</th><th class='text-end'>Monto</th></tr>
            </thead><tbody>";
        foreach ($pagos as $p) {
            $htmlPagos .= "<tr>
                <td class='small'>{$p['concepto']}</td>
                <td class='small fw-bold text-end text-success'>$ ".number_format($p['cantidad'], 2)."</td>
            </tr>";
        }
        $htmlPagos .= "</tbody></table></div>";
    }

    // 4. SACRAMENTOS
    $stmt = $db->prepare("
        SELECT 'Bautismo' AS sac, fecha FROM bautismo WHERE id_feligres=?
        UNION
        SELECT 'Comunión', fecha FROM comunion WHERE id_feligres=?
        UNION
        SELECT 'Confirmación', fecha FROM confirmacion WHERE id_feligres=?
        ORDER BY fecha ASC
    ");
    $stmt->execute([$id, $id, $id]);
    $sacramentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $htmlSacramentos = "<h6 class='mb-2'>⛪ Sacramentos</h6><div class='d-flex flex-wrap gap-2'>";
    if ($sacramentos) {
        foreach ($sacramentos as $s) {
            $f_formato = $s['fecha'] ? date("d/m/Y", strtotime($s['fecha'])) : 'Pendiente';
            $htmlSacramentos .= "<div class='badge p-2 border text-dark bg-white shadow-sm'>
                <i class='bi bi-check-circle-fill text-success'></i> {$s['sac']}: <span class='text-muted fw-normal'>{$f_formato}</span>
            </div>";
        }
    } else {
        $htmlSacramentos .= "<p class='text-muted small'>Ningún sacramento registrado</p>";
    }
    $htmlSacramentos .= "</div>";

    // RESPUESTA FINAL
    echo json_encode([
        'feligres' => $f,
        'catequesis' => $htmlCatequesis,
        'donaciones' => $htmlPagos,
        'sacramentos' => $htmlSacramentos
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error en el sistema de gestión',
        'detalle' => $e->getMessage()
    ]);
}
?>