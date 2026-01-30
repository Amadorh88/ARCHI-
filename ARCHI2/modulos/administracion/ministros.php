<?php
// Asumiendo que $bd es tu conexión PDO a la base de datos
// Procesar búsqueda y filtro
$busqueda = $_GET['busqueda'] ?? '';
$filtroTipo = $_GET['tipo'] ?? '';

$query = "SELECT * FROM ministros WHERE 1=1";
$params = [];

if (!empty($busqueda)) {
    // Columnas de búsqueda: nombre_completo, DIP, telefono
    $query .= " AND (nombre_completo LIKE ? OR DIP LIKE ? OR telefono LIKE ?)";
    $searchTerm = "%" . $busqueda . "%"; 
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
}

if (!empty($filtroTipo)) {
    // Columna de filtro: tipo
    $query .= " AND tipo = ?";
    $params[] = $filtroTipo;
}

$query .= " ORDER BY nombre_completo ASC LIMIT 50"; // Ordenar por nombre como en catequistas.php

// Preparación y ejecución de la consulta
try {
    $stmt = $bd->prepare($query);
    $stmt->execute($params);
    $ministros = $stmt->fetchAll(PDO::FETCH_ASSOC); 
} catch (PDOException $e) {
    $ministros = [];
}

// Obtener tipos únicos para el filtro (Sacerdote, Diácono, Obispo, Catequista)
try {
    $tipos = $bd->query("SELECT DISTINCT tipo FROM ministros WHERE tipo IS NOT NULL AND tipo != ''")->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $tipos = [];
}
?>

<h2>Gestión de Ministros</h2>

<div style="margin-bottom: 2rem; display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; justify-content: space-between;">
    
    <form method="GET" action="modulo.php" style="display: flex; gap: 0.5rem; flex-grow: 1; max-width: 600px;">
        <input type="hidden" name="modulo" value="ministros"> 
        
        <input 
            type="text" 
            name="busqueda" 
            placeholder="Buscar por nombre, DIP o teléfono..."
            value="<?php echo htmlspecialchars($busqueda); ?>"
            style="padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; flex-grow: 1;"
        />

        <select 
            name="tipo" 
            onchange="this.form.submit()"
            style="padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;"
        >
            <option value="">Todos los tipos</option>
            <?php foreach ($tipos as $tipo): ?>
                <option value="<?php echo htmlspecialchars($tipo); ?>" 
                        <?php echo ($filtroTipo === $tipo) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($tipo); ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <button type="submit" style="padding: 0.5rem 1rem; border: none; border-radius: 4px; cursor: pointer; background-color: #3498db; color: white;">
            <i class="fas fa-search"></i> Buscar
        </button>
    </form>
    
    <div style="display: flex; gap: 0.5rem;">
        <button 
            style="padding: 0.5rem 1rem; border: none; border-radius: 4px; cursor: pointer; background-color: #27ae60; color: white; font-weight: bold;"
            onclick="abrirModalMinistro()"
        >
            <i class="fas fa-plus"></i> Agregar
        </button>

        <button 
            style="padding: 0.5rem 1rem; border: none; border-radius: 4px; cursor: pointer; background-color: #f39c12; color: white; font-weight: bold;"
            onclick="window.print()"
        >
            <i class="fas fa-print"></i> Imprimir Lista
        </button>
    </div>
</div>

<div style="margin-top: 1.5rem; overflow-x: auto;">
    <table style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">
        <thead>
            <tr>
                <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">ID</th>
                <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">DIP</th>
                <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">Nombre Completo</th>
                <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">Teléfono</th>
                <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">Tipo</th>
                <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($ministros)): ?>
            <tr>
                <td colspan="6" style="padding: 1rem; text-align: center; color: #7f8c8d;">
                    No se encontraron registros de ministros con los criterios de búsqueda o filtro.
                </td>
            </tr>
            <?php else: ?>
                <?php foreach($ministros as $ministro): ?>
                <tr style="border-bottom: 1px solid #e0e0e0;">
                    <td style="padding: 0.75rem 1rem;"><?php echo htmlspecialchars($ministro['id_ministro']); ?></td>
                    <td style="padding: 0.75rem 1rem;"><?php echo htmlspecialchars($ministro['DIP']); ?></td>
                    <td style="padding: 0.75rem 1rem;"><?php echo htmlspecialchars($ministro['nombre_completo']); ?></td>
                    <td style="padding: 0.75rem 1rem;"><?php echo htmlspecialchars($ministro['telefono']); ?></td>
                    <td style="padding: 0.75rem 1rem;">
                        <span style="padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.75rem; font-weight: bold; background-color: #e8f4fd; color: #004085;">
                            <?php echo htmlspecialchars($ministro['tipo']); ?>
                        </span>
                    </td>
                    <td style="padding: 0.75rem 1rem;">
                        <div style="display: flex; gap: 0.5rem;">
                            <button style="padding: 0.25rem 0.5rem; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem; background-color: #27ae60; color: white;" onclick="viewItem(<?php echo $ministro['id_ministro']; ?>, 'ministro')">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button style="padding: 0.25rem 0.5rem; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem; background-color: #3498db; color: white;" onclick="editItem(<?php echo $ministro['id_ministro']; ?>, 'ministro')">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    // Función de ejemplo para el botón Agregar
    function abrirFormularioAgregar() {
        alert('Abrir formulario para agregar nuevo ministro.');
        // Aquí se llamaría a la función real para abrir un modal o redirigir
        // window.location.href = 'agregar_ministro.php'; 
    }
</script>