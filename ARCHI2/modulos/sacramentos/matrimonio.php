<?php
 
require_once 'config/db.php';

$busqueda = $_GET['busqueda'] ?? '';

$query = "
    SELECT 
        m.id_matrimonio,
        m.registro,
        m.fecha,
        esposo.nombre_completo  AS esposo_nombre,
        esposa.nombre_completo  AS esposa_nombre,
        m.lugar,
        mi.nombre_completo      AS ministro_nombre
    FROM matrimonio m

    -- Esposo
    LEFT JOIN matrimonio_feligres mf_esposo 
        ON mf_esposo.id_matrimonio = m.id_matrimonio 
        AND mf_esposo.rol = 'esposo'
    LEFT JOIN feligres esposo 
        ON esposo.id_feligres = mf_esposo.id_feligres

    -- Esposa
    LEFT JOIN matrimonio_feligres mf_esposa 
        ON mf_esposa.id_matrimonio = m.id_matrimonio 
        AND mf_esposa.rol = 'esposa'
    LEFT JOIN feligres esposa 
        ON esposa.id_feligres = mf_esposa.id_feligres

    -- Ministro
    LEFT JOIN ministros mi 
        ON mi.id_ministro = m.id_ministro

    WHERE 1 = 1
";

$params = [];

if (!empty($busqueda)) {
    $query .= "
        AND (
            m.registro LIKE ?
            OR esposo.nombre_completo LIKE ?
            OR esposa.nombre_completo LIKE ?
        )
    ";

    $searchTerm = '%' . trim($busqueda) . '%';
    $params = [$searchTerm, $searchTerm, $searchTerm];
}

$query .= " ORDER BY m.fecha DESC LIMIT 50";

try {
    $stmt = $bd->prepare($query);
    $stmt->execute($params);
    $matrimonioList = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $matrimonioList = [];
    echo "<div style='color:red;padding:10px;border:1px solid red'>
            Error: {$e->getMessage()}
          </div>";
}

/* Listas auxiliares (si las necesitas en filtros o formularios) */
$feligresesList = $bd->query("
    SELECT id_feligres, nombre_completo 
    FROM feligres 
    ORDER BY nombre_completo
")->fetchAll(PDO::FETCH_ASSOC);

$ministrosList = $bd->query("
    SELECT id_ministro, nombre_completo 
    FROM ministros 
    WHERE tipo IN ('sacerdote','obispo') 
    ORDER BY nombre_completo
")->fetchAll(PDO::FETCH_ASSOC);


?>

<h2><i class="fas fa-heart"></i> Gestión de Matrimonios</h2>

<div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
    <h4 style="margin-top: 0; color: #2c3e50;">
        <i class="fas fa-search"></i> Buscar Matrimonios
    </h4>
    <form method="GET" action="" style="display: grid; grid-template-columns: 1fr auto auto; gap: 1rem; align-items: end;">
        <input type="hidden" name="section" value="matrimonio">
        
        <div>
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #2c3e50;">
                <i class="fas fa-filter"></i> Término de Búsqueda
            </label>
            <input type="text" name="busqueda" value="<?php echo htmlspecialchars($busqueda); ?>" 
                   placeholder="Buscar por registro, feligrés o cónyuge..." 
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
            <button type="button" onclick="window.location.href='?section=matrimonio'" class="btn tooltip"
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
            • Resultados: <?php echo count($matrimonioList); ?> registros
        </span>
    </div>
    <?php endif; ?>
</div>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
    <div>
        <button class="btn tooltip" onclick="agregarMatrimonio()" 
                style="background: #27ae60; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 5px; cursor: pointer; font-weight: 600; margin-right: 10px;">
            <i class="fas fa-plus"></i> Nuevo Registro
        </button>
        
        <button class="btn tooltip" onclick="printTable('matrimonio-table', 'Lista de Matrimonios')"
                style="background: #34495e; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 5px; cursor: pointer; font-weight: 600;">
            <i class="fas fa-print"></i> Imprimir Lista
        </button>
    </div>
    
    <div style="color: #7f8c8d; font-size: 0.9rem;">
        <i class="fas fa-info-circle"></i> Total: <?php echo count($matrimonioList); ?> matrimonios
    </div>
</div>

<div style="margin-top: 1.5rem; overflow-x: auto;">
    <table id="matrimonio-table" style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">
    <thead>
    <tr>
        <th style="padding:0.75rem 1rem;background:#2c3e50;color:white;">Registro</th>
        <th style="padding:0.75rem 1rem;background:#2c3e50;color:white;">Esposo</th>
        <th style="padding:0.75rem 1rem;background:#2c3e50;color:white;">Esposa</th>
        <th style="padding:0.75rem 1rem;background:#2c3e50;color:white;">Lugar</th>
        <th style="padding:0.75rem 1rem;background:#2c3e50;color:white;">Fecha</th>
        <th style="padding:0.75rem 1rem;background:#2c3e50;color:white;">Acciones</th>
    </tr>
</thead>

<tbody>
<?php foreach ($matrimonioList as $row): ?>
    <tr style="border-bottom:1px solid #e0e0e0;">
        <td style="padding:0.75rem 1rem;">
            <?= htmlspecialchars($row['registro']) ?>
        </td>

        <td style="padding:0.75rem 1rem;">
            <?= htmlspecialchars($row['esposo_nombre'] ?? '—') ?>
        </td>

        <td style="padding:0.75rem 1rem;">
            <?= htmlspecialchars($row['esposa_nombre'] ?? '—') ?>
        </td>

        <td style="padding:0.75rem 1rem;">
            <?= htmlspecialchars($row['lugar']) ?>
        </td>

        <td style="padding:0.75rem 1rem;">
            <?= formatDate($row['fecha']) ?>
        </td>

        <td style="padding:0.75rem 1rem;">
            <div style="display:flex;gap:0.5rem;">
                <button class="btn-action btn-view tooltip"
                        onclick="viewItem(<?= $row['id_matrimonio'] ?>,'matrimonio')">
                    <i class="fas fa-eye"></i>
                    <span class="tooltiptext">Ver detalles</span>
                </button>

                <button class="btn-action btn-edit tooltip"
                        onclick="editarMatrimonio(<?= $row['id_matrimonio'] ?>,'matrimonio')">
                    <i class="fas fa-edit"></i>
                    <span class="tooltiptext">Editar</span>
                </button>

                <button class="btn-action btn-delete tooltip"
                        onclick="deleteItem(<?= $row['id_matrimonio'] ?>,'matrimonio')">
                    <i class="fas fa-trash"></i>
                    <span class="tooltiptext">Eliminar</span>
                </button>

                <button class="btn-action tooltip"
                        onclick="printRecord('matrimonio',<?= $row['id_matrimonio'] ?>)"
                        style="background-color:#27ae60;">
                    <i class="fas fa-file-pdf"></i>
                    <span class="tooltiptext">Imprimir Certificado</span>
                </button>
            </div>
        </td>
    </tr>
<?php endforeach; ?>

<?php if (empty($matrimonioList)): ?>
    <tr>
        <td colspan="6" style="padding:2rem;text-align:center;color:#7f8c8d;">
            <i class="fas fa-search" style="font-size:2rem;"></i>
            <h3>No se encontraron resultados</h3>
            <p>
                <?= !empty($busqueda)
                    ? "No hay matrimonios que coincidan con \"$busqueda\""
                    : "No hay registros de matrimonios"; ?>
            </p>
        </td>
    </tr>
<?php endif; ?>
</tbody>

    </table>
</div>

<div id="addEditModal_matrimonio" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('addEditModal_matrimonio')">&times;</span>
        <h3><i class="fas fa-plus-circle"></i> Nuevo Registro de Matrimonio</h3>
        <form id="matrimonioForm" onsubmit="saveData(event, 'matrimonio')">
            <input type="hidden" id="registroId_matrimonio" name="id">
            
            <div class="form-group">
                <label for="id_feligres_mat">Feligrés (Debe haber completado Catequesis Matrimonial):</label>
                <select id="id_feligres_mat" name="id_feligres" required>
                    <option value="">Seleccione Feligrés</option>
                    <?php foreach ($feligresesList as $feligres): ?>
                        <option value="<?= $feligres['id_feligres'] ?>"><?= $feligres['nombre_completo'] ?></option>
                    <?php endforeach; ?>
                </select>
                </div>

            <div class="form-group">
                <label for="conyugue_mat">Nombre del Cónyuge:</label>
                <input type="text" id="conyugue_mat" name="conyugue" required>
            </div>
            
            <div class="form-group">
                <label for="registro_mat">Nº de Registro:</label>
                <input type="text" id="registro_mat" name="registro" required>
            </div>
            
            <div class="form-group">
                <label for="fecha_mat">Fecha del Sacramento:</label>
                <input type="date" id="fecha_mat" name="fecha" required>
            </div>
            
            <div class="form-group">
                <label for="lugar_mat">Lugar del Matrimonio:</label>
                <input type="text" id="lugar_mat" name="lugar" required>
            </div>

            <div class="form-group">
                <label for="testigos_mat">Testigos (separados por coma):</label>
                <input type="text" id="testigos_mat" name="testigos" placeholder="Ej: José Díaz, Marta Ramos" required>
            </div>
            
            <div class="form-group">
                <label for="id_ministro_mat">Ministro (Sacerdote/Obispo):</label>
                <select id="id_ministro_mat" name="id_ministro" required>
                    <option value="">Seleccione Ministro</option>
                    <?php foreach ($ministrosList as $ministro): ?>
                        <option value="<?= $ministro['id_ministro'] ?>"><?= $ministro['nombre_completo'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Guardar</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('addEditModal_matrimonio')">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<div id="viewModal_matrimonio" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('viewModal_matrimonio')">&times;</span>
        <h3><i class="fas fa-info-circle"></i> Detalles del Matrimonio</h3>
        <div id="matrimonioDetails">
            </div>
        <div class="form-actions">
            <button onclick="printRecord('matrimonio', 'id_matrimonio')" class="btn btn-info"><i class="fas fa-certificate"></i> Imprimir Certificado</button>
        </div>
    </div>
</div>