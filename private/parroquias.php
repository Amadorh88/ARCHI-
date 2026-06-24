<?php include 'header.php'; ?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">
            <i class="bi bi-building"></i> Gestión de Parroquias
        </h4>

        <div class="d-flex gap-2">
            <input type="text" id="buscador" class="form-control" placeholder="Buscar parroquia..." style="width: 250px;">
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

<!-- MODAL -->
<div class="modal fade" id="modalParroquia" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form id="formParroquia">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-building me-2"></i>Registro de Parroquia
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <input type="hidden" name="id_parroquia" id="id_parroquia">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nombre *</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Teléfono</label>
                        <input type="text" name="telefono" id="telefono" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Dirección</label>
                        <input type="text" name="direccion" id="direccion" class="form-control">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
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
    
    // Variables de paginación
    let parroquiasGlobal = [];
    let parroquiasFiltradas = [];
    let paginaActual = 1;
    let registrosPorPagina = 10;
    let totalPaginas = 1;

    function limpiarModal() {
        document.body.classList.remove('modal-open');
        document.body.style = '';
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    }

    document.addEventListener("DOMContentLoaded", function() {
        const limiteGuardado = localStorage.getItem('parroquias_limite');
        if (limiteGuardado) {
            document.getElementById('limiteRegistros').value = limiteGuardado;
            registrosPorPagina = parseInt(limiteGuardado);
        }
        listar();
    });

    function cambiarLimite() {
        const select = document.getElementById('limiteRegistros');
        registrosPorPagina = parseInt(select.value);
        localStorage.setItem('parroquias_limite', registrosPorPagina);
        paginaActual = 1;
        actualizarPaginacion();
        renderizarPagina();
    }

    function listar() {
        fetch('../api/parroquias/listar.php')
            .then(res => res.json())
            .then(data => {
                parroquiasGlobal = data;
                parroquiasFiltradas = [...data];
                paginaActual = 1;
                actualizarPaginacion();
                renderizarPagina();
            });
    }

    function actualizarPaginacion() {
        totalPaginas = Math.ceil(parroquiasFiltradas.length / registrosPorPagina);
        if (totalPaginas === 0) totalPaginas = 1;
        
        if (paginaActual > totalPaginas) paginaActual = totalPaginas;
        
        const inicio = (paginaActual - 1) * registrosPorPagina + 1;
        const fin = Math.min(paginaActual * registrosPorPagina, parroquiasFiltradas.length);
        const total = parroquiasFiltradas.length;
        
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
        const paginaData = parroquiasFiltradas.slice(inicio, fin);
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

    function render(lista) {
        let html = '';

        if (lista.length === 0) {
            html = `<tr><td colspan="5" class="text-center text-muted">No hay parroquias registradas</td></tr>`;
        } else {
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
                </tr>
                `;
            });
        }
        tabla.innerHTML = html;
    }

    document.getElementById("buscador").addEventListener("input", function() {
        let texto = this.value.toLowerCase().trim();
        
        if (texto === '') {
            parroquiasFiltradas = [...parroquiasGlobal];
        } else {
            parroquiasFiltradas = parroquiasGlobal.filter(p =>
                p.nombre.toLowerCase().includes(texto)
            );
        }
        
        paginaActual = 1;
        actualizarPaginacion();
        renderizarPagina();
    });

    document.getElementById("formParroquia").addEventListener("submit", function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        const id = document.getElementById("id_parroquia").value;
        const nombre = document.getElementById("nombre").value;
        const esEdicion = id !== '';

        fetch('../api/parroquias/guardar.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (esEdicion) {
                    registrarActividad(`EDITÓ PARROQUIA: ${nombre}`, 'PARROQUIAS', `ID: ${id}`);
                } else {
                    registrarActividad(`CREÓ PARROQUIA: ${nombre}`, 'PARROQUIAS');
                }
                
                modal.hide();
                setTimeout(() => { limpiarModal(); }, 300);
                this.reset();
                listar();
                Swal.fire('Éxito', 'Parroquia guardada correctamente', 'success');
            } else {
                Swal.fire('Error', data.error || 'No se pudo guardar', 'error');
            }
        })
        .catch(() => {
            Swal.fire('Error', 'No se pudo guardar', 'error');
        });
    });

    function editar(id) {
        fetch('../api/parroquias/ver.php?id=' + id)
            .then(res => res.json())
            .then(p => {
                document.getElementById("id_parroquia").value = p.id_parroquia;
                document.getElementById("nombre").value = p.nombre;
                document.getElementById("direccion").value = p.direccion;
                document.getElementById("telefono").value = p.telefono;
                modal.show();
            });
    }

    function eliminarParroquia(id) {
        const parroquia = parroquiasGlobal.find(p => p.id_parroquia == id);
        const nombre = parroquia ? parroquia.nombre : 'ID:' + id;
        
        Swal.fire({
            title: '¿Eliminar parroquia?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('../api/parroquias/eliminar.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${id}`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        registrarActividad(`ELIMINÓ PARROQUIA: ${nombre}`, 'PARROQUIAS');
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

    modalEl.addEventListener('hidden.bs.modal', limpiarModal);
    listar();
</script>

<?php include 'footer.php'; ?>