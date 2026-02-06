<?php
/**
 * Inscribe un feligrés a un curso en la tabla 'catequesis'.
 *
 * @param PDO $bd La conexión a la base de datos.
 * @param int $id_feligres ID del feligrés (FK a tabla feligres).
 * @param string $nombre_catequesis Nombre corto del registro (opcional, columna de la DB).
 * @param int $id_curso ID del curso al que se inscribe (FK a tabla curso).
 * @param int $id_parroquia ID de la parroquia asociada (FK a tabla parroquia).
 * @param string $tipo Tipo de catequesis (ENUM: 'Pre-bautismal', 'Primera comunión', 'Confirmación', 'Matrimonial').
 * @return bool Devuelve true si la inserción fue exitosa, false en caso contrario.
 */
function inscribirFeligresACurso($bd, $id_feligres, $nombre_catequesis, $id_curso, $id_parroquia, $tipo) {
    // 1. Consulta SQL de inserción
    $query = "INSERT INTO catequesis (id_feligres, nombre_catequesis, id_curso, id_parroquia, tipo) 
              VALUES (?, ?, ?, ?, ?)";
    
    // Asegurarse de que el tipo coincida con los valores ENUM de la DB
    $tiposValidos = ['Pre-bautismal', 'Primera comunión', 'Confirmación', 'Matrimonial'];
    if (!in_array($tipo, $tiposValidos)) {
        // Manejar tipo inválido si es necesario
        return false;
    }
    
    try {
        // 2. Preparar la declaración
        $stmt = $bd->prepare($query);

        // 3. Vincular los parámetros
        $params = [
            $id_feligres,
            $nombre_catequesis,
            $id_curso,
            $id_parroquia,
            $tipo
        ];

        // 4. Ejecutar la consulta
        return $stmt->execute($params);
        
    } catch (PDOException $e) {
        // Manejo de errores
        // echo "Error al inscribir feligrés: " . $e->getMessage();
        return false;
    }
}
?>