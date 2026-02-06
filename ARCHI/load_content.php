<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    exit('Acceso denegado');
}

include_once 'config/db.php';
$database = new Database();
$bd = $database->getConnection();

$section = $_GET['section'] ?? 'inicio';

// Lista de secciones válidas para prevenir Inclusión de Archivo Local (LFI)
$validSections = [
    'inicio', 'feligreses', 'prebautismal', 'comunion-catequesis', 
    'confirmacion-catequesis', 'prematrimonial', 'bautismo', 'comunion', 
    'confirmacion', 'matrimonio', 'catequistas', 'ministros', 
    'parroquias', 'pagos', 'usuarios', 'actividades'
];

// Si la sección no es válida, se redirige a un caso de error seguro.
if (!in_array($section, $validSections)) {
    $section = 'default_content';
}

// --- Funciones de Utilidad ---

// Función para formatear fecha
function formatDate($date) {
    return $date ? date('d/m/Y', strtotime($date)) : 'No registrada';
}

// Función para búsqueda segura
function safeSearch($term) {
    return '%' . $term . '%';
}

// Función para obtener nombre completo del feligrés (con prepare/execute y sanitización de salida)
function getFeligresName($bd, $id) {
    $query = "SELECT nombres, apellidos FROM feligreses WHERE id = :id";
    $stmt = $bd->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    // Sanitización de la salida
    return $row ? htmlspecialchars($row['nombres'] . ' ' . $row['apellidos']) : 'N/A';
}

// Nueva función para obtener nombre de Parroquia
function getParroquiaName($bd, $id) {
    $query = "SELECT nombre FROM parroquias WHERE id = :id";
    $stmt = $bd->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? htmlspecialchars($row['nombre']) : 'N/A';
}

// Nueva función para obtener nombre de Usuario
function getUserName($bd, $id) {
    $query = "SELECT usuario FROM usuarios WHERE id = :id";
    $stmt = $bd->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? htmlspecialchars($row['usuario']) : 'N/A';
}

// --- Funciones de Listado General ---

function getFeligreses($bd) {
    $stmt = $bd->prepare("SELECT id, nombres, apellidos FROM feligreses WHERE estado = 'activo' ORDER BY nombres");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCatequistas($bd) {
    $stmt = $bd->prepare("SELECT id, nombres, apellidos FROM catequista WHERE estado = 'activo' ORDER BY nombres");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getMinistros($bd) {
    $stmt = $bd->prepare("SELECT id, nombres, apellidos FROM ministros WHERE estado = 'activo' ORDER BY nombres");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Nueva función para listar parroquias
function getParishList($bd) {
    $stmt = $bd->prepare("SELECT id, nombre, telefono FROM parroquias WHERE estado = 'activo' ORDER BY nombre");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Nueva función para listar usuarios
function getUsersList($bd) {
    $stmt = $bd->prepare("SELECT id, usuario, rol FROM usuarios WHERE estado = 'activo' ORDER BY usuario");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// --- Cargar Módulos según la sección ---
switch($section) {
    case 'inicio':
        include 'modulos/inicio.php';
        break;
        
    case 'feligreses':
        include 'modulos/feligreses.php';
        break;
        
    case 'prebautismal':
        include 'modulos/catequesis/prebautismal.php';
        break;
    
    case 'comunion-catequesis':
        include 'modulos/catequesis/comunion.php';
        break;
    
    case 'confirmacion-catequesis':
        include 'modulos/catequesis/confirmacion.php';
        break;

    case 'prematrimonial':
        include 'modulos/catequesis/prematrimonial.php';
        break;
        
    case 'bautismo':
        include 'modulos/sacramentos/bautismo.php';
        break;
    
    case 'comunion':
        include 'modulos/sacramentos/comunion.php';
        break;
        
    case 'confirmacion':
        include 'modulos/sacramentos/confirmacion.php';
        break;

    case 'matrimonio':
        include 'modulos/sacramentos/matrimonio.php';
        break;

    case 'catequistas':
        include 'modulos/administracion/catequistas.php';
        break;
            
    case 'ministros':
        include 'modulos/administracion/ministros.php';
        break;

    case 'parroquias':
        include 'modulos/administracion/parroquias.php';
        break;

    case 'pagos':
        include 'modulos/administracion/pagos.php';
        break;

    case 'usuarios':
        include 'modulos/usuarios.php';
        break;
    
    case 'actividades':
        include 'modulos/actividades.php';
        break;
        
    case 'default_content': // Manejo de sección no válida
    default:
        // Se sanitiza el valor original de $_GET['section'] si existe, para evitar XSS
        $safeSection = htmlspecialchars($_GET['section'] ?? 'Sección no especificada');
        echo "<h2>Error</h2><p>Contenido no válido o no permitido para la sección: **$safeSection**.</p>";
}
?>