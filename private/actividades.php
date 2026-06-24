<?php include 'header.php'; ?>
<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
        <h4 class="fw-bold mb-0"><i class="bi bi-person-gear"></i> Actividades de Usuarios</h4>
        <div class="input-group w-100 w-md-25">
            <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
            <input type="text" id="buscador" class="form-control" placeholder="Buscar...">
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>ID Usuario</th>
                        <th>Acción</th>
                        <th>Módulo</th>
                        <th>Fecha</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaActividades"></tbody>
            </table>
        </div>
    </div>
</div>

<script>
    const tabla = document.getElementById("tablaActividades");
    let actividadesGlobal = [];

    function listarActividades() {
        fetch('../api/actividades/listar.php')
            .then(res => res.json())
            .then(data => {
                actividadesGlobal = data;
                renderActividades(data);
            })
            .catch(err => console.error("Error cargando actividades:", err));
    }

    function renderActividades(lista) {
        let html = lista.length === 0 
            ? '<tr><td colspan="6" class="text-center">No se encontraron resultados</td></tr>'
            : lista.map(a => `
                <tr>
                    <td>${a.id_actividad}</td>
                    <td>${a.id_usuario}</td>
                    <td>${a.accion}</td>
                    <td>${a.modulo}</td>
                    <td>${a.fecha}</td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-info" onclick="ver(${a.id_actividad})"><i class="bi bi-eye"></i></button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="imprimir(${a.id_actividad})"><i class="bi bi-printer"></i></button>
                    </td>
                </tr>`).join('');
        tabla.innerHTML = html;
    }

    document.getElementById("buscador").addEventListener("input", function () {
        let t = this.value.toLowerCase();
        renderActividades(actividadesGlobal.filter(a => 
            String(a.id_actividad).includes(t) || 
            String(a.id_usuario).includes(t) || 
            a.accion.toLowerCase().includes(t) || 
            a.modulo.toLowerCase().includes(t)
        ));
    });

    function ver(id) {
        fetch('../api/actividades/ver.php?id=' + id)
            .then(res => res.json())
            .then(a => {
                document.getElementById("mod_id").innerText = a.id_actividad;
                document.getElementById("mod_usuario").innerText = `${a.nombre_usuario} (ID: ${a.id_usuario})`;
                document.getElementById("mod_modulo").innerText = a.modulo;
                document.getElementById("mod_accion").innerText = a.accion;
                document.getElementById("mod_fecha").innerText = a.fecha;
                new bootstrap.Modal(document.getElementById('modalVerActividad')).show();
            });
    }

    function imprimir(id) { window.open('../report/imprimir_actividad.php?id=' + id, '_blank'); }

    listarActividades();
</script>

<div class="modal fade" id="modalVerActividad" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Detalle de Actividad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tr><th>Usuario</th><td id="mod_usuario">-</td></tr>
                    <tr><th>Módulo</th><td id="mod_modulo">-</td></tr>
                    <tr><th>Acción</th><td id="mod_accion">-</td></tr>
                    <tr><th>Fecha</th><td id="mod_fecha">-</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>