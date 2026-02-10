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
<div class="modal fade" id="modalParroquia">
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
const tabla = document.getElementById("tablaParroquias");
const modal = new bootstrap.Modal(document.getElementById('modalParroquia'));
let parroquiasGlobal = [];

function listar() {
    fetch('../api/parroquias/listar.php')
        .then(res => res.json())
        .then(data => {
            parroquiasGlobal = data;
            render(data);
        });
}

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
               
            </td>
        </tr>`;
    });

    tabla.innerHTML = html;
}

document.getElementById("buscador")
.addEventListener("input", function(){
    let texto = this.value.toLowerCase();
    let filtrado = parroquiasGlobal.filter(p =>
        p.nombre.toLowerCase().includes(texto)
    );
    render(filtrado);
});

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
        this.reset();
        listar();
    });
});

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

function eliminar(id){
    if(!confirm("¿Eliminar parroquia?")) return;

    fetch('../api/parroquias/eliminar.php?id='+id)
    .then(()=> listar());
}

listar();
</script>
<?php include 'footer.php'; ?>
 