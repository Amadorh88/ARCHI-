<?php
require_once '../config/db.php';
$database = new Database();
$db = $database->getConnection();

$modulo = $_GET['modulo'] ?? '';
$accion = $_GET['accion'] ?? '';
$id = $_GET['id'] ?? null;

$datos = [];
$readOnly = ($accion === 'ver') ? 'readonly disabled' : '';

$tabla = '';
$pk = '';
$campos = [];

/* =========================
   DEFINICIÓN DE MÓDULOS
========================= */
switch ($modulo) {

    case 'usuarios':
        $tabla = 'usuarios';
        $pk = 'id';
        $campos = [
            ['label' => 'Nombre', 'name' => 'nombre', 'type' => 'text', 'required' => true],
            ['label' => 'DNI', 'name' => 'dni', 'type' => 'text', 'required' => true],
            ['label' => 'Usuario', 'name' => 'usuario', 'type' => 'text', 'required' => true],
            ['label' => 'Contraseña', 'name' => 'contraseña', 'type' => 'password', 'required' => ($accion === 'crear')],
            [
                'label' => 'Rol',
                'name' => 'rol',
                'type' => 'select',
                'options' => [
                    'admin' => 'Administrador',
                    'secretario' => 'Secretario',
                    'archivista' => 'Archivista',
                    'parroco' => 'Párroco'
                ]
            ],
            [
                'label' => 'Estado',
                'name' => 'estado',
                'type' => 'select',
                'options' => ['1' => 'Activo', '0' => 'Inactivo']
            ],
        ];
        break;

    case 'feligres':
        $tabla = 'feligres';
        $pk = 'id_feligres';
        $campos = [
            ['label' => 'Nombre Completo', 'name' => 'nombre_completo', 'type' => 'text', 'required' => true],
            ['label' => 'Nombre del Padre', 'name' => 'nombre_padre', 'type' => 'text'],
            ['label' => 'Nombre de la Madre', 'name' => 'nombre_madre', 'type' => 'text'],
            ['label' => 'Fecha de Nacimiento', 'name' => 'fecha_nacimiento', 'type' => 'date'],
            ['label' => 'Lugar de Nacimiento', 'name' => 'lugar_nacimiento', 'type' => 'text'],
        ];
        break;

    case 'bautismo':
    case 'comunion':
    case 'confirmacion':
        $tabla = $modulo;
        $pk = "id_$modulo";
        $campos = [
            ['label' => 'Registro', 'name' => 'registro', 'type' => 'text'],
            ['label' => 'Feligres', 'name' => 'id_feligres', 'type' => 'select', 'table' => 'feligres', 'display' => 'nombre_completo'],
            ['label' => 'Fecha', 'name' => 'fecha', 'type' => 'date'],
            ['label' => 'Ministro', 'name' => 'id_ministro', 'type' => 'select', 'table' => 'ministros', 'display' => 'nombre_completo'],
            ['label' => 'Parroquia', 'name' => 'id_parroquia', 'type' => 'select', 'table' => 'parroquia', 'display' => 'nombre'],
        ];
        break;

    case 'catequista':
        $tabla = 'catequista';
        $pk = 'id_catequista';
        $campos = [
            ['label' => 'Nombre', 'name' => 'nombre', 'type' => 'text', 'required' => true],
            ['label' => 'Teléfono', 'name' => 'telefono', 'type' => 'text'],
            ['label' => 'Especialidad', 'name' => 'especialidad', 'type' => 'text'],
        ];
        break;

    case 'parroquia':
        $tabla = 'parroquia';
        $pk = 'id_parroquia';
        $campos = [
            ['label' => 'Nombre', 'name' => 'nombre', 'type' => 'text', 'required' => true],
            ['label' => 'Dirección', 'name' => 'direccion', 'type' => 'text'],
            ['label' => 'Teléfono', 'name' => 'telefono', 'type' => 'text'],
        ];
        break;
    case 'ministros':
        $tabla = 'ministros';
        $pk = 'id_ministro';
        $campos = [
            ['label' => 'Nombre', 'name' => 'nombre_completo', 'type' => 'text', 'required' => true],
            ['label' => 'Dirección', 'name' => 'direccion', 'type' => 'text'],
            ['label' => 'Dip', 'name' => 'dip', 'type' => 'text'],
            ['label' => 'Teléfono', 'name' => 'telefono', 'type' => 'text'],
            [
                'label' => 'Tipo',
                'name' => 'tipo',
                'type' => 'select',
                'options' => [
                    /*  Sacerdote','Diácono','Obispo','Catequista */
                    'sacerdote' => 'Sacerdote',
                    'diácono' => 'Diácono',
                    'obispo' => 'Obispo',
                    'catequista' => 'Catequista'
                ]
            ],
        ];
        break;

    default:
        echo "<div style='color:red;'>Módulo no soportado</div>";
        exit;
}

/* =========================
   CARGAR DATOS (EDITAR / VER)
========================= */
if ($id && in_array($accion, ['editar', 'ver'])) {
    $stmt = $db->prepare("SELECT * FROM $tabla WHERE $pk = ?");
    $stmt->execute([$id]);
    $datos = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$datos) {
        echo "<div style='color:red;'>Registro no encontrado</div>";
        exit;
    }
}
?>

<div class="modal-header">
    <h3><?php echo ucfirst($accion) . ' ' . ucfirst($modulo); ?></h3>
    <button class="modal-close-btn" onclick="document.getElementById('crudModal').style.display='none'">
        &times;
    </button>
</div>

<form method="POST" action="api/guardar.php">
    <input type="hidden" name="modulo" value="<?php echo $modulo; ?>">
    <input type="hidden" name="accion" value="<?php echo $accion; ?>">
    <input type="hidden" name="id" value="<?php echo $id; ?>">

    <div class="form-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
        <?php foreach ($campos as $campo): ?>
            <div class="form-group">
                <label><?php echo $campo['label']; ?></label>

                <?php if ($campo['type'] === 'select'): ?>
                    <select name="<?php echo $campo['name']; ?>" <?php echo $readOnly; ?>>
                        <option value="">Seleccione</option>

                        <?php
                        if (isset($campo['options'])) {
                            foreach ($campo['options'] as $val => $txt) {
                                $sel = ($datos[$campo['name']] ?? '') == $val ? 'selected' : '';
                                echo "<option value='$val' $sel>$txt</option>";
                            }
                        } else {
                            $q = $db->query("SELECT id, {$campo['display']} FROM {$campo['table']}");
                            while ($r = $q->fetch(PDO::FETCH_ASSOC)) {
                                $sel = ($datos[$campo['name']] ?? '') == $r['id'] ? 'selected' : '';
                                echo "<option value='{$r['id']}' $sel>{$r[$campo['display']]}</option>";
                            }
                        }
                        ?>
                    </select>

                <?php elseif ($campo['type'] === 'textarea'): ?>
                    <textarea name="<?php echo $campo['name']; ?>" <?php echo $readOnly; ?>>
                                <?php echo htmlspecialchars($datos[$campo['name']] ?? ''); ?>
                            </textarea>

                <?php else: ?>
                    <input type="<?php echo $campo['type']; ?>" name="<?php echo $campo['name']; ?>"
                        value="<?php echo htmlspecialchars($datos[$campo['name']] ?? ''); ?>" <?php echo $campo['required'] ?? false ? 'required' : ''; ?>         <?php echo $readOnly; ?>>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($accion !== 'ver'): ?>
        <div class="modal-actions" style="margin-top:20px; text-align:right;">
            <button type="button" class="btn-cancel" onclick="document.getElementById('crudModal').style.display='none'">
                Cancelar
            </button>
            <button type="submit" class="btn-primary">
                <?php echo $accion === 'editar' ? 'Actualizar' : 'Guardar'; ?>
            </button>
        </div>
    <?php endif; ?>
</form>