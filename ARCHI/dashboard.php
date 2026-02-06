
<?php
session_start();

// Verificar si el usuario está logueado Y NO ES ADMIN
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] === 'admin') {
    // Si es admin, redirigir al panel de admin
    if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin') {
        header("Location: dashboard-admin.php");
        exit();
    }
    // Si no está logueado, redirigir al inicio
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: index.php");
        exit();
    }
}


// Incluir la conexión a la base de datos
include_once 'config/db.php';
$database = new Database();
$bd = $database->getConnection();

// Definición de permisos por rol
$permisosrol = [
    'admin' => [
        "inicio", "feligreses", "catequesis", "prebautismal", "comunion-catequesis", 
        "confirmacion-catequesis", "prematrimonial", "sacramentos", "bautismo", 
        "comunion", "confirmacion", "matrimonio", "administracion", "catequistas", 
        "ministros", "parroquias", "pagos", "configuracion", "usuarios", "actividades"
    ],
    'secretaria' => [
        "inicio", "feligreses", "catequesis", "prebautismal", "comunion-catequesis", 
        "confirmacion-catequesis", "prematrimonial", "sacramentos", "bautismo", 
        "comunion", "confirmacion", "matrimonio", "pagos"
    ],
    'archivista' => [
        "inicio", "feligreses", "sacramentos", "bautismo", "comunion", 
        "confirmacion", "matrimonio"
    ],
    'parroco' => [
        "inicio", "feligreses", "catequesis", "prebautismal", "comunion-catequesis", 
        "confirmacion-catequesis", "prematrimonial", "sacramentos", "bautismo", 
        "comunion", "confirmacion", "matrimonio", "administracion", "catequistas", 
        "ministros"
    ]
];

// Función para obtener el nombre de visualización del rol
function getRoleDisplayName($role) {
    $roleNames = [
        'admin' => 'Administrador',
        'secretaria' => 'Secretaria',
        'archivista' => 'Archivista',
        'parroco' => 'Párroco'
    ];
    return $roleNames[$role] ?? $role;
}

$Rolusuario = $_SESSION['rol'];
$Nombreusuario = $_SESSION['nombre_completo'];
$permisos = $permisosrol[$Rolusuario] ?? [];

// Obtener estadísticas
function countTableData($db, $tableName) {
    // Uso de prepare/execute para robustez
    $query = "SELECT COUNT(*) as total FROM $tableName";
    $stmt = $db->prepare($query);
    if ($stmt->execute()) {
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }
    return 0; // Devolver 0 en caso de error
}

// CORRECCIÓN: Se usa 'feligreses' y se utiliza countTableData para todas las estadísticas.
$estadistica = [
    'feligreses' => countTableData($bd, 'feligres'),
    'catequesis' => countTableData($bd, 'catequesis'),
    'sacramentos' => countTableData($bd, 'sacramento'),
    'pagos' => countTableData($bd, 'pago')
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página principal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/modals-certificates.css">
    <script src="js/modals-system.js"></script>
</head>
<body>
    <div class="alert-container" id="alertContainer"></div>

    <div class="container">
        <?php include 'sidebar.php'; ?>
        
        <div class="main-wrapper">
            <div class="main-content">
                <div class="header">
                    <h1 id="pageTitle">
                        <span class="tooltip">
                            Inicio
                            <span class="tooltiptext">Página principal </span>
                        </span>
                    </h1>
                    <div class="user-info">
                        <button class="profile-btn" onclick="abrirModalPerfil()">
                            <i class="fas fa-user-circle"></i> Mi Perfil
                        </button>
                        <span class="role-badge <?php echo htmlspecialchars($Rolusuario); ?>-badge tooltip">
                            <i class="fas fa-shield-alt"></i> <?php echo htmlspecialchars(getRoleDisplayName($Rolusuario)); ?>
                            <span class="tooltiptext">Rol: <?php echo htmlspecialchars(getRoleDisplayName($Rolusuario)); ?></span>
                        </span>
                        <button class="logout-btn" onclick="showLogoutModal()">
                            <span class="tooltip">
                                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                                <span class="tooltiptext">Cerrar sesión actual</span>
                            </span>
                        </button>
                    </div>
                </div>
                
                <div class="content" id="mainContent">
                    </div>
            </div>
   
            <?php include 'footer.php'; ?>
        </div>
    </div>

<?php

// Permisos de acción por rol (qué puede hacer cada rol)
$permisosAccion = [
    'admin' => ['crear', 'editar', 'eliminar', 'ver', 'imprimir', 'exportar'],
    'secretaria' => ['crear', 'editar', 'ver', 'imprimir'],
    'archivista' => ['ver', 'imprimir'],
    'parroco' => ['crear', 'editar', 'ver', 'imprimir', 'aprobar']
];

// Función para verificar si un usuario tiene permiso para una acción
function tienePermiso($accion) {
    global $permisosAccion, $Rolusuario;
    return in_array($accion, $permisosAccion[$Rolusuario] ?? []);
}

// Pasar permisos a JavaScript
echo '<script>';
echo 'const permisosUsuario = ' . json_encode($permisosAccion[$Rolusuario] ?? []) . ';';
echo 'function puede(accion) { return permisosUsuario.includes(accion); }';
echo '</script>';
?>
    <?php include 'modals.php'; ?>

    <script src="js/dashboard.js"></script>
</body>
</html>