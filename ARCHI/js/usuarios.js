/* =========================================
   CRUD USUARIOS
========================================= */

// Abrir Modal (crear / editar / ver)
function abrirModalUsuario(accion, id = null) {
    const modal = document.getElementById('crudModal');
    const container = document.getElementById('crudModalContent');

    let url = `acciones/obtener_formulario.php?modulo=usuarios&accion=${accion}`;
    if (id) url += `&id=${id}`;

    fetch(url)
        .then(res => {
            if (!res.ok) throw new Error('Error al cargar formulario');
            return res.text();
        })
        .then(html => {
            container.innerHTML = html;
            modal.style.display = 'block';
        })
        .catch(err => {
            console.error('[USUARIOS]', err);
            alert('No se pudo cargar el formulario');
        });
}

// Abrir Modal según acción
function verUsuario(id) {
    abrirModalUsuario('ver', id);
}

function agregarUsuario() {
    abrirModalUsuario('crear');
}

function editarUsuario(id) {
    abrirModalUsuario('editar', id);
}

/* =========================================
   Guardar / Editar Usuario
========================================= */
function guardarUsuario(e) {
    e.preventDefault();

    const form = document.getElementById('formCRUD');
    const data = new FormData(form);

    fetch('api/guardar.php', {
        method: 'POST',
        body: data
    })
    .then(res => res.json())
    .then(resp => {
        if (resp.success) {
            location.reload();
        } else {
            alert(resp.message || 'Error al guardar usuario');
        }
    })
    .catch(err => {
        console.error('[USUARIOS]', err);
        alert('Error interno del servidor');
    });
}

/* =========================================
   Cambiar Estado Usuario
========================================= */
function cambiarEstadoUsuario(id, nuevoEstado) {
    const formData = new FormData();
    formData.append('id', id);
    formData.append('modulo', 'usuarios'); // importante para el backend
    formData.append('accion', 'cambiar_estado'); // backend puede diferenciar

    formData.append('estado', nuevoEstado);

    fetch('acciones/cambiar_estado_usuario.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            alert('Error al cambiar estado');
            location.reload(); // revertir visualmente
        }
    })
    .catch(err => {
        console.error('[USUARIOS]', err);
        alert('Error interno al cambiar estado');
        location.reload();
    });
}

/* =========================================
   Eliminar Usuario
========================================= */
let tempDeleteUsuario = null;

function confirmarEliminarUsuario(id, nombre) {
    tempDeleteUsuario = { modulo: 'usuarios', id };
    document.getElementById('confirmMessage').innerText = `¿Desea eliminar a ${nombre}?`;
    document.getElementById('confirmModal').style.display = 'block';
}

function executeConfirmedUsuario() {
    if (!tempDeleteUsuario) return;

    fetch('acciones/eliminar_registro.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(tempDeleteUsuario)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) location.reload();
        else alert(data.message || 'Error al eliminar usuario');
    })
    .catch(err => {
        console.error('[USUARIOS]', err);
        alert('Error al eliminar');
    });
}

function closeConfirmUsuarioModal() {
    document.getElementById('confirmModal').style.display = 'none';
}


/* // Abrir Modales de CRUD
function abrirModalCRUD(modulo, accion, id = null) {
    const modal = document.getElementById('crudModal');
    const container = document.getElementById('crudModalContent');
    
    fetch(`acciones/obtener_formulario.php?modulo=usuarios&accion=${accion}&id=${id}`)
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
} */