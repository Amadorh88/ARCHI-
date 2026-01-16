        // Funcionalidad JavaScript para el menú desplegable
        document.addEventListener('DOMContentLoaded', function() {
            const menuItems = document.querySelectorAll('.menu-item');
            
            menuItems.forEach(item => {
                if (item.querySelector('.fa-chevron-down')) {
                    item.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const submenu = this.nextElementSibling;
                        submenu.classList.toggle('active');
                        
                        const icon = this.querySelector('.fa-chevron-down');
                        icon.classList.toggle('fa-rotate-180');
                    });
                }
            });
            
            // Cargar contenido inicial
            showContent('inicio', 'Inicio');
            
            // Mostrar alerta de bienvenida
            showAlert('Bienvenido al Sistema Parroquial', 'success');
        });

        function showContent(sectionId, sectionName) {
            document.getElementById('pageTitle').textContent = sectionName;
            
            // Mostrar loading
            document.getElementById('mainContent').innerHTML = `
                <div style="text-align: center; padding: 3rem;">
                    <span class="tooltip">
                        <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #3498db;"></i>
                        <span class="tooltiptext">Cargando contenido...</span>
                    </span>
                    <p style="margin-top: 1rem;">Cargando ${sectionName}...</p>
                </div>
            `;
            
            // Cargar contenido via AJAX
            const xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.getElementById('mainContent').innerHTML = xhr.responseText;
                    
                    // Mostrar alerta de carga completada
                    showAlert(`${sectionName} cargado correctamente`, 'success');
                } else if (xhr.readyState === 4) {
                    document.getElementById('mainContent').innerHTML = `
                        <div style="text-align: center; padding: 2rem; color: #e74c3c;">
                            <span class="tooltip">
                                <i class="fas fa-exclamation-triangle" style="font-size: 3rem;"></i>
                                <span class="tooltiptext">Error del sistema</span>
                            </span>
                            <h3>Error al cargar el contenido</h3>
                            <p>Por favor, intente nuevamente.</p>
                        </div>
                    `;
                    showAlert('Error al cargar el contenido', 'error');
                }
            };
            
            xhr.open('GET', `load_content.php?section=${sectionId}`, true);
            xhr.send();
        }

        // Funciones para el perfil de usuario
        function abrirModalPerfil() {
            document.getElementById('profileModal').style.display = 'block';
        }

        function cerrarModalPerfil() {
            document.getElementById('profileModal').style.display = 'none';
            // Limpiar campos de contraseña
            document.getElementById('passwordActual').value = '';
            document.getElementById('nuevaPassword').value = '';
            document.getElementById('confirmarPassword').value = '';
            document.getElementById('passwordStrength').textContent = '';
            document.getElementById('passwordMatch').textContent = '';
        }

        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }

        function verificarFortalezaPassword(password) {
            const strengthElement = document.getElementById('passwordStrength');
            
            if (password.length === 0) {
                strengthElement.textContent = '';
                strengthElement.className = 'password-strength';
                return;
            }
            
            let strength = 0;
            let feedback = '';
            
            // Verificar longitud
            if (password.length >= 8) strength++;
            else feedback = 'Muy corta';
            
            // Verificar mayúsculas
            if (/[A-Z]/.test(password)) strength++;
            else if (!feedback) feedback = 'Agregue mayúsculas';
            
            // Verificar minúsculas
            if (/[a-z]/.test(password)) strength++;
            else if (!feedback) feedback = 'Agregue minúsculas';
            
            // Verificar números
            if (/[0-9]/.test(password)) strength++;
            else if (!feedback) feedback = 'Agregue números';
            
            // Verificar caracteres especiales
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            else if (!feedback) feedback = 'Agregue caracteres especiales';
            
            // Determinar fortaleza
            let strengthText = '';
            let strengthClass = '';
            
            if (strength <= 2) {
                strengthText = 'Débil';
                strengthClass = 'strength-weak';
                feedback = feedback || 'Contraseña débil';
            } else if (strength <= 4) {
                strengthText = 'Media';
                strengthClass = 'strength-medium';
                feedback = feedback || 'Contraseña aceptable';
            } else {
                strengthText = 'Fuerte';
                strengthClass = 'strength-strong';
                feedback = 'Contraseña segura';
            }
            
            strengthElement.textContent = `${strengthText} - ${feedback}`;
            strengthElement.className = `password-strength ${strengthClass}`;
        }

        // Verificar coincidencia de contraseñas
        document.getElementById('confirmarPassword').addEventListener('input', function() {
            const nuevaPassword = document.getElementById('nuevaPassword').value;
            const confirmarPassword = this.value;
            const matchElement = document.getElementById('passwordMatch');
            
            if (confirmarPassword.length === 0) {
                matchElement.textContent = '';
            } else if (nuevaPassword === confirmarPassword) {
                matchElement.textContent = 'Las contraseñas coinciden';
                matchElement.style.color = '#27ae60';
            } else {
                matchElement.textContent = 'Las contraseñas no coinciden';
                matchElement.style.color = '#e74c3c';
            }
        });

        function actualizarPerfil(event) {
            event.preventDefault();
            
            const formData = new FormData(event.target);
            const nuevaPassword = formData.get('nueva_password');
            const confirmarPassword = formData.get('confirmar_password');
            const passwordActual = formData.get('password_actual');
            
            // Validaciones
            if (nuevaPassword && !passwordActual) {
                showAlert('Debe ingresar su contraseña actual para cambiar la contraseña', 'error');
                return;
            }
            
            if (nuevaPassword && nuevaPassword !== confirmarPassword) {
                showAlert('Las contraseñas nuevas no coinciden', 'error');
                return;
            }
            
            if (nuevaPassword && nuevaPassword.length < 6) {
                showAlert('La nueva contraseña debe tener al menos 6 caracteres', 'error');
                return;
            }
            
            // Mostrar loading
            const submitBtn = event.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
            submitBtn.disabled = true;
            
            // Enviar datos al servidor
            fetch('actualizar_perfil.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    cerrarModalPerfil();
                } else {
                    showAlert(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Error al conectar con el servidor', 'error');
            })
            .finally(() => {
                // Restaurar botón
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        }

        // Funciones para el modal de cierre de sesión
        function showLogoutModal() {
            document.getElementById('logoutModal').style.display = 'block';
            showAlert('¿Está seguro de que desea cerrar sesión?', 'warning');
        }

        function closeLogoutModal() {
            document.getElementById('logoutModal').style.display = 'none';
        }

        // Cerrar modal al hacer clic fuera de él
        window.onclick = function(event) {
            const modalPerfil = document.getElementById('profileModal');
            const modalLogout = document.getElementById('logoutModal');
            
            if (event.target === modalPerfil) {
                cerrarModalPerfil();
            }
            if (event.target === modalLogout) {
                closeLogoutModal();
            }
        }

        // Funciones para alertas
        function showAlert(message, type = 'success') {
            const alertContainer = document.getElementById('alertContainer');
            const alert = document.createElement('div');
            alert.className = `alert alert-${type}`;
            alert.innerHTML = `
                <span class="tooltip">
                    <i class="fas fa-${getAlertIcon(type)}"></i>
                    <span class="tooltiptext">${type === 'success' ? 'Éxito' : type === 'warning' ? 'Advertencia' : 'Error'}</span>
                </span>
                <span>${message}</span>
                <button onclick="this.parentElement.remove()" style="margin-left: auto; background: none; border: none; color: white; cursor: pointer;">
                    <span class="tooltip">
                        <i class="fas fa-times"></i>
                        <span class="tooltiptext">Cerrar alerta</span>
                    </span>
                </button>
            `;
            
            alertContainer.appendChild(alert);
            
            // Auto-remover después de 5 segundos
            setTimeout(() => {
                if (alert.parentElement) {
                    alert.remove();
                }
            }, 1000);
        }

        function getAlertIcon(type) {
            const icons = {
                'success': 'check-circle',
                'warning': 'exclamation-triangle',
                'error': 'times-circle'
            };
            return icons[type] || 'info-circle';
        }

        // Funciones para acciones de tabla
        function editItem(id, table) {
            showAlert(`Editando ${table} con ID: ${id}`, 'warning');
        }

        function deleteItem(id, table) {
            if (confirm('¿Está seguro de que desea eliminar este registro?')) {
                showAlert(`${table} con ID: ${id} eliminado`, 'success');
            }
        }

        function viewItem(id, table) {
            showAlert(`Viendo detalles de ${table} con ID: ${id}`, 'success');
        }

        function imprimirBautismo(id) {
            showAlert('Generando certificado de bautismo...', 'success');
            window.open(`imprimir_bautismo.php?id=${id}`, '_blank');
        }

        // Detectar intento de cierre de la pestaña/navegador
        window.addEventListener('beforeunload', function (e) {
            e.preventDefault();
            e.returnValue = '';
        });



        // Funciones específicas para el módulo de usuarios
function resetPassword(userId) {
    if (confirm('¿Está seguro de resetear la contraseña de este usuario?')) {
        fetch(`api/reset-password.php?id=${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('Contraseña reseteada exitosamente', 'success');
                    // Mostrar nueva contraseña si es necesario
                    if (data.newPassword) {
                        showAlert(`Nueva contraseña: ${data.newPassword}`, 'warning');
                    }
                } else {
                    showAlert(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Error al resetear contraseña', 'error');
            });
    }
}

function verUsuario(userId) {
    // Abrir modal de vista con datos del usuario
    fetch(`api/get-usuario.php?id=${userId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mostrar modal con información detallada
                const modalContent = `
                    <div class="user-detail-modal">
                        <h3><i class="fas fa-user"></i> Detalles del Usuario</h3>
                        <div class="user-detail-grid">
                            <div class="detail-item">
                                <label>DNI:</label>
                                <span>${data.usuario.dni}</span>
                            </div>
                            <div class="detail-item">
                                <label>Nombre:</label>
                                <span>${data.usuario.nombre}</span>
                            </div>
                            <div class="detail-item">
                                <label>Usuario:</label>
                                <span>${data.usuario.usuario}</span>
                            </div>
                            <div class="detail-item">
                                <label>Rol:</label>
                                <span class="role-badge-small ${data.usuario.rol}">${data.usuario.rol}</span>
                            </div>
                            <div class="detail-item">
                                <label>Estado:</label>
                                <span class="status-badge ${data.usuario.estado == 1 ? 'success' : 'danger'}">
                                    ${data.usuario.estado == 1 ? 'Activo' : 'Inactivo'}
                                </span>
                            </div>
                            <div class="detail-item">
                                <label>Fecha Registro:</label>
                                <span>${new Date(data.usuario.fecha_registro).toLocaleDateString()}</span>
                            </div>
                            <div class="detail-item full-width">
                                <label>Última Actividad:</label>
                                <span>${data.usuario.ultima_actividad || 'No registrada'}</span>
                            </div>
                        </div>
                        <div class="modal-actions">
                            <button onclick="abrirModalUsuario('editar', ${userId})" class="btn-edit">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button onclick="resetPassword(${userId})" class="btn-reset">
                                <i class="fas fa-key"></i> Resetear Contraseña
                            </button>
                            <button onclick="cerrarModal()" class="btn-cancel">
                                <i class="fas fa-times"></i> Cerrar
                            </button>
                        </div>
                    </div>
                `;
                
                showCustomModal(modalContent);
            } else {
                showAlert('Error al cargar usuario', 'error');
            }
        });
}

function limpiarBusquedaUsuarios() {
    window.location.href = '?section=usuarios';
}

// Función para mostrar estadísticas rápidas
function showQuickStats() {
    const statsContent = `
        <div class="quick-stats">
            <h3><i class="fas fa-chart-bar"></i> Estadísticas Rápidas</h3>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $totalUsuarios; ?></h3>
                        <p>Total Usuarios</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon success">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $usuariosActivos; ?></h3>
                        <p>Activos</p>
                    </div>
                </div>
            </div>
            <div class="distribution-chart">
                <h4>Distribución por Rol</h4>
                <div class="chart-bars">
                    <?php foreach($distribucionRol as $dist): 
                        $porcentaje = $usuariosActivos > 0 ? round(($dist['cantidad'] / $usuariosActivos) * 100, 1) : 0;
                    ?>
                    <div class="chart-bar">
                        <div class="chart-label"><?php echo getRoleDisplayName($dist['rol']); ?></div>
                        <div class="chart-progress" style="width: ${porcentaje}%"></div>
                        <div class="chart-value">${porcentaje}%</div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    `;
    
    showCustomModal(statsContent);
}

// Funciones para modal de CRUD dinámico
function openCrudModal(title, content, size = 'medium') {
    const modal = document.getElementById('crudModal');
    const modalContent = document.getElementById('crudModalContent');
    
    // Establecer tamaño del modal
    const sizes = {
        'small': '400px',
        'medium': '600px',
        'large': '800px',
        'xl': '90%'
    };
    
    modalContent.style.width = sizes[size] || '600px';
    modalContent.innerHTML = content;
    modal.style.display = 'block';
}

function closeCrudModal() {
    document.getElementById('crudModal').style.display = 'none';
}

// Modal de confirmación
let confirmCallback = null;

function showConfirmModal(title, message, details, callback) {
    document.getElementById('confirmModalTitle').innerHTML = title;
    document.getElementById('confirmMessage').textContent = message;
    document.getElementById('confirmDetails').textContent = details;
    confirmCallback = callback;
    document.getElementById('confirmModal').style.display = 'block';
}

function closeConfirmModal() {
    document.getElementById('confirmModal').style.display = 'none';
    confirmCallback = null;
}

function executeConfirmedAction() {
    if (confirmCallback) {
        confirmCallback();
    }
    closeConfirmModal();
}

// Cerrar modales al hacer clic fuera
window.onclick = function(event) {
    const modals = ['crudModal', 'confirmModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (event.target === modal) {
            if (modalId === 'crudModal') closeCrudModal();
            if (modalId === 'confirmModal') closeConfirmModal();
        }
    });
};
