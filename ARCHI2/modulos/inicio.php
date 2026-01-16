<?php
// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

// Incluir la conexión a la base de datos
include_once 'config/db.php';
$database = new Database();
$db = $database->getConnection();

// Obtener estadísticas
function countTableData($db, $tableName) {
    $query = "SELECT COUNT(*) as total FROM $tableName ";
    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

$estadistica = [
    'feligreses' => countTableData($db, 'feligres'),
    'catequesis' => countTableData($db, 'catequesis'),
    'sacramentos' => $db->query("SELECT COUNT(*) FROM sacramento ")->fetchColumn(),
    'pagos' => $db->query("SELECT COUNT(*) FROM pago ")->fetchColumn()
];

// Obtener información del usuario actual
$Rolusuario = $_SESSION['rol'];
$Nombreusuario = $_SESSION['nombre_completo'];
$permisos = $permisosrol[$Rolusuario] ?? [];
?>

<!-- Contenido de Inicio con Estadísticas -->
<div id="inicio-content" class="page-content active">
    <div class="welcome-section">
        <p> <?php echo date('d/m/Y'); ?></p>
        <h2>Bienvenido, <?php echo $Nombreusuario; ?></h2>
    </div>
    
   <div class="stats-grid">
            <div class="stat-card">
                <span class="tooltip">
                    <h3><?php echo $estadistica['feligreses']; ?></h3>
                    <span class="tooltiptext">Total de feligreses activos</span>
                </span>
                <span class="tooltip">
                    <p><i class="fas fa-users"></i> Feligreses Registrados</p>
                    <span class="tooltiptext">Personas registradas en la parroquia</span>
                </span>
            </div>
            <div class="stat-card">
                <span class="tooltip">
                    <h3><?php echo $estadistica['catequesis']; ?></h3>
                    <span class="tooltiptext">Total de cursos activos</span>
                </span>
                <span class="tooltip">
                    <p><i class="fas fa-book"></i> Cursos de Catequesis</p>
                    <span class="tooltiptext">Cursos de formación en progreso</span>
                </span>
            </div>
            <div class="stat-card">
                <span class="tooltip">
                    <h3><?php echo $estadistica['sacramentos']; ?></h3>
                    <span class="tooltiptext">Sacramentos administrados</span>
                </span>
                <span class="tooltip">
                    <p><i class="fas fa-church"></i> Sacramentos Realizados</p>
                    <span class="tooltiptext">Celebraciones sacramentales completadas</span>
                </span>
            </div>
            <div class="stat-card">
                <span class="tooltip">
                    <h3><?php echo $estadistica['pagos']; ?></h3>
                    <span class="tooltiptext">Transacciones procesadas</span>
                </span>
                <span class="tooltip">
                    <p><i class="fas fa-dollar-sign"></i> Donaciones Registradas</p>
                    <span class="tooltiptext">Pagos y donaciones registrados</span>
                </span>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; margin-top: 1rem;">
            <div style="background: #e8f4fd; padding: 1rem; border-radius: 5px;">
                <span class="tooltip">
                    <h4><i class="fas fa-users"></i> Feligreses</h4>
                    <span class="tooltiptext">Gestión de comunidad parroquial</span>
                </span>
                <p>Gestión de información de feligreses</p>
            </div>
            <div style="background: #e8f8f0; padding: 1rem; border-radius: 5px;">
                <span class="tooltip">
                    <h4><i class="fas fa-book"></i> Catequesis</h4>
                    <span class="tooltiptext">Programas de formación</span>
                </span>
                <p>Control de procesos de catequesis</p>
            </div>
            <div style="background: #fef9e7; padding: 1rem; border-radius: 5px;">
                <span class="tooltip">
                    <h4><i class="fas fa-church"></i> Sacramentos</h4>
                    <span class="tooltiptext">Administración de sacramentos</span>
                </span>
                <p>Registro de sacramentos administrados</p>
            </div>
            <div style="background: #fbeeee; padding: 1rem; border-radius: 5px;">
                <span class="tooltip">
                    <h4><i class="fas fa-cogs"></i> Administración</h4>
                    <span class="tooltiptext">Configuración del sistema</span>
                </span>
                <p>Gestión de recursos parroquiales</p>
            </div>
    </div>
