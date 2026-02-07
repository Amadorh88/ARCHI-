<?php
$menu = [
    [
        "id" => "inicio",
        "name" => "Inicio",
        "icon" => "fas fa-home"
    ],
    [
        "id" => "feligreses",
        "name" => "Feligreses",
        "icon" => "fas fa-users"
    ],
    [
        "id" => "catequesis",
        "name" => "Catequesis",
        "icon" => "fas fa-book",
        "submenu" => [
            ["id" => "prebautismal", "name" => "Prebautismal"],
            ["id" => "comunion-catequesis", "name" => "Comunión"],
            ["id" => "confirmacion-catequesis", "name" => "Confirmación"],
            ["id" => "prematrimonial", "name" => "Prematrimonial"]
        ]
    ],
    [
        "id" => "sacramentos",
        "name" => "Sacramentos",
        "icon" => "fas fa-church",
        "submenu" => [
            ["id" => "bautismo", "name" => "Bautismo"],
            ["id" => "comunion", "name" => "Comunión"],
            ["id" => "confirmacion", "name" => "Confirmación"],
            ["id" => "matrimonio", "name" => "Matrimonio"]
        ]
    ],
    [
        "id" => "administracion",
        "name" => "Administración",
        "icon" => "fas fa-cogs",
        "submenu" => [
            ["id" => "catequistas", "name" => "Catequistas"],
            ["id" => "ministros", "name" => "Ministros"],
            ["id" => "parroquias", "name" => "Parroquias"],
            ["id" => "curso", "name" => "Parroquias"],
            ["id" => "pagos", "name" => "Pagos"]
        ]
    ],
    [
        "id" => "configuracion",
        "name" => "Configuración",
        "icon" => "fas fa-user-cog",
        "submenu" => [
            ["id" => "usuarios", "name" => "Usuarios"],
            ["id" => "curso", "name" => "Cursos"],
            ["id" => "actividades", "name" => "actividades"]
        ]
    ]
];
?>

<div class="sidebar">
    <div class="sidebar-header">
        <h2>
            <span class="tooltip">
                <i class="fas fa-church"></i> ARCHI+
                <span class="tooltiptext">Gestión Parroquial</span>
            </span>
        </h2>
    </div>
   <div class="sidebar-menu" id="sidebarMenu">
        <?php foreach ($menu as $item): ?>
            <?php if (in_array($item['id'], $permisos)): ?>
                <?php 
                    // Sanitización de IDs y Nombres para prevenir XSS en JS
                    $itemId = htmlspecialchars($item['id'], ENT_QUOTES, 'UTF-8');
                    $itemName = htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8');
                    
                    $onclick_action = isset($item['submenu']) 
                        ? "toggleSubmenu(this)" 
                        : "showContent('{$itemId}', '{$itemName}')"; 
                ?>
                <div class="menu-item" onclick="<?php echo $onclick_action; ?>">
                    <span class="tooltip">
                        <i class="<?php echo htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8'); ?>"></i>
                        <span class="tooltiptext"><?php echo $itemName; ?></span>
                    </span>
                    <span><?php echo $itemName; ?></span>
                    <?php if (isset($item['submenu'])): ?>
                        <span class="tooltip chevron">
                            <i class="fas fa-chevron-down" style="margin-left: auto;"></i>
                            <span class="tooltiptext">Desplegar submenú</span>
                        </span>
                    <?php endif; ?>
                </div>
                
                <?php if (isset($item['submenu'])): ?>
                    <div class="submenu">
                        <?php foreach ($item['submenu'] as $subItem): ?>
                            <?php if (in_array($subItem['id'], $permisos)): ?>
                                <?php
                                    // Sanitización de Sub-IDs y Sub-Nombres para prevenir XSS en JS
                                    $subItemId = htmlspecialchars($subItem['id'], ENT_QUOTES, 'UTF-8');
                                    $subItemName = htmlspecialchars($subItem['name'], ENT_QUOTES, 'UTF-8');
                                ?>
                                <div class="submenu-item" onclick="showContent('<?php echo $subItemId; ?>', '<?php echo $subItemName; ?>')">
                                    <span class="tooltip">
                                        <?php echo $subItemName; ?>
                                        <span class="tooltiptext">Ir a <?php echo $subItemName; ?></span>
                                    </span>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>