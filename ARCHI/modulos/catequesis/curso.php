<?php
/**
 * Inserta un nuevo curso en la tabla 'curso'.
 *
 * @param PDO $bd La conexión a la base de datos.
 * @param string $nombre El nombre del curso (ej. "Prebautismal Enero 2026").
 * @param string $duracion La duración o período (ej. "3 sesiones", "6 meses").
 * @param int $id_catequista El ID del catequista responsable (FK a tabla catequista).
 * @param string $observaciones Notas opcionales sobre el curso.
 * @return bool Devuelve true si la inserción fue exitosa, false en caso contrario.
 */
function crearNuevoCurso($bd, $nombre, $duracion, $id_catequista, $observaciones) {
    // 1. Consulta SQL de inserción
    $query = "INSERT INTO curso (nombre, duracion, id_catequista, observaciones) 
              VALUES (?, ?, ?, ?)";
    
    try {
        // 2. Preparar la declaración
        $stmt = $bd->prepare($query);

        // 3. Vincular los parámetros
        $params = [
            $nombre,
            $duracion,
            $id_catequista,
            $observaciones
        ];

        // 4. Ejecutar la consulta
        return $stmt->execute($params);
        
    } catch (PDOException $e) {
        // Manejo de errores (por ejemplo, registro en un log)
        // echo "Error al crear curso: " . $e->getMessage();
        return false;
    }
}

// Ejemplo de uso:
/*
// Asumiendo que $bd es la conexión PDO
// Y que $id_catequista = 1 es válido de la tabla `catequista`
$nombreCurso = "Curso Prebautismal Q4 2026";
$duracionCurso = "3 semanas";
$idCatequista = 1; 
$observacionesCurso = "Curso intensivo para padres y padrinos.";

if (crearNuevoCurso($bd, $nombreCurso, $duracionCurso, $idCatequista, $observacionesCurso)) {
    // echo "Curso creado exitosamente.";
} else {
    // echo "Fallo al crear el curso.";
}
*/
?>