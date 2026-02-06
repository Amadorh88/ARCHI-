<?php
/**
 * Procesador de eliminación dinámica de registros
 */
session_start();
header('Content-Type: application/json');

// 1. Verificar sesión y permisos
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Sesión no válida']);
    exit;
}

// Opcional: Solo permitir a ciertos roles (ej: admin o secretaria)
$rolesPermitidos = ['admin', 'secretaria'];
if (!in_array($_SESSION['rol'], $rolesPermitidos)) {
    echo json_encode(['success' => false, 'message' => 'No tienes permisos para eliminar registros']);
    exit;
}

// 2. Incluir conexión a la base de datos
require_once '../config/db.php';
$database = new Database();
$bd = $database->getConnection();

// 3. Obtener los datos enviados por el Fetch (JSON)
$input = json_decode(file_get_contents('php://input'), true);

$modulo = $input['modulo'] ?? '';
$id = $input['id'] ?? 0;

if (empty($modulo) || empty($id)) {
    echo json_encode(['success' => false, 'message' => 'Datos insuficientes para la eliminación']);
    exit;
}

/**
 * 4. Mapeo de Tablas y sus Llaves Primarias
 * Esto evita ataques de inyección al validar que el módulo sea real
 */
$tablasPermitidas = [
    'usuario'    => ['tabla' => 'usuarios',   'pk' => 'id'],
    'feligres'   => ['tabla' => 'feligres',   'pk' => 'id_feligres'],
    'matrimonio' => ['tabla' => 'matrimonio', 'pk' => 'id_matrimonio'],
    'ministro'   => ['tabla' => 'ministros',  'pk' => 'id_ministro'],
    'parroquia'  => ['tabla' => 'parroquia',  'pk' => 'id_parroquia']
];

if (!array_key_exists($modulo, $tablasPermitidas)) {
    echo json_encode(['success' => false, 'message' => 'Módulo no reconocido']);
    exit;
}

$tablaReal = $tablasPermitidas[$modulo]['tabla'];
$pkReal = $tablasPermitidas[$modulo]['pk'];

try {
    // 5. Ejecutar eliminación
    $sql = "DELETE FROM $tablaReal WHERE $pkReal = :id";
    $stmt = $bd->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        // Opcional: Podrías registrar aquí una actividad en la tabla 'actividades'
        // registrarLog($_SESSION['usuario_id'], 'Eliminación', $modulo, $id);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Registro eliminado correctamente'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'No se pudo eliminar el registro de la base de datos'
        ]);
    }

} catch (PDOException $e) {
    // Manejo de errores de integridad referencial (ej: si el feligrés tiene un bautismo asociado)
    if ($e->getCode() == '23000') {
        echo json_encode([
            'success' => false, 
            'message' => 'No se puede eliminar: el registro está vinculado a otros datos (ej. sacramentos o actas).'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Error de base de datos: ' . $e->getMessage()
        ]);
    }
}