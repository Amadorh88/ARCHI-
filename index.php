<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archi+ | Sistema de Gestión Sacramental</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 para notificaciones -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-image: url('imagenes/catedral.jpg'); background-size: cover; background-position: center; background-attachment: fixed; min-height: 100vh; display: flex; flex-direction: column; }
        .overlay { flex: 1; background: rgba(255, 255, 255, 0.67); }
        .container { max-width: 1000px; margin: 0 auto; padding: 1.5rem; transition: filter 0.3s ease; }
        .blur-active { filter: blur(8px); pointer-events: none; }

        /* Navegación y Header */
        .header { text-align: center; padding: 1.5rem 0; }
        .logo-container { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px; margin-bottom: 0.0rem; }
        .brand-logo { height: 60px; width: auto; object-fit: contain; }
        
        .nav-menu { background: #164b7f; border-radius: 10px; padding: 0.5rem; display: flex; justify-content: center; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 2rem; }
        .nav-link { color: #fff; text-decoration: none; padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer; font-size: 0.9rem; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { background: #2f8ad3; }

        /* Secciones */
        .section { background: #fff; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 1.5rem; border: 1px solid #e0e0e0; }
        .section h2 { color: #000; margin-bottom: 1rem; border-left: 3px solid #14448b; padding-left: 0.75rem; }
        
        /* Grid y elementos */
        .features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin: 1rem 0; }
        .feature-item { background: #f5f5f5; padding: 1rem; border-radius: 10px; text-align: center; }
        .service-list { list-style: none; display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.5rem; margin: 1rem 0; }
        .service-list li { padding: 0.5rem; background: #f5f5f5; border-radius: 8px; text-align: center; }

        /* Modal */
        .modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: none; justify-content: center; align-items: center; z-index: 1000; }
        .modal-content { background: #fff; padding: 2.5rem; border-radius: 16px; width: 90%; max-width: 400px; position: relative; box-shadow: 0 20px 40px rgba(0,0,0,0.2); }
        .close-btn { position: absolute; top: 15px; right: 20px; font-size: 1.5rem; cursor: pointer; }
        .input-group { position: relative; margin-bottom: 1.2rem; }
        .input-group i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #888; }
        .form-control { width: 100%; padding: 0.8rem 0.8rem 0.8rem 45px; border: 2px solid #eee; border-radius: 10px; }
        .btn-full { width: 100%; padding: 0.9rem; background: #164b7f; color: #fff; border: none; border-radius: 10px; cursor: pointer; font-weight: bold; }
        
        /* Footer */
        .footer { text-align: center; padding: 1.5rem; background: rgba(255, 255, 255, 0.8); border-top: 1px solid #ddd; }
    </style>
</head>
<body>

<div class="overlay">
    <div class="container">
        <div class="header">
            <h1 class="logo-container">
                <img src="imagenes/archi.png" alt="Logo Archi+" class="brand-logo">
            </h1>
            <p>Gestión Sacramental</p>
        </div>

        <nav class="nav-menu">
            <a class="nav-link active" onclick="showSection('inicio', this)">Inicio</a>
            <a class="nav-link" onclick="showSection('nosotros', this)">Nosotros</a>
            <a class="nav-link" onclick="showSection('servicios', this)">Servicios</a>
            <a class="nav-link" onclick="showSection('horarios', this)">Horarios</a>
            <a class="nav-link" onclick="showSection('contacto', this)">Contacto</a>
            <a class="nav-link" onclick="toggleModal(true)">Acceder</a>
        </nav>

        <div id="inicio" class="section">
            <h2>Le damos la Bienvenida</h2>
            <p>Sistema de gestión sacramental para feligreses.</p>
            <div class="features-grid">
                <div class="feature-item"><i class="fas fa-users"></i><h4>Feligreses</h4></div>
                <div class="feature-item"><i class="fas fa-bible"></i><h4>Sacramentos</h4></div>
                <div class="feature-item"><i class="fas fa-chalkboard-teacher"></i><h4>Catequesis</h4></div>
                <div class="feature-item"><i class="fas fa-hand-holding-heart"></i><h4>Pagos</h4></div>
            </div>
        </div>

        <div id="nosotros" class="section" style="display: none;">
            <h2>Quiénes Somos</h2>
            <p>Comunidad comprometida con la fe y el servicio.</p>
            <h3>Misión</h3><p>Anunciar el Evangelio y servir con amor.</p>
        </div>

        <div id="servicios" class="section" style="display: none;">
            <h2>Servicios</h2>
            <h3>Sacramentos</h3>
            <ul class="service-list"><li>Bautismos</li><li>Primera Comunión</li><li>Confirmación</li><li>Matrimonios</li></ul>
        </div>

        <div id="horarios" class="section" style="display: none;">
            <h2>Horarios</h2>
            <p>Oficina: Lun-Vie 9-16h | Misas: Lun-Vie 7h y 18h | Confesiones: Mar-Jue 17h</p>
        </div>

        <div id="contacto" class="section" style="display: none;">
            <h2>Contacto</h2>
            <p>Dirección: Av. Independencia, Malabo</p>
            <p>Tel: 222-111-333</p>
        </div>
    </div>
</div>

<div id="login-modal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="toggleModal(false)">&times;</span>
        <h2>Acceso</h2>
        <form action="auth.php" method="POST">
            <div class="input-group"><i class="fas fa-user"></i><input type="text" id="usuario" name="usuario" class="form-control" placeholder="Usuario" required></div>
            <div class="input-group"><i class="fas fa-key"></i><input type="password" id="contraseña" name="contraseña" class="form-control" placeholder="Contraseña" required></div>
            <button type="submit" class="btn-full">Iniciar Sesión</button>
        </form>
    </div>
</div>

<!-- Lógica de error de autenticación con SweetAlert2 -->
<?php if (isset($_SESSION['error'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                icon: 'error',
                title: 'Error de acceso',
                text: '<?php echo $_SESSION['error']; ?>',
                confirmButtonText: 'Reintentar'
            }).then(() => {
                toggleModal(true);
            });
        });
    </script>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<footer class="footer">
    <p>&copy; <?php echo date('Y'); ?> Archi+ - Gestión Sacramental</p>
</footer>

<script>
    function showSection(id, el) {
        document.querySelectorAll('.section').forEach(s => s.style.display = 'none');
        document.getElementById(id).style.display = 'block';
        document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
        if(el) el.classList.add('active');
    }
    function toggleModal(show) {
        document.getElementById('login-modal').style.display = show ? 'flex' : 'none';
        document.querySelector('.container').classList.toggle('blur-active', show);
    }
    window.onclick = (e) => { if(e.target == document.getElementById('login-modal')) toggleModal(false); }
</script>
</body>
</html>