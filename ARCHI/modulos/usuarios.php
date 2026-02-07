<?php
// Se asume que la función safeSearch() está definida y que la conexión $bd (PDO) está establecida.

// Procesar búsqueda
$busqueda = $_GET['busqueda'] ?? '';
$rol = $_GET['rol'] ?? '';
$estado = $_GET['estado'] ?? '';

// Query base
$query = "SELECT id, nombre, dni, usuario, rol, estado, fecha_registro 
          FROM usuarios 
          WHERE 1=1";
$params = [];

// Filtro búsqueda
if (!empty($busqueda)) {
    $query .= " AND (nombre LIKE ? OR usuario LIKE ? OR dni LIKE ?)";
    $searchTerm = safeSearch($busqueda);
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
}

// Filtro rol
if (!empty($rol)) {
    $query .= " AND rol = ?";
    $params[] = $rol;
}

// Filtro estado
if ($estado !== '') {
    $query .= " AND estado = ?";
    $params[] = $estado;
}

$query .= " ORDER BY id DESC LIMIT 50";

try {
    $stmt = $bd->prepare($query);
    $stmt->execute($params);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $usuarios = [];
    echo "<div style='color:red'>Error en la consulta: {$e->getMessage()}</div>";
}
?>
<style>
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .55);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 999;
    }

    .modal-card {
        background: white;
        width: 420px;
        border-radius: 12px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, .25);
        animation: fadeIn .3s ease;
    }

    .modal-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        margin: 0;
        color: #2c3e50;
    }

    .modal-header button {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
    }

    .modal-body {
        padding: 1.25rem;
    }

    .modal-body p {
        margin: .5rem 0;
        color: #34495e;
    }

    .modal-body span {
        font-weight: 600;
        color: #2c3e50;
    }

    .modal-footer {
        padding: 1rem;
        text-align: right;
        border-top: 1px solid #eee;
    }

    .btn-cerrar {
        background: #3498db;
        color: white;
        padding: .5rem 1.2rem;
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }

    @keyframes fadeIn {
        from {
            transform: scale(.95);
            opacity: 0
        }

        to {
            transform: scale(1);
            opacity: 1
        }
    }
</style>

<h2>Gestión de Usuarios</h2>

<!-- <div style="background:#f8f9fa;padding:1.5rem;border-radius:8px;margin-bottom:1.5rem;">
    <h4><i class="fas fa-search"></i> Buscar Usuarios</h4>

    <form id="formBuscarUsuarios" style="display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:1rem;">

        <input type="hidden" name="section" value="usuarios">

        <input type="text" name="busqueda"
               value="<?= htmlspecialchars($busqueda) ?>"
               placeholder="Nombre, usuario o DNI"
               style="padding:0.75rem;border-radius:5px;border:1px solid #ccc;">

        <select name="rol" style="padding:0.75rem;border-radius:5px;">
            <option value="">Todos los roles</option>
            <?php foreach (['admin', 'secretario', 'archivista', 'parroco'] as $r): ?>
                <option value="<?= $r ?>" <?= $rol === $r ? 'selected' : '' ?>>
                    <?= ucfirst($r) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="estado" style="padding:0.75rem;border-radius:5px;">
            <option value="">Todos</option>
            <option value="1" <?= $estado === '1' ? 'selected' : '' ?>>Activo</option>
            <option value="0" <?= $estado === '0' ? 'selected' : '' ?>>Inactivo</option>
        </select>

        <button type="submit" style="background:#3498db;color:white;border:none;padding:0.75rem 1.5rem;border-radius:5px;">
            <i class="fas fa-search"></i> Buscar
        </button>
    </form>
</div>
 -->
<div style="margin-bottom:1rem;">
    <button onclick="abrirModalUsuario('crear')"
        style="background:#27ae60;color:white;padding:0.75rem 1.5rem;border:none;border-radius:5px;">
        <i class="fas fa-user-plus"></i> Nuevo Usuario
    </button>
</div>
<div style="margin-top: 1.5rem; overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">
              
    <thead>
            <tr style="background:#2c3e50;color:white;">
                <th>ID</th>
                <th>Nombre</th>
                <th>DNI</th>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Registro</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $u): ?>
                <tr style="border-bottom: 1px solid #e0e0e0;">
                <td style="padding: 0.75rem 1rem;"><?= $u['id'] ?></td>
                    <td style="padding: 0.75rem 1rem;"><?= htmlspecialchars($u['nombre']) ?></td>
                    <td style="padding: 0.75rem 1rem;"><?= htmlspecialchars($u['dni']) ?></td>
                    <td style="padding: 0.75rem 1rem;"><?= htmlspecialchars($u['usuario']) ?></td>
                    <td style="padding: 0.75rem 1rem;"><?= ucfirst($u['rol']) ?></td>
                    <td style="padding: 0.75rem 1rem;">
                        <button onclick="toggleEstadoUsuario(<?= $u['id'] ?>, <?= $u['estado'] ?>)"
                            title="<?= $u['estado'] ? 'Desactivar' : 'Activar' ?>"
                            style="color:<?= $u['estado'] ? '#c0392b' : '#27ae60' ?>">
                            <i class="fas <?= $u['estado'] ? 'fa-user-slash' : 'fa-user-check' ?>"></i>
                        </button>
                    </td>
                    <td><?= $u['fecha_registro'] ?></td>
                    <td>
                        <button onclick="verUsuario(<?= $u['id'] ?>)" title="Ver">
                            <i class="fas fa-eye"></i>
                        </button>

                        <button onclick="abrirModalUsuario('editar',<?= $u['id'] ?>)" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button> 
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- =========================== Modal para ver usuario ================ -->
<div id="modalVerUsuario" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-header">
            <h3><i class="fas fa-user-shield"></i> Detalle del Usuario</h3>
            <button onclick="cerrarModalUsuario()">×</button>
        </div>

        <div class="modal-body" id="contenidoUsuario">
            <!-- Contenido dinámico -->
        </div>

        <div class="modal-footer">
            <button onclick="cerrarModalUsuario()" class="btn-cerrar">
                Cerrar
            </button>
        </div>
    </div>
</div>