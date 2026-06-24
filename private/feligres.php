<?php
include 'header.php';
registrarAccesoModulo(__FILE__);
?>

<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
        <h4 class="fw-bold mb-0">
            <i class="bi bi-person-vcard"></i> Gestión de Feligreses
        </h4>

        <div class="d-flex gap-2 w-100 w-md-auto">
            <div class="input-group shadow-sm">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" id="buscadorFeligres" class="form-control border-start-0"
                    placeholder="Buscar en el archivo...">
            </div>

            <?php if ($puede_crear): ?>
                <button class="btn btn-primary shadow-sm px-4" onclick="nuevoFeligres()">
                    <i class="bi bi-plus-circle me-1"></i> Nuevo Registro
                </button>
            <?php endif; ?>
        </div>
    </div>
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Nombre</th>
                        <th>Fecha Nac.</th>
                        <th>Lugar</th>
                        <th>Padre</th>
                        <th>Madre</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaFeligres"></tbody>
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

<?php if (!$es_solo_lector): ?>
    <div class="modal fade" id="modalFeligres" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <form id="formFeligres">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-person-lines-fill me-2"></i>Ficha del Feligrés
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body row g-3">
                        <input type="hidden" name="id_feligres" id="id_feligres">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nombre completo <span class="text-danger"></span></label>
                            <input type="text" name="nombre_completo" id="nombre_completo" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Género <span class="text-danger"></span></label>
                            <select name="genero" id="genero" class="form-select" required>
                                <option value="">Seleccionar...</option>
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Fecha nacimiento</label>
                            <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Lugar nacimiento</label>
                            <input type="text" name="lugar_nacimiento" id="lugar_nacimiento" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nombre del Padre</label>
                            <input type="text" name="nombre_padre" id="nombre_padre" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nombre de la Madre</label>
                            <input type="text" name="nombre_madre" id="nombre_madre" class="form-control">
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
<?php endif; ?>

<div class="modal fade" id="modalPerfil" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="bi bi-person-badge me-2"></i>Perfil Canónico</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="perfilContenido"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalHistorial" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">
                    <i class="bi bi-journal-richtext me-2"></i>Historial Integral
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="contenidoHistorial"></div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                <a id="btnImprimirHistorial" class="btn btn-dark" target="_blank">
                    <i class="bi bi-printer me-1"></i> Imprimir Reporte
                </a>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="toastLive" class="toast align-items-center border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMensaje"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const ROL_USUARIO = '<?php echo $rolUsuario; ?>';
    const PUEDE_EDITAR = <?php echo $puede_editar ? 'true' : 'false'; ?>;
    const PUEDE_ELIMINAR = <?php echo $puede_eliminar ? 'true' : 'false'; ?>;

    const tabla = document.getElementById("tablaFeligres");
    const modalF = document.getElementById('modalFeligres') ? new bootstrap.Modal(document.getElementById('modalFeligres')) : null;
    const modalP = new bootstrap.Modal(document.getElementById('modalPerfil'));
    const modalH = new bootstrap.Modal(document.getElementById('modalHistorial'));

    const toastElement = document.getElementById('toastLive');
    const toastMensaje = document.getElementById('toastMensaje');
    const toastBootstrap = new bootstrap.Toast(toastElement);

    // Variables de paginación
    let listaGlobal = [];
    let listaFiltrada = [];
    let paginaActual = 1;
    let registrosPorPagina = 10; // Valor por defecto
    let totalPaginas = 1;

    function mostrarToast(mensaje, tipo = "success") {
        const colores = {
            success: "bg-success text-white",
            error: "bg-danger text-white",
            info: "bg-primary text-white",
            warning: "bg-warning text-dark"
        };
        toastElement.className = `toast align-items-center border-0 ${colores[tipo] || colores.success}`;
        toastMensaje.innerHTML = mensaje;
        toastBootstrap.show();
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Cargar límite guardado en localStorage
        const limiteGuardado = localStorage.getItem('feligres_limite');
        if (limiteGuardado) {
            document.getElementById('limiteRegistros').value = limiteGuardado;
            registrosPorPagina = parseInt(limiteGuardado);
        }
        listarFeligres();
    });

    function cambiarLimite() {
        const select = document.getElementById('limiteRegistros');
        registrosPorPagina = parseInt(select.value);
        localStorage.setItem('feligres_limite', registrosPorPagina);
        paginaActual = 1;
        actualizarPaginacion();
        renderizarPagina();
    }

    function listarFeligres() {
        fetch('../api/feligres/listar.php')
            .then(res => res.json())
            .then(data => {
                listaGlobal = data;
                listaFiltrada = [...data];
                paginaActual = 1;
                actualizarPaginacion();
                renderizarPagina();
            })
            .catch(err => mostrarToast("Error al sincronizar datos", "error"));
    }

    function actualizarPaginacion() {
        totalPaginas = Math.ceil(listaFiltrada.length / registrosPorPagina);
        if (totalPaginas === 0) totalPaginas = 1;
        
        if (paginaActual > totalPaginas) paginaActual = totalPaginas;
        
        const inicio = (paginaActual - 1) * registrosPorPagina + 1;
        const fin = Math.min(paginaActual * registrosPorPagina, listaFiltrada.length);
        const total = listaFiltrada.length;
        
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
        const paginaData = listaFiltrada.slice(inicio, fin);
        renderTabla(paginaData);
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

    function renderTabla(lista) {
        let html = '';
        if (lista.length === 0) {
            html = `<td><td colspan="8" class="text-center text-muted py-4">No hay registros disponibles</td></tr>`;
        } else {
            lista.forEach(f => {
                let btnEditar = PUEDE_EDITAR ?
                    `<button class="btn btn-sm btn-outline-primary" onclick="editar(${f.id_feligres})" title="Editar"><i class="bi bi-pencil"></i></button>` : '';
                
                let btnEliminar = PUEDE_ELIMINAR ?
                    `<button class="btn btn-sm btn-outline-danger" onclick="eliminarFeligres(${f.id_feligres})" title="Eliminar"><i class="bi bi-trash"></i></button>` : '';

                html += `
                    <tr>
                        <td><span class="badge bg-light text-dark border">${f.id_feligres}</span></td>
                        <td class="fw-semibold">${escapeHtml(f.nombre_completo)}</td>
                        <td>${f.fecha_nacimiento ?? '-'}</td>
                        <td>${escapeHtml(f.lugar_nacimiento ?? '-')}</td>
                        <td>${escapeHtml(f.nombre_padre ?? '-')}</td>
                        <td>${escapeHtml(f.nombre_madre ?? '-')}</td>
                        <td class="text-end">
                            <div class="btn-group shadow-sm">
                                ${btnEditar}
                                <button class="btn btn-sm btn-outline-info" onclick="verPerfil(${f.id_feligres})" title="Ver Perfil"><i class="bi bi-eye"></i></button>
                                <button class="btn btn-sm btn-outline-dark" onclick="verHistorial(${f.id_feligres})" title="Historial"><i class="bi bi-person-vcard"></i></button>
                                <button class="btn btn-sm btn-outline-secondary" onclick="imprimir(${f.id_feligres})" title="Imprimir"><i class="bi bi-printer"></i></button>
                                ${btnEliminar}
                            </div>
                        </td>
                    </tr>
                `;
            });
        }
        tabla.innerHTML = html;
    }

    function escapeHtml(text) {
        if (!text) return text;
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    document.getElementById("buscadorFeligres").addEventListener("input", function () {
        let texto = this.value.toLowerCase().trim();
        
        if (texto === '') {
            listaFiltrada = [...listaGlobal];
        } else {
            listaFiltrada = listaGlobal.filter(f =>
                f.nombre_completo.toLowerCase().includes(texto) ||
                (f.lugar_nacimiento && f.lugar_nacimiento.toLowerCase().includes(texto)) ||
                (f.nombre_padre && f.nombre_padre.toLowerCase().includes(texto)) ||
                (f.nombre_madre && f.nombre_madre.toLowerCase().includes(texto))
            );
        }
        
        paginaActual = 1;
        actualizarPaginacion();
        renderizarPagina();
    });

    function nuevoFeligres() {
        if (!modalF) return;
        document.getElementById("formFeligres").reset();
        document.getElementById("id_feligres").value = "";
        modalF.show();
    }

    if (document.getElementById("formFeligres")) {
        document.getElementById("formFeligres").addEventListener("submit", function (e) {
            e.preventDefault();
            
            const genero = document.getElementById("genero").value;
            if (!genero) {
                mostrarToast("Por favor seleccione el género", "warning");
                return;
            }
            
            let formData = new FormData(this);
            const id = document.getElementById("id_feligres").value;
            const nombre = document.getElementById("nombre_completo").value;
            const esEdicion = id !== '';
            
            fetch('../api/feligres/guardar.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.error) throw new Error(data.error);
                    
                    if (esEdicion) {
                        registrarActividad(`EDITÓ FELIGRÉS: ${nombre}`, 'FELIGRESES', `ID: ${id}`);
                    } else {
                        registrarActividad(`CREÓ FELIGRÉS: ${nombre}`, 'FELIGRESES', `ID: ${data.id || 'nuevo'}`);
                    }
                    
                    modalF.hide();
                    listarFeligres();
                    mostrarToast(data.message || "Sistema actualizado correctamente");
                })
                .catch(err => mostrarToast(err.message, "error"));
        });
    }

    function editar(id) {
        if (!PUEDE_EDITAR) return;
        fetch('../api/feligres/ver.php?id=' + id)
            .then(res => res.json())
            .then(f => {
                document.getElementById("id_feligres").value = f.id_feligres;
                document.getElementById("nombre_completo").value = f.nombre_completo;
                document.getElementById("genero").value = f.genero || '';
                document.getElementById("fecha_nacimiento").value = f.fecha_nacimiento;
                document.getElementById("lugar_nacimiento").value = f.lugar_nacimiento;
                document.getElementById("nombre_padre").value = f.nombre_padre;
                document.getElementById("nombre_madre").value = f.nombre_madre;
                modalF.show();
            })
            .catch(err => mostrarToast("Error al cargar datos", "error"));
    }

    function verPerfil(id) {
        fetch('../api/feligres/perfil.php?id=' + id)
            .then(res => res.json())
            .then(data => {
                let f = data.feligres;
                let generoIcon = f.genero === 'Masculino' ? 
                    '<i class="bi bi-gender-male text-primary"></i>' : 
                    '<i class="bi bi-gender-female text-danger"></i>';
                
                let html = `
                    <div class="text-center mb-3">
                        <div class="display-6 text-primary">${generoIcon}</div>
                        <h5 class="fw-bold mb-0">${escapeHtml(f.nombre_completo)}</h5>
                        <small class="text-muted">Expediente #${f.id_feligres}</small>
                    </div>
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item"><strong>Género:</strong> ${f.genero ?? '-'}</li>
                        <li class="list-group-item"><strong>Nacimiento:</strong> ${f.fecha_nacimiento ?? '-'}</li>
                        <li class="list-group-item"><strong>Lugar nacimiento:</strong> ${escapeHtml(f.lugar_nacimiento ?? '-')}</li>
                        <li class="list-group-item"><strong>Padre:</strong> ${escapeHtml(f.nombre_padre ?? '-')}</li>
                        <li class="list-group-item"><strong>Madre:</strong> ${escapeHtml(f.nombre_madre ?? '-')}</li>
                    </ul>`;
                document.getElementById("perfilContenido").innerHTML = html;
                modalP.show();
            })
            .catch(err => mostrarToast("Error al cargar perfil", "error"));
    }

    function verHistorial(id) {
        const contenedor = document.getElementById('contenidoHistorial');
        contenedor.innerHTML = `<div class="text-center p-4"><div class="spinner-border text-primary"></div></div>`;
        modalH.show();
        fetch('../api/feligres/historial.php?id=' + id)
            .then(res => res.json())
            .then(data => {
                document.getElementById('btnImprimirHistorial').href = '../report/imprimir_reporte.php?id=' + data.feligres.id_feligres;
                contenedor.innerHTML = `
                    <div class="row g-3">
                        <div class="col-md-12 border-bottom pb-2">
                            <h4 class="fw-bold">${escapeHtml(data.feligres.nombre_completo)}</h4>
                            <p class="text-muted">Género: ${data.feligres.genero ?? '-'}</p>
                        </div>
                        <div class="col-md-4">${data.sacramentos}</div>
                        <div class="col-md-8">${data.catequesis} ${data.donaciones}</div>
                    </div>`;
            })
            .catch(err => mostrarToast("Error al cargar historial", "error"));
    }

    function imprimir(id) {
        window.open('../report/imprimir_sacramentos.php?id=' + id, '_blank');
    }

    function eliminarFeligres(id) {
        const feligres = listaGlobal.find(f => f.id_feligres == id);
        const nombre = feligres ? feligres.nombre_completo : 'ID:' + id;
        
        Swal.fire({
            title: '¿Eliminar feligrés?',
            text: 'El feligrés será eliminado',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                mostrarToast("Eliminando feligrés...", "warning");
                
                fetch('../api/feligres/eliminar.php?id=' + id)
                    .then(res => res.json())
                    .then(data => {
                        if (data.error) {
                            mostrarToast(data.error, "error");
                            return;
                        }
                        
                        registrarActividad(`ELIMINÓ FELIGRÉS: ${nombre}`, 'FELIGRESES', `ID: ${id}`);
                        listarFeligres();
                        mostrarToast(data.message, "success");
                    })
                    .catch(error => {
                        mostrarToast("Error al eliminar feligrés", "error");
                        console.error(error);
                    });
            }
        });
    }
</script>
<?php include 'footer.php'; ?>