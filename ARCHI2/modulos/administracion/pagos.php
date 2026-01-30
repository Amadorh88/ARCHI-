<?php
// Módulo: pagos.php - Registro de Pagos
// NOTA: Asume que las funciones safeSearch(), formatDate() y la conexión $bd están definidas.

// --- LÓGICA PHP PARA PROCESAR BÚSQUEDA ---
$busqueda = $_GET['busqueda'] ?? '';

// Los filtros de estado y método de pago se mantienen aquí para que el formulario no falle, 
// pero no se usarán en la consulta SQL ya que no existen en la tabla 'pago'.
$filtroEstado = $_GET['estado'] ?? '';
$filtroMetodo = $_GET['metodo'] ?? '';

// Definición de las variables de filtro vacías para el formulario (se rellenan con [] para evitar errores)
$metodosPago = []; 
$estadosPago = []; 

$query = "SELECT p.id_pago, p.concepto, p.cantidad, p.recibido, p.cambio, f.nombre_completo AS feligres_nombre 
         FROM pago p 
         LEFT JOIN feligres f ON p.id_feligres = f.id_feligres 
         WHERE 1=1";
$params = [];

if (!empty($busqueda)) {
    // La búsqueda se aplica al nombre del feligrés o al concepto del pago.
    $query .= " AND (f.nombre_completo LIKE ? OR p.concepto LIKE ?)";
    // Usar la función safeSearch() si está definida
    $searchTerm = isset($bd) ? "%" . trim($busqueda) . "%" : safeSearch($busqueda);
    $params = array_merge($params, array($searchTerm, $searchTerm));
}

// **IMPORTANTE**: No se aplican los filtros $filtroEstado ni $filtroMetodo, 
// ya que los campos 'estado', 'metodo_pago', 'referencia' y 'fecha_pago' no existen en la tabla `pago`.

$query .= " ORDER BY p.id_pago DESC LIMIT 50";

try {
    $stmt = $bd->prepare($query);
    $stmt->execute($params);
    $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // En caso de error de DB
    $pagos = [];
}
?>

<h2>Donaciones</h2>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <form method="GET" action="pagos.php" style="flex-grow: 1; max-width: 50%; display: flex; gap: 0.5rem;">
        <input type="text" name="busqueda" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Buscar por Nombre de Feligrés o Concepto" 
               style="padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; flex-grow: 1;">
        <button type="submit" style="padding: 0.5rem 1rem; border: none; border-radius: 4px; cursor: pointer; font-size: 0.9rem; background-color: #3498db; color: white;">
            <i class="fas fa-search"></i> Buscar
        </button>
    </form>
    
    <div style="display: flex; gap: 0.75rem;">
        <button style="padding: 0.5rem 1rem; border: none; border-radius: 4px; cursor: pointer; font-size: 0.9rem; background-color: #2ecc71; color: white;" 
                onclick="agregarPago()">
            <i class="fas fa-plus"></i> Agregar 
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
                <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">ID </th>
                <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">Feligrés</th>
                <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">Concepto</th>
                <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">Cantidad Debida</th><!-- 
                <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">Recibido</th>
                <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">Cambio</th> -->
                <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pagos)): ?>
                <tr style="border-bottom: 1px solid #e0e0e0;">
                    <td colspan="7" style="padding: 0.75rem 1rem; text-align: center;">No se encontraron registros de donaciones.</td>
                </tr>
            <?php else: ?>
                <?php foreach($pagos as $pago): ?>
                <tr style="border-bottom: 1px solid #e0e0e0;">
                    <td style="padding: 0.75rem 1rem;"><?php echo htmlspecialchars($pago['id_pago']); ?></td>
                    <td style="padding: 0.75rem 1rem;"><?php echo htmlspecialchars($pago['feligres_nombre'] ?? 'N/A'); ?></td>
                    <td style="padding: 0.75rem 1rem;"><?php echo htmlspecialchars($pago['concepto']); ?></td>
                    <td style="padding: 0.75rem 1rem;"> <?php echo number_format($pago['cantidad'] ?? 0, 2); ?> Fcfa</td>
                    <!-- <td style="padding: 0.75rem 1rem;"> <?php echo number_format($pago['recibido'] ?? 0, 2); ?> Fcfa</td>
                    <td style="padding: 0.75rem 1rem;"> <?php echo number_format($pago['cambio'] ?? 0, 2); ?> Fcfa</td>
                     --><td style="padding: 0.75rem 1rem;">
                        <div style="display: flex; gap: 0.5rem;">
                            <button style="padding: 0.25rem 0.5rem; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem; background-color: #27ae60; color: white;" 
                                    onclick="verPago(<?php echo $pago['id_pago']; ?>, 'pago')">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button style="padding: 0.25rem 0.5rem; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem; background-color: #3498db; color: white;" 
                                    onclick="editarPago(<?php echo $pago['id_pago']; ?>, 'pago')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button style="padding: 0.25rem 0.5rem; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem; background-color: #e74c3c; color: white;" 
                                    onclick="printRecord(<?php echo $pago['id_pago']; ?>, 'pago')">
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