<?php
include 'header.php';
registrarAccesoModulo(__FILE__);
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">
            <i class="bi bi-person-badge"></i> Gestión de Ministros
        </h4>

        <div class="d-flex gap-2">
            <input type="text" id="buscador" class="form-control" placeholder="Buscar ministro..." style="width: 250px;">
           
           <?php if (($rolUsuario !== 'archivista')): ?>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalMinistro" onclick="document.getElementById('formMinistro').reset(); document.getElementById('id_ministro').value='';">
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
        
        <!-- PAGINACIÓN con selector de límite -->
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center gap-2">
                    <label class="text-muted small mb-0">Mostrar:</label>
                    <select id="limiteRegistros" class="form-select form-select-sm w-auto" onchange="cambiarLimite()">
                        <option value="3">3</option>
                        <option value="5" selected>5</option>
                        <option value="7">7</option>
                        
                    </select>
                    <span class="text-muted small">registros por página</span>
                </div>
                <div class="text-muted small">
                    Mostrando <span id="infoDesde">0</span> - <span id="infoHasta">0</span> de <span id="infoTotal">0</span> registros
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary btn-sm" id="btnAnterior" onclick="paginaAnterior()" disabled>
                        <i class="bi bi-chevron-left"></i> Anterior
                    </button>
                    <span class="badge bg-primary d-flex align-items-center px-3" id="paginaActual">Página 1</span>
                    <button class="btn btn-outline-primary btn-sm" id="btnSiguiente" onclick="paginaSiguiente()" disabled>
                        Siguiente <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalMinistro" tabindex="-1">
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const tabla = document.getElementById("tablaMinistros");
    const modalElement = document.getElementById('modalMinistro');
    const modal = new bootstrap.Modal(modalElement);
    
    // Variables de paginación
    let ministrosGlobal = [];
    let ministrosFiltrados = [];
    let paginaActual = 1;
    let registrosPorPagina = 10;
    let totalPaginas = 1;

    function cerrarModalCorrectamente() {
        const modalInstance = bootstrap.Modal.getInstance(modalElement);
        if (modalInstance) {
            modalInstance.hide();
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        const limiteGuardado = localStorage.getItem('ministros_limite');
        if (limiteGuardado) {
            document.getElementById('limiteRegistros').value = limiteGuardado;
            registrosPorPagina = parseInt(limiteGuardado);
        }
        listar();
    });

    function cambiarLimite() {
        const select = document.getElementById('limiteRegistros');
        registrosPorPagina = parseInt(select.value);
        localStorage.setItem('ministros_limite', registrosPorPagina);
        paginaActual = 1;
        actualizarPaginacion();
        renderizarPagina();
    }

    function listar(){
        fetch('../api/ministros/listar.php')
            .then(res => res.json())
            .then(data => {
                ministrosGlobal = data;
                ministrosFiltrados = [...data];
                paginaActual = 1;
                actualizarPaginacion();
                renderizarPagina();
            });
    }

    function actualizarPaginacion() {
        totalPaginas = Math.ceil(ministrosFiltrados.length / registrosPorPagina);
        if (totalPaginas === 0) totalPaginas = 1;
        
        if (paginaActual > totalPaginas) paginaActual = totalPaginas;
        
        const inicio = (paginaActual - 1) * registrosPorPagina + 1;
        const fin = Math.min(paginaActual * registrosPorPagina, ministrosFiltrados.length);
        const total = ministrosFiltrados.length;
        
        document.getElementById('infoDesde').innerText = total > 0 ? inicio : 0;
        document.getElementById('infoHasta').innerText = fin;
        document.getElementById('infoTotal').innerText = total;
        document.getElementById('paginaActual').innerHTML = `Página ${paginaActual} de ${totalPaginas}`;
        
        document.getElementById('btnAnterior').disabled = (paginaActual <= 1);
        document.getElementById('btnSiguiente').disabled = (paginaActual >= totalPaginas);
    }

    function renderizarPagina() {
        const inicio = (paginaActual - 1) * registrosPorPagina;
        const fin = inicio + registrosPorPagina;
        const paginaData = ministrosFiltrados.slice(inicio, fin);
        render(paginaData);
    }

    function paginaAnterior() {
        if (paginaActual > 1) {
            paginaActual--;
            renderizarPagina();
            actualizarPaginacion();
        }
    }

    function paginaSiguiente() {
        if (paginaActual < totalPaginas) {
            paginaActual++;
            renderizarPagina();
            actualizarPaginacion();
        }
    }

    function render(lista){
        let html = '';
        if(lista.length === 0){
            html = `<tr><td colspan="6" class="text-center text-muted">No hay ministros registrados</td></tr>`;
        } else {
            lista.forEach(m => {
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
                        <?php if (($rolUsuario !== 'archivista')): ?>
                        <button class="btn btn-sm btn-danger" onclick="eliminarMinistro(${m.id_ministro})">
                            <i class="bi bi-trash"></i>
                        </button>
                        <?php endif; ?>
                    </td>
                </table>
                `;
            });
        }
        tabla.innerHTML = html;
    }

    document.getElementById("buscador").addEventListener("input", function(){
        let texto = this.value.toLowerCase().trim();
        
        if (texto === '') {
            ministrosFiltrados = [...ministrosGlobal];
        } else {
            ministrosFiltrados = ministrosGlobal.filter(m =>
                m.nombre_completo.toLowerCase().includes(texto) ||
                (m.DIP ?? '').toLowerCase().includes(texto) ||
                m.tipo.toLowerCase().includes(texto)
            );
        }
        
        paginaActual = 1;
        actualizarPaginacion();
        renderizarPagina();
    });

    document.getElementById("formMinistro").addEventListener("submit", function(e){
        e.preventDefault();
        let formData = new FormData(this);
        const id = document.getElementById('id_ministro').value;
        const nombre = document.getElementById('nombre_completo').value;
        const tipo = document.getElementById('tipo').value;
        const esEdicion = id !== '';

        fetch('../api/ministros/guardar.php',{
            method:'POST',
            body:formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                if (esEdicion) {
                    registrarActividad(`EDITÓ MINISTRO: ${nombre}`, 'MINISTROS', `Tipo: ${tipo}`);
                } else {
                    registrarActividad(`CREÓ MINISTRO: ${nombre}`, 'MINISTROS', `Tipo: ${tipo}`);
                }
                
                cerrarModalCorrectamente(); 
                this.reset();
                listar();
                Swal.fire({ icon: 'success', title: '¡Operación Exitosa!', text: data.message, showConfirmButton: false, timer: 1500 });
            } else {
                Swal.fire({ icon: 'error', title: 'Error al guardar', text: data.error || "Ocurrió un problema inesperado." });
            }
        })
        .catch(err => {
            console.error(err);
            cerrarModalCorrectamente();
            Swal.fire({ icon: 'error', title: 'Error de servidor', text: 'No se pudo conectar con la base de datos.' });
        });
    });

    function editar(id){
        fetch('../api/ministros/ver.php?id='+id)
            .then(res => res.json())
            .then(m => {
                document.getElementById('id_ministro').value = m.id_ministro;
                document.getElementById('nombre_completo').value = m.nombre_completo;
                document.getElementById('DIP').value = m.DIP;
                document.getElementById('telefono').value = m.telefono;
                document.getElementById('tipo').value = m.tipo;
                modal.show();
            });
    }

    function eliminarMinistro(id) {
        const ministro = ministrosGlobal.find(m => m.id_ministro == id);
        const nombre = ministro ? ministro.nombre_completo : 'ID:' + id;
        
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
                fetch('../api/ministros/eliminar.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${id}`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        registrarActividad(`ELIMINÓ MINISTRO: ${nombre}`, 'MINISTROS');
                        Swal.fire('Eliminado', data.message, 'success');
                        listar();
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