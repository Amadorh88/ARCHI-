// Función para abrir el formulario (Crear, Editar, Ver)
function abrirModalCRUD(modulo, accion, id = null) {
    const modal = document.getElementById('crudModal');
    const container = document.getElementById('crudModalContent');
    
    // Petición al servidor para obtener el HTML del formulario
    fetch(`acciones/obtener_formulario.php?modulo=${modulo}&accion=${accion}&id=${id}`)
        .then(response => response.text())
        .then(html => {
            container.innerHTML = html;
            modal.style.display = 'block';
        });
}

// Lógica de Eliminación
let datosAEliminar = null;

function confirmarEliminar(modulo, id, nombre) {
    datosAEliminar = { modulo, id };
    document.getElementById('confirmMessage').innerText = `¿Eliminar ${modulo}?`;
    document.getElementById('confirmDetails').innerText = `Estás a punto de borrar a: ${nombre}`;
    document.getElementById('confirmModal').style.display = 'block';
}

document.getElementById('confirmActionBtn').onclick = function() {
    if (!datosAEliminar) return;

    fetch(`acciones/eliminar_registro.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datosAEliminar)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Refrescar para ver cambios
        } else {
            alert("Error: " + data.message);
        }
    });
};

function closeConfirmModal() {
    document.getElementById('confirmModal').style.display = 'none';
}