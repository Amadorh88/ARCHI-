<?php
session_start();

// Mostrar mensaje de despedida
$_SESSION['success'] = "Sesión cerrada correctamente. ¡Hasta pronto!";

// Destruir todas las variables de sesión
$_SESSION = array();

// Destruir la sesión
session_destroy();

// Redirigir al login
header("Location: index.php");
exit();
?>