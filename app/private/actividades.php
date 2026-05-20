<?php include 'header.php'; ?>
<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">

        <h4 class="fw-bold mb-0">
            <i class="bi bi-person-gear"></i> Actividades de Usuarios
        </h4>

        <div class="d-flex gap-2 w-100 w-md-auto">

            <div class="input-group">
                <span class="input-group-text bg-light">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="buscador" class="form-control" placeholder="Buscar por nombre, usuario o rol...">
            </div>
        </div>
    </div>


    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID_actividad</th>
                        <th>Id_usuario</th>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const tabla = document.getElementById("tablaActividades");
        const toastElement = document.getElementById('toastLive');
    const toastMensaje = document.getElementById('toastMensaje');
    const toast = new bootstrap.Toast(toastElement);
    let actividadesGlobal = [];

    function mostrarToast(mensaje, tipo = "success") {

        const colores = {
            success: "bg-success text-white",
            error: "bg-danger text-white",
            warning: "bg-warning text-dark",
            info: "bg-primary text-white"
        };

        toastElement.className = `toast align-items-center border-0 ${colores[tipo]}`;
        toastMensaje.innerHTML = mensaje;
        toast.show();
    }

    function logAccion(accion, detalle = "") {
        console.log(`[ACTIVIDADES] ${accion}`, detalle);
    }

    function listarActividades() {
        mostrarToast("Cargando actividades...", "info");
        logAccion("LISTAR_INICIO");

        fetch('../api/actividades/listar.php')
            .then(res => res.json())
            .then(data => {

                actividadesGlobal = data; // Guardamos en memoria
                renderActividades(data);

                mostrarToast("Actividades cargadas correctamente", "success");
                logAccion("LISTAR_OK", data.length + " registros");

            })

            .catch(error => {
                mostrarToast("Error al cargar actividades", "error");
                logAccion("LISTAR_ERROR", error);
            });
    }

    // RENDERIZAR

    function renderActividades(lista) {

        let html = '';

        if (lista.length === 0) {
            html = `
    <tr>
        <td colspan="6" class="text-center text-muted py-4">
            <i class="bi bi-search"></i> No se encontraron resultados
        </td>
    </tr>`;
        }

        lista.forEach(a => {
            html += `
    <tr>
        <td>${a.id_actividad}</td>
        <td>${a.id_usuario}</td>
        <td>${a.accion}</td>
        <td>${a.modulo}</td>
        <td>${a.fecha}</td>
        <td class="text-end">
            <button class="btn btn-sm btn-outline-info" onclick="ver(${a.id_actividad})">
              <i class="bi bi-eye"></i>
            </button>
             <button class="btn btn-sm btn-outline-secondary" onclick="imprimir(${a.id_actividad})"><i class="bi bi-printer"></i></button>
        </td>
    </tr>`;
        });

        tabla.innerHTML = html;
    }

    // FILTROS Y BUSCADORES
    document.getElementById("buscador")
        .addEventListener("input", function () {

            let texto = this.value.toLowerCase().trim();

            let filtrados = actividadesGlobal.filter(a =>
                a.id_actividad.toLowerCase().includes(texto) ||
                a.id_usuario.toLowerCase().includes(texto) ||
                a.accion.toLowerCase().includes(texto)||
                a.modulo.toLowerCase().includes(texto)
            );

            renderActividades(filtrados);

            logAccion("BUSQUEDA", texto);
        });
 function ver(id_actividad) {
    mostrarToast("Consultando información...", "info");
    logAccion("VER_INICIO", id_actividad);

    fetch('../api/actividades/ver.php?id=' + id_actividad)
        .then(res => {
            if(!res.ok) throw new Error('Error en la respuesta del servidor');
            return res.json();
        })
        .then(a => {
            // Rellenar campos del modal
            document.getElementById("mod_id").innerText = a.id_actividad;
            document.getElementById("mod_usuario").innerText = `${a.nombre_usuario} (ID: ${a.id_usuario})`;
            document.getElementById("mod_modulo").innerText = a.modulo;
            document.getElementById("mod_accion").innerText = a.accion;
            document.getElementById("mod_fecha").innerText = a.fecha;
            document.getElementById("mod_ip").innerText = a.ip ? a.ip : 'No registrada';

            // Configurar el botón de imprimir del modal de forma dinámica
            document.getElementById("btnImprimirModal").onclick = function() {
                imprimir(a.id_actividad);
            };

            // Inicializar y mostrar el modal de Bootstrap
            const myModal = new bootstrap.Modal(document.getElementById('modalVerActividad'));
            myModal.show();

            mostrarToast("Detalles cargados", "success");
            logAccion("VER_OK", a);
        })
        .catch(error => {
            mostrarToast("Error al consultar actividad", "error");
            logAccion("VER_ERROR", error);
        });
}
    
    function imprimir(id_actividad) {
        window.open('../report/imprimir actividad.php?id_actividad=' + id_actividad, '_blank');
    }

listarActividades();

</script>
<div class="modal fade" id="modalVerActividad" tabindex="-1" aria-labelledby="modalVerActividadLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="modalVerActividadLabel">
                    <i class="bi bi-info-circle-fill me-2"></i> Detalle de Actividad
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-distmiss="modal" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <table class="table table-bordered mb-0">
                    <tr>
                        <th class="bg-light" style="width: 35%;">ID Actividad</th>
                        <td id="mod_id">-</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Usuario</th>
                        <td id="mod_usuario">-</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Módulo</th>
                        <td><span class="badge bg-secondary" id="mod_modulo">-</span></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Acción</th>
                        <td id="mod_accion" class="fw-bold text-dark">-</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Fecha y Hora</th>
                        <td id="mod_fecha">-</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Dirección IP</th>
                        <td id="mod_ip"><code>-</code></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" id="btnImprimirModal" class="btn btn-primary">
                    <i class="bi bi-printer"></i> Imprimir Ficha
                </button>
            </div>
        </div>
    </div>
</div>


<?php include 'footer.php'; ?>