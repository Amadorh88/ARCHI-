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

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="toastLive" class="toast align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMensaje"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<!-- ============ VER DETALLES ============ -->
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
            
            <div class="modal-body p-0 bg-light" id="detalleContenido">
                </div>
            
            <div class="modal-footer bg-white border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar Archivo</button>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i class="bi bi-printer me-2"></i>Emitir Constancia
                </button>
            </div>
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
   LISTADO Y TABLA
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
        const badge = s.tipo.toLowerCase() === 'matrimonio' 
            ? `<span class="badge ${s.estado === 'activo' ? 'bg-success' : 'bg-secondary'} cursor-pointer" onclick="toggleMatrimonio(${s.id})">${s.estado}</span>`
            : `<span class="badge bg-info">Registrado</span>`;

        html += `
        <tr>
            <td>${s.id}</td>
            <td><strong>${s.tipo}</strong></td>
            <td>${s.feligres}</td>
            <td>${s.fecha}</td>
            <td><code>${s.registro}</code></td>
            <td>${badge}</td>
            <td class="text-end">
            <button class="btn btn-sm btn-outline-info shadow-sm" 
                    title="Ver Expediente Completo" 
                    onclick="verSacramento('${s.tipo.toLowerCase()}', ${s.id})">
                <i class="bi bi-eye-fill me-1"></i> Ver
            </button>
            <button class="btn btn-sm btn-outline-primary" onclick="editarSacramento(${s.id}, '${s.tipo}')"><i class="bi bi-pencil"></i></button>
            
                </td>
        </tr>`;
    });
    tabla.innerHTML = html || '<tr><td colspan="7" class="text-center">No hay datos</td></tr>';
}

/* ==========================================
   ACCIONES MODAL
   ========================================== */
function abrirModalNuevo() {
    form.reset();
    document.getElementById('id_editar').value = '';
    document.getElementById('modalTitulo').innerHTML = '<i class="bi bi-plus-circle"></i> Nuevo Registro';
    limpiarFormularioIntermedio();
    modalBS.show();
}

selectorSacramento.addEventListener('change', async function() {
    const tipo = this.value;
    limpiarFormularioIntermedio();
    mostrarToast("Verificando aptitud...", "info");

    const res = await fetch(`../api/sacramentos/obtener_aptos.php?tipo=${mapaCatequesis[tipo]}`);
    feligresesAptosCargados = await res.json();
    
    if (feligresesAptosCargados.length === 0) {
        mostrarToast("Sin feligreses aptos para este sacramento", "warning");
        return;
    }

    renderizarCamposDinamicos(tipo, feligresesAptosCargados);
    document.querySelectorAll('.campos-ocultos').forEach(el => el.style.display = 'block');
    document.getElementById('fecha').disabled = false;
    contenedor.style.display = 'flex';
    if(!document.getElementById('checkManual').checked) generarCodigoAuto(tipo);
    cargarAuxiliares();
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
        if(tipo === 'bautismo') html += `<div class="col-md-6"><label>Padrino</label><input name="padrino" class="form-control"></div><div class="col-md-6"><label>Madrina</label><input name="madrina" class="form-control"></div>`;
    }
    contenedor.innerHTML = html;
    document.getElementById('btnGuardar').disabled = false;
}

function agregarTestigo() {
    const opciones = feligresesAptosCargados.map(f => `<option value="${f.id_feligres}">${f.nombre_completo}</option>`).join('');
    const div = document.createElement('div');
    div.className = 'col-md-10 mt-2 d-flex gap-2';
    div.innerHTML = `<select name="testigos[]" class="form-select">${opciones}</select>
                     <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove()"><i class="bi bi-trash"></i></button>`;
    document.getElementById('listaTestigos').appendChild(div);
}

async function cargarAuxiliares() {
    try {
        // CAMBIO AQUÍ: Usar listar.php en lugar de ver.php
        const [rMin, rPar] = await Promise.all([
            fetch('../api/ministros/listar.php'), 
            fetch('../api/parroquias/listar.php')
        ]);
        const ministros = await rMin.json();
        const parroquias = await rPar.json();

        const selectMin = document.getElementById('id_ministro');
        const selectPar = document.getElementById('id_parroquia');

        selectMin.innerHTML = ministros.map(m => `<option value="${m.id_ministro}">${m.nombre_completo}</option>`).join('');
        selectPar.innerHTML = parroquias.map(p => `<option value="${p.id_parroquia}">${p.nombre}</option>`).join('');
    } catch (e) { console.error(e); }
}

/* ==========================================
   CRUD: GUARDAR Y OTROS
   ========================================== */
form.onsubmit = function(e) {
    e.preventDefault();
    fetch('../api/sacramentos/guardar.php', { method: 'POST', body: new FormData(this) })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            mostrarToast("¡Guardado!");
            modalBS.hide();
            listarSacramentos();
        } else mostrarToast(data.error, "error");
    });
};

function toggleMatrimonio(id) {
    fetch(`../api/matrimonio/toggle.php?id=${id}`).then(() => listarSacramentos());
}

/* function editarSacramento(id, tipo) {
    mostrarToast("Función de edición en desarrollo para tipo: " + tipo, "info");
}
 */
function generarCodigoAuto(tipo) {
    document.getElementById('registro').value = `${tipo.substring(0,3).toUpperCase()}-${Math.floor(1000+Math.random()*9000)}`;
}

function limpiarFormularioIntermedio() {
    contenedor.innerHTML = '';
    document.getElementById('btnGuardar').disabled = true;
    document.querySelectorAll('.campos-ocultos').forEach(el => el.style.display = 'none');
}

/* ==========================================
   FUNCIÓN EDITAR (ACTUALIZADA)
   ========================================== */
   async function editarSacramento(id, tipoDisplay) {
    const tipo = tipoDisplay.toLowerCase().replace("primera comunión", "comunion").replace("confirmación", "confirmacion");
    
    mostrarToast("Cargando datos del registro...", "info");

    try {
        // 1. Obtener los datos actuales del registro
        const res = await fetch(`../api/sacramentos/obtener.php?id=${id}&tipo=${tipo}`);
        const datos = await res.json();

        if (!datos) throw new Error("No se encontraron datos");

        // 2. Preparar el modal
        form.reset();
        document.getElementById('id_editar').value = id;
        document.getElementById('modalTitulo').innerHTML = `<i class="bi bi-pencil-square"></i> Editar ${tipoDisplay}`;
        
        // Bloquear el cambio de tipo de sacramento en edición para evitar inconsistencias
        selectorSacramento.value = tipo;
        selectorSacramento.disabled = true;

        // 3. Cargar feligreses aptos para ese tipo (para llenar los selects)
        const resAptos = await fetch(`../api/sacramentos/obtener_aptos.php?tipo=${mapaCatequesis[tipo]}`);
        feligresesAptosCargados = await resAptos.json();

        // 4. Renderizar los campos dinámicos
        renderizarCamposDinamicos(tipo, feligresesAptosCargados);
        
        // 5. Rellenar campos comunes
        document.getElementById('registro').value = datos.registro;
        document.getElementById('fecha').value = datos.fecha;
        document.getElementById('fecha').disabled = false;
        document.querySelectorAll('.campos-ocultos').forEach(el => el.style.display = 'block');
        contenedor.style.display = 'flex';

        // 6. Rellenar campos específicos
        await cargarAuxiliares(); // Asegurar que ministros y parroquias estén cargados
        document.getElementById('id_ministro').value = datos.id_ministro;
        document.getElementById('id_parroquia').value = datos.id_parroquia || "";

        if (tipo === 'matrimonio') {
            // Lógica para Matrimonio (tabla relacional)
            const esposo = datos.participantes.find(p => p.rol === 'esposo');
            const esposa = datos.participantes.find(p => p.rol === 'esposa');
            const testigos = datos.participantes.filter(p => p.rol === 'testigo');

            if(esposo) form.id_esposo.value = esposo.id_feligres;
            if(esposa) form.id_esposa.value = esposa.id_feligres;

            // Limpiar testigos por defecto y agregar los reales
            document.getElementById('listaTestigos').innerHTML = '';
            testigos.forEach((t, index) => {
                if (index === 0) {
                    // El primero va en el contenedor base
                    const div = document.createElement('div');
                    div.className = 'col-md-10';
                    div.innerHTML = generarSelectTestigo(t.id_feligres);
                    const btnAdd = document.createElement('div');
                    btnAdd.className = 'col-md-2';
                    btnAdd.innerHTML = `<button type="button" class="btn btn-primary w-100" onclick="agregarTestigo()"><i class="bi bi-plus"></i></button>`;
                    document.getElementById('listaTestigos').appendChild(div);
                    document.getElementById('listaTestigos').appendChild(btnAdd);
                } else {
                    agregarTestigo(t.id_feligres);
                }
            });
        } else {
            // Lógica para Bautismo, Comunion, Confirmación
            form.id_feligres.value = datos.id_feligres;
            if (tipo === 'bautismo') {
                form.padrino.value = datos.padrino || "";
                form.madrina.value = datos.madrina || "";
            }
        }

        modalBS.show();

    } catch (error) {
        console.error(error);
        mostrarToast("Error al cargar la edición", "error");
    }
}

// Función auxiliar para generar el HTML del select de testigo
function generarSelectTestigo(idSeleccionado = "") {
    const opciones = feligresesAptosCargados.map(f => 
        `<option value="${f.id_feligres}" ${f.id_feligres == idSeleccionado ? 'selected' : ''}>${f.nombre_completo}</option>`
    ).join('');
    return `<select name="testigos[]" class="form-select">${opciones}</select>`;
}

// Actualizar la función agregarTestigo para que acepte un ID preseleccionado
function agregarTestigo(idPreseleccionado = "") {
    const div = document.createElement('div');
    div.className = 'col-md-10 mt-2 d-flex gap-2';
    div.innerHTML = `
        ${generarSelectTestigo(idPreseleccionado)}
        <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove()"><i class="bi bi-trash"></i></button>
    `;
    document.getElementById('listaTestigos').appendChild(div);
}

/* ======= VER DETALLES =========== */
async function verSacramento(tipo, id) {
    try {
        const res = await fetch(`../api/sacramentos/ver_detalle.php?id=${id}&tipo=${tipo}`);
        const d = await res.json();
        
        const modal = new bootstrap.Modal(document.getElementById('modalVerSacramento'));
        const container = document.getElementById('detalleContenido');

        // Diseño Elegante (Kiyosaki: La información es el activo más valioso)
        let html = `
            <div class="p-4 bg-white border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="text-primary fw-bold mb-1">${tipo.toUpperCase()}</h5>
                    <span class="badge bg-dark">REGISTRO: ${d.registro}</span>
                </div>
                <div class="text-end">
                    <p class="mb-0 text-muted small">Fecha de Celebración</p>
                    <h6 class="fw-bold">${new Date(d.fecha).toLocaleDateString('es-ES', { dateStyle: 'long' })}</h6>
                </div>
            </div>
            
            <div class="p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="text-muted text-uppercase small fw-bold mb-3 border-bottom pb-1">Datos del Feligres</h6>
                        <p class="mb-1"><strong>Nombre:</strong> ${d.feligres_nombre || 'Ver Participantes'}</p>
                        ${d.nombre_padre ? `<p class="mb-1 text-secondary">Padre: ${d.nombre_padre}</p>` : ''}
                        ${d.nombre_madre ? `<p class="mb-1 text-secondary">Madre: ${d.nombre_madre}</p>` : ''}
                        <p class="mb-1 text-secondary">Origen: ${d.lugar_nacimiento || 'N/A'}</p>
                    </div>

                    <div class="col-md-6">
                        <h6 class="text-muted text-uppercase small fw-bold mb-3 border-bottom pb-1">Autoridad Eclesiástica</h6>
                        <p class="mb-1"><strong>Ministro:</strong> ${d.ministro_nombre}</p>
                        <p class="mb-1 badge bg-info text-dark">${d.ministro_rango}</p>
                        <p class="mt-2 mb-0"><strong>Sede:</strong> ${d.parroquia_nombre || d.lugar || 'Sede Central'}</p>
                        <small class="text-muted italic">${d.parroquia_dir || ''}</small>
                    </div>

                    <div class="col-12 mt-4 bg-light p-3 rounded border">
                        <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-2"></i>Detalles de Fe y Testimonio</h6>
                        <div class="row">
        `;

        if (tipo === 'matrimonio') {
            const participantes = d.participantes.split('|');
            participantes.forEach(p => {
                html += `<div class="col-md-4 mb-2"><span class="badge bg-white text-dark border w-100 p-2">${p}</span></div>`;
            });
        } else if (tipo === 'bautismo') {
            html += `
                <div class="col-md-6"><strong>Padrino:</strong> ${d.padrino || 'N/A'}</div>
                <div class="col-md-6"><strong>Madrina:</strong> ${d.madrina || 'N/A'}</div>
            `;
        } else {
            html += `<div class="col-12 text-muted italic">Registro sacramental validado bajo actas parroquiales.</div>`;
        }

        html += `</div></div></div></div>`;
        
        container.innerHTML = html;
        modal.show();
    } catch (error) {
        mostrarToast("No se pudo auditar el registro seleccionado", "error");
    }
}
// Iniciar
listarSacramentos();
</script>