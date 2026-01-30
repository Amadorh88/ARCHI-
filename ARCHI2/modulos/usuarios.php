<?php
// Procesar búsqueda y filtros
$busqueda = $_GET['busqueda'] ?? '';
$filtro_rol = $_GET['rol'] ?? '';
$filtro_estado = $_GET['estado'] ?? '';

// Construcción de Query (reutilizando tu lógica existente)
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
    <div class="header-section">
        <div class="header-content">
            <div class="header-title">
                <div class="title-icon">
                    <i class="fas fa-users-cog"></i>
                </div>
                <div>
                    <h1>Gestión de Usuarios</h1>
                    <p class="subtitle">Administra los usuarios del sistema</p>
                </div>
            </div>
            <button class="btn-primary tooltip" onclick="agregarUsuario()">
                <i class="fas fa-plus"></i> Nuevo Usuario
                <span class="tooltiptext">Crear nuevo usuario</span>
            </button>
        </div>
    </div>

    <!-- Búsqueda avanzada -->
    <div class="search-container">
        <div class="search-header">
            <h3><i class="fas fa-search"></i> Búsqueda Avanzada</h3>
            <button type="button" class="btn-toggle-search" onclick="toggleSearchOptions()">
                <i class="fas fa-sliders-h"></i> <span>Filtros</span>
            </button>
        </div>
        
        <form id="formBusquedaUsuarios" method="GET" action="" class="search-form">
            <input type="hidden" name="section" value="usuarios">
            
            <!-- Búsqueda principal -->
            <div class="search-main">
                <div class="search-input-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="busqueda" value="<?= htmlspecialchars($busqueda) ?>" 
                           placeholder="Buscar por nombre, DNI, usuario..." 
                           class="search-input" id="mainSearch">
                    <?php if (!empty($busqueda)): ?>
                    <button type="button" class="clear-search" onclick="clearSearchField()" title="Limpiar búsqueda">
                        <i class="fas fa-times"></i>
                    </button>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn-search">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </div>
            
            <!-- Filtros avanzados (inicialmente colapsados) -->
            <div class="search-filters" id="searchFilters">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label class="filter-label">
                            <i class="fas fa-user-tag"></i> Rol del usuario
                        </label>
                        <div class="filter-options">
                            <label class="option-chip <?= empty($filtro_rol) ? 'active' : '' ?>">
                                <input type="radio" name="rol" value="" <?= empty($filtro_rol) ? 'checked' : '' ?> 
                                       onchange="this.form.submit()">
                                <span>Todos</span>
                            </label>
                            <label class="option-chip <?= $filtro_rol == 'admin' ? 'active' : '' ?>">
                                <input type="radio" name="rol" value="admin" <?= $filtro_rol == 'admin' ? 'checked' : '' ?>
                                       onchange="this.form.submit()">
                                <i class="fas fa-user-shield"></i>
                                <span>Administrador</span>
                            </label>
                            <label class="option-chip <?= $filtro_rol == 'secretaria' ? 'active' : '' ?>">
                                <input type="radio" name="rol" value="secretaria" <?= $filtro_rol == 'secretaria' ? 'checked' : '' ?>
                                       onchange="this.form.submit()">
                                <i class="fas fa-user-tie"></i>
                                <span>Secretaría</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label">
                            <i class="fas fa-power-off"></i> Estado del usuario
                        </label>
                        <div class="filter-options">
                            <label class="option-chip <?= $filtro_estado === '' ? 'active' : '' ?>">
                                <input type="radio" name="estado" value="" <?= $filtro_estado === '' ? 'checked' : '' ?>
                                       onchange="this.form.submit()">
                                <span>Todos</span>
                            </label>
                            <label class="option-chip <?= $filtro_estado === '1' ? 'active' : '' ?>">
                                <input type="radio" name="estado" value="1" <?= $filtro_estado === '1' ? 'checked' : '' ?>
                                       onchange="this.form.submit()">
                                <i class="fas fa-check-circle"></i>
                                <span>Activos</span>
                            </label>
                            <label class="option-chip <?= $filtro_estado === '0' ? 'active' : '' ?>">
                                <input type="radio" name="estado" value="0" <?= $filtro_estado === '0' ? 'checked' : '' ?>
                                       onchange="this.form.submit()">
                                <i class="fas fa-times-circle"></i>
                                <span>Inactivos</span>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="filters-actions">
                    <button type="button" class="btn-clear-all" onclick="clearAllFilters()">
                        <i class="fas fa-broom"></i> Limpiar todos los filtros
                    </button>
                    <button type="submit" class="btn-apply-filters">
                        <i class="fas fa-filter"></i> Aplicar filtros
                    </button>
                </div>
            </div>
        </form>
        
        <!-- Estado de filtros activos -->
        <?php if (!empty($busqueda) || !empty($filtro_rol) || $filtro_estado !== ''): ?>
        <div class="active-filters-bar">
            <div class="active-filters-content">
                <span class="filters-label">
                    <i class="fas fa-filter"></i> Filtros aplicados:
                </span>
                <div class="active-filters-tags">
                    <?php if (!empty($busqueda)): ?>
                    <span class="filter-tag">
                        <span>Búsqueda: "<?= htmlspecialchars($busqueda) ?>"</span>
                        <button type="button" class="remove-filter" onclick="removeFilter('busqueda')">
                            <i class="fas fa-times"></i>
                        </button>
                    </span>
                    <?php endif; ?>
                    
                    <?php if (!empty($filtro_rol)): ?>
                    <span class="filter-tag">
                        <span>Rol: <?= ucfirst($filtro_rol) ?></span>
                        <button type="button" class="remove-filter" onclick="removeFilter('rol')">
                            <i class="fas fa-times"></i>
                        </button>
                    </span>
                    <?php endif; ?>
                    
                    <?php if ($filtro_estado !== ''): ?>
                    <span class="filter-tag">
                        <span>Estado: <?= $filtro_estado == '1' ? 'Activo' : 'Inactivo' ?></span>
                        <button type="button" class="remove-filter" onclick="removeFilter('estado')">
                            <i class="fas fa-times"></i>
                        </button>
                    </span>
                    <?php endif; ?>
                </div>
                <div class="results-summary">
                    <span class="results-count">
                        <i class="fas fa-chart-bar"></i>
                        <?php echo count($usuarios); ?> resultados encontrados
                    </span>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Tabla de resultados -->
    <div class="results-container">
        <div class="results-header">
            <h3><i class="fas fa-list"></i> Lista de Usuarios</h3>
            <div class="results-stats">
                <span class="stat-item">
                    <i class="fas fa-users"></i>
                    <span>Total: <?php echo count($usuarios); ?></span>
                </span>
                <?php 
                $activos = array_filter($usuarios, fn($u) => $u['estado'] == 1);
                $inactivos = array_filter($usuarios, fn($u) => $u['estado'] == 0);
                ?>
                <span class="stat-item">
                    <i class="fas fa-check-circle" style="color: #27ae60;"></i>
                    <span>Activos: <?php echo count($activos); ?></span>
                </span>
                <span class="stat-item">
                    <i class="fas fa-times-circle" style="color: #e74c3c;"></i>
                    <span>Inactivos: <?php echo count($inactivos); ?></span>
                </span>
            </div>
        </div>

        <?php if (!empty($usuarios)): ?>
        <div class="table-container">
            <table class="usuarios-table">
                <thead>
                    <tr>
                        <th class="sortable" data-sort="nombre">
                            <span>Nombre</span>
                            <i class="fas fa-sort"></i>
                        </th>
                        <th class="sortable" data-sort="usuario">
                            <span>Usuario</span>
                            <i class="fas fa-sort"></i>
                        </th>
                        <th>DNI</th>
                        <th class="sortable" data-sort="rol">
                            <span>Rol</span>
                            <i class="fas fa-sort"></i>
                        </th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td>
                            <div class="user-info">
                                <div class="user-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="user-details">
                                    <strong><?= htmlspecialchars($u['nombre']) ?></strong>
                                    <small>ID: <?= $u['id'] ?></small>
                                </div>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($u['usuario']) ?></td>
                        <td>
                            <span class="dni-badge"><?= htmlspecialchars($u['dni']) ?></span>
                        </td>
                        <td>
                            <span class="role-badge role-<?= $u['rol'] ?>">
                                <i class="fas fa-<?= $u['rol'] == 'admin' ? 'user-shield' : 'user-tie' ?>"></i>
                                <?= ucfirst($u['rol']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="status-container">
                                <label class="status-switch">
                                    <input type="checkbox" <?= $u['estado'] == 1 ? 'checked' : '' ?> 
                                           onchange="cambiarEstadoUsuario(<?= $u['id'] ?>, this.checked ? 1 : 0)">
                                    <span class="slider"></span>
                                </label>
                                <span class="status-label <?= $u['estado'] == 1 ? 'active' : 'inactive' ?>">
                                    <i class="fas fa-<?= $u['estado'] == 1 ? 'check-circle' : 'times-circle' ?>"></i>
                                    <?= $u['estado'] == 1 ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="action-buttons">
                                <button class="btn-action btn-view tooltip" onclick="abrirModalCRUD('usuario', 'ver', <?= $u['id'] ?>)">
                                    <i class="fas fa-eye"></i>
                                    <span class="tooltiptext">Ver detalles</span>
                                </button>
                                <button class="btn-action btn-edit tooltip" onclick="abrirModalCRUD('usuario', 'editar', <?= $u['id'] ?>)">
                                    <i class="fas fa-edit"></i>
                                    <span class="tooltiptext">Editar</span>
                                </button>
                                <button class="btn-action btn-delete tooltip" onclick="confirmarEliminar('usuario', <?= $u['id'] ?>, '<?= addslashes($u['nombre']) ?>')">
                                    <i class="fas fa-trash"></i>
                                    <span class="tooltiptext">Eliminar</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="no-results">
            <div class="no-results-content">
                <i class="fas fa-search"></i>
                <h3>No se encontraron usuarios</h3>
                <p><?php echo (!empty($busqueda) || !empty($filtro_rol) || $filtro_estado !== '') 
                    ? "No hay usuarios que coincidan con los filtros aplicados" 
                    : "No hay usuarios registrados en el sistema"; ?></p>
                
                <?php if (!empty($busqueda) || !empty($filtro_rol) || $filtro_estado !== ''): ?>
                <div class="no-results-actions">
                    <button onclick="clearAllFilters()" class="btn-clear-filters">
                        <i class="fas fa-broom"></i> Limpiar filtros
                    </button>
                    <button onclick="abrirModalCRUD('usuario', 'crear')" class="btn-create-user">
                        <i class="fas fa-user-plus"></i> Crear primer usuario
                    </button>
                </div>
                <?php else: ?>
                <button onclick="abrirModalCRUD('usuario', 'crear')" class="btn-create-user">
                    <i class="fas fa-user-plus"></i> Crear primer usuario
                </button>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'modals.php'; ?>
<script src="js/usuarios.js"></script>
<script>
// Funciones para mejorar la experiencia de búsqueda
function toggleSearchOptions() {
    const filters = document.getElementById('searchFilters');
    const toggleBtn = document.querySelector('.btn-toggle-search');
    
    if (filters.style.display === 'none' || filters.style.display === '') {
        filters.style.display = 'block';
        toggleBtn.classList.add('active');
        toggleBtn.innerHTML = '<i class="fas fa-sliders-h"></i> <span>Ocultar filtros</span>';
    } else {
        filters.style.display = 'none';
        toggleBtn.classList.remove('active');
        toggleBtn.innerHTML = '<i class="fas fa-sliders-h"></i> <span>Mostrar filtros</span>';
    }
}

function clearSearchField() {
    document.getElementById('mainSearch').value = '';
    document.getElementById('formBusquedaUsuarios').submit();
}

function removeFilter(filterName) {
    const url = new URL(window.location.href);
    url.searchParams.delete(filterName);
    window.location.href = url.toString();
}

function clearAllFilters() {
    window.location.href = 'dashboard.php?section=usuarios';
}

// Auto-submit al cambiar opciones de filtro
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar filtros como colapsados
    const filters = document.getElementById('searchFilters');
    if (filters) {
        filters.style.display = 'none';
    }
    
    // Focus en campo de búsqueda principal
    const mainSearch = document.getElementById('mainSearch');
    if (mainSearch) {
        mainSearch.focus();
        mainSearch.select();
    }
    
    // Efecto de placeholder dinámico
    const placeholders = [
        "Buscar por nombre...",
        "Buscar por DNI...", 
        "Buscar por usuario...",
        "Buscar usuarios..."
    ];
    let currentIndex = 0;
    
    if (mainSearch && !mainSearch.value) {
        setInterval(() => {
            mainSearch.placeholder = placeholders[currentIndex];
            currentIndex = (currentIndex + 1) % placeholders.length;
        }, 3000);
    }
}); 
</script>