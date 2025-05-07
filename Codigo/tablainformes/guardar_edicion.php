<?php
// Script para guardar la edición de un documento (solo texto plano por ahora)

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluir archivos necesarios
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/logger.php';

// Establecer la cabecera para responder en JSON
header('Content-Type: application/json');

// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Logger::logError('Acceso no permitido a guardar_edicion.php', ['method' => $_SERVER['REQUEST_METHOD'], 'ip' => $_SERVER['REMOTE_ADDR']]);
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit();
}

// Validar CSRF Token (si se implementa el paso del token desde el cliente)
// if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
//     Logger::logError('Token CSRF inválido en guardar_edicion.php', ['ip' => $_SERVER['REMOTE_ADDR']]);
//     echo json_encode(['success' => false, 'message' => 'Token CSRF inválido.']);
//     exit();
// }

// Validar si se recibieron el ID del documento y el contenido
if (!isset($_POST['id']) || !isset($_POST['contenido'])) {
    Logger::logError('Parámetros incompletos para guardar edición', ['post_keys' => array_keys($_POST), 'ip' => $_SERVER['REMOTE_ADDR']]);
    echo json_encode(['success' => false, 'message' => 'Parámetros incompletos.']);
    exit();
}

$documento_id = intval($_POST['id']); // Obtener el ID del documento y asegurarse de que sea un entero
$contenido = $_POST['contenido']; // Obtener el contenido del editor

// Conectar a la base de datos
$conn = null; // Inicializa la conexión a null

try {
    $conn = new mysqli(DB_SERVERNAME, DB_USERNAME, DB_PASSWORD, DB_NAME);

    // Verificar conexión
    if ($conn->connect_error) {
        Logger::logError('Error de conexión a la base de datos al guardar edición', ['db_error' => $conn->connect_error, 'document_id' => $documento_id, 'ip' => $_SERVER['REMOTE_ADDR']]);
        throw new Exception("Error de conexión a la base de datos.");
    }

    // Consultar la base de datos para obtener la ruta del archivo
    $stmt = $conn->prepare("SELECT ruta_archivo FROM documentos WHERE id = ?");

    if ($stmt === false) {
        Logger::logError('Error al preparar la sentencia SQL para obtener ruta (guardar_edicion)', ['db_error' => $conn->error, 'document_id' => $documento_id, 'ip' => $_SERVER['REMOTE_ADDR']]);
        throw new Exception("Error interno del servidor al preparar la consulta.");
    }

    $stmt->bind_param("i", $documento_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $documento = $result->fetch_assoc();

    $stmt->close(); // Cerrar la sentencia

    // Verificar si se encontró el documento
    if (!$documento) {
        Logger::logError('Documento no encontrado en la base de datos para guardar', ['document_id' => $documento_id, 'ip' => $_SERVER['REMOTE_ADDR']]);
        throw new Exception('El documento a guardar no fue encontrado.');
    }

    $ruta_archivo_bd = $documento['ruta_archivo']; // Ruta relativa almacenada en la BD
    $ruta_archivo_fisica = ROOT_DIR . DIRECTORY_SEPARATOR . $ruta_archivo_bd; // Ruta física completa

     // Validar la ruta física del archivo para prevenir Directory Traversal
    $real_upload_dir = realpath(UPLOAD_DIR); // Directorio real donde se guardan los documentos
    $real_archivo_fisica = realpath($ruta_archivo_fisica); // Ruta real del archivo

    // Verificar que la ruta real del archivo esté dentro del directorio de subida real
    if ($real_upload_dir === false || $real_archivo_fisica === false || strpos($real_archivo_fisica, $real_upload_dir) !== 0) {
         Logger::logError('Intento de acceso a archivo fuera del directorio de subida (guardar_edicion)', [
            'document_id' => $documento_id,
            'ruta_archivo_bd' => $ruta_archivo_bd,
            'ruta_archivo_fisica' => $ruta_archivo_fisica,
            'real_upload_dir' => $real_upload_dir ?? 'N/A',
            'real_archivo_fisica' => $real_archivo_fisica ?? 'N/A',
            'ip' => $_SERVER['REMOTE_ADDR']
        ]);
        throw new Exception('Ruta de archivo inválida.');
    }


    // Guardar el contenido en el archivo físico
    // Usar LOCK_EX para bloquear el archivo mientras se escribe
    if (file_put_contents($ruta_archivo_fisica, $contenido, LOCK_EX) === false) {
        Logger::logError('Error al escribir en el archivo', ['ruta_archivo_fisica' => $ruta_archivo_fisica, 'php_error' => error_get_last(), 'ip' => $_SERVER['REMOTE_ADDR']]);
        throw new Exception('Error al guardar el contenido del archivo.');
    }

    // Opcional: Actualizar la fecha de modificación en la base de datos si tienes una columna para ello
    // $stmt_update = $conn->prepare("UPDATE documentos SET fecha_modificacion = NOW() WHERE id = ?");
    // $stmt_update->bind_param("i", $documento_id);
    // $stmt_update->execute();
    // $stmt_update->close();

    Logger::logOperation('Documento guardado correctamente', ['document_id' => $documento_id, 'ruta_archivo' => $ruta_archivo_bd, 'ip' => $_SERVER['REMOTE_ADDR']]);

    // Responder con éxito en formato JSON
    echo json_encode(['success' => true, 'message' => 'Cambios guardados correctamente.']);

} catch (Exception $e) {
    // Captura cualquier excepción y responde con error en formato JSON
    Logger::logError('Fallo al guardar edición del documento', [
        'document_id' => $documento_id,
        'error_message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'ip' => $_SERVER['REMOTE_ADDR']
    ]);
    echo json_encode(['success' => false, 'message' => 'Error al guardar cambios: ' . $e->getMessage()]);
} finally {
    // Asegura que la conexión a la base de datos se cierre si se abrió y es válida
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
?>
