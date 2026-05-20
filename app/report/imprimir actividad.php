<?php
require_once '../config/db.php'; // Ajusta la ruta a tu config si es necesario

if (!isset($_GET['id_actividad']) || !filter_var($_GET['id_actividad'], FILTER_VALIDATE_INT)) {
    die('ID de actividad no válido.');
}

$id_actividad = $_GET['id_actividad'];

try {
    $db = new Database();
    $pdo = $db->getConnection();

    $stmt = $pdo->prepare("SELECT a.id_actividad, a.id_usuario, u.nombre as nombre_usuario, u.rol, a.accion, a.modulo, a.fecha, a.ip 
                           FROM actividades a 
                           INNER JOIN usuarios u ON a.id_usuario = u.id 
                           WHERE a.id_actividad = :id_actividad LIMIT 1");
    $stmt->bindParam(':id_actividad', $id_actividad, PDO::PARAM_INT);
    $stmt->execute();
    $actividad = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$actividad) {
        die('Actividad no encontrada.');
    }
} catch (Exception $e) {
    die('Error en el sistema: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ficha_Actividad_<?php echo $actividad['id_actividad']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #fff; font-size: 14px; color: #333; }
        .ficha-container { max-width: 800px; margin: 30px auto; border: 2px solid #333; padding: 20px; border-radius: 8px; }
        .header-ficha { border-bottom: 2px double #333; padding-bottom: 15px; margin-bottom: 20px; }
        .table-ficha th { width: 30%; background-color: #f4f4f4 !important; font-weight: bold; }
        
        /* Estilos de impresión */
        @media print {
            .no-print { display: none !important; }
            .ficha-container { border: none; margin: 0; padding: 0; }
            body { background-color: #fff; }
        }
    </style>
</head>
<body>

<div class="container no-print my-3 text-center">
    <button onclick="window.print();" class="btn btn-primary me-2">
        <i class="glyphicon glyphicon-print"></i> Imprimir Ficha
    </button>
    <button onclick="window.close();" class="btn btn-secondary">Cerrar Ventana</button>
</div>

<div class="ficha-container shadow-sm">
    <div class="header-ficha d-flex justify-content-between align-items-center">
        <div>
            <h3 class="mb-1 text-uppercase fw-bold">Sistema de Gestión Parroquial</h3>
            <p class="text-muted mb-0">Control de Auditoría y Registro de Actividades</p>
        </div>
        <div class="text-end">
            <span class="badge bg-dark fs-6">FICHA DE AUDITORÍA</span>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-6">
            <strong>Fecha de Impresión:</strong> <?php echo date('d/m/Y H:i:s'); ?>
        </div>
        <div class="col-6 text-end">
            <strong>ID Actividad / Registro:</strong> #<?php echo $actividad['id_actividad']; ?>
        </div>
    </div>

    <h5 class="fw-bold mb-3 text-secondary border-bottom pb-1">Detalles del Suceso</h5>
    <table class="table table-bordered table-ficha align-middle">
        <tr>
            <th>Módulo del Sistema</th>
            <td><span class="fw-bold"><?php echo htmlspecialchars($actividad['modulo']); ?></span></td>
        </tr>
        <tr>
            <th>Acción Realizada</th>
            <td class="fs-5 text-primary fw-semibold"><?php echo htmlspecialchars($actividad['accion']); ?></td>
        </tr>
        <tr>
            <th>Fecha y Hora del Suceso</th>
            <td><?php echo date('d/m/Y H:i:s', strtotime($actividad['fecha'])); ?></td>
        </tr>
    </table>

    <h5 class="fw-bold mb-3 mt-4 text-secondary border-bottom pb-1">Datos del Operador (Usuario)</h5>
    <table class="table table-bordered table-ficha align-middle">
        <tr>
            <th>ID Interno / Usuario</th>
            <td>ID: <?php echo $actividad['id_usuario']; ?></td>
        </tr>
        <tr>
            <th>Nombre Completo</th>
            <td><?php echo htmlspecialchars($actividad['nombre_usuario']); ?></td>
        </tr>
        <tr>
            <th>Rol / Permisos</th>
            <td><span class="badge bg-info text-dark text-uppercase"><?php echo htmlspecialchars($actividad['rol']); ?></span></td>
        </tr>
        <tr>
            <th>Dirección IP de Conexión</th>
            <td><code><?php echo htmlspecialchars($actividad['ip'] ?? 'No registrada'); ?></code></td>
        </tr>
    </table>

    <div class="mt-5 pt-5">
        <div class="row text-center">
            <div class="col-4 offset-4">
                <hr style="border-top: 1px solid #333;">
                <p class="mb-0 fw-bold">Firma del Administrador</p>
                <p class="small text-muted">Sello del Sistema</p>
            </div>
        </div>
    </div>
</div>

</body>
</html>