<style>
    /* Estilos básicos para el modal (deberían estar en tu CSS principal) */
    .modal {
        display: none; /* Oculto por defecto */
        position: fixed; 
        z-index: 1000; 
        left: 0;
        top: 0;
        width: 100%; 
        height: 100%; 
        overflow: auto; 
        background-color: rgba(0,0,0,0.4); 
    }
    .modal-content {
        background-color: #fefefe;
        margin: 5% auto; /* 5% desde arriba y centrado */
        padding: 20px;
        border: 1px solid #888;
        width: 80%; /* Podría ser más grande o más pequeño */
        max-width: 600px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }
    .close:hover,
    .close:focus {
        color: #000;
        text-decoration: none;
        cursor: pointer;
    }
    .form-group {
        margin-bottom: 1rem;
    }
    .form-group label {
        display: block;
        margin-bottom: 0.3rem;
        font-weight: 600;
    }
    .form-group input, .form-group select {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }
</style>

<div id="modalAgregar" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('modalAgregar')">&times;</span>
        <h3>➕ Agregar Nuevo Catequista</h3>
        <form action="procesar_agregar.php" method="POST">
            <input type="hidden" name="action" value="create">
            <div class="form-group">
                <label for="nombre_a">Nombre Completo:</label>
                <input type="text" id="nombre_a" name="nombre" required>
            </div>
            <div class="form-group">
                <label for="telefono_a">Teléfono:</label>
                <input type="text" id="telefono_a" name="telefono">
            </div>
            <div class="form-group">
                <label for="especialidad_a">Especialidad:</label>
                <input type="text" id="especialidad_a" name="especialidad" placeholder="Ej: Catequesis Infantil">
            </div>
            <button type="submit" style="padding: 0.75rem 1rem; background-color: #27ae60; color: white; border: none; border-radius: 4px; cursor: pointer;">Guardar Catequista</button>
        </form>
    </div>
</div>

<div id="modalEditar" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('modalEditar')">&times;</span>
        <h3>✏️ Editar Catequista</h3>
        <form id="formEditar" action="procesar_editar.php" method="POST">
            <input type="hidden" name="action" value="update">
            <input type="hidden" id="edit_id" name="id_catequista">
            
            <div class="form-group">
                <label for="nombre_e">Nombre Completo:</label>
                <input type="text" id="nombre_e" name="nombre" required>
            </div>
            <div class="form-group">
                <label for="telefono_e">Teléfono:</label>
                <input type="text" id="telefono_e" name="telefono">
            </div>
            <div class="form-group">
                <label for="especialidad_e">Especialidad:</label>
                <input type="text" id="especialidad_e" name="especialidad">
            </div>
            
            <button type="submit" style="padding: 0.75rem 1rem; background-color: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer;">Guardar Cambios</button>
        </form>
        </div>
</div>

<div id="modalVer" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('modalVer')">&times;</span>
        <h3>👁️ Detalles del Catequista</h3>
        <div id="detallesCatequista">
            <p><strong>ID:</strong> <span id="view_id"></span></p>
            <p><strong>Nombre:</strong> <span id="view_nombre"></span></p>
            <p><strong>Teléfono:</strong> <span id="view_telefono"></span></p>
            <p><strong>Especialidad:</strong> <span id="view_especialidad"></span></p>
            </div>
        <button onclick="closeModal('modalVer')" style="margin-top: 1rem; padding: 0.75rem 1rem; background-color: #f39c12; color: white; border: none; border-radius: 4px; cursor: pointer;">Cerrar</button>
    </div>
</div>

<script>
    // Función genérica para abrir un modal
    function openModal(modalId) {
        document.getElementById(modalId).style.display = 'block';
    }

    // Función genérica para cerrar un modal
    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    // Cerrar el modal al hacer clic fuera de él
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = "none";
        }
    }
</script>