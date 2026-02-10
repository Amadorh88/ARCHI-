


</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>

    function toggleSidebar(){ document.getElementById('sidebar').classList.toggle('show'); }
</script>
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
                // Redirigir al script de limpieza de sesión
                window.location.href = 'http://localhost/ARCHI-/app/logout.php';

            }
        });
    }


</script>
</body>
</html>