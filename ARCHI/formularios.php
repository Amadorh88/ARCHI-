<!-- [file name]: formularios.php -->
<?php
// Función para generar botones según permisos
function generarBotonesAccion($tipo, $id, $estado = 'activo', $extraData = null) {
    $botones = '';
    
    // Botón Ver
    if (tienePermiso('ver')) {
        $botones .= '<button class="btn-action btn-view tooltip" onclick="ver' . ucfirst($tipo) . '(' . $id . ')" ' . 
                   'title="Ver detalles"><i class="fas fa-eye"></i></button>';
    }
    
    // Botón Editar
    if (tienePermiso('editar')) {
        $botones .= '<button class="btn-action btn-edit tooltip" onclick="abrirModal' . ucfirst($tipo) . '(\'editar\', ' . $id . ')" ' .
                   'title="Editar"><i class="fas fa-edit"></i></button>';
    }
    
    // Botón Eliminar (sólo para activos)
    if (tienePermiso('eliminar') && $estado === 'activo') {
        $botones .= '<button class="btn-action btn-delete tooltip" onclick="eliminar' . ucfirst($tipo) . '(' . $id . ')" ' .
                   'title="Eliminar"><i class="fas fa-trash"></i></button>';
    }
    
    // Botón Imprimir
    if (tienePermiso('imprimir')) {
        $botones .= '<button class="btn-action btn-print tooltip" onclick="imprimir' . ucfirst($tipo) . '(' . $id . ')" ' .
                   'style="background-color: #9b59b6;" title="Imprimir"><i class="fas fa-print"></i></button>';
    }
    
    // Botones específicos por tipo
    switch($tipo) {
        case 'usuario':
            if (tienePermiso('editar')) {
                $estadoBtn = $estado === 'activo' ? 
                    '<button class="btn-action tooltip" onclick="cambiarEstadoUsuario(' . $id . ', 0)" ' .
                    'style="background-color: #f39c12;" title="Desactivar"><i class="fas fa-toggle-off"></i></button>' :
                    '<button class="btn-action tooltip" onclick="cambiarEstadoUsuario(' . $id . ', 1)" ' .
                    'style="background-color: #2ecc71;" title="Activar"><i class="fas fa-toggle-on"></i></button>';
                $botones .= $estadoBtn;
                
                $botones .= '<button class="btn-action tooltip" onclick="resetearPassword(' . $id . ')" ' .
                           'style="background-color: #34495e;" title="Resetear contraseña"><i class="fas fa-key"></i></button>';
            }
            break;
            
        case 'sacramento':
            if (tienePermiso('aprobar')) {
                $botones .= '<button class="btn-action tooltip" onclick="aprobarSacramento(' . $id . ')" ' .
                           'style="background-color: #27ae60;" title="Aprobar"><i class="fas fa-check"></i></button>';
            }
            break;
            
        case 'pago':
            if (tienePermiso('editar')) {
                $botones .= '<button class="btn-action tooltip" onclick="registrarPago(' . $id . ')" ' .
                           'style="background-color: #27ae60;" title="Registrar pago"><i class="fas fa-dollar-sign"></i></button>';
            }
            break;
    }
    
    return '<div class="action-buttons">' . $botones . '</div>';
}

// ==================== FORMULARIOS DE FELIGRESES ====================
function getFeligresForm($action = 'crear', $data = null) {
    // Si no tiene permiso, mostrar mensaje
    if (!tienePermiso($action === 'crear' ? 'crear' : 'editar')) {
        return '<div class="no-permission">
            <h3><i class="fas fa-ban"></i> Acceso Denegado</h3>
            <p>No tiene permisos para ' . ($action === 'crear' ? 'crear' : 'editar') . ' feligreses.</p>
        </div>';
    }
    
    // Preparar datos
    $id = $data['id'] ?? '';
    $nombres = htmlspecialchars($data['nombres'] ?? '');
    $apellidos = htmlspecialchars($data['apellidos'] ?? '');
    $dni = htmlspecialchars($data['dni'] ?? '');
    $fecha_nacimiento = $data['fecha_nacimiento'] ?? '';
    $lugar_nacimiento = htmlspecialchars($data['lugar_nacimiento'] ?? '');
    $direccion = htmlspecialchars($data['direccion'] ?? '');
    $telefono = htmlspecialchars($data['telefono'] ?? '');
    $email = htmlspecialchars($data['email'] ?? '');
    $estado_civil = htmlspecialchars($data['estado_civil'] ?? 'soltero');
    $padre = htmlspecialchars($data['padre'] ?? '');
    $madre = htmlspecialchars($data['madre'] ?? '');
    $padrino_bautismo = htmlspecialchars($data['padrino_bautismo'] ?? '');
    
    $formTitle = $action === 'crear' ? 'Nuevo Feligrés' : 'Editar Feligrés';
    $submitText = $action === 'crear' ? 'Registrar' : 'Actualizar';
    
    ob_start(); ?>
    <div class="crud-form">
        <div class="modal-header">
            <h3><i class="fas fa-user-plus"></i> <?php echo $formTitle; ?></h3>
            <button class="modal-close-btn" onclick="closeCrudModal()">&times;</button>
        </div>
        
        <form id="feligresForm" onsubmit="guardarFeligres(event, '<?php echo $action; ?>')">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            
            <div class="form-section">
                <h4><i class="fas fa-id-card"></i> Datos Personales</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-id-card"></i> DNI/NIE *</label>
                        <input type="text" name="dni" value="<?php echo $dni; ?>" required 
                               pattern="[0-9]{8}[A-Za-z]?" placeholder="12345678A">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-birthday-cake"></i> Fecha Nacimiento *</label>
                        <input type="date" name="fecha_nacimiento" value="<?php echo $fecha_nacimiento; ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Nombres *</label>
                        <input type="text" name="nombres" value="<?php echo $nombres; ?>" required 
                               placeholder="Juan Carlos">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Apellidos *</label>
                        <input type="text" name="apellidos" value="<?php echo $apellidos; ?>" required 
                               placeholder="García López">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-map-marker-alt"></i> Lugar Nacimiento</label>
                        <input type="text" name="lugar_nacimiento" value="<?php echo $lugar_nacimiento; ?>" 
                               placeholder="Ciudad, Provincia">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-heart"></i> Estado Civil</label>
                        <select name="estado_civil">
                            <option value="soltero" <?php echo $estado_civil === 'soltero' ? 'selected' : ''; ?>>Soltero/a</option>
                            <option value="casado" <?php echo $estado_civil === 'casado' ? 'selected' : ''; ?>>Casado/a</option>
                            <option value="divorciado" <?php echo $estado_civil === 'divorciado' ? 'selected' : ''; ?>>Divorciado/a</option>
                            <option value="viudo" <?php echo $estado_civil === 'viudo' ? 'selected' : ''; ?>>Viudo/a</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <h4><i class="fas fa-home"></i> Datos de Contacto</h4>
                <div class="form-group">
                    <label><i class="fas fa-home"></i> Dirección</label>
                    <textarea name="direccion" rows="2" placeholder="Calle, número, piso..."><?php echo $direccion; ?></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-phone"></i> Teléfono</label>
                        <input type="tel" name="telefono" value="<?php echo $telefono; ?>" 
                               pattern="[0-9]{9,15}" placeholder="600123456">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" name="email" value="<?php echo $email; ?>" 
                               placeholder="ejemplo@correo.com">
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <h4><i class="fas fa-users"></i> Datos Familiares</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-male"></i> Nombre del Padre</label>
                        <input type="text" name="padre" value="<?php echo $padre; ?>" 
                               placeholder="Nombre completo del padre">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-female"></i> Nombre de la Madre</label>
                        <input type="text" name="madre" value="<?php echo $madre; ?>" 
                               placeholder="Nombre completo de la madre">
                    </div>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-user-friends"></i> Padrino/Madrina de Bautismo</label>
                    <input type="text" name="padrino_bautismo" value="<?php echo $padrino_bautismo; ?>" 
                           placeholder="Nombre del padrino/madrina">
                </div>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closeCrudModal()">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> <?php echo $submitText; ?> Feligrés
                </button>
            </div>
        </form>
    </div>
    <?php return ob_get_clean();
}

// ==================== FORMULARIOS DE USUARIOS ====================
function getUsuarioForm($action = 'crear', $data = null) {
    if (!tienePermiso($action === 'crear' ? 'crear' : 'editar')) {
        return '<div class="no-permission">
            <h3><i class="fas fa-ban"></i> Acceso Denegado</h3>
            <p>No tiene permisos para ' . ($action === 'crear' ? 'crear' : 'editar') . ' usuarios.</p>
        </div>';
    }
    
    $id = $data['id'] ?? '';
    $nombre = htmlspecialchars($data['nombre'] ?? '');
    $dni = htmlspecialchars($data['dni'] ?? '');
    $usuario = htmlspecialchars($data['usuario'] ?? '');
    $rol = htmlspecialchars($data['rol'] ?? 'secretaria');
    $email = htmlspecialchars($data['email'] ?? '');
    $telefono = htmlspecialchars($data['telefono'] ?? '');
    $parroquia_id = $data['parroquia_id'] ?? '';
    
    ob_start(); ?>
    <div class="crud-form">
        <div class="modal-header">
            <h3><i class="fas fa-user-plus"></i> <?php echo $action === 'crear' ? 'Nuevo Usuario' : 'Editar Usuario'; ?></h3>
            <button class="modal-close-btn" onclick="closeCrudModal()">&times;</button>
        </div>
        
        <form id="usuarioForm" onsubmit="guardarUsuario(event, '<?php echo $action; ?>')">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            
            <div class="form-section">
                <h4><i class="fas fa-id-card"></i> Datos Personales</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-id-card"></i> DNI *</label>
                        <input type="text" name="dni" value="<?php echo $dni; ?>" required 
                               pattern="[0-9]{8}[A-Za-z]?" placeholder="12345678A">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Nombre Completo *</label>
                        <input type="text" name="nombre" value="<?php echo $nombre; ?>" required 
                               placeholder="Nombre Apellidos">
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <h4><i class="fas fa-user-cog"></i> Datos de Cuenta</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-user-tag"></i> Nombre de Usuario *</label>
                        <input type="text" name="usuario" value="<?php echo $usuario; ?>" required 
                               placeholder="usuario123" <?php echo $action === 'editar' ? 'readonly' : ''; ?>>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-user-cog"></i> Rol *</label>
                        <select name="rol" required>
                            <?php if ($_SESSION['rol'] === 'admin'): ?>
                            <option value="admin" <?php echo $rol === 'admin' ? 'selected' : ''; ?>>Administrador</option>
                            <?php endif; ?>
                            <option value="secretaria" <?php echo $rol === 'secretaria' ? 'selected' : ''; ?>>Secretaría</option>
                            <option value="archivista" <?php echo $rol === 'archivista' ? 'selected' : ''; ?>>Archivista</option>
                            <option value="parroco" <?php echo $rol === 'parroco' ? 'selected' : ''; ?>>Párroco</option>
                        </select>
                    </div>
                </div>
                
                <?php if ($action === 'crear'): ?>
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Contraseña *</label>
                        <div class="password-input-container">
                            <input type="password" name="password" required 
                                   oninput="verificarFortalezaPassword(this.value)" 
                                   placeholder="Mínimo 6 caracteres">
                            <button type="button" class="password-toggle" onclick="togglePassword(this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div id="passwordStrength" class="password-strength"></div>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Confirmar Contraseña *</label>
                        <div class="password-input-container">
                            <input type="password" name="confirm_password" required 
                                   placeholder="Repita la contraseña">
                            <button type="button" class="password-toggle" onclick="togglePassword(this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="form-section">
                <h4><i class="fas fa-address-book"></i> Información de Contacto</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" name="email" value="<?php echo $email; ?>" 
                               placeholder="usuario@correo.com">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-phone"></i> Teléfono</label>
                        <input type="tel" name="telefono" value="<?php echo $telefono; ?>" 
                               pattern="[0-9]{9,15}" placeholder="600123456">
                    </div>
                </div>
            </div>
            
            <?php if ($_SESSION['rol'] === 'admin'): ?>
            <div class="form-section">
                <h4><i class="fas fa-church"></i> Parroquia Asignada</h4>
                <div class="form-group">
                    <select name="parroquia_id">
                        <option value="">Seleccione una parroquia</option>
                        <?php
                        global $bd;
                        $stmt = $bd->prepare("SELECT id, nombre FROM parroquias WHERE estado = 'activo' ORDER BY nombre");
                        $stmt->execute();
                        while ($parroquia = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                        <option value="<?php echo $parroquia['id']; ?>" 
                            <?php echo $parroquia_id == $parroquia['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($parroquia['nombre']); ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closeCrudModal()">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> <?php echo $action === 'crear' ? 'Registrar' : 'Actualizar'; ?> Usuario
                </button>
            </div>
        </form>
    </div>
    <?php return ob_get_clean();
}

// ==================== FORMULARIOS DE BAUTISMO ====================
function getBautismoForm($action = 'crear', $data = null) {
    if (!tienePermiso($action === 'crear' ? 'crear' : 'editar')) {
        return '<div class="no-permission">
            <h3><i class="fas fa-ban"></i> Acceso Denegado</h3>
            <p>No tiene permisos para ' . ($action === 'crear' ? 'registrar' : 'editar') . ' bautismos.</p>
        </div>';
    }
    
    global $bd;
    
    $id = $data['id'] ?? '';
    $feligres_id = $data['feligres_id'] ?? '';
    $fecha_bautismo = $data['fecha_bautismo'] ?? date('Y-m-d');
    $libro = $data['libro'] ?? '';
    $folio = $data['folio'] ?? '';
    $numero = $data['numero'] ?? '';
    $sacerdote = htmlspecialchars($data['sacerdote'] ?? '');
    $padrino = htmlspecialchars($data['padrino'] ?? '');
    $madrina = htmlspecialchars($data['madrina'] ?? '');
    $notas = htmlspecialchars($data['notas'] ?? '');
    
    ob_start(); ?>
    <div class="crud-form">
        <div class="modal-header">
            <h3><i class="fas fa-water"></i> <?php echo $action === 'crear' ? 'Registrar Bautismo' : 'Editar Bautismo'; ?></h3>
            <button class="modal-close-btn" onclick="closeCrudModal()">&times;</button>
        </div>
        
        <form id="bautismoForm" onsubmit="guardarBautismo(event, '<?php echo $action; ?>')">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            
            <div class="form-section">
                <h4><i class="fas fa-user"></i> Datos del Bautizado</h4>
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Feligrés *</label>
                    <select name="feligres_id" required onchange="cargarDatosFeligres(this.value)">
                        <option value="">Seleccione un feligrés</option>
                        <?php
                        $stmt = $bd->prepare("SELECT id, CONCAT(nombres, ' ', apellidos) as nombre, dni FROM feligreses WHERE estado = 'activo' ORDER BY nombres");
                        $stmt->execute();
                        while ($feligres = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                        <option value="<?php echo $feligres['id']; ?>" 
                            <?php echo $feligres_id == $feligres['id'] ? 'selected' : ''; ?>
                            data-dni="<?php echo htmlspecialchars($feligres['dni']); ?>">
                            <?php echo htmlspecialchars($feligres['nombre']) . ' (DNI: ' . htmlspecialchars($feligres['dni']) . ')'; ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                    <div id="infoFeligres" style="margin-top: 10px; padding: 10px; background: #f8f9fa; border-radius: 5px; display: none;">
                        <small>Datos del feligrés: <span id="datosFeligres"></span></small>
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <h4><i class="fas fa-calendar-alt"></i> Datos del Sacramento</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-calendar"></i> Fecha de Bautismo *</label>
                        <input type="date" name="fecha_bautismo" value="<?php echo $fecha_bautismo; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-user-tie"></i> Sacerdote Oficiante *</label>
                        <input type="text" name="sacerdote" value="<?php echo $sacerdote; ?>" required 
                               placeholder="Nombre del sacerdote">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-male"></i> Padrino</label>
                        <input type="text" name="padrino" value="<?php echo $padrino; ?>" 
                               placeholder="Nombre completo del padrino">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-female"></i> Madrina</label>
                        <input type="text" name="madrina" value="<?php echo $madrina; ?>" 
                               placeholder="Nombre completo de la madrina">
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <h4><i class="fas fa-book"></i> Registro Eclesiástico</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-book"></i> Libro</label>
                        <input type="text" name="libro" value="<?php echo $libro; ?>" 
                               placeholder="Ej: Libro 5">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-book-open"></i> Folio</label>
                        <input type="text" name="folio" value="<?php echo $folio; ?>" 
                               placeholder="Número de folio">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-hashtag"></i> Número</label>
                        <input type="text" name="numero" value="<?php echo $numero; ?>" 
                               placeholder="Número de acta">
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <h4><i class="fas fa-sticky-note"></i> Observaciones</h4>
                <div class="form-group">
                    <label><i class="fas fa-edit"></i> Notas Adicionales</label>
                    <textarea name="notas" rows="3" placeholder="Observaciones o notas importantes..."><?php echo $notas; ?></textarea>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closeCrudModal()">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> <?php echo $action === 'crear' ? 'Registrar' : 'Actualizar'; ?> Bautismo
                </button>
            </div>
        </form>
    </div>
    <script>
    function cargarDatosFeligres(feligresId) {
        if (!feligresId) {
            document.getElementById('infoFeligres').style.display = 'none';
            return;
        }
        
        fetch(`api/get_feligres.php?id=${feligresId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const f = data.feligres;
                    document.getElementById('datosFeligres').innerHTML = 
                        `Fecha Nacimiento: ${f.fecha_nacimiento ? f.fecha_nacimiento.split('-').reverse().join('/') : 'No registrada'}, ` +
                        `Padre: ${f.padre || 'No registrado'}, Madre: ${f.madre || 'No registrada'}`;
                    document.getElementById('infoFeligres').style.display = 'block';
                    
                    // Autocompletar padrino si existe
                    if (!document.querySelector('input[name="padrino"]').value && f.padrino_bautismo) {
                        document.querySelector('input[name="padrino"]').value = f.padrino_bautismo;
                    }
                }
            });
    }
    
    // Cargar datos si ya hay un feligrés seleccionado
    <?php if ($feligres_id): ?>
    document.addEventListener('DOMContentLoaded', function() {
        cargarDatosFeligres(<?php echo $feligres_id; ?>);
    });
    <?php endif; ?>
    </script>
    <?php return ob_get_clean();
}

// ==================== FORMULARIOS DE MATRIMONIO ====================
function getMatrimonioForm($action = 'crear', $data = null) {
    if (!tienePermiso($action === 'crear' ? 'crear' : 'editar')) {
        return '<div class="no-permission">
            <h3><i class="fas fa-ban"></i> Acceso Denegado</h3>
            <p>No tiene permisos para ' . ($action === 'crear' ? 'registrar' : 'editar') . ' matrimonios.</p>
        </div>';
    }
    
    global $bd;
    
    $id = $data['id'] ?? '';
    $esposo_id = $data['esposo_id'] ?? '';
    $esposa_id = $data['esposa_id'] ?? '';
    $fecha_matrimonio = $data['fecha_matrimonio'] ?? date('Y-m-d');
    $testigos = htmlspecialchars($data['testigos'] ?? '');
    $sacerdote = htmlspecialchars($data['sacerdote'] ?? '');
    $libro = $data['libro'] ?? '';
    $folio = $data['folio'] ?? '';
    $numero = $data['numero'] ?? '';
    
    ob_start(); ?>
    <div class="crud-form">
        <div class="modal-header">
            <h3><i class="fas fa-ring"></i> <?php echo $action === 'crear' ? 'Registrar Matrimonio' : 'Editar Matrimonio'; ?></h3>
            <button class="modal-close-btn" onclick="closeCrudModal()">&times;</button>
        </div>
        
        <form id="matrimonioForm" onsubmit="guardarMatrimonio(event, '<?php echo $action; ?>')">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            
            <div class="form-section">
                <h4><i class="fas fa-user-friends"></i> Datos de los Contrayentes</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-male"></i> Esposo *</label>
                        <select name="esposo_id" required>
                            <option value="">Seleccione el esposo</option>
                            <?php
                            $stmt = $bd->prepare("SELECT id, CONCAT(nombres, ' ', apellidos) as nombre, dni FROM feligreses WHERE estado = 'activo' AND estado_civil IN ('soltero', 'divorciado', 'viudo') ORDER BY nombres");
                            $stmt->execute();
                            while ($feligres = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?php echo $feligres['id']; ?>" 
                                <?php echo $esposo_id == $feligres['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($feligres['nombre']) . ' (DNI: ' . htmlspecialchars($feligres['dni']) . ')'; ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-female"></i> Esposa *</label>
                        <select name="esposa_id" required>
                            <option value="">Seleccione la esposa</option>
                            <?php
                            $stmt = $bd->prepare("SELECT id, CONCAT(nombres, ' ', apellidos) as nombre, dni FROM feligreses WHERE estado = 'activo' AND estado_civil IN ('soltero', 'divorciado', 'viudo') ORDER BY nombres");
                            $stmt->execute();
                            while ($feligres = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?php echo $feligres['id']; ?>" 
                                <?php echo $esposa_id == $feligres['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($feligres['nombre']) . ' (DNI: ' . htmlspecialchars($feligres['dni']) . ')'; ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <h4><i class="fas fa-calendar-alt"></i> Datos del Matrimonio</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-calendar"></i> Fecha de Matrimonio *</label>
                        <input type="date" name="fecha_matrimonio" value="<?php echo $fecha_matrimonio; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-user-tie"></i> Sacerdote Oficiante *</label>
                        <input type="text" name="sacerdote" value="<?php echo $sacerdote; ?>" required 
                               placeholder="Nombre del sacerdote">
                    </div>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-users"></i> Testigos</label>
                    <textarea name="testigos" rows="2" placeholder="Nombres completos de los testigos (separados por coma)"><?php echo $testigos; ?></textarea>
                </div>
            </div>
            
            <div class="form-section">
                <h4><i class="fas fa-book"></i> Registro Eclesiástico</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-book"></i> Libro</label>
                        <input type="text" name="libro" value="<?php echo $libro; ?>" placeholder="Ej: Libro 3">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-book-open"></i> Folio</label>
                        <input type="text" name="folio" value="<?php echo $folio; ?>" placeholder="Número de folio">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-hashtag"></i> Número</label>
                        <input type="text" name="numero" value="<?php echo $numero; ?>" placeholder="Número de acta">
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closeCrudModal()">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> <?php echo $action === 'crear' ? 'Registrar' : 'Actualizar'; ?> Matrimonio
                </button>
            </div>
        </form>
    </div>
    <?php return ob_get_clean();
}

// ==================== FORMULARIOS DE CATEQUISTA ====================
function getCatequistaForm($action = 'crear', $data = null) {
    if (!tienePermiso($action === 'crear' ? 'crear' : 'editar')) {
        return '<div class="no-permission">
            <h3><i class="fas fa-ban"></i> Acceso Denegado</h3>
            <p>No tiene permisos para ' . ($action === 'crear' ? 'registrar' : 'editar') . ' catequistas.</p>
        </div>';
    }
    
    $id = $data['id'] ?? '';
    $nombres = htmlspecialchars($data['nombres'] ?? '');
    $apellidos = htmlspecialchars($data['apellidos'] ?? '');
    $dni = htmlspecialchars($data['dni'] ?? '');
    $telefono = htmlspecialchars($data['telefono'] ?? '');
    $email = htmlspecialchars($data['email'] ?? '');
    $especialidad = htmlspecialchars($data['especialidad'] ?? '');
    $fecha_ingreso = $data['fecha_ingreso'] ?? date('Y-m-d');
    
    ob_start(); ?>
    <div class="crud-form">
        <div class="modal-header">
            <h3><i class="fas fa-chalkboard-teacher"></i> <?php echo $action === 'crear' ? 'Nuevo Catequista' : 'Editar Catequista'; ?></h3>
            <button class="modal-close-btn" onclick="closeCrudModal()">&times;</button>
        </div>
        
        <form id="catequistaForm" onsubmit="guardarCatequista(event, '<?php echo $action; ?>')">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-id-card"></i> DNI *</label>
                    <input type="text" name="dni" value="<?php echo $dni; ?>" required 
                           pattern="[0-9]{8}[A-Za-z]?" placeholder="12345678A">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-calendar"></i> Fecha Ingreso</label>
                    <input type="date" name="fecha_ingreso" value="<?php echo $fecha_ingreso; ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Nombres *</label>
                    <input type="text" name="nombres" value="<?php echo $nombres; ?>" required 
                           placeholder="Juan Carlos">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Apellidos *</label>
                    <input type="text" name="apellidos" value="<?php echo $apellidos; ?>" required 
                           placeholder="García López">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-phone"></i> Teléfono</label>
                    <input type="tel" name="telefono" value="<?php echo $telefono; ?>" 
                           pattern="[0-9]{9,15}" placeholder="600123456">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" name="email" value="<?php echo $email; ?>" 
                           placeholder="catequista@correo.com">
                </div>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-graduation-cap"></i> Especialidad</label>
                <select name="especialidad">
                    <option value="">Seleccione especialidad</option>
                    <option value="prebautismal" <?php echo $especialidad === 'prebautismal' ? 'selected' : ''; ?>>Prebautismal</option>
                    <option value="comunion" <?php echo $especialidad === 'comunion' ? 'selected' : ''; ?>>Comunión</option>
                    <option value="confirmacion" <?php echo $especialidad === 'confirmacion' ? 'selected' : ''; ?>>Confirmación</option>
                    <option value="prematrimonial" <?php echo $especialidad === 'prematrimonial' ? 'selected' : ''; ?>>Prematrimonial</option>
                    <option value="adultos" <?php echo $especialidad === 'adultos' ? 'selected' : ''; ?>>Catequesis de Adultos</option>
                    <option value="juvenil" <?php echo $especialidad === 'juvenil' ? 'selected' : ''; ?>>Catequesis Juvenil</option>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closeCrudModal()">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> <?php echo $action === 'crear' ? 'Registrar' : 'Actualizar'; ?> Catequista
                </button>
            </div>
        </form>
    </div>
    <?php return ob_get_clean();
}

// ==================== FORMULARIOS DE PARROQUIA ====================
function getParroquiaForm($action = 'crear', $data = null) {
    if (!tienePermiso($action === 'crear' ? 'crear' : 'editar')) {
        return '<div class="no-permission">
            <h3><i class="fas fa-ban"></i> Acceso Denegado</h3>
            <p>No tiene permisos para ' . ($action === 'crear' ? 'registrar' : 'editar') . ' parroquias.</p>
        </div>';
    }
    
    $id = $data['id'] ?? '';
    $nombre = htmlspecialchars($data['nombre'] ?? '');
    $direccion = htmlspecialchars($data['direccion'] ?? '');
    $telefono = htmlspecialchars($data['telefono'] ?? '');
    $email = htmlspecialchars($data['email'] ?? '');
    $parroco = htmlspecialchars($data['parroco'] ?? '');
    $diocesis = htmlspecialchars($data['diocesis'] ?? '');
    
    ob_start(); ?>
    <div class="crud-form">
        <div class="modal-header">
            <h3><i class="fas fa-church"></i> <?php echo $action === 'crear' ? 'Nueva Parroquia' : 'Editar Parroquia'; ?></h3>
            <button class="modal-close-btn" onclick="closeCrudModal()">&times;</button>
        </div>
        
        <form id="parroquiaForm" onsubmit="guardarParroquia(event, '<?php echo $action; ?>')">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            
            <div class="form-group">
                <label><i class="fas fa-church"></i> Nombre de la Parroquia *</label>
                <input type="text" name="nombre" value="<?php echo $nombre; ?>" required 
                       placeholder="Ej: Parroquia San Juan Bautista">
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-map-marker-alt"></i> Dirección *</label>
                <textarea name="direccion" rows="2" required placeholder="Calle, número, ciudad, código postal"><?php echo $direccion; ?></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-phone"></i> Teléfono</label>
                    <input type="tel" name="telefono" value="<?php echo $telefono; ?>" 
                           pattern="[0-9]{9,15}" placeholder="900123456">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" name="email" value="<?php echo $email; ?>" 
                           placeholder="parroquia@correo.com">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-user-tie"></i> Párroco Actual</label>
                    <input type="text" name="parroco" value="<?php echo $parroco; ?>" 
                           placeholder="Nombre del párroco">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-globe-americas"></i> Diócesis</label>
                    <input type="text" name="diocesis" value="<?php echo $diocesis; ?>" 
                           placeholder="Nombre de la diócesis">
                </div>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closeCrudModal()">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> <?php echo $action === 'crear' ? 'Registrar' : 'Actualizar'; ?> Parroquia
                </button>
            </div>
        </form>
    </div>
    <?php return ob_get_clean();
}

// ==================== FORMULARIOS DE PAGOS ====================
function getPagoForm($action = 'crear', $data = null) {
    if (!tienePermiso($action === 'crear' ? 'crear' : 'editar')) {
        return '<div class="no-permission">
            <h3><i class="fas fa-ban"></i> Acceso Denegado</h3>
            <p>No tiene permisos para ' . ($action === 'crear' ? 'registrar' : 'editar') . ' pagos.</p>
        </div>';
    }
    
    global $bd;
    
    $id = $data['id'] ?? '';
    $feligres_id = $data['feligres_id'] ?? '';
    $tipo = $data['tipo'] ?? 'donacion';
    $monto = $data['monto'] ?? '';
    $fecha_pago = $data['fecha_pago'] ?? date('Y-m-d');
    $metodo_pago = $data['metodo_pago'] ?? 'efectivo';
    $concepto = htmlspecialchars($data['concepto'] ?? '');
    $observaciones = htmlspecialchars($data['observaciones'] ?? '');
    
    ob_start(); ?>
    <div class="crud-form">
        <div class="modal-header">
            <h3><i class="fas fa-money-bill-wave"></i> <?php echo $action === 'crear' ? 'Nuevo Pago' : 'Editar Pago'; ?></h3>
            <button class="modal-close-btn" onclick="closeCrudModal()">&times;</button>
        </div>
        
        <form id="pagoForm" onsubmit="guardarPago(event, '<?php echo $action; ?>')">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            
            <div class="form-section">
                <h4><i class="fas fa-user"></i> Datos del Pagador</h4>
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Feligrés</label>
                    <select name="feligres_id">
                        <option value="">Donación anónima</option>
                        <?php
                        $stmt = $bd->prepare("SELECT id, CONCAT(nombres, ' ', apellidos) as nombre, dni FROM feligreses WHERE estado = 'activo' ORDER BY nombres");
                        $stmt->execute();
                        while ($feligres = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                        <option value="<?php echo $feligres['id']; ?>" 
                            <?php echo $feligres_id == $feligres['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($feligres['nombre']) . ' (DNI: ' . htmlspecialchars($feligres['dni']) . ')'; ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-section">
                <h4><i class="fas fa-dollar-sign"></i> Datos del Pago</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-tag"></i> Tipo de Pago *</label>
                        <select name="tipo" required>
                            <option value="donacion" <?php echo $tipo === 'donacion' ? 'selected' : ''; ?>>Donación</option>
                            <option value="bautismo" <?php echo $tipo === 'bautismo' ? 'selected' : ''; ?>>Bautismo</option>
                            <option value="matrimonio" <?php echo $tipo === 'matrimonio' ? 'selected' : ''; ?>>Matrimonio</option>
                            <option value="comunion" <?php echo $tipo === 'comunion' ? 'selected' : ''; ?>>Comunión</option>
                            <option value="confirmacion" <?php echo $tipo === 'confirmacion' ? 'selected' : ''; ?>>Confirmación</option>
                            <option value="catequesis" <?php echo $tipo === 'catequesis' ? 'selected' : ''; ?>>Catequesis</option>
                            <option value="otros" <?php echo $tipo === 'otros' ? 'selected' : ''; ?>>Otros</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-money-bill"></i> Monto (€) *</label>
                        <input type="number" name="monto" value="<?php echo $monto; ?>" required 
                               step="0.01" min="0" placeholder="0.00">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-calendar"></i> Fecha de Pago *</label>
                        <input type="date" name="fecha_pago" value="<?php echo $fecha_pago; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-credit-card"></i> Método de Pago</label>
                        <select name="metodo_pago">
                            <option value="efectivo" <?php echo $metodo_pago === 'efectivo' ? 'selected' : ''; ?>>Efectivo</option>
                            <option value="transferencia" <?php echo $metodo_pago === 'transferencia' ? 'selected' : ''; ?>>Transferencia</option>
                            <option value="tarjeta" <?php echo $metodo_pago === 'tarjeta' ? 'selected' : ''; ?>>Tarjeta</option>
                            <option value="cheque" <?php echo $metodo_pago === 'cheque' ? 'selected' : ''; ?>>Cheque</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <h4><i class="fas fa-file-invoice"></i> Información Adicional</h4>
                <div class="form-group">
                    <label><i class="fas fa-edit"></i> Concepto</label>
                    <input type="text" name="concepto" value="<?php echo $concepto; ?>" 
                           placeholder="Ej: Donación mensual, Pago bautismo...">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-sticky-note"></i> Observaciones</label>
                    <textarea name="observaciones" rows="3" placeholder="Observaciones adicionales..."><?php echo $observaciones; ?></textarea>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closeCrudModal()">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> <?php echo $action === 'crear' ? 'Registrar' : 'Actualizar'; ?> Pago
                </button>
            </div>
        </form>
    </div>
    <?php return ob_get_clean();
}

// ==================== VISTAS DETALLADAS ====================
function getFeligresView($data) {
    if (!tienePermiso('ver')) {
        return '<div class="no-permission">
            <h3><i class="fas fa-ban"></i> Acceso Denegado</h3>
            <p>No tiene permisos para ver detalles de feligreses.</p>
        </div>';
    }
    
    ob_start(); ?>
    <div class="view-form">
        <div class="modal-header">
            <h3><i class="fas fa-eye"></i> Detalles del Feligrés</h3>
            <button class="modal-close-btn" onclick="closeCrudModal()">&times;</button>
        </div>
        
        <div class="view-grid">
            <div class="view-section">
                <h4><i class="fas fa-id-card"></i> Información Personal</h4>
                <div class="view-row">
                    <span class="view-label">DNI/NIE:</span>
                    <span class="view-value"><?php echo htmlspecialchars($data['dni']); ?></span>
                </div>
                <div class="view-row">
                    <span class="view-label">Nombre Completo:</span>
                    <span class="view-value"><?php echo htmlspecialchars($data['nombres'] . ' ' . $data['apellidos']); ?></span>
                </div>
                <div class="view-row">
                    <span class="view-label">Fecha Nacimiento:</span>
                    <span class="view-value"><?php echo formatDate($data['fecha_nacimiento']); ?></span>
                </div>
                <div class="view-row">
                    <span class="view-label">Lugar Nacimiento:</span>
                    <span class="view-value"><?php echo htmlspecialchars($data['lugar_nacimiento']); ?></span>
                </div>
                <div class="view-row">
                    <span class="view-label">Estado Civil:</span>
                    <span class="view-value"><?php echo ucfirst($data['estado_civil']); ?></span>
                </div>
            </div>
            
            <div class="view-section">
                <h4><i class="fas fa-home"></i> Información de Contacto</h4>
                <div class="view-row">
                    <span class="view-label">Dirección:</span>
                    <span class="view-value"><?php echo htmlspecialchars($data['direccion']); ?></span>
                </div>
                <div class="view-row">
                    <span class="view-label">Teléfono:</span>
                    <span class="view-value"><?php echo htmlspecialchars($data['telefono']); ?></span>
                </div>
                <div class="view-row">
                    <span class="view-label">Email:</span>
                    <span class="view-value"><?php echo htmlspecialchars($data['email']); ?></span>
                </div>
            </div>
            
            <div class="view-section">
                <h4><i class="fas fa-users"></i> Datos Familiares</h4>
                <div class="view-row">
                    <span class="view-label">Padre:</span>
                    <span class="view-value"><?php echo htmlspecialchars($data['padre']); ?></span>
                </div>
                <div class="view-row">
                    <span class="view-label">Madre:</span>
                    <span class="view-value"><?php echo htmlspecialchars($data['madre']); ?></span>
                </div>
                <div class="view-row">
                    <span class="view-label">Padrino Bautismo:</span>
                    <span class="view-value"><?php echo htmlspecialchars($data['padrino_bautismo']); ?></span>
                </div>
            </div>
            
            <div class="view-section">
                <h4><i class="fas fa-info-circle"></i> Información del Registro</h4>
                <div class="view-row">
                    <span class="view-label">Estado:</span>
                    <span class="view-value <?php echo $data['estado'] === 'activo' ? 'status-active' : 'status-inactive'; ?>">
                        <?php echo ucfirst($data['estado']); ?>
                    </span>
                </div>
                <div class="view-row">
                    <span class="view-label">Fecha Registro:</span>
                    <span class="view-value"><?php echo formatDate($data['fecha_registro']); ?></span>
                </div>
                <?php if ($data['ultima_actualizacion']): ?>
                <div class="view-row">
                    <span class="view-label">Última Actualización:</span>
                    <span class="view-value"><?php echo formatDate($data['ultima_actualizacion']); ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="view-actions">
            <?php if (tienePermiso('editar')): ?>
            <button class="btn-primary" onclick="abrirFeligresForm('editar', <?php echo $data['id']; ?>)">
                <i class="fas fa-edit"></i> Editar
            </button>
            <?php endif; ?>
            
            <?php if (tienePermiso('imprimir')): ?>
            <button class="btn-print" onclick="imprimirFichaFeligres(<?php echo $data['id']; ?>)" style="background-color: #9b59b6;">
                <i class="fas fa-print"></i> Imprimir Ficha
            </button>
            <?php endif; ?>
            
            <button class="btn-cancel" onclick="closeCrudModal()">
                <i class="fas fa-times"></i> Cerrar
            </button>
        </div>
    </div>
    <?php return ob_get_clean();
}
?>