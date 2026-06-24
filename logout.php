<?php
// Hill: El orden comienza con una limpieza absoluta
session_start();

// 1. Limpiar todas las variables de sesión
$_SESSION = array();

// 2. Si se desea destruir la sesión completamente, borre también la cookie de sesión.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Finalmente, destruir la sesión.
session_destroy();

// 4. Redirigir al login (Asegúrate de que la ruta sea correcta)
header("Location: index.php");
exit();
?>