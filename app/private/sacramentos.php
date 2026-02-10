<?php include 'header.php'; ?>

<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-journal-check text-primary"></i> Registro de Sacramentos</h4>
        <div class="d-flex gap-2 w-100 w-md-auto">
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                <input type="text" id="buscador" class="form-control" placeholder="Buscar por feligrés o sacramento...">
            </div>
            <button class="btn btn-primary px-4" onclick="abrirModalNuevo()">
                <i class="bi bi-plus-lg"></i> <span class="d-none d-md-inline">Nuevo Registro</span>
            </button>
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
    </div>
</div>

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
                        
                        <div class="col-md-12">
                            <label class="form-label fw-bold">1. Tipo de Sacramento</label>
                            <select name="tipo_sacramento" id="tipo_sacramento" class="form-select form-select-lg border-primary" required>
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
                                <label class="form-check-label" for="checkManual">¿Registro manual/externo?</label>
                            </div>
                            <input type="text" name="registro" id="registro" class="form-control" placeholder="Automático" readonly required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Fecha de Celebración</label>
                            <input type="date" name="fecha" id="fecha" class="form-control" required disabled>
                        </div>

                        <div id="contenedorDinamico" class="row g-3 m-0 p-0" style="display:none;"></div>

                        <div class="col-md-6 campos-ocultos" style="display:none;">
                            <label class="form-label">Ministro / Celebrante</label>
                            <select name="id_ministro" id="id_ministro" class="form-select" required></select>
                        </div>
                        <div class="col-md-6 campos-ocultos" style="display:none;">
                            <label class="form-label">Parroquia</label>
                            <select name="id_parroquia" id="id_parroquia" class="form-select" required></select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success px-4" id="btnGuardar" disabled>
                        <i class="bi bi-save me-1"></i> Guardar Registro
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="toastLive" class="toast align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMensaje"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
const modalBS = new bootstrap.Modal(document.getElementById('modalSacramento'));
const form = document.getElementById('formSacramento');
const tabla = document.getElementById("tablaSacramentos");
const selectorSacramento = document.getElementById('tipo_sacramento');
const contenedor = document.getElementById('contenedorDinamico');
const toastBS = new bootstrap.Toast(document.getElementById('toastLive'));

let registrosGlobal = [];
let feligresesAptosCargados = [];

const mapaCatequesis = {
    'bautismo': 'Pre-bautismal',
    'comunion': 'Primera comunión',
    'confirmacion': 'Confirmación',
    'matrimonio': 'Matrimonial'
};

function mostrarToast(mensaje, tipo = "success") {
    const el = document.getElementById('toastLive');
    const colores = { success: "bg-success", error: "bg-danger", info: "bg-primary", warning: "bg-warning text-dark" };
    el.className = `toast align-items-center border-0 text-white ${colores[tipo] || 'bg-dark'}`;
    document.getElementById('toastMensaje').innerText = mensaje;
    toastBS.show();
}

/* ==========================================
   LISTADO Y RENDERIZADO (Kiyosaki & Hill Style)
   ========================================== */
function listarSacramentos() {
    fetch('../api/sacramentos/listar.php')
        .then(res => res.json())
        .then(data => {
            registrosGlobal = data;
            renderTabla(data);
        })
        .catch(() => mostrarToast("Error de conexión", "error"));
}

function renderTabla(lista) {
    let html = '';
    lista.forEach(s => {
        const esMatrimonio = s.tipo.toLowerCase() === 'matrimonio';
        const esActivo = s.estado === 'activo';
        
        // Badge de Estado
        const badge = esMatrimonio 
            ? `<span class="badge ${esActivo ? 'bg-success' : 'bg-secondary'}">${s.estado}</span>`
            : `<span class="badge bg-info">Registrado</span>`;

        // Botón Toggle dinámico (Solo para matrimonio)
        const botonToggle = esMatrimonio ? `
            <button class="btn btn-sm ${esActivo ? 'btn-outline-warning' : 'btn-outline-success'}" 
                    title="${esActivo ? 'Anular/Desactivar' : 'Reactivar'}" 
                    onclick="toggleMatrimonio(${s.id})">
                <i class="bi ${esActivo ? 'bi-lock-fill' : 'bi-unlock-fill'}"></i>
            </button>` : '';

        html += `
        <tr>
            <td>${s.id}</td>
            <td><strong>${s.tipo}</strong></td>
            <td>${s.feligres}</td>
            <td>${s.fecha}</td>
            <td><code>${s.registro}</code></td>
            <td>${badge}</td>
            <td class="text-end">
                <div class="btn-group shadow-sm">
                    <button class="btn btn-sm btn-outline-info" title="Ver Detalle" 
                            onclick="verSacramento('${s.tipo.toLowerCase()}', ${s.id})">
                        <i class="bi bi-eye-fill"></i>
                    </button>
                    ${botonToggle}
                    <button class="btn btn-sm btn-outline-primary" title="Editar"
                            onclick="editarSacramento(${s.id}, '${s.tipo}')">
                        <i class="bi bi-pencil"></i>
                    </button>
                </div>
            </td>
        </tr>`;
    });
    tabla.innerHTML = html || '<tr><td colspan="7" class="text-center">No hay datos</td></tr>';
}

/* ==========================================
   LÓGICA DE NEGOCIO (Validaciones Hill)
   ========================================== */
function abrirModalNuevo() {
    form.reset();
    selectorSacramento.disabled = false;
    document.getElementById('id_editar').value = '';
    document.getElementById('modalTitulo').innerHTML = '<i class="bi bi-plus-circle"></i> Nuevo Registro';
    limpiarFormularioIntermedio();
    modalBS.show();
}

selectorSacramento.addEventListener('change', async function() {
    const tipo = this.value;
    limpiarFormularioIntermedio();
    mostrarToast("Verificando aptitud y duplicados...", "info");

    try {
        const res = await fetch(`../api/sacramentos/obtener_aptos.php?tipo=${mapaCatequesis[tipo]}`);
        feligresesAptosCargados = await res.json();
        
        if (feligresesAptosCargados.length === 0) {
            mostrarToast("No hay feligreses aptos o ya poseen este sacramento", "warning");
            return;
        }

        renderizarCamposDinamicos(tipo, feligresesAptosCargados);
        document.querySelectorAll('.campos-ocultos').forEach(el => el.style.display = 'block');
        document.getElementById('fecha').disabled = false;
        contenedor.style.display = 'flex';
        if(!document.getElementById('checkManual').checked) generarCodigoAuto(tipo);
        cargarAuxiliares();
    } catch (e) {
        mostrarToast("Error al validar feligreses", "error");
    }
});

function renderizarCamposDinamicos(tipo, lista) {
    const opciones = lista.map(f => `<option value="${f.id_feligres}">${f.nombre_completo}</option>`).join('');
    let html = '';

    if (tipo === 'matrimonio') {
        html = `
            <div class="col-md-6"><label class="form-label">Esposo</label><select name="id_esposo" class="form-select">${opciones}</select></div>
            <div class="col-md-6"><label class="form-label">Esposa</label><select name="id_esposa" class="form-select">${opciones}</select></div>
            <div class="col-12"><label class="form-label fw-bold border-bottom w-100">Testigos</label></div>
            <div id="listaTestigos" class="col-12 row g-2">
                <div class="col-md-10"><select name="testigos[]" class="form-select">${opciones}</select></div>
                <div class="col-md-2"><button type="button" class="btn btn-primary w-100" onclick="agregarTestigo()"><i class="bi bi-plus"></i></button></div>
            </div>`;
    } else {
        html = `<div class="col-md-12"><label class="form-label">Feligrés</label><select name="id_feligres" class="form-select">${opciones}</select></div>`;
        if(tipo === 'bautismo') {
            html += `<div class="col-md-6"><label class="form-label">Padrino</label><input name="padrino" class="form-control"></div>
                     <div class="col-md-6"><label class="form-label">Madrina</label><input name="madrina" class="form-control"></div>`;
        }
    }
    contenedor.innerHTML = html;
    document.getElementById('btnGuardar').disabled = false;
}

/* ==========================================
   CRUD ACTIONS (Cialdini Authority)
   ========================================== */
function toggleMatrimonio(id) {
    if(!confirm("¿Está seguro de cambiar el estado de este matrimonio? Esto afectará la aptitud de los feligreses.")) return;
    
    fetch(`../api/matrimonio/toggle.php?id=${id}`)
        .then(res => res.json())
        .then(data => {
            mostrarToast("Estado actualizado correctamente", "success");
            listarSacramentos();
        })
        .catch(() => mostrarToast("Error al procesar cambio", "error"));
}

async function verSacramento(tipo, id) {
    try {
        const res = await fetch(`../api/sacramentos/ver_detalle.php?id=${id}&tipo=${tipo}`);
        const d = await res.json();
        const container = document.getElementById('detalleContenido');

        let html = `
            <div class="p-4 bg-white border-bottom d-flex justify-content-between align-items-center">
                <div><h5 class="text-primary fw-bold mb-1">${tipo.toUpperCase()}</h5><span class="badge bg-dark">REGISTRO: ${d.registro}</span></div>
                <div class="text-end"><p class="mb-0 text-muted small">Fecha</p><h6 class="fw-bold">${d.fecha}</h6></div>
            </div>
            <div class="p-4"><div class="row g-4">
                <div class="col-md-6">
                    <h6 class="text-muted small fw-bold mb-3 border-bottom pb-1">DATOS DEL SUJETO</h6>
                    <p class="mb-1"><strong>Nombre:</strong> ${d.feligres_nombre || 'Pareja'}</p>
                    <p class="mb-1 text-secondary small">Padres: ${d.nombre_padre || 'N/A'} & ${d.nombre_madre || 'N/A'}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted small fw-bold mb-3 border-bottom pb-1">AUTORIDAD</h6>
                    <p class="mb-1"><strong>Ministro:</strong> ${d.ministro_nombre}</p>
                    <p class="mb-0 small"><strong>Parroquia:</strong> ${d.parroquia_nombre || 'Sede Central'}</p>
                </div>`;

        if (tipo === 'matrimonio' && d.participantes) {
            html += `<div class="col-12 mt-3"><h6 class="fw-bold small">PARTICIPANTES:</h6><div class="d-flex flex-wrap gap-2">`;
            d.participantes.split('|').forEach(p => html += `<span class="badge border text-dark bg-white">${p}</span>`);
            html += `</div></div>`;
        }

        html += `</div></div>`;
        container.innerHTML = html;
        new bootstrap.Modal(document.getElementById('modalVerSacramento')).show();
    } catch (e) { mostrarToast("Error al cargar detalles", "error"); }
}

// Resto de funciones auxiliares (agregarTestigo, cargarAuxiliares, etc. se mantienen)
function agregarTestigo() {
    const opciones = feligresesAptosCargados.map(f => `<option value="${f.id_feligres}">${f.nombre_completo}</option>`).join('');
    const div = document.createElement('div');
    div.className = 'col-md-10 mt-2 d-flex gap-2';
    div.innerHTML = `<select name="testigos[]" class="form-select">${opciones}</select>
                     <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove()"><i class="bi bi-trash"></i></button>`;
    document.getElementById('listaTestigos').appendChild(div);
}

async function cargarAuxiliares() {
    const [rMin, rPar] = await Promise.all([fetch('../api/ministros/listar.php'), fetch('../api/parroquias/listar.php')]);
    document.getElementById('id_ministro').innerHTML = (await rMin.json()).map(m => `<option value="${m.id_ministro}">${m.nombre_completo}</option>`).join('');
    document.getElementById('id_parroquia').innerHTML = (await rPar.json()).map(p => `<option value="${p.id_parroquia}">${p.nombre}</option>`).join('');
}

function generarCodigoAuto(tipo) {
    document.getElementById('registro').value = `${tipo.substring(0,3).toUpperCase()}-${Math.floor(1000+Math.random()*9000)}`;
}

function limpiarFormularioIntermedio() {
    contenedor.innerHTML = '';
    document.getElementById('btnGuardar').disabled = true;
    document.querySelectorAll('.campos-ocultos').forEach(el => el.style.display = 'none');
}

form.onsubmit = function(e) {
    e.preventDefault();
    fetch('../api/sacramentos/guardar.php', { method: 'POST', body: new FormData(this) })
    .then(res => res.json())
    .then(data => {
        if(data.success) { mostrarToast("¡Operación Exitosa!"); modalBS.hide(); listarSacramentos(); }
        else mostrarToast(data.error, "error");
    });
};

// Iniciar sistema
listarSacramentos();
</script>
<?php include 'footer.php'; ?>
 