
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
    <div class="header-section" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2><i class="fas fa-users-cog"></i> Gestión de Usuarios</h2>
        <button class="btn-primary" onclick="abrirModalCRUD('usuario', 'crear')" style="background: #27ae60; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer;">
            <i class="fas fa-plus"></i> Nuevo Usuario
        </button>
    </div>

    <div class="filters-card" style="background: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <form action="" method="GET" style="display: flex; gap: 10px; align-items: flex-end;">
            <input type="hidden" name="section" value="usuarios">
            <input type="text" name="busqueda" value="<?= htmlspecialchars($busqueda) ?>" placeholder="Buscar...">
            <select name="rol">
                <option value="">Todos los Roles</option>
                <option value="admin" <?= $filtro_rol == 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="secretaria" <?= $filtro_rol == 'secretaria' ? 'selected' : '' ?>>Secretaria</option>
            </select>
            <button type="submit" style="background: #3498db; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer;">Filtrar</button>
            <a href="dashboard.php?section=usuarios" style="text-decoration: none; color: #666; padding: 8px;">Limpiar</a>
        </form>
    </div>

    <div class="table-container">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f4f4f4;">
                <tr>
                    <th style="padding: 12px;">Nombre</th>
                    <th style="padding: 12px;">Rol</th>
                    <th style="padding: 12px; text-align: center;">Estado</th>
                    <th style="padding: 12px; text-align: center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px;"><?= htmlspecialchars($u['nombre']) ?></td>
                    <td style="padding: 12px;"><?= ucfirst($u['rol']) ?></td>
                    <td style="padding: 12px; text-align: center;">
                        <label class="switch">
                            <input type="checkbox" <?= $u['estado'] == 1 ? 'checked' : '' ?> 
                                   onchange="cambiarEstadoUsuario(<?= $u['id'] ?>, this.checked ? 1 : 0)">
                            <span class="slider"></span>
                        </label>
                    </td>
                    <td style="padding: 12px; text-align: center;">
                        <button class="btn-action btn-view" onclick="abrirModalCRUD('usuario', 'ver', <?= $u['id'] ?>)"><i class="fas fa-eye"></i></button>
                        <button class="btn-action btn-edit" onclick="abrirModalCRUD('usuario', 'editar', <?= $u['id'] ?>)"><i class="fas fa-edit"></i></button>
                        <button class="btn-action btn-delete" onclick="confirmarEliminar('usuario', <?= $u['id'] ?>, '<?= $u['nombre'] ?>')"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'modals.php'; ?>
<script src="js/usuarios.js"></script>
<style>
    /* Estilos adicionales para mejorar la UI de los botones de acción */
    .btn-action:hover {
        transform: scale(1.2);
        transition: 0.2s;
    }
    .filters-card select, .filters-card input {
        outline: none;
    }
    .filters-card select:focus, .filters-card input:focus {
        border-color: #3498db !important;
        box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
    }
</style>