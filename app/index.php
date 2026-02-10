<?php
// index.php
session_start();

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit();
}

// Si no está logueado, mostrar la página de inicio
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --success: #27ae60;
            --danger: #e74c3c;
            --warning: #f39c12;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --gold: #d4af37;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        /* Barra de navegación */
        .navbar {
            background-color: rgba(44, 62, 80, 0.95);
            backdrop-filter: blur(10px);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.2);
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
            text-decoration: none;
            color: white;
        }
        
        .logo-icon {
            font-size: 2rem;
            color: var(--gold);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .logo-text h1 {
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(to right, var(--gold), #fff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .logo-text p {
            font-size: 0.9rem;
            color: #bdc3c7;
        }
        
        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        
        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
            position: relative;
            padding: 0.5rem 0;
        }
        
        .nav-links a:hover {
            color: var(--gold);
        }
        
        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--gold);
            transition: width 0.3s;
        }
        
        .nav-links a:hover::after {
            width: 100%;
        }
        
        .login-btn {
            background: linear-gradient(135deg, var(--secondary), #2980b9);
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }
        
        .login-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
            color: white;
        }
        
        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 6rem 2rem 2rem;
            position: relative;
            overflow: hidden;
        }
        
        .hero-bg {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(rgba(44, 62, 80, 0.9), rgba(44, 62, 80, 0.7)),
                        url('https://images.unsplash.com/photo-1544717305-2782549b5136?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80');
            background-size: cover;
            background-position: center;
            z-index: -1;
        }
        
        .hero-content {
            max-width: 1200px;
            text-align: center;
            color: white;
            z-index: 1;
        }
        
        .hero-title {
            font-size: 4rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            animation: fadeInUp 1s ease;
        }
        
        .hero-subtitle {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            color: #ecf0f1;
            animation: fadeInUp 1s ease 0.2s both;
        }
        
        .hero-description {
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto 3rem;
            line-height: 1.8;
            animation: fadeInUp 1s ease 0.4s both;
        }
        
        .cta-buttons {
            display: flex;
            gap: 2rem;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 1s ease 0.6s both;
        }
        
        .btn {
            padding: 1.25rem 3rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--gold), #b8860b);
            color: white;
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
        }
        
        .btn-primary:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(212, 175, 55, 0.6);
            color: white;
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
        }
        
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-5px);
            color: white;
        }
        
        /* Features Section */
        .features {
            padding: 6rem 2rem;
            background: white;
        }
        
        .section-title {
            text-align: center;
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 3rem;
            position: relative;
        }
        
        .section-title::after {
            content: '';
            display: block;
            width: 100px;
            height: 4px;
            background: linear-gradient(to right, var(--secondary), var(--gold));
            margin: 1rem auto;
            border-radius: 2px;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 3rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .feature-card {
            background: #f8f9fa;
            padding: 2.5rem;
            border-radius: 20px;
            text-align: center;
            transition: all 0.3s;
            border: 1px solid #e9ecef;
            position: relative;
            overflow: hidden;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border-color: var(--secondary);
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--secondary), var(--gold));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
            color: white;
        }
        
        .feature-card h3 {
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }
        
        .feature-card p {
            color: #666;
            line-height: 1.6;
        }
        
        /* About Section */
        .about {
            padding: 6rem 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            position: relative;
        }
        
        .about-content {
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
        }
        
        .about h2 {
            font-size: 3rem;
            margin-bottom: 2rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .about p {
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto 2rem;
            line-height: 1.8;
        }
        
        /* Footer */
        .footer {
            background-color: var(--primary);
            color: white;
            padding: 4rem 2rem 2rem;
        }
        
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
        }
        
        .footer-section h3 {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            color: var(--gold);
        }
        
        .footer-section p {
            color: #bdc3c7;
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        
        .footer-links {
            list-style: none;
        }
        
        .footer-links li {
            margin-bottom: 0.75rem;
        }
        
        .footer-links a {
            color: #bdc3c7;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-links a:hover {
            color: var(--gold);
        }
        
        .footer-social {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        .footer-social a {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .footer-social a:hover {
            background: var(--secondary);
            transform: translateY(-3px);
        }
        
        .footer-bottom {
            text-align: center;
            margin-top: 4rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: #95a5a6;
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .hero-title {
                font-size: 3rem;
            }
            
            .hero-subtitle {
                font-size: 1.3rem;
            }
            
            .section-title {
                font-size: 2.5rem;
            }
        }
        
        @media (max-width: 768px) {
            .navbar {
                padding: 1rem;
            }
            
            .nav-links {
                display: none;
            }
            
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }
            
            .section-title {
                font-size: 2rem;
            }
        }
        
        @media (max-width: 480px) {
            .hero {
                padding-top: 5rem;
            }
            
            .hero-title {
                font-size: 2rem;
            }
            
            .features {
                padding: 4rem 1rem;
            }
            
            .feature-card {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Barra de navegación -->
    <nav class="navbar">
        <a href="index.php" class="logo">
            <div class="logo-icon">
                <i class="fas fa-church"></i>
            </div>
            <div class="logo-text">
                <h1>Archi+</h1>
                <p>Gestión Sacramental</p>
            </div>
        </a>
        
        <div class="nav-links">
            <a href="#inicio">Inicio</a>
            <a href="#acerca">Acerca</a>
            <a href="#contacto">Contacto</a>
            <a href="index.html" class="login-btn">
                <i class="fas fa-sign-in-alt"></i>Acceder
            </a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="inicio" class="hero">
        <div class="hero-bg"></div>
        <div class="hero-content">
            <h1 class="hero-title">Sistema de Gestión Sacramental</h1>
            <p class="hero-subtitle">Moderniza y optimiza la administración de tu parroquia</p>
            <p class="hero-description">
                Una solución completa para la gestión de sacramentos, feligreses, catequesis y actividades parroquiales. 
                Diseñado para facilitar el trabajo administrativo y espiritual de tu comunidad.
            </p>
            
            <div class="cta-buttons">
                <a href="index.html" class="btn btn-primary">
                    <i class="fas fa-rocket"></i> Acceder al Sistema
                </a>
                <a href="#caracteristicas" class="btn btn-secondary">
                    <i class="fas fa-info-circle"></i> Más Información
                </a>
            </div>
        </div>
    </section>

   
    <!-- Acerca de -->
    <section id="acerca" class="about">
        <div class="about-content">
            <h2>Sobre Nuestro Sistema</h2>
            <p>
                Desarrollado con la experiencia de más de 10 años trabajando con comunidades parroquiales, 
                nuestro sistema combina tecnología moderna con un profundo entendimiento de las necesidades 
                administrativas y pastorales de la Iglesia.
            </p>
            <p>
                Con un equipo de desarrolladores comprometidos y en constante comunicación con párrocos y 
                administradores diocesanos, garantizamos una solución que realmente funciona para tu comunidad.
            </p>
            <a href="login.php" class="btn btn-primary" style="margin-top: 2rem;">
                <i class="fas fa-play-circle"></i> Comenzar Ahora
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contacto" class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Archi+</h3>
                <p>Solución integral para la gestión administrativa y sacramental de parroquias y diócesis.</p>
              </div>
                       
            <div class="footer-section">
                <h3>Contacto</h3>
                <p><i class="fas fa-map-marker-alt"></i> Av. de la Independencia, Malabo</p>
                <p><i class="fas fa-phone"></i> (123) 456-7890</p>
            </div>
            
            <div class="footer-section">
                <h3>Horario de Atención</h3>
                <p>Lunes a Viernes: 8:00 AM - 6:00 PM</p>
                <p>Sábados: 9:00 AM - 1:00 PM</p>
                <p>Domingo: Cerrado (Día del Señor)</p>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2025 Archi+. Todos los derechos reservados. | Desarrollado por ABAE</p>
            <p style="margin-top: 0.5rem; font-size: 0.9rem;">Versión 1.2 - Archi+</p>
        </div>
    </footer>

    <script>        
        // Navegación suave
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            });
        });
        
        // Efecto de scroll en navbar
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 100) {
                navbar.style.backgroundColor = 'rgba(44, 62, 80, 0.98)';
                navbar.style.backdropFilter = 'blur(15px)';
            } else {
                navbar.style.backgroundColor = 'rgba(44, 62, 80, 0.95)';
                navbar.style.backdropFilter = 'blur(10px)';
            }
        });
        
        // Inicializar cuando se carga la página
        document.addEventListener('DOMContentLoaded', function() {
            startCountersWhenVisible();
            
            // Mostrar año actual en copyright
            const yearElement = document.querySelector('.footer-bottom p');
            if (yearElement) {
                yearElement.innerHTML = yearElement.innerHTML.replace('2025', new Date().getFullYear());
            }
            
            // Efecto de carga inicial
            document.body.style.opacity = '0';
            document.body.style.transition = 'opacity 0.5s';
            setTimeout(() => {
                document.body.style.opacity = '1';
            }, 100);
        });
        
        // Prevenir envío de formularios vacíos (para futuros formularios)
        document.addEventListener('submit', function(e) {
            if (e.target.tagName === 'FORM') {
                const inputs = e.target.querySelectorAll('input[required], textarea[required]');
                let isValid = true;
                
                inputs.forEach(input => {
                    if (!input.value.trim()) {
                        isValid = false;
                        input.style.borderColor = 'var(--danger)';
                    } else {
                        input.style.borderColor = '';
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    alert('Por favor, complete todos los campos requeridos.');
                }
            }
        });
    </script>
</body>
</html>