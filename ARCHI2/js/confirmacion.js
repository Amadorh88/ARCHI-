/* ===============================
   ABRIR MODAL CRUD Confirmacion
================================ */
function abrirModalConfirmacion(accion, id = null) {

    let url = `acciones/obtener_formulario.php?modulo=confirmacion&accion=${accion}`;
    if (id) url += `&id=${id}`;

    fetch(url)
        .then(res => {
            if (!res.ok) throw new Error('Error al cargar formulario');
            return res.text();
        })
        .then(html => {
            document.getElementById('crudModalContent').innerHTML = html;
            document.getElementById('crudModal').style.display = 'block';
        })
        .catch(err => {
            console.error('[Confirmacion]', err);
            alert('No se pudo cargar el formulario');
        });
}

/* ===============================
   VER Confirmacion
================================ */
function verConfirmacion(id) {
    abrirModalConfirmacion('ver', id);
}

/* ===============================
   AGREGAR Confirmacion
================================ */
function agregarConfirmacion() {
    abrirModalConfirmacion('crear');
}

/* ===============================
   EDITAR Confirmacion
================================ */
function editarConfirmacion(id) {
    abrirModalConfirmacion('editar', id);
}

/* ===============================
   GUARDAR / EDITAR Confirmacion
================================ */
function guardarCambios(e) {
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
            alert(resp.message);
        }
    })
    .catch(err => {
        console.error('[Confirmacion]', err);
        alert('Error interno del servidor');
    });
}

/* ===============================
   ELIMINAR Confirmacion
================================ */
function eliminarConfirmacion(id) {

    document.getElementById('confirmModalTitle').innerHTML =
        '<i class="fas fa-trash-alt"></i> Eliminar Confirmacion';

    document.getElementById('confirmMessage').innerText =
        '¿Desea eliminar esta Confirmacion?';

    document.getElementById('confirmDetails').innerText =
        'Esta acción no se puede deshacer';

    const btn = document.getElementById('confirmActionBtn');
    btn.onclick = () => {

        fetch('api/liminar.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ id })
        })
        .then(res => res.json())
        .then(resp => {
            if (resp.success) {
                closeConfirmModal();
                location.reload();
            } else {
                alert(resp.message);
            }
        })
        .catch(() => alert('Error al eliminar'));
    };

    document.getElementById('confirmModal').style.display = 'block';
}
