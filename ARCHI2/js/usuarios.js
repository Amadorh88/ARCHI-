// Abrir Modales de CRUD
function abrirModalCRUD(modulo, accion, id = null) {
    const modal = document.getElementById('crudModal');
    const container = document.getElementById('crudModalContent');
    
    fetch(`acciones/obtener_formulario.php?modulo=${modulo}&accion=${accion}&id=${id}`)
        .then(res => res.text())
        .then(html => {
            container.innerHTML = html;
            modal.style.display = 'block';
        });
}

// Cambiar Estado con el Interruptor
function cambiarEstadoUsuario(id, nuevoEstado) {
    const formData = new FormData();
    formData.append('id', id);
    formData.append('estado', nuevoEstado);

    fetch('acciones/cambiar_estado_usuario.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            alert("Error al cambiar estado");
            location.reload(); // Revertir visualmente si falla
        }
    });
}

// Confirmar Eliminación
let tempDeleteData = null;
function confirmarEliminar(modulo, id, nombre) {
    tempDeleteData = { modulo, id };
    document.getElementById('confirmMessage').innerText = `¿Eliminar a ${nombre}?`;
    document.getElementById('confirmModal').style.display = 'block';
}

function executeConfirmedAction() {
    if (!tempDeleteData) return;
    fetch('acciones/eliminar_registro.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(tempDeleteData)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) location.reload();
        else alert(data.message);
    });
}

function closeConfirmModal() {
    document.getElementById('confirmModal').style.display = 'none';
}