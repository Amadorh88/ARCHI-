<?php
// Módulo: parroquias.php - Gestión de Parroquias
// NOTA: Asume que la conexión a la base de datos PDO está definida como $bd,
// y que las funciones como htmlspecialchars(), addItem(), viewItem(), editItem(), 
// y printRecord() están definidas en otro lugar (p.ej., un archivo de configuración o JS).

// --- LÓGICA PHP PARA PROCESAR BÚSQUEDA Y DATOS ---

$busqueda = $_GET['busqueda'] ?? '';

$query = "SELECT * FROM parroquia WHERE 1=1";
$params = [];

if (!empty($busqueda)) {
    // La búsqueda se aplica a los campos nombre, direccion, y telefono
    $query .= " AND (nombre LIKE ? OR direccion LIKE ? OR telefono LIKE ?)";
    // Sanitización básica para búsqueda
    $searchTerm = "%" . trim($busqueda) . "%"; 
    $params = array_merge($params, array_fill(0, 3, $searchTerm));
}

$query .= " ORDER BY nombre ASC";

try {
    $stmt = $bd->prepare($query);
    $stmt->execute($params);
    $parroquias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Manejo básico de errores de la base de datos
    $parroquias = [];
    // Opcional: echo "Error de consulta: " . $e->getMessage();
}


// Función de ejemplo para formatear teléfono (si es necesario)
function formatTelefono($telefono) {
    return htmlspecialchars($telefono);
}
?>

<h2>Gestión de Parroquias</h2>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <form method="GET" action="parroquias.php" style="flex-grow: 1; max-width: 50%; display: flex; gap: 0.5rem;">
        <input type="text" name="busqueda" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Buscar por Nombre, Dirección o Teléfono" 
               style="padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; flex-grow: 1;">
        <button type="submit" style="padding: 0.5rem 1rem; border: none; border-radius: 4px; cursor: pointer; font-size: 0.9rem; background-color: #3498db; color: white;">
            <i class="fas fa-search"></i> Buscar
        </button>
    </form>
    
    <div style="display: flex; gap: 0.75rem;">
        <button style="padding: 0.5rem 1rem; border: none; border-radius: 4px; cursor: pointer; font-size: 0.9rem; background-color: #2ecc71; color: white;" 
        onclick="agregarParroquia()"
        >
            <i class="fas fa-plus"></i> Agregar Parroquia
        </button>
        <button style="padding: 0.5rem 1rem; border: none; border-radius: 4px; cursor: pointer; font-size: 0.9rem; background-color: #f39c12; color: white;" 
                onclick="window.print()">
            <i class="fas fa-print"></i> Imprimir Lista
        </button>
    </div>
</div>

<div style="overflow-x: auto;">
    <table style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">
        <thead>
            <tr>
                <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">Nombre</th>
                <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">Dirección</th>
                <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">Teléfono</th>
                <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($parroquias)): ?>
                <tr style="border-bottom: 1px solid #e0e0e0;">
                    <td colspan="4" style="padding: 0.75rem 1rem; text-align: center;">No se encontraron parroquias.</td>
                </tr>
            <?php else: ?>
                <?php foreach($parroquias as $parroquia): ?>
                <tr style="border-bottom: 1px solid #e0e0e0;">
                    <td style="padding: 0.75rem 1rem;"><?php echo htmlspecialchars($parroquia['nombre']); ?></td>
                    <td style="padding: 0.75rem 1rem;"><?php echo htmlspecialchars($parroquia['direccion']); ?></td>
                    <td style="padding: 0.75rem 1rem;"><?php echo formatTelefono($parroquia['telefono']); ?></td>
                    <td style="padding: 0.75rem 1rem;">
                        <div style="display: flex; gap: 0.5rem;">
                            <button style="padding: 0.25rem 0.5rem; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem; background-color: #27ae60; color: white;" 
                                    onclick="verParroquia(<?php echo $parroquia['id_parroquia']; ?>, 'parroquia')">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button style="padding: 0.25rem 0.5rem; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem; background-color: #3498db; color: white;" 
                                    onclick="editarParroquia(<?php echo $parroquia['id_parroquia']; ?>, 'parroquia')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button style="padding: 0.25rem 0.5rem; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem; background-color: #e74c3c; color: white;" 
                                    onclick="printRecord(<?php echo $parroquia['id_parroquia']; ?>, 'parroquia')">
                                <i class="fas fa-print"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>