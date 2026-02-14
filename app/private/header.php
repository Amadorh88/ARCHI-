<?php
session_start();
require_once "../config/db.php";

// =============================
// CONTROL DE SEGURIDAD (HILL)
// =============================
// Disciplina: Sin sesión no hay acceso.
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.html');
    exit();
}

$database = new Database();
$db = $database->getConnection();

$nombreUsuario = $_SESSION['nombre'] ?? "Usuario";
$rolUsuario = $_SESSION['rol'] ?? "secretario"; // admin, secretario, archivista, parroco

// Definición de permisos según el Sistema de Roles (Principio de Autoridad - Cialdini)
// admin, archivista, secretario, parroco
$rol = $_SESSION['rol'] ?? 'secretario';

$puede_crear = in_array($rolUsuario, ['admin', 'secretario']);
$puede_editar = in_array($rolUsuario, ['admin', 'archivista', 'secretario']);
$es_solo_lector = ($rol === 'parroco');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel Administrativo | Archidiócesis de Malabo</title>

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
            width: 260px;
            min-height: 100vh;
            background: #0f172a;
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
            background: #1e293b;
            color: #fff;
        }

        .sidebar .nav-link i {
            font-size: 1.1rem;
        }

        .logo {
            font-weight: 600;
            color: #fff;
            border-bottom: 1px solid #1e293b;
        }

        .content {
            flex-grow: 1;
        }

        .topbar {
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
        }

        .role-badge {
            font-size: 0.7rem;
            letter-spacing: 0.5px;
        }

        @media(max-width:991px) {
            .sidebar {
                position: fixed;
                z-index: 1050;
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
                    <button type="button" class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast"></button>
                </div>
            </div>
        </div>

        <nav id="sidebar" class="sidebar p-3 shadow">
            <div class="text-center pb-4 mb-4 logo">
                <i class="bi bi-shield-shaded h2 text-primary"></i>
                <div class="mt-2">Archidiócesis</div>
                <small class="text-muted opacity-75">Gestor Sacramental</small>
            </div>

            <ul class="nav nav-pills flex-column small">
                <li class="nav-item">
                    <a class="nav-link active" href="dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                </li>

                <?php if ($rolUsuario !== 'parroco'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="feligres.php"><i class="bi bi-people me-2"></i>Feligreses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="ministros.php"><i class="bi bi-person-badge me-2"></i>Ministros</a>
                    </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link" href="sacramentos.php"><i class="bi bi-droplet me-2"></i>Sacramentos</a>
                </li>

                <?php if ($rolUsuario !== 'parroco'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="catequesis.php"><i class="bi bi-journal-bookmark me-2"></i>Catequesis</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="parroquias.php"><i class="bi bi-house-door me-2"></i>Parroquias</a>
                    </li>
                <?php endif; ?>

                <?php if ($rolUsuario === 'admin' || $rolUsuario === 'archivista'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="pagos.php"><i class="bi bi-cash-coin me-2"></i>Pagos / Ofrendas</a>
                    </li>
                <?php endif; ?>

                <?php if ($rolUsuario === 'admin'): ?>
                    <li class="nav-item mt-4 pt-3 border-top border-secondary">
                        <span class="text-muted x-small text-uppercase ps-3" style="font-size: 0.7rem;">Configuración</span>
                        <a class="nav-link text-warning" href="usuarios.php">
                            <i class="bi bi-person-gear me-2"></i>Gestión Usuarios
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

                // Cialdini: Compromiso y Coherencia
                function confirmarSalida() {
                    Swal.fire({
                        title: '¿Cerrar sesión?',
                        text: "Su sesión actual será finalizada.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#0f172a',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, salir',
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'logout.php';
                        }
                    })
                }
            </script>