<?php
session_start();
require_once "../config/db.php";

// =============================
// CONTROL DE SEGURIDAD (HILL)
// =============================
// Disciplina: Sin sesión no hay acceso.
if (!isset($_SESSION['usuario'])) {
    header('Location:../index.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();

$nombreUsuario = $_SESSION['nombre'] ?? "Usuario";
$rolUsuario = $_SESSION['rol'] ?? "secretario"; // admin, secretario, archivista, parroco

// Definición de permisos según el Sistema de Roles (Principio de Autoridad - Cialdini)
$rol = $_SESSION['rol'] ?? 'secretario';

$puede_crear = in_array($rolUsuario, ['admin', 'secretario']);
$puede_editar = in_array($rolUsuario, ['admin', 'archivista', 'secretario']);
$puede_eliminar = in_array($rolUsuario, ['admin','secretario']);

$es_solo_lector = ($rol === 'parroco');

// ============================================
// FUNCIÓN PARA REGISTRAR ACCESOS A MÓDULOS
// ============================================
function registrarAccesoModulo($modulo) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    
    $accion = "ACCEDIÓ AL MÓDULO";
    $moduloNombre = pathinfo($modulo, PATHINFO_FILENAME);
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $id_usuario = $_SESSION['id'] ?? null;
    $nombre_usuario = $_SESSION['usuario'] ?? $_SESSION['nombre'] ?? '';
    
    try {
        require_once __DIR__ . "/../config/db.php";
        $database = new Database();
        $pdo = $database->getConnection();
        
        $sql = "INSERT INTO actividades (id_usuario, nombre_usuario, accion, modulo, ip) 
                VALUES (:id_usuario, :nombre_usuario, :accion, :modulo, :ip)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id_usuario' => $id_usuario,
            ':nombre_usuario' => $nombre_usuario,
            ':accion' => $accion,
            ':modulo' => $moduloNombre,
            ':ip' => $ip
        ]);
    } catch (Exception $e) {
        error_log("Error al registrar acceso: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel Administrativo | Catedral de Malabo</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
         body {
            background: #f4f6f9;
            font-size: 0.9rem;
            overflow-x: hidden;
        }

        .sidebar {
            width: 200px;
            min-height: 100vh;
            background: #164b7f;
            transition: all 0.3s;
        }

        .sidebar .nav-link {
            color: #cbd5e1;
            border-radius: 8px;
            margin-bottom: 5px;
            padding: 10px 15px;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: #0d5eb0;
            color: #fff;
        }

        .sidebar .nav-link i {
            font-size: 1.1rem;
        }

        .logo {
            font-weight: 700;
            color: #fff;
            border-bottom: 1px solid #1e293b;
            text-align: center;
            padding-bottom: 0.3rem;
            margin-bottom: 0.3rem;
        }
        
        /* Estilos para el logo con borde redondeado y sombra */
        .logo-img {
            width: 90px;
            height: 100px;
            object-fit: cover;
            border-radius: 60%;
            border: 2px solid rgba(255, 255, 255, 0);
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0);
            transition: all 0.3s ease;
            background: #ffffff00;
            padding: 5px;
        }
        
        /* Efecto hover para el logo */
        .logo-img:hover {
            transform: scale(1.05);
            border-color: rgba(255, 255, 255, 0);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0);
        }
        
        .logo-text {
            font-size: 0.65rem;
            font-weight: 10;
            margin-top: 0px;
            letter-spacing: 0.5px;
        }
        
        .content {
            flex-grow: 1;
        }

        .topbar {
            background: #ffffffcb;
            border-bottom: 1px solid #e5e7eb;
        }

        .role-badge {
            font-size: 0.7rem;
            letter-spacing: 0.5px;
        }

        @media(max-width:991px) {
            .sidebar {
                position: fixed;
                z-index: 1000;
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }
        }
    </style>
</head>

<body>
    <div class="d-flex">
       
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 1080">
            <div id="toastLive" class="toast align-items-center border-0 text-white" role="alert">
                <div class="d-flex">
                    <div class="toast-body" id="toastMensaje"></div>
                </div>
            </div>
        </div>
        <nav id="sidebar" class="sidebar p-3 shadow">
            <div class="text-center logo">
                <!-- Logo con borde redondeado y sombra -->
                <img src="../imagenes/logo.png" alt="Logo Archidiócesis" class="logo-img" 
                     onerror="this.onerror=null; this.src='../assets/img/logo-default.png'">
                <div class="logo-text">Inmaculado Corazón de María</div>
                
            </div>
            <ul class="nav nav-pills flex-column small">
                <li class="nav-item">
                    <a class="nav-link active" href="dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Inicio</a>
                </li>

                <?php if ($rolUsuario !== 'parroco'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="feligres.php"><i class="bi bi-people me-2"></i>Feligreses</a>
                    </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link" href="sacramentos.php"><i class="bi bi-droplet me-2"></i>Sacramentos</a>
                </li>

                <?php if ($rolUsuario !== 'parroco'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="catequesis.php"><i class="bi bi-journal-bookmark me-2"></i>Catequesis</a>
                    </li>
                <?php endif; ?>

                <?php if ($rolUsuario === 'admin' || $rolUsuario === 'archivista'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="pagos.php"><i class="bi bi-cash-coin me-2"></i>Estipendios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="ministros.php"><i class="bi bi-person-badge me-2"></i>Ministros</a>
                    </li>
                <?php endif; ?>

                <?php if ($rolUsuario === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="parroquias.php"><i class="bi bi-house-door me-2"></i>Parroquias</a>
                    </li>
                    <li class="nav-item mt-4 pt-3 border-top border-secondary">
                        <a class="nav-link text-warning" href="usuarios.php">
                            <i class="bi bi-person-gear me-2"></i> Usuarios
                        </a>
                        <a class="nav-link text-warning" href="actividades.php">
                            <i class="bi bi-clock-history me-2"></i> Actividades
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>

        <div class="content w-100">
            <div class="topbar p-3 d-flex justify-content-between align-items-center flex-wrap gap-2 shadow-sm">
                <button class="btn btn-outline-secondary d-lg-none" onclick="toggleSidebar()">
                    <i class="bi bi-list"></i>
                </button>

                <div class="fw-semibold">
                    <span class="text-primary"><i class="bi bi-geo-alt-fill me-1"></i>Malabo</span>
                    <span class="mx-2 text-muted">|</span>
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem;">Panel
                        Administrativo</small>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <div class="text-end border-end pe-3 d-none d-sm-block">
                        <div class="fw-bold text-dark lh-1"><?php echo htmlspecialchars($nombreUsuario); ?></div>
                        <span class="badge bg-secondary-subtle text-secondary role-badge mt-1 text-uppercase">
                            <?php echo $rolUsuario; ?>
                        </span>
                    </div>

                    <button onclick="confirmarSalida()" class="btn btn-sm btn-danger rounded-circle shadow-sm"
                        title="Cerrar Sesión">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </div>
            </div>

            <script>
                // Hill: Precisión y Orden en las funciones
                function toggleSidebar() {
                    document.getElementById('sidebar').classList.toggle('show');
                }

                // Función para confirmar salida
                function confirmarSalida() {
                    Swal.fire({
                        title: '¿Cerrar sesión?',
                        text: '¿Estás seguro de que deseas salir del sistema?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, salir',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'logout.php';
                        }
                    });
                }

                // ============================================
                // FUNCIÓN GLOBAL PARA REGISTRAR ACTIVIDADES CRUD
                // ============================================
                function registrarActividad(accion, modulo, detalle = null) {
                    const formData = new FormData();
                    formData.append('accion', accion);
                    formData.append('modulo', modulo);
                    if (detalle) formData.append('detalle', detalle);
                    
                    fetch('../api/actividades/registrar.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (!data.success) {
                            console.error('Error al registrar actividad:', data.error);
                        }
                    })
                    .catch(err => console.error('Error en registro de actividad:', err));
                }

                // ============================================
                // FUNCIÓN PARA MOSTRAR TOAST DE NOTIFICACIONES
                // ============================================
                function mostrarToast(mensaje, tipo = "success") {
                    const toastElement = document.getElementById('toastLive');
                    const toastMensaje = document.getElementById('toastMensaje');
                    const colores = {
                        success: "bg-success",
                        error: "bg-danger",
                        info: "bg-primary",
                        warning: "bg-warning text-dark"
                    };
                    
                    toastElement.className = `toast align-items-center border-0 ${colores[tipo] || colores.success}`;
                    toastMensaje.innerHTML = mensaje;
                    
                    const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
                    toast.show();
                }

                // Activar enlace activo en la barra lateral
                document.addEventListener('DOMContentLoaded', function() {
                    const currentPath = window.location.pathname;
                    const currentPage = currentPath.substring(currentPath.lastIndexOf('/') + 1);
                    const navLinks = document.querySelectorAll('#sidebar .nav-link');
                    
                    navLinks.forEach(link => {
                        const href = link.getAttribute('href');
                        if (href && currentPage === href) {
                            navLinks.forEach(l => l.classList.remove('active'));
                            link.classList.add('active');
                        }
                    });
                });
            </script>