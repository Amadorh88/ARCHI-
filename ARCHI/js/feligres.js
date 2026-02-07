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
/* function verFeligres(id) {
    abrirModalFeligres('ver', id);
}
 */
function verFeligres(id) {
    fetch(`acciones/feligres_ver.php?id=${id}`)
        .then(r => r.json())
        .then(f => {

            document.getElementById('contenidoFeligres').innerHTML = `
                <h4 style="color:#2c3e50;">Datos Personales</h4>
                <p><span>Nombre completo:</span> ${f.nombre_completo}</p>
                <p><span>Padre:</span> ${f.nombre_padre ?? '—'}</p>
                <p><span>Madre:</span> ${f.nombre_madre ?? '—'}</p>
                <p><span>Fecha de nacimiento:</span> ${f.fecha_nacimiento ?? '—'}</p>
                <p><span>Lugar de nacimiento:</span> ${f.lugar_nacimiento ?? '—'}</p>

                <hr>

                <h4 style="color:#2980b9;">Bautismo</h4>
                <p><span>Registro:</span> ${f.bautismo_registro ?? '—'}</p>
                <p><span>Fecha:</span> ${f.bautismo_fecha ?? '—'}</p>

                <hr>

                <h4 style="color:#27ae60;">Catequesis</h4>
                <p><span>Tipo:</span> ${f.catequesis_tipo ?? '—'}</p>
                <p><span>Curso:</span> ${f.nombre_catequesis ?? '—'}</p>

                <hr>

                <h4 style="color:#f39c12;">Comunión</h4>
                <p><span>Fecha:</span> ${f.com_fecha ?? '—'}</p>

                <hr>

                <h4 style="color:#8e44ad;">Confirmación</h4>
                <p><span>Fecha:</span> ${f.conf_fecha ?? '—'}</p>

                <hr>

                <h4 style="color:#c0392b;">Matrimonio</h4>
                <p><span>Fecha:</span> ${f.mat_fecha ?? '—'}</p>
                <p><span>Lugar:</span> ${f.mat_lugar ?? '—'}</p>
            `;

            document.getElementById('modalVerFeligres').style.display = 'flex';
        })
        .catch(err => {
            console.error('[FELIGRES]', err);
            alert('No se pudo cargar la información del feligrés');
        });
}

function cerrarModalFeligres() {
    document.getElementById('modalVerFeligres').style.display = 'none';
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
