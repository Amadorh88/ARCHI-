<?php
require_once 'config/db.php';

$busqueda = $_GET['busqueda'] ?? '';
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';
$modulo = $_GET['modulo'] ?? '';

$query = "
    SELECT 
        a.id_actividad,
        a.accion,
        a.modulo,
        a.fecha,
        a.ip,
        u.nombre AS usuario_nombre,
        u.rol AS usuario_rol
    FROM 
        actividades a
    LEFT JOIN 
        usuarios u ON a.id_usuario = u.id
    WHERE 1=1";

$params = [];

if (!empty($busqueda)) {
    $query .= " AND (a.accion LIKE ? OR u.nombre LIKE ? OR a.ip LIKE ?)";
    $searchTerm = safeSearch($busqueda);
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
}

if (!empty($modulo)) {
    $query .= " AND a.modulo = ?";
    $params[] = $modulo;
}

if (!empty($fecha_inicio)) {
    $query .= " AND DATE(a.fecha) >= ?";
    $params[] = $fecha_inicio;
}

if (!empty($fecha_fin)) {
    $query .= " AND DATE(a.fecha) <= ?";
    $params[] = $fecha_fin;
}

$query .= " ORDER BY a.fecha DESC LIMIT 100";

try {
    $stmt = $bd->prepare($query);
    $stmt->execute($params);
    $actividadesList = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $actividadesList = [];
    echo "<div style='color: red; padding: 10px; border: 1px solid red;'>Error: " . $e->getMessage() . "</div>";
}

// Obtener lista de módulos únicos para el filtro
$modulosList = $bd->query("SELECT DISTINCT modulo FROM actividades WHERE modulo IS NOT NULL ORDER BY modulo")->fetchAll(PDO::FETCH_ASSOC);
?>

<h2><i class="fas fa-history"></i> Registro de Actividades</h2>

<div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
    <h4 style="margin-top: 0; color: #2c3e50;">
        <i class="fas fa-filter"></i> Filtros de Actividades
    </h4>
    <form method="GET" action="" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: end;">
        <input type="hidden" name="section" value="actividades">
        
        <div>
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #2c3e50;">
                <i class="fas fa-search"></i> Término de Búsqueda
            </label>
            <input type="text" name="busqueda" value="<?php echo htmlspecialchars($busqueda); ?>" 
                   placeholder="Buscar por acción, usuario o IP..." 
                   style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px;">
        </div>
        
        <div>
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #2c3e50;">
                <i class="fas fa-folder"></i> Módulo
            </label>
            <select name="modulo" style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px;">
                <option value="">Todos los módulos</option>
                <?php foreach ($modulosList as $mod): ?>
                    <option value="<?= htmlspecialchars($mod['modulo']) ?>" <?= ($modulo == $mod['modulo']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($mod['modulo']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div>
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #2c3e50;">
                <i class="fas fa-calendar-alt"></i> Fecha Inicio
            </label>
            <input type="date" name="fecha_inicio" value="<?php echo htmlspecialchars($fecha_inicio); ?>" 
                   style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px;">
        </div>
        
        <div>
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #2c3e50;">
                <i class="fas fa-calendar-alt"></i> Fecha Fin
            </label>
            <input type="date" name="fecha_fin" value="<?php echo htmlspecialchars($fecha_fin); ?>" 
                   style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px;">
        </div>
        
        <div style="display: flex; gap: 1rem; align-items: center; grid-column: span 2;">
            <div>
                <button type="submit" class="btn tooltip" 
                        style="background: #3498db; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 5px; cursor: pointer; font-weight: 600; height: 48px;">
                    <i class="fas fa-search"></i> Aplicar Filtros
                    <span class="tooltiptext">Ejecutar búsqueda con filtros</span>
                </button>
            </div>
            
            <div>
                <button type="button" onclick="window.location.href='?section=actividades'" class="btn tooltip"
                        style="background: #95a5a6; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 5px; cursor: pointer; font-weight: 600; height: 48px;">
                    <i class="fas fa-broom"></i> Limpiar
                    <span class="tooltiptext">Limpiar todos los filtros</span>
                </button>
            </div>
        </div>
    </form>
    
    <?php if (!empty($busqueda) || !empty($modulo) || !empty($fecha_inicio) || !empty($fecha_fin)): ?>
    <div style="margin-top: 1rem; padding: 0.75rem; background: #e8f4fd; border-radius: 5px; border-left: 4px solid #3498db;">
        <strong>Filtros activos:</strong>
        <?php 
        $filtros = [];
        if (!empty($busqueda)) $filtros[] = "Búsqueda: \"$busqueda\"";
        if (!empty($modulo)) $filtros[] = "Módulo: \"$modulo\"";
        if (!empty($fecha_inicio)) $filtros[] = "Desde: " . formatDate($fecha_inicio);
        if (!empty($fecha_fin)) $filtros[] = "Hasta: " . formatDate($fecha_fin);
        echo implode(' • ', $filtros);
        ?>
        <span style="color: #7f8c8d; font-size: 0.9rem;">
            • Registros: <?php echo count($actividadesList); ?> actividades
        </span>
    </div>
    <?php endif; ?>
</div>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
    <div>
        <button class="btn tooltip" onclick="exportToExcel('actividades-table', 'Registro_de_Actividades')"
                style="background: #27ae60; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 5px; cursor: pointer; font-weight: 600; margin-right: 10px;">
            <i class="fas fa-file-excel"></i> Exportar a Excel
        </button>
        
        <button class="btn tooltip" onclick="printTable('actividades-table', 'Registro de Actividades del Sistema')"
                style="background: #34495e; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 5px; cursor: pointer; font-weight: 600;">
            <i class="fas fa-print"></i> Imprimir
        </button>
    </div>
    
    <div style="color: #7f8c8d; font-size: 0.9rem;">
        <i class="fas fa-info-circle"></i> Total: <?php echo count($actividadesList); ?> actividades registradas
        <?php if (!empty($actividadesList)): ?>
            • Última: <?php echo formatDate($actividadesList[0]['fecha']); ?>
        <?php endif; ?>
    </div>
</div>

<div style="margin-top: 1.5rem; overflow-x: auto;">
    <table id="actividades-table" style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">
        <thead>
            <tr>
                <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">Usuario</th>
                <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">Acción</th>
                <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">Módulo</th>
                <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">Fecha y Hora</th>
                <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">Dirección IP</th>
                <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">Rol</th>
                <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($actividadesList as $row): ?>
            <tr style="border-bottom: 1px solid #e0e0e0;">
                <td style="padding: 0.75rem 1rem;">
                    <?php if (!empty($row['usuario_nombre'])): ?>
                        <?= htmlspecialchars($row['usuario_nombre']) ?>
                    <?php else: ?>
                        <span style="color: #95a5a6; font-style: italic;">Sistema</span>
                    <?php endif; ?>
                </td>
                <td style="padding: 0.75rem 1rem;">
                    <span style="display: inline-block; max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" 
                          title="<?= htmlspecialchars($row['accion']) ?>">
                        <?= htmlspecialchars($row['accion']) ?>
                    </span>
                </td>
                <td style="padding: 0.75rem 1rem;">
                    <?php if (!empty($row['modulo'])): ?>
                        <span class="badge" style="background: #3498db; color: white; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.8rem;">
                            <?= htmlspecialchars($row['modulo']) ?>
                        </span>
                    <?php else: ?>
                        <span style="color: #95a5a6;">—</span>
                    <?php endif; ?>
                </td>
                <td style="padding: 0.75rem 1rem;"><?= formatDate($row['fecha']) ?></td>
                <td style="padding: 0.75rem 1rem;">
                    <code style="background: #f8f9fa; padding: 0.25rem 0.5rem; border-radius: 3px; font-family: monospace;">
                        <?= htmlspecialchars($row['ip']) ?>
                    </code>
                </td>
                <td style="padding: 0.75rem 1rem;">
                    <?php if (!empty($row['usuario_rol'])): ?>
                        <span class="badge" style="background: #9b59b6; color: white; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.8rem;">
                            <?= htmlspecialchars($row['usuario_rol']) ?>
                        </span>
                    <?php else: ?>
                        <span style="color: #95a5a6;">—</span>
                    <?php endif; ?>
                </td>
                <td style="padding: 0.75rem 1rem;">
                    <div style="display: flex; gap: 0.5rem;">
                        <button class="btn-action btn-view tooltip" onclick="verActividad(<?= $row['id_actividad'] ?>, 'actividades')">
                            <i class="fas fa-eye"></i>
                            <span class="tooltiptext">Ver detalles</span>
                        </button>
                        <?php if ($_SESSION['rol'] === 'admin'): ?>
                           <button class="btn-action btn-delete tooltip" onclick="confirmarEliminar('actividad', <?= $actividad['id'] ?>, '<?= addslashes($actividad['id_actividad']) ?>')">
                                <i class="fas fa-trash"></i>
                                <span class="tooltiptext">Eliminar</span>
                            </button>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            
            <?php if(empty($actividadesList)): ?>
            <tr>
                <td colspan="7" style="padding: 2rem; text-align: center; color: #7f8c8d;">
                    <i class="fas fa-history" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                    <h3>No se encontraron actividades</h3>
                    <p><?php echo (!empty($busqueda) || !empty($modulo) || !empty($fecha_inicio) || !empty($fecha_fin)) ? "No hay actividades que coincidan con los filtros aplicados" : "No hay actividades registradas aún"; ?></p>
                    <?php if (!empty($busqueda) || !empty($modulo) || !empty($fecha_inicio) || !empty($fecha_fin)): ?>
                    <button onclick="window.location.href='?section=actividades'" class="btn" style="background: #3498db; color: white; border: none; padding: 0.5rem 1rem; border-radius: 5px; cursor: pointer; margin-top: 0.5rem;">
                        <i class="fas fa-times"></i> Limpiar filtros
                    </button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal para ver detalles -->
<div id="viewModal_actividades" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('viewModal_actividades')">&times;</span>
        <h3><i class="fas fa-info-circle"></i> Detalles de la Actividad</h3>
        <div id="actividadesDetails">
            <!-- Los detalles se cargarán aquí dinámicamente -->
        </div>
        <div class="form-actions" style="margin-top: 1rem;">
            <button onclick="closeModal('viewModal_actividades')" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cerrar
            </button>
        </div>
    </div>
</div>

<script>
// Función para exportar a Excel
function exportToExcel(tableId, filename) {
    var table = document.getElementById(tableId);
    var html = table.outerHTML;
    
    // Crear un blob con el contenido HTML
    var blob = new Blob([html], {type: 'application/vnd.ms-excel'});
    
    // Crear un enlace de descarga
    var downloadLink = document.createElement('a');
    downloadLink.href = URL.createObjectURL(blob);
    downloadLink.download = filename + '.xls';
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}

// Función para formatear fecha y hora (si no existe en tus funciones)
function formatDateTime(dateTimeString) {
    if (!dateTimeString) return '';
    var date = new Date(dateTimeString);
    return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
}
</script>