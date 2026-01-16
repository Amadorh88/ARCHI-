// En tu archivo modals-system.js o donde manejes los modales
function abrirModalPerfil() {
    const modal = document.getElementById('modalPerfil');
    modal.style.display = 'flex';
    
    // Cargar datos actuales del usuario
    document.getElementById('perfilUsername').value = '<?php echo isset($_SESSION["username"]) ? htmlspecialchars($_SESSION["username"]) : ""; ?>';
    document.getElementById('perfilNombre').value = '<?php echo isset($_SESSION["nombre_completo"]) ? htmlspecialchars($_SESSION["nombre_completo"]) : ""; ?>';
    
    // Limpiar mensajes y campos de contraseña
    document.getElementById('perfilMensaje').style.display = 'none';
    document.getElementById('passwordActual').value = '';
    document.getElementById('nuevaPassword').value = '';
    document.getElementById('confirmarPassword').value = '';
}

function cerrarModalPerfil() {
    const modal = document.getElementById('modalPerfil');
    modal.style.display = 'none';
}

function actualizarPerfil(event) {
    event.preventDefault();
    
    const form = event.target;
    const nuevaPassword = document.getElementById('nuevaPassword').value;
    const confirmarPassword = document.getElementById('confirmarPassword').value;
    const mensajeDiv = document.getElementById('perfilMensaje');
    const btnActualizar = document.getElementById('btnActualizarPerfil');
    
    // Validar que las contraseñas coincidan si se está cambiando
    if (nuevaPassword && nuevaPassword !== confirmarPassword) {
        mensajeDiv.innerHTML = '<div style="background-color: #f8d7da; color: #721c24; padding: 0.75rem; border-radius: 5px; border: 1px solid #f5c6cb;"><i class="fas fa-exclamation-circle"></i> Las contraseñas no coinciden</div>';
        mensajeDiv.style.display = 'block';
        return;
    }
    
    // Validar longitud mínima de contraseña
    if (nuevaPassword && nuevaPassword.length < 6) {
        mensajeDiv.innerHTML = '<div style="background-color: #f8d7da; color: #721c24; padding: 0.75rem; border-radius: 5px; border: 1px solid #f5c6cb;"><i class="fas fa-exclamation-circle"></i> La contraseña debe tener al menos 6 caracteres</div>';
        mensajeDiv.style.display = 'block';
        return;
    }
    
    // Mostrar loading en el botón
    const originalText = btnActualizar.innerHTML;
    btnActualizar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Actualizando...';
    btnActualizar.disabled = true;
    
    // Enviar datos via AJAX
    fetch('actualizar_perfil.php', {
        method: 'POST',
        body: new FormData(form)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mostrar mensaje de éxito
            mensajeDiv.innerHTML = `<div style="background-color: #d4edda; color: #155724; padding: 0.75rem; border-radius: 5px; border: 1px solid #c3e6cb;">
                <i class="fas fa-check-circle"></i> ${data.message}
            </div>`;
            mensajeDiv.style.display = 'block';
            
            // Actualizar datos en la sesión (opcional: recargar página)
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            // Mostrar mensaje de error
            mensajeDiv.innerHTML = `<div style="background-color: #f8d7da; color: #721c24; padding: 0.75rem; border-radius: 5px; border: 1px solid #f5c6cb;">
                <i class="fas fa-exclamation-circle"></i> ${data.message}
            </div>`;
            mensajeDiv.style.display = 'block';
        }
    })
    .catch(error => {
        mensajeDiv.innerHTML = `<div style="background-color: #f8d7da; color: #721c24; padding: 0.75rem; border-radius: 5px; border: 1px solid #f5c6cb;">
            <i class="fas fa-exclamation-circle"></i> Error al procesar la solicitud
        </div>`;
        mensajeDiv.style.display = 'block';
    })
    .finally(() => {
        // Restaurar botón
        btnActualizar.innerHTML = originalText;
        btnActualizar.disabled = false;
    });
}

// Cerrar modal haciendo clic fuera
window.onclick = function(event) {
    const modal = document.getElementById('modalPerfil');
    if (event.target == modal) {
        cerrarModalPerfil();
    }
}