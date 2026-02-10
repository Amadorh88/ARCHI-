<?php include 'header.php'; ?>

<div class="container-fluid py-4">
    <div class="mb-4">
        <h3 class="fw-bold text-dark">Bienvenido, <?php echo htmlspecialchars($nombreUsuario); ?></h3>
        <p class="text-muted small">Estado actual del inventario sacramental de la Archidiócesis.</p>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm p-3 border-start border-primary border-4">
                <h6 class="text-muted text-uppercase small fw-bold">Bautismos</h6>
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 fw-bold" id="totalBautismos"><div class="spinner-border spinner-border-sm text-light"></div></h4>
                    <i class="bi bi-water text-primary h4 mb-0"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm p-3 border-start border-success border-4">
                <h6 class="text-muted text-uppercase small fw-bold">Comuniones</h6>
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 fw-bold" id="totalComuniones">--</h4>
                    <i class="bi bi-sun text-success h4 mb-0"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm p-3 border-start border-info border-4">
                <h6 class="text-muted text-uppercase small fw-bold">Confirmaciones</h6>
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 fw-bold" id="totalConfirmaciones">--</h4>
                    <i class="bi bi-fire text-info h4 mb-0"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm p-3 border-start border-danger border-4">
                <h6 class="text-muted text-uppercase small fw-bold">Matrimonios</h6>
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 fw-bold" id="totalMatrimonios">--</h4>
                    <i class="bi bi-heart-fill text-danger h4 mb-0"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <?php if($rolUsuario !== 'parroco'): ?>
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-lightning-charge me-2"></i>Acciones Rápidas</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <?php if($rolUsuario === 'admin' || $rolUsuario === 'archivista'): ?>
                            <div class="col-6 col-md-4">
                                <a href="sacramentos.php" class="btn btn-outline-primary w-100 py-3">
                                    <i class="bi bi-plus-circle d-block h4"></i> Registrar
                                </a>
                            </div>
                        <?php endif; ?>

                        <div class="col-6 col-md-4">
                            <a href="feligres.php" class="btn btn-outline-dark w-100 py-3">
                                <i class="bi bi-search d-block h4"></i> Buscar
                            </a>
                        </div>

                        <?php if($rolUsuario === 'admin' || $rolUsuario === 'archivista'): ?>
                        <div class="col-6 col-md-4">
                            <a href="pagos.php" class="btn btn-outline-success w-100 py-3">
                                <i class="bi bi-cash-stack d-block h4"></i> Ofrendas
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="<?php echo ($rolUsuario === 'parroco') ? 'col-12' : 'col-md-4'; ?>">
            <div class="card border-0 shadow-sm text-center p-4">
                <div class="mb-3">
                    <span class="badge bg-primary-subtle text-primary p-2 rounded-circle">
                        <i class="bi bi-shield-lock h3"></i>
                    </span>
                </div>
                <h5 class="fw-bold mb-1"><?php echo strtoupper($rolUsuario); ?></h5>
                <p class="text-muted small">Nivel de acceso institucional activo.</p>
                <div class="alert alert-light border-0 small">
                    <i class="bi bi-info-circle me-1"></i>
                    <?php 
                        if($rolUsuario === 'parroco') echo "Modo consulta: Visualización de libros sacramentales.";
                        else if($rolUsuario === 'secretario') echo "Modo edición: Actualización de datos existentes.";
                        else echo "Modo total: Gestión de infraestructura y registros.";
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    cargarEstadisticas();
});

// Función para cargar los "Activos" del sistema (Kiyosaki)
async function cargarEstadisticas() {
    try {
        const response = await fetch('../api/dashboard/estadisticas.php');
        const data = await response.json();
        
        if (data.error) throw new Error(data.error);

        // Animación simple de números (Hill: El movimiento genera entusiasmo)
        actualizarNumero('totalBautismos', data.bautismos);
        actualizarNumero('totalComuniones', data.comuniones);
        actualizarNumero('totalConfirmaciones', data.confirmaciones);
        actualizarNumero('totalMatrimonios', data.matrimonios);

    } catch (error) {
        console.error('Error cargando estadísticas:', error);
    }
}

function actualizarNumero(id, valor) {
    const el = document.getElementById(id);
    el.innerText = valor;
}
</script>

<?php include 'footer.php'; ?>