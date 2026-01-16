<?php
session_start();
include_once 'config/database.php';

if (!isset($_SESSION['usuario_id'])) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    
    $usuario_id = $_POST['id'];
    $username = $_POST['username'];
    $nombre_completo = $_POST['nombre_completo'];
    $password_actual = $_POST['password_actual'] ?? '';
    $nueva_password = $_POST['nueva_password'] ?? '';
    
    // Verificar que el usuario solo pueda editar su propio perfil
    if ($usuario_id != $_SESSION['usuario_id']) {
        echo json_encode(['success' => false, 'message' => 'No tiene permisos para editar este perfil']);
        exit();
    }
    
    try {
        // Obtener usuario actual
        $query = "SELECT username, password FROM usuarios WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $usuario_id);
        $stmt->execute();
        
        if ($stmt->rowCount() == 1) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verificar contraseña actual si se quiere cambiar la contraseña
            if (!empty($nueva_password)) {
                if (empty($password_actual)) {
                    echo json_encode(['success' => false, 'message' => 'Debe ingresar la contraseña actual']);
                    exit();
                }
                
                if (!password_verify($password_actual, $usuario['password'])) {
                    echo json_encode(['success' => false, 'message' => 'Contraseña actual incorrecta']);
                    exit();
                }
                
                // Actualizar con nueva contraseña
                $hashed_password = password_hash($nueva_password, PASSWORD_DEFAULT);
                $query = "UPDATE usuarios SET username = :username, nombre_completo = :nombre_completo, password = :password WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':password', $hashed_password);
            } else {
                // Actualizar sin cambiar contraseña
                $query = "UPDATE usuarios SET username = :username, nombre_completo = :nombre_completo WHERE id = :id";
                $stmt = $db->prepare($query);
            }
            
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':nombre_completo', $nombre_completo);
            $stmt->bindParam(':id', $usuario_id);
            
            if ($stmt->execute()) {
                // Actualizar datos en la sesión
                $_SESSION['username'] = $username;
                $_SESSION['nombre_completo'] = $nombre_completo;
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Perfil actualizado correctamente',
                    'data' => [
                        'username' => $username,
                        'nombre_completo' => $nombre_completo
                    ]
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar el perfil']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
        }
    } catch (PDOException $e) {
        // Verificar si es error de username duplicado
        if ($e->getCode() == '23000') {
            echo json_encode(['success' => false, 'message' => 'El nombre de usuario ya está en uso']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error del sistema: ' . $e->getMessage()]);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>