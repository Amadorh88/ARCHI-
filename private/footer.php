<!-- footer.php -->
<style>
    /* Estructura para mantener el footer al final */
    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    
    .content {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    /* Centrar el contenido principal */
    .content main {
        flex: 1;
        display: flex;
        justify-content: center;
    }
    
    /* Ancho máximo para el contenido */
    .content main .container-fluid {
        max-width: 1400px;
        width: 100%;
        margin: 0 auto;
    }
    
    /* Footer al final */
    footer {
        margin-top: auto;
        background-color: #f8f9fa;
        border-top: 1px solid #dee2e6;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .content main .container-fluid {
            padding-left: 1rem;
            padding-right: 1rem;
        }
    }
</style>

<footer class="text-center py-3">
    <div class="container-fluid">
        <p class="text-muted small mb-0">
            &copy;  UNGE (AMADOR y SEBASTIÁN) <?php echo date("Y"); ?> - Todos los derechos reservados a la Archidiócesis de Malabo.
        </p>
    </div>
</footer>

<!-- Cierre de etiquetas principales -->
</div> <!-- Cierra <div class="content"> -->
</div> <!-- Cierra <div class="d-flex"> -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Toggle Sidebar para móviles
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('show');
    }

    // Hill: Sistema de confirmación profesional para evitar cierres accidentales
    function confirmarSalida() {
        Swal.fire({
            title: '¿Cerrar sesión?',
            text: "Cualquier cambio no guardado se perderá.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, salir ahora',
            cancelButtonText: 'Permanecer aquí',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'http://localhost/ARCHI+/logout.php';
            }
        });
    }
</script>

</body>
</html>