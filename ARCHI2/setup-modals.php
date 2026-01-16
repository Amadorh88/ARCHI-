<?php
// Este script ayuda a configurar el sistema de modales
echo "<h2>Configuración del Sistema de Modales</h2>";

// 1. Verificar si dashboard-admin.php existe
if (!file_exists('dashboard-admin.php')) {
    echo "<p style='color: red;'>❌ dashboard-admin.php no existe</p>";
} else {
    echo "<p style='color: green;'>✅ dashboard-admin.php existe</p>";
}

// 2. Verificar archivos CSS y JS
$files = [
    'css/modals-certificates.css',
    'js/modals-system.js',
    'api/usuarios.php',
    'api/feligreses.php'
];

foreach ($files as $file) {
    if (!file_exists($file)) {
        echo "<p style='color: orange;'>⚠️ $file - Debe ser creado</p>";
    } else {
        echo "<p style='color: green;'>✅ $file existe</p>";
    }
}

echo "<h3>Pasos para completar:</h3>";
echo "<ol>";
echo "<li>Copiar el contenido de dashboard-admin.php a un archivo nuevo</li>";
echo "<li>Crear los archivos CSS y JS según el código proporcionado</li>";
echo "<li>Actualizar los módulos existentes con los nuevos botones</li>";
echo "<li>Crear las APIs para cada módulo (usuarios.php, feligreses.php, etc.)</li>";
echo "<li>Agregar los enlaces a CSS y JS en dashboard.php y dashboard-admin.php</li>";
echo "<li>Modificar login.php para redirigir admins al nuevo dashboard</li>";
echo "</ol>";
?>