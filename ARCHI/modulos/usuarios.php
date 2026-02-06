<?php
// Procesar búsqueda y filtros
$busqueda = $_GET['busqueda'] ?? '';
$filtro_rol = $_GET['rol'] ?? '';
$filtro_estado = $_GET['estado'] ?? '';

// Construcción de Query
$query = "SELECT id, nombre, dni, usuario, rol, estado FROM usuarios WHERE 1=1";
$params = [];

if (!empty($busqueda)) {
    $query .= " AND (nombre LIKE ? OR dni LIKE ? OR usuario LIKE ?)";
    $searchTerm = "%$busqueda%";
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
}
if (!empty($filtro_rol)) { $query .= " AND rol = ?"; $params[] = $filtro_rol; }
if ($filtro_estado !== '') { $query .= " AND estado = ?"; $params[] = $filtro_estado; }

$query .= " ORDER BY id DESC";
$stmt = $bd->prepare($query);
$stmt->execute($params);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="css/usuarios.css">

<div class="container-usuarios">
    <h2>Gestión de Usuarios</h2>
    
    <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
        <h4 style="margin-top: 0; color: #2c3e50;">
            <i class="fas fa-search"></i> Buscar Usuarios
        </h4>
        <form id="formBusquedaUsuarios" method="GET" action="" style="display: grid; grid-template-columns: 1fr auto auto auto; gap: 1rem; align-items: end;">
            <input type="hidden" name="section" value="usuarios">
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #2c3e50;">
                    <i class="fas fa-filter"></i> Término de Búsqueda
                </label>
                <input type="text" name="busqueda" value="<?php echo htmlspecialchars($busqueda); ?>" 
                       placeholder="Buscar por nombre, DNI, usuario..." 
                       style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px;">
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #2c3e50;">
                    <i class="fas fa-user-tag"></i> Rol
                </label>
                <select name="rol" style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px; height: 48px;">
                    <option value="">Todos los roles</option>
                    <option value="admin" <?= $filtro_rol == 'admin' ? 'selected' : '' ?>>Administrador</option>
                    <option value="secretaria" <?= $filtro_rol == 'secretaria' ? 'selected' : '' ?>>Secretaría</option>
                </select>
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #2c3e50;">
                    <i class="fas fa-power-off"></i> Estado
                </label>
                <select name="estado" style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px; height: 48px;">
                    <option value="">Todos los estados</option>
                    <option value="1" <?= $filtro_estado === '1' ? 'selected' : '' ?>>Activos</option>
                    <option value="0" <?= $filtro_estado === '0' ? 'selected' : '' ?>>Inactivos</option>
                </select>
            </div>
            
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn tooltip" 
                        style="background: #3498db; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 5px; cursor: pointer; font-weight: 600; height: 48px;">
                    <i class="fas fa-search"></i> Buscar
                    <span class="tooltiptext">Ejecutar búsqueda</span>
                </button>
                
                <button type="button" onclick="limpiarBusquedaUsuarios()" class="btn tooltip"
                        style="background: #95a5a6; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 5px; cursor: pointer; font-weight: 600; height: 48px;">
                    <i class="fas fa-broom"></i> Limpiar
                    <span class="tooltiptext">Limpiar filtros</span>
                </button>
            </div>
        </form>
        
        <?php if (!empty($busqueda) || !empty($filtro_rol) || $filtro_estado !== ''): ?>
        <div style="margin-top: 1rem; padding: 0.75rem; background: #e8f4fd; border-radius: 5px; border-left: 4px solid #3498db;">
            <strong>Filtros activos:</strong>
            <?php 
            $filtros = [];
            if (!empty($busqueda)) $filtros[] = "Búsqueda: \"$busqueda\"";
            if (!empty($filtro_rol)) $filtros[] = "Rol: " . ($filtro_rol == 'admin' ? 'Administrador' : 'Secretaría');
            if ($filtro_estado !== '') $filtros[] = "Estado: " . ($filtro_estado == '1' ? 'Activo' : 'Inactivo');
            echo implode(' • ', $filtros);
            ?>
            <span style="color: #7f8c8d; font-size: 0.9rem;">
                • Resultados: <?php echo count($usuarios); ?> registros
            </span>
        </div>
        <?php endif; ?>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <div>
            <button class="btn tooltip" onclick="agregarUsuario()" 
                    style="width: auto; background: #27ae60; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 5px; cursor: pointer; font-weight: 600; margin-right: 10px;">
                <i class="fas fa-user-plus"></i> Nuevo Usuario
                <span class="tooltiptext">Crear nuevo usuario</span>
            </button>
        </div>
        
        <div style="color: #7f8c8d; font-size: 0.9rem;">
            <i class="fas fa-info-circle"></i> 
            <?php 
            $activos = array_filter($usuarios, fn($u) => $u['estado'] == 1);
            $inactivos = array_filter($usuarios, fn($u) => $u['estado'] == 0);
            ?>
            Total: <?php echo count($usuarios); ?> usuarios • 
            Activos: <?php echo count($activos); ?> • 
            Inactivos: <?php echo count($inactivos); ?>
        </div>
    </div>

    <div style="margin-top: 1.5rem; overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">
            <thead>
                <tr>
                    <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">ID</th>
                    <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">Nombre</th>
                    <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">Usuario</th>
                    <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">DNI</th>
                    <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">Rol</th>
                    <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">Estado</th>
                    <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($usuarios as $usuario): ?>
                <tr style="border-bottom: 1px solid #e0e0e0;">
                    <td style="padding: 0.75rem 1rem;"><?php echo htmlspecialchars($usuario['id']); ?></td>
                    <td style="padding: 0.75rem 1rem;"><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                    <td style="padding: 0.75rem 1rem;"><?php echo htmlspecialchars($usuario['usuario']); ?></td>
                    <td style="padding: 0.75rem 1rem;"><?php echo htmlspecialchars($usuario['dni']); ?></td>
                    <td style="padding: 0.75rem 1rem;">
                        <span style="padding: 0.25rem 0.5rem; border-radius: 3px; font-size: 0.85rem; font-weight: 600; 
                              background-color: <?= $usuario['rol'] == 'admin' ? '#e74c3c' : '#3498db' ?>; color: white;">
                            <i class="fas fa-<?= $usuario['rol'] == 'admin' ? 'user-shield' : 'user-tie' ?>"></i>
                            <?= ucfirst($usuario['rol']) ?>
                        </span>
                    </td>
                    <td style="padding: 0.75rem 1rem;">
                        <span style="padding: 0.25rem 0.5rem; border-radius: 3px; font-size: 0.85rem; font-weight: 600; 
                              background-color: <?= $usuario['estado'] == 1 ? '#27ae60' : '#e74c3c' ?>; color: white;">
                            <i class="fas fa-<?= $usuario['estado'] == 1 ? 'check-circle' : 'times-circle' ?>"></i>
                            <?= $usuario['estado'] == 1 ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </td>
                    <td style="padding: 0.75rem 1rem;">
                        <div style="display: flex; gap: 0.5rem;">
                            <button class="btn-action btn-view tooltip" onclick="abrirModalCRUD('usuario', 'ver', <?= $usuario['id'] ?>)">
                                <i class="fas fa-eye"></i>
                                <span class="tooltiptext">Ver detalles</span>
                            </button>
                            <button class="btn-action btn-edit tooltip" onclick="abrirModalCRUD('usuario', 'editar', <?= $usuario['id'] ?>)">
                                <i class="fas fa-edit"></i>
                                <span class="tooltiptext">Editar</span>
                            </button>
                            <button class="btn-action btn-delete tooltip" onclick="confirmarEliminar('usuario', <?= $usuario['id'] ?>, '<?= addslashes($usuario['nombre']) ?>')">
                                <i class="fas fa-trash"></i>
                                <span class="tooltiptext">Eliminar</span>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if(empty($usuarios)): ?>
                <tr>
                    <td colspan="7" style="padding: 2rem; text-align: center; color: #7f8c8d;">
                        <i class="fas fa-search" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                        <h3>No se encontraron resultados</h3>
                        <p><?php echo (!empty($busqueda) || !empty($filtro_rol) || $filtro_estado !== '') 
                            ? "No hay usuarios que coincidan con los filtros aplicados" 
                            : "No hay usuarios registrados en el sistema"; ?></p>
                        
                        <?php if (!empty($busqueda) || !empty($filtro_rol) || $filtro_estado !== ''): ?>
                        <button onclick="limpiarBusquedaUsuarios()" class="btn" style="background: #3498db; color: white; border: none; padding: 0.5rem 1rem; border-radius: 5px; cursor: pointer; margin-top: 0.5rem;">
                            <i class="fas fa-times"></i> Limpiar filtros
                        </button>
                        <?php endif; ?>
                        <button onclick="agregarUsuario()" class="btn" style="background: #27ae60; color: white; border: none; padding: 0.5rem 1rem; border-radius: 5px; cursor: pointer; margin-top: 0.5rem; margin-left: 0.5rem;">
                            <i class="fas fa-user-plus"></i> Crear usuario
                        </button>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'modals.php'; ?>
<script src="js/usuarios.js"></script>
<script>
// Función para limpiar búsqueda de usuarios
function limpiarBusquedaUsuarios() {
    window.location.href = 'dashboard.php?section=usuarios';
}

// Función para agregar usuario
function agregarUsuario() {
    abrirModalCRUD('usuario', 'crear');
}

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    // Focus en campo de búsqueda principal si existe
    const searchInput = document.querySelector('input[name="busqueda"]');
    if (searchInput) {
        searchInput.focus();
        searchInput.select();
    }
});
</script>