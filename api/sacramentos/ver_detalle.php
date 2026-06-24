<?php
require '../../config/db.php';
header('Content-Type: application/json');

$db = (new Database())->getConnection();

$id   = $_GET['id']   ?? null;
$tipo = $_GET['tipo'] ?? null;

if (!$id || !$tipo) {
    echo json_encode([]);
    exit;
}

try {

    switch ($tipo) {

        /* ================= BAUTISMO ================= */
        case 'bautismo':
            $sql = "
            SELECT 
                b.registro,
                b.fecha,
                b.padrino,
                b.madrina,
                f.nombre_completo AS feligres_nombre,
                f.nombre_padre,
                f.nombre_madre,
                f.lugar_nacimiento,
                m.nombre_completo AS ministro_nombre,
                m.tipo AS ministro_rango,
                p.nombre AS parroquia_nombre,
                p.direccion AS parroquia_dir
            FROM bautismo b
            JOIN feligres f ON f.id_feligres = b.id_feligres
            LEFT JOIN ministros m ON m.id_ministro = b.id_ministro
            LEFT JOIN parroquia p ON p.id_parroquia = b.id_parroquia
            WHERE b.id_bautismo = ?
            ";
            break;

        /* ================= COMUNIÓN ================= */
        case 'comunion':
            $sql = "
            SELECT 
                c.registro,
                c.fecha,
                f.nombre_completo AS feligres_nombre,
                f.nombre_padre,
                f.nombre_madre,
                f.lugar_nacimiento,
                m.nombre_completo AS ministro_nombre,
                m.tipo AS ministro_rango,
                p.nombre AS parroquia_nombre,
                p.direccion AS parroquia_dir
            FROM comunion c
            JOIN feligres f ON f.id_feligres = c.id_feligres
            LEFT JOIN ministros m ON m.id_ministro = c.id_ministro
            LEFT JOIN parroquia p ON p.id_parroquia = c.id_parroquia
            WHERE c.id_comunion = ?
            ";
            break;

        /* ================= CONFIRMACIÓN ================= */
        case 'confirmacion':
            $sql = "
            SELECT 
                c.registro,
                c.fecha,
                f.nombre_completo AS feligres_nombre,
                f.nombre_padre,
                f.nombre_madre,
                f.lugar_nacimiento,
                m.nombre_completo AS ministro_nombre,
                m.tipo AS ministro_rango,
                p.nombre AS parroquia_nombre,
                p.direccion AS parroquia_dir
            FROM confirmacion c
            JOIN feligres f ON f.id_feligres = c.id_feligres
            LEFT JOIN ministros m ON m.id_ministro = c.id_ministro
            LEFT JOIN parroquia p ON p.id_parroquia = c.id_parroquia
            WHERE c.id_confirmacion = ?
            ";
            break;

        /* ================= MATRIMONIO ================= */
        case 'matrimonio':
            $sql = "
            SELECT 
                m.registro,
                m.fecha,
                m.lugar,
                m.estado,
                mi.nombre_completo AS ministro_nombre,
                mi.tipo AS ministro_rango,
                GROUP_CONCAT(CONCAT(f.nombre_completo,' (',mf.rol,')') SEPARATOR ' | ') AS participantes
            FROM matrimonio m
            JOIN ministros mi ON mi.id_ministro = m.id_ministro
            JOIN matrimonio_feligres mf ON mf.id_matrimonio = m.id_matrimonio
            JOIN feligres f ON f.id_feligres = mf.id_feligres
            WHERE m.id_matrimonio = ?
            GROUP BY m.id_matrimonio
            ";
            break;

        default:
            echo json_encode([]);
            exit;
    }

    $stmt = $db->prepare($sql);
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($data ?: []);

} catch (Throwable $e) {
    echo json_encode([
        'error' => 'Error al obtener el detalle',
        'debug' => $e->getMessage()
    ]);
}
