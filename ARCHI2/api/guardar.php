<?php
/**
 * guardar.php
 * Backend genérico para crear y editar registros
 * Compatible con módulos simples y con tablas auxiliares
 */

session_start();
require_once '../config/db.php';

$database = new Database();
$db = $database->getConnection();

/* ===============================
   VALIDACIONES BÁSICAS
================================ */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Acceso no permitido');
}

$modulo = $_POST['modulo'] ?? '';
$accion = $_POST['accion'] ?? '';
$id     = $_POST['id'] ?? null;

if ($modulo === '' || $accion === '') {
    die('Parámetros incompletos');
}

/* ===============================
   CONFIGURACIÓN DE MÓDULOS
================================ */
$config = [

    'feligres' => [
        'tabla' => 'feligres',
        'pk' => 'id_feligres',
        'campos' => ['nombre_completo','nombre_padre','nombre_madre','fecha_nacimiento','lugar_nacimiento']
    ],

    'bautismo' => [
        'tabla' => 'bautismo',
        'pk' => 'id_bautismo',
        'campos' => ['registro','id_feligres','fecha','padrino','madrina','id_ministro','id_parroquia']
    ],

    'comunion' => [
        'tabla' => 'comunion',
        'pk' => 'id_comunion',
        'campos' => ['registro','id_feligres','fecha','id_ministro','id_parroquia']
    ],

    'confirmacion' => [
        'tabla' => 'confirmacion',
        'pk' => 'id_confirmacion',
        'campos' => ['registro','id_feligres','fecha','id_ministro','id_parroquia']
    ],

    'matrimonio' => [
        'tabla' => 'matrimonio',
        'pk' => 'id_matrimonio',
        'campos' => ['registro','fecha','id_ministro','lugar','estado']
    ],

    'ministros' => [
        'tabla' => 'ministros',
        'pk' => 'id_ministro',
        'campos' => ['nombre_completo','DIP','telefono','tipo']
    ],

    'parroquia' => [
        'tabla' => 'parroquia',
        'pk' => 'id_parroquia',
        'campos' => ['nombre','direccion','telefono']
    ]
];

if (!isset($config[$modulo])) {
    die('Módulo no válido');
}

$tabla  = $config[$modulo]['tabla'];
$pk     = $config[$modulo]['pk'];
$campos = $config[$modulo]['campos'];

/* ===============================
   LIMPIAR DATOS
================================ */
$datos = [];

foreach ($campos as $campo) {
    if (isset($_POST[$campo])) {
        $datos[$campo] = trim($_POST[$campo]);
    }
}

if (empty($datos)) {
    die('No hay datos para guardar');
}

/* ===============================
   TRANSACCIÓN
================================ */
try {
    $db->beginTransaction();

    /* ===============================
       CREAR
    ================================ */
    if ($accion === 'crear') {

        $columnas = implode(',', array_keys($datos));
        $marcas   = implode(',', array_fill(0, count($datos), '?'));

        $sql = "INSERT INTO $tabla ($columnas) VALUES ($marcas)";
        $stmt = $db->prepare($sql);
        $stmt->execute(array_values($datos));

        $idRegistro = $db->lastInsertId();
    }

    /* ===============================
       EDITAR
    ================================ */
    elseif ($accion === 'editar') {

        if (!$id) {
            throw new Exception('ID no recibido');
        }

        $sets = [];
        foreach ($datos as $campo => $valor) {
            $sets[] = "$campo = ?";
        }

        $sql = "UPDATE $tabla SET " . implode(',', $sets) . " WHERE $pk = ?";
        $stmt = $db->prepare($sql);

        $params = array_values($datos);
        $params[] = $id;

        $stmt->execute($params);

        $idRegistro = $id;
    }
    else {
        throw new Exception('Acción no válida');
    }

    /* ==================================================
       LÓGICA ESPECIAL: MATRIMONIO_FELIGRES
    =================================================== */
    if ($modulo === 'matrimonio') {

        $esposo   = $_POST['esposo'] ?? null;
        $esposa   = $_POST['esposa'] ?? null;
        $testigo1 = $_POST['testigo1'] ?? null;
        $testigo2 = $_POST['testigo2'] ?? null;

        if (!$esposo || !$esposa || !$testigo1 || !$testigo2) {
            throw new Exception('Debe seleccionar esposo, esposa y dos testigos');
        }

        // Limpiar relaciones anteriores (en edición)
        $del = $db->prepare("DELETE FROM matrimonio_feligres WHERE id_matrimonio = ?");
        $del->execute([$idRegistro]);

        // Insertar nuevas relaciones
        $sqlRel = "
            INSERT INTO matrimonio_feligres (id_matrimonio, id_feligres, rol)
            VALUES (?, ?, ?)
        ";
        $rel = $db->prepare($sqlRel);

        $rel->execute([$idRegistro, $esposo, 'esposo']);
        $rel->execute([$idRegistro, $esposa, 'esposa']);
        $rel->execute([$idRegistro, $testigo1, 'testigo']);
        $rel->execute([$idRegistro, $testigo2, 'testigo']);
    }

    /* ===============================
       FINAL
    ================================ */
    $db->commit();
    header("Location: ../dashboard-admin.php?success=1");
    exit;

} catch (Exception $e) {

    $db->rollBack();
    header("Location: ../dashboard-admin.php?error=" . urlencode($e->getMessage()));
    exit;
}
