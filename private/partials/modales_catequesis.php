<!-- modales_catequesis.php - Versión simplificada sin botón de donación -->
<!-- Modal Catequesis (SIN botón de donación) -->
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
                            <select name="id_curso" id="selCurso" class="form-select">
                                <option value="">-- Seleccione un curso --</option>
                            </select>
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
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <div class="d-flex justify-content-end w-100">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Guardar Inscripción
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de verificación de pago (se muestra si no hay pago registrado) -->
<div class="modal fade" id="modalVerificacionPago" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle me-2"></i>Verificación de Estipendio
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <i class="bi bi-cash-stack display-1 text-warning"></i>
                </div>
                <p class="text-center">
                    <strong id="verificacionFeligres"></strong>
                </p>
                <p class="text-center text-muted">
                    No se ha registrado un estipendio para esta catequesis.
                </p>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Para completar la inscripción, debe registrar el estipendio correspondiente.
                </div>
                <div class="d-flex justify-content-center gap-2 mt-3">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </button>
                    <button class="btn btn-success" onclick="abrirPagoDesdeVerificacion()">
                        <i class="bi bi-cash-stack"></i> Registrar Estipendio
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de pago (se abre desde la verificación) -->
<div class="modal fade" id="modalPagoCatequesis" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form id="formPagoCatequesis">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-cash-stack me-2"></i>Registro de Estipendio
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 row g-3">
                    <input type="hidden" name="id_feligres_pago" id="id_feligres_pago">
                    <input type="hidden" name="tipo_catequesis_pago" id="tipo_catequesis_pago">
                    <input type="hidden" name="registrar_pago" value="1">
                    
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Feligrés</label>
                        <input type="text" id="nombre_feligres_pago" class="form-control" readonly>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Concepto <span class="text-danger">*</span></label>
                        <input type="text" name="concepto" id="concepto_pago" class="form-control" required readonly>
                        <small class="text-muted">Concepto generado automáticamente según el tipo de catequesis</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Monto <span class="text-danger"></span></label>
                        <div class="input-group">
                            <input type="number" name="cantidad" id="cantidad_pago" class="form-control" required oninput="calcularCambioDonacion()" min="1" step="1">
                            <span class="input-group-text">FCFA</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Monto Recibido</label>
                        <div class="input-group">
                            <input type="number" name="recibido" id="recibido_pago" class="form-control" oninput="calcularCambioDonacion()" min="0" step="1">
                            <span class="input-group-text">FCFA</span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="p-3 bg-light rounded d-flex justify-content-between align-items-center border border-success">
                            <span class="fw-bold text-muted small">CAMBIO A DEVOLVER:</span>
                            <span class="h4 mb-0 fw-bold text-success" id="vueltoTextPago">0 FCFA</span>
                            <input type="hidden" name="cambio" id="cambio_pago">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="confirmarPago" required>
                            <label class="form-check-label" for="confirmarPago">
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