<?php
include 'header.php';
registrarAccesoModulo(__FILE__);
?>

<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-0">
                <i class="bi bi-heart-pulse text-danger"></i> Gestión de donaciones
            </h4>
            <p class="text-muted small mb-0">Tesorería Parroquial | Moneda: **FCFA**</p>
        </div>

        <div class="d-flex gap-2 w-100 w-md-auto">
            <div class="input-group">
                <span class="input-group-text bg-light border-0 shadow-sm">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="buscador" class="form-control border-0 shadow-sm"
                    placeholder="Buscar por feligrés o concepto...">
            </div>
            <!--
            <?php if (($rolUsuario !== 'archivista')): ?>
                <button class="btn btn-primary shadow-sm px-4" onclick="abrirModalNuevo()">
                    <i class="bi bi-plus-lg"></i>
                    <span class="d-none d-md-inline">Nueva Donación</span>
                </button>
            <?php endif; ?>
            -->
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <h6 class="text-white-50 small mb-1">Ingresos Totales</h6>
                    <h3 class="fw-bold mb-0"><span id="totalMes">0</span> FCFA</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>ID</th>
                        <th>Feligrés</th>
                        <th>Concepto</th>
                        <th>Monto</th>
                        <th>Fecha</th>
                        <?php if (($rolUsuario !== 'archivista')): ?>
                        <th class="text-end">Acciones</th>
                         <?php endif; ?>
                    </tr>
                </thead>
                <tbody id="tablaOfrendas"></tbody>
            </table>
        </div>
        
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

<!-- Modal de formulario de registro -->
<div class="modal fade" id="modalOfrenda" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form id="formOfrenda">
                <div class="modal-header bg-dark text-white border-0">
                    <h5 class="modal-title"><i class="bi bi-cash-stack me-2"></i>Registro de Tesorería</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 row g-3">
                    <input type="hidden" name="id_pago" id="id_pago">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Feligrés (Opcional)</label>
                        <select name="id_feligres" id="id_feligres" class="form-select border-primary shadow-sm">
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Concepto </label>
                        <input type="text" name="concepto" id="concepto" class="form-control" required
                            placeholder="Ej: Catequesis, Sacramento...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Monto Ofrenda </label>
                        <div class="input-group">
                            <input type="number" name="cantidad" id="cantidad" class="form-control" required oninput="calcularCambio()" step="1">
                            <span class="input-group-text">FCFA</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-success">Monto Recibido </label>
                        <div class="input-group">
                            <input type="number" name="recibido" id="recibido" class="form-control" required oninput="calcularCambio()" step="1">
                            <span class="input-group-text">FCFA</span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="p-3 bg-light rounded d-flex justify-content-between align-items-center border border-primary">
                            <span class="fw-bold text-muted small">CAMBIO A DEVOLVER:</span>
                            <span class="h4 mb-0 fw-bold text-primary" id="vueltoText">0 FCFA</span>
                            <input type="hidden" name="cambio" id="cambio">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light p-3">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success px-4 shadow-sm fw-bold">
                        <i class="bi bi-save me-1"></i> Guardar Registro
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de notificación de éxito -->
<div class="modal fade" id="modalExito" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body text-center p-4">
                <div class="mb-3"><i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i></div>
                <h5 class="fw-bold mb-2">¡Registro Guardado!</h5>
                <p class="text-muted small mb-0">El registro se ha guardado correctamente</p>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4 pt-0">
                <button type="button" class="btn btn-success px-4" data-bs-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const tabla = document.getElementById("tablaOfrendas");
    const modal = new bootstrap.Modal(document.getElementById('modalOfrenda'));
    const modalExito = new bootstrap.Modal(document.getElementById('modalExito'));
    
    let ofrendasGlobal = [];
    let ofrendasFiltradas = [];
    let paginaActual = 1;
    let registrosPorPagina = 10;
    let totalPaginas = 1;
    
    let enviando = false;

    const formatCFA = (valor) => new Intl.NumberFormat('fr-FR').format(Math.round(valor || 0));

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    async function fetchJSON(url, options = {}) {
        try {
            const res = await fetch(url, options);
            if (!res.ok) throw new Error(`HTTP Error: ${res.status}`);
            return await res.json();
        } catch (err) {
            console.error("Error en Fetch:", err);
            return { error: true, message: err.message };
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        const limiteGuardado = localStorage.getItem('pagos_limite');
        if (limiteGuardado) {
            document.getElementById('limiteRegistros').value = limiteGuardado;
            registrosPorPagina = parseInt(limiteGuardado);
        }
        listarOfrendas();
    });

    function cambiarLimite() {
        const select = document.getElementById('limiteRegistros');
        registrosPorPagina = parseInt(select.value);
        localStorage.setItem('pagos_limite', registrosPorPagina);
        paginaActual = 1;
        actualizarPaginacion();
        renderizarPagina();
    }

    function listarOfrendas() {
        fetch('../api/ofrendas/listar.php')
            .then(res => res.json())
            .then(data => {
                if (data.error) return;
                ofrendasGlobal = data;
                ofrendasFiltradas = [...data];
                paginaActual = 1;
                actualizarPaginacion();
                renderizarPagina();
                actualizarTotalMes(data);
            })
            .catch(error => console.error("Error listarOfrendas:", error));
    }

    function actualizarTotalMes(lista) {
        const total = lista.reduce((acc, curr) => acc + parseFloat(curr.cantidad || 0), 0);
        document.getElementById('totalMes').innerText = formatCFA(total);
    }

    function actualizarPaginacion() {
        totalPaginas = Math.ceil(ofrendasFiltradas.length / registrosPorPagina);
        if (totalPaginas === 0) totalPaginas = 1;
        
        if (paginaActual > totalPaginas) paginaActual = totalPaginas;
        
        const inicio = (paginaActual - 1) * registrosPorPagina + 1;
        const fin = Math.min(paginaActual * registrosPorPagina, ofrendasFiltradas.length);
        const total = ofrendasFiltradas.length;
        
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
        const paginaData = ofrendasFiltradas.slice(inicio, fin);
        renderOfrendas(paginaData);
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

    function renderOfrendas(lista) {
        let html = '';
        if (lista.length === 0) {
            html = '<tr><td colspan="6" class="text-center py-4">Sin registros</td></tr>';
        } else {
            lista.forEach(o => {
                html += `
                <tr>
                    <td><span class="text-muted small">#${o.id_pago}</span></td>
                    <td><i class="bi bi-person-circle me-2 text-primary"></i>${escapeHtml(o.feligres_nombre || 'Anónimo')}</td>
                    <td><span class="badge bg-light text-dark border">${escapeHtml(o.concepto)}</span></td>
                    <td class="fw-bold">${formatCFA(o.cantidad)} FCFA</td>
                    <td class="small text-muted">${o.fecha || 'N/A'}</td>
                    <?php if (($rolUsuario !== 'archivista')): ?>
                    <td class="text-end text-nowrap">
                        <button class="btn btn-sm btn-outline-primary me-1" onclick="editar(${o.id_pago})">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="eliminarPago(${o.id_pago})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                    <?php endif; ?>
                </tr>
                `;
            });
        }
        tabla.innerHTML = html;
    }

    function calcularCambio() {
        const cant = parseInt(document.getElementById('cantidad').value) || 0;
        const rec = parseInt(document.getElementById('recibido').value) || 0;
        const cambio = rec - cant;
        const display = document.getElementById('vueltoText');

        display.innerText = `${formatCFA(Math.max(0, cambio))} FCFA`;
        display.className = cambio < 0 ? "h4 mb-0 fw-bold text-danger" : "h4 mb-0 fw-bold text-primary";
        document.getElementById('cambio').value = Math.max(0, cambio);
    }

    function abrirModalNuevo() {
        document.getElementById("formOfrenda").reset();
        document.getElementById("id_pago").value = '';
        document.getElementById("vueltoText").innerText = "0 FCFA";
        document.getElementById("cambio").value = '0';
        cargarFeligreses();
        modal.show();
    }

    async function cargarFeligreses() {
        try {
            const data = await fetchJSON('../api/feligres/listar.php');
            let options = '<option value="">-- Donación Anónima --</option>';
            if (!data.error && Array.isArray(data)) {
                data.forEach(f => {
                    options += `<option value="${f.id_feligres}">${escapeHtml(f.nombre_completo)}</option>`;
                });
            }
            document.getElementById('id_feligres').innerHTML = options;
        } catch (error) {
            console.error("Error cargando feligreses:", error);
        }
    }

    function editar(id) {
        fetch(`../api/ofrendas/ver.php?id=${id}`)
            .then(res => res.json())
            .then(o => {
                if (o.error) return;
                document.getElementById("id_pago").value = o.id_pago;
                document.getElementById("concepto").value = o.concepto;
                document.getElementById("cantidad").value = Math.round(o.cantidad);
                document.getElementById("recibido").value = Math.round(o.recibido);

                cargarFeligreses().then(() => {
                    document.getElementById("id_feligres").value = o.id_feligres || "";
                    calcularCambio();
                    modal.show();
                });
            })
            .catch(error => console.error("Error editar:", error));
    }

    document.getElementById("formOfrenda").addEventListener("submit", function(e) {
        e.preventDefault();
        
        if (enviando) {
            Swal.fire('Procesando', 'Ya se está procesando esta donación, por favor espere', 'warning');
            return;
        }
        
        const id = document.getElementById("id_pago").value;
        const concepto = document.getElementById("concepto").value.trim();
        const cantidad = document.getElementById("cantidad").value;
        const selectFeligres = document.getElementById("id_feligres");
        const nombreFeligres = selectFeligres.options[selectFeligres.selectedIndex]?.text || 'Anónimo';
        const esEdicion = id !== '';
        
        if (!concepto) {
            Swal.fire('Error', 'Por favor ingrese un concepto', 'error');
            return;
        }
        
        if (!cantidad || parseFloat(cantidad) <= 0) {
            Swal.fire('Error', 'Por favor ingrese un monto válido', 'error');
            return;
        }
        
        const formData = new FormData(this);
        
        enviando = true;
        
        Swal.fire({ 
            title: 'Guardando...', 
            text: 'Por favor espere', 
            allowOutsideClick: false, 
            didOpen: () => { Swal.showLoading(); } 
        });
        
        fetch('../api/ofrendas/guardar.php', { 
            method: 'POST', 
            body: formData 
        })
        .then(res => res.json())
        .then(data => {
            enviando = false;
            
            if (data.success) {
                if (esEdicion) {
                    registrarActividad(`EDITÓ DONACIÓN: ${concepto} - ${cantidad} FCFA`, 'PAGOS', `Feligrés: ${nombreFeligres}`);
                } else {
                    registrarActividad(`CREÓ DONACIÓN: ${concepto} - ${cantidad} FCFA`, 'PAGOS', `Feligrés: ${nombreFeligres}`);
                }
                
                Swal.close();
                modalExito.show();
                modal.hide();
                document.getElementById("formOfrenda").reset();
                setTimeout(() => { 
                    listarOfrendas(); 
                }, 500);
            } else {
                Swal.fire({ 
                    title: 'Error', 
                    text: data.error || 'Error al guardar', 
                    icon: 'error', 
                    confirmButtonText: 'Aceptar' 
                });
            }
        })
        .catch(error => {
            enviando = false;
            console.error("Error:", error);
            Swal.fire({ 
                title: 'Error', 
                text: 'Error del servidor: ' + error.message, 
                icon: 'error', 
                confirmButtonText: 'Aceptar' 
            });
        });
    });

    document.getElementById("buscador").addEventListener("input", function() {
        const bus = this.value.toLowerCase().trim();
        
        if (bus === '') {
            ofrendasFiltradas = [...ofrendasGlobal];
        } else {
            ofrendasFiltradas = ofrendasGlobal.filter(o =>
                (o.feligres_nombre && o.feligres_nombre.toLowerCase().includes(bus)) ||
                (o.concepto && o.concepto.toLowerCase().includes(bus))
            );
        }
        
        paginaActual = 1;
        actualizarPaginacion();
        renderizarPagina();
    });

    function eliminarPago(id) {
        const pago = ofrendasGlobal.find(o => o.id_pago == id);
        const concepto = pago ? pago.concepto : 'ID:' + id;
        const cantidad = pago ? pago.cantidad : '';
        
        Swal.fire({
            title: '¿Eliminar pago?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Eliminando...', text: 'Por favor espere', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                
                fetch('../api/ofrendas/eliminar.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${id}`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        registrarActividad(`ELIMINÓ DONACIÓN: ${concepto} ${cantidad ? `- ${cantidad} FCFA` : ''}`, 'PAGOS');
                        Swal.fire({ title: '¡Eliminado!', text: data.message, icon: 'success', confirmButtonText: 'Aceptar', timer: 2000 });
                        listarOfrendas();
                    } else {
                        Swal.fire('Error', data.error, 'error');
                    }
                })
                .catch(error => { 
                    Swal.fire('Error', 'Error del servidor: ' + error.message, 'error'); 
                });
            }
        });
    }

    listarOfrendas();
</script>

<?php include 'footer.php'; ?>