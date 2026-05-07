<?php include 'header.php'; ?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">
            <i class="bi bi-building"></i> Gestión de Parroquias
        </h4>

        <div class="d-flex gap-2">
            <input type="text" id="buscador" class="form-control" placeholder="Buscar parroquia...">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalParroquia">
                <i class="bi bi-plus-circle"></i> Nueva
            </button>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Dirección</th>
                        <th>Teléfono</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaParroquias"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL -->
<!-- <div class="modal fade" id="modalParroquia" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="formParroquia">
                <div class="modal-header">
                    <h5 class="modal-title">Parroquia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body row g-3">
                    <input type="hidden" name="id_parroquia" id="id_parroquia">

                    <div class="col-md-6">
                        <label class="form-label">Nombre *</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" id="telefono" class="form-control">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Dirección</label>
                        <input type="text" name="direccion" id="direccion" class="form-control">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
 -->


 <div class="modal fade" id="modalParroquia" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">

            <form id="formParroquia">

                <!-- HEADER -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-building me-2"></i>Registro de Parroquia
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- BODY -->
                <div class="modal-body row g-3">

                    <input type="hidden" name="id_parroquia" id="id_parroquia">

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nombre *</label>
                        <input 
                            type="text" 
                            name="nombre" 
                            id="nombre" 
                            class="form-control" 
                            required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Teléfono</label>
                        <input 
                            type="text" 
                            name="telefono" 
                            id="telefono" 
                            class="form-control">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold">Dirección</label>
                        <input 
                            type="text" 
                            name="direccion" 
                            id="direccion" 
                            class="form-control">
                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save me-1"></i> Guardar Cambios
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
const tabla = document.getElementById("tablaParroquias");
const modalEl = document.getElementById('modalParroquia');
const modal = new bootstrap.Modal(modalEl);

let parroquiasGlobal = [];

/* 🔧 LIMPIAR BACKDROP (SOLUCIÓN CLAVE) */
function limpiarModal() {
    document.body.classList.remove('modal-open');
    document.body.style = '';

    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
}

/* LISTAR */
function listar() {
    fetch('../api/parroquias/listar.php')
        .then(res => res.json())
        .then(data => {
            parroquiasGlobal = data;
            render(data);
        });
}

/* RENDER */
function render(lista) {
    let html = '';

    if(lista.length === 0){
        html = `<tr>
                    <td colspan="5" class="text-center text-muted">
                        No hay parroquias registradas
                    </td>
                </tr>`;
    }

    lista.forEach(p => {
        html += `
        <tr>
            <td>${p.id_parroquia}</td>
            <td>${p.nombre}</td>
            <td>${p.direccion ?? ''}</td>
            <td>${p.telefono ?? ''}</td>
            <td class="text-end">
                <button class="btn btn-sm btn-primary" onclick="editar(${p.id_parroquia})">
                    <i class="bi bi-pencil"></i>
                </button>

                <button class="btn btn-sm btn-danger" onclick="eliminarParroquia(${p.id_parroquia})">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>`;
    });

    tabla.innerHTML = html;
}

/* BUSCADOR */
document.getElementById("buscador")
.addEventListener("input", function(){
    let texto = this.value.toLowerCase();
    let filtrado = parroquiasGlobal.filter(p =>
        p.nombre.toLowerCase().includes(texto)
    );
    render(filtrado);
});

/* GUARDAR */
document.getElementById("formParroquia")
.addEventListener("submit", function(e){
    e.preventDefault();
    let formData = new FormData(this);

    fetch('../api/parroquias/guardar.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {

        modal.hide();

        setTimeout(() => {
            limpiarModal();
        }, 300);

        this.reset();
        listar();

        Swal.fire('Éxito', 'Parroquia guardada correctamente', 'success');
    })
    .catch(() => {
        Swal.fire('Error', 'No se pudo guardar', 'error');
    });
});

/* EDITAR */
function editar(id){
    fetch('../api/parroquias/ver.php?id='+id)
    .then(res=>res.json())
    .then(p=>{
        id_parroquia.value = p.id_parroquia;
        nombre.value = p.nombre;
        direccion.value = p.direccion;
        telefono.value = p.telefono;
        modal.show();
    });
}

/* ELIMINAR */
function eliminarParroquia(id) {
    Swal.fire({
        title: '¿Eliminar parroquia?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {

        if (result.isConfirmed) {

            fetch('../api/parroquias/eliminar.php', {   // ✅ RUTA CORREGIDA
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `id=${id}`
            })
            .then(res => res.json())
            .then(data => {

                if (data.success) {
                    Swal.fire('Eliminado', data.message, 'success');
                    listar();
                } else {
                    Swal.fire('Error', data.error, 'error');
                }

            })
            .catch(() => {
                Swal.fire('Error', 'Error del servidor', 'error');
            });
        }
    });
}

/* 🔥 EVENTO GLOBAL AL CERRAR MODAL */
modalEl.addEventListener('hidden.bs.modal', limpiarModal);

/* INIT */
listar();
</script>

<?php include 'footer.php'; ?>