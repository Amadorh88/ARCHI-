/* ===============================
   ABRIR MODAL CRUD Matrimonio
================================ */
function abrirModalMatrimonio(accion, id = null) {

    let url = `acciones/obtener_formulario.php?modulo=matrimonio&accion=${accion}`;
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
            console.error('[Matrimonio]', err);
            alert('No se pudo cargar el formulario');
        });
}

/* ===============================
   VER Matrimonio
================================ */
function verMatrimonio(id) {
    abrirModalMatrimonio('ver', id);
}

/* ===============================
   AGREGAR Matrimonio
================================ */
function agregarMatrimonio() {
    abrirModalMatrimonio('crear');
}

/* ===============================
   EDITAR Matrimonio
================================ */
function editarMatrimonio(id) {
    abrirModalMatrimonio('editar', id);
}

/* ===============================
   GUARDAR / EDITAR Matrimonio
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
        console.error('[Matrimonio]', err);
        alert('Error interno del servidor');
    });
}

/* ===============================
   ELIMINAR Matrimonio
================================ */
function eliminarMatrimonio(id) {

    document.getElementById('confirmModalTitle').innerHTML =
        '<i class="fas fa-trash-alt"></i> Eliminar Matrimonio';

    document.getElementById('confirmMessage').innerText =
        '¿Desea eliminar esta Matrimonio?';

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
