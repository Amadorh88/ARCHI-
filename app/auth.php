<?php
session_start();

require_once __DIR__ . '/config/db.php';

$database = new Database();
$db = $database->getConnection();

// Seguridad: solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.html");
    exit;
}

// Obtener datos
$usuario    = trim($_POST['usuario'] ?? '');
$clave      = $_POST['contraseña'] ?? '';

// Validar campos
if ($usuario === '' || $clave === '') {
    $_SESSION['error'] = "Todos los campos son obligatorios";
    header("Location: index.html");
    exit;
}

// Buscar usuario activo
$sql = "SELECT id, nombre, usuario, contraseña, rol 
        FROM usuarios 
        WHERE usuario = :usuario 
        AND estado = 1 
        LIMIT 1";

$stmt = $db->prepare($sql);
$stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Validar usuario
if (!$user || !password_verify($clave, $user['contraseña'])) {
    $_SESSION['error'] = "Usuario o contraseña incorrectos";
    header("Location: index.html");
    exit;
}

// ✅ Login correcto
$_SESSION['login']      = true;
$_SESSION['user_id']    = $user['id'];
$_SESSION['usuario']    = $user['usuario'];
$_SESSION['nombre']     = $user['nombre'];
$_SESSION['rol']        = $user['rol'];

// Redirección por rol (opcional)
header("Location: private/dashboard.php");
exit;
