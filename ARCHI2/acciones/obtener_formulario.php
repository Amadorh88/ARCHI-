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
            ['label'=>'Nombre','name'=>'nombre','type'=>'text','required'=>true],
            ['label'=>'DNI','name'=>'dni','type'=>'text','required'=>true],
            ['label'=>'Usuario','name'=>'usuario','type'=>'text','required'=>true],
            ['label'=>'Contraseña','name'=>'contraseña','type'=>'password','required'=>($accion==='crear')],
            [
                'label'=>'Rol','name'=>'rol','type'=>'select',
                'options'=>[
                    'admin'=>'Administrador',
                    'secretario'=>'Secretario',
                    'archivista'=>'Archivista',
                    'parroco'=>'Párroco'
                ]
            ],
            [
                'label'=>'Estado','name'=>'estado','type'=>'select',
                'options'=>['1'=>'Activo','0'=>'Inactivo']
            ]
        ];
        break;

    case 'feligres':
        $tabla = 'feligres';
        $pk = 'id_feligres';
        $campos = [
            ['label'=>'Nombre Completo','name'=>'nombre_completo','type'=>'text','required'=>true],
            ['label'=>'Nombre del Padre','name'=>'nombre_padre','type'=>'text'],
            ['label'=>'Nombre de la Madre','name'=>'nombre_madre','type'=>'text'],
            ['label'=>'Fecha de Nacimiento','name'=>'fecha_nacimiento','type'=>'date'],
            ['label'=>'Lugar de Nacimiento','name'=>'lugar_nacimiento','type'=>'text']
        ];
        break;

    case 'bautismo':
    case 'comunion':
    case 'confirmacion':
        $tabla = $modulo;
        $pk = "id_$modulo";
        $campos = [
            ['label'=>'Registro','name'=>'registro','type'=>'text'],
            [
                'label'=>'Feligres','name'=>'id_feligres','type'=>'select',
                'table'=>'feligres','pk'=>'id_feligres','display'=>'nombre_completo'
            ],
            ['label'=>'Fecha','name'=>'fecha','type'=>'date'],
            [
                'label'=>'Ministro','name'=>'id_ministro','type'=>'select',
                'table'=>'ministros','pk'=>'id_ministro','display'=>'nombre_completo'
            ],
            [
                'label'=>'Parroquia','name'=>'id_parroquia','type'=>'select',
                'table'=>'parroquia','pk'=>'id_parroquia','display'=>'nombre'
            ]
        ];
        break;

    case 'catequista':
        $tabla = 'catequista';
        $pk = 'id_catequista';
        $campos = [
            ['label'=>'Nombre','name'=>'nombre','type'=>'text','required'=>true],
            ['label'=>'Teléfono','name'=>'telefono','type'=>'text'],
            ['label'=>'Especialidad','name'=>'especialidad','type'=>'text']
        ];
        break;

    case 'parroquia':
        $tabla = 'parroquia';
        $pk = 'id_parroquia';
        $campos = [
            ['label'=>'Nombre','name'=>'nombre','type'=>'text','required'=>true],
            ['label'=>'Dirección','name'=>'direccion','type'=>'text'],
            ['label'=>'Teléfono','name'=>'telefono','type'=>'text']
        ];
        break;

    case 'ministros':
        $tabla = 'ministros';
        $pk = 'id_ministro';
        $campos = [
            ['label'=>'Nombre','name'=>'nombre_completo','type'=>'text','required'=>true],
            ['label'=>'DIP','name'=>'DIP','type'=>'text'],
            ['label'=>'Teléfono','name'=>'telefono','type'=>'text'],
            [
                'label'=>'Tipo','name'=>'tipo','type'=>'select',
                'options'=>[
                    'sacerdote'=>'Sacerdote',
                    'diácono'=>'Diácono',
                    'obispo'=>'Obispo',
                    'catequista'=>'Catequista'
                ]
            ]
        ];
        break;

    case 'pago':
        $tabla = 'pago';
        $pk = 'id_pago';
        $campos = [
            ['label'=>'Concepto','name'=>'concepto','type'=>'text','required'=>true],
            ['label'=>'Cantidad','name'=>'cantidad','type'=>'number'],
           /*  ['label'=>'Monto Recibido','name'=>'recibido','type'=>'number'],
            ['label'=>'Cambio','name'=>'cambio','type'=>'number'], */
            [
                'label'=>'Feligres','name'=>'id_feligres','type'=>'select',
                'table'=>'feligres','pk'=>'id_feligres','display'=>'nombre_completo'
            ]
        ];
        break;
        case 'matrimonio':
            $tabla = 'matrimonio';
            $pk = 'id_matrimonio';
        
            $campos = [
                [
                    'label' => 'Registro',
                    'name'  => 'registro',
                    'type'  => 'text',
                    'required' => true
                ],
                [
                    'label' => 'Fecha',
                    'name'  => 'fecha',
                    'type'  => 'date',
                    'required' => true
                ],
                [
                    'label' => 'Ministro',
                    'name'  => 'id_ministro',
                    'type'  => 'select',
                    'table' => 'ministros',
                    'pk'    => 'id_ministro',
                    'display' => 'nombre_completo',
                    'required' => true
                ],
                [
                    'label' => 'Lugar',
                    'name'  => 'lugar',
                    'type'  => 'text'
                ],
        
                // ========= FELIGRESES =========
                [
                    'label' => 'Esposo',
                    'name'  => 'esposo',
                    'type'  => 'select',
                    'table' => 'feligres',
                    'pk'    => 'id_feligres',
                    'display' => 'nombre_completo',
                    'required' => true
                ],
                [
                    'label' => 'Esposa',
                    'name'  => 'esposa',
                    'type'  => 'select',
                    'table' => 'feligres',
                    'pk'    => 'id_feligres',
                    'display' => 'nombre_completo',
                    'required' => true
                ],
                [
                    'label' => 'Testigo 1',
                    'name'  => 'testigo1',
                    'type'  => 'select',
                    'table' => 'feligres',
                    'pk'    => 'id_feligres',
                    'display' => 'nombre_completo',
                    'required' => true
                ],
                [
                    'label' => 'Testigo 2',
                    'name'  => 'testigo2',
                    'type'  => 'select',
                    'table' => 'feligres',
                    'pk'    => 'id_feligres',
                    'display' => 'nombre_completo',
                    'required' => true
                ]
            ];
            break;
        
    default:
        echo "<div style='color:red;'>Módulo no soportado</div>";
        exit;
}

/* =========================
   CARGAR DATOS
========================= */
if ($id && in_array($accion, ['editar','ver'])) {
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
    <h3><?= ucfirst($accion).' '.ucfirst($modulo) ?></h3>
    <button class="modal-close-btn" onclick="crudModal.style.display='none'">&times;</button>
</div>

<form method="POST" action="api/guardar.php">
    <input type="hidden" name="modulo" value="<?= $modulo ?>">
    <input type="hidden" name="accion" value="<?= $accion ?>">
    <input type="hidden" name="id" value="<?= $id ?>">

    <div class="form-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
        <?php foreach ($campos as $campo): ?>
            <div class="form-group">
                <label><?= $campo['label'] ?></label>

                <?php if ($campo['type'] === 'select'): ?>
                    <select name="<?= $campo['name'] ?>" <?= $readOnly ?>>
                        <option value="">Seleccione</option>
                        <?php
                        if (isset($campo['options'])) {
                            foreach ($campo['options'] as $val=>$txt) {
                                $sel = (($datos[$campo['name']] ?? '') == $val) ? 'selected' : '';
                                echo "<option value='$val' $sel>$txt</option>";
                            }
                        } else {
                            $pkSel = $campo['pk'] ?? 'id';
                            $sql = "SELECT $pkSel, {$campo['display']} 
                                    FROM {$campo['table']} 
                                    ORDER BY {$campo['display']} ASC";
                            $q = $db->query($sql);
                            while ($r = $q->fetch(PDO::FETCH_ASSOC)) {
                                $sel = (($datos[$campo['name']] ?? '') == $r[$pkSel]) ? 'selected' : '';
                                echo "<option value='{$r[$pkSel]}' $sel>{$r[$campo['display']]}</option>";
                            }
                        }
                        ?>
                    </select>

                <?php else: ?>
                    <input type="<?= $campo['type'] ?>"
                           name="<?= $campo['name'] ?>"
                           value="<?= htmlspecialchars($datos[$campo['name']] ?? '') ?>"
                           <?= !empty($campo['required']) ? 'required' : '' ?>
                           <?= $readOnly ?>>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($accion !== 'ver'): ?>
        <div class="modal-actions" style="margin-top:20px;text-align:right;">
            <button type="button" onclick="crudModal.style.display='none'">Cancelar</button>
            <button type="submit"><?= $accion === 'editar' ? 'Actualizar' : 'Guardar' ?></button>
        </div>
    <?php endif; ?>
</form>
