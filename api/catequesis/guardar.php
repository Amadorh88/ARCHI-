<?php
// guardar.php - Gestión de inscripciones de catequesis
declare(strict_types=1);

require '../../config/db.php';
header('Content-Type: application/json; charset=utf-8');

try {

    $db = (new Database())->getConnection();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ===============================
    // 1️⃣ SANITIZACIÓN Y VALIDACIÓN
    // ===============================

    $id_catequesis = filter_input(INPUT_POST, 'id_catequesis', FILTER_VALIDATE_INT);
    $id_feligres   = filter_input(INPUT_POST, 'id_feligres', FILTER_VALIDATE_INT);
    $id_curso      = filter_input(INPUT_POST, 'id_curso', FILTER_VALIDATE_INT);
    $id_parroquia  = filter_input(INPUT_POST, 'id_parroquia', FILTER_VALIDATE_INT);
    $id_periodo    = filter_input(INPUT_POST, 'id_periodo', FILTER_VALIDATE_INT);
    
    // Campos para pago
    $concepto      = trim($_POST['concepto'] ?? '');
    $cantidad      = filter_input(INPUT_POST, 'cantidad', FILTER_VALIDATE_FLOAT);
    $recibido      = filter_input(INPUT_POST, 'recibido', FILTER_VALIDATE_FLOAT);
    $cambio        = filter_input(INPUT_POST, 'cambio', FILTER_VALIDATE_FLOAT);
    $registrar_pago = filter_input(INPUT_POST, 'registrar_pago', FILTER_VALIDATE_INT) ?? 0;
    
    // Campos de estipendio desde el frontend
    $id_estipendio   = filter_input(INPUT_POST, 'id_estipendio', FILTER_VALIDATE_INT);
    $monto_estipendio = filter_input(INPUT_POST, 'monto_estipendio', FILTER_VALIDATE_FLOAT);

    $tipo = trim($_POST['tipo'] ?? '');
    $estado = 0;

    $tiposValidos = [
        'Bautismal',
        'Primera comunión',
        'Confirmación',
        'Matrimonial'
    ];

    if (!$id_feligres || !$id_periodo || !$id_parroquia || empty($tipo)) {
        throw new Exception('Campos obligatorios incompletos.');
    }

    if (!in_array($tipo, $tiposValidos, true)) {
        throw new Exception('Tipo de catequesis inválido.');
    }

    // ===============================
    // 2️⃣ VALIDAR EXISTENCIA FELIGRÉS
    // ===============================

    $stmt = $db->prepare("SELECT nombre_completo FROM feligres WHERE id_feligres = ?");
    $stmt->execute([$id_feligres]);
    $feligres = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$feligres) {
        throw new Exception('El feligrés seleccionado no existe.');
    }

    // ===============================
    // 3️⃣ VALIDAR PERIODO ACTIVO
    // ===============================

    $stmt = $db->prepare("
        SELECT COUNT(*) 
        FROM periodo 
        WHERE id_periodo = ? 
        AND estado = 'activo'
    ");
    $stmt->execute([$id_periodo]);

    if ($stmt->fetchColumn() == 0) {
        throw new Exception('El período no existe o no está activo.');
    }

    // ===============================
    // 4️⃣ VALIDAR QUE EL CURSO COINCIDA CON EL TIPO DE CATEQUESIS
    // ===============================

    if (!empty($id_curso)) {
        // Obtener el nombre del curso
        $stmt = $db->prepare("SELECT nombre FROM curso WHERE id_curso = ?");
        $stmt->execute([$id_curso]);
        $nombreCurso = $stmt->fetchColumn();

        if (!$nombreCurso) {
            throw new Exception('El curso seleccionado no existe.');
        }

        // Mapeo de tipos de catequesis a palabras clave
        $mapeoCursos = [
            'Bautismal' => ['bautismal', 'bautismo', 'baut', 'pre-bautismal', 'prebautismal'],
            'Primera comunión' => ['primera comunión', 'primera comunion', 'comunión', 'comunion', 'comun', 'com'],
            'Confirmación' => ['confirmación', 'confirmacion', 'confirm', 'conf'],
            'Matrimonial' => ['matrimonial', 'matrimonio', 'matri', 'mat']
        ];

        $palabrasClave = $mapeoCursos[$tipo] ?? [];
        $nombreCursoLower = strtolower($nombreCurso);
        $cursoValido = false;

        foreach ($palabrasClave as $clave) {
            if (strpos($nombreCursoLower, $clave) !== false) {
                $cursoValido = true;
                break;
            }
        }

        if (!$cursoValido) {
            throw new Exception("El curso '{$nombreCurso}' no corresponde al tipo de catequesis '{$tipo}'. Por favor, seleccione un curso válido.");
        }
    }

    // ===============================
    // 5️⃣ EVITAR DUPLICAR CATEQUESIS ACTIVA
    // ===============================

    if (!$id_catequesis) {
        $stmt = $db->prepare("
            SELECT COUNT(*) 
            FROM catequesis
            WHERE id_feligres = ?
            AND tipo = ?
            AND id_periodo = ?
        ");

        $stmt->execute([$id_feligres, $tipo, $id_periodo]);

        if ($stmt->fetchColumn() > 0) {
            throw new Exception('El feligrés ya está inscrito en esta catequesis en el período actual.');
        }
    }

    // ===============================
    // 6️⃣ VALIDAR JERARQUÍA SACRAMENTAL REAL
    // ===============================

    // Bautismo requerido para todo lo demás
    if ($tipo !== 'Bautismal') {
        $stmt = $db->prepare("SELECT COUNT(*) FROM bautismo WHERE id_feligres = ?");
        $stmt->execute([$id_feligres]);

        if ($stmt->fetchColumn() == 0) {
            throw new Exception('El feligrés debe tener registrado el Bautismo antes de continuar.');
        }
    }

    // Comunión antes de Confirmación
    if ($tipo === 'Confirmación') {
        $stmt = $db->prepare("SELECT COUNT(*) FROM comunion WHERE id_feligres = ?");
        $stmt->execute([$id_feligres]);

        if ($stmt->fetchColumn() == 0) {
            throw new Exception('El feligrés debe haber recibido la Primera Comunión antes de la Confirmación.');
        }
    }

    // Confirmación antes de Matrimonio
    if ($tipo === 'Matrimonial') {
        $stmt = $db->prepare("SELECT COUNT(*) FROM confirmacion WHERE id_feligres = ?");
        $stmt->execute([$id_feligres]);

        if ($stmt->fetchColumn() == 0) {
            throw new Exception('El feligrés debe estar Confirmado antes de la Catequesis Matrimonial.');
        }
    }

    // ===============================
    // 7️⃣ VERIFICAR PAGO/ESTIPENDIO (si es nueva inscripción)
    // ===============================

    $pago_registrado = false;
    $id_pago = null;

    if (!$id_catequesis) {
        // Verificar si se envió un estipendio ya registrado desde el frontend
        if (!empty($id_estipendio) && $monto_estipendio > 0) {
            // Verificar que el pago exista y pertenezca al feligrés
            $stmt = $db->prepare("
                SELECT id_pago, cantidad 
                FROM pago 
                WHERE id_pago = ? AND id_feligres = ?
            ");
            $stmt->execute([$id_estipendio, $id_feligres]);
            $pagoExistente = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($pagoExistente) {
                $pago_registrado = true;
                $id_pago = $id_estipendio;
                $monto_estipendio = $pagoExistente['cantidad'];
            } else {
                throw new Exception('El estipendio registrado no es válido o no pertenece a este feligrés.');
            }
        } 
        // Si no se envió estipendio, verificar si se requiere y se está registrando uno nuevo
        else if ($registrar_pago == 1 && !empty($concepto) && $cantidad > 0) {
            // Verificar si ya existe un pago reciente para este feligrés y tipo de catequesis
            $stmt = $db->prepare("
                SELECT COUNT(*) 
                FROM pago 
                WHERE id_feligres = ? 
                AND concepto LIKE ?
                AND DATE(fecha) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            ");
            
            $conceptoBusqueda = '%Catequesis ' . $tipo . '%';
            $stmt->execute([$id_feligres, $conceptoBusqueda]);
            
            if ($stmt->fetchColumn() > 0) {
                throw new Exception('Ya existe un pago registrado para esta catequesis en los últimos 30 días.');
            }
            
            // El pago se registrará dentro de la transacción
            $registrar_nuevo_pago = true;
        } 
        // Si es nueva inscripción y no hay estipendio, lanzar error
        else {
            throw new Exception('Debe registrar el estipendio para completar la inscripción.');
        }
    }

    // ===============================
    // 8️⃣ TRANSACCIÓN PRINCIPAL
    // ===============================

    $db->beginTransaction();

    // Registrar nuevo pago si corresponde
    if (isset($registrar_nuevo_pago) && $registrar_nuevo_pago === true) {
        $stmt = $db->prepare("
            INSERT INTO pago 
            (id_feligres, concepto, cantidad, recibido, cambio, fecha)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute([
            $id_feligres,
            $concepto,
            $cantidad,
            $recibido ?? $cantidad,
            $cambio ?? 0
        ]);
        
        $id_pago = $db->lastInsertId();
        $pago_registrado = true;
        $monto_estipendio = $cantidad;
    }

    // Guardar o actualizar catequesis
    if (!$id_catequesis) {
        // Nueva inscripción
        $stmt = $db->prepare("
            INSERT INTO catequesis
            (id_feligres, id_curso, id_parroquia, id_periodo, tipo, id_pago, monto_pago)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $id_feligres,
            $id_curso ?: null,
            $id_parroquia,
            $id_periodo,
            $tipo,
            $id_pago,
            $monto_estipendio ?? 0
        ]);

        $mensaje = 'Inscripción registrada correctamente.';
        if ($pago_registrado) {
            $mensaje .= ' Estipendio registrado.';
        }
    } else {
        // Actualización de inscripción existente
        $stmt = $db->prepare("
            UPDATE catequesis SET
                estado = ?,
                id_curso = ?,
                id_parroquia = ?,
                id_periodo = ?,
                tipo = ?
            WHERE id_catequesis = ?
        ");

        $stmt->execute([
            $estado,
            $id_curso ?: null,
            $id_parroquia,
            $id_periodo,
            $tipo,
            $id_catequesis
        ]);

        $mensaje = 'Inscripción actualizada correctamente.';
    }

    $db->commit();

    // ===============================
    // 9️⃣ RESPUESTA EXITOSA
    // ===============================

    echo json_encode([
        'success' => true,
        'message' => $mensaje,
        'pago_registrado' => $pago_registrado,
        'id_pago' => $id_pago,
        'monto_pago' => $monto_estipendio ?? 0,
        'tipo' => $tipo,
        'id_feligres' => $id_feligres,
        'nombre_feligres' => $feligres['nombre_completo']
    ]);

} catch (Throwable $e) {

    // ===============================
    // 🔴 MANEJO DE ERRORES
    // ===============================

    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }

    // Log del error (opcional)
    error_log("Error en guardar.php: " . $e->getMessage());

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>