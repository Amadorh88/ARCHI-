<!-- [file name]: modals.php -->
<div class="modals-container">
    <!-- Modal Perfil de Usuario -->
    <div id="profileModal" class="modal">
        <div class="modal-content profile-modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-user-circle"></i> Mi Perfil</h3>
                <button class="modal-close-btn" onclick="cerrarModalPerfil()">&times;</button>
            </div>
            <div class="profile-header">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <h4><?php echo htmlspecialchars($_SESSION['nombre_completo'] ?? 'Usuario'); ?></h4>
                <p><?php echo htmlspecialchars($_SESSION['usuario'] ?? ''); ?></p>
            </div>
            
            <form id="profileForm" onsubmit="actualizarPerfil(event)">
                <div class="form-group">
                    <label><i class="fas fa-id-card"></i> Nombre Completo</label>
                    <input type="text" name="nombre_completo" 
                           value="<?php echo htmlspecialchars($_SESSION['nombre_completo'] ?? ''); ?>" 
                           required>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Correo Electrónico</label>
                    <input type="email" name="email" 
                           value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-phone"></i> Teléfono</label>
                    <input type="tel" name="telefono" 
                           value="<?php echo htmlspecialchars($_SESSION['telefono'] ?? ''); ?>">
                </div>
                
                <div class="password-section">
                    <h4><i class="fas fa-key"></i> Cambiar Contraseña</h4>
                    <p class="password-info">Dejar en blanco si no desea cambiar la contraseña</p>
                    
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Contraseña Actual</label>
                        <div class="password-input-container">
                            <input type="password" id="passwordActual" name="password_actual">
                            <button type="button" class="password-toggle" 
                                    onclick="togglePassword('passwordActual')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Nueva Contraseña</label>
                        <div class="password-input-container">
                            <input type="password" id="nuevaPassword" name="nueva_password" 
                                   oninput="verificarFortalezaPassword(this.value)">
                            <button type="button" class="password-toggle" 
                                    onclick="togglePassword('nuevaPassword')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div id="passwordStrength" class="password-strength"></div>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Confirmar Nueva Contraseña</label>
                        <div class="password-input-container">
                            <input type="password" id="confirmarPassword" name="confirmar_password">
                            <button type="button" class="password-toggle" 
                                    onclick="togglePassword('confirmarPassword')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div id="passwordMatch" class="password-match"></div>
                    </div>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="cerrarModalPerfil()">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Cerrar Sesión -->
    <div id="logoutModal" class="modal">
        <div class="modal-content logout-modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</h3>
                <button class="modal-close-btn" onclick="closeLogoutModal()">&times;</button>
            </div>
            <div style="text-align: center; padding: 2rem 0;">
                <span class="tooltip">
                    <i class="fas fa-question-circle" style="font-size: 3rem; color: #f39c12;"></i>
                    <span class="tooltiptext">Confirmación de cierre de sesión</span>
                </span>
                <h3 style="margin: 1rem 0;">¿Está seguro de cerrar sesión?</h3>
                <p style="color: #7f8c8d;">Será redirigido a la página de inicio de sesión</p>
                <p class="logout-warning">
                    <i class="fas fa-exclamation-triangle"></i> Asegúrese de guardar cualquier trabajo pendiente
                </p>
            </div>
            <div class="modal-buttons">
                <button class="btn-cancel" onclick="closeLogoutModal()">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <a href="logout.php" class="btn-confirm">
                    <i class="fas fa-sign-out-alt"></i> Sí, Cerrar Sesión
                </a>
            </div>
        </div>
    </div>

    <!-- Modal para CRUD General -->
   <div class="modals-container">
    <div id="crudModal" class="modal">
        <div class="modal-content" id="crudModalContent">
            </div>
    </div>

    <div id="confirmModal" class="modal">
        <div class="modal-content logout-modal-content">
            <div class="modal-header">
                <h3 id="confirmModalTitle"><i class="fas fa-exclamation-triangle"></i> Confirmar</h3>
                <button class="modal-close-btn" onclick="closeConfirmModal()">&times;</button>
            </div>
            <div style="text-align: center; padding: 2rem 0;">
                <i class="fas fa-trash-alt" style="font-size: 3rem; color: #e74c3c;"></i>
                <h3 id="confirmMessage" style="margin: 1rem 0;"></h3>
                <p id="confirmDetails" style="color: #7f8c8d;"></p>
            </div>
            <div class="modal-buttons">
                <button class="btn-cancel" onclick="closeConfirmModal()">Cancelar</button>
                <button id="confirmActionBtn" class="btn-confirm" style="background: #e74c3c;">
                    <i class="fas fa-check"></i> Confirmar Eliminación
                </button>
            </div>
        </div>
    </div>
</div>

    <!-- Modal Confirmación -->
    <div id="confirmModal" class="modal">
        <div class="modal-content logout-modal-content">
            <div class="modal-header">
                <h3 id="confirmModalTitle"><i class="fas fa-exclamation-triangle"></i> Confirmar</h3>
                <button class="modal-close-btn" onclick="closeConfirmModal()">&times;</button>
            </div>
            <div style="text-align: center; padding: 2rem 0;">
                <span class="tooltip">
                    <i class="fas fa-question-circle" style="font-size: 3rem; color: #f39c12;"></i>
                    <span class="tooltiptext">Confirmación de acción</span>
                </span>
                <h3 id="confirmMessage" style="margin: 1rem 0;"></h3>
                <p id="confirmDetails" style="color: #7f8c8d;"></p>
            </div>
            <div class="modal-buttons">
                <button class="btn-cancel" onclick="closeConfirmModal()">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button id="confirmActionBtn" class="btn-confirm" onclick="executeConfirmedAction()">
                    <i class="fas fa-check"></i> Confirmar
                </button>
            </div>
        </div>
    </div>
</div>