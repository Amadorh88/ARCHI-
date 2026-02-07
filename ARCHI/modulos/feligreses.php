<?php
// Nota: Se asume que la función safeSearch() está definida y que la conexión $bd (PDO) está establecida.

// Procesar búsqueda si existe
$busqueda = $_GET['busqueda'] ?? '';
// El filtro de estado se ha eliminado ya que el campo 'estado' no existe en la tabla 'feligres'.

// Query para la tabla 'feligres'
$query = "SELECT id_feligres, nombre_completo, nombre_padre, nombre_madre, fecha_nacimiento, lugar_nacimiento FROM feligres WHERE 1=1";
$params = [];

if (!empty($busqueda)) {
    // La búsqueda se realizará sobre 'nombre_completo', 'nombre_padre', o 'nombre_madre'
    $query .= " AND (nombre_completo LIKE ? OR nombre_padre LIKE ? OR nombre_madre LIKE ?)";
    // Asume que safeSearch() añade los comodines '%' si es necesario.
    $searchTerm = safeSearch($busqueda); 
    // Usamos el mismo término de búsqueda para los 3 campos de nombre
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
}

// Se ha eliminado el filtro de estado

$query .= " ORDER BY id_feligres DESC LIMIT 50"; // Ordenar por ID del feligrés

try {
    $stmt = $bd->prepare($query);
    $stmt->execute($params);
    $feligreses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // En un entorno real, manejar el error (e.g., loguearlo y mostrar un mensaje amigable)
    $feligreses = []; 
    echo "<div style='color: red; padding: 10px; border: 1px solid red;'>Error en la consulta de base de datos: " . $e->getMessage() . "</div>";
}
?>

        <h2>Gestión de Feligreses</h2>
        
     <!--    <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
            <h4 style="margin-top: 0; color: #2c3e50;">
                <i class="fas fa-search"></i> Buscar Feligreses
            </h4>
            <form id="formBusquedaFeligreses" method="GET" action="" style="display: grid; grid-template-columns: 1fr auto auto; gap: 1rem; align-items: end;">
                <input type="hidden" name="section" value="feligreses">
                
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #2c3e50;">
                        <i class="fas fa-filter"></i> Término de Búsqueda
                    </label>
                    <input type="text" name="busqueda" value="<?php echo htmlspecialchars($busqueda); ?>" 
                           placeholder="Buscar por nombre completo, padre o madre..." 
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
                    <button type="button" onclick="limpiarBusquedaFeligreses()" class="btn tooltip"
                            style="background: #95a5a6; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 5px; cursor: pointer; font-weight: 600; height: 48px;">
                        <i class="fas fa-broom"></i> Limpiar
                        <span class="tooltiptext">Limpiar filtros</span>
                    </button>
                </div>
            </form>
            
            <?php if (!empty($busqueda)): // Solo se muestra si hay búsqueda activa ?>
            <div style="margin-top: 1rem; padding: 0.75rem; background: #e8f4fd; border-radius: 5px; border-left: 4px solid #3498db;">
                <strong>Filtros activos:</strong>
                <?php 
                $filtros = [];
                if (!empty($busqueda)) $filtros[] = "Búsqueda: \"$busqueda\"";
                echo implode(' • ', $filtros);
                ?>
                <span style="color: #7f8c8d; font-size: 0.9rem;">
                    • Resultados: <?php echo count($feligreses); ?> registros
                </span>
            </div>
            <?php endif; ?>
        </div> -->

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <div>
                <button class="btn tooltip" onclick="abrirModalFeligres('crear')" 
                        style="width: auto; background: #27ae60; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 5px; cursor: pointer; font-weight: 600; margin-right: 10px;">
                    <i class="fas fa-user-plus"></i> Nuevo Feligrés
                    <span class="tooltiptext">Registrar nuevo feligrés</span>
                </button>
                
                <!-- <button class="btn tooltip" onclick="imprimirListaFeligreses()" 
                        style="width: auto; background: #34495e; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 5px; cursor: pointer; font-weight: 600;">
                    <i class="fas fa-print"></i> Imprimir Lista
                    <span class="tooltiptext">Imprimir la lista actual de feligreses</span>
                </button> -->
            </div>
            
            <div style="color: #7f8c8d; font-size: 0.9rem;">
                <i class="fas fa-info-circle"></i> Total: <?php echo count($feligreses); ?> feligreses
            </div>
        </div>

        <div style="margin-top: 1.5rem; overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">
                <thead>
                    <tr>
                        <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">ID Feligrés</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">Nombre Completo</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">Padre</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">Madre</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">Fec. Nacimiento</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; background-color: #2c3e50; color: white; font-weight: 600;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($feligreses as $feligres): ?>
                    <tr style="border-bottom: 1px solid #e0e0e0;">
                        <td style="padding: 0.75rem 1rem;"><?php echo htmlspecialchars($feligres['id_feligres']); ?></td>
                        <td style="padding: 0.75rem 1rem;"><?php echo htmlspecialchars($feligres['nombre_completo']); ?></td>
                        <td style="padding: 0.75rem 1rem;"><?php echo htmlspecialchars($feligres['nombre_padre']); ?></td>
                        <td style="padding: 0.75rem 1rem;"><?php echo htmlspecialchars($feligres['nombre_madre']); ?></td>
                        <td style="padding: 0.75rem 1rem;"><?php echo htmlspecialchars($feligres['fecha_nacimiento']); ?></td>
                        <td style="padding: 0.75rem 1rem;">
                            <div style="display: flex; gap: 0.5rem;">
                                <button class="btn-action btn-view tooltip" onclick="verFeligres(<?php echo $feligres['id_feligres']; ?>)">
                                    <i class="fas fa-eye"></i>
                                    <span class="tooltiptext">Ver detalles</span>
                                </button>
                                
                                <button class="btn-action btn-edit tooltip" onclick="abrirModalFeligres('editar', <?php echo $feligres['id_feligres']; ?>)">
                                    <i class="fas fa-edit"></i>
                                    <span class="tooltiptext">Editar</span>
                                </button>
                                <button class="btn-action btn-delete tooltip" onclick="eliminarFeligres(<?php echo $feligres['id_feligres']; ?>)">
                                    <i class="fas fa-trash"></i>
                                    <span class="tooltiptext">Eliminar</span>
                                </button>
                               <!--  <button class="btn-action tooltip" onclick="imprimirFeligres(<?php echo $feligres['id_feligres']; ?>)" style="background-color: #34495e;">
                                    <i class="fas fa-print"></i>
                                    <span class="tooltiptext">Imprimir Registro</span>
                                </button> -->
                               <!--  <button class="btn-action tooltip" onclick="imprimirBautismoFeligres(<?php echo $feligres['id_feligres']; ?>)" style="background-color: #f39c12;">
                                    <i class="fas fa-certificate"></i>
                                    <span class="tooltiptext">Certificado Bautismo</span>
                                </button> -->
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if(empty($feligreses)): ?>
                    <tr>
                        <td colspan="6" style="padding: 2rem; text-align: center; color: #7f8c8d;">
                            <i class="fas fa-search" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                            <h3>No se encontraron resultados</h3>
                            <p><?php echo !empty($busqueda) ? "No hay feligreses que coincidan con \"$busqueda\"" : "No hay feligreses registrados"; ?></p>
                            <?php if (!empty($busqueda)): ?>
                            <button onclick="limpiarBusquedaFeligreses()" class="btn" style="background: #3498db; color: white; border: none; padding: 0.5rem 1rem; border-radius: 5px; cursor: pointer; margin-top: 0.5rem;">
                                <i class="fas fa-times"></i> Limpiar búsqueda
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

       
        
<!-- =========================== Modal para ver Feligres ================ -->
<div id="modalVerFeligres" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-header">
            <h3   ><i class="fas fa-user-shield "></i> Detalle del Feligres</h3>
            <button onclick="cerrarModalFeligres()">×</button>
        </div>

        <div class="modal-body" id="contenidoFeligres">
            <!-- Contenido dinámico -->
        </div>

        <div class="modal-footer">
            <button onclick="cerrarModalFeligres()" class="btn-cerrar">
                Cerrar
            </button>
        </div>
    </div>
</div>
 