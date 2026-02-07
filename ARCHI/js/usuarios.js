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
    fetch(`acciones/usuario_ver.php?id=${id}`)
        .then(r => r.json())
        .then(u => {
            document.getElementById('contenidoUsuario').innerHTML = `
                    <p><span>Nombre:</span> ${u.nombre}</p>
                    <p><span>DNI:</span> ${u.dni}</p>
                    <p><span>Usuario:</span> ${u.usuario}</p>
                    <p><span>Rol:</span> ${u.rol}</p>
                    <p><span>Estado:</span> 
                        <strong style="color:${u.estado == 1 ? '#27ae60' : '#c0392b'}">
                            ${u.estado == 1 ? 'Activo' : 'Inactivo'}
                        </strong>
                    </p>
                    <p><span>Registrado:</span> ${u.fecha_registro}</p>
                `;
            document.getElementById('modalVerUsuario').style.display = 'flex';
        });
}

function cerrarModalUsuario() {
    document.getElementById('modalVerUsuario').style.display = 'none';
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
   Cambiar Estado Usuario (CORREGIDO)
========================================= */
function toggleEstadoUsuario(id, estadoActual) {

    // 🔁 invertir estado (1 → 0, 0 → 1)
    const nuevoEstado = estadoActual == 1 ? 0 : 1;

    const accion = nuevoEstado === 1 ? 'activar' : 'desactivar';
    if (!confirm(`¿Deseas ${accion} este usuario?`)) return;

    const formData = new FormData();
    formData.append('id', id);
    formData.append('estado', nuevoEstado); // 👈 AQUÍ ESTÁ LA CLAVE

    fetch('acciones/cambiar_estado_usuario.php', {
        method: 'POST',
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // ✔ actualización visual simple y segura
                location.reload();
            } else {
                alert(data.message || 'Error al cambiar estado');
                location.reload();
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

/* ====================== */
// BUSQUEDA
/* ====================== */


document.getElementById('formBuscarUsuarios').addEventListener('submit', function (e) {
    e.preventDefault();
    buscarUsuarios();
});

function buscarUsuarios() {

    const form = document.getElementById('formBuscarUsuarios');
    const formData = new FormData(form);
    console.log("estoy buscando")
    fetch('acciones/buscar_usuarios.php', {
        method: 'POST',
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            renderTablaUsuarios(data);
        })
        .catch(err => {
            console.error('[BUSQUEDA USUARIOS]', err);
            alert('Error al buscar usuarios');
        });
}

function renderTablaUsuarios(usuarios) {

    const tbody = document.getElementById('tablaUsuariosBody');
    tbody.innerHTML = '';

    if (usuarios.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" style="text-align:center;padding:2rem;">
                    No se encontraron usuarios
                </td>
            </tr>
        `;
        return;
    }

    usuarios.forEach(u => {
        tbody.innerHTML += `
            <tr>
                <td>${u.id}</td>
                <td>${escapeHtml(u.nombre)}</td>
                <td>${escapeHtml(u.dni)}</td>
                <td>${escapeHtml(u.usuario)}</td>
                <td>${u.rol}</td>
                <td>
                    <button onclick="toggleEstadoUsuario(${u.id}, ${u.estado})"
                        style="color:${u.estado == 1 ? '#c0392b' : '#27ae60'}"
                        title="${u.estado == 1 ? 'Desactivar' : 'Activar'}">
                        <i class="fas ${u.estado == 1 ? 'fa-user-slash' : 'fa-user-check'}"></i>
                    </button>
                </td>
                <td>${u.fecha_registro}</td>
                <td>
                    <button onclick="verUsuario(${u.id})">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button onclick="abrirModalUsuario('editar', ${u.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                </td>
            </tr>
        `;
    });
}
renderTablaUsuarios()
// 🔐 evitar XSS
function escapeHtml(text) {
    return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;");
}


