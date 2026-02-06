<?php
require_once '../config/db.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    exit(json_encode(['success' => false, 'message' => 'No autorizado']));
}

$database = new Database();
$db = $database->getConnection();

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$id = $_POST['id'] ?? $_GET['id'] ?? 0;

switch($action) {
    case 'get':
        // Obtener usuario por ID
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario) {
            echo json_encode([
                'success' => true,
                'data' => [
                    'id' => $usuario['id'],
                    'dni' => $usuario['dni'],
                    'nombre' => $usuario['nombre'],
                    'usuario' => $usuario['usuario'],
                    'rol' => $usuario['rol'],
                    'estado' => $usuario['estado'],
                    'fecha_registro' => $usuario['fecha_registro']
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
        }
        break;
        
    case 'crear':
        // Validar datos
        $required = ['dni', 'nombre', 'usuario', 'rol', 'estado', 'password'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                exit(json_encode(['success' => false, 'message' => "El campo $field es requerido"]));
            }
        }
        
        // Verificar si el usuario ya existe
        $check = $db->prepare("SELECT id FROM usuarios WHERE usuario = ? OR dni = ?");
        $check->execute([$_POST['usuario'], $_POST['dni']]);
        
        if ($check->rowCount() > 0) {
            exit(json_encode(['success' => false, 'message' => 'El usuario o DNI ya existen']));
        }
        
        // Crear usuario
        $stmt = $db->prepare("INSERT INTO usuarios (dni, nombre, usuario, contraseña, rol, estado) 
                             VALUES (?, ?, ?, ?, ?, ?)");
        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $success = $stmt->execute([
            $_POST['dni'],
            $_POST['nombre'],
            $_POST['usuario'],
            $password_hash,
            $_POST['rol'],
            $_POST['estado']
        ]);
        
        if ($success) {
            // Registrar actividad
            $actividad = $db->prepare("INSERT INTO actividades (id_usuario, accion, modulo, ip) 
                                      VALUES (?, ?, ?, ?)");
            $actividad->execute([
                $_SESSION['usuario_id'],
                "Creó nuevo usuario: {$_POST['usuario']}",
                'Usuarios',
                $_SERVER['REMOTE_ADDR']
            ]);
            
            echo json_encode(['success' => true, 'message' => 'Usuario creado exitosamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al crear usuario']);
        }
        break;
        
    case 'editar':
        // Validar datos
        if (empty($id)) {
            exit(json_encode(['success' => false, 'message' => 'ID de usuario requerido']));
        }
        
        // Actualizar usuario
        $stmt = $db->prepare("UPDATE usuarios SET dni = ?, nombre = ?, usuario = ?, rol = ?, estado = ? 
                             WHERE id = ?");
        $success = $stmt->execute([
            $_POST['dni'],
            $_POST['nombre'],
            $_POST['usuario'],
            $_POST['rol'],
            $_POST['estado'],
            $id
        ]);
        
        // Si se proporciona nueva contraseña
        if (!empty($_POST['password'])) {
            $pass_stmt = $db->prepare("UPDATE usuarios SET contraseña = ? WHERE id = ?");
            $pass_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $pass_stmt->execute([$pass_hash, $id]);
        }
        
        if ($success) {
            // Registrar actividad
            $actividad = $db->prepare("INSERT INTO actividades (id_usuario, accion, modulo, ip) 
                                      VALUES (?, ?, ?, ?)");
            $actividad->execute([
                $_SESSION['usuario_id'],
                "Editó usuario ID: $id",
                'Usuarios',
                $_SERVER['REMOTE_ADDR']
            ]);
            
            echo json_encode(['success' => true, 'message' => 'Usuario actualizado']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar']);
        }
        break;
        
    case 'eliminar':
        if (empty($id)) {
            exit(json_encode(['success' => false, 'message' => 'ID de usuario requerido']));
        }
        
        // Verificar que no sea el propio usuario
        if ($id == $_SESSION['usuario_id']) {
            exit(json_encode(['success' => false, 'message' => 'No puede eliminar su propio usuario']));
        }
        
        // Cambiar estado a inactivo
        $stmt = $db->prepare("UPDATE usuarios SET estado = 0 WHERE id = ?");
        $success = $stmt->execute([$id]);
        
        if ($success) {
            // Registrar actividad
            $actividad = $db->prepare("INSERT INTO actividades (id_usuario, accion, modulo, ip) 
                                      VALUES (?, ?, ?, ?)");
            $actividad->execute([
                $_SESSION['usuario_id'],
                "Eliminó usuario ID: $id",
                'Usuarios',
                $_SERVER['REMOTE_ADDR']
            ]);
            
            echo json_encode(['success' => true, 'message' => 'Usuario eliminado']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar']);
        }
        break;
        
    case 'buscar':
        // Búsqueda avanzada
        $params = [];
        $where = "1=1";
        
        if (!empty($_POST['busqueda'])) {
            $where .= " AND (nombre LIKE ? OR dni LIKE ? OR usuario LIKE ?)";
            $search = "%" . $_POST['busqueda'] . "%";
            $params = array_merge($params, [$search, $search, $search]);
        }
        
        if (!empty($_POST['estado'])) {
            $where .= " AND estado = ?";
            $params[] = $_POST['estado'];
        }
        
        if (!empty($_POST['rol'])) {
            $where .= " AND rol = ?";
            $params[] = $_POST['rol'];
        }
        
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE $where ORDER BY nombre LIMIT 100");
        $stmt->execute($params);
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'data' => $usuarios]);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
}
?>