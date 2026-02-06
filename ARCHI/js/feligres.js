/* ===============================
   ABRIR MODAL CRUD FELIGRES
================================ */
function abrirModalFeligres(accion, id = null) {

    let url = `acciones/obtener_formulario.php?modulo=feligres&accion=${accion}`;
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
            console.error('[FELIGRES]', err);
            alert('No se pudo cargar el formulario');
        });
}

/* ===============================
   VER FELIGRES
================================ */
function verFeligres(id) {
    abrirModalFeligres('ver', id);
}

/* ===============================
   GUARDAR / EDITAR
================================ */
function guardarCambios(e) {
    e.preventDefault();

    const form = document.getElementById('formCRUD');
    const data = new FormData(form);

    fetch('api/feligres_guardar.php', {
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
        console.error(err);
        alert('Error interno del servidor');
    });
}

/* ===============================
   ELIMINAR FELIGRES
================================ */
function eliminarFeligres(id) {

    document.getElementById('confirmModalTitle').innerHTML =
        '<i class="fas fa-trash-alt"></i> Eliminar Feligrés';

    document.getElementById('confirmMessage').innerText =
        '¿Desea eliminar este feligrés?';

    document.getElementById('confirmDetails').innerText =
        'Esta acción no se puede deshacer';

    const btn = document.getElementById('confirmActionBtn');
    btn.onclick = () => {

        fetch('acciones/feligres_eliminar.php', {
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
