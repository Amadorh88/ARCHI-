<!-- [file name]: formularios.php -->
<?php
function getFeligresForm($action = 'crear', $data = null) {
    $id = $data['id'] ?? '';
    $nombres = htmlspecialchars($data['nombres'] ?? '');
    $apellidos = htmlspecialchars($data['apellidos'] ?? '');
    $dni = htmlspecialchars($data['dni'] ?? '');
    $fecha_nacimiento = $data['fecha_nacimiento'] ?? '';
    $lugar_nacimiento = htmlspecialchars($data['lugar_nacimiento'] ?? '');
    $direccion = htmlspecialchars($data['direccion'] ?? '');
    $telefono = htmlspecialchars($data['telefono'] ?? '');
    $email = htmlspecialchars($data['email'] ?? '');
    $estado_civil = htmlspecialchars($data['estado_civil'] ?? '');
    $fecha_bautismo = $data['fecha_bautismo'] ?? '';
    $parroquia_bautismo = htmlspecialchars($data['parroquia_bautismo'] ?? '');
    
    $formTitle = $action === 'crear' ? 'Nuevo Feligrés' : 'Editar Feligrés';
    $submitText = $action === 'crear' ? 'Registrar' : 'Actualizar';
    
    return '
    <div class="crud-form">
        <div class="modal-header">
            <h3><i class="fas fa-user-plus"></i> ' . $formTitle . '</h3>
            <button class="modal-close-btn" onclick="closeCrudModal()">&times;</button>
        </div>
        
        <form id="feligresForm" onsubmit="guardarFeligres(event, \'' . $action . '\')">
            <input type="hidden" name="id" value="' . $id . '">
            
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-id-card"></i> DNI/NIE</label>
                    <input type="text" name="dni" value="' . $dni . '" required 
                           pattern="[0-9]{8}[A-Za-z]?" 
                           title="Formato: 12345678A">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Nombres</label>
                    <input type="text" name="nombres" value="' . $nombres . '" required>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Apellidos</label>
                    <input type="text" name="apellidos" value="' . $apellidos . '" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-birthday-cake"></i> Fecha de Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" value="' . $fecha_nacimiento . '" required>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-map-marker-alt"></i> Lugar de Nacimiento</label>
                    <input type="text" name="lugar_nacimiento" value="' . $lugar_nacimiento . '">
                </div>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-home"></i> Dirección</label>
                <textarea name="direccion" rows="2">' . $direccion . '</textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-phone"></i> Teléfono</label>
                    <input type="tel" name="telefono" value="' . $telefono . '" 
                           pattern="[0-9]{9,15}">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" name="email" value="' . $email . '">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-heart"></i> Estado Civil</label>
                    <select name="estado_civil">
                        <option value="soltero"' . ($estado_civil === 'soltero' ? ' selected' : '') . '>Soltero</option>
                        <option value="casado"' . ($estado_civil === 'casado' ? ' selected' : '') . '>Casado</option>
                        <option value="divorciado"' . ($estado_civil === 'divorciado' ? ' selected' : '') . '>Divorciado</option>
                        <option value="viudo"' . ($estado_civil === 'viudo' ? ' selected' : '') . '>Viudo</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-church"></i> Fecha de Bautismo</label>
                    <input type="date" name="fecha_bautismo" value="' . $fecha_bautismo . '">
                </div>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-church"></i> Parroquia de Bautismo</label>
                <input type="text" name="parroquia_bautismo" value="' . $parroquia_bautismo . '">
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closeCrudModal()">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> ' . $submitText . ' Feligrés
                </button>
            </div>
        </form>
    </div>';
}

function getFeligresView($data) {
    return '
    <div class="view-form">
        <div class="modal-header">
            <h3><i class="fas fa-eye"></i> Detalles del Feligrés</h3>
            <button class="modal-close-btn" onclick="closeCrudModal()">&times;</button>
        </div>
        
        <div class="view-grid">
            <div class="view-section">
                <h4><i class="fas fa-user"></i> Información Personal</h4>
                <div class="view-row">
                    <span class="view-label">Nombre Completo:</span>
                    <span class="view-value">' . htmlspecialchars($data['nombres'] . ' ' . $data['apellidos']) . '</span>
                </div>
                <div class="view-row">
                    <span class="view-label">DNI/NIE:</span>
                    <span class="view-value">' . htmlspecialchars($data['dni']) . '</span>
                </div>
                <div class="view-row">
                    <span class="view-label">Fecha Nacimiento:</span>
                    <span class="view-value">' . formatDate($data['fecha_nacimiento']) . '</span>
                </div>
                <div class="view-row">
                    <span class="view-label">Lugar Nacimiento:</span>
                    <span class="view-value">' . htmlspecialchars($data['lugar_nacimiento']) . '</span>
                </div>
                <div class="view-row">
                    <span class="view-label">Estado Civil:</span>
                    <span class="view-value">' . ucfirst($data['estado_civil']) . '</span>
                </div>
            </div>
            
            <div class="view-section">
                <h4><i class="fas fa-address-card"></i> Información de Contacto</h4>
                <div class="view-row">
                    <span class="view-label">Dirección:</span>
                    <span class="view-value">' . htmlspecialchars($data['direccion']) . '</span>
                </div>
                <div class="view-row">
                    <span class="view-label">Teléfono:</span>
                    <span class="view-value">' . htmlspecialchars($data['telefono']) . '</span>
                </div>
                <div class="view-row">
                    <span class="view-label">Email:</span>
                    <span class="view-value">' . htmlspecialchars($data['email']) . '</span>
                </div>
            </div>
            
            <div class="view-section">
                <h4><i class="fas fa-church"></i> Información Sacramental</h4>
                <div class="view-row">
                    <span class="view-label">Fecha Bautismo:</span>
                    <span class="view-value">' . formatDate($data['fecha_bautismo']) . '</span>
                </div>
                <div class="view-row">
                    <span class="view-label">Parroquia Bautismo:</span>
                    <span class="view-value">' . htmlspecialchars($data['parroquia_bautismo']) . '</span>
                </div>
                <div class="view-row">
                    <span class="view-label">Estado:</span>
                    <span class="view-value ' . ($data['estado'] === 'activo' ? 'status-active' : 'status-inactive') . '">
                        ' . ucfirst($data['estado']) . '
                    </span>
                </div>
            </div>
            
            <div class="view-section">
                <h4><i class="fas fa-calendar"></i> Registro</h4>
                <div class="view-row">
                    <span class="view-label">Fecha Registro:</span>
                    <span class="view-value">' . formatDate($data['fecha_registro']) . '</span>
                </div>
                <div class="view-row">
                    <span class="view-label">Última Actualización:</span>
                    <span class="view-value">' . formatDate($data['ultima_actualizacion']) . '</span>
                </div>
            </div>
        </div>
        
        <div class="view-actions">
            <button class="btn-primary" onclick="abrirFeligresForm(\'editar\', ' . $data['id'] . ')">
                <i class="fas fa-edit"></i> Editar
            </button>
            <button class="btn-cancel" onclick="closeCrudModal()">
                <i class="fas fa-times"></i> Cerrar
            </button>
        </div>
    </div>';
}

function getUsuarioForm($action = 'crear', $data = null) {
    $id = $data['id'] ?? '';
    $nombre = htmlspecialchars($data['nombre'] ?? '');
    $dni = htmlspecialchars($data['dni'] ?? '');
    $usuario = htmlspecialchars($data['usuario'] ?? '');
    $rol = htmlspecialchars($data['rol'] ?? 'secretaria');
    $email = htmlspecialchars($data['email'] ?? '');
    $telefono = htmlspecialchars($data['telefono'] ?? '');
    
    $formTitle = $action === 'crear' ? 'Nuevo Usuario' : 'Editar Usuario';
    $submitText = $action === 'crear' ? 'Registrar' : 'Actualizar';
    
    return '
    <div class="crud-form">
        <div class="modal-header">
            <h3><i class="fas fa-user-plus"></i> ' . $formTitle . '</h3>
            <button class="modal-close-btn" onclick="closeCrudModal()">&times;</button>
        </div>
        
        <form id="usuarioForm" onsubmit="guardarUsuario(event, \'' . $action . '\')">
            <input type="hidden" name="id" value="' . $id . '">
            
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-id-card"></i> DNI</label>
                    <input type="text" name="dni" value="' . $dni . '" required 
                           pattern="[0-9]{8}[A-Za-z]?">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Nombre Completo</label>
                    <input type="text" name="nombre" value="' . $nombre . '" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-user-tag"></i> Usuario</label>
                    <input type="text" name="usuario" value="' . $usuario . '" required>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-user-cog"></i> Rol</label>
                    <select name="rol" required>
                        <option value="admin"' . ($rol === 'admin' ? ' selected' : '') . '>Administrador</option>
                        <option value="secretaria"' . ($rol === 'secretaria' ? ' selected' : '') . '>Secretaría</option>
                        <option value="archivista"' . ($rol === 'archivista' ? ' selected' : '') . '>Archivista</option>
                        <option value="parroco"' . ($rol === 'parroco' ? ' selected' : '') . '>Párroco</option>
                    </select>
                </div>
            </div>
            
            ' . ($action === 'crear' ? '
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Contraseña</label>
                    <div class="password-input-container">
                        <input type="password" name="password" required 
                               oninput="verificarFortalezaPassword(this.value)">
                        <button type="button" class="password-toggle" 
                                onclick="togglePassword(this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div id="passwordStrength" class="password-strength"></div>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Confirmar Contraseña</label>
                    <div class="password-input-container">
                        <input type="password" name="confirm_password" required>
                        <button type="button" class="password-toggle" 
                                onclick="togglePassword(this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
            ' : '') . '
            
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" name="email" value="' . $email . '">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-phone"></i> Teléfono</label>
                    <input type="tel" name="telefono" value="' . $telefono . '">
                </div>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closeCrudModal()">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> ' . $submitText . ' Usuario
                </button>
            </div>
        </form>
    </div>';
}

// Puedes agregar funciones similares para otros módulos:
// - getCatequistaForm()
// - getSacramentoForm()
// - getBautismoForm()
// - getMatrimonioForm()
// etc.
?>