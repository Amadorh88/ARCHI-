<?php
include 'header.php';
registrarAccesoModulo(__FILE__);
?>

<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-journal-check text-primary"></i> Registro de Sacramentos</h4>
        <div class="d-flex gap-2 w-100 w-md-auto">
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                <input type="text" id="buscador" class="form-control" placeholder="Buscar por feligrés o sacramento...">
            </div>

            <?php if ($rolUsuario !== 'parroco' && ($rolUsuario !== 'archivista')): ?>
                <button class="btn btn-primary px-4" onclick="abrirModalNuevo()">
                    <i class="bi bi-plus-lg"></i> <span class="d-none d-md-inline">Nuevo Registro</span>
                </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>ID</th>
                        <th>Sacramento</th>
                        <th>Feligrés / Pareja</th>
                        <th>Fecha</th>
                        <th>Registro</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaSacramentos"></tbody>
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

<!-- Modal Principal para Gestión de Sacramentos -->
<div class="modal fade" id="modalSacramento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form id="formSacramento">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="modalTitulo"><i class="bi bi-stars me-2"></i>Gestionar Sacramento</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <input type="hidden" name="id_editar" id="id_editar">
                        <input type="hidden" name="check_manual" id="check_manual" value="0">

                        <div class="col-md-12">
                            <label class="form-label fw-bold">1. Tipo de Sacramento</label>
                            <select name="tipo_sacramento" id="tipo_sacramento"
                                class="form-select form-select-lg border-primary" required>
                                <option value="" selected disabled>Elija una opción...</option>
                                <option value="bautismo">Bautismo</option>
                                <option value="comunion">Primera Comunión</option>
                                <option value="confirmacion">Confirmación</option>
                                <option value="matrimonio">Matrimonio</option>
                            </select>
                        </div>

                        <hr class="my-3">

                        <div class="col-md-6">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="checkManual">
                                <label class="form-check-label" for="checkManual">Código de registro manual</label>
                            </div>
                            <input type="text" name="registro" id="registro" class="form-control"
                                placeholder="Automático" readonly required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Fecha de Celebración</label>
                            <input type="date" name="fecha" id="fecha" class="form-control" required disabled>
                        </div>

                        <div id="contenedorDinamico" class="row g-3 m-0 p-0" style="display:none;"></div>

                        <div class="col-md-6 campos-ocultos" style="display:none;">
                            <label class="form-label">Ministro</label>
                            <select name="id_ministro" id="id_ministro" class="form-select" required></select>
                        </div>
                        <div class="col-md-6 campos-ocultos" style="display:none;">
                            <label class="form-label">Parroquia</label>
                            <select name="id_parroquia" id="id_parroquia" class="form-select" required></select>
                        </div>

                        <!-- SECCIÓN DE PAGO - Solo visible en modo nuevo -->
                        <div class="col-md-12 mt-3 border-top pt-3" id="seccionPago">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-danger"><i class="bi bi-cash-stack"></i> Pago Requerido</span>
                                <button type="button" class="btn btn-outline-success btn-sm" id="btnRegistrarPago" onclick="abrirDonacionDesdeSacramento()">
                                    <i class="bi bi-plus-circle"></i> Registrar Pago
                                </button>
                            </div>
                            <div id="infoPagoSacramento" style="display:none;" class="mt-2">
                                <div class="alert alert-success py-2">
                                    <i class="bi bi-check-circle-fill me-2"></i> 
                                    <span id="infoPagoSacramentoText">Pago registrado correctamente</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <div class="d-flex justify-content-between w-100">
                        <div></div>
                        <div>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success px-4" id="btnGuardar" disabled>
                                <i class="bi bi-save me-1"></i> Guardar Registro
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de donación rápida desde sacramentos -->
<div class="modal fade" id="modalDonacionRapidaSacramento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form id="formDonacionRapidaSacramento">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-cash-stack me-2"></i>Registro de Pago
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 row g-3">
                    <input type="hidden" name="id_feligres" id="id_feligres_donacion_sacramento">
                    <input type="hidden" name="registrar_pago" value="1">
                    
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Feligrés</label>
                        <input type="text" id="nombre_feligres_donacion_sacramento" class="form-control" readonly>
                    </div>
                    
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Concepto <span class="text-danger">*</span></label>
                        <input type="text" name="concepto" id="concepto_donacion_sacramento" class="form-control" required
                            placeholder="Ej: Sacramento - Bautismo">
                        <small class="text-muted">Concepto generado automáticamente según el sacramento</small>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Monto <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="cantidad" id="cantidad_donacion_sacramento" 
                                class="form-control" required 
                                oninput="calcularCambioDonacionSacramento()" 
                                min="1" step="1" 
                                placeholder="Ingrese el monto">
                            <span class="input-group-text">FCFA</span>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Monto Recibido</label>
                        <div class="input-group">
                            <input type="number" name="recibido" id="recibido_donacion_sacramento" 
                                class="form-control" 
                                oninput="calcularCambioDonacionSacramento()" 
                                min="0" step="1" 
                                placeholder="Ingrese el monto recibido">
                            <span class="input-group-text">FCFA</span>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="p-3 bg-light rounded d-flex justify-content-between align-items-center border border-success">
                            <span class="fw-bold text-muted small"><i class="bi bi-arrow-return-left me-1"></i> CAMBIO A DEVOLVER:</span>
                            <span class="h4 mb-0 fw-bold text-success" id="vueltoTextDonacionSacramento">0 FCFA</span>
                            <input type="hidden" name="cambio" id="cambio_donacion_sacramento">
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="confirmarPagoSacramento" required>
                            <label class="form-check-label" for="confirmarPagoSacramento">
                                Confirmo que he recibido el pago y verifico los datos ingresados
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-check-circle me-1"></i> Confirmar y Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Toast para notificaciones -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="toastLive" class="toast align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMensaje"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<!-- Modal para Ver Detalles del Sacramento -->
<div class="modal fade" id="modalVerSacramento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg overflow-hidden">
            <div class="modal-header bg-primary text-white p-4">
                <div>
                    <h4 class="modal-title fw-bold mb-0" id="verTitulo">RESEÑA SACRAMENTAL</h4>
                    <small class="opacity-75">Archivo Eclesiástico Centralizado</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 bg-light" id="detalleContenido"></div>
            <div class="modal-footer bg-white border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar Archivo</button>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i class="bi bi-printer me-2"></i>Emitir Constancia
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación de Éxito (Edición y Creación) -->
<div class="modal fade" id="modalConfirmacionEdicion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header" id="modalConfirmacionHeader" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title fw-bold text-white">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <span id="modalConfirmacionTitulo">¡Éxito!</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <div id="modalConfirmacionIcono" style="font-size: 4rem;">
                        <i class="bi bi-archive-fill" style="color: #28a745;"></i>
                    </div>
                </div>
                <div id="modalConfirmacionInfoFeligres" class="mb-3 p-3 bg-light rounded" style="display: none;">
                    <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                        <i class="bi bi-person-badge fs-4 text-primary"></i>
                        <strong class="fs-5">Feligrés:</strong>
                    </div>
                    <p class="mb-0 fw-bold text-primary" id="modalConfirmacionNombreFeligres">-</p>
                </div>
                <div class="mb-2">
                    <span class="badge bg-success fs-6 px-3 py-2" id="modalConfirmacionTipoSacramento">-</span>
                </div>
                <p class="fs-5 mb-0" id="modalConfirmacionMensaje">Operación realizada con éxito</p>
            </div>
            <div class="modal-footer justify-content-center border-0">
                <button type="button" class="btn btn-success px-4" data-bs-dismiss="modal" id="btnAceptarConfirmacion">
                    <i class="bi bi-check-lg me-1"></i> Aceptar
                </button>
            </div>
        </div>
    </div>
</div>

<style>
#modalConfirmacionEdicion .modal-content {
    border-radius: 20px;
    overflow: hidden;
    animation: slideInUp 0.3s ease-out;
}
#modalConfirmacionInfoFeligres {
    border-radius: 15px;
    transition: all 0.3s ease;
}
#modalConfirmacionInfoFeligres:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
@keyframes slideInUp {
    from { transform: translateY(50px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
@keyframes pulse {
    0% { transform: scale(1); color: #0d6efd; }
    50% { transform: scale(1.05); color: #0a58ca; }
    100% { transform: scale(1); color: #0d6efd; }
}
#modalConfirmacionTipoSacramento {
    font-size: 1rem;
    padding: 0.5rem 1rem;
    border-radius: 50px;
    letter-spacing: 1px;
}
.modal-header .btn-close {
    filter: brightness(0) invert(1);
}
.table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.05);
    cursor: pointer;
}
.btn-sm {
    transition: all 0.2s ease;
}
.btn-sm:hover {
    transform: translateY(-1px);
}
.card {
    border-radius: 15px;
}
.form-select-lg {
    font-size: 1rem;
}
#cantidad_donacion_sacramento,
#recibido_donacion_sacramento {
    font-size: 1.25rem;
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    border: 2px solid #ced4da;
    transition: all 0.3s ease;
}
#cantidad_donacion_sacramento:focus,
#recibido_donacion_sacramento:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
    background-color: #f8fff8;
}
#cantidad_donacion_sacramento:hover,
#recibido_donacion_sacramento:hover {
    background-color: #f8fff8;
    border-color: #28a745;
}
#cantidad_donacion_sacramento::placeholder,
#recibido_donacion_sacramento::placeholder {
    color: #adb5bd;
    font-size: 0.9rem;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const modalBS = new bootstrap.Modal(document.getElementById('modalSacramento'));
    const modalConfirmacionBS = new bootstrap.Modal(document.getElementById('modalConfirmacionEdicion'));
    const form = document.getElementById('formSacramento');
    const tabla = document.getElementById("tablaSacramentos");
    const selectorSacramento = document.getElementById('tipo_sacramento');
    const contenedor = document.getElementById('contenedorDinamico');
    const toastBS = new bootstrap.Toast(document.getElementById('toastLive'));

    let registrosGlobal = [];
    let registrosFiltrados = [];
    let feligresesAptosCargados = [];

    // Variables de paginación
    let paginaActual = 1;
    let registrosPorPagina = 10;
    let totalPaginas = 1;

    // Variables para el pago
    let pagoSacramentoRegistrado = false;
    let idPagoSacramento = null;
    let montoPagoSacramento = 0;
    let modalDonacionRapidaSacramento = null;

    const mapaSacramentos = {
        'bautismo': 'Bautismo',
        'comunion': 'Comunión',
        'confirmacion': 'Confirmación',
        'matrimonio': 'Matrimonio'
    };

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatCFA(valor) {
        return new Intl.NumberFormat('fr-FR').format(Math.round(valor || 0));
    }

    function resetearEstadoPagoSacramento() {
        pagoSacramentoRegistrado = false;
        idPagoSacramento = null;
        montoPagoSacramento = 0;
        document.getElementById('infoPagoSacramento').style.display = 'none';
        document.getElementById('infoPagoSacramentoText').innerHTML = '';
        document.getElementById('btnGuardar').disabled = true;
    }

    document.addEventListener("DOMContentLoaded", function() {
        const limiteGuardado = localStorage.getItem('sacramentos_limite');
        if (limiteGuardado) {
            document.getElementById('limiteRegistros').value = limiteGuardado;
            registrosPorPagina = parseInt(limiteGuardado);
        }
        listarSacramentos();
        initModalDonacionSacramento();
        
        document.getElementById('modalSacramento').addEventListener('hidden.bs.modal', function() {
            resetearEstadoPagoSacramento();
        });
    });

    function cambiarLimite() {
        const select = document.getElementById('limiteRegistros');
        registrosPorPagina = parseInt(select.value);
        localStorage.setItem('sacramentos_limite', registrosPorPagina);
        paginaActual = 1;
        actualizarPaginacion();
        renderizarPagina();
    }

    function mostrarToast(mensaje, tipo = "success") {
        const el = document.getElementById('toastLive');
        const colores = { success: "bg-success", error: "bg-danger", info: "bg-primary", warning: "bg-warning text-dark" };
        el.className = `toast align-items-center border-0 text-white ${colores[tipo] || 'bg-dark'}`;
        document.getElementById('toastMensaje').innerText = mensaje;
        toastBS.show();
    }

    function mostrarModalConfirmacion(mensaje, esEdicion = false, nombreFeligres = '', tipoSacramento = '') {
        return new Promise((resolve) => {
            const header = document.getElementById('modalConfirmacionHeader');
            const iconoDiv = document.getElementById('modalConfirmacionIcono');
            const titulo = document.getElementById('modalConfirmacionTitulo');
            const mensajeElem = document.getElementById('modalConfirmacionMensaje');
            const nombreFeligresElem = document.getElementById('modalConfirmacionNombreFeligres');
            const tipoSacramentoElem = document.getElementById('modalConfirmacionTipoSacramento');
            const infoContainer = document.getElementById('modalConfirmacionInfoFeligres');
            
            if (nombreFeligres && nombreFeligres !== '') {
                infoContainer.style.display = 'block';
                nombreFeligresElem.textContent = nombreFeligres;
                nombreFeligresElem.style.animation = 'none';
                nombreFeligresElem.offsetHeight;
                nombreFeligresElem.style.animation = 'pulse 0.5s ease';
                setTimeout(() => { nombreFeligresElem.style.animation = ''; }, 500);
            } else {
                infoContainer.style.display = 'none';
            }
            
            if (tipoSacramento && tipoSacramento !== '') {
                tipoSacramentoElem.textContent = tipoSacramento;
                tipoSacramentoElem.style.display = 'inline-block';
            } else {
                tipoSacramentoElem.style.display = 'none';
            }
            
            if (esEdicion) {
                header.style.background = "linear-gradient(135deg, #f093fb 0%, #f5576c 100%)";
                iconoDiv.innerHTML = '<i class="bi bi-pencil-square" style="font-size: 4rem; color: #ffc107;"></i>';
                titulo.innerHTML = '<i class="bi bi-pencil-square me-2"></i> ¡Actualización Exitosa!';
                mensajeElem.innerHTML = mensaje || '✓ El registro ha sido actualizado correctamente';
            } else {
                header.style.background = "linear-gradient(135deg, #667eea 0%, #764ba2 100%)";
                iconoDiv.innerHTML = '<i class="bi bi-archive-fill" style="font-size: 4rem; color: #28a745;"></i>';
                titulo.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i> ¡Registro Exitoso!';
                mensajeElem.innerHTML = mensaje || '✓ El registro ha sido guardado correctamente';
            }
            
            // Guardar referencia al botón de aceptar
            const btnAceptar = document.getElementById('btnAceptarConfirmacion');
            
            // Remover eventos anteriores para evitar duplicados
            const newBtn = btnAceptar.cloneNode(true);
            btnAceptar.parentNode.replaceChild(newBtn, btnAceptar);
            
            newBtn.addEventListener('click', function() {
                modalConfirmacionBS.hide();
                resolve();
            });
            
            // También cerrar al hacer clic en la X o fuera del modal
            const modalElement = document.getElementById('modalConfirmacionEdicion');
            const onHidden = function() {
                modalElement.removeEventListener('hidden.bs.modal', onHidden);
                resolve();
            };
            modalElement.addEventListener('hidden.bs.modal', onHidden);
            
            modalConfirmacionBS.show();
        });
    }

    window.rolUsuario = window.rolUsuario || '<?php echo $rolUsuario; ?>';

    function listarSacramentos() {
        return fetch('../api/sacramentos/listar.php')
            .then(res => res.json())
            .then(data => {
                registrosGlobal = data.data || [];
                registrosFiltrados = [...registrosGlobal];
                paginaActual = 1;
                actualizarPaginacion();
                renderizarPagina();
            })
            .catch(() => mostrarToast("Error de conexión", "error"));
    }

    function actualizarPaginacion() {
        totalPaginas = Math.ceil(registrosFiltrados.length / registrosPorPagina);
        if (totalPaginas === 0) totalPaginas = 1;
        
        if (paginaActual > totalPaginas) paginaActual = totalPaginas;
        
        const inicio = (paginaActual - 1) * registrosPorPagina + 1;
        const fin = Math.min(paginaActual * registrosPorPagina, registrosFiltrados.length);
        const total = registrosFiltrados.length;
        
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
        const paginaData = registrosFiltrados.slice(inicio, fin);
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

    function puedeEditar() {
        return ['admin', 'archivista', 'secretario'].includes(rolUsuario);
    }
    
    function puedeActivar() {
        return ['admin', 'parroco'].includes(rolUsuario);
    }
    
    function puedeSeparar() {
        return ['admin', 'parroco'].includes(rolUsuario);
    }
    
    function puedeEliminar() {
        return ['admin', 'secretario'].includes(rolUsuario);
    }
    
    function renderTabla(lista) {
        let html = '';

        if (lista.length === 0) {
            html = `<tr><td colspan="7" class="text-center py-4 text-muted">No hay datos disponibles</td></tr>`;
        } else {
            lista.forEach(s => {
                const tipoClean = (s.tipo || '').toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                const idSacramento = s.id;
                const nombreSacramento = s.tipo || 'Desconocido';
                const nombreFeligres = s.feligres || 'No registrado';
                const fecha = s.fecha || '-';
                const registro = s.registro || '-';
                const esActivo = s.estado === 'activo' || s.estado == 1 || s.estado === 'Activo';
                const estadoTexto = esActivo ? 'activo' : 'inactivo';
                
                const btnEliminar = puedeEliminar()
                    ? `<button class="btn btn-sm btn-outline-danger ms-1" onclick="eliminarSacramento(${idSacramento}, '${tipoClean}')"><i class="bi bi-trash"></i></button>`
                    : '';
                
                const btnEditar = puedeEditar()
                    ? `<button class="btn btn-sm btn-outline-primary ms-1" onclick="editarSacramento(${idSacramento}, '${nombreSacramento}')"><i class="bi bi-pencil"></i></button>`
                    : '';
                
                const btnToggle = puedeSeparar()
                    ? `<button class="btn btn-sm btn-outline-warning ms-1" onclick="toggleEstado(${idSacramento}, '${tipoClean}', '${estadoTexto}')" title="Activar / Desactivar Registro"><i class="bi bi-arrow-repeat"></i></button>`
                    : '';

                html += `
                <tr id="fila-${tipoClean}-${idSacramento}" onclick="verSacramento('${tipoClean}', ${idSacramento})" style="cursor: pointer;">
                    <td><span class="badge bg-light text-dark border">${idSacramento}</span></td>
                    <td><strong>${escapeHtml(nombreSacramento)}</strong></td>
                    <td>${escapeHtml(nombreFeligres)}</td>
                    <td>${fecha}</td>
                    <td><code>${escapeHtml(registro)}</code></td>
                    <td><span id="estado-${tipoClean}-${idSacramento}" class="badge ${esActivo ? 'bg-success' : 'bg-secondary'}">${estadoTexto}</span></td>
                    <td class="text-end" onclick="event.stopPropagation()">
                        <button class="btn btn-sm btn-outline-info shadow-sm" onclick="verSacramento('${tipoClean}', ${idSacramento})"><i class="bi bi-eye-fill me-1"></i> Ver</button>
                        ${btnEditar}
                        ${btnToggle}
                        ${btnEliminar}
                      </td>
                  </tr>
                `;
            });
        }
        tabla.innerHTML = html;
    }

    function toggleEstado(id, tipo, estadoActual) {
        const registro = registrosGlobal.find(r => {
            const tipoClean = (r.tipo || '').toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
            return r.id == id && tipoClean === tipo;
        });
        const nombreBeneficiario = registro ? registro.feligres : 'ID:' + id;
        const nombreSacramento = registro ? registro.tipo : tipo;
        const nuevaAccion = estadoActual === 'activo' ? 'DESACTIVÓ' : 'ACTIVÓ';
        
        Swal.fire({
            title: '¿Cambiar estado?',
            text: `¿Deseas ${estadoActual === 'activo' ? 'desactivar' : 'activar'} este registro?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: estadoActual === 'activo' ? '#dc3545' : '#28a745',
            confirmButtonText: `Sí, ${estadoActual === 'activo' ? 'Desactivar' : 'Activar'}`,
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (!result.isConfirmed) return;
            
            fetch(`../api/sacramentos/toggle.php?tipo=${tipo}&id=${id}`)
                .then(res => res.json())
                .then(resp => {
                    if (!resp.success) {
                        mostrarToast(resp.error || 'Error al cambiar estado', 'error');
                        return;
                    }
                    
                    registrarActividad(`${nuevaAccion} ${nombreSacramento}: ${nombreBeneficiario}`, 'SACRAMENTOS', `ID: ${id}`);
                    listarSacramentos();
                    mostrarToast(`Estado cambiado a ${resp.nuevo_estado || 'activo'}`, 'success');
                })
                .catch((err) => {
                    console.error(err);
                    mostrarToast('Error de conexión', 'error');
                });
        });
    }

    function abrirModalNuevo() {
        form.reset();
        document.getElementById('id_editar').value = '';
        document.getElementById('modalTitulo').innerHTML = '<i class="bi bi-plus-circle"></i> Nuevo Registro';
        selectorSacramento.disabled = false;
        limpiarFormularioIntermedio();
        resetearEstadoPagoSacramento();
        
        // Mostrar sección de pago en modo nuevo
        document.getElementById('seccionPago').style.display = 'block';
        document.getElementById('btnRegistrarPago').style.display = 'inline-block';
        document.getElementById('btnGuardar').disabled = true;
        
        modalBS.show();
    }

    selectorSacramento.addEventListener('change', async function () {
        const tipo = this.value;
        limpiarFormularioIntermedio();
        mostrarToast("Verificando aptitud...", "info");

        try {
            const res = await fetch(`../api/sacramentos/obtener_aptos.php?tipo=${mapaSacramentos[tipo]}`);
            feligresesAptosCargados = await res.json();

            if (!feligresesAptosCargados || feligresesAptosCargados.length === 0) {
                mostrarToast("Sin feligreses aptos para este sacramento", "warning");
                return;
            }

            renderizarCamposDinamicos(tipo, feligresesAptosCargados, false);
            document.querySelectorAll('.campos-ocultos').forEach(el => el.style.display = 'block');
            document.getElementById('fecha').disabled = false;
            contenedor.style.display = 'flex';
            if (!document.getElementById('checkManual').checked) generarCodigoAuto(tipo);
            cargarAuxiliares();
            document.getElementById('btnGuardar').disabled = true;
        } catch (error) {
            console.error("Error al cargar aptos:", error);
            mostrarToast("Error al cargar datos", "error");
        }
    });

    function renderizarCamposDinamicos(tipo, lista , esEdicion = false) {
        const optionVacia = `<option value="">-- Seleccione --</option>`;
        const opcionesEsposo = optionVacia + lista.filter(f => f.genero == "Masculino").map(f => `<option value="${f.id_feligres}">${escapeHtml(f.nombre_completo)}</option>`).join('');
        const opcionesEsposa = optionVacia + lista.filter(f => f.genero == "Femenino").map(f => `<option value="${f.id_feligres}">${escapeHtml(f.nombre_completo)}</option>`).join('');
        const opciones = optionVacia + lista.map(f => `<option value="${f.id_feligres}">${escapeHtml(f.nombre_completo)}</option>`).join('');
       // const opcionActivo = lista.filter(f  => f.id_feligres == idEdit).map(f  => `<option value="${f.id_feligres}">${escapeHtml(f.nombre_completo)}</option>`).join('');
       // console.log(opcionActivo)
        let html = '';

        if (tipo === 'matrimonio') {
            html = `
            <div class="col-md-6">
                <label class="form-label">Esposo *</label>
                <select name="id_esposo" id="id_esposo" class="form-select select-feligres" ${esEdicion ? 'disabled' : ''} required>${opcionesEsposo}</select>
                ${esEdicion ? '<input type="hidden" name="id_esposo" id="hidden_id_esposo" value="">' : ''}
            </div>
            <div class="col-md-6">
                <label class="form-label">Esposa *</label>
                <select name="id_esposa" id="id_esposa" class="form-select select-feligres" ${esEdicion ? 'disabled' : ''} required>${opcionesEsposa}</select>
                ${esEdicion ? '<input type="hidden" name="id_esposa" id="hidden_id_esposa" value="">' : ''}
            </div>
            <div class="col-12"><label class="form-label fw-bold border-bottom w-100">Testigos</label></div>
            <div id="listaTestigos" class="col-12 row g-2">
                <div class="col-md-12 d-flex gap-2">
                    <select name="testigos[]" class="form-select select-feligres" ${esEdicion ? 'disabled' : ''}>${opciones}</select>
                    ${!esEdicion ? '<button type="button" class="btn btn-primary" onclick="agregarTestigo()"><i class="bi bi-plus"></i></button>' : ''}
                </div>
            </div>`;
        } else {
            html = `
            <div class="col-md-12">
                <label class="form-label">Feligrés *</label>
                <select name="id_feligres" id="id_feligres" class="form-select select-feligres" ${esEdicion ? 'disabled' : ''} required>${opciones}</select>
                ${esEdicion ? '<input type="hidden" name="id_feligres" id="hidden_id_feligres" value="">' : ''}
            </div>`;
            if (tipo === 'bautismo') {
                html += `<div class="col-md-6"><label>Padrino</label>
                            <input name="padrino" id="padrino" class="form-control" placeholder="Nombre del padrino" ${esEdicion ? 'readonly' : ''}>
                        </div>
                        <div class="col-md-6"><label>Madrina</label>
                            <input name="madrina" id="madrina" class="form-control" placeholder="Nombre de la madrina" ${esEdicion ? 'readonly' : ''}>
                        </div>`;
            }
        }
        contenedor.innerHTML = html;
    }

    function agregarTestigo(idPreseleccionado = "", modoEdicion = false) {
        const optionVacia = `<option value="">-- Seleccione --</option>`;
        const opciones = optionVacia + feligresesAptosCargados.map(f => 
            `<option value="${f.id_feligres}" ${f.id_feligres == idPreseleccionado ? 'selected' : ''}>${escapeHtml(f.nombre_completo)}</option>`
        ).join('');
        
        const div = document.createElement('div');
        div.className = 'col-md-12 mt-2 d-flex gap-2';
        
        if (modoEdicion) {
            div.innerHTML = `
                <select name="testigos_aux[]" class="form-select select-feligres" disabled>${opciones}</select>
                <input type="hidden" name="testigos[]" value="${idPreseleccionado}">`;
        } else {
            div.innerHTML = `
                <select name="testigos[]" class="form-select select-feligres">${opciones}</select>
                <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove();"><i class="bi bi-trash"></i></button>`;
        }
        
        document.getElementById('listaTestigos').appendChild(div);
    }

    async function cargarAuxiliares() {
        try {
            const [rMin, rPar] = await Promise.all([
                fetch('../api/ministros/listar.php'),
                fetch('../api/parroquias/listar.php')
            ]);
            const ministros = await rMin.json();
            const parroquias = await rPar.json();

            const selectMin = document.getElementById('id_ministro');
            const selectPar = document.getElementById('id_parroquia');

            if (selectMin) {
                selectMin.innerHTML = '<option value="">-- Seleccione Ministro --</option>' + (ministros || []).map(m => `<option value="${m.id_ministro}">${escapeHtml(m.nombre_completo)} (${m.tipo || ''})</option>`).join('');
            }
            if (selectPar) {
                selectPar.innerHTML = '<option value="">-- Seleccione Parroquia --</option>' + (parroquias || []).map(p => `<option value="${p.id_parroquia}">${escapeHtml(p.nombre)}</option>`).join('');
            }
        } catch (e) { 
            console.error("Error cargando auxiliares:", e); 
        }
    }

    function limpiarFormularioIntermedio() {
        contenedor.innerHTML = '';
        document.getElementById('btnGuardar').disabled = true;
        document.querySelectorAll('.campos-ocultos').forEach(el => el.style.display = 'none');
        document.getElementById('fecha').disabled = true;
        document.getElementById('registro').value = '';
        document.getElementById('checkManual').checked = false;
        document.getElementById('check_manual').value = '0';
        document.getElementById('registro').readOnly = true;
        resetearEstadoPagoSacramento();
    }

    // === FUNCIÓN GUARDAR REGISTRO CORREGIDA ===
    function guardarRegistro(form) {
        const esEdicion = document.getElementById('id_editar').value !== '';
        const formData = new FormData(form);
        const tipoSacramento = document.getElementById('tipo_sacramento').value;
        const nombreSacramento = tipoSacramento === 'bautismo' ? 'Bautismo' : 
                                 tipoSacramento === 'comunion' ? 'Comunión' :
                                 tipoSacramento === 'confirmacion' ? 'Confirmación' : 'Matrimonio';
        
        let nombreBeneficiario = '';
        if (tipoSacramento === 'matrimonio') {
            const esposo = document.getElementById('id_esposo');
            const esposa = document.getElementById('id_esposa');
            if (esposo && esposa && esposo.value && esposa.value) {
                nombreBeneficiario = `${esposo.options[esposo.selectedIndex]?.text || ''} y ${esposa.options[esposa.selectedIndex]?.text || ''}`;
            }
        } else {
            const selectFeligres = document.getElementById('id_feligres');
            if (selectFeligres && selectFeligres.value) {
                nombreBeneficiario = selectFeligres.options[selectFeligres.selectedIndex]?.text || '';
            }
        }
        
        // Solo agregar datos de pago si es nuevo registro
        if (!esEdicion && pagoSacramentoRegistrado) {
            formData.append('id_pago', idPagoSacramento);
            formData.append('monto_pago', montoPagoSacramento);
        }
        
        const btnGuardar = document.getElementById('btnGuardar');
        const textoOriginal = btnGuardar.innerHTML;
        btnGuardar.disabled = true;
        btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Guardando...';
        
        fetch('../api/sacramentos/guardar.php', { 
            method: 'POST', 
            body: formData 
        })
        .then(res => res.json())
        .then(async (data) => {
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = textoOriginal;
            
            if (data.success) {
                // Registrar actividad
                registrarActividad(
                    esEdicion ? `EDITÓ ${nombreSacramento}` : `CREÓ ${nombreSacramento}`,
                    'SACRAMENTOS',
                    `${nombreBeneficiario || 'Registro'} - ${data.registro || ''}`
                );
                
                // Cerrar el modal principal
                modalBS.hide();
                
                // Mostrar modal de confirmación
                await mostrarModalConfirmacion(
                    data.message, 
                    esEdicion, 
                    data.nombreFeligres || nombreBeneficiario, 
                    nombreSacramento
                );
                
                // Actualizar la tabla en tiempo real
                await listarSacramentos();
                
                // Mostrar toast de éxito
                mostrarToast(data.message, "success");
                
                // Resetear estado del pago
                resetearEstadoPagoSacramento();
            } else {
                Swal.fire('Error', data.error || 'Error al guardar', 'error');
                mostrarToast(data.error || 'Error al guardar', "error");
            }
        })
        .catch(error => {
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = textoOriginal;
            console.error('Error en guardarRegistro:', error);
            Swal.fire('Error de conexión', 'No se pudo conectar con el servidor. Verifique su conexión.', 'error');
            mostrarToast("Error de conexión al servidor", "error");
        });
    }

    form.onsubmit = function (e) {
        e.preventDefault();
        
        const esEdicion = document.getElementById('id_editar').value !== '';
        
        // Si es edición, proceder directamente a guardar
        if (esEdicion) {
            guardarRegistro(this);
            return;
        }
        
        // Para nuevo registro, verificar pago
        if (!pagoSacramentoRegistrado) {
            Swal.fire({
                title: 'Pago requerido',
                text: 'Debe registrar un pago para completar el registro del sacramento',
                icon: 'warning',
                confirmButtonText: 'Registrar pago',
                showCancelButton: true,
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    abrirDonacionDesdeSacramento();
                }
            });
            return;
        }
        
        guardarRegistro(this);
    };

    function generarCodigoAuto(tipo) {
        const prefijos = {
            'bautismo': 'BAU',
            'comunion': 'COM',
            'confirmacion': 'CONF',
            'matrimonio': 'MAT'
        };
        const prefijo = prefijos[tipo] || 'REG';
        const numero = Math.floor(1000 + Math.random() * 9000);
        document.getElementById('registro').value = `${prefijo}${numero.toString().padStart(4, '0')}`;
    }

    document.getElementById('checkManual').addEventListener('change', function(e) {
        const registroInput = document.getElementById('registro');
        const checkManualHidden = document.getElementById('check_manual');
        
        if (e.target.checked) {
            registroInput.readOnly = false;
            registroInput.placeholder = "Ingrese código manualmente (ej: BAU0001)";
            registroInput.value = '';
            checkManualHidden.value = '1';
        } else {
            registroInput.readOnly = true;
            registroInput.placeholder = "Automático";
            checkManualHidden.value = '0';
            const tipo = document.getElementById('tipo_sacramento').value;
            if (tipo) generarCodigoAuto(tipo);
        }
    });

    async function editarSacramento(id, tipoDisplay) {
        let tipo = tipoDisplay.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        if (tipo.includes("comunion")) tipo = "comunion";
        if (tipo.includes("confirmacion")) tipo = "confirmacion";

        mostrarToast("Cargando datos del registro...", "info");   

        try {
            const res = await fetch(`../api/sacramentos/obtener.php?id=${id}&tipo=${tipo}`);
            const datos = await res.json();

            if (!datos || datos.error) {
                throw new Error(datos.error || "No se encontraron datos");
            }

            form.reset();
            document.getElementById('id_editar').value = id;
            document.getElementById('modalTitulo').innerHTML = `<i class="bi bi-pencil-square"></i> Editar ${tipoDisplay}`;

            selectorSacramento.value = tipo;
            selectorSacramento.disabled = true;

            const resAptos = await fetch(`../api/sacramentos/obtener_aptos.php?tipo=${mapaSacramentos[tipo]}`);
            feligresesAptosCargados = await resAptos.json();
console.log(`tipo: ${tipo} 
                id: ${id}`);
            renderizarCamposDinamicos(tipo, feligresesAptosCargados,  true);

            document.getElementById('registro').value = datos.registro || '';
            document.getElementById('fecha').value = datos.fecha || '';
            document.getElementById('fecha').disabled = false;
            document.querySelectorAll('.campos-ocultos').forEach(el => el.style.display = 'block');
            contenedor.style.display = 'flex';

            await cargarAuxiliares();
            
            if (document.getElementById('id_ministro')) {
                document.getElementById('id_ministro').value = datos.id_ministro || '';
            }
            if (document.getElementById('id_parroquia')) {
                document.getElementById('id_parroquia').value = datos.id_parroquia || '';
            }
            
            // Ocultar completamente la sección de pago en modo edición
            document.getElementById('seccionPago').style.display = 'none';
            document.getElementById('btnRegistrarPago').style.display = 'none';
            document.getElementById('infoPagoSacramento').style.display = 'none';
            document.getElementById('btnGuardar').disabled = false;

            if (tipo === 'matrimonio') {
                const esposo = datos.participantes?.find(p => p.rol === 'esposo');
                const esposa = datos.participantes?.find(p => p.rol === 'esposa');
                const testigos = datos.participantes?.filter(p => p.rol === 'testigo') || [];

                if (esposo && document.getElementById('id_esposo')) {
                    const selectEsposo = document.getElementById('id_esposo');
                    selectEsposo.value = esposo.id_feligres;
                    const hiddenEsposo = document.getElementById('hidden_id_esposo');
                    if (hiddenEsposo) hiddenEsposo.value = esposo.id_feligres;
                }
                if (esposa && document.getElementById('id_esposa')) {
                    const selectEsposa = document.getElementById('id_esposa');
                    selectEsposa.value = esposa.id_feligres;
                    const hiddenEsposa = document.getElementById('hidden_id_esposa');
                    if (hiddenEsposa) hiddenEsposa.value = esposa.id_feligres;
                }

                const listaTestigos = document.getElementById('listaTestigos');
                if (listaTestigos) {
                    listaTestigos.innerHTML = '';
                    if (testigos.length > 0) {
                        testigos.forEach(t => agregarTestigo(t.id_feligres, true));
                    } else {
                        agregarTestigo('', true);
                    }
                    const botonAgregar = listaTestigos.querySelector('.btn-primary');
                    if (botonAgregar) botonAgregar.style.display = 'none';
                }
            } else {
                if (document.getElementById('id_feligres') && datos.id_feligres) {
                    const selectFeligres = document.getElementById('id_feligres');
                    const idFeligres = datos.id_feligres;
                    
                    setTimeout(() => {
                        selectFeligres.value = idFeligres;
                        const hiddenFeligres = document.getElementById('hidden_id_feligres');
                        if (hiddenFeligres) {
                            hiddenFeligres.value = idFeligres;
                        }
                        if (selectFeligres.value != idFeligres) {
                            const nombreFeligres = datos.nombre_feligres || '';
                            for (let i = 0; i < selectFeligres.options.length; i++) {
                                if (selectFeligres.options[i].value == idFeligres || 
                                    selectFeligres.options[i].text == nombreFeligres) {
                                    selectFeligres.selectedIndex = i;
                                    if (hiddenFeligres) hiddenFeligres.value = idFeligres;
                                    break;
                                }
                            }
                        }
                    }, 150);
                }
                
                if (tipo === 'bautismo') {
                    if (document.getElementById('padrino')) {
                        document.getElementById('padrino').value = datos.padrino || '';
                    }
                    if (document.getElementById('madrina')) {
                        document.getElementById('madrina').value = datos.madrina || '';
                    }
                }
            }

            modalBS.show();

        } catch (error) {
            console.error('Error en editarSacramento:', error);
            Swal.fire('Error', error.message || 'No se pudo cargar el registro para editar', 'error');
            mostrarToast("Error al cargar la edición", "error");
        }
    }

    async function verSacramento(tipo, id) {
        try {
            const res = await fetch(`../api/sacramentos/ver_detalle.php?id=${id}&tipo=${tipo}`);
            const d = await res.json();

            const modal = new bootstrap.Modal(document.getElementById('modalVerSacramento'));
            const container = document.getElementById('detalleContenido');
            const tituloModal = document.getElementById('verTitulo');

            tituloModal.innerHTML = `${(tipo || '').toUpperCase()} - REGISTRO #${d.registro || id}`;

            let html = `
            <div class="p-4 bg-white border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="text-primary fw-bold mb-1">${(tipo || '').toUpperCase()}</h5>
                    <span class="badge bg-dark">REGISTRO: ${d.registro || '-'}</span>
                </div>
                <div class="text-end">
                    <p class="mb-0 text-muted small">Fecha de Celebración</p>
                    <h6 class="fw-bold">${d.fecha ? new Date(d.fecha).toLocaleDateString('es-ES', { dateStyle: 'long' }) : 'No especificada'}</h6>
                </div>
            </div>
            
            <div class="p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="text-muted text-uppercase small fw-bold mb-3 border-bottom pb-1">
                            <i class="bi bi-person me-1"></i> Datos del Feligrés
                        </h6>
                        <p class="mb-2"><strong>Nombre:</strong> ${escapeHtml(d.feligres_nombre || d.participantes || 'No registrado')}</p>
                        ${d.nombre_padre ? `<p class="mb-2 text-secondary"><strong>Padre:</strong> ${escapeHtml(d.nombre_padre)}</p>` : ''}
                        ${d.nombre_madre ? `<p class="mb-2 text-secondary"><strong>Madre:</strong> ${escapeHtml(d.nombre_madre)}</p>` : ''}
                        ${d.lugar_nacimiento ? `<p class="mb-2 text-secondary"><strong>Lugar de nacimiento:</strong> ${escapeHtml(d.lugar_nacimiento)}</p>` : ''}
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted text-uppercase small fw-bold mb-3 border-bottom pb-1">
                            <i class="bi bi-building me-1"></i> Autoridad Eclesiástica
                        </h6>
                        <p class="mb-2"><strong>Ministro:</strong> ${escapeHtml(d.ministro_nombre || 'No especificado')}</p>
                        <p class="mt-2 mb-0"><strong>Sede:</strong> ${escapeHtml(d.parroquia_nombre || d.lugar || 'Sede Central')}</p>
                    </div>
                </div>
            </div>
            `;

            container.innerHTML = html;
            modal.show();
        } catch (error) {
            console.error(error);
            Swal.fire('Error', 'No se pudo cargar el detalle del registro', 'error');
            mostrarToast("No se pudo cargar el detalle", "error");
        }
    }

    const buscador = document.getElementById('buscador');
    buscador.addEventListener('input', function() {
        const texto = this.value.toLowerCase();
        const filtrados = registrosGlobal.filter(r => {
            const feligres = (r.feligres || '').toLowerCase();
            const sacramento = (r.tipo || '').toLowerCase();
            return feligres.includes(texto) || sacramento.includes(texto);
        });
        registrosFiltrados = filtrados;
        paginaActual = 1;
        actualizarPaginacion();
        renderizarPagina();
    });

    function eliminarSacramento(id, tipo) {
        const registro = registrosGlobal.find(r => {
            const tipoClean = (r.tipo || '').toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
            return r.id == id && tipoClean === tipo;
        });
        const nombreBeneficiario = registro ? registro.feligres : 'ID:' + id;
        const nombreSacramento = registro ? registro.tipo : tipo;
        
        Swal.fire({
            title: '¿Eliminar registro?',
            text: "El registro se borrará de la base de datos",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (!result.isConfirmed) return;
            
            fetch('../api/sacramentos/eliminar.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `id=${id}&tipo=${tipo}`
            })
            .then(res => res.json())
            .then(resp => {
                if (resp.success) {
                    registrarActividad(`ELIMINÓ ${nombreSacramento}: ${nombreBeneficiario}`, 'SACRAMENTOS', `ID: ${id}`);
                    Swal.fire('Eliminado', resp.message, 'success');
                    listarSacramentos();
                } else {
                    Swal.fire('Error', resp.error, 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
            });
        });
    }

    function initModalDonacionSacramento() {
        const modalElement = document.getElementById('modalDonacionRapidaSacramento');
        if (modalElement) {
            modalDonacionRapidaSacramento = new bootstrap.Modal(modalElement);
        }
    }

    function calcularCambioDonacionSacramento() {
        const cant = parseInt(document.getElementById('cantidad_donacion_sacramento').value) || 0;
        const rec = parseInt(document.getElementById('recibido_donacion_sacramento').value) || 0;
        const cambio = rec - cant;
        const display = document.getElementById('vueltoTextDonacionSacramento');
        const cambioInput = document.getElementById('cambio_donacion_sacramento');

        display.innerText = `${formatCFA(Math.max(0, cambio))} FCFA`;
        display.className = cambio < 0 ? "h4 mb-0 fw-bold text-danger" : "h4 mb-0 fw-bold text-success";
        cambioInput.value = Math.max(0, cambio);
    }

    function abrirDonacionDesdeSacramento() {
        const tipoSacramento = document.getElementById('tipo_sacramento').value;
        let idFeligres = '';
        let nombreFeligres = '';
        
        if (tipoSacramento === 'matrimonio') {
            const esposo = document.getElementById('id_esposo');
            const esposa = document.getElementById('id_esposa');
            
            if (esposo && esposo.value) {
                idFeligres = esposo.value;
                nombreFeligres = esposo.options[esposo.selectedIndex]?.text || '';
            } else if (esposa && esposa.value) {
                idFeligres = esposa.value;
                nombreFeligres = esposa.options[esposa.selectedIndex]?.text || '';
            }
        } else {
            const selectFeligres = document.getElementById('id_feligres');
            if (selectFeligres && selectFeligres.value) {
                idFeligres = selectFeligres.value;
                nombreFeligres = selectFeligres.options[selectFeligres.selectedIndex]?.text || '';
            }
        }
        
        if (!idFeligres) {
            Swal.fire({
                title: 'Seleccione un feligrés',
                text: 'Por favor seleccione primero el feligrés para registrar el pago',
                icon: 'warning',
                confirmButtonText: 'Aceptar'
            });
            return;
        }
        
        const concepto = 'Sacramento - ' + (mapaSacramentos[tipoSacramento] || tipoSacramento);
        
        document.getElementById('id_feligres_donacion_sacramento').value = idFeligres;
        document.getElementById('nombre_feligres_donacion_sacramento').value = nombreFeligres;
        document.getElementById('concepto_donacion_sacramento').value = concepto;
        document.getElementById('cantidad_donacion_sacramento').value = '';
        document.getElementById('recibido_donacion_sacramento').value = '';
        document.getElementById('vueltoTextDonacionSacramento').innerText = '0 FCFA';
        document.getElementById('cambio_donacion_sacramento').value = '';
        document.getElementById('confirmarPagoSacramento').checked = false;
        
        if (!modalDonacionRapidaSacramento) {
            initModalDonacionSacramento();
        }
        modalDonacionRapidaSacramento.show();
    }

    if (document.getElementById('formDonacionRapidaSacramento')) {
        document.getElementById('formDonacionRapidaSacramento').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const concepto = document.getElementById('concepto_donacion_sacramento').value;
            const cantidad = document.getElementById('cantidad_donacion_sacramento').value;
            const nombreFeligres = document.getElementById('nombre_feligres_donacion_sacramento').value;
            const confirmar = document.getElementById('confirmarPagoSacramento').checked;
            
            if (!confirmar) {
                Swal.fire({
                    title: 'Confirmación requerida',
                    text: 'Por favor confirme que ha recibido el pago y verificado los datos',
                    icon: 'warning',
                    confirmButtonText: 'Aceptar'
                });
                return;
            }
            
            if (!cantidad || parseFloat(cantidad) <= 0) {
                Swal.fire({
                    title: 'Monto inválido',
                    text: 'Por favor ingrese un monto válido para el pago',
                    icon: 'warning',
                    confirmButtonText: 'Aceptar'
                });
                return;
            }
            
            Swal.fire({ 
                title: 'Guardando pago...', 
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
                if (data.success) {
                    registrarActividad(`CREÓ DONACIÓN: ${concepto} - ${cantidad} FCFA`, 'PAGOS', `Feligrés: ${nombreFeligres}`);
                    
                    pagoSacramentoRegistrado = true;
                    idPagoSacramento = data.id_ofrenda || data.id;
                    montoPagoSacramento = parseFloat(cantidad) || 0;
                    
                    document.getElementById('infoPagoSacramento').style.display = 'block';
                    document.getElementById('infoPagoSacramentoText').innerHTML = 
                        `Pago registrado: <strong>${formatCFA(montoPagoSacramento)} FCFA</strong> - ${concepto}`;
                    
                    document.getElementById('btnGuardar').disabled = false;
                    
                    Swal.fire({ 
                        title: '¡Pago Registrado!', 
                        text: `El pago de ${formatCFA(montoPagoSacramento)} FCFA se ha guardado correctamente. Ahora puede guardar el registro.`, 
                        icon: 'success', 
                        confirmButtonText: 'Aceptar' 
                    });
                    
                    if (modalDonacionRapidaSacramento) {
                        modalDonacionRapidaSacramento.hide();
                    }
                    this.reset();
                } else {
                    Swal.fire({ title: 'Error', text: data.error || 'Error al guardar', icon: 'error', confirmButtonText: 'Aceptar' });
                }
            })
            .catch(error => {
                Swal.fire({ title: 'Error', text: 'Error del servidor: ' + error.message, icon: 'error', confirmButtonText: 'Aceptar' });
            });
        });
    }
</script>

<?php include 'footer.php'; ?>