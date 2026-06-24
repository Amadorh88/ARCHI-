<?php
include 'header.php';
registrarAccesoModulo(__FILE__);
?>

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

    <!-- SECCIÓN CATEQUESIS -->
    <section id="sec-catequesis" class="content-section">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
            <h4 class="fw-bold mb-0 text-primary"><i class="bi bi-journal-check"></i> Registro de Inscripciones</h4>
            <div class="d-flex gap-2">
                <input type="text" id="busCatequesis" class="form-control" placeholder="Buscar feligres o curso..." style="width: 250px;" onkeyup="filtrarTablaCatequesis()">
                <?php if (($rolUsuario !== 'archivista')): ?>
                <button class="btn btn-primary" onclick="nuevaCatequesis()">
                    <i class="bi bi-plus-circle"></i> Nueva Inscripción
                </button>
                 <?php endif; ?>
            </div>
        </div>
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Feligres</th>
                            <th>Catequesis</th>
                            <th>Curso</th>
                            <th>Periodo</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaCatequesis"></tbody>
                </table>
            </div>
            
            <div class="card-footer bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <label class="text-muted small mb-0">Mostrar:</label>
                        <select id="limiteCatequesis" class="form-select form-select-sm w-auto" onchange="cambiarLimiteCatequesis()">
                        <option value="3">3</option>
                        <option value="5" selected>5</option>
                        <option value="7">7</option>
                     </select>
                        <span class="text-muted small">registros por página</span>
                    </div>
                    <div class="text-muted small">
                        Mostrando <span id="infoCatequesisDesde">0</span> - <span id="infoCatequesisHasta">0</span> de <span id="infoCatequesisTotal">0</span> registros
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary btn-sm" id="btnCatequesisAnterior" onclick="paginaCatequesisAnterior()" disabled>
                            <i class="bi bi-chevron-left"></i> Anterior
                        </button>
                        <span class="badge bg-primary d-flex align-items-center px-3" id="paginaCatequesisActual">Página 1</span>
                        <button class="btn btn-outline-primary btn-sm" id="btnCatequesisSiguiente" onclick="paginaCatequesisSiguiente()" disabled>
                            Siguiente <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECCIÓN CURSOS -->
    <section id="sec-cursos" class="content-section d-none">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
            <h4 class="fw-bold mb-0 text-success"><i class="bi bi-mortarboard"></i> Catálogo de Cursos</h4>
            <div class="d-flex gap-2">
                <input type="text" id="busCursos" class="form-control" placeholder="Buscar curso..." style="width: 250px;" onkeyup="filtrarTablaCursos()">
                <?php if (($rolUsuario !== 'archivista')): ?>
                <button class="btn btn-success" onclick="nuevoCurso()">
                    <i class="bi bi-plus-circle"></i> Crear Curso
                </button>
                 <?php endif; ?>
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
            
            <div class="card-footer bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <label class="text-muted small mb-0">Mostrar:</label>
                        <select id="limiteCursos" class="form-select form-select-sm w-auto" onchange="cambiarLimiteCursos()">
                        <option value="3">3</option>
                        <option value="5" selected>5</option>
                        <option value="7">7</option>
                     </select>
                        <span class="text-muted small">registros por página</span>
                    </div>
                    <div class="text-muted small">
                        Mostrando <span id="infoCursosDesde">0</span> - <span id="infoCursosHasta">0</span> de <span id="infoCursosTotal">0</span> registros
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary btn-sm" id="btnCursosAnterior" onclick="paginaCursosAnterior()" disabled>
                            <i class="bi bi-chevron-left"></i> Anterior
                        </button>
                        <span class="badge bg-primary d-flex align-items-center px-3" id="paginaCursosActual">Página 1</span>
                        <button class="btn btn-outline-primary btn-sm" id="btnCursosSiguiente" onclick="paginaCursosSiguiente()" disabled>
                            Siguiente <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECCIÓN CATEQUISTAS -->
    <section id="sec-catequistas" class="content-section d-none">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
            <h4 class="fw-bold mb-0 text-dark"><i class="bi bi-person-badge"></i> Cuerpo de Catequistas</h4>
            <div class="d-flex gap-2">
                <input type="text" id="busCatequistas" class="form-control" placeholder="Buscar mentor..." style="width: 250px;" onkeyup="filtrarTablaCatequistas()">
                <?php if (($rolUsuario !== 'archivista')): ?>
                <button class="btn btn-dark" onclick="nuevoCatequista()">
                    <i class="bi bi-person-plus"></i> Nuevo Catequista
                </button>
                 <?php endif; ?>
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
            
            <div class="card-footer bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <label class="text-muted small mb-0">Mostrar:</label>
                        <select id="limiteCatequistas" class="form-select form-select-sm w-auto" onchange="cambiarLimiteCatequistas()">
                        <option value="3">3</option>
                        <option value="5" selected>5</option>
                        <option value="7">7</option>
                        </select>
                        <span class="text-muted small">registros por página</span>
                    </div>
                    <div class="text-muted small">
                        Mostrando <span id="infoCatequistasDesde">0</span> - <span id="infoCatequistasHasta">0</span> de <span id="infoCatequistasTotal">0</span> registros
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary btn-sm" id="btnCatequistasAnterior" onclick="paginaCatequistasAnterior()" disabled>
                            <i class="bi bi-chevron-left"></i> Anterior
                        </button>
                        <span class="badge bg-primary d-flex align-items-center px-3" id="paginaCatequistasActual">Página 1</span>
                        <button class="btn btn-outline-primary btn-sm" id="btnCatequistasSiguiente" onclick="paginaCatequistasSiguiente()" disabled>
                            Siguiente <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECCIÓN PERIODOS -->
    <section id="sec-periodos" class="content-section d-none">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
            <h4 class="fw-bold mb-0 text-info"><i class="bi bi-calendar3"></i> Periodos Lectivos</h4>
            <div class="d-flex gap-2">
                <input type="text" id="busPeriodos" class="form-control" placeholder="Buscar año..." style="width: 250px;" onkeyup="filtrarTablaPeriodos()">
                <?php if (($rolUsuario !== 'archivista')): ?>
                    <button class="btn btn-info text-white" onclick="nuevoPeriodo()">
                        <i class="bi bi-plus-circle"></i> Nuevo Periodo
                    </button>
                <?php endif; ?>
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
            
            <div class="card-footer bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <label class="text-muted small mb-0">Mostrar:</label>
                        <select id="limitePeriodos" class="form-select form-select-sm w-auto" onchange="cambiarLimitePeriodos()">
                        <option value="3">3</option>
                        <option value="5" selected>5</option>
                        <option value="7">7</option>
                        </select>
                        <span class="text-muted small">registros por página</span>
                    </div>
                    <div class="text-muted small">
                        Mostrando <span id="infoPeriodosDesde">0</span> - <span id="infoPeriodosHasta">0</span> de <span id="infoPeriodosTotal">0</span> registros
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary btn-sm" id="btnPeriodosAnterior" onclick="paginaPeriodosAnterior()" disabled>
                            <i class="bi bi-chevron-left"></i> Anterior
                        </button>
                        <span class="badge bg-primary d-flex align-items-center px-3" id="paginaPeriodosActual">Página 1</span>
                        <button class="btn btn-outline-primary btn-sm" id="btnPeriodosSiguiente" onclick="paginaPeriodosSiguiente()" disabled>
                            Siguiente <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>

<!-- MODALES CATEQUESIS -->
<!-- Modal Catequesis -->
<div class="modal fade" id="modalCatequesis" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form id="formCatequesis">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-journal-bookmark-fill me-2"></i>Registro de Inscripción
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <input type="hidden" name="id_catequesis" id="id_catequesis">
                        
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Tipo de Catequesis <span class="text-danger">*</span></label>
                            <select name="tipo" id="tipo_catequesis" class="form-select" required>
                                <option value="">Seleccionar tipo...</option>
                                <option value="Bautismal">Catequesis Bautismal</option>
                                <option value="Primera comunión">Primera Comunión</option>
                                <option value="Confirmación">Confirmación</option>
                                <option value="Matrimonial">Catequesis Matrimonial</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Feligrés <span class="text-danger">*</span></label>
                            <select name="id_feligres" id="selFeligresApto" class="form-select" required disabled>
                                <option value="">-- Primero seleccione un tipo de catequesis --</option>
                            </select>
                            <div id="mensajeRequisitos" class="small mt-1"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Curso</label>
                            <select name="id_curso" id="selCurso" class="form-select" required>
                                <option value="">-- Seleccione un curso --</option>
                            </select>
                            <div id="mensajeCurso" class="small text-danger mt-1" style="display:none;">
                                <i class="bi bi-exclamation-triangle-fill"></i> El curso seleccionado no corresponde al tipo de catequesis
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Periodo Lectivo <span class="text-danger">*</span></label>
                            <select name="id_periodo" id="selPeriodo" class="form-select" required>
                                <option value="">-- Seleccione un periodo --</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Parroquia <span class="text-danger">*</span></label>
                            <select name="id_parroquia" id="selParroquia" class="form-select" required>
                                <option value="">-- Seleccione una parroquia --</option>
                            </select>
                        </div>

                        <!-- SECCIÓN DE ESTIPENDIO -->
                        <div class="col-md-12 mt-3 border-top pt-3" id="seccionEstipendio">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-success"><i class="bi bi-cash-stack"></i> Estipendio Requerido</span>
                                <button type="button" class="btn btn-outline-success btn-sm" id="btnRegistrarEstipendio" onclick="abrirDonacionDesdeCatequesis()">
                                    <i class="bi bi-plus-circle"></i> Registrar Estipendio
                                </button>
                            </div>
                            <div id="infoEstipendio" style="display:none;" class="mt-2">
                                <div class="alert alert-success py-2">
                                    <i class="bi bi-check-circle-fill me-2"></i> 
                                    <span id="infoEstipendioText">Estipendio registrado correctamente</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <div class="d-flex justify-content-end w-100">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-4" id="btnGuardarCatequesis" disabled>
                            <i class="bi bi-save me-1"></i> Guardar Inscripción
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de donación rápida desde catequesis -->
<div class="modal fade" id="modalDonacionRapidaCatequesis" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form id="formDonacionRapidaCatequesis">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-cash-stack me-2"></i>Registro de Estipendio
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 row g-3">
                    <input type="hidden" name="id_feligres" id="id_feligres_donacion_catequesis">
                    <input type="hidden" name="registrar_pago" value="1">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Feligrés</label>
                        <input type="text" id="nombre_feligres_donacion_catequesis" class="form-control" readonly>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Concepto <span class="text-danger">*</span></label>
                        <input type="text" name="concepto" id="concepto_donacion" class="form-control" required readonly>
                        <small class="text-muted">Concepto generado automáticamente según el tipo de catequesis</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Monto <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="cantidad" id="cantidad_donacion" class="form-control" required oninput="calcularCambioDonacion()" min="1" step="1">
                            <span class="input-group-text">FCFA</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Monto Recibido</label>
                        <div class="input-group">
                            <input type="number" name="recibido" id="recibido_donacion" class="form-control" oninput="calcularCambioDonacion()" min="0" step="1">
                            <span class="input-group-text">FCFA</span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="p-3 bg-light rounded d-flex justify-content-between align-items-center border border-success">
                            <span class="fw-bold text-muted small">CAMBIO A DEVOLVER:</span>
                            <span class="h4 mb-0 fw-bold text-success" id="vueltoTextDonacion">0 FCFA</span>
                            <input type="hidden" name="cambio" id="cambio_donacion">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="confirmarEstipendio" required>
                            <label class="form-check-label" for="confirmarEstipendio">
                                Confirmo que he recibido el estipendio y verifico los datos ingresados
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

<!-- Modal Curso -->
<div class="modal fade" id="modalCurso" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formCurso">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="bi bi-mortarboard"></i> Curso</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <input type="hidden" name="id_curso" id="id_curso">
                    <div class="col-12">
                        <label class="form-label">Nombre del Curso <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Duración</label>
                        <input type="text" name="duracion" class="form-control" placeholder="Ej: 9 meses">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Catequista</label>
                        <select name="id_catequista" id="selCatequista" class="form-select">
                            <option value="">-- Seleccionar Mentor --</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Catequista -->
<div class="modal fade" id="modalCatequista" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formCatequista">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title"><i class="bi bi-person-badge"></i> Catequista</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <input type="hidden" name="id_catequista" id="id_catequista">
                    <div class="col-12">
                        <label class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Especialidad</label>
                        <input type="text" name="especialidad" class="form-control" placeholder="Ej: Catequesis infantil">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-dark">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Periodo -->
<div class="modal fade" id="modalPeriodo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formPeriodo">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="bi bi-calendar3"></i> Periodo Lectivo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <input type="hidden" name="id_periodo" id="id_periodo">
                    <div class="col-md-6">
                        <label class="form-label">Año Inicio <span class="text-danger">*</span></label>
                        <input type="number" name="fecha_inicio" id="fecha_inicio" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Año Fin <span class="text-danger">*</span></label>
                        <input type="number" name="fecha_fin" id="fecha_fin" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="activo">Activo</option>
                            <option value="finalizado">Finalizado</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info text-white">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modales de Ver -->
<div class="modal fade" id="modalVerCatequista" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Detalle del Catequista</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Nombre:</strong> <span id="verCatequistaNombre"></span></p>
                <p><strong>Teléfono:</strong> <span id="verCatequistaTelefono"></span></p>
                <p><strong>Especialidad:</strong> <span id="verCatequistaEspecialidad"></span></p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalVerCurso" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Detalle del Curso</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Nombre:</strong> <span id="verCursoNombre"></span></p>
                <p><strong>Duración:</strong> <span id="verCursoDuracion"></span></p>
                <p><strong>Catequista:</strong> <span id="verCursoCatequista"></span></p>
                <p><strong>Observaciones:</strong> <span id="verCursoObs"></span></p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalVerPeriodo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Detalle del Periodo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h5 id="verPeriodoNombre"></h5>
                <p id="verPeriodoFechas"></p>
                <p>Estado: <span id="verPeriodoEstado" class="badge"></span></p>
                <hr>
                <h6>Catequesis asociadas</h6>
                <ul id="listaCatequesis" class="list-group mb-3"></ul>
                <h6>Bautismos</h6>
                <ul id="listaBautismos" class="list-group mb-3"></ul>
                <h6>Comuniones</h6>
                <ul id="listaComuniones" class="list-group mb-3"></ul>
                <h6>Confirmaciones</h6>
                <ul id="listaConfirmaciones" class="list-group mb-3"></ul>
                <h6>Matrimonios</h6>
                <ul id="listaMatrimonios" class="list-group"></ul>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let MODALES = {};
    let feligresesGlobal = [];
    let cursosGlobal = [];

    // Variables de paginación
    let registrosCatequesisGlobal = [];
    let registrosCatequesisFiltrados = [];
    let paginaCatequesisActual = 1;
    let registrosCatequesisPorPagina = 10;
    let totalPaginasCatequesis = 1;

    let registrosCursosGlobal = [];
    let registrosCursosFiltrados = [];
    let paginaCursosActual = 1;
    let registrosCursosPorPagina = 10;
    let totalPaginasCursos = 1;

    let registrosCatequistasGlobal = [];
    let registrosCatequistasFiltrados = [];
    let paginaCatequistasActual = 1;
    let registrosCatequistasPorPagina = 10;
    let totalPaginasCatequistas = 1;

    let registrosPeriodosGlobal = [];
    let registrosPeriodosFiltrados = [];
    let paginaPeriodosActual = 1;
    let registrosPeriodosPorPagina = 10;
    let totalPaginasPeriodos = 1;

    // Variables para estipendio
    let estipendioRegistrado = false;
    let idEstipendio = null;
    let montoEstipendio = 0;
    let modalDonacionRapidaCatequesis = null;

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatCFA(valor) {
        return new Intl.NumberFormat('fr-FR').format(Math.round(valor || 0));
    }

    function resetearEstadoEstipendio() {
        estipendioRegistrado = false;
        idEstipendio = null;
        montoEstipendio = 0;
        document.getElementById('infoEstipendio').style.display = 'none';
        document.getElementById('infoEstipendioText').innerHTML = '';
        document.getElementById('btnGuardarCatequesis').disabled = true;
    }

    // ============================================
    // MAPEO DE TIPOS DE CATEQUESIS A CURSOS
    // ============================================
    function getCursoPorTipo(tipoCatequesis) {
        // Mapeo de tipos de catequesis a palabras clave para buscar en cursos
        const mapeo = {
            'Bautismal': ['bautismal', 'bautismo', 'baut'],
            'Primera comunión': ['primera comunión', 'primera comunion', 'comunión', 'comunion', 'comun'],
            'Confirmación': ['confirmación', 'confirmacion', 'confirm'],
            'Matrimonial': ['matrimonial', 'matrimonio', 'matri']
        };
        
        const palabrasClave = mapeo[tipoCatequesis] || [];
        return palabrasClave;
    }

    function validarCursoPorTipo(tipoCatequesis, nombreCurso) {
        if (!tipoCatequesis || !nombreCurso) return false;
        
        const nombreCursoLower = nombreCurso.toLowerCase();
        const palabrasClave = getCursoPorTipo(tipoCatequesis);
        
        // Verificar si el nombre del curso contiene alguna de las palabras clave
        for (const clave of palabrasClave) {
            if (nombreCursoLower.includes(clave)) {
                return true;
            }
        }
        return false;
    }

    // ============================================
    // FUNCIONES DE NAVEGACIÓN
    // ============================================
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

        if (sectionId === 'sec-catequesis') listarCatequesis();
        if (sectionId === 'sec-cursos') listarCursos();
        if (sectionId === 'sec-catequistas') listarCatequistas();
        if (sectionId === 'sec-periodos') listarPeriodos();
    }

    // ============================================
    // CATEQUESIS
    // ============================================
    document.addEventListener("DOMContentLoaded", function() {
        const limiteGuardado = localStorage.getItem('catequesis_limite');
        if (limiteGuardado) {
            document.getElementById('limiteCatequesis').value = limiteGuardado;
            registrosCatequesisPorPagina = parseInt(limiteGuardado);
        }
        
        const limiteGuardadoCursos = localStorage.getItem('cursos_limite');
        if (limiteGuardadoCursos) {
            document.getElementById('limiteCursos').value = limiteGuardadoCursos;
            registrosCursosPorPagina = parseInt(limiteGuardadoCursos);
        }
        
        const limiteGuardadoCatequistas = localStorage.getItem('catequistas_limite');
        if (limiteGuardadoCatequistas) {
            document.getElementById('limiteCatequistas').value = limiteGuardadoCatequistas;
            registrosCatequistasPorPagina = parseInt(limiteGuardadoCatequistas);
        }
        
        const limiteGuardadoPeriodos = localStorage.getItem('periodos_limite');
        if (limiteGuardadoPeriodos) {
            document.getElementById('limitePeriodos').value = limiteGuardadoPeriodos;
            registrosPeriodosPorPagina = parseInt(limiteGuardadoPeriodos);
        }
        
        cargarSelects();
        listarCatequesis();
        initModalDonacionCatequesis();
        
        // Resetear estado del estipendio al cerrar el modal
        document.getElementById('modalCatequesis').addEventListener('hidden.bs.modal', function() {
            resetearEstadoEstipendio();
        });
        
        const formCatequesis = document.getElementById('formCatequesis');
        if (formCatequesis) formCatequesis.addEventListener('submit', e => { e.preventDefault(); guardarCatequesis(); });
        
        const formCurso = document.getElementById('formCurso');
        if (formCurso) formCurso.addEventListener('submit', e => { e.preventDefault(); guardarCurso(); });
        
        const formCatequista = document.getElementById('formCatequista');
        if (formCatequista) formCatequista.addEventListener('submit', e => { e.preventDefault(); guardarCatequista(); });
        
        const formPeriodo = document.getElementById('formPeriodo');
        if (formPeriodo) formPeriodo.addEventListener('submit', e => { e.preventDefault(); guardarPeriodo(); });
        
        const tipoSelect = document.getElementById('tipo_catequesis');
        if (tipoSelect) {
            tipoSelect.addEventListener('change', function() {
                cargarFeligresesAptos();
                cargarCursosPorTipo(this.value);
            });
        }
        
        // Validar curso cuando se selecciona
        const selCurso = document.getElementById('selCurso');
        if (selCurso) {
            selCurso.addEventListener('change', function() {
                validarSeleccionCurso();
            });
        }
    });

    function validarSeleccionCurso() {
        const tipo = document.getElementById('tipo_catequesis').value;
        const cursoSelect = document.getElementById('selCurso');
        const mensajeCurso = document.getElementById('mensajeCurso');
        const btnGuardar = document.getElementById('btnGuardarCatequesis');
        
        if (!tipo || !cursoSelect.value) {
            mensajeCurso.style.display = 'none';
            // Solo habilitar si hay estipendio y tipo seleccionado
            btnGuardar.disabled = !estipendioRegistrado || !tipo;
            return;
        }
        
        // Obtener el nombre del curso seleccionado
        const selectedOption = cursoSelect.options[cursoSelect.selectedIndex];
        const nombreCurso = selectedOption ? selectedOption.text : '';
        
        // Validar que el curso coincida con el tipo de catequesis
        const esValido = validarCursoPorTipo(tipo, nombreCurso);
        
        if (esValido) {
            mensajeCurso.style.display = 'none';
            btnGuardar.disabled = !estipendioRegistrado;
        } else {
            mensajeCurso.style.display = 'block';
            mensajeCurso.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i> El curso seleccionado no corresponde al tipo de catequesis';
            btnGuardar.disabled = true;
        }
    }

    function cargarCursosPorTipo(tipo) {
        const cursoSelect = document.getElementById('selCurso');
        const mensajeCurso = document.getElementById('mensajeCurso');
        const btnGuardar = document.getElementById('btnGuardarCatequesis');
        
        if (!tipo) {
            cursoSelect.innerHTML = '<option value="">-- Seleccione un curso --</option>';
            mensajeCurso.style.display = 'none';
            btnGuardar.disabled = !estipendioRegistrado;
            return;
        }
        
        // Cargar todos los cursos disponibles
        fetch('../api/cursos/listar.php')
            .then(res => res.json())
            .then(cursos => {
                cursosGlobal = cursos;
                let options = '<option value="">-- Seleccione un curso --</option>';
                let tieneCursosValidos = false;
                
                cursos.forEach(curso => {
                    const esValido = validarCursoPorTipo(tipo, curso.nombre);
                    if (esValido) {
                        tieneCursosValidos = true;
                        options += `<option value="${curso.id_curso}" data-nombre="${escapeHtml(curso.nombre)}">${escapeHtml(curso.nombre)}</option>`;
                    }
                });
                
                cursoSelect.innerHTML = options;
                
                if (!tieneCursosValidos) {
                    mensajeCurso.style.display = 'block';
                    mensajeCurso.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i> No hay cursos disponibles para este tipo de catequesis';
                    btnGuardar.disabled = true;
                } else {
                    mensajeCurso.style.display = 'none';
                    btnGuardar.disabled = !estipendioRegistrado;
                }
            })
            .catch(err => {
                console.error('Error cargando cursos:', err);
                cursoSelect.innerHTML = '<option value="">Error al cargar cursos</option>';
            });
    }

    function cambiarLimiteCatequesis() {
        const select = document.getElementById('limiteCatequesis');
        registrosCatequesisPorPagina = parseInt(select.value);
        localStorage.setItem('catequesis_limite', registrosCatequesisPorPagina);
        paginaCatequesisActual = 1;
        actualizarPaginacionCatequesis();
        renderizarPaginaCatequesis();
    }

    function cambiarLimiteCursos() {
        const select = document.getElementById('limiteCursos');
        registrosCursosPorPagina = parseInt(select.value);
        localStorage.setItem('cursos_limite', registrosCursosPorPagina);
        paginaCursosActual = 1;
        actualizarPaginacionCursos();
        renderizarPaginaCursos();
    }

    function cambiarLimiteCatequistas() {
        const select = document.getElementById('limiteCatequistas');
        registrosCatequistasPorPagina = parseInt(select.value);
        localStorage.setItem('catequistas_limite', registrosCatequistasPorPagina);
        paginaCatequistasActual = 1;
        actualizarPaginacionCatequistas();
        renderizarPaginaCatequistas();
    }

    function cambiarLimitePeriodos() {
        const select = document.getElementById('limitePeriodos');
        registrosPeriodosPorPagina = parseInt(select.value);
        localStorage.setItem('periodos_limite', registrosPeriodosPorPagina);
        paginaPeriodosActual = 1;
        actualizarPaginacionPeriodos();
        renderizarPaginaPeriodos();
    }

    function listarCatequesis() {
        fetch('../api/catequesis/listar.php')
            .then(res => res.json())
            .then(data => {
                const tabla = document.getElementById("tablaCatequesis");
                if (!tabla) return;

                if (data.error) {
                    tabla.innerHTML = `<tr><td colspan="5" class="text-center text-danger">Error: ${data.message}</td></tr>`;
                    return;
                }

                registrosCatequesisGlobal = data;
                registrosCatequesisFiltrados = [...data];
                paginaCatequesisActual = 1;
                actualizarPaginacionCatequesis();
                renderizarPaginaCatequesis();
            })
            .catch(err => console.error("Error listarCatequesis:", err));
    }

    function actualizarPaginacionCatequesis() {
        totalPaginasCatequesis = Math.ceil(registrosCatequesisFiltrados.length / registrosCatequesisPorPagina);
        if (totalPaginasCatequesis === 0) totalPaginasCatequesis = 1;
        
        if (paginaCatequesisActual > totalPaginasCatequesis) paginaCatequesisActual = totalPaginasCatequesis;
        
        const inicio = (paginaCatequesisActual - 1) * registrosCatequesisPorPagina + 1;
        const fin = Math.min(paginaCatequesisActual * registrosCatequesisPorPagina, registrosCatequesisFiltrados.length);
        const total = registrosCatequesisFiltrados.length;
        
        document.getElementById('infoCatequesisDesde').innerText = total > 0 ? inicio : 0;
        document.getElementById('infoCatequesisHasta').innerText = fin;
        document.getElementById('infoCatequesisTotal').innerText = total;
        document.getElementById('paginaCatequesisActual').innerHTML = `Página ${paginaCatequesisActual} de ${totalPaginasCatequesis}`;
        
        document.getElementById('btnCatequesisAnterior').disabled = (paginaCatequesisActual <= 1);
        document.getElementById('btnCatequesisSiguiente').disabled = (paginaCatequesisActual >= totalPaginasCatequesis);
    }

    function renderizarPaginaCatequesis() {
        const inicio = (paginaCatequesisActual - 1) * registrosCatequesisPorPagina;
        const fin = inicio + registrosCatequesisPorPagina;
        const paginaData = registrosCatequesisFiltrados.slice(inicio, fin);
        renderTablaCatequesis(paginaData);
    }

    function paginaCatequesisAnterior() {
        if (paginaCatequesisActual > 1) {
            paginaCatequesisActual--;
            renderizarPaginaCatequesis();
            actualizarPaginacionCatequesis();
        }
    }

    function paginaCatequesisSiguiente() {
        if (paginaCatequesisActual < totalPaginasCatequesis) {
            paginaCatequesisActual++;
            renderizarPaginaCatequesis();
            actualizarPaginacionCatequesis();
        }
    }

    function renderTablaCatequesis(lista) {
        let html = '';

        if (lista.length === 0) {
            html = '<tr><td colspan="5" class="text-center">No hay registros</td></tr>';
        } else {
            lista.forEach(cat => {
                html += `
                    <tr id="fila-catequesis-${cat.id_catequesis}">
                        <td>
                            <div class="fw-bold text-dark">${escapeHtml(cat.nombre_feligres)}</div>
                            <small class="text-muted"><i class="bi bi-house-door"></i> ${cat.nombre_parroquia || 'Parroquia no asignada'}</small>
                        </td>
                        <td><span class="badge bg-light text-primary border border-primary">${escapeHtml(cat.tipo)}</span></td>
                        <td><i class="bi bi-book small"></i> ${cat.nombre_curso || 'N/A'}</td>
                        <td><span class="badge bg-secondary">${cat.anio || 'S/P'}</span></td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-primary" onclick="editarCatequesis(${cat.id_catequesis})"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-outline-danger ms-1" onclick="eliminarCatequesis(${cat.id_catequesis})"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                `;
            });
        }

        document.getElementById("tablaCatequesis").innerHTML = html;
    }

    function filtrarTablaCatequesis() {
        let texto = document.getElementById("busCatequesis").value.toLowerCase().trim();
        
        if (texto === '') {
            registrosCatequesisFiltrados = [...registrosCatequesisGlobal];
        } else {
            registrosCatequesisFiltrados = registrosCatequesisGlobal.filter(cat => {
                const feligres = (cat.nombre_feligres || '').toLowerCase();
                const tipo = (cat.tipo || '').toLowerCase();
                const curso = (cat.nombre_curso || '').toLowerCase();
                return feligres.includes(texto) || tipo.includes(texto) || curso.includes(texto);
            });
        }
        
        paginaCatequesisActual = 1;
        actualizarPaginacionCatequesis();
        renderizarPaginaCatequesis();
    }

    async function cargarFeligresesAptos() {
        const tipo = document.getElementById('tipo_catequesis').value;
        const feligresSelect = document.getElementById('selFeligresApto');
        const mensajeDiv = document.getElementById('mensajeRequisitos');
        
        // Resetear estado del estipendio al cambiar de tipo
        resetearEstadoEstipendio();
        
        if (!tipo) {
            feligresSelect.innerHTML = '<option value="">-- Primero seleccione un tipo de catequesis --</option>';
            feligresSelect.disabled = true;
            mensajeDiv.innerHTML = '';
            return;
        }
        
        feligresSelect.innerHTML = '<option value="">Cargando feligreses aptos...</option>';
        feligresSelect.disabled = true;
        mensajeDiv.innerHTML = '<div class="spinner-border spinner-border-sm text-primary me-1" role="status"></div> Verificando requisitos...';
        
        try {
            const response = await fetch(`../api/catequesis/obtener_aptos.php?tipo=${encodeURIComponent(tipo)}`);
            const data = await response.json();
            
            if (data.success && data.data && data.data.length > 0) {
                let options = '<option value="">-- Seleccione un feligrés --</option>';
                data.data.forEach(f => {
                    options += `<option value="${f.id_feligres}">${escapeHtml(f.nombre_completo)}</option>`;
                });
                feligresSelect.innerHTML = options;
                feligresSelect.disabled = false;
                mensajeDiv.innerHTML = `<span class="text-success"><i class="bi bi-check-circle"></i> ${data.mensaje || `${data.data.length} feligreses disponibles`}</span>`;
            } else {
                feligresSelect.innerHTML = '<option value="">No hay feligreses aptos</option>';
                feligresSelect.disabled = true;
                let mensajeError = data.mensaje || 'No hay feligreses que cumplan los requisitos para esta catequesis';
                mensajeDiv.innerHTML = `<span class="text-warning"><i class="bi bi-exclamation-triangle"></i> ${mensajeError}</span>`;
            }
        } catch (error) {
            console.error('Error cargando feligreses aptos:', error);
            feligresSelect.innerHTML = '<option value="">Error al cargar datos</option>';
            feligresSelect.disabled = true;
            mensajeDiv.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle"></i> Error al verificar requisitos</span>';
        }
    }

    // ============================================
    // CURSOS
    // ============================================
    function listarCursos() {
        fetch('../api/cursos/listar.php')
            .then(res => res.json())
            .then(data => {
                registrosCursosGlobal = data;
                registrosCursosFiltrados = [...data];
                paginaCursosActual = 1;
                actualizarPaginacionCursos();
                renderizarPaginaCursos();
            });
    }

    function actualizarPaginacionCursos() {
        totalPaginasCursos = Math.ceil(registrosCursosFiltrados.length / registrosCursosPorPagina);
        if (totalPaginasCursos === 0) totalPaginasCursos = 1;
        
        if (paginaCursosActual > totalPaginasCursos) paginaCursosActual = totalPaginasCursos;
        
        const inicio = (paginaCursosActual - 1) * registrosCursosPorPagina + 1;
        const fin = Math.min(paginaCursosActual * registrosCursosPorPagina, registrosCursosFiltrados.length);
        const total = registrosCursosFiltrados.length;
        
        document.getElementById('infoCursosDesde').innerText = total > 0 ? inicio : 0;
        document.getElementById('infoCursosHasta').innerText = fin;
        document.getElementById('infoCursosTotal').innerText = total;
        document.getElementById('paginaCursosActual').innerHTML = `Página ${paginaCursosActual} de ${totalPaginasCursos}`;
        
        document.getElementById('btnCursosAnterior').disabled = (paginaCursosActual <= 1);
        document.getElementById('btnCursosSiguiente').disabled = (paginaCursosActual >= totalPaginasCursos);
    }

    function renderizarPaginaCursos() {
        const inicio = (paginaCursosActual - 1) * registrosCursosPorPagina;
        const fin = inicio + registrosCursosPorPagina;
        const paginaData = registrosCursosFiltrados.slice(inicio, fin);
        renderTablaCursos(paginaData);
    }

    function paginaCursosAnterior() {
        if (paginaCursosActual > 1) {
            paginaCursosActual--;
            renderizarPaginaCursos();
            actualizarPaginacionCursos();
        }
    }

    function paginaCursosSiguiente() {
        if (paginaCursosActual < totalPaginasCursos) {
            paginaCursosActual++;
            renderizarPaginaCursos();
            actualizarPaginacionCursos();
        }
    }

    function renderTablaCursos(lista) {
        const tabla = document.getElementById("tablaCursos");
        if (!tabla) return;
        
        let html = '';
        if (lista.length === 0) {
            html = '<tr><td colspan="4" class="text-center">No hay registros</td></tr>';
        } else {
            lista.forEach(c => {
                html += `
                    <tr>
                        <td class="fw-bold">${escapeHtml(c.nombre)}</td>
                        <td>${c.duracion || '---'}</td>
                        <td>${c.nombre_catequista || '<span class="text-muted">No asignado</span>'}</td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-success" onclick="editarCurso(${c.id_curso})"><i class="bi bi-gear"></i></button>
                            <button class="btn btn-sm btn-outline-info" onclick="verCurso(${c.id_curso})"><i class="bi bi-eye"></i></button>
                        </td>
                    </tr>
                `;
            });
        }
        tabla.innerHTML = html;
    }

    function filtrarTablaCursos() {
        let texto = document.getElementById("busCursos").value.toLowerCase().trim();
        
        if (texto === '') {
            registrosCursosFiltrados = [...registrosCursosGlobal];
        } else {
            registrosCursosFiltrados = registrosCursosGlobal.filter(c => 
                (c.nombre || '').toLowerCase().includes(texto)
            );
        }
        
        paginaCursosActual = 1;
        actualizarPaginacionCursos();
        renderizarPaginaCursos();
    }

    // ============================================
    // CATEQUISTAS
    // ============================================
    function listarCatequistas() {
        fetch('../api/catequistas/listar.php')
            .then(res => res.json())
            .then(data => {
                registrosCatequistasGlobal = data;
                registrosCatequistasFiltrados = [...data];
                paginaCatequistasActual = 1;
                actualizarPaginacionCatequistas();
                renderizarPaginaCatequistas();
            });
    }

    function actualizarPaginacionCatequistas() {
        totalPaginasCatequistas = Math.ceil(registrosCatequistasFiltrados.length / registrosCatequistasPorPagina);
        if (totalPaginasCatequistas === 0) totalPaginasCatequistas = 1;
        
        if (paginaCatequistasActual > totalPaginasCatequistas) paginaCatequistasActual = totalPaginasCatequistas;
        
        const inicio = (paginaCatequistasActual - 1) * registrosCatequistasPorPagina + 1;
        const fin = Math.min(paginaCatequistasActual * registrosCatequistasPorPagina, registrosCatequistasFiltrados.length);
        const total = registrosCatequistasFiltrados.length;
        
        document.getElementById('infoCatequistasDesde').innerText = total > 0 ? inicio : 0;
        document.getElementById('infoCatequistasHasta').innerText = fin;
        document.getElementById('infoCatequistasTotal').innerText = total;
        document.getElementById('paginaCatequistasActual').innerHTML = `Página ${paginaCatequistasActual} de ${totalPaginasCatequistas}`;
        
        document.getElementById('btnCatequistasAnterior').disabled = (paginaCatequistasActual <= 1);
        document.getElementById('btnCatequistasSiguiente').disabled = (paginaCatequistasActual >= totalPaginasCatequistas);
    }

    function renderizarPaginaCatequistas() {
        const inicio = (paginaCatequistasActual - 1) * registrosCatequistasPorPagina;
        const fin = inicio + registrosCatequistasPorPagina;
        const paginaData = registrosCatequistasFiltrados.slice(inicio, fin);
        renderTablaCatequistas(paginaData);
    }

    function paginaCatequistasAnterior() {
        if (paginaCatequistasActual > 1) {
            paginaCatequistasActual--;
            renderizarPaginaCatequistas();
            actualizarPaginacionCatequistas();
        }
    }

    function paginaCatequistasSiguiente() {
        if (paginaCatequistasActual < totalPaginasCatequistas) {
            paginaCatequistasActual++;
            renderizarPaginaCatequistas();
            actualizarPaginacionCatequistas();
        }
    }

    function renderTablaCatequistas(lista) {
        const tabla = document.getElementById("tablaCatequistas");
        if (!tabla) return;
        
        let html = '';
        if (lista.length === 0) {
            html = '<tr><td colspan="4" class="text-center">No hay registros</td></tr>';
        } else {
            lista.forEach(c => {
                html += `
                    <tr id="fila-catequista-${c.id_catequista}">
                        <td class="fw-bold">${escapeHtml(c.nombre)}</td>
                        <td>${c.telefono || '---'}</td>
                        <td><span class="badge bg-dark">${escapeHtml(c.especialidad || 'General')}</span></td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-dark" onclick="editarCatequista(${c.id_catequista})"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-outline-info" onclick="verCatequista(${c.id_catequista})"><i class="bi bi-eye"></i></button>
                            <button class="btn btn-sm btn-outline-danger ms-1" onclick="eliminarCatequista(${c.id_catequista})"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                `;
            });
        }
        tabla.innerHTML = html;
    }

    function filtrarTablaCatequistas() {
        let texto = document.getElementById("busCatequistas").value.toLowerCase().trim();
        
        if (texto === '') {
            registrosCatequistasFiltrados = [...registrosCatequistasGlobal];
        } else {
            registrosCatequistasFiltrados = registrosCatequistasGlobal.filter(c => 
                (c.nombre || '').toLowerCase().includes(texto) ||
                (c.especialidad || '').toLowerCase().includes(texto)
            );
        }
        
        paginaCatequistasActual = 1;
        actualizarPaginacionCatequistas();
        renderizarPaginaCatequistas();
    }

    // ============================================
    // PERIODOS
    // ============================================
    function listarPeriodos() {
        fetch('../api/periodos/listar.php')
            .then(res => res.json())
            .then(data => {
                registrosPeriodosGlobal = data;
                registrosPeriodosFiltrados = [...data];
                paginaPeriodosActual = 1;
                actualizarPaginacionPeriodos();
                renderizarPaginaPeriodos();
            });
    }

    function actualizarPaginacionPeriodos() {
        totalPaginasPeriodos = Math.ceil(registrosPeriodosFiltrados.length / registrosPeriodosPorPagina);
        if (totalPaginasPeriodos === 0) totalPaginasPeriodos = 1;
        
        if (paginaPeriodosActual > totalPaginasPeriodos) paginaPeriodosActual = totalPaginasPeriodos;
        
        const inicio = (paginaPeriodosActual - 1) * registrosPeriodosPorPagina + 1;
        const fin = Math.min(paginaPeriodosActual * registrosPeriodosPorPagina, registrosPeriodosFiltrados.length);
        const total = registrosPeriodosFiltrados.length;
        
        document.getElementById('infoPeriodosDesde').innerText = total > 0 ? inicio : 0;
        document.getElementById('infoPeriodosHasta').innerText = fin;
        document.getElementById('infoPeriodosTotal').innerText = total;
        document.getElementById('paginaPeriodosActual').innerHTML = `Página ${paginaPeriodosActual} de ${totalPaginasPeriodos}`;
        
        document.getElementById('btnPeriodosAnterior').disabled = (paginaPeriodosActual <= 1);
        document.getElementById('btnPeriodosSiguiente').disabled = (paginaPeriodosActual >= totalPaginasPeriodos);
    }

    function renderizarPaginaPeriodos() {
        const inicio = (paginaPeriodosActual - 1) * registrosPeriodosPorPagina;
        const fin = inicio + registrosPeriodosPorPagina;
        const paginaData = registrosPeriodosFiltrados.slice(inicio, fin);
        renderTablaPeriodos(paginaData);
    }

    function paginaPeriodosAnterior() {
        if (paginaPeriodosActual > 1) {
            paginaPeriodosActual--;
            renderizarPaginaPeriodos();
            actualizarPaginacionPeriodos();
        }
    }

    function paginaPeriodosSiguiente() {
        if (paginaPeriodosActual < totalPaginasPeriodos) {
            paginaPeriodosActual++;
            renderizarPaginaPeriodos();
            actualizarPaginacionPeriodos();
        }
    }

    function renderTablaPeriodos(lista) {
        const tabla = document.getElementById("tablaPeriodos");
        if (!tabla) return;
        
        let html = '';
        if (lista.length === 0) {
            html = '<tr><td colspan="4" class="text-center">No hay registros</td></tr>';
        } else {
            lista.forEach(p => {
                html += `
                    <tr id="fila-periodo-${p.id_periodo}">
                        <td>${p.fecha_inicio || '---'}</td>
                        <td>${p.fecha_fin || '---'}</td>
                        <td>
                            <button class="btn btn-sm ${p.estado === 'activo' ? 'btn-warning' : 'btn-success'}" onclick="togglePeriodo(${p.id_periodo}, '${p.estado}')">
                                <i class="bi ${p.estado === 'activo' ? 'bi-pause-circle' : 'bi-play-circle'}"></i> 
                                ${p.estado === 'activo' ? 'Desactivar' : 'Activar'}
                            </button>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-info" onclick="editarPeriodo(${p.id_periodo})"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-outline-success" onclick="verPeriodo(${p.id_periodo})"><i class="bi bi-eye"></i></button>
                        </td>
                    </tr>
                `;
            });
        }
        tabla.innerHTML = html;
    }

    function filtrarTablaPeriodos() {
        let texto = document.getElementById("busPeriodos").value.toLowerCase().trim();
        
        if (texto === '') {
            registrosPeriodosFiltrados = [...registrosPeriodosGlobal];
        } else {
            registrosPeriodosFiltrados = registrosPeriodosGlobal.filter(p => 
                (p.fecha_inicio || '').toString().includes(texto) ||
                (p.fecha_fin || '').toString().includes(texto)
            );
        }
        
        paginaPeriodosActual = 1;
        actualizarPaginacionPeriodos();
        renderizarPaginaPeriodos();
    }

    // ============================================
    // FUNCIONES CRUD
    // ============================================
    async function cargarSelects() {
        try {
            const [resC, resCat, resP, resPa] = await Promise.all([
                fetch('../api/cursos/listar.php').then(r => r.json()),
                fetch('../api/catequistas/listar.php').then(r => r.json()),
                fetch('../api/periodos/listar.php').then(r => r.json()),
                fetch('../api/parroquias/listar.php').then(r => r.json())
            ]);

            const selCurso = document.getElementById('selCurso');
            if (selCurso) {
                selCurso.innerHTML = '<option value="">-- Seleccione un curso --</option>';
                if (!resC.error && resC.length > 0) {
                    cursosGlobal = resC;
                    // No cargar todos los cursos aquí, se cargarán según el tipo seleccionado
                }
            }

            const selPeriodo = document.getElementById('selPeriodo');
            if (selPeriodo) {
                selPeriodo.innerHTML = '<option value="">-- Seleccione un periodo --</option>';
                if (!resP.error && resP.length > 0) {
                    resP.forEach(p => {
                        const anio = p.anio || `${p.fecha_inicio}-${p.fecha_fin}`;
                        selPeriodo.innerHTML += `<option value="${p.id_periodo}">${anio}</option>`;
                    });
                }
            }

            const selParroquia = document.getElementById('selParroquia');
            if (selParroquia) {
                selParroquia.innerHTML = '<option value="">-- Seleccione una parroquia --</option>';
                if (!resPa.error && resPa.length > 0) {
                    resPa.forEach(p => {
                        selParroquia.innerHTML += `<option value="${p.id_parroquia}">${escapeHtml(p.nombre)}</option>`;
                    });
                }
            }

            const selCatequista = document.getElementById('selCatequista');
            if (selCatequista) {
                selCatequista.innerHTML = '<option value="">-- Seleccionar Mentor --</option>';
                if (!resCat.error && resCat.length > 0) {
                    resCat.forEach(c => {
                        selCatequista.innerHTML += `<option value="${c.id_catequista}">${escapeHtml(c.nombre)}</option>`;
                    });
                }
            }

        } catch (e) { 
            console.warn("Error cargando catálogos:", e); 
        }
    }

    function nuevaCatequesis() { 
        const form = document.getElementById('formCatequesis');
        if (form) form.reset();
        document.getElementById('id_catequesis').value = '';
        resetearEstadoEstipendio();
        
        // Mostrar la sección de estipendio
        document.getElementById('seccionEstipendio').style.display = 'block';
        document.getElementById('btnRegistrarEstipendio').style.display = 'inline-block';
        
        const feligresSelect = document.getElementById('selFeligresApto');
        if (feligresSelect) {
            feligresSelect.innerHTML = '<option value="">-- Primero seleccione un tipo de catequesis --</option>';
            feligresSelect.disabled = true;
        }
        
        const tipoSelect = document.getElementById('tipo_catequesis');
        if (tipoSelect) tipoSelect.disabled = false;
        
        const mensajeDiv = document.getElementById('mensajeRequisitos');
        if (mensajeDiv) mensajeDiv.innerHTML = '';
        
        const mensajeCurso = document.getElementById('mensajeCurso');
        if (mensajeCurso) mensajeCurso.style.display = 'none';
        
        const selCurso = document.getElementById('selCurso');
        if (selCurso) {
            selCurso.innerHTML = '<option value="">-- Seleccione un curso --</option>';
        }
        
        // Ocultar mensaje de error de curso
        const mensajeCursoError = document.getElementById('mensajeCurso');
        if (mensajeCursoError) mensajeCursoError.style.display = 'none';
        
        MODALES.catequesis?.show(); 
    }
    
    function nuevoCurso() { 
        document.getElementById('formCurso')?.reset(); 
        document.getElementById('id_curso').value = '';
        MODALES.curso?.show(); 
    }
    
    function nuevoCatequista() { 
        document.getElementById('formCatequista')?.reset(); 
        document.getElementById('id_catequista').value = '';
        MODALES.catequista?.show(); 
    }
    
    function nuevoPeriodo() { 
        document.getElementById('formPeriodo')?.reset(); 
        document.getElementById('id_periodo').value = '';
        MODALES.periodo?.show(); 
    }

    function guardarCatequesis() {
        const form = document.getElementById('formCatequesis');
        if (!form) return;
        
        const esEdicion = document.getElementById('id_catequesis').value !== '';
        const tipo = document.getElementById('tipo_catequesis')?.value || '';
        const idCurso = document.getElementById('selCurso')?.value || '';
        const nombreCurso = document.getElementById('selCurso')?.options[document.getElementById('selCurso').selectedIndex]?.text || '';
        
        // Validar que el curso coincida con el tipo de catequesis
        if (idCurso && tipo) {
            const esValido = validarCursoPorTipo(tipo, nombreCurso);
            if (!esValido) {
                Swal.fire({
                    title: 'Curso incorrecto',
                    text: 'El curso seleccionado no corresponde al tipo de catequesis. Por favor, seleccione un curso válido.',
                    icon: 'warning',
                    confirmButtonText: 'Aceptar'
                });
                return;
            }
        }
        
        // Para nuevos registros, verificar que se haya registrado el estipendio
        if (!esEdicion && !estipendioRegistrado) {
            Swal.fire({
                title: 'Estipendio requerido',
                text: 'Debe registrar el estipendio para completar la inscripción',
                icon: 'warning',
                confirmButtonText: 'Registrar estipendio',
                showCancelButton: true,
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    abrirDonacionDesdeCatequesis();
                }
            });
            return;
        }

        const formData = new FormData(form);
        const id = document.getElementById('id_catequesis').value;
        const selectFeligres = document.getElementById('selFeligresApto');
        const nombreFeligres = selectFeligres?.options[selectFeligres.selectedIndex]?.text || '';
        
        // Solo agregar datos de estipendio si es nuevo registro
        if (!esEdicion && estipendioRegistrado) {
            formData.append('id_estipendio', idEstipendio);
            formData.append('monto_estipendio', montoEstipendio);
        }

        const btnGuardar = document.getElementById('btnGuardarCatequesis');
        const textoOriginal = btnGuardar.innerHTML;
        btnGuardar.disabled = true;
        btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Guardando...';

        fetch('../api/catequesis/guardar.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = textoOriginal;
            
            if (data.success) {
                if (esEdicion) {
                    registrarActividad(`EDITÓ INSCRIPCIÓN: ${nombreFeligres} - ${tipo}`, 'CATEQUESIS', `ID: ${id}`);
                } else {
                    registrarActividad(`CREÓ INSCRIPCIÓN: ${nombreFeligres} - ${tipo}`, 'CATEQUESIS');
                }
                
                MODALES.catequesis?.hide();
                listarCatequesis();
                Swal.fire('Éxito', data.message, 'success');
                resetearEstadoEstipendio();
            } else {
                Swal.fire('Error', data.message || "No se pudo guardar", 'error');
            }
        })
        .catch(err => {
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = textoOriginal;
            console.error("Error guardarCatequesis:", err);
            Swal.fire('Error', 'Error de conexión', 'error');
        });
    }

    function eliminarCatequesis(id) {
        const fila = document.getElementById(`fila-catequesis-${id}`);
        let nombreFeligres = '';
        if (fila) {
            const nombreDiv = fila.querySelector('td:first-child .fw-bold');
            if (nombreDiv) nombreFeligres = nombreDiv.innerText;
        }
        
        Swal.fire({
            title: '¿Eliminar registro?',
            text: "Se borrará el registro",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (!result.isConfirmed) return;

            fetch('../api/catequesis/eliminar.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `id=${id}`
            })
            .then(res => res.json())
            .then(resp => {
                if (resp.success) {
                    registrarActividad(`ELIMINÓ INSCRIPCIÓN: ${nombreFeligres || 'ID:' + id}`, 'CATEQUESIS');
                    Swal.fire('Eliminado', resp.message, 'success');
                    listarCatequesis();
                } else {
                    Swal.fire('Error', resp.error, 'error');
                }
            });
        });
    }

    function editarCatequesis(id) {
        fetch(`../api/catequesis/ver.php?id=${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    alert("Error al obtener los datos: " + data.error);
                    return;
                }

                const form = document.getElementById('formCatequesis');
                if (!form) return;

                form.querySelector('#id_catequesis').value = data.data.id_catequesis || '';
                form.querySelector('[name="tipo"]').value = data.data.tipo || '';
                form.querySelector('[name="id_periodo"]').value = data.data.id_periodo || '';
                form.querySelector('[name="id_parroquia"]').value = data.data.id_parroquia || '';
                
                const feligresSelect = document.getElementById('selFeligresApto');
                if (feligresSelect && data.data.id_feligres) {
                    feligresSelect.innerHTML = `<option value="${data.data.id_feligres}" selected>${escapeHtml(data.data.nombre_feligres || 'Cargando...')}</option>`;
                    feligresSelect.disabled = true;
                }
                
                const tipoSelect = document.getElementById('tipo_catequesis');
                if (tipoSelect) tipoSelect.disabled = true;
                
                // Cargar cursos disponibles y seleccionar el actual
                const tipo = data.data.tipo || '';
                if (tipo) {
                    cargarCursosPorTipo(tipo);
                    // Seleccionar el curso actual
                    const selCurso = document.getElementById('selCurso');
                    if (selCurso && data.data.id_curso) {
                        // Esperar a que se carguen los cursos
                        setTimeout(() => {
                            selCurso.value = data.data.id_curso;
                            validarSeleccionCurso();
                        }, 300);
                    }
                }
                
                // Ocultar la sección de estipendio en modo edición
                document.getElementById('seccionEstipendio').style.display = 'none';
                document.getElementById('btnRegistrarEstipendio').style.display = 'none';
                document.getElementById('infoEstipendio').style.display = 'none';
                document.getElementById('btnGuardarCatequesis').disabled = false;
                
                MODALES.catequesis?.show();
            })
            .catch(err => console.error("Error editarCatequesis:", err));
    }

    function eliminarCatequista(id) {
        const fila = document.getElementById(`fila-catequista-${id}`);
        let nombre = '';
        if (fila) {
            nombre = fila.querySelector('td:first-child')?.innerText || '';
        }
        
        Swal.fire({
            title: '¿Eliminar registro?',
            text: "Se borrará el registro de catequista",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (!result.isConfirmed) return;

            fetch('../api/catequistas/eliminar.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `id=${id}`
            })
            .then(res => res.json())
            .then(resp => {
                if (resp.success) {
                    registrarActividad(`ELIMINÓ CATEQUISTA: ${nombre || 'ID:' + id}`, 'CATEQUISTAS');
                    Swal.fire('Eliminado', resp.message, 'success');
                    listarCatequistas();
                } else {
                    Swal.fire('Error', resp.error, 'error');
                }
            });
        });
    }

    function guardarCatequista() {
        const form = document.getElementById('formCatequista');
        if (!form) return;

        const formData = new FormData(form);
        const id = document.getElementById('id_catequista')?.value;
        const nombre = document.querySelector('#formCatequista input[name="nombre"]')?.value || '';
        const esEdicion = id !== '';

        fetch('../api/catequistas/guardar.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (esEdicion) {
                    registrarActividad(`EDITÓ CATEQUISTA: ${nombre}`, 'CATEQUISTAS', `ID: ${id}`);
                } else {
                    registrarActividad(`CREÓ CATEQUISTA: ${nombre}`, 'CATEQUISTAS');
                }
                
                MODALES.catequista?.hide();
                listarCatequistas();
                Swal.fire('Éxito', data.message, 'success');
            } else {
                Swal.fire('Error', data.error || "No se pudo guardar", 'error');
            }
        })
        .catch(err => {
            console.error("Error guardarCatequista:", err);
            Swal.fire('Error', 'Error de conexión', 'error');
        });
    }

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

                form.querySelector('[name="id_catequista"]').value = data.id_catequista || '';
                form.querySelector('[name="nombre"]').value = data.nombre || '';
                form.querySelector('[name="telefono"]').value = data.telefono || '';
                form.querySelector('[name="especialidad"]').value = data.especialidad || '';

                MODALES.catequista?.show();
            })
            .catch(err => console.error("Error editarCatequista:", err));
    }

    function verCatequista(id) {
        fetch(`../api/catequistas/ver.php?id=${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    alert("Error: " + data.message);
                    return;
                }
                document.getElementById("verCatequistaNombre").innerText = data.nombre || '';
                document.getElementById("verCatequistaTelefono").innerText = data.telefono || '---';
                document.getElementById("verCatequistaEspecialidad").innerText = data.especialidad || 'General';
                
                if (MODALES.verCatequista) {
                    MODALES.verCatequista.show();
                } else {
                    MODALES.verCatequista = new bootstrap.Modal(document.getElementById('modalVerCatequista'));
                    MODALES.verCatequista.show();
                }
            })
            .catch(err => console.error("Error verCatequista:", err));
    }

    function guardarCurso() {
        const form = document.getElementById('formCurso');
        if (!form) return;

        const formData = new FormData(form);
        const id = document.getElementById('id_curso')?.value;
        const nombre = document.querySelector('#formCurso input[name="nombre"]')?.value || '';
        const esEdicion = id !== '';

        fetch('../api/cursos/guardar.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (esEdicion) {
                    registrarActividad(`EDITÓ CURSO: ${nombre}`, 'CURSOS', `ID: ${id}`);
                } else {
                    registrarActividad(`CREÓ CURSO: ${nombre}`, 'CURSOS');
                }
                
                MODALES.curso?.hide();
                listarCursos();
                Swal.fire('Éxito', data.message, 'success');
            } else {
                Swal.fire('Error', data.message || "No se pudo guardar", 'error');
            }
        })
        .catch(err => {
            console.error("Error guardarCurso:", err);
            Swal.fire('Error', 'Error de conexión', 'error');
        });
    }

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

                form.querySelector('[name="id_curso"]').value = data.id_curso || '';
                form.querySelector('[name="nombre"]').value = data.nombre || '';
                form.querySelector('[name="duracion"]').value = data.duracion || '';
                form.querySelector('[name="id_catequista"]').value = data.id_catequista || '';

                MODALES.curso?.show();
            })
            .catch(err => console.error("Error editarCurso:", err));
    }

    function verCurso(id) {
        fetch(`../api/cursos/ver.php?id=${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    alert("Error: " + data.message);
                    return;
                }
                document.getElementById("verCursoNombre").innerText = data.nombre || '';
                document.getElementById("verCursoDuracion").innerText = data.duracion || '---';
                document.getElementById("verCursoCatequista").innerText = data.nombre_catequista || 'No asignado';
                document.getElementById("verCursoObs").innerText = data.observaciones || 'Sin observaciones';
                
                if (MODALES.verCurso) {
                    MODALES.verCurso.show();
                } else {
                    MODALES.verCurso = new bootstrap.Modal(document.getElementById('modalVerCurso'));
                    MODALES.verCurso.show();
                }
            })
            .catch(err => console.error("Error verCurso:", err));
    }

    function guardarPeriodo() {
        const form = document.getElementById('formPeriodo');
        if (!form) return;

        const formData = new FormData(form);
        const id = document.getElementById('id_periodo')?.value;
        const anioInicio = document.getElementById('fecha_inicio')?.value;
        const anioFin = document.getElementById('fecha_fin')?.value;
        const esEdicion = id !== '';

        fetch('../api/periodos/guardar.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (esEdicion) {
                    registrarActividad(`EDITÓ PERIODO: ${anioInicio}-${anioFin}`, 'PERIODOS', `ID: ${id}`);
                } else {
                    registrarActividad(`CREÓ PERIODO: ${anioInicio}-${anioFin}`, 'PERIODOS');
                }
                
                MODALES.periodo?.hide();
                listarPeriodos();
                Swal.fire('Éxito', data.message, 'success');
            } else {
                Swal.fire('Error', data.message || "No se pudo guardar", 'error');
            }
        })
        .catch(err => {
            console.error("Error guardarPeriodo:", err);
            Swal.fire('Error', 'Error de conexión', 'error');
        });
    }

    function editarPeriodo(id) {
        fetch(`../api/periodos/ver.php?id=${id}`)
            .then(res => res.json())
            .then(data => {
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

    function togglePeriodo(id, estadoActual) {
        const accion = estadoActual === 'activo' ? 'desactivar' : 'activar';
        const accionTexto = estadoActual === 'activo' ? 'DESACTIVÓ' : 'ACTIVÓ';
        
        Swal.fire({
            title: `¿${accion.charAt(0).toUpperCase() + accion.slice(1)} periodo?`,
            text: `El periodo quedará ${estadoActual === 'activo' ? 'inactivo' : 'activo'}`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: estadoActual === 'activo' ? '#dc3545' : '#28a745',
            confirmButtonText: `Sí, ${accion}`,
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (!result.isConfirmed) return;

            fetch(`../api/periodos/toggle.php?id=${id}`, { method: 'POST' })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const fila = document.getElementById(`fila-periodo-${id}`);
                        let anios = '';
                        if (fila) {
                            anios = fila.querySelector('td:first-child')?.innerText || '';
                            if (fila.querySelector('td:nth-child(2)')) {
                                anios += '-' + fila.querySelector('td:nth-child(2)')?.innerText;
                            }
                        }
                        registrarActividad(`${accionTexto} PERIODO: ${anios || 'ID:' + id}`, 'PERIODOS');
                        listarPeriodos();
                        Swal.fire('Éxito', data.message, 'success');
                    } else {
                        Swal.fire('Error', data.message || "No se pudo cambiar el estado", 'error');
                    }
                })
                .catch(err => console.error("Error togglePeriodo:", err));
        });
    }

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

                document.getElementById("verPeriodoNombre").innerText = p.nombre || `Periodo ${p.fecha_inicio}-${p.fecha_fin}`;
                document.getElementById("verPeriodoFechas").innerText = `Desde: ${p.fecha_inicio} Hasta: ${p.fecha_fin}`;
                const estadoEl = document.getElementById("verPeriodoEstado");
                estadoEl.innerText = p.estado;
                estadoEl.className = 'badge ' + (p.estado === 'activo' ? 'bg-success' : 'bg-secondary');

                const renderList = (idLista, items, template) => {
                    const ul = document.getElementById(idLista);
                    if (!ul) return;
                    if (items.length === 0) {
                        ul.innerHTML = '<li class="list-group-item text-muted">No hay registros</li>';
                    } else {
                        ul.innerHTML = items.map(template).join('');
                    }
                };

                renderList("listaCatequesis", eventos.catequesis || [], c => `<li class="list-group-item"><strong>${escapeHtml(c.feligres_nombre)}</strong> | ${c.tipo} | Curso: ${c.curso_nombre || 'Sin curso'}</li>`);
                renderList("listaBautismos", eventos.bautismos || [], b => `<li class="list-group-item"><strong>${escapeHtml(b.feligres_nombre)}</strong> | Registro: ${b.registro} | Fecha: ${b.fecha}</li>`);
                renderList("listaComuniones", eventos.comuniones || [], co => `<li class="list-group-item"><strong>${escapeHtml(co.feligres_nombre)}</strong> | Registro: ${co.registro} | Fecha: ${co.fecha}</li>`);
                renderList("listaConfirmaciones", eventos.confirmaciones || [], cf => `<li class="list-group-item"><strong>${escapeHtml(cf.feligres_nombre)}</strong> | Registro: ${cf.registro} | Fecha: ${cf.fecha}</li>`);
                renderList("listaMatrimonios", eventos.matrimonios || [], m => `<li class="list-group-item"><strong>${escapeHtml(m.esposos)}</strong> | Registro: ${m.registro} | Fecha: ${m.fecha}</li>`);

                if (MODALES.verPeriodo) {
                    MODALES.verPeriodo.show();
                } else {
                    MODALES.verPeriodo = new bootstrap.Modal(document.getElementById('modalVerPeriodo'));
                    MODALES.verPeriodo.show();
                }
            }).catch(err => console.error("Error al cargar periodo:", err));
    }

    // ============================================
    // FUNCIONES PARA ESTIPENDIO
    // ============================================
    function initModalDonacionCatequesis() {
        const modalElement = document.getElementById('modalDonacionRapidaCatequesis');
        if (modalElement) {
            modalDonacionRapidaCatequesis = new bootstrap.Modal(modalElement);
        }
    }

    function calcularCambioDonacion() {
        const cant = parseInt(document.getElementById('cantidad_donacion').value) || 0;
        const rec = parseInt(document.getElementById('recibido_donacion').value) || 0;
        const cambio = rec - cant;
        const display = document.getElementById('vueltoTextDonacion');
        const cambioInput = document.getElementById('cambio_donacion');

        display.innerText = `${formatCFA(Math.max(0, cambio))} FCFA`;
        display.className = cambio < 0 ? "h4 mb-0 fw-bold text-danger" : "h4 mb-0 fw-bold text-success";
        cambioInput.value = Math.max(0, cambio);
    }

    function abrirDonacionDesdeCatequesis() {
        const selectFeligres = document.getElementById('selFeligresApto');
        const selectedOption = selectFeligres.options[selectFeligres.selectedIndex];
        const idFeligres = selectFeligres.value;
        const nombreFeligres = selectedOption?.text || '';
        const tipoCatequesis = document.getElementById('tipo_catequesis')?.value || '';
        
        if (!idFeligres) {
            Swal.fire({
                title: 'Seleccione un feligrés',
                text: 'Por favor seleccione primero el feligrés para registrar el estipendio',
                icon: 'warning',
                confirmButtonText: 'Aceptar'
            });
            return;
        }
        
        if (!tipoCatequesis) {
            Swal.fire({
                title: 'Seleccione un tipo de catequesis',
                text: 'Por favor seleccione primero el tipo de catequesis',
                icon: 'warning',
                confirmButtonText: 'Aceptar'
            });
            return;
        }
        
        document.getElementById('id_feligres_donacion_catequesis').value = idFeligres;
        document.getElementById('nombre_feligres_donacion_catequesis').value = nombreFeligres;
        document.getElementById('concepto_donacion').value = `Estipendio - Catequesis ${tipoCatequesis}`;
        document.getElementById('cantidad_donacion').value = '';
        document.getElementById('recibido_donacion').value = '';
        document.getElementById('vueltoTextDonacion').innerText = '0 FCFA';
        document.getElementById('cambio_donacion').value = '';
        document.getElementById('confirmarEstipendio').checked = false;
        
        if (!modalDonacionRapidaCatequesis) {
            initModalDonacionCatequesis();
        }
        modalDonacionRapidaCatequesis.show();
    }

    if (document.getElementById('formDonacionRapidaCatequesis')) {
        document.getElementById('formDonacionRapidaCatequesis').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const concepto = document.getElementById('concepto_donacion').value;
            const cantidad = document.getElementById('cantidad_donacion').value;
            const nombreFeligres = document.getElementById('nombre_feligres_donacion_catequesis').value;
            
            if (!cantidad || parseFloat(cantidad) <= 0) {
                Swal.fire({
                    title: 'Monto inválido',
                    text: 'Por favor ingrese un monto válido para el estipendio',
                    icon: 'warning',
                    confirmButtonText: 'Aceptar'
                });
                return;
            }
            
            Swal.fire({ 
                title: 'Guardando estipendio...', 
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
                    registrarActividad(`CREÓ ESTIPENDIO: ${concepto} - ${cantidad} FCFA`, 'PAGOS', `Feligrés: ${nombreFeligres}`);
                    
                    estipendioRegistrado = true;
                    idEstipendio = data.id_ofrenda || data.id;
                    montoEstipendio = parseFloat(cantidad) || 0;
                    
                    document.getElementById('infoEstipendio').style.display = 'block';
                    document.getElementById('infoEstipendioText').innerHTML = 
                        `Estipendio registrado: <strong>${formatCFA(montoEstipendio)} FCFA</strong> - ${concepto}`;
                    document.getElementById('btnGuardarCatequesis').disabled = false;
                    
                    Swal.fire({ 
                        title: '¡Estipendio Registrado!', 
                        text: 'El estipendio se ha guardado correctamente. Ahora puede guardar la inscripción.', 
                        icon: 'success', 
                        confirmButtonText: 'Aceptar' 
                    });
                    
                    if (modalDonacionRapidaCatequesis) {
                        modalDonacionRapidaCatequesis.hide();
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

    // INICIALIZACIÓN
    document.addEventListener('DOMContentLoaded', () => {
        const ids = {
            catequesis: 'modalCatequesis',
            curso: 'modalCurso',
            catequista: 'modalCatequista',
            periodo: 'modalPeriodo'
        };

        Object.keys(ids).forEach(key => {
            const el = document.getElementById(ids[key]);
            if (el) {
                MODALES[key] = new bootstrap.Modal(el);
            }
        });
        
        const modalVerCatequista = document.getElementById('modalVerCatequista');
        if (modalVerCatequista) {
            MODALES.verCatequista = new bootstrap.Modal(modalVerCatequista);
        }
        
        const modalVerCurso = document.getElementById('modalVerCurso');
        if (modalVerCurso) {
            MODALES.verCurso = new bootstrap.Modal(modalVerCurso);
        }
        
        const modalVerPeriodo = document.getElementById('modalVerPeriodo');
        if (modalVerPeriodo) {
            MODALES.verPeriodo = new bootstrap.Modal(modalVerPeriodo);
        }
    });
</script>

<?php include 'footer.php'; ?>