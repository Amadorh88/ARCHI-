<!-- Modal de confirmación de cierre -->
<div id="logoutModal" class="modal logout-modal" style="display: none;">
    <div class="modal-content logout-modal-content">
        <div class="modal-header">
            <h3>
                <span class="tooltip">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                    <span class="tooltiptext">Cerrar Sesión</span>
                </span>
            </h3>
        </div>
        <p>¿Está seguro de que desea cerrar sesión?</p>
        <p class="logout-warning">Será redirigido a la página de inicio de sesión.</p>
        <div class="modal-actions">
            <button class="btn-cancel" onclick="closeLogoutModal()">
                <span class="tooltip">
                    <i class="fas fa-times"></i> Cancelar
                    <span class="tooltiptext">Cancelar cierre de sesión</span>
                </span>
            </button>
            <a href="logout.php" class="btn-confirm">
                <span class="tooltip">
                    <i class="fas fa-sign-out-alt"></i> Sí, Cerrar Sesión
                    <span class="tooltiptext">Confirmar cierre de sesión</span>
                </span>
            </a>
        </div>
    </div>
</div>
<!-- Modal para Feligreses -->
        <div id="modalFeligres" class="modal" style="display: none;">
            <div class="modal-content" style="max-width: 600px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h3 id="modalFeligresTitulo">Nuevo Feligrés</h3>
                    <button onclick="cerrarModalFeligres()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #7f8c8d;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form id="formFeligres" onsubmit="guardarFeligres(event)">
                    <input type="hidden" id="feligresId" name="id">
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Cédula *</label>
                            <input type="text" id="cedula" name="cedula" required 
                                   style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px;"
                                   placeholder="Ej: 001-1234567-8">
                        </div>
                        
                        <div>
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Estado</label>
                            <select id="estado" name="estado" style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px;">
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Nombres *</label>
                            <input type="text" id="nombres" name="nombres" required 
                                   style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px;"
                                   placeholder="Nombres del feligrés">
                        </div>
                        
                        <div>
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Apellidos *</label>
                            <input type="text" id="apellidos" name="apellidos" required 
                                   style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px;"
                                   placeholder="Apellidos del feligrés">
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Fecha de Nacimiento</label>
                        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" 
                               style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px;">
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Teléfono</label>
                            <input type="tel" id="telefono" name="telefono" 
                                   style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px;"
                                   placeholder="Ej: 809-123-4567">
                        </div>
                        
                        <div>
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Email</label>
                            <input type="email" id="email" name="email" 
                                   style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px;"
                                   placeholder="correo@ejemplo.com">
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Dirección</label>
                        <textarea id="direccion" name="direccion" rows="3"
                                  style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px;"
                                  placeholder="Dirección completa"></textarea>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Estado Civil</label>
                        <select id="estado_civil" name="estado_civil" style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px;">
                            <option value="">Seleccionar</option>
                            <option value="soltero">Soltero</option>
                            <option value="casado">Casado</option>
                            <option value="divorciado">Divorciado</option>
                            <option value="viudo">Viudo</option>
                        </select>
                    </div>
                    
                    <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                        <button type="button" onclick="cerrarModalFeligres()" 
                                style="padding: 0.75rem 1.5rem; border: 1px solid #ddd; background: white; border-radius: 5px; cursor: pointer;">
                            Cancelar
                        </button>
                        <button type="submit" 
                                style="padding: 0.75rem 1.5rem; background: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 600;">
                            <i class="fas fa-save"></i> Guardar Feligrés
                        </button>
                    </div>
                      </form>
            </div>
        </div>