<?php
// Recibimos los parámetros de la URL
$modulo = $_GET['modulo'] ?? '';
$accion = $_GET['accion'] ?? '';
$id = $_GET['id'] ?? null;
$datos = [];

// Si es editar o ver, cargamos los datos actuales del usuario
if ($id && ($accion == 'editar' || $accion == 'ver')) {
    // Nota: Asegúrate de que la variable $bd (PDO) esté disponible en este scope
    $stmt = $bd->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$id]);
    $datos = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Definimos si los campos serán de solo lectura
$readOnly = ($accion == 'ver') ? 'readonly disabled' : '';
?>

<div class="modal-header">
    <h3><?php echo ucfirst($accion); ?> <?php echo ucfirst($modulo); ?></h3>
    <button class="modal-close-btn" onclick="document.getElementById('crudModal').style.display='none'">&times;</button>
</div>

<form id="formCRUD" onsubmit="guardarCambios(event)">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <input type="hidden" name="modulo" value="<?php echo $modulo; ?>">
    <input type="hidden" name="accion" value="<?php echo $accion; ?>">

    <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
        
        <div class="form-group">
            <label>Nombre Completo</label>
            <input type="text" name="nombre" value="<?php echo $datos['nombre'] ?? ''; ?>" required <?php echo $readOnly; ?>>
        </div>

        <div class="form-group">
            <label>DNI / DIP</label>
            <input type="text" name="dni" value="<?php echo $datos['dni'] ?? ''; ?>" required <?php echo $readOnly; ?>>
        </div>

        <div class="form-group">
            <label>Nombre de Usuario</label>
            <input type="text" name="usuario" value="<?php echo $datos['usuario'] ?? ''; ?>" required <?php echo $readOnly; ?>>
        </div>

        <div class="form-group">
            <label>Contraseña <?php echo ($accion == 'editar') ? '(Dejar en blanco para no cambiar)' : ''; ?></label>
            <input type="password" name="contraseña" <?php echo ($accion == 'nuevo') ? 'required' : ''; ?> <?php echo $readOnly; ?>>
        </div>

        <div class="form-group">
            <label>Rol de Usuario</label>
            <select name="rol" required <?php echo ($accion == 'ver') ? 'disabled' : ''; ?>>
                <option value="">Seleccione un rol</option>
                <option value="admin" <?php echo (isset($datos['rol']) && $datos['rol'] == 'admin') ? 'selected' : ''; ?>>Administrador</option>
                <option value="secretario" <?php echo (isset($datos['rol']) && $datos['rol'] == 'secretario') ? 'selected' : ''; ?>>Secretario</option>
                <option value="archivista" <?php echo (isset($datos['rol']) && $datos['rol'] == 'archivista') ? 'selected' : ''; ?>>Archivista</option>
                <option value="parroco" <?php echo (isset($datos['rol']) && $datos['rol'] == 'parroco') ? 'selected' : ''; ?>>Párroco</option>
            </select>
        </div>

        <div class="form-group">
            <label>Estado</label>
            <select name="estado" required <?php echo ($accion == 'ver') ? 'disabled' : ''; ?>>
                <option value="1" <?php echo (!isset($datos['estado']) || $datos['estado'] == 1) ? 'selected' : ''; ?>>Activo</option>
                <option value="0" <?php echo (isset($datos['estado']) && $datos['estado'] == 0) ? 'selected' : ''; ?>>Inactivo</option>
            </select>
        </div>

    </div>

    <?php if ($accion != 'ver'): ?>
    <div class="modal-actions" style="margin-top: 20px; text-align: right;">
        <button type="button" class="btn-cancel" onclick="document.getElementById('crudModal').style.display='none'">Cancelar</button>
        <button type="submit" class="btn-primary">
            <?php echo ($accion == 'editar') ? 'Actualizar' : 'Guardar'; ?>
        </button>
    </div>
    <?php endif; ?>
</form>