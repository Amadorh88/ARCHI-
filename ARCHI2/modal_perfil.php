<!-- Modal de Editar Perfil -->
<div id="modalPerfil" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 800px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #eee;">
            <h3 style="margin: 0;">
                <span class="tooltip">
                    <i class="fas fa-user-edit"></i> Editar Mi Perfil
                    <span class="tooltiptext">Editar información personal y contraseña</span>
                </span>
            </h3>
            <button onclick="cerrarModalPerfil()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #7f8c8d; padding: 0; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="formPerfil" onsubmit="actualizarPerfil(event)">
            <input type="hidden" id="perfilId" name="id" value="<?php echo isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : ''; ?>">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                <!-- Columna Izquierda -->
                <div>
                    <h4 style="color: #2c3e50; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid #3498db;">
                        <i class="fas fa-id-card"></i> Información Personal
                    </h4>
                    
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #34495e;">
                            <i class="fas fa-user"></i> Nombre de Usuario *
                        </label>
                        <input type="text" id="perfilUsername" name="username" required 
                               style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px; transition: border-color 0.3s;"
                               placeholder="Ej: juan.perez"
                               onfocus="this.style.borderColor='#3498db';" 
                               onblur="this.style.borderColor='#ddd';">
                        <small style="display: block; margin-top: 0.25rem; color: #7f8c8d; font-size: 0.85rem;">
                            Nombre que usarás para iniciar sesión
                        </small>
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #34495e;">
                            <i class="fas fa-user-tag"></i> Nombre Completo *
                        </label>
                        <input type="text" id="perfilNombre" name="nombre_completo" required 
                               style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px; transition: border-color 0.3s;"
                               placeholder="Ej: Juan Pérez Rodríguez"
                               onfocus="this.style.borderColor='#3498db';" 
                               onblur="this.style.borderColor='#ddd';">
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #34495e;">
                            <i class="fas fa-shield-alt"></i> Rol
                        </label>
                        <input type="text" value="<?php echo isset($_SESSION['rol']) ? htmlspecialchars(getRoleDisplayName($_SESSION['rol'])) : ''; ?>" 
                               style="width: 100%; padding: 0.75rem; border: 1px solid #e0e0e0; border-radius: 5px; background-color: #f8f9fa; color: #666;" 
                               readonly>
                        <small style="display: block; margin-top: 0.25rem; color: #7f8c8d; font-size: 0.85rem;">
                            El rol no se puede cambiar desde aquí
                        </small>
                    </div>
                </div>
                
                <!-- Columna Derecha -->
                <div>
                    <h4 style="color: #2c3e50; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid #e74c3c;">
                        <i class="fas fa-key"></i> Cambiar Contraseña
                    </h4>
                    
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #34495e;">
                            <i class="fas fa-lock"></i> Contraseña Actual
                        </label>
                        <input type="password" id="passwordActual" name="password_actual" 
                               style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px; transition: border-color 0.3s;"
                               placeholder="Solo si deseas cambiar la contraseña"
                               onfocus="this.style.borderColor='#3498db';" 
                               onblur="this.style.borderColor='#ddd';">
                        <small style="display: block; margin-top: 0.25rem; color: #7f8c8d; font-size: 0.85rem;">
                            Requerido solo si cambias la contraseña
                        </small>
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #34495e;">
                            <i class="fas fa-lock"></i> Nueva Contraseña
                        </label>
                        <input type="password" id="nuevaPassword" name="nueva_password" 
                               style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px; transition: border-color 0.3s;"
                               placeholder="Mínimo 6 caracteres"
                               onfocus="this.style.borderColor='#3498db';" 
                               onblur="this.style.borderColor='#ddd';">
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #34495e;">
                            <i class="fas fa-lock"></i> Confirmar Nueva Contraseña
                        </label>
                        <input type="password" id="confirmarPassword" 
                               style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px; transition: border-color 0.3s;"
                               placeholder="Repite la nueva contraseña"
                               onfocus="this.style.borderColor='#3498db';" 
                               onblur="this.style.borderColor='#ddd';">
                        <small style="display: block; margin-top: 0.25rem; color: #7f8c8d; font-size: 0.85rem;">
                            Debe coincidir con la nueva contraseña
                        </small>
                    </div>
                    
                    <div style="background-color: #f8f9fa; padding: 1rem; border-radius: 5px; border-left: 4px solid #3498db; margin-top: 1rem;">
                        <p style="margin: 0; color: #2c3e50; font-size: 0.9rem;">
                            <i class="fas fa-info-circle"></i> 
                            <strong>Nota:</strong> Deja los campos de contraseña vacíos si no deseas cambiarla.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Mensaje de error/success -->
            <div id="perfilMensaje" style="display: none; margin-bottom: 1rem; padding: 1rem; border-radius: 5px;"></div>
            
            <!-- Botones de acción -->
            <div style="display: flex; gap: 1rem; justify-content: flex-end; padding-top: 1.5rem; border-top: 1px solid #eee;">
                <button type="button" onclick="cerrarModalPerfil()" 
                        style="padding: 0.75rem 2rem; border: 1px solid #ddd; background: white; border-radius: 5px; cursor: pointer; font-weight: 600; color: #7f8c8d; transition: all 0.3s;"
                        onmouseover="this.style.backgroundColor='#f8f9fa'; this.style.borderColor='#bbb';"
                        onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#ddd';">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="submit" id="btnActualizarPerfil"
                        style="padding: 0.75rem 2rem; background: linear-gradient(135deg, #3498db, #2980b9); color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 600; transition: all 0.3s;"
                        onmouseover="this.style.background='linear-gradient(135deg, #2980b9, #3498db)'; this.style.transform='translateY(-2px)';"
                        onmouseout="this.style.background='linear-gradient(135deg, #3498db, #2980b9)'; this.style.transform='translateY(0)';">
                    <i class="fas fa-save"></i> Actualizar Perfil
                </button>
            </div>
        </form>
    </div>
</div>