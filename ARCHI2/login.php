[file name]: login.php (modificado)
[file content begin]
<?php
session_start();

// 1. Verificar si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 2. Incluir la conexión a la base de datos
    include_once 'config/db.php';
    $database = new Database();
    $db = $database->getConnection();

    // 3. Limpiar y obtener datos del formulario
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contraseña'];

    // 4. Preparar la consulta SQL para obtener el usuario activo
    // 'estado = 1' asegura que solo los usuarios activos puedan iniciar sesión.
    $query = "SELECT id, nombre, contraseña, rol FROM usuarios WHERE usuario = :usuario AND estado = 1 LIMIT 0,1";
    $stmt = $db->prepare($query);
    
    // 5. Vincular parámetros y ejecutar
    $stmt->bindParam(':usuario', $usuario);
    $stmt->execute();
    
    $num = $stmt->rowCount();

    if ($num > 0) {
        // 6. El usuario existe, obtener los datos
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $id_usuario = $row['id'];
        $nombre_completo = $row['nombre'];
        $contrasena_hash = $row['contraseña'];
        $rol = $row['rol'];

        // 7. Verificar la contraseña hasheada
        if (password_verify($contrasena, $contrasena_hash)) {
            // Contraseña correcta. Crear variables de sesión.
            $_SESSION['usuario_id'] = $id_usuario;
            $_SESSION['nombre_completo'] = $nombre_completo;
            
            // Mapear el rol de la BD al rol usado en dashboard.php
            // El rol en la DB es 'admin', 'secretario', 'archivista', 'parroco'
            // El rol esperado por dashboard.php es 'admin', 'secretaria', 'archivista', 'parroco'
            $rol_mapeado = $rol;
            if ($rol === 'secretario') {
                $rol_mapeado = 'secretaria';
            }
            
            $_SESSION['rol'] = $rol_mapeado;

            // Registrar actividad de inicio de sesión (Opcional, usando la tabla `actividades`)
            $actividad_query = "INSERT INTO actividades (id_usuario, accion, modulo, ip) VALUES (:id_usuario, 'Inició sesión en el sistema', 'Login', :ip)";
            $actividad_stmt = $db->prepare($actividad_query);
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
            $actividad_stmt->bindParam(':id_usuario', $id_usuario);
            $actividad_stmt->bindParam(':ip', $ip_address);
            $actividad_stmt->execute();

            // 8. MODIFICACIÓN: Redirigir según el rol
            if ($rol_mapeado === 'admin') {
                header("Location: dashboard-admin.php"); // Nueva página para admin
            } else {
                header("Location: dashboard.php"); // Página normal para otros roles
            }
            exit();
        } else {
            // Contraseña incorrecta
            $_SESSION['login_error'] = "Contraseña incorrecta.";
            header("Location: index.php");
            exit();
        }
    } else {
        // Usuario no encontrado o inactivo
        $_SESSION['login_error'] = "Usuario no encontrado o inactivo.";
        header("Location: index.php");
        exit();
    }
} else {
    // Si se accede a login.php directamente sin POST, redirigir al formulario
    header("Location: index.php");
    exit();
}
?>
[file content end]