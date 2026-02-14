<?php include 'header.php'; ?>

<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-0">
                <i class="bi bi-heart-pulse text-danger"></i> Gestión de Ofrendas y Colectas
            </h4>
            <p class="text-muted small mb-0">Tesorería Parroquial | Moneda: **FCFA**</p>
        </div>

        <div class="d-flex gap-2 w-100 w-md-auto">
            <div class="input-group">
                <span class="input-group-text bg-light border-0 shadow-sm">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="buscador" class="form-control border-0 shadow-sm" placeholder="Buscar por feligrés o concepto...">
            </div>

            <button class="btn btn-primary shadow-sm px-4" onclick="abrirModalNuevo()">
                <i class="bi bi-plus-lg"></i>
                <span class="d-none d-md-inline">Nueva Ofrenda</span>
            </button>
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
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaOfrendas"></tbody>
            </table>
        </div>
    </div>
</div>

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
                        <select name="id_feligres" id="id_feligres" class="form-select border-primary shadow-sm"></select>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold">Concepto</label>
                        <input type="text" name="concepto" id="concepto" class="form-control" required placeholder="Ej: Colecta Dominical, Diezmo...">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Monto Ofrenda</label>
                        <div class="input-group">
                            <input type="number" name="cantidad" id="cantidad" class="form-control" required oninput="calcularCambio()">
                            <span class="input-group-text">CFA</span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-success">Monto Recibido</label>
                        <div class="input-group">
                            <input type="number" name="recibido" id="recibido" class="form-control" required oninput="calcularCambio()">
                            <span class="input-group-text">CFA</span>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const tabla = document.getElementById("tablaOfrendas");
    const modal = new bootstrap.Modal(document.getElementById('modalOfrenda'));
    let ofrendasGlobal = [];

    // Formateador (Hill: Estética profesional para generar confianza)
    const formatCFA = (valor) => new Intl.NumberFormat('fr-FR').format(Math.round(valor));

    // Función de log para depuración (Hill: Atención al detalle)
    const log = (msg, tipo = "INFO") => console.log(`[${tipo}] ${msg}`);

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

    function listarOfrendas() {
        fetchJSON('../api/ofrendas/listar.php').then(data => {
            if (data.error) return;
            ofrendasGlobal = data;
            renderOfrendas(data);
            actualizarTotalMes(data);
        });
    }

    function renderOfrendas(lista) {
        let html = '';
        lista.forEach(o => {
            html += `
            <tr>
                <td><span class="text-muted small">#${o.id_pago}</span></td>
                <td><i class="bi bi-person-circle me-2 text-primary"></i>${o.feligres_nombre || 'Anónimo'}</td>
                <td><span class="badge bg-light text-dark border">${o.concepto}</span></td>
                <td class="fw-bold">${formatCFA(o.cantidad)} FCFA</td>
                <td class="small text-muted">${o.fecha || 'N/A'}</td>
                <td class="text-end text-nowrap">
                    <button class="btn btn-sm btn-outline-primary me-1" onclick="editar(${o.id_pago})"><i class="bi bi-pencil"></i></button>
                      </td>
            </tr>`;
        });
        tabla.innerHTML = html || '<tr><td colspan="6" class="text-center py-4">Sin registros</td></tr>';
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
        cargarFeligreses();
        modal.show();
    }

    async function cargarFeligreses() {
        // CORRECCIÓN DE RUTA (Verifica que feligreses/listar.php exista)
        const data = await fetchJSON('../api/feligres/listar.php'); 
        let options = '<option value="">-- Donación Anónima --</option>';
        if (!data.error) {
            data.forEach(f => {
                options += `<option value="${f.id_feligres}">${f.nombre_completo}</option>`;
            });
        }
        document.getElementById('id_feligres').innerHTML = options;
    }

    function editar(id) {
        fetchJSON(`../api/ofrendas/ver.php?id=${id}`).then(o => {
            if(o.error) return;
            document.getElementById("id_pago").value = o.id_pago;
            document.getElementById("concepto").value = o.concepto;
            document.getElementById("cantidad").value = Math.round(o.cantidad);
            document.getElementById("recibido").value = Math.round(o.recibido);
            
            cargarFeligreses().then(() => {
                document.getElementById("id_feligres").value = o.id_feligres || "";
                calcularCambio();
                modal.show();
            });
        });
    }

    function eliminar(id) {
        if (!confirm("¿Desea eliminar este registro contable?")) return;
        fetchJSON(`../api/ofrendas/eliminar.php?id=${id}`).then(res => {
            if (res.success) listarOfrendas();
        });
    }

    document.getElementById("formOfrenda").addEventListener("submit", function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('../api/ofrendas/guardar.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    modal.hide();
                    listarOfrendas();
                } else {
                    alert("Error: " + data.error);
                }
            });
    });

    function actualizarTotalMes(lista) {
        const total = lista.reduce((acc, curr) => acc + parseFloat(curr.cantidad), 0);
        document.getElementById('totalMes').innerText = formatCFA(total);
    }

    // Buscador
    document.getElementById("buscador").addEventListener("input", function() {
        const bus = this.value.toLowerCase();
        renderOfrendas(ofrendasGlobal.filter(o => 
            (o.feligres_nombre && o.feligres_nombre.toLowerCase().includes(bus)) || 
            o.concepto.toLowerCase().includes(bus)
        ));
    });

    listarOfrendas();
</script>

<?php include 'footer.php'; ?>