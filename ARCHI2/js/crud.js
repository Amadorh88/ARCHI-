// [file name]: crud.js
// ==================== FUNCIONES GENERALES ====================

// Verificar permisos del usuario
function puede(accion) {
    return window.permisosUsuario && window.permisosUsuario.includes(accion);
}

// Mostrar error de permisos
function mostrarErrorPermisos(accion) {
    showAlert(`No tiene permisos para ${accion}`, 'error');
}

// ==================== FUNCIONES PARA FELIGRESES ====================

function abrirFeligresForm(action = 'crear', id = null) {
    // Verificar permisos
    const accionPermiso = action === 'crear' ? 'crear' : 'editar';
    if (!puede(accionPermiso)) {
        mostrarErrorPermisos(accionPermiso + ' feligreses');
        return;
    }
    
    if (id) {
        fetch(`api/get_feligres.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const formContent = getFeligresForm(action, data.feligres);
                    openCrudModal(action === 'crear' ? 'Nuevo Feligrés' : 'Editar Feligrés', formContent, 'large');
                } else {
                    showAlert('Error al cargar feligrés', 'error');
                }
            });
    } else {
        const formContent = getFeligresForm(action);
        openCrudModal('Nuevo Feligrés', formContent, 'large');
    }
}

function verFeligres(id) {
    if (!puede('ver')) {
        mostrarErrorPermisos('ver feligreses');
        return;
    }
    
    fetch(`api/get_feligres.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const viewContent = getFeligresView(data.feligres);
                openCrudModal('Detalles del Feligrés', viewContent, 'large');
            } else {
                showAlert('Error al cargar feligrés', 'error');
            }
        });
}

function guardarFeligres(event, action) {
    event.preventDefault();
    
    const accionPermiso = action === 'crear' ? 'crear' : 'editar';
    if (!puede(accionPermiso)) {
        mostrarErrorPermisos(accionPermiso + ' feligreses');
        return;
    }
    
    const formData = new FormData(event.target);
    
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
    submitBtn.disabled = true;
    
    const url = action === 'crear' ? 'api/create_feligres.php' : 'api/update_feligres.php';
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            closeCrudModal();
            // Recargar contenido actual
            setTimeout(() => {
                const currentSection = document.getElementById('pageTitle').textContent.toLowerCase();
                showContent(currentSection, document.getElementById('pageTitle').textContent);
            }, 1000);
        } else {
            showAlert(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error al guardar feligrés', 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

function eliminarFeligres(id) {
    if (!puede('eliminar')) {
        mostrarErrorPermisos('eliminar feligreses');
        return;
    }
    
    showConfirmModal(
        'Eliminar Feligrés',
        '¿Está seguro de eliminar este feligrés?',
        'Esta acción marcará al feligrés como inactivo. Se mantendrá en el registro histórico.',
        function() {
            fetch(`api/delete_feligres.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('Feligrés eliminado correctamente', 'success');
                        setTimeout(() => {
                            const currentSection = document.getElementById('pageTitle').textContent.toLowerCase();
                            showContent(currentSection, document.getElementById('pageTitle').textContent);
                        }, 1000);
                    } else {
                        showAlert(data.message, 'error');
                    }
                });
        }
    );
}

function imprimirFichaFeligres(id) {
    if (!puede('imprimir')) {
        mostrarErrorPermisos('imprimir fichas');
        return;
    }
    window.open(`reportes/ficha_feligres.php?id=${id}`, '_blank');
}

// ==================== FUNCIONES PARA USUARIOS ====================

function abrirModalUsuario(action = 'crear', id = null) {
    const accionPermiso = action === 'crear' ? 'crear' : 'editar';
    if (!puede(accionPermiso)) {
        mostrarErrorPermisos(accionPermiso + ' usuarios');
        return;
    }
    
    if (id) {
        fetch(`api/get_usuario.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const formContent = getUsuarioForm(action, data.usuario);
                    openCrudModal(action === 'crear' ? 'Nuevo Usuario' : 'Editar Usuario', formContent, 'medium');
                } else {
                    showAlert('Error al cargar usuario', 'error');
                }
            });
    } else {
        const formContent = getUsuarioForm(action);
        openCrudModal('Nuevo Usuario', formContent, 'medium');
    }
}

function verUsuario(id) {
    if (!puede('ver')) {
        mostrarErrorPermisos('ver usuarios');
        return;
    }
    
    fetch(`api/get_usuario.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const viewContent = `
                    <div class="view-form">
                        <div class="modal-header">
                            <h3><i class="fas fa-eye"></i> Detalles del Usuario</h3>
                            <button class="modal-close-btn" onclick="closeCrudModal()">&times;</button>
                        </div>
                        
                        <div class="view-grid">
                            <div class="view-section">
                                <h4><i class="fas fa-id-card"></i> Información Personal</h4>
                                <div class="view-row">
                                    <span class="view-label">DNI:</span>
                                    <span class="view-value">${data.usuario.dni}</span>
                                </div>
                                <div class="view-row">
                                    <span class="view-label">Nombre:</span>
                                    <span class="view-value">${data.usuario.nombre}</span>
                                </div>
                                <div class="view-row">
                                    <span class="view-label">Usuario:</span>
                                    <span class="view-value">${data.usuario.usuario}</span>
                                </div>
                            </div>
                            
                            <div class="view-section">
                                <h4><i class="fas fa-user-cog"></i> Información de Cuenta</h4>
                                <div class="view-row">
                                    <span class="view-label">Rol:</span>
                                    <span class="view-value role-badge-small ${data.usuario.rol}">
                                        ${data.usuario.rol === 'admin' ? 'Administrador' : 
                                          data.usuario.rol === 'secretaria' ? 'Secretaría' :
                                          data.usuario.rol === 'archivista' ? 'Archivista' : 'Párroco'}
                                    </span>
                                </div>
                                <div class="view-row">
                                    <span class="view-label">Estado:</span>
                                    <span class="view-value ${data.usuario.estado == 1 ? 'status-active' : 'status-inactive'}">
                                        ${data.usuario.estado == 1 ? 'Activo' : 'Inactivo'}
                                    </span>
                                </div>
                                <div class="view-row">
                                    <span class="view-label">Fecha Registro:</span>
                                    <span class="view-value">${new Date(data.usuario.fecha_registro).toLocaleDateString()}</span>
                                </div>
                            </div>
                            
                            <div class="view-section">
                                <h4><i class="fas fa-address-book"></i> Información de Contacto</h4>
                                <div class="view-row">
                                    <span class="view-label">Email:</span>
                                    <span class="view-value">${data.usuario.email || 'No registrado'}</span>
                                </div>
                                <div class="view-row">
                                    <span class="view-label">Teléfono:</span>
                                    <span class="view-value">${data.usuario.telefono || 'No registrado'}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="view-actions">
                            ${puede('editar') ? 
                            `<button class="btn-primary" onclick="abrirModalUsuario('editar', ${id})">
                                <i class="fas fa-edit"></i> Editar
                            </button>` : ''}
                            <button class="btn-cancel" onclick="closeCrudModal()">
                                <i class="fas fa-times"></i> Cerrar
                            </button>
                        </div>
                    </div>
                `;
                openCrudModal('Detalles del Usuario', viewContent, 'medium');
            } else {
                showAlert('Error al cargar usuario', 'error');
            }
        });
}

function guardarUsuario(event, action) {
    event.preventDefault();
    
    const accionPermiso = action === 'crear' ? 'crear' : 'editar';
    if (!puede(accionPermiso)) {
        mostrarErrorPermisos(accionPermiso + ' usuarios');
        return;
    }
    
    const formData = new FormData(event.target);
    
    // Validar contraseñas si es creación
    if (action === 'crear') {
        const password = formData.get('password');
        const confirmPassword = formData.get('confirm_password');
        
        if (password !== confirmPassword) {
            showAlert('Las contraseñas no coinciden', 'error');
            return;
        }
        
        if (password.length < 6) {
            showAlert('La contraseña debe tener al menos 6 caracteres', 'error');
            return;
        }
    }
    
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
    submitBtn.disabled = true;
    
    const url = action === 'crear' ? 'api/create_usuario.php' : 'api/update_usuario.php';
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            closeCrudModal();
            setTimeout(() => showContent('usuarios', 'Usuarios'), 1000);
        } else {
            showAlert(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error al guardar usuario', 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

function cambiarEstadoUsuario(id, estado) {
    if (!puede('editar')) {
        mostrarErrorPermisos('cambiar estado de usuarios');
        return;
    }
    
    const action = estado === 1 ? 'activar' : 'desactivar';
    showConfirmModal(
        `${action === 'activar' ? 'Activar' : 'Desactivar'} Usuario`,
        `¿Está seguro de ${action} este usuario?`,
        'El usuario ' + (estado === 1 ? 'volverá a tener acceso al sistema' : 'perderá acceso temporalmente'),
        function() {
            fetch(`api/update_usuario_estado.php?id=${id}&estado=${estado}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert(`Usuario ${action}do correctamente`, 'success');
                        setTimeout(() => showContent('usuarios', 'Usuarios'), 1000);
                    } else {
                        showAlert(data.message, 'error');
                    }
                });
        }
    );
}

function resetearPassword(id) {
    if (!puede('editar')) {
        mostrarErrorPermisos('resetear contraseñas');
        return;
    }
    
    showConfirmModal(
        'Resetear Contraseña',
        '¿Está seguro de resetear la contraseña de este usuario?',
        'Se generará una nueva contraseña aleatoria. Deberá comunicarla al usuario.',
        function() {
            fetch(`api/reset_password.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert(data.message, 'success');
                        if (data.new_password) {
                            // Mostrar la nueva contraseña temporalmente
                            const modalContent = `
                                <div class="password-result">
                                    <h3><i class="fas fa-key"></i> Contraseña Reseteada</h3>
                                    <div class="password-display">
                                        <p>La nueva contraseña para el usuario es:</p>
                                        <div class="password-box">
                                            <code style="font-size: 1.2rem; font-weight: bold; color: #2c3e50;">${data.new_password}</code>
                                        </div>
                                        <p class="warning"><i class="fas fa-exclamation-triangle"></i> 
                                        Esta contraseña es temporal. El usuario deberá cambiarla en su primer acceso.</p>
                                    </div>
                                    <div class="modal-actions">
                                        <button class="btn-cancel" onclick="closeCrudModal()">
                                            <i class="fas fa-times"></i> Cerrar
                                        </button>
                                        <button class="btn-primary" onclick="copiarPassword('${data.new_password}')">
                                            <i class="fas fa-copy"></i> Copiar Contraseña
                                        </button>
                                    </div>
                                </div>
                            `;
                            openCrudModal('Contraseña Reseteada', modalContent, 'small');
                        }
                    } else {
                        showAlert(data.message, 'error');
                    }
                });
        }
    );
}

function copiarPassword(password) {
    navigator.clipboard.writeText(password).then(() => {
        showAlert('Contraseña copiada al portapapeles', 'success');
    });
}

function eliminarUsuario(id) {
    if (!puede('eliminar')) {
        mostrarErrorPermisos('eliminar usuarios');
        return;
    }
    
    showConfirmModal(
        'Eliminar Usuario',
        '¿Está seguro de eliminar este usuario?',
        'El usuario será marcado como inactivo y no podrá acceder al sistema.',
        function() {
            fetch(`api/delete_usuario.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('Usuario eliminado correctamente', 'success');
                        setTimeout(() => showContent('usuarios', 'Usuarios'), 1000);
                    } else {
                        showAlert(data.message, 'error');
                    }
                });
        }
    );
}

// ==================== FUNCIONES PARA BAUTISMOS ====================

function abrirBautismoForm(action = 'crear', id = null) {
    const accionPermiso = action === 'crear' ? 'crear' : 'editar';
    if (!puede(accionPermiso)) {
        mostrarErrorPermisos(accionPermiso + ' bautismos');
        return;
    }
    
    if (id) {
        fetch(`api/get_bautismo.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const formContent = getBautismoForm(action, data.bautismo);
                    openCrudModal(action === 'crear' ? 'Registrar Bautismo' : 'Editar Bautismo', formContent, 'large');
                } else {
                    showAlert('Error al cargar bautismo', 'error');
                }
            });
    } else {
        const formContent = getBautismoForm(action);
        openCrudModal('Registrar Bautismo', formContent, 'large');
    }
}

function verBautismo(id) {
    if (!puede('ver')) {
        mostrarErrorPermisos('ver bautismos');
        return;
    }
    
    fetch(`api/get_bautismo.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Crear vista similar a las otras
                const b = data.bautismo;
                const viewContent = `
                    <div class="view-form">
                        <div class="modal-header">
                            <h3><i class="fas fa-eye"></i> Detalles del Bautismo</h3>
                            <button class="modal-close-btn" onclick="closeCrudModal()">&times;</button>
                        </div>
                        
                        <div class="view-grid">
                            <div class="view-section">
                                <h4><i class="fas fa-user"></i> Bautizado</h4>
                                <div class="view-row">
                                    <span class="view-label">Nombre:</span>
                                    <span class="view-value">${b.nombre_feligres}</span>
                                </div>
                            </div>
                            
                            <div class="view-section">
                                <h4><i class="fas fa-calendar-alt"></i> Datos del Sacramento</h4>
                                <div class="view-row">
                                    <span class="view-label">Fecha:</span>
                                    <span class="view-value">${new Date(b.fecha_bautismo).toLocaleDateString()}</span>
                                </div>
                                <div class="view-row">
                                    <span class="view-label">Sacerdote:</span>
                                    <span class="view-value">${b.sacerdote}</span>
                                </div>
                                <div class="view-row">
                                    <span class="view-label">Padrino:</span>
                                    <span class="view-value">${b.padrino || 'No registrado'}</span>
                                </div>
                                <div class="view-row">
                                    <span class="view-label">Madrina:</span>
                                    <span class="view-value">${b.madrina || 'No registrado'}</span>
                                </div>
                            </div>
                            
                            <div class="view-section">
                                <h4><i class="fas fa-book"></i> Registro</h4>
                                <div class="view-row">
                                    <span class="view-label">Libro:</span>
                                    <span class="view-value">${b.libro || 'No registrado'}</span>
                                </div>
                                <div class="view-row">
                                    <span class="view-label">Folio:</span>
                                    <span class="view-value">${b.folio || 'No registrado'}</span>
                                </div>
                                <div class="view-row">
                                    <span class="view-label">Número:</span>
                                    <span class="view-value">${b.numero || 'No registrado'}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="view-actions">
                            ${puede('editar') ? 
                            `<button class="btn-primary" onclick="abrirBautismoForm('editar', ${id})">
                                <i class="fas fa-edit"></i> Editar
                            </button>` : ''}
                            
                            ${puede('imprimir') ? 
                            `<button class="btn-print" onclick="imprimirCertificadoBautismo(${id})" style="background-color: #9b59b6;">
                                <i class="fas fa-print"></i> Certificado
                            </button>` : ''}
                            
                            <button class="btn-cancel" onclick="closeCrudModal()">
                                <i class="fas fa-times"></i> Cerrar
                            </button>
                        </div>
                    </div>
                `;
                openCrudModal('Detalles del Bautismo', viewContent, 'medium');
            } else {
                showAlert('Error al cargar bautismo', 'error');
            }
        });
}

// ==================== FUNCIONES PARA MATRIMONIOS ====================

function abrirMatrimonioForm(action = 'crear', id = null) {
    const accionPermiso = action === 'crear' ? 'crear' : 'editar';
    if (!puede(accionPermiso)) {
        mostrarErrorPermisos(accionPermiso + ' matrimonios');
        return;
    }
    
    if (id) {
        fetch(`api/get_matrimonio.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const formContent = getMatrimonioForm(action, data.matrimonio);
                    openCrudModal(action === 'crear' ? 'Registrar Matrimonio' : 'Editar Matrimonio', formContent, 'large');
                } else {
                    showAlert('Error al cargar matrimonio', 'error');
                }
            });
    } else {
        const formContent = getMatrimonioForm(action);
        openCrudModal('Registrar Matrimonio', formContent, 'large');
    }
}

// ==================== FUNCIONES PARA CATEQUISTAS ====================

function abrirCatequistaForm(action = 'crear', id = null) {
    const accionPermiso = action === 'crear' ? 'crear' : 'editar';
    if (!puede(accionPermiso)) {
        mostrarErrorPermisos(accionPermiso + ' catequistas');
        return;
    }
    
    if (id) {
        fetch(`api/get_catequista.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const formContent = getCatequistaForm(action, data.catequista);
                    openCrudModal(action === 'crear' ? 'Nuevo Catequista' : 'Editar Catequista', formContent, 'medium');
                } else {
                    showAlert('Error al cargar catequista', 'error');
                }
            });
    } else {
        const formContent = getCatequistaForm(action);
        openCrudModal('Nuevo Catequista', formContent, 'medium');
    }
}

// ==================== FUNCIONES PARA PARROQUIAS ====================

function abrirParroquiaForm(action = 'crear', id = null) {
    const accionPermiso = action === 'crear' ? 'crear' : 'editar';
    if (!puede(accionPermiso)) {
        mostrarErrorPermisos(accionPermiso + ' parroquias');
        return;
    }
    
    if (id) {
        fetch(`api/get_parroquia.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const formContent = getParroquiaForm(action, data.parroquia);
                    openCrudModal(action === 'crear' ? 'Nueva Parroquia' : 'Editar Parroquia', formContent, 'medium');
                } else {
                    showAlert('Error al cargar parroquia', 'error');
                }
            });
    } else {
        const formContent = getParroquiaForm(action);
        openCrudModal('Nueva Parroquia', formContent, 'medium');
    }
}

// ==================== FUNCIONES PARA PAGOS ====================

function abrirPagoForm(action = 'crear', id = null) {
    const accionPermiso = action === 'crear' ? 'crear' : 'editar';
    if (!puede(accionPermiso)) {
        mostrarErrorPermisos(accionPermiso + ' pagos');
        return;
    }
    
    if (id) {
        fetch(`api/get_pago.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const formContent = getPagoForm(action, data.pago);
                    openCrudModal(action === 'crear' ? 'Nuevo Pago' : 'Editar Pago', formContent, 'medium');
                } else {
                    showAlert('Error al cargar pago', 'error');
                }
            });
    } else {
        const formContent = getPagoForm(action);
        openCrudModal('Nuevo Pago', formContent, 'medium');
    }
}

// ==================== FUNCIONES DE IMPRESIÓN ====================

function imprimirListaUsuarios() {
    if (!puede('imprimir')) {
        mostrarErrorPermisos('imprimir listas');
        return;
    }
    window.open('reportes/usuarios_pdf.php', '_blank');
}

function imprimirCertificadoBautismo(id) {
    if (!puede('imprimir')) {
        mostrarErrorPermisos('imprimir certificados');
        return;
    }
    window.open(`reportes/certificado_bautismo.php?id=${id}`, '_blank');
}

function imprimirCertificadoMatrimonio(id) {
    if (!puede('imprimir')) {
        mostrarErrorPermisos('imprimir certificados');
        return;
    }
    window.open(`reportes/certificado_matrimonio.php?id=${id}`, '_blank');
}

// ==================== FUNCIONES AUXILIARES ====================

function limpiarBusquedaUsuarios() {
    window.location.href = '?section=usuarios';
}

function limpiarBusquedaFeligreses() {
    window.location.href = '?section=feligreses';
}

// Inicializar permisos cuando se cargue la página
document.addEventListener('DOMContentLoaded', function() {
    // Verificar si el usuario tiene permisos para acciones en la página actual
    const currentSection = window.location.hash || 'inicio';
    
    // Deshabilitar botones según permisos
    document.querySelectorAll('[data-required-permission]').forEach(button => {
        const permission = button.getAttribute('data-required-permission');
        if (!puede(permission)) {
            button.style.display = 'none';
        }
    });
});