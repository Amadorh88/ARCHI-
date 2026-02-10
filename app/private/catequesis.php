<?php include 'header.php'; ?>

<div class="container-fluid py-4">

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-2 d-flex justify-content-center gap-2 flex-wrap">
                    <button class="btn btn-primary active nav-link-custom"
                        onclick="showSection('sec-catequesis', this)">
                        <i class="bi bi-journal-bookmark"></i> Inscripciones
                    </button>
                    <button class="btn btn-outline-primary nav-link-custom" onclick="showSection('sec-cursos', this)">
                        <i class="bi bi-mortarboard"></i> Cursos
                    </button>
                    <button class="btn btn-outline-primary nav-link-custom"
                        onclick="showSection('sec-catequistas', this)">
                        <i class="bi bi-person-workspace"></i> Catequistas
                    </button>
                    <button class="btn btn-outline-primary nav-link-custom" onclick="showSection('sec-periodos', this)">
                        <i class="bi bi-calendar-range"></i> Periodos
                    </button>
                </div>
            </div>
        </div>
    </div>

    <section id="sec-catequesis" class="content-section">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
            <h4 class="fw-bold mb-0 text-primary"><i class="bi bi-journal-check"></i> Registro de Inscripciones</h4>
            <div class="d-flex gap-2">
                <input type="text" id="busCatequesis" class="form-control" placeholder="Buscar feligres o curso..."
                    onkeyup="filtrarTabla('tablaCatequesis', this.value)">
                <button class="btn btn-primary" onclick="nuevaCatequesis()">
                    <i class="bi bi-plus-circle"></i> Nueva Inscripción
                </button>
            </div>
        </div>
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>feligres</th>
                            <th>Sacramento</th>
                            <th>Curso / Mentor</th>
                            <th>Periodo</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaCatequesis"></tbody>
                </table>
            </div>
        </div>
    </section>

    <section id="sec-cursos" class="content-section d-none">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
            <h4 class="fw-bold mb-0 text-success"><i class="bi bi-mortarboard"></i> Catálogo de Cursos</h4>
            <div class="d-flex gap-2">
                <input type="text" id="busCursos" class="form-control" placeholder="Buscar curso..."
                    onkeyup="filtrarTabla('tablaCursos', this.value)">
                <button class="btn btn-success" onclick="nuevoCurso()">
                    <i class="bi bi-plus-circle"></i> Crear Curso
                </button>
            </div>
        </div>
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre del Curso</th>
                            <th>Duración</th>
                            <th>Catequista Asignado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaCursos"></tbody>
                </table>
            </div>
        </div>
    </section>

    <section id="sec-catequistas" class="content-section d-none">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
            <h4 class="fw-bold mb-0 text-dark"><i class="bi bi-person-badge"></i> Cuerpo de Catequistas</h4>
            <div class="d-flex gap-2">
                <input type="text" id="busCatequistas" class="form-control" placeholder="Buscar mentor..."
                    onkeyup="filtrarTabla('tablaCatequistas', this.value)">
                <button class="btn btn-dark" onclick="nuevoCatequista()">
                    <i class="bi bi-person-plus"></i> Nuevo Mentor
                </button>
            </div>
        </div>
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre Completo</th>
                            <th>Teléfono</th>
                            <th>Especialidad</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaCatequistas"></tbody>
                </table>
            </div>
        </div>
    </section>

    <section id="sec-periodos" class="content-section d-none">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
            <h4 class="fw-bold mb-0 text-info"><i class="bi bi-calendar3"></i> Periodos Lectivos</h4>
            <div class="d-flex gap-2">
                <input type="text" id="busPeriodos" class="form-control" placeholder="Buscar año..."
                    onkeyup="filtrarTabla('tablaPeriodos', this.value)">
                <button class="btn btn-info text-white" onclick="nuevoPeriodo()">
                    <i class="bi bi-plus-circle"></i> Nuevo Periodo
                </button>
            </div>
        </div>
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>

                            <th>Inicio</th>
                            <th>Fin</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaPeriodos"></tbody>
                </table>
            </div>
        </div>
    </section>

</div>

<?php include 'partials/modales_catequesis.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>



<script>
    // --- CONFIGURACIÓN Y ESTADO GLOBAL ---
    let MODALES = {};

    // --- NAVEGACIÓN SPA ---
    function showSection(sectionId, btn) {
        document.querySelectorAll('.content-section').forEach(sec => sec.classList.add('d-none'));
        document.querySelectorAll('.nav-link-custom').forEach(b => {
            b.classList.remove('active', 'btn-primary');
            b.classList.add('btn-outline-primary');
        });

        const target = document.getElementById(sectionId);
        if (target) {
            target.classList.remove('d-none');
            btn.classList.add('active', 'btn-primary');
            btn.classList.remove('btn-outline-primary');
        }

        // Carga selectiva de datos según la sección
        if (sectionId === 'sec-catequesis') listarCatequesis();
        if (sectionId === 'sec-cursos') listarCursos();
        if (sectionId === 'sec-catequistas') listarCatequistas();
        if (sectionId === 'sec-periodos') listarPeriodos();
    }

    function listarCatequesis() {
        fetch('../api/catequesis/listar.php')
            .then(res => res.json())
            .then(data => {

                const tabla = document.getElementById("tablaCatequesis");
                if (!tabla) return;

                if (data.error) {
                    tabla.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center text-danger">
                            Error: ${data.message}
                        </td>
                    </tr>`;
                    return;
                }

                tabla.innerHTML = data.length === 0
                    ? '<tr><td colspan="5" class="text-center">No hay registros</td></tr>'
                    : data.map(cat => `
                    <tr id="fila-${cat.id_catequesis}">
                        <td>
                            <div class="fw-bold text-dark">${cat.nombre_feligres}</div>
                            <small class="text-muted">
                                <i class="bi bi-house-door"></i> 
                                ${cat.nombre_parroquia || 'Parroquia no asignada'}
                            </small>
                        </td>

                        <td>
                            <span class="badge bg-light text-primary border border-primary">
                                ${cat.tipo}
                            </span>
                        </td>

                        <td>
                            <i class="bi bi-book small"></i> 
                            ${cat.nombre_curso || 'N/A'}
                        </td>

                        <td>
                            <span class="badge bg-secondary">
                                ${cat.anio || 'S/P'}
                            </span>
                        </td>

                        <td class="text-end">

                            <!-- Toggle Estado -->
                            <div class="form-check form-switch d-inline-block me-2">
                                <input 
                                    class="form-check-input"
                                    type="checkbox"
                                    ${cat.estado == 1 ? 'checked' : ''}
                                    onchange="toggleEstadoCatequesis(${cat.id_catequesis}, this)">
                            </div>

                            <button 
                                class="btn btn-sm btn-outline-primary"
                                onclick="editarCatequesis(${cat.id_catequesis})">
                                <i class="bi bi-pencil"></i>
                            </button>

                        </td>
                    </tr>
                `).join('');

            })
            .catch(err => console.error("Error listarCatequesis:", err));
    }
    function listarCursos() {
        fetch('../api/cursos/listar.php').then(res => res.json()).then(data => {
            const tabla = document.getElementById("tablaCursos");
            if (tabla) tabla.innerHTML = data.map(c => `
                <tr>
                    <td class="fw-bold">${c.nombre}</td>
                    <td>${c.duracion || '---'}</td>
                    <td>${c.nombre_catequista || '<span class="text-muted">No asignado</span>'}</td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-success" onclick="editarCurso(${c.id_curso})"><i class="bi bi-gear"></i></button>
                        </td>
                </tr>
            `).join('');
        });
    }

    function listarCatequistas() {
        fetch('../api/catequistas/listar.php').then(res => res.json()).then(data => {
            const tabla = document.getElementById("tablaCatequistas");
            console.log(data)
            if (tabla) tabla.innerHTML = data.map(c => `
                <tr>
                    <td class="fw-bold">${c.nombre}</td>
                    <td>${c.telefono || '---'}</td>
                    <td><span class="badge bg-dark">${c.especialidad || 'General'}</span></td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-dark" onclick="editarCatequista(${c.id_catequista})"><i class="bi bi-pencil"></i></button>
                          
                        </td>
                </tr>
            `).join('');
        });
    }

    function listarPeriodos() {
        fetch('../api/periodos/listar.php').then(res => res.json()).then(data => {
            const tabla = document.getElementById("tablaPeriodos");
            console.log(data)
            if (tabla) tabla.innerHTML = data.map(p => `
                <tr>
                     
                    <td>${p.fecha_inicio || '---'}</td>
                    <td>${p.fecha_fin || '---'}</td>
                    <td> 
                    <button 
                    class="btn btn-sm ${p.estado === 'activo' ? 'btn-warning' : 'btn-success'}" 
                    onclick="togglePeriodo(${p.id_periodo}, '${p.estado}')">
                    <i class="bi ${p.estado === 'activo' ? 'bi-pause-circle' : 'bi-play-circle'}"></i> 
                    ${p.estado === 'activo' ? 'Desactivar' : 'Activar'}
                    </button>
                    </td>
                    
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-info" onclick="editarPeriodo(${p.id_periodo})"><i class="bi bi-pencil"></i></button>
                       
                    <button class="btn btn-sm btn-outline-success" onclick="verPeriodo(${p.id_periodo})">
                        <i class="bi bi-eye"></i> 
                    </button>
                    
   
                        </td>
                </tr>
            `).join('');
        });
    }

    // --- CARGA DE SELECTORES ---
    async function cargarSelects() {
        try {
            const [resF, resC, resCat, resP, resPa] = await Promise.all([
                fetch('../api/feligres/listar.php').then(r => r.json()),
                fetch('../api/cursos/listar.php').then(r => r.json()),
                fetch('../api/catequistas/listar.php').then(r => r.json()),
                fetch('../api/periodos/listar.php').then(r => r.json()),
                fetch('../api/parroquias/listar.php').then(r => r.json())
            ]);

            const llenar = (id, data, val, text, extra = "") => {
                const el = document.getElementById(id);
                if (el) el.innerHTML = extra + data.map(d => `<option value="${d[val]}">${d[text]}</option>`).join('');
            };

            llenar('selFeligres', resF, 'id_feligres', 'nombre_completo', '<option value="">Seleccione...</option>');
            llenar('selCurso', resC, 'id_curso', 'nombre', '<option value="">-- Sin curso --</option>');
            llenar('selCatequista', resCat, 'id_catequista', 'nombre', '<option value="">-- Seleccionar Mentor --</option>');
            llenar('selPeriodo', resP, 'id_periodo', 'anio', '<option value="">Seleccione periodo...</option>');
            llenar('selParroquia', resPa, 'id_parroquia', 'nombre', '<option value="">Seleccione Parroquia...</option>');

        } catch (e) { console.warn("Error cargando catálogos:", e); }
    }

    // --- UTILIDADES ---
    function filtrarTabla(tablaId, valor) {
        const rows = document.getElementById(tablaId)?.getElementsByTagName("tr");
        if (!rows) return;
        valor = valor.toLowerCase();
        for (let row of rows) {
            row.style.display = row.innerText.toLowerCase().includes(valor) ? "" : "none";
        }
    }

    function configForm(formId, apiFolder, modalKey) {
        const form = document.getElementById(formId);
        if (!form) return;

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            fetch(`../api/${apiFolder}/guardar.php`, { method: 'POST', body: new FormData(this) })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        if (MODALES[modalKey]) MODALES[modalKey].hide();
                        location.reload();
                    } else {
                        alert("Error: " + (data.message || "No se pudo guardar"));
                    }
                }).catch(err => console.error("Error al guardar:", err));
        });
    }



    // --- FUNCIONES "NUEVO" (CON PROTECCIÓN) ---
    function nuevaCatequesis() { document.getElementById('formCatequesis')?.reset(); MODALES.catequesis?.show(); }
    function nuevoCurso() { document.getElementById('formCurso')?.reset(); MODALES.curso?.show(); }
    function nuevoCatequista() { document.getElementById('formCatequista')?.reset(); MODALES.catequista?.show(); }
    function nuevoPeriodo() { document.getElementById('formPeriodo')?.reset(); MODALES.periodo?.show(); }

    // --- FUNCIONES CATEQUESIS --- 

    //toggle catequesis 
    function toggleEstadoCatequesis(id, elemento) {

        const nuevoEstado = elemento.checked ? 1 : 0;

        const formData = new FormData();
        formData.append('id_catequesis', id);
        formData.append('estado', nuevoEstado);

        fetch('../api/catequesis/toggle.php', {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(data => {

                if (!data.success) {
                    alert(data.message);
                    elemento.checked = !elemento.checked; // Revertir si falla
                }

            })
            .catch(error => {
                console.error("Error:", error);
                elemento.checked = !elemento.checked; // Revertir si error
                alert("Error en la petición");
            });
    }

    // Abrir modal para NUEVA catequesis
    function nuevaCatequesis() {
        const form = document.getElementById('formCatequesis');
        if (!form) return;
        form.reset();
        form.querySelector('#id_catequesis').value = ''; // asegurarse de limpiar el id
        MODALES.catequesis?.show();
    }

    // Abrir modal para EDITAR catequesis
    function editarCatequesis(id) {
        fetch(`../api/catequesis/ver.php?id=${id}`)
            .then(res => res.json())
            .then(data => {
                console.log(data.data.id_periodo)
                if (data.error) {
                    alert("Error al obtener los datos: " + data.error);
                    return;
                }

                const form = document.getElementById('formCatequesis');
                if (!form) return;

                // Llenar campos
                form.querySelector('#id_catequesis').value = data.data.id_catequesis || '';
                form.querySelector('[name="id_feligres"]').value = data.data.id_feligres || '';
                form.querySelector('[name="tipo"]').value = data.data.tipo || '';
                form.querySelector('[name="id_periodo"]').value = data.data.id_periodo || '';
                form.querySelector('[name="id_curso"]').value = data.data.id_curso || '';
                form.querySelector('[name="id_parroquia"]').value = data.data.id_parroquia || '';
               /*  form.querySelector('[name="nombre_catequesis"]').value = data.data.nombre_catequesis || ''; */

                MODALES.catequesis?.show();
            })
            .catch(err => console.error("Error editarCatequesis:", err));
    }

    // Guardar o editar catequesis
    function guardarCatequesis() {
        const form = document.getElementById('formCatequesis');
        if (!form) return;

        const formData = new FormData(form);

        fetch('../api/catequesis/guardar.php', {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    MODALES.catequesis?.hide();
                    listarCatequesis(); // recarga la tabla
                    alert(data.message);
                } else {
                    alert("Error: " + (data.message || "No se pudo guardar"));
                }
            })
            .catch(err => console.error("Error guardarCatequesis:", err));
    }

    // Asociar al submit del form
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('formCatequesis');
        if (form) {
            form.addEventListener('submit', e => {
                e.preventDefault();
                guardarCatequesis();
            });
        }
    });


    // --- FUNCIONES PERIODO ---

    // Abrir modal NUEVO periodo
    function nuevoPeriodo() {
        const form = document.getElementById('formPeriodo');
        if (!form) return;
        form.reset();
        form.querySelector('#id_periodo').value = '';
        MODALES.periodo?.show();
    }

    // Abrir modal EDITAR periodo
    function editarPeriodo(id) {
        console.log(id)
        fetch(`../api/periodos/ver.php?id=${id}`)
            .then(res => res.json())
            .then(data => {
                console.log(data)
                if (data.error) {
                    alert("Error al obtener datos: " + data.error);
                    return;
                }

                const form = document.getElementById('formPeriodo');
                if (!form) return;

                form.querySelector('#id_periodo').value = data.periodo.id_periodo || '';
                form.querySelector('[name="fecha_inicio"]').value = data.periodo.fecha_inicio || '';
                form.querySelector('[name="fecha_fin"]').value = data.periodo.fecha_fin || '';
                form.querySelector('[name="estado"]').value = data.periodo.estado || 'activo';

                MODALES.periodo?.show();
            })
            .catch(err => console.error("Error editarPeriodo:", err));
    }

    // Validación de fechas
    // Validación de años para el periodo
    function validarPeriodo(anioInicio, anioFin) {
        anioInicio = parseInt(anioInicio, 10);
        anioFin = parseInt(anioFin, 10);
        const anioActual = new Date().getFullYear();

        if (isNaN(anioInicio) || isNaN(anioFin)) {
            return "El año de inicio y fin son obligatorios y deben ser números válidos.";
        }

        if (anioInicio > anioFin) {
            return "El año de inicio no puede ser mayor que el año de fin.";
        }

        if ((anioFin - anioInicio) !== 1) {
            return "El periodo debe tener exactamente 1 año de diferencia entre inicio y fin.";
        }

        if (anioInicio > anioActual) {
            return "El año de inicio no puede ser mayor al año actual.";
        }

        return true; // todo correcto
    }


    // Guardar o editar periodo
    function guardarPeriodo() {
        const form = document.getElementById('formPeriodo');
        if (!form) return;

        const fechaInicio = form.querySelector('[name="fecha_inicio"]').value;
        const fechaFin = form.querySelector('[name="fecha_fin"]').value;

        // Validación local
        const validacion = validarPeriodo(fechaInicio, fechaFin);
        if (validacion !== true) {
            alert(validacion);
            return;
        }

        const formData = new FormData(form);

        fetch('../api/periodos/guardar.php', {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    MODALES.periodo?.hide();
                    listarPeriodos(); // recarga la tabla
                    alert(data.message);
                } else {
                    alert("Error: " + (data.message || "No se pudo guardar"));
                }
            })
            .catch(err => console.error("Error guardarPeriodo:", err));
    }

    // Asociar submit del form al guardarPeriodo
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('formPeriodo');
        if (form) {
            form.addEventListener('submit', e => {
                e.preventDefault();
                guardarPeriodo();
            });
        }
    });
    function verPeriodo(id) {
        fetch(`../api/periodos/ver.php?id=${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    alert("Error: " + data.message);
                    return;
                }

                const p = data.periodo;
                const eventos = data.eventos;

                // Actualizar encabezado
                document.getElementById("verPeriodoNombre").innerText = p.nombre;
                document.getElementById("verPeriodoFechas").innerText = `Desde: ${p.fecha_inicio} Hasta: ${p.fecha_fin}`;
                const estadoEl = document.getElementById("verPeriodoEstado");
                estadoEl.innerText = p.estado;
                estadoEl.className = 'badge ' + (p.estado === 'activo' ? 'bg-success' : 'bg-secondary');

                // Función para renderizar listas
                const renderList = (idLista, items, template) => {
                    const ul = document.getElementById(idLista);
                    if (!ul) return;
                    if (items.length === 0) {
                        ul.innerHTML = '<li class="list-group-item text-muted">No hay registros</li>';
                    } else {
                        ul.innerHTML = items.map(template).join('');
                    }
                };

                // Renderizar catequesis
                renderList("listaCatequesis", eventos.catequesis, c => `
                <li class="list-group-item">
                    <strong>${c.feligres_nombre}</strong> | ${c.tipo} | Curso: ${c.curso_nombre || 'Sin curso'}
                </li>
            `);

                // Renderizar bautismos
                renderList("listaBautismos", eventos.bautismos, b => `
                <li class="list-group-item">
                    <strong>${b.feligres_nombre}</strong> | Registro: ${b.registro} | Fecha: ${b.fecha}
                </li>
            `);

                // Renderizar comuniones
                renderList("listaComuniones", eventos.comuniones, co => `
                <li class="list-group-item">
                    <strong>${co.feligres_nombre}</strong> | Registro: ${co.registro} | Fecha: ${co.fecha}
                </li>
            `);

                // Renderizar confirmaciones
                renderList("listaConfirmaciones", eventos.confirmaciones, cf => `
                <li class="list-group-item">
                    <strong>${cf.feligres_nombre}</strong> | Registro: ${cf.registro} | Fecha: ${cf.fecha}
                </li>
            `);

                // Renderizar matrimonios
                renderList("listaMatrimonios", eventos.matrimonios, m => `
                <li class="list-group-item">
                    <strong>${m.esposos}</strong> | Registro: ${m.registro} | Fecha: ${m.fecha}
                </li>
            `);

                // Mostrar modal
                if (MODALES.verPeriodo) {
                    MODALES.verPeriodo.show();
                } else {
                    MODALES.verPeriodo = new bootstrap.Modal(document.getElementById('modalVerPeriodo'));
                    MODALES.verPeriodo.show();
                }

            }).catch(err => console.error("Error al cargar periodo:", err));
    }



    function togglePeriodo(id, estadoActual) {
        const accion = estadoActual === 'activo' ? 'desactivar' : 'activar';
        if (!confirm(`¿Desea ${accion} este periodo?`)) return;

        fetch(`../api/periodos/toggle.php?id=${id}`, { method: 'POST' })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    listarPeriodos(); // refresca tabla
                } else {
                    alert("Error: " + (data.message || "No se pudo cambiar el estado"));
                }
            }).catch(err => console.error("Error togglePeriodo:", err));
    }


    // VER CURSOS
    function verCurso(id) {
        fetch(`../api/cursos/ver.php?id=${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    alert("Error: " + data.message);
                    return;
                }

                const c = data.data;

                document.getElementById("verCursoNombre").innerText = c.nombre;
                document.getElementById("verCursoDuracion").innerText = c.duracion || '---';
                document.getElementById("verCursoCatequista").innerText = c.nombre_catequista || 'No asignado';
                document.getElementById("verCursoObs").innerText = c.observaciones || 'Sin observaciones';

                if (MODALES.verCurso) {
                    MODALES.verCurso.show();
                } else {
                    MODALES.verCurso = new bootstrap.Modal(document.getElementById('modalVerCurso'));
                    MODALES.verCurso.show();
                }
            })
            .catch(err => console.error("Error verCurso:", err));
    }


    // EDITAR CARSO
    function editarCurso(id) {
        fetch(`../api/cursos/ver.php?id=${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    alert("Error: " + data.message);
                    return;
                }

                const form = document.getElementById('formCurso');
                if (!form) return;

                const c = data;

                form.querySelector('[name="id_curso"]').value = c.id_curso || '';
                form.querySelector('[name="nombre"]').value = c.nombre || '';
                form.querySelector('[name="duracion"]').value = c.duracion || '';
                form.querySelector('[name="id_catequista"]').value = c.id_catequista || '';
                /*  form.querySelector('[name="observaciones"]').value = c.observaciones || ''; */

                MODALES.curso?.show();
            })
            .catch(err => console.error("Error editarCurso:", err));
    }

    // GUARDAR CURSOS
    function guardarCurso() {
        const form = document.getElementById('formCurso');
        if (!form) return;

        const formData = new FormData(form);

        fetch('../api/cursos/guardar.php', {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    MODALES.curso?.hide();
                    listarCursos();
                    alert(data.message);
                } else {
                    alert("Error: " + (data.message || "No se pudo guardar"));
                }
            })
            .catch(err => console.error("Error guardarCurso:", err));
    }

    // VER CATEQUISTA
    function verCatequista(id) {
        fetch(`../api/catequistas/ver.php?id=${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    alert("Error: " + data.message);
                    return;
                }

                const c = data.data;

                document.getElementById("verCatequistaNombre").innerText = c.nombre;
                document.getElementById("verCatequistaTelefono").innerText = c.telefono || '---';
                document.getElementById("verCatequistaEspecialidad").innerText = c.especialidad || 'General';

                if (MODALES.verCatequista) {
                    MODALES.verCatequista.show();
                } else {
                    MODALES.verCatequista = new bootstrap.Modal(document.getElementById('modalVerCatequista'));
                    MODALES.verCatequista.show();
                }
            })
            .catch(err => console.error("Error verCatequista:", err));
    }

    //EDITAR CATEQUISTA 
    function editarCatequista(id) {
        fetch(`../api/catequistas/ver.php?id=${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    alert("Error: " + data.message);
                    return;
                }

                const form = document.getElementById('formCatequista');
                if (!form) return;

                const c = data;

                form.querySelector('[name="id_catequista"]').value = c.id_catequista || '';
                form.querySelector('[name="nombre"]').value = c.nombre || '';
                form.querySelector('[name="telefono"]').value = c.telefono || '';
                form.querySelector('[name="especialidad"]').value = c.especialidad || '';

                MODALES.catequista?.show();
            })
            .catch(err => console.error("Error editarCatequista:", err));
    }


    // GUARDAR Catequistas
    function guardarCatequista() {
        const form = document.getElementById('formCatequista');
        if (!form) return;

        const formData = new FormData(form);

        fetch('../api/catequistas/guardar.php', {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    MODALES.catequista?.hide();
                    listarCatequistas();
                    alert(data.message);
                } else {
                    alert("Error: " + (data.message || "No se pudo guardar"));
                }
            })
            .catch(err => console.error("Error guardarCatequista:", err));
    }


    // --- INICIALIZACIÓN ÚNICA ---
    document.addEventListener('DOMContentLoaded', () => {
        const ids = {
            catequesis: 'modalCatequesis',
            curso: 'modalCurso',
            catequista: 'modalCatequista',
            periodo: 'modalPeriodo'
        };

        // Inicializar modales solo si existen en el HTML
        Object.keys(ids).forEach(key => {
            const el = document.getElementById(ids[key]);
            if (el) {
                MODALES[key] = new bootstrap.Modal(el);
            } else {
                console.warn(`Aviso: Elemento #${ids[key]} no encontrado.`);
            }
        });

        cargarSelects();
        listarCatequesis();

        // Configurar formularios
        configForm('formCatequesis', 'catequesis', 'catequesis');
        configForm('formCurso', 'cursos', 'curso');
        configForm('formCatequista', 'catequistas', 'catequista');
        configForm('formPeriodo', 'periodos', 'periodo');
    });
</script>
   <?php include 'footer.php'; ?>
 