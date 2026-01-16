<?php
session_start();
require_once '../config/database.php';

// Verificar autenticación
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}

// Obtener datos de usuarios
$query = "SELECT id, nombre, dni, usuario, rol, estado, fecha_registro FROM usuarios ORDER BY id DESC";
$stmt = $bd->prepare($query);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Configurar para impresión
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Usuarios</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #2c3e50; }
        .header .fecha { color: #7f8c8d; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #2c3e50; color: white; padding: 8px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        .badge { padding: 2px 6px; border-radius: 3px; font-size: 11px; font-weight: bold; }
        .badge-admin { background-color: #e74c3c; color: white; }
        .badge-secretario { background-color: #3498db; color: white; }
        .badge-archivista { background-color: #2ecc71; color: white; }
        .badge-parroco { background-color: #9b59b6; color: white; }
        .badge-activo { background-color: #2ecc71; color: white; }
        .badge-inactivo { background-color: #e74c3c; color: white; }
        .footer { margin-top: 30px; text-align: center; color: #7f8c8d; font-size: 11px; }
        @media print {
            .no-print { display: none; }
            button { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="background: #3498db; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
            <i class="fas fa-print"></i> Imprimir
        </button>
        <button onclick="window.close()" style="background: #95a5a6; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin-left: 10px;">
            <i class="fas fa-times"></i> Cerrar
        </button>
    </div>
    
    <div class="header">
        <h1>Lista de Usuarios</h1>
        <div class="fecha">
            Generado el: <?php echo date('d/m/Y H:i:s'); ?> | 
            Total: <?php echo count($usuarios); ?> usuarios
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>DNI</th>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Fec. Registro</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($usuarios as $usuario): ?>
            <tr>
                <td><?php echo htmlspecialchars($usuario['id']); ?></td>
                <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                <td><?php echo htmlspecialchars($usuario['dni']); ?></td>
                <td><?php echo htmlspecialchars($usuario['usuario']); ?></td>
                <td>
                    <span class="badge badge-<?php echo $usuario['rol']; ?>">
                        <?php 
                        $roles = [
                            'admin' => 'Admin',
                            'secretario' => 'Secretario',
                            'archivista' => 'Archivista',
                            'parroco' => 'Párroco'
                        ];
                        echo htmlspecialchars($roles[$usuario['rol']] ?? $usuario['rol']); 
                        ?>
                    </span>
                </td>
                <td>
                    <span class="badge badge-<?php echo $usuario['estado'] == 1 ? 'activo' : 'inactivo'; ?>">
                        <?php echo $usuario['estado'] == 1 ? 'Activo' : 'Inactivo'; ?>
                    </span>
                </td>
                <td><?php echo date('d/m/Y H:i', strtotime($usuario['fecha_registro'])); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="footer">
        Sistema de Gestión Parroquial © <?php echo date('Y'); ?>
    </div>
    
    <script>
        // Auto-imprimir al cargar la página
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>