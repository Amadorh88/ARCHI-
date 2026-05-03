<?php include 'header.php'; ?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">
            <i class="bi bi-person-badge"></i> Gestión de Ministros
        </h4>

        <div class="d-flex gap-2">
            <input type="text" id="buscador" class="form-control" placeholder="Buscar ministro...">
           
           <?php if (($rolUsuario !== 'archivista')): ?>
             <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalMinistro">
                <i class="bi bi-plus-circle"></i> Nuevo
            </button>
             <?php endif; ?>

        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>DIP</th>
                        <th>Teléfono</th>
                        <th>Tipo</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaMinistros"></tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="modalMinistro">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="formMinistro">
                <div class="modal-header">
                    <h5 class="modal-title">Ministro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body row g-3">

                    <input type="hidden" name="id_ministro" id="id_ministro">

                    <div class="col-md-6">
                        <label class="form-label">Nombre completo *</label>
                        <input type="text" name="nombre_completo" id="nombre_completo" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">DIP</label>
                        <input type="text" name="DIP" id="DIP" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" id="telefono" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Tipo *</label>
                        <select name="tipo" id="tipo" class="form-select" required>
                            <option value="">Seleccione</option>
                            <option value="Sacerdote">Sacerdote</option>
                            <option value="Diácono">Diácono</option>
                            <option value="Obispo">Obispo</option>
                            <option value="Catequista">Catequista</option>
                        </select>
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


const tabla = document.getElementById("tablaMinistros");
// Seleccionamos el elemento del DOM primero
const modalElement = document.getElementById('modalMinistro');
const modal = new bootstrap.Modal(modalElement);
let ministrosGlobal = [];

// Función para limpiar el rastro oscuro de Bootstrap
/* function cerrarModalCorrectamente() {
    modal.hide();
    const backdrop = document.querySelector('.modal-backdrop');
    if (backdrop) {
        backdrop.remove();
    }
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
} */

function cerrarModalCorrectamente() {
    const modalInstance = bootstrap.Modal.getInstance(modalElement);
    if (modalInstance) {
        modalInstance.hide();
    }
}

function listar(){
    fetch('../api/ministros/listar.php')
    .then(res=>res.json())
    .then(data=>{
        ministrosGlobal = data;
        render(data);
    });
}

function render(lista){
    let html = '';
    if(lista.length === 0){
        html = `<tr><td colspan="6" class="text-center text-muted">No hay ministros registrados</td></tr>`;
    }

    lista.forEach(m=>{
        html += `
        <tr>
            <td>${m.id_ministro}</td>
            <td>${m.nombre_completo}</td>
            <td>${m.DIP ?? ''}</td>
            <td>${m.telefono ?? ''}</td>
            <td>${m.tipo}</td>
            <td class="text-end">
                <button class="btn btn-sm btn-primary" onclick="editar(${m.id_ministro})">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-danger" onclick="eliminarMinistro(${m.id_ministro})">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>`;
    });
    tabla.innerHTML = html;
}

document.getElementById("buscador").addEventListener("input", function(){
    let texto = this.value.toLowerCase();
    let filtrado = ministrosGlobal.filter(m =>
        m.nombre_completo.toLowerCase().includes(texto) ||
        (m.DIP ?? '').toLowerCase().includes(texto) ||
        m.tipo.toLowerCase().includes(texto)
    );
    render(filtrado);
});

// CORRECCIÓN AQUÍ: Manejo del guardado
document.getElementById("formMinistro").addEventListener("submit", function(e){
    e.preventDefault();
    let formData = new FormData(this);

    fetch('../api/ministros/guardar.php',{
        method:'POST',
        body:formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.success || data) { // Ajusta según lo que devuelva tu API
            cerrarModalCorrectamente(); // Limpia la opacidad
            this.reset();
            listar();
        } else {
            alert("Error al guardar: " + (data.error || "Desconocido"));
        }
    })
    .catch(err => {
        console.error(err);
        cerrarModalCorrectamente(); // Incluso en error, liberamos la pantalla
    });
});

function editar(id){
    fetch('../api/ministros/ver.php?id='+id)
    .then(res=>res.json())
    .then(m=>{
        document.getElementById('id_ministro').value = m.id_ministro;
        document.getElementById('nombre_completo').value = m.nombre_completo;
        document.getElementById('DIP').value = m.DIP;
        document.getElementById('telefono').value = m.telefono;
        document.getElementById('tipo').value = m.tipo;
        modal.show();
    });
}

function eliminarMinistro(id) {
    Swal.fire({
        title: '¿Eliminar ministro?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('../api/ministros/eliminar.php', { // Corregida la ruta si es necesario
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${id}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Eliminado', data.message, 'success');
                    listar(); // Mejor usar listar() que recargar toda la página
                } else {
                    Swal.fire('Error', data.error, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error', 'Error del servidor', 'error');
                console.error(error);
            });
        }
    });
}
modalElement.addEventListener('hidden.bs.modal', () => {
    document.body.classList.remove('modal-open');
    document.body.style = '';
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
});
listar();
</script>


<?php include 'footer.php'; ?>
 