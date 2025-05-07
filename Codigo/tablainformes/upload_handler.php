<?php
// Manejador de subida de archivos

// Incluir archivo de configuración
require_once __DIR__ . '/config.php';
// Incluir archivo del logger
require_once __DIR__ . '/logger.php';

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generar token CSRF si no existe (se usa en el formulario en tablainformes.php)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


// Habilitar reporte de errores para depuración (desactivar en producción)
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Registrar información de depuración al inicio
// Logger::logOperation("Inicio de upload_handler.php", [
//     'method' => $_SERVER['REQUEST_METHOD'],
//     'files' => $_FILES,
//     'post' => $_POST,
//     'session_csrf' => $_SESSION['csrf_token'] ?? 'N/A',
//     'post_csrf' => $_POST['csrf_token'] ?? 'N/A'
// ]);

// Variable para la conexión a la base de datos, inicializada a null
$conn = null;

try {
    // 1. Validar método POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    // 2. Validar CSRF Token para proteger contra ataques de falsificación de solicitud entre sitios
    // Compara el token enviado en el formulario con el token almacenado en la sesión
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        // Registra el intento de CSRF
        Logger::logError('Intento de CSRF detectado', [
            'session_token' => $_SESSION['csrf_token'] ?? 'N/A',
            'post_token' => $_POST['csrf_token'] ?? 'N/A',
            'ip' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'N/A'
        ]);
        throw new Exception('Token CSRF inválido');
    }

    // 3. Validar si se subió un archivo y si no hubo errores en la subida inicial
    if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
         // Registra el error de subida reportado por PHP
        $upload_errors = [
            UPLOAD_ERR_INI_SIZE => 'El archivo subido excede la directiva upload_max_filesize en php.ini.',
            UPLOAD_ERR_FORM_SIZE => 'El archivo subido excede la directiva MAX_FILE_SIZE especificada en el formulario HTML.',
            UPLOAD_ERR_PARTIAL => 'El archivo subido fue sólo parcialmente subido.',
            UPLOAD_ERR_NO_FILE => 'No se subió ningún archivo.',
            UPLOAD_ERR_NO_TMP_DIR => 'Falta una carpeta temporal.',
            UPLOAD_ERR_CANT_WRITE => 'No se pudo escribir el archivo en el disco.',
            UPLOAD_ERR_EXTENSION => 'Una extensión de PHP detuvo la subida del archivo.'
        ];
        $error_message = $upload_errors[$_FILES['archivo']['error']] ?? 'Error de subida desconocido';
        Logger::logError('Error en la subida inicial del archivo', [
            'php_error_code' => $_FILES['archivo']['error'],
            'error_message' => $error_message,
            'file_name' => $_FILES['archivo']['name'] ?? 'N/A'
        ]);
        throw new Exception('Error en la subida del archivo: ' . $error_message);
    }

    // 4. Validar tamaño máximo del archivo subido usando la constante de config.php
    if ($_FILES['archivo']['size'] > MAX_FILE_SIZE) {
        Logger::logError('Archivo excede el tamaño máximo permitido', [
            'file_name' => $_FILES['archivo']['name'],
            'file_size' => $_FILES['archivo']['size'],
            'max_size' => MAX_FILE_SIZE
        ]);
        throw new Exception('El archivo excede el tamaño máximo permitido (' . (MAX_FILE_SIZE / 1024 / 1024) . 'MB)');
    }

    // 5. Validar el tipo MIME real del archivo para evitar la subida de archivos maliciosos renombrados
    $finfo = finfo_open(FILEINFO_MIME_TYPE); // Abre el recurso Fileinfo
    if ($finfo === false) {
        Logger::logError('No se pudo abrir el recurso finfo', ['error' => error_get_last()]);
        throw new Exception('Error interno del servidor al verificar el tipo de archivo.');
    }
    $mime = finfo_file($finfo, $_FILES['archivo']['tmp_name']); // Obtiene el tipo MIME del archivo temporal
    finfo_close($finfo); // Cierra el recurso Fileinfo

    // Verifica si el tipo MIME obtenido está en la lista de tipos permitidos usando la constante de config.php
    if (!in_array($mime, ALLOWED_MIME_TYPES)) {
        Logger::logError('Tipo de archivo no permitido', [
            'file_name' => $_FILES['archivo']['name'],
            'mime_type' => $mime,
            'allowed_mimes' => ALLOWED_MIME_TYPES
        ]);
        throw new Exception('Tipo de archivo no permitido: ' . htmlspecialchars($mime));
    }

    // 6. Sanitizar y generar un nombre de archivo único y seguro
    $extension = strtolower(pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION)); // Obtiene la extensión original
    // Genera un nombre único basado en un ID único y el nombre original (limpiado)
    // Elimina caracteres no permitidos del nombre original para mayor seguridad
    $nombre_original_limpio = preg_replace('/[^a-zA-Z0-9-_\.]/', '', basename($_FILES['archivo']['name'], ".$extension"));
    $nombre_archivo_seguro = uniqid() . '_' . $nombre_original_limpio . "." . $extension;

    // Define la ruta completa de destino para el archivo usando la constante de config.php
    $target_file = UPLOAD_DIR . DIRECTORY_SEPARATOR . $nombre_archivo_seguro;

    // 7. Validar la ruta de destino para prevenir ataques de Directory Traversal
    // Realpath resuelve rutas simbólicas y puntos (..)
    $real_upload_dir = realpath(UPLOAD_DIR);
    $real_target_file_dir = realpath(dirname($target_file));

    // Verifica que el directorio real de destino sea el directorio de subida real o un subdirectorio seguro
    // strpos === 0 asegura que $real_target_file_dir comienza con $real_upload_dir
    if ($real_upload_dir === false || $real_target_file_dir === false || strpos($real_target_file_dir, $real_upload_dir) !== 0) {
        Logger::logError('Intento de Directory Traversal detectado', [
            'original_target' => $target_file,
            'real_upload_dir' => $real_upload_dir ?? 'N/A',
            'real_target_dir' => $real_target_file_dir ?? 'N/A',
            'ip' => $_SERVER['REMOTE_ADDR']
        ]);
        throw new Exception('Ruta de archivo de destino inválida.');
    }

    // 8. Mover el archivo subido desde la ubicación temporal a la ubicación de destino segura
    if (!move_uploaded_file($_FILES['archivo']['tmp_name'], $target_file)) {
        Logger::logError('Error al mover el archivo subido', [
            'tmp_name' => $_FILES['archivo']['tmp_name'],
            'target_file' => $target_file,
            'php_error' => error_get_last()
        ]);
        throw new Exception('Error al mover el archivo subido.');
    }

    // 9. Guardar la información del documento en la base de datos
    // Establece la conexión a la base de datos utilizando las constantes de config.php
    $conn = new mysqli(DB_SERVERNAME, DB_USERNAME, DB_PASSWORD, DB_NAME);

    // Verifica si la conexión fue exitosa
    if ($conn->connect_error) {
        // Registra el error de conexión
        Logger::logError('Error de conexión a la base de datos', ['db_error' => $conn->connect_error]);
        // Intenta eliminar el archivo subido si la conexión a la BD falla
        if (file_exists($target_file)) {
            unlink($target_file);
        }
        throw new Exception("Error de conexión a la base de datos.");
    }

    // Prepara la sentencia SQL para insertar el nuevo documento
    // Se asume que 'id_usuario' viene de la sesión del usuario autenticado
    // Si no tienes un sistema de autenticación, podrías omitir id_usuario o manejarlo de otra manera
    // Asegúrate de que la columna 'id_usuario' existe en tu tabla 'documentos' y permite NULL si es necesario
    $stmt = $conn->prepare("INSERT INTO documentos (nombre, ruta_archivo, fecha_subida, id_usuario) VALUES (?, ?, NOW(), ?)");

    // Verifica si la preparación de la sentencia fue exitosa
    if ($stmt === false) {
        Logger::logError('Error al preparar la sentencia SQL de inserción', ['db_error' => $conn->error]);
          // Intenta eliminar el archivo subido si la preparación de la sentencia falla
        if (file_exists($target_file)) {
            unlink($target_file);
        }
        throw new Exception("Error interno del servidor al preparar la inserción.");
    }

    // Obtiene el ID del usuario de la sesión (ajusta según tu sistema de autenticación)
    // Si no hay usuario autenticado, podrías usar NULL o un valor por defecto
    $id_usuario = $_SESSION['id_usuario'] ?? NULL; // Asumiendo que el ID del usuario se guarda en $_SESSION['id_usuario']

    // Vincula los parámetros a la sentencia preparada
    // 's' para string (nombre, ruta_archivo), 'i' para integer (id_usuario, si no es NULL)
    // Si id_usuario puede ser NULL, ajusta el tipo a 's' y maneja el valor NULL adecuadamente en la base de datos (permite NULL en la columna)
    // Si id_usuario es siempre un entero, usa 'i'. Si puede ser NULL, la base de datos debe permitir NULL y bind_param puede necesitar un ajuste si se usa NULL.
     $stmt->bind_param("ssi", $_POST['nombre'], $nombre_archivo_seguro, $id_usuario);


    // Ejecuta la sentencia preparada
    if (!$stmt->execute()) {
        // Registra el error de ejecución de la sentencia
        Logger::logError('Error al ejecutar la sentencia SQL de inserción', ['db_error' => $stmt->error, 'nombre' => $_POST['nombre'], 'ruta' => $nombre_archivo_seguro]);
        // Elimina el archivo subido si falla la inserción en la base de datos
        if (file_exists($target_file)) {
            unlink($target_file);
        }
        throw new Exception("Error al guardar la información del documento en la base de datos.");
    }

    // Cierra la sentencia
    $stmt->close();
    // La conexión a la base de datos se cerrará en el bloque finally

    // Registra la operación exitosa
    Logger::logOperation('Documento subido correctamente', [
        'nombre' => $_POST['nombre'],
        'ruta_archivo' => $nombre_archivo_seguro,
        'id_usuario' => $id_usuario,
        'ip' => $_SERVER['REMOTE_ADDR']
    ]);

    // Establece un mensaje de éxito en la sesión para mostrarlo en la página de informes
    $_SESSION['alert'] = ['type' => 'success', 'message' => 'Documento subido correctamente'];

} catch (Exception $e) {
    // Captura cualquier excepción lanzada durante el proceso y establece un mensaje de error
    Logger::logError('Fallo en el manejador de subida', [
        'error_message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'file_name_attempt' => $_FILES['archivo']['name'] ?? 'N/A',
        'ip' => $_SERVER['REMOTE_ADDR']
    ]);
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Error al procesar el documento: ' . $e->getMessage()];
} finally {
    // Asegura que la conexión a la base de datos se cierre si se abrió y es un objeto mysqli válido
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
    // Redirige siempre al usuario a la página de informes después de procesar la subida
    header("Location: tablainformes.php");
    exit(); // Asegura que el script se detenga después de la redirección
}
?>
