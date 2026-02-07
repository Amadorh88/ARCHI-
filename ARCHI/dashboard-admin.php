<?php
session_start();

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Incluir la conexión a la base de datos
include_once 'config/db.php';
$database = new Database();
$bd = $database->getConnection();

// Definición de permisos por rol (solo admin aquí, pero completo para consistencia)
$permisosrol = [
    'admin' => [
        "inicio", "feligreses", "catequesis", "prebautismal", "comunion-catequesis", 
        "confirmacion-catequesis", "prematrimonial", "sacramentos", "bautismo", 
        "comunion", "confirmacion", "matrimonio", "administracion", "catequistas", 
        "ministros", "parroquias", "pagos", "configuracion", "usuarios", "actividades"
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

// Obtener estadísticas avanzadas para admin
function countTableData($db, $tableName) {
    $query = "SELECT COUNT(*) as total FROM $tableName";
    $stmt = $db->prepare($query);
    if ($stmt->execute()) {
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }
    return 0;
}

// Obtener estadísticas de usuarios por rol (específico para admin)
function getUsersByRole($db) {
    $query = "SELECT rol, COUNT(*) as total FROM usuarios WHERE estado = 1 GROUP BY rol";
    $stmt = $db->prepare($query);
    $usersByRole = [];
    if ($stmt->execute()) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usersByRole[$row['rol']] = $row['total'];
        }
    }
    return $usersByRole;
}

// Obtener actividades recientes
function getRecentActivities($db, $limit = 10) {
    $query = "SELECT a.*, u.nombre as usuario_nombre 
              FROM actividades a 
              JOIN usuarios u ON a.id_usuario = u.id 
              ORDER BY a.fecha DESC 
              LIMIT :limit";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $activities = [];
    if ($stmt->execute()) {
        $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return $activities;
}

// Estadísticas para administrador
$estadistica = [
    'feligreses' => countTableData($bd, 'feligres'),
    'catequesis' => countTableData($bd, 'catequesis'),
    'sacramentos' => countTableData($bd, 'sacramento'),
    'pagos' => countTableData($bd, 'pago'),
    'usuarios' => countTableData($bd, 'usuarios'),
    'catequistas' => countTableData($bd, 'catequista'),
    'parroquias' => countTableData($bd, 'parroquia')
];

$usersByRole = getUsersByRole($bd);
$recentActivities = getRecentActivities($bd, 5);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/modals-certificates.css">
    <link rel="stylesheet" href="css/jquery.dataTables.min.css">
 

    <script src="js/modals-system.js"></script>
    <style>
        /* Estilos adicionales para el panel de admin */
        .admin-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .admin-stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .admin-stat-card:hover {
            transform: translateY(-5px);
        }
        
        .admin-stat-card h3 {
            font-size: 2.5rem;
            margin: 10px 0;
            font-weight: 700;
        }
        
        .admin-stat-card p {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .admin-stat-card i {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .admin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }
        
        .admin-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .admin-card h3 {
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #3498db;
        }
        
        .user-role-distribution {
            margin-top: 20px;
        }
        
        .role-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            margin-bottom: 8px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #3498db;
        }
        
        .role-item.admin {
            border-left-color: #e74c3c;
        }
        
        .role-item.secretaria {
            border-left-color: #2ecc71;
        }
        
        .role-item.parroco {
            border-left-color: #f39c12;
        }
        
        .role-item.archivista {
            border-left-color: #9b59b6;
        }
        
        .activity-list {
            list-style: none;
            padding: 0;
        }
        
        .activity-item {
            padding: 15px;
            margin-bottom: 10px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #3498db;
        }
        
        .activity-item .time {
            font-size: 0.8rem;
            color: #7f8c8d;
            margin-top: 5px;
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .quick-action-btn {
            background: #3498db;
            color: white;
            border: none;
            padding: 15px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .quick-action-btn:hover {
            background: #2980b9;
            transform: translateY(-3px);
        }
        
        .quick-action-btn i {
            font-size: 1.5rem;
        }
        
        .welcome-admin {
            background: linear-gradient(135deg, #2c3e50 0%, #4a6491 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .welcome-admin h2 {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .welcome-admin p {
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }

        /* ============== MODAL ========== */
        /* Overlay */
.modal-overlay {
    position: fixed;
    inset: 0; /* top:0 right:0 bottom:0 left:0 */
    background: rgba(0, 0, 0, 0.6); 
    display: none;               /* oculto por defecto */
    justify-content: center;
    align-items: center;
    z-index: 9999;               /* MUY IMPORTANTE */
}

.modal-header h3 {
    color: white !important; /* !important asegura que no sea sobrescrito */
}
/* Tarjeta del modal */
.modal-card {
    background: #fff;
    width: 100%;
    max-width: 500px;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 15px 40px rgba(0,0,0,.3);
    animation: modalFade .25s ease;
}

/* Header */
.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: #2c3e50;
    color: white;
}

.modal-header button {
    background: none;
    border: none;
    color: white;
    font-size: 1.5rem;
    cursor: pointer;
}

/* Body */
.modal-body {
    padding: 1.2rem;
}

.modal-body p {
    margin: .4rem 0;
}

.modal-body span {
    font-weight: bold;
}

/* Footer */
.modal-footer {
    padding: 1rem;
    text-align: right;
    background: #f4f4f4;
}

.btn-cerrar {
    background: #c0392b;
    color: white;
    border: none;
    padding: .5rem 1.2rem;
    border-radius: 5px;
    cursor: pointer;
}
.modal-body h4 {
    margin-top: 1rem;
    margin-bottom: .5rem;
    font-size: 1.1rem;
    font-weight: 700;
}

.modal-body p {
    margin: .25rem 0;
}

.modal-body span {
    font-weight: 600;
    color: #2c3e50;
}

/* Animación */
@keyframes modalFade {
    from {
        transform: translateY(-15px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

    </style>
    <!--  -->
      
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
                            Panel de Administración
                            <span class="tooltiptext">Panel exclusivo para administradores</span>
                        </span>
                    </h1>
                    <div class="user-info">
                        <button class="profile-btn" onclick="abrirModalPerfil()">
                            <i class="fas fa-user-circle"></i> Mi Perfil
                        </button>
                        <span class="role-badge <?php echo htmlspecialchars($Rolusuario); ?>-badge tooltip">
                            <i class="fas fa-crown"></i> <?php echo htmlspecialchars(getRoleDisplayName($Rolusuario)); ?>
                            <span class="tooltiptext">Rol: <?php echo htmlspecialchars(getRoleDisplayName($Rolusuario)); ?> - Privilegios completos</span>
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
                    <!-- Bienvenida especial para admin -->
                    <div class="welcome-admin">
                        <h2>Bienvenido, <?php echo htmlspecialchars($Nombreusuario); ?>!</h2>
                        <p>Como Administrador del Sistema, tienes acceso completo a todas las funcionalidades y configuraciones.</p>
                    </div>
                    
                    <!-- Estadísticas principales -->
                    <div class="admin-stats">
                        <div class="admin-stat-card">
                            <i class="fas fa-users"></i>
                            <h3><?php echo $estadistica['usuarios']; ?></h3>
                            <p>Usuarios Activos</p>
                        </div>
                        <div class="admin-stat-card">
                            <i class="fas fa-user-friends"></i>
                            <h3><?php echo $estadistica['feligreses']; ?></h3>
                            <p>Feligreses Registrados</p>
                        </div>
                        <div class="admin-stat-card">
                            <i class="fas fa-church"></i>
                            <h3><?php echo $estadistica['parroquias']; ?></h3>
                            <p>Parroquias</p>
                        </div>
                        <div class="admin-stat-card">
                            <i class="fas fa-book"></i>
                            <h3><?php echo $estadistica['catequesis']; ?></h3>
                            <p>Sesiones de Catequesis</p>
                        </div>
                    </div>
                    
                    <!-- Grid de contenido admin -->
                    <div class="admin-grid">
                        <!-- Distribución de usuarios por rol -->
                        <div class="admin-card">
                            <h3><i class="fas fa-chart-pie"></i> Distribución de Usuarios</h3>
                            <div class="user-role-distribution">
                                <?php foreach ($usersByRole as $role => $count): 
                                    $roleDisplay = getRoleDisplayName($role === 'secretario' ? 'secretaria' : $role);
                                ?>
                                <div class="role-item <?php echo $role === 'secretario' ? 'secretaria' : $role; ?>">
                                    <span><?php echo $roleDisplay; ?></span>
                                    <strong><?php echo $count; ?></strong>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Actividades recientes -->
                        <div class="admin-card">
                            <h3><i class="fas fa-history"></i> Actividad Reciente</h3>
                            <ul class="activity-list">
                                <?php if (empty($recentActivities)): ?>
                                    <li class="activity-item">No hay actividades recientes</li>
                                <?php else: ?>
                                    <?php foreach ($recentActivities as $activity): ?>
                                    <li class="activity-item">
                                        <strong><?php echo htmlspecialchars($activity['usuario_nombre']); ?></strong>
                                        <div><?php echo htmlspecialchars($activity['accion']); ?></div>
                                        <div class="time">
                                            <?php echo date('d/m/Y H:i', strtotime($activity['fecha_registro'])); ?>
                                            <?php if ($activity['ip']): ?>
                                                <span style="margin-left: 10px;">IP: <?php echo htmlspecialchars($activity['ip']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                        
                        <!-- Acciones rápidas -->
                        <div class="admin-card">
                            <h3><i class="fas fa-bolt"></i> Acciones Rápidas</h3>
                            <div class="quick-actions">
                                <button class="quick-action-btn" onclick="window.location.href='usuarios.php'">
                                    <i class="fas fa-user-plus"></i>
                                    <span>Nuevo Usuario</span>
                                </button>
                                <button class="quick-action-btn" onclick="window.location.href='backup.php'">
                                    <i class="fas fa-database"></i>
                                    <span>Backup DB</span>
                                </button>
                                <button class="quick-action-btn" onclick="window.location.href='configuracion.php'">
                                    <i class="fas fa-cog"></i>
                                    <span>Configurar</span>
                                </button>
                                <button class="quick-action-btn" onclick="window.location.href='reportes.php'">
                                    <i class="fas fa-chart-bar"></i>
                                    <span>Reportes</span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Estado del sistema -->
                        <div class="admin-card">
                            <h3><i class="fas fa-server"></i> Estado del Sistema</h3>
                            <div class="system-status">
                                <div class="status-item">
                                    <span>Base de Datos:</span>
                                    <span class="status-badge success">Conectada</span>
                                </div>
                                <div class="status-item">
                                    <span>Espacio Disco:</span>
                                    <span class="status-badge warning"><?php echo round(disk_free_space("/") / 1024 / 1024 / 1024, 2); ?> GB libre</span>
                                </div>
                                <div class="status-item">
                                    <span>PHP Version:</span>
                                    <span class="status-badge info"><?php echo phpversion(); ?></span>
                                </div>
                                <div class="status-item">
                                    <span>Último Backup:</span>
                                    <span class="status-badge"><?php echo date('d/m/Y'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
   
            <?php include 'footer.php'; ?>
        </div>
    </div>

    <!-- ===== MODALES DEL SISTEMA  -->
    <?php include 'modals.php'; ?>

    <script src="js/dashboard.js"></script>
    <script src="js/feligres.js"></script>
    <script src="js/parroquia.js"></script>
    <script src="js/ministro.js"></script>
    <script src="js/catequista.js"></script>
    <script src="js/pago.js"></script>
    <script src="js/prebautismal.js"></script>
    <script src="js/bautismo.js"></script>
    <script src="js/comunion.js"></script>
    <script src="js/confirmacion.js"></script>
    <script src="js/matrimonio.js"></script>
    <script src="js/usuarios.js"></script>
<!--     <script src="js/usuarios.js"></script> -->
    <script>
        // Script adicional para el panel de admin
        document.addEventListener('DOMContentLoaded', function() {
            // Actualizar estadísticas cada 60 segundos
            setInterval(function() {
                fetch('api/get-stats.php?role=admin')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Actualizar contadores
                            document.querySelectorAll('.admin-stat-card h3')[0].textContent = data.stats.usuarios;
                            document.querySelectorAll('.admin-stat-card h3')[1].textContent = data.stats.feligreses;
                            // Agregar más actualizaciones según sea necesario
                        }
                    });
            }, 60000);
        });
    </script>
</body>
</html>