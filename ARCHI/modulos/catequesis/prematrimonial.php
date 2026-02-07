<?php
require_once 'config/db.php';

$busqueda = $_GET['busqueda'] ?? '';

$query = "
    SELECT 
        c.id_catequesis,
        c.id_feligres,
        c.nombre_catequesis,
        f.nombre_completo AS feligres_nombre,
        p.nombre AS parroquia_nombre
    FROM 
        catequesis c
    JOIN 
        feligres f ON c.id_feligres = f.id_feligres
    JOIN 
        parroquia p ON c.id_parroquia = p.id_parroquia
    WHERE 
        c.tipo = 'Matrimonial'";

$params = [];

if (!empty($busqueda)) {
    $query .= " AND (f.nombre_completo LIKE ? OR c.nombre_catequesis LIKE ? OR p.nombre LIKE ?)";
    $searchTerm = safeSearch($busqueda);
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
}

$query .= " ORDER BY c.id_catequesis DESC LIMIT 50";

try {
    $stmt = $bd->prepare($query);
    $stmt->execute($params);
    $catequesisList = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $catequesisList = [];
    echo "<div style='color: red; padding: 10px; border: 1px solid red;'>Error: " . $e->getMessage() . "</div>";
}

$feligresesList = $bd->query("SELECT id_feligres, nombre_completo FROM feligres ORDER BY nombre_completo")->fetchAll(PDO::FETCH_ASSOC);
$cursosList = $bd->query("SELECT id_curso, nombre FROM curso WHERE nombre LIKE '%matrimonial%' OR nombre LIKE '%matrimonio%' ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
$parroquiasList = $bd->query("SELECT id_parroquia, nombre FROM parroquia ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
?>

<h2><i class="fas fa-ring"></i> Gestión de Catequesis Pre-matrimonial</h2>
<!-- 
<div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
    <h4 style="margin-top: 0; color: #2c3e50;">
        <i class="fas fa-search"></i> Buscar Catequesis
    </h4>
    <form method="GET" action="" style="display: grid; grid-template-columns: 1fr auto auto; gap: 1rem; align-items: end;">
        <input type="hidden" name="section" value="prematrimonial">
        
        <div>
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #2c3e50;">
                <i class="fas fa-filter"></i> Término de Búsqueda
            </label>
            <input type="text" name="busqueda" value="<?php echo htmlspecialchars($busqueda); ?>" 
                   placeholder="Buscar por feligrés, curso o parroquia..." 
                   style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px;">
        </div>
        
        <div>
            <button type="submit" class="btn tooltip" 
                    style="background: #3498db; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 5px; cursor: pointer; font-weight: 600; height: 48px;">
                <i class="fas fa-search"></i> Buscar
                <span class="tooltiptext">Ejecutar búsqueda</span>
            </button>
        </div>
        
        <div>
            <button type="button" onclick="window.location.href='?section=prematrimonial'" class="btn tooltip"
                    style="background: #95a5a6; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 5px; cursor: pointer; font-weight: 600; height: 48px;">
                <i class="fas fa-broom"></i> Limpiar
                <span class="tooltiptext">Limpiar filtros</span>
            </button>
        </div>
    </form>
    
    <?php if (!empty($busqueda)): ?>
    <div style="margin-top: 1rem; padding: 0.75rem; background: #e8f4fd; border-radius: 5px; border-left: 4px solid #3498db;">
        <strong>Filtros activos:</strong>
        <?php 
        $filtros = [];
        if (!empty($busqueda)) $filtros[] = "Búsqueda: \"$busqueda\"";
        echo implode(' • ', $filtros);
        ?>
        <span style="color: #7f8c8d; font-size: 0.9rem;">
            • Resultados: <?php echo count($catequesisList); ?> registros
        </span>
    </div>
    <?php endif; ?>
</div>
 -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
    <div>
        <button class="btn tooltip" onclick="openAddModal('prematrimonial')" 
                style="background: #27ae60; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 5px; cursor: pointer; font-weight: 600; margin-right: 10px;">
            <i class="fas fa-plus"></i> Nuevo Registro
        </button>
        
       <!--  <button class="btn tooltip" onclick="printTable('catequesis-table', 'Lista de Catequesis Pre-matrimonial')"
                style="background: #34495e; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 5px; cursor: pointer; font-weight: 600;">
            <i class="fas fa-print"></i> Imprimir Lista
        </button>
    --> </div>
    
    <div style="color: #7f8c8d; font-size: 0.9rem;">
        <i class="fas fa-info-circle"></i> Total: <?php echo count($catequesisList); ?> registros
    </div>
</div>

<div style="margin-top: 1.5rem; overflow-x: auto;">
    <table id="catequesis-table" style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">
        <thead>
            <tr>
                <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">ID</th>
                <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">Feligrés</th>
                <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">Curso</th>
                <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">Parroquia</th>
                <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($catequesisList as $row): ?>
            <tr style="border-bottom: 1px solid #e0e0e0;">
                <td style="padding: 0.75rem 1rem;"><?= htmlspecialchars($row['id_catequesis']) ?></td>
                <td style="padding: 0.75rem 1rem;"><?= htmlspecialchars($row['feligres_nombre']) ?></td>
                <td style="padding: 0.75rem 1rem;"><?= htmlspecialchars($row['nombre_catequesis']) ?></td>
                <td style="padding: 0.75rem 1rem;"><?= htmlspecialchars($row['parroquia_nombre']) ?></td>
                <td style="padding: 0.75rem 1rem;">
                    <div style="display: flex; gap: 0.5rem;">
                        <button class="btn-action btn-view tooltip" onclick="viewItem(<?= $row['id_catequesis'] ?>, 'catequesis')">
                            <i class="fas fa-eye"></i>
                            <span class="tooltiptext">Ver detalles</span>
                        </button>
                        <button class="btn-action btn-edit tooltip" onclick="editItem(<?= $row['id_catequesis'] ?>, 'catequesis')">
                            <i class="fas fa-edit"></i>
                            <span class="tooltiptext">Editar</span>
                        </button>
                        <button class="btn-action btn-delete tooltip" onclick="deleteItem(<?= $row['id_catequesis'] ?>, 'catequesis')">
                            <i class="fas fa-trash"></i>
                            <span class="tooltiptext">Eliminar</span>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            
            <?php if(empty($catequesisList)): ?>
            <tr>
                <td colspan="5" style="padding: 2rem; text-align: center; color: #7f8c8d;">
                    <i class="fas fa-search" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                    <h3>No se encontraron resultados</h3>
                    <p><?php echo !empty($busqueda) ? "No hay catequesis que coincidan con \"$busqueda\"" : "No hay registros de catequesis"; ?></p>
                    <?php if (!empty($busqueda)): ?>
                    <button onclick="window.location.href='?section=prematrimonial'" class="btn" style="background: #3498db; color: white; border: none; padding: 0.5rem 1rem; border-radius: 5px; cursor: pointer; margin-top: 0.5rem;">
                        <i class="fas fa-times"></i> Limpiar búsqueda
                    </button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="addEditModal_prematrimonial" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('addEditModal_prematrimonial')">&times;</span>
        <h3><i class="fas fa-plus-circle"></i> Nuevo Registro de Catequesis Pre-matrimonial</h3>
        <form id="prematrimonialForm" onsubmit="saveData(event, 'prematrimonial')">
            <input type="hidden" id="registroId_prematrimonial" name="id">
            
            <div class="form-group">
                <label for="id_feligres_prem">Feligrés:</label>
                <select id="id_feligres_prem" name="id_feligres" required>
                    <option value="">Seleccione Feligrés</option>
                    <?php foreach ($feligresesList as $feligres): ?>
                        <option value="<?= $feligres['id_feligres'] ?>"><?= $feligres['nombre_completo'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="id_curso_prem">Curso de Catequesis:</label>
                <select id="id_curso_prem" name="id_curso" required>
                    <option value="">Seleccione Curso</option>
                    <?php foreach ($cursosList as $curso): ?>
                        <option value="<?= $curso['id_curso'] ?>"><?= $curso['nombre'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="id_parroquia_prem">Parroquia:</label>
                <select id="id_parroquia_prem" name="id_parroquia" required>
                    <option value="">Seleccione Parroquia</option>
                    <?php foreach ($parroquiasList as $parroquia): ?>
                        <option value="<?= $parroquia['id_parroquia'] ?>"><?= $parroquia['nombre'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <input type="hidden" name="tipo" value="Matrimonial">
            <input type="hidden" name="nombre_catequesis" value="Catequesis Pre-matrimonial"> 
            
            <div class="form-actions">
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Guardar</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('addEditModal_prematrimonial')">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<div id="viewModal_prematrimonial" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('viewModal_prematrimonial')">&times;</span>
        <h3><i class="fas fa-info-circle"></i> Detalles de la Catequesis</h3>
        <div id="prematrimonialDetails">
            </div>
        <div class="form-actions">
            <button onclick="printRecord('catequesis', 'id_catequesis')" class="btn btn-info"><i class="fas fa-certificate"></i> Imprimir</button>
        </div>
    </div>
</div>