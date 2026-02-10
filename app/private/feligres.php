<?php 
include 'header.php'; 


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
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaFeligres"></tbody>
            </table>
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
                        <label class="form-label fw-bold">Nombre completo</label>
                        <input type="text" name="nombre_completo" id="nombre_completo" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Fecha nacimiento</label>
                        <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="form-control">
                    </div>
                    <div class="col-md-12">
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

<script>
    // Inyectamos el rol desde PHP para control en JS (Principio de Precisión - Hill)
    const ROL_USUARIO = '<?php echo $rol; ?>';
    const PUEDE_EDITAR = <?php echo $puede_editar ? 'true' : 'false'; ?>;

    const tabla = document.getElementById("tablaFeligres");
    const modalF = document.getElementById('modalFeligres') ? new bootstrap.Modal(document.getElementById('modalFeligres')) : null;
    const modalP = new bootstrap.Modal(document.getElementById('modalPerfil'));
    const modalH = new bootstrap.Modal(document.getElementById('modalHistorial'));
    
    const toastElement = document.getElementById('toastLive');
    const toastMensaje = document.getElementById('toastMensaje');
    const toastBootstrap = new bootstrap.Toast(toastElement);
    
    let listaGlobal = [];

    function mostrarToast(mensaje, tipo = "success") {
        const colores = {
            success: "bg-success text-white",
            error: "bg-danger text-white",
            info: "bg-primary text-white"
        };
        toastElement.className = `toast align-items-center border-0 ${colores[tipo]}`;
        toastMensaje.innerHTML = mensaje;
        toastBootstrap.show();
    }

    document.addEventListener("DOMContentLoaded", listarFeligres);

    function listarFeligres() {
        fetch('../api/feligres/listar.php')
            .then(res => res.json())
            .then(data => {
                listaGlobal = data;
                renderTabla(data);
            })
            .catch(err => mostrarToast("Error al sincronizar datos", "error"));
    }

    function renderTabla(lista) {
        let html = '';
        if (lista.length === 0) {
            html = `<tr><td colspan="5" class="text-center text-muted py-4">No hay registros disponibles</td></tr>`;
        } else {
            lista.forEach(f => {
                // Generación dinámica de botones basada en ROL (Principio de Escasez - Cialdini: No todos ven todo)
                let btnEditar = PUEDE_EDITAR ? 
                    `<button class="btn btn-sm btn-outline-primary" onclick="editar(${f.id_feligres})"><i class="bi bi-pencil"></i></button>` : '';
                
                html += `
                <tr>
                    <td><span class="badge bg-light text-dark border">#${f.id_feligres}</span></td>
                    <td class="fw-semibold">${f.nombre_completo}</td>
                    <td>${f.fecha_nacimiento ?? '-'}</td>
                    <td>${f.lugar_nacimiento ?? '-'}</td>
                    <td class="text-end">
                        <div class="btn-group shadow-sm">
                            ${btnEditar}
                            <button class="btn btn-sm btn-outline-info" onclick="verPerfil(${f.id_feligres})"><i class="bi bi-eye"></i></button>
                            <button class="btn btn-sm btn-outline-dark" onclick="verHistorial(${f.id_feligres})"><i class="bi bi-person-vcard"></i></button>
                            <button class="btn btn-sm btn-outline-secondary" onclick="imprimir(${f.id_feligres})"><i class="bi bi-printer"></i></button>
                        </div>
                    </td>
                </tr>`;
            });
        }
        tabla.innerHTML = html;
    }

    document.getElementById("buscadorFeligres").addEventListener("input", function () {
        let texto = this.value.toLowerCase().trim();
        let filtrado = listaGlobal.filter(f =>
            f.nombre_completo.toLowerCase().includes(texto) ||
            (f.lugar_nacimiento && f.lugar_nacimiento.toLowerCase().includes(texto))
        );
        renderTabla(filtrado);
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
            let formData = new FormData(this);
            fetch('../api/feligres/guardar.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.error) throw new Error(data.error);
                modalF.hide();
                listarFeligres();
                mostrarToast("Sistema actualizado correctamente");
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
                document.getElementById("fecha_nacimiento").value = f.fecha_nacimiento;
                document.getElementById("lugar_nacimiento").value = f.lugar_nacimiento;
                document.getElementById("nombre_padre").value = f.nombre_padre;
                document.getElementById("nombre_madre").value = f.nombre_madre;
                modalF.show();
            });
    }

    function verPerfil(id) {
        fetch('../api/feligres/perfil.php?id=' + id)
            .then(res => res.json())
            .then(data => {
                let f = data.feligres;
                let html = `
                    <div class="text-center mb-3">
                        <div class="display-6 text-primary"><i class="bi bi-person-circle"></i></div>
                        <h5 class="fw-bold mb-0">${f.nombre_completo}</h5>
                        <small class="text-muted">Expediente #${f.id_feligres}</small>
                    </div>
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item"><strong>Nacimiento:</strong> ${f.fecha_nacimiento ?? '-'}</li>
                        <li class="list-group-item"><strong>Padre:</strong> ${f.nombre_padre ?? '-'}</li>
                        <li class="list-group-item"><strong>Madre:</strong> ${f.nombre_madre ?? '-'}</li>
                    </ul>`;
                document.getElementById("perfilContenido").innerHTML = html;
                modalP.show();
            });
    }

    function verHistorial(id) {
        const contenedor = document.getElementById('contenidoHistorial');
        contenedor.innerHTML = `<div class="text-center p-4"><div class="spinner-border text-primary"></div></div>`;
        modalH.show();
        fetch('../api/feligres/historial.php?id=' + id)
            .then(res => res.json())
            .then(data => {
                document.getElementById('btnImprimirHistorial').href = 'imprimir_historial.php?id=' + data.feligres.id_feligres;
                contenedor.innerHTML = `
                    <div class="row g-3">
                        <div class="col-md-12 border-bottom pb-2">
                            <h4 class="fw-bold">${data.feligres.nombre_completo}</h4>
                        </div>
                        <div class="col-md-4">${data.sacramentos}</div>
                        <div class="col-md-8">${data.catequesis} ${data.donaciones}</div>
                    </div>`;
            });
    }

    function imprimir(id) {
        window.open('../report/imprimir_sacramentos.php?id=' + id, '_blank');
    }
</script>

<?php include 'footer.php'; ?>