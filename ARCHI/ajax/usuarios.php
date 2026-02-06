<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

// Verificar autenticación
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'get':
            // Obtener usuario por ID
            if (isset($_GET['id'])) {
                $stmt = $bd->prepare("SELECT id, nombre, dni, usuario, rol, estado, fecha_registro FROM usuarios WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($usuario) {
                    echo json_encode(['success' => true, 'usuario' => $usuario]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
                }
            }
            break;
            
        case 'ver':
            // Ver detalles del usuario
            if (isset($_GET['id'])) {
                $stmt = $bd->prepare("SELECT id, nombre, dni, usuario, rol, estado, fecha_registro FROM usuarios WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($usuario) {
                    echo json_encode(['success' => true, 'usuario' => $usuario]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
                }
            }
            break;
            
        case 'crear':
            // Crear nuevo usuario
            $nombre = $_POST['nombre'] ?? '';
            $dni = $_POST['dni'] ?? '';
            $usuario = $_POST['usuario'] ?? '';
            $contrasena = $_POST['contrasena'] ?? '';
            $rol = $_POST['rol'] ?? '';
            $estado = $_POST['estado'] ?? 1;
            
            // Validar campos requeridos
            if (empty($nombre) || empty($dni) || empty($usuario) || empty($contrasena) || empty($rol)) {
                echo json_encode(['success' => false, 'message' => 'Todos los campos son requeridos']);
                exit;
            }
            
            // Verificar si el DNI ya existe
            $stmt = $bd->prepare("SELECT id FROM usuarios WHERE dni = ?");
            $stmt->execute([$dni]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'El DNI ya está registrado']);
                exit;
            }
            
            // Verificar si el usuario ya existe
            $stmt = $bd->prepare("SELECT id FROM usuarios WHERE usuario = ?");
            $stmt->execute([$usuario]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'El nombre de usuario ya está en uso']);
                exit;
            }
            
            // Hash de la contraseña
            $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);
            
            // Insertar nuevo usuario
            $stmt = $bd->prepare("INSERT INTO usuarios (nombre, dni, usuario, contraseña, rol, estado) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nombre, $dni, $usuario, $contrasena_hash, $rol, $estado]);
            
            // Registrar actividad
            registrarActividad($_SESSION['usuario_id'], "Creó un nuevo usuario: $nombre", "Usuarios");
            
            echo json_encode(['success' => true, 'message' => 'Usuario creado correctamente']);
            break;
            
        case 'editar':
            // Editar usuario existente
            $id = $_POST['id'] ?? '';
            $nombre = $_POST['nombre'] ?? '';
            $dni = $_POST['dni'] ?? '';
            $usuario = $_POST['usuario'] ?? '';
            $contrasena = $_POST['contrasena'] ?? '';
            $rol = $_POST['rol'] ?? '';
            $estado = $_POST['estado'] ?? 1;
            
            if (empty($id)) {
                echo json_encode(['success' => false, 'message' => 'ID de usuario no especificado']);
                exit;
            }
            
            // Verificar si el DNI ya existe en otro usuario
            $stmt = $bd->prepare("SELECT id FROM usuarios WHERE dni = ? AND id != ?");
            $stmt->execute([$dni, $id]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'El DNI ya está registrado en otro usuario']);
                exit;
            }
            
            // Verificar si el usuario ya existe en otro registro
            $stmt = $bd->prepare("SELECT id FROM usuarios WHERE usuario = ? AND id != ?");
            $stmt->execute([$usuario, $id]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'El nombre de usuario ya está en uso por otro usuario']);
                exit;
            }
            
            // Preparar la consulta de actualización
            if (!empty($contrasena)) {
                // Actualizar con nueva contraseña
                $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);
                $stmt = $bd->prepare("UPDATE usuarios SET nombre = ?, dni = ?, usuario = ?, contraseña = ?, rol = ?, estado = ? WHERE id = ?");
                $stmt->execute([$nombre, $dni, $usuario, $contrasena_hash, $rol, $estado, $id]);
            } else {
                // Actualizar sin cambiar contraseña
                $stmt = $bd->prepare("UPDATE usuarios SET nombre = ?, dni = ?, usuario = ?, rol = ?, estado = ? WHERE id = ?");
                $stmt->execute([$nombre, $dni, $usuario, $rol, $estado, $id]);
            }
            
            // Registrar actividad
            registrarActividad($_SESSION['usuario_id'], "Editó el usuario: $nombre", "Usuarios");
            
            echo json_encode(['success' => true, 'message' => 'Usuario actualizado correctamente']);
            break;
            
        case 'eliminar':
            // Eliminar usuario
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
                
                // Verificar si el usuario a eliminar es el mismo que está logueado
                if ($id == $_SESSION['usuario_id']) {
                    echo json_encode(['success' => false, 'message' => 'No puede eliminar su propio usuario']);
                    exit;
                }
                
                // Obtener información del usuario antes de eliminarlo
                $stmt = $bd->prepare("SELECT nombre FROM usuarios WHERE id = ?");
                $stmt->execute([$id]);
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$usuario) {
                    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
                    exit;
                }
                
                // Eliminar usuario
                $stmt = $bd->prepare("DELETE FROM usuarios WHERE id = ?");
                $stmt->execute([$id]);
                
                // Registrar actividad
                registrarActividad($_SESSION['usuario_id'], "Eliminó el usuario: " . $usuario['nombre'], "Usuarios");
                
                echo json_encode(['success' => true, 'message' => 'Usuario eliminado correctamente']);
            }
            break;
            
        case 'cambiar_estado':
            // Cambiar estado del usuario (activar/desactivar)
            if (isset($_GET['id']) && isset($_GET['estado'])) {
                $id = $_GET['id'];
                $estado = $_GET['estado'];
                
                // Verificar si el usuario a modificar es el mismo que está logueado
                if ($id == $_SESSION['usuario_id'] && $estado == 0) {
                    echo json_encode(['success' => false, 'message' => 'No puede desactivar su propio usuario']);
                    exit;
                }
                
                // Obtener información del usuario
                $stmt = $bd->prepare("SELECT nombre FROM usuarios WHERE id = ?");
                $stmt->execute([$id]);
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$usuario) {
                    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
                    exit;
                }
                
                // Actualizar estado
                $stmt = $bd->prepare("UPDATE usuarios SET estado = ? WHERE id = ?");
                $stmt->execute([$estado, $id]);
                
                $accion = $estado == 1 ? 'activó' : 'desactivó';
                registrarActividad($_SESSION['usuario_id'], "$accion el usuario: " . $usuario['nombre'], "Usuarios");
                
                echo json_encode(['success' => true, 'message' => 'Estado actualizado correctamente']);
            }
            break;
            
        case 'reset_password':
            // Resetear contraseña del usuario
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
                
                // Generar nueva contraseña aleatoria
                $nueva_password = generarContrasenaAleatoria();
                $contrasena_hash = password_hash($nueva_password, PASSWORD_DEFAULT);
                
                // Actualizar contraseña
                $stmt = $bd->prepare("UPDATE usuarios SET contraseña = ? WHERE id = ?");
                $stmt->execute([$contrasena_hash, $id]);
                
                // Obtener información del usuario
                $stmt = $bd->prepare("SELECT nombre FROM usuarios WHERE id = ?");
                $stmt->execute([$id]);
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Registrar actividad
                registrarActividad($_SESSION['usuario_id'], "Reseteó la contraseña del usuario: " . $usuario['nombre'], "Usuarios");
                
                echo json_encode(['success' => true, 'nueva_password' => $nueva_password, 'message' => 'Contraseña reseteada correctamente']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Acción no válida']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
}

// Función para generar contraseña aleatoria
function generarContrasenaAleatoria($longitud = 8) {
    $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $contrasena = '';
    for ($i = 0; $i < $longitud; $i++) {
        $contrasena .= $caracteres[rand(0, strlen($caracteres) - 1)];
    }
    return $contrasena;
}

// Función para registrar actividad
function registrarActividad($id_usuario, $accion, $modulo) {
    global $bd;
    $ip = $_SERVER['REMOTE_ADDR'];
    
    $stmt = $bd->prepare("INSERT INTO actividades (id_usuario, accion, modulo, ip) VALUES (?, ?, ?, ?)");
    $stmt->execute([$id_usuario, $accion, $modulo, $ip]);
}
?>