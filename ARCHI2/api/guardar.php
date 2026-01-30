<?php
/**
 * guardar.php
 * -----------
 * Backend genérico para guardar y actualizar registros
 * usando POST tradicional (sin fetch, sin AJAX)
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
$id = $_POST['id'] ?? null;

if ($modulo === '' || $accion === '') {
    die('Parámetros incompletos');
}

/* ===============================
   DEFINICIÓN DE TABLAS Y PK
================================ */
$config = [

    'usuarios' => [
        'tabla' => 'usuarios',
        'pk' => 'id',
        'campos' => ['nombre', 'dni', 'usuario', 'contraseña', 'rol', 'estado']
    ],

    'feligres' => [
        'tabla' => 'feligres',
        'pk' => 'id_feligres',
        'campos' => ['nombre_completo', 'nombre_padre', 'nombre_madre', 'fecha_nacimiento', 'lugar_nacimiento']
    ],

    'bautismo' => [
        'tabla' => 'bautismo',
        'pk' => 'id_bautismo',
        'campos' => ['registro', 'id_feligres', 'fecha', 'padrino', 'madrina', 'id_ministro', 'id_parroquia']
    ],

    'comunion' => [
        'tabla' => 'comunion',
        'pk' => 'id_comunion',
        'campos' => ['registro', 'id_feligres', 'fecha', 'id_ministro', 'id_parroquia']
    ],

    'confirmacion' => [
        'tabla' => 'confirmacion',
        'pk' => 'id_confirmacion',
        'campos' => ['registro', 'id_feligres', 'fecha', 'id_ministro', 'id_parroquia']
    ],

    'catequesis' => [
        'tabla' => 'catequesis',
        'pk' => 'id_catequesis',
        'campos' => ['id_feligres', 'nombre_catequesis', 'id_curso', 'id_parroquia', 'tipo']
    ],

    'catequista' => [
        'tabla' => 'catequista',
        'pk' => 'id_catequista',
        'campos' => ['nombre', 'telefono', 'especialidad']
    ],

    'curso' => [
        'tabla' => 'curso',
        'pk' => 'id_curso',
        'campos' => ['nombre', 'duracion', 'id_catequista', 'observaciones']
    ],

   'matrimonio' => [
    'tabla' => 'matrimonio',
    'pk'    => 'id_matrimonio',
    'campos'=> ['registro','fecha','id_ministro','lugar','estado']
],


    'ministros' => [
        'tabla' => 'ministros',
        'pk' => 'id_ministro',
        'campos' => ['nombre_completo', 'DIP', 'telefono', 'tipo']
    ],

    'pago' => [
        'tabla' => 'pago',
        'pk' => 'id_pago',
        'campos' => ['concepto', 'cantidad', 'recibido', 'cambio', 'id_feligres']
    ],

    'parroquia' => [
        'tabla' => 'parroquia',
        'pk' => 'id_parroquia',
        'campos' => ['nombre', 'direccion', 'telefono']
    ],
];

if (!isset($config[$modulo])) {
    die('Módulo no válido');
}

$tabla = $config[$modulo]['tabla'];
$pk = $config[$modulo]['pk'];
$campos = $config[$modulo]['campos'];

/* ===============================
   LIMPIAR Y PREPARAR DATOS
================================ */
$datos = [];

foreach ($campos as $campo) {

    if (!isset($_POST[$campo])) {
        continue;
    }

    // Contraseña (solo usuarios)
    if ($campo === 'contraseña') {
        if ($_POST[$campo] !== '') {
            $datos[$campo] = password_hash($_POST[$campo], PASSWORD_DEFAULT);
        }
        continue;
    }

    $datos[$campo] = trim($_POST[$campo]);
}

/* ===============================
   INSERTAR
================================ */
try {
    /* =========================================
   LÓGICA ESPECIAL: MATRIMONIO_FELIGRES
========================================= */
    $db->beginTransaction();
    if ($modulo === 'matrimonio') {

        // IDs recibidos del formulario
        $esposo = $_POST['esposo'] ?? null;
        $esposa = $_POST['esposa'] ?? null;
        $testigo1 = $_POST['testigo1'] ?? null;
        $testigo2 = $_POST['testigo2'] ?? null;

        if (!$esposo || !$esposa || !$testigo1 || !$testigo2) {
            throw new Exception('Debe seleccionar esposo, esposa y dos testigos');
        }

        // Si es creación, obtener ID insertado
        if ($accion === 'crear') {
            $idMatrimonio = $db->lastInsertId();
        } else {
            $idMatrimonio = $id;
        }

        // En edición: borrar relaciones previas
        $del = $db->prepare("DELETE FROM matrimonio_feligres WHERE id_matrimonio = ?");
        $del->execute([$idMatrimonio]);

        // Insertar relaciones
        $sqlRel = "
        INSERT INTO matrimonio_feligres (id_matrimonio, id_feligres, rol)
        VALUES (?, ?, ?)
    ";
        $stmtRel = $db->prepare($sqlRel);

        $stmtRel->execute([$idMatrimonio, $esposo, 'esposo']);
        $stmtRel->execute([$idMatrimonio, $esposa, 'esposa']);
        $stmtRel->execute([$idMatrimonio, $testigo1, 'testigo']);
        $stmtRel->execute([$idMatrimonio, $testigo2, 'testigo']);
    }else{

 


    /* =============== todos los demas modulos================== */

    if ($accion === 'crear') {

        if (empty($datos)) {
            throw new Exception('No hay datos para guardar');
        }

        $columnas = implode(',', array_keys($datos));
        $marcas = implode(',', array_fill(0, count($datos), '?'));

        $sql = "INSERT INTO $tabla ($columnas) VALUES ($marcas)";
        $stmt = $db->prepare($sql);
        $stmt->execute(array_values($datos));

    }

    /* ===============================
       ACTUALIZAR
    ================================ */
     elseif ($accion === 'editar') {

        if (!$id) {
            throw new Exception('ID no recibido');
        }

        $sets = [];
        foreach ($datos as $campo => $valor) {
            $sets[] = "$campo = ?";
        }

        if (empty($sets)) {
            throw new Exception('No hay datos para actualizar');
        }

        $sql = "UPDATE $tabla SET " . implode(',', $sets) . " WHERE $pk = ?";
        $stmt = $db->prepare($sql);

        $params = array_values($datos);
        $params[] = $id;

        $stmt->execute($params);
    } else {
        throw new Exception('Acción no válida');
    }
}
    /* ===============================
       REDIRECCIÓN FINAL
    ================================ */
    $db->commit();
    header("Location: ../dashboard-admin.php?success=1");
    exit;

} catch (Exception $e) {

    $db->rollBack();
    // Puedes mejorar esto con mensajes flash
    header("Location: ../dashboard-admin.php?error=" . urlencode($e->getMessage()));
    exit;
}
