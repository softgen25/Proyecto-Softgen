<?php
// Script para eliminar un documento

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluir archivos necesarios
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/logger.php';

// Variable para la conexión a la base de datos, inicializada a null
$conn = null;

try {
    // 1. Validar parámetros GET (id y ruta)
    if (empty($_GET['id']) || empty($_GET['ruta'])) {
        Logger::logError('Parámetros de eliminación inválidos', ['get' => $_GET, 'ip' => $_SERVER['REMOTE_ADDR']]);
        throw new Exception('Parámetros inválidos para eliminar el documento.');
    }

    // 2. Obtener y sanitizar datos
    $documento_id = (int)$_GET['id']; // Convertir a entero para mayor seguridad
    $ruta_relativa_bd = urldecode($_GET['ruta']); // Decodificar la ruta URL-encoded

    // 3. Construir la ruta física del archivo y normalizar rutas
    $base_dir = realpath(UPLOAD_DIR) . DIRECTORY_SEPARATOR; // Directorio base real de subida
    $ruta_fisica = realpath(UPLOAD_DIR . DIRECTORY_SEPARATOR . $ruta_relativa_bd); // Ruta física real del archivo

    // Debugging (puedes eliminar esto después)
    // error_log("Base dir: " . $base_dir);
    // error_log("Ruta física a eliminar: " . $ruta_fisica);
    // error_log("Ruta relativa de BD: " . $ruta_relativa_bd);


    // 4. Validación de ruta segura para prevenir Directory Traversal
    // Verifica que la ruta física real del archivo esté dentro del directorio base real de subida
    if ($ruta_fisica === false || strpos($ruta_fisica, $base_dir) !== 0) {
        Logger::logError('Intento de acceso a ruta fuera del directorio de subida (eliminacion)', [
            'document_id' => $documento_id,
            'ruta_relativa_bd' => $ruta_relativa_bd,
            'ruta_fisica_intento' => $ruta_fisica ?? null,
            'base_dir' => $base_dir ?? null,
            'ip' => $_SERVER['REMOTE_ADDR']
        ]);
        throw new Exception("Intento de eliminar archivo de una ruta no permitida.");
    }

    // 5. Conexión a la base de datos
    $conn = new mysqli(DB_SERVERNAME, DB_USERNAME, DB_PASSWORD, DB_NAME);

    // Verificar conexión
    if ($conn->connect_error) {
        Logger::logError('Error de conexión a la base de datos al eliminar documento', ['db_error' => $conn->connect_error, 'document_id' => $documento_id, 'ip' => $_SERVER['REMOTE_ADDR']]);
        throw new Exception("Error de conexión a la base de datos.");
    }

    // 6. Iniciar una transacción para asegurar que ambas operaciones (BD y archivo) se completen o ninguna lo haga
    $conn->begin_transaction();

    // 7. Eliminar el registro de la base de datos PRIMERO
    // Prepara la sentencia SQL para eliminar el registro
    $stmt = $conn->prepare("DELETE FROM documentos WHERE id = ?");

    if ($stmt === false) {
         Logger::logError('Error al preparar la sentencia SQL de eliminación', ['db_error' => $conn->error, 'document_id' => $documento_id, 'ip' => $_SERVER['REMOTE_ADDR']]);
        throw new Exception("Error interno del servidor al preparar la eliminación.");
    }

    $stmt->bind_param("i", $documento_id);

    if (!$stmt->execute()) {
        // Si falla la ejecución, revertir la transacción
        $conn->rollback();
        Logger::logError('Error al ejecutar la sentencia SQL de eliminación', ['db_error' => $stmt->error, 'document_id' => $documento_id, 'ip' => $_SERVER['REMOTE_ADDR']]);
        throw new Exception("Error al eliminar el registro del documento de la base de datos.");
    }

    // Verificar si se eliminó exactamente una fila (el documento con ese ID)
    if ($stmt->affected_rows === 0) {
         // Si no se eliminó ninguna fila, el documento no existía en la BD con ese ID
         $conn->rollback();
         Logger::logError('Intento de eliminar documento no existente en BD', ['document_id' => $documento_id, 'ip' => $_SERVER['REMOTE_ADDR']]);
         // Podrías decidir si esto es un error o simplemente que alguien intentó eliminar algo que ya no estaba
         // Por ahora, lo tratamos como un error para alertar
         throw new Exception("El documento no fue encontrado en la base de datos.");
    }

    $stmt->close(); // Cerrar la sentencia

    // 8. Eliminar el archivo físico
    // Verificar si el archivo físico existe antes de intentar eliminarlo
    if (file_exists($ruta_fisica)) {
        if (!unlink($ruta_fisica)) {
            // Si falla la eliminación del archivo físico, revertir la transacción de la BD
            $conn->rollback();
            Logger::logError('No se pudo eliminar el archivo físico', ['ruta_fisica' => $ruta_fisica, 'php_error' => error_get_last(), 'document_id' => $documento_id, 'ip' => $_SERVER['REMOTE_ADDR']]);
            throw new Exception("No se pudo eliminar el archivo físico.");
        }
    } else {
        // Si el archivo físico no existe, registrar una advertencia pero continuar si el registro de BD se eliminó
        // Esto puede pasar si el archivo fue eliminado manualmente o hubo un error previo
        Logger::logOperation("Advertencia: Archivo físico no encontrado al intentar eliminar", ['ruta_fisica' => $ruta_fisica, 'document_id' => $documento_id, 'ip' => $_SERVER['REMOTE_ADDR']]);
        // No lanzamos una excepción aquí porque el registro de BD ya se eliminó exitosamente
    }

    // 9. Si ambas operaciones (BD y archivo) fueron exitosas, confirmar la transacción
    $conn->commit();

    Logger::logOperation('Documento eliminado correctamente', ['document_id' => $documento_id, 'ruta_archivo' => $ruta_relativa_bd, 'ip' => $_SERVER['REMOTE_ADDR']]);

    // Establecer un mensaje de éxito en la sesión
    $_SESSION['alert'] = [
        'type' => 'success',
        'message' => 'Documento eliminado correctamente'
    ];

} catch (Exception $e) {
    // Captura cualquier excepción y establece un mensaje de error
    // Si la conexión se abrió y hubo un error después de begin_transaction, el rollback ya se hizo
    // Si el error ocurrió antes de begin_transaction, no hay transacción que revertir
    Logger::logError('Fallo al eliminar documento', [
        'document_id' => $documento_id,
        'error_message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'ip' => $_SERVER['REMOTE_ADDR']
    ]);
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Error al eliminar el documento: ' . $e->getMessage()];
} finally {
    // Asegura que la conexión a la base de datos se cierre si se abrió y es válida
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
    // Redirige siempre al usuario a la página de informes después de procesar la eliminación
    header('Location: tablainformes.php');
    exit(); // Asegura que el script se detenga después de la redirección
}
?>
