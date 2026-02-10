<div class="modal fade" id="modalCurso" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Configurar Curso</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCurso">
                <div class="modal-body row g-3">
                    <input type="hidden" name="id_curso" id="id_curso">
                    <div class="col-12">
                        <label class="form-label">Nombre del Curso</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Duración</label>
                        <input type="text" name="duracion" class="form-control" placeholder="Ej: 6 meses">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Catequista (Mentor)</label>
                        <select name="id_catequista" id="selCatequista" class="form-select"></select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Guardar Curso</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCatequista" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Ficha de Catequista</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCatequista">
                <div class="modal-body row g-3">
                    <input type="hidden" name="id_catequista" id="id_catequista">
                    <div class="col-12">
                        <label class="form-label">Nombre Completo</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Especialidad</label>
                        <input type="text" name="especialidad" class="form-control" placeholder="Ej: Confirmación">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-dark">Guardar Catequista</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCatequesis" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Inscribir en Catequesis</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCatequesis">
                <div class="modal-body row g-3">
                    <input type="hidden" name="id_catequesis" id="id_catequesis">
                   
                    <div class="col-6">
                        <label class="form-label">Feligrés (Inscrito)</label>
                        <select name="id_feligres" id="selFeligres" class="form-select" required></select>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Parroquia</label>
                        <select name="id_parroquia" id="selParroquia" class="form-select" required></select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Tipo de Sacramento</label>
                        <select name="tipo" class="form-select" required>
                            <option value="Pre-bautismal">Pre-bautismal</option>
                            <option value="Primera comunión">Primera comunión</option>
                            <option value="Confirmación">Confirmación</option>
                            <option value="Matrimonial">Matrimonial</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Periodo</label>
                        <select name="id_periodo" id="selPeriodo" class="form-select" required></select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Curso Específico</label>
                        <select name="id_curso" id="selCurso" class="form-select"></select>
                    </div>

                   <!--  <div class="col-12">
                        <label class="form-label">Nota/Nombre de Inscripción</label>
                        <input type="text" name="nombre_catequesis" class="form-control" placeholder="Ej: Catequesis Verano 2026">
                    </div> -->
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100">Guardar Inscripción</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalPeriodo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Gestión de Periodos</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formPeriodo">
                <div class="modal-body row g-3">
                    <input type="hidden" name="id_periodo" id="id_periodo">
                    
                    <div class="col-md-6">
                        <label class="form-label">Fecha de Inicio</label>
                        <input type="number" name="fecha_inicio" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Fecha de Cierre</label>
                        <input type="number" name="fecha_fin" class="form-control" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Estado del Periodo</label>
                        <select name="estado" class="form-select">
                            <option value="activo">Activo (Abierto para inscripciones)</option>
                            <option value="finalizado">Finalizado (Histórico)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info text-white w-100">Guardar Periodo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ver Periodo -->
<div class="modal fade" id="modalVerPeriodo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Resumen del Periodo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Datos del periodo -->
                <div class="mb-3">
                    <h6 id="verPeriodoNombre" class="fw-bold"></h6>
                    <p>
                        <span id="verPeriodoFechas"></span> | 
                        Estado: <span id="verPeriodoEstado" class="badge bg-success"></span>
                    </p>
                </div>

                <!-- Actividades -->
                <div class="accordion" id="accordionActividades">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingCatequesis">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCatequesis">
                                Catequesis Inscritas
                            </button>
                        </h2>
                        <div id="collapseCatequesis" class="accordion-collapse collapse show" data-bs-parent="#accordionActividades">
                            <div class="accordion-body">
                                <ul id="listaCatequesis" class="list-group list-group-flush"></ul>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingBautismos">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBautismos">
                                Bautismos Realizados
                            </button>
                        </h2>
                        <div id="collapseBautismos" class="accordion-collapse collapse" data-bs-parent="#accordionActividades">
                            <div class="accordion-body">
                                <ul id="listaBautismos" class="list-group list-group-flush"></ul>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingComuniones">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseComuniones">
                                Comuniones Realizadas
                            </button>
                        </h2>
                        <div id="collapseComuniones" class="accordion-collapse collapse" data-bs-parent="#accordionActividades">
                            <div class="accordion-body">
                                <ul id="listaComuniones" class="list-group list-group-flush"></ul>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingConfirmaciones">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseConfirmaciones">
                                Confirmaciones Realizadas
                            </button>
                        </h2>
                        <div id="collapseConfirmaciones" class="accordion-collapse collapse" data-bs-parent="#accordionActividades">
                            <div class="accordion-body">
                                <ul id="listaConfirmaciones" class="list-group list-group-flush"></ul>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingMatrimonios">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMatrimonios">
                                Matrimonios Celebrados
                            </button>
                        </h2>
                        <div id="collapseMatrimonios" class="accordion-collapse collapse" data-bs-parent="#accordionActividades">
                            <div class="accordion-body">
                                <ul id="listaMatrimonios" class="list-group list-group-flush"></ul>
                            </div>
                        </div>
                    </div>
                </div> <!-- /accordion -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success w-100" data-bs-dismiss="modal">Cerrar Resumen</button>
            </div>
        </div>
    </div>
</div>
