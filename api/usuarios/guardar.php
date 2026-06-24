<?php
// Desactivar visualización de errores HTML para que no rompan el JSON
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once '../../config/db.php';
header('Content-Type: application/json');

function limpiar($dato) {
    return htmlspecialchars(strip_tags(trim($dato)));
}

try {
    $db = new Database();
    $pdo = $db->getConnection();

    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $nombre = limpiar($_POST['nombre'] ?? '');
    $dni = limpiar($_POST['dni'] ?? '');
    $usuario = limpiar($_POST['usuario'] ?? '');
    $rol = limpiar($_POST['rol'] ?? '');
    $password = $_POST['contraseña'] ?? '';

    // Validaciones básicas
    if (strlen($nombre) < 3 || strlen($usuario) < 3) {
        throw new Exception('Datos insuficientes (nombre o usuario demasiado cortos)');
    }

    $roles_validos = ['admin','secretario','archivista','parroco'];
    if (!in_array($rol, $roles_validos)) {
        throw new Exception('Rol inválido');
    }

    if ($id > 0) {
        // ACTUALIZAR
        if (!empty($password)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET nombre=:nombre, dni=:dni, usuario=:usuario, rol=:rol, contraseña=:pass WHERE id=:id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':pass', $hash);
        } else {
            $sql = "UPDATE usuarios SET nombre=:nombre, dni=:dni, usuario=:usuario, rol=:rol WHERE id=:id";
            $stmt = $pdo->prepare($sql);
        }
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    } else {
        // CREAR NUEVO
        if (strlen($password) < 6) {
            throw new Exception('La contraseña debe tener mínimo 6 caracteres');
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios(nombre, dni, usuario, contraseña, rol, estado) VALUES(:nombre, :dni, :usuario, :pass, :rol, 1)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':pass', $hash);
    }

    // BindParams comunes a ambas operaciones
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':dni', $dni);
    $stmt->bindParam(':usuario', $usuario);
    $stmt->bindParam(':rol', $rol);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception('No se pudo ejecutar la consulta en la base de datos');
    }

} catch (Exception $e) {
    http_response_code(400); // Cambiado a 400 para errores de validación
    echo json_encode(['error' => $e->getMessage()]);
}