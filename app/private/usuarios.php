<?php include 'header.php'; ?>

<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">

        <h4 class="fw-bold mb-0">
            <i class="bi bi-person-gear"></i> Gestión de Usuarios
        </h4>

        <div class="d-flex gap-2 w-100 w-md-auto">

            <div class="input-group">
                <span class="input-group-text bg-light">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="buscador" class="form-control" placeholder="Buscar por nombre, usuario o rol...">
            </div>

            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUsuario">
                <i class="bi bi-plus-circle"></i>
                <span class="d-none d-md-inline">Nuevo</span>
            </button>

        </div>
    </div>


    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaUsuarios"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL -->
<div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered"> <div class="modal-content border-0 shadow-lg">
            
            <div class="modal-header bg-light border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-primary" id="modalUsuarioLabel">
                    <i class="bi bi-person-badge-fill me-2"></i>
                    <span id="modalTitulo">Configuración de Usuario</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formUsuario" class="needs-validation" novalidate>
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="id">
                    
                    <p class="text-uppercase text-muted fw-bold small mb-3">Información Personal</p>
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Nombre Completo</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-person text-muted"></i></span>
                                <input type="text" name="nombre" id="nombre" class="form-control border-start-0" placeholder="Ej. Juan Pérez" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-semibold">DNI / Documento</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-card-text text-muted"></i></span>
                                <input type="text" name="dni" id="dni" class="form-control border-start-0" placeholder="00000000" required>
                            </div>
                        </div>
                    </div>

                    <p class="text-uppercase text-muted fw-bold small mb-3">Seguridad y Acceso</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Nombre de Usuario</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-at text-muted"></i></span>
                                <input type="text" name="usuario" id="usuario" class="form-control border-start-0" placeholder="usuario123" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Rol del Sistema</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-shield-lock text-muted"></i></span>
                                <select name="rol" id="rol" class="form-select border-start-0" required>
                                    <option value="" selected disabled>Elegir...</option>
                                    <option value="admin">Administrador</option>
                                    <option value="secretario">Secretario</option>
                                    <option value="archivista">Archivista</option>
                                    <option value="parroco">Párroco</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-key text-muted"></i></span>
                                <input type="password" name="contraseña" id="contraseña" class="form-control border-start-0" placeholder="••••••••">
                            </div>
                            <div id="passwordHelp" class="form-text mt-1">
                                <i class="bi bi-info-circle me-1"></i> Dejar en blanco para mantener la actual.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top-0 p-4">
                    <button type="button" class="btn btn-outline-secondary border-0 fw-semibold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm fw-semibold">
                        <i class="bi bi-check2-circle me-1"></i> Guardar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Efecto de foco elegante */
    #modalUsuario .form-control:focus, 
    #modalUsuario .form-select:focus {
        border-color: #0d6efd;
        box-shadow: none;
        background-color: #f8fbff;
    }
    
    #modalUsuario .input-group-text {
        border-right: none;
    }
    
    #modalUsuario .form-control, 
    #modalUsuario .form-select {
        border-left: none;
    }

    #modalUsuario .modal-content {
        border-radius: 15px;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const tabla = document.getElementById("tablaUsuarios");
    const modal = new bootstrap.Modal(document.getElementById('modalUsuario'));
    const toastElement = document.getElementById('toastLive');
    const toastMensaje = document.getElementById('toastMensaje');
    const toast = new bootstrap.Toast(toastElement);
    let usuariosGlobal = [];

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
        console.log(`[USUARIOS] ${accion}`, detalle);
    }

    function listarUsuarios() { 
        mostrarToast("Cargando usuarios...", "info");
        logAccion("LISTAR_INICIO");

        fetch('../api/usuarios/listar.php')
            .then(res => res.json())
            .then(data => {

                usuariosGlobal = data; // Guardamos en memoria
                renderUsuarios(data);

                mostrarToast("Usuarios cargados correctamente", "success");
                logAccion("LISTAR_OK", data.length + " registros");

            })

            .catch(error => {
                mostrarToast("Error al cargar usuarios", "error");
                logAccion("LISTAR_ERROR", error);
            });
    }

    // RENDERIZAR

    function renderUsuarios(lista) {

        let html = '';

        if (lista.length === 0) {
            html = `
    <tr>
        <td colspan="6" class="text-center text-muted py-4">
            <i class="bi bi-search"></i> No se encontraron resultados
        </td>
    </tr>`;
        }

        lista.forEach(u => {
            html += `
    <tr>
        <td>${u.id}</td>
        <td>${u.nombre}</td>
        <td>${u.usuario}</td>
        <td><span class="badge bg-info text-dark">${u.rol}</span></td>
        <td>
            <span class="badge ${u.estado == 1 ? 'bg-success' : 'bg-secondary'}">
                ${u.estado == 1 ? 'Activo' : 'Inactivo'}
            </span>
        </td>
        <td class="text-end">
            <button class="btn btn-sm btn-outline-primary" onclick="editar(${u.id})">
                <i class="bi bi-pencil"></i>
            </button>
            <button class="btn btn-sm btn-outline-warning" onclick="cambiarEstado(${u.id})">
                <i class="bi bi-arrow-repeat"></i>
            </button>
            <button class="btn btn-sm btn-outline-info" onclick="ver(${u.id})">
                <i class="bi bi-eye"></i>
            </button>
        </td>
    </tr>`;
        });

        tabla.innerHTML = html;
    }

    // FILTROS Y BUSCADORES
    document.getElementById("buscador")
        .addEventListener("input", function () {

            let texto = this.value.toLowerCase().trim();

            let filtrados = usuariosGlobal.filter(u =>
                u.nombre.toLowerCase().includes(texto) ||
                u.usuario.toLowerCase().includes(texto) ||
                u.rol.toLowerCase().includes(texto)
            );

            renderUsuarios(filtrados);

            logAccion("BUSQUEDA", texto);
        });


    // GUARDAR / ACTUALIZAR
    document.getElementById("formUsuario")
        .addEventListener("submit", function (e) {

            e.preventDefault();

            let formData = new FormData(this);
            let accion = formData.get("id") ? "ACTUALIZAR" : "CREAR";

            mostrarToast("Procesando solicitud...", "info");
            logAccion(accion + "_INICIO");

            fetch('../api/usuarios/guardar.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {

                    if (data.error) {
                        mostrarToast(data.error, "error");
                        logAccion(accion + "_ERROR", data.error);
                        return;
                    }

                    modal.hide();
                    this.reset();
                    listarUsuarios();

                    mostrarToast("Usuario guardado correctamente", "success");
                    logAccion(accion + "_OK");

                })
                .catch(error => {
                    mostrarToast("Error en la operación", "error");
                    logAccion(accion + "_ERROR", error);
                });
        });


    function editar(id) {

        mostrarToast("Cargando información del usuario...", "info");
        logAccion("EDITAR_INICIO", id);

        fetch('../api/usuarios/ver.php?id=' + id)
            .then(res => res.json())
            .then(u => {

                document.getElementById("id").value = u.id;
                document.getElementById("nombre").value = u.nombre;
                document.getElementById("dni").value = u.dni;
                document.getElementById("usuario").value = u.usuario;
                document.getElementById("rol").value = u.rol;
                document.getElementById("contraseña").value = '';

                modal.show();

                mostrarToast("Usuario listo para edición", "success");
                logAccion("EDITAR_OK", u);

            })
            .catch(error => {
                mostrarToast("Error al obtener usuario", "error");
                logAccion("EDITAR_ERROR", error);
            });
    }


    function ver(id) {

        mostrarToast("Consultando información...", "info");
        logAccion("VER_INICIO", id);

        fetch('../api/usuarios/ver.php?id=' + id)
            .then(res => res.json())
            .then(u => {
                mostrarToast(`Usuario: ${u.nombre} | Rol: ${u.rol}`, "info");
                logAccion("VER_OK", u);
            })
            .catch(error => {
                mostrarToast("Error al consultar usuario", "error");
                logAccion("VER_ERROR", error);
            });
    }


    function cambiarEstado(id) {

        mostrarToast("Actualizando estado...", "warning");
        logAccion("ESTADO_INICIO", id);

        fetch('../api/usuarios/estado.php?id=' + id)
            .then(res => res.json())
            .then(data => {
                listarUsuarios();
                mostrarToast("Estado actualizado correctamente", "success");
                logAccion("ESTADO_OK");
            })
            .catch(error => {
                mostrarToast("Error al cambiar estado", "error");
                logAccion("ESTADO_ERROR", error);
            });
    }


    listarUsuarios();
</script>


<?php include 'footer.php'; ?>

<!-- ======================
ENDPOINTS NECESARIOS:
../api/usuarios/listar.php
../api/usuarios/ver.php
../api/usuarios/guardar.php
../api/usuarios/estado.php
(usar PDO + password_hash() para contraseña)
====================== -->