<?php
// Script para crear un nuevo documento a partir de una plantilla

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluir archivos necesarios
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/logger.php';

// Asegurarse de que solo se permita el método GET (o POST si envías un formulario)
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    Logger::logError('Acceso no permitido a crear_desde_plantilla.php', ['method' => $_SERVER['REQUEST_METHOD'], 'ip' => $_SERVER['REMOTE_ADDR']]);
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Método no permitido.'];
    header("Location: creacion.html"); // Redirigir a una página segura
    exit();
}

// Validar si se recibió el nombre de la plantilla
if (!isset($_GET['template']) || empty($_GET['template'])) {
    Logger::logError('Nombre de plantilla no especificado en crear_desde_plantilla.php', ['ip' => $_SERVER['REMOTE_ADDR']]);
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'No se especificó la plantilla a utilizar.'];
    header("Location: plantillas.html"); // Redirigir a la página de plantillas
    exit();
}

$nombre_plantilla = basename($_GET['template']); // Obtener solo el nombre del archivo por seguridad
$ruta_plantilla_fisica = TEMPLATES_DIR . DIRECTORY_SEPARATOR . $nombre_plantilla;

// Validar la ruta de la plantilla para prevenir Directory Traversal
$real_templates_dir = realpath(TEMPLATES_DIR);
$real_ruta_plantilla = realpath($ruta_plantilla_fisica);

if ($real_templates_dir === false || $real_ruta_plantilla === false || strpos($real_ruta_plantilla, $real_templates_dir) !== 0) {
     Logger::logError('Intento de Directory Traversal en plantilla detectado', [
        'template_param' => $_GET['template'],
        'ruta_plantilla_fisica' => $ruta_plantilla_fisica,
        'real_templates_dir' => $real_templates_dir ?? 'N/A',
        'real_ruta_plantilla' => $real_ruta_plantilla ?? 'N/A',
        'ip' => $_SERVER['REMOTE_ADDR']
    ]);
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Plantilla no encontrada o ruta inválida.'];
    header("Location: plantillas.html");
    exit();
}


// Verificar si el archivo de plantilla existe
if (!file_exists($ruta_plantilla_fisica)) {
    Logger::logError('Archivo de plantilla no encontrado', ['ruta_plantilla_fisica' => $ruta_plantilla_fisica, 'ip' => $_SERVER['REMOTE_ADDR']]);
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'El archivo de plantilla especificado no existe.'];
    header("Location: plantillas.html");
    exit();
}

// Generar un nombre único para el nuevo documento
$extension = pathinfo($nombre_plantilla, PATHINFO_EXTENSION);
$nombre_nuevo_archivo = uniqid('doc_') . '.' . strtolower($extension);
$ruta_nuevo_archivo_fisica = UPLOAD_DIR . DIRECTORY_SEPARATOR . $nombre_nuevo_archivo;
$ruta_nuevo_archivo_bd = 'documentos/' . $nombre_nuevo_archivo; // Ruta relativa para guardar en BD

// Copiar el contenido de la plantilla al nuevo archivo
if (!copy($ruta_plantilla_fisica, $ruta_nuevo_archivo_fisica)) {
    Logger::logError('Error al copiar el archivo de plantilla', [
        'origen' => $ruta_plantilla_fisica,
        'destino' => $ruta_nuevo_archivo_fisica,
        'php_error' => error_get_last(),
        'ip' => $_SERVER['REMOTE_ADDR']
        ]);
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Error al crear el nuevo documento a partir de la plantilla.'];
    header("Location: plantillas.html");
    exit();
}

// Conectar a la base de datos
$conn = new mysqli(DB_SERVERNAME, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Verificar conexión
if ($conn->connect_error) {
    Logger::logError('Error de conexión a la base de datos al crear documento desde plantilla', ['db_error' => $conn->connect_error, 'ip' => $_SERVER['REMOTE_ADDR']]);
    // Intentar eliminar el archivo copiado si falla la BD
    if (file_exists($ruta_nuevo_archivo_fisica)) {
        unlink($ruta_nuevo_archivo_fisica);
    }
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Error de conexión a la base de datos.'];
    header("Location: plantillas.html");
    exit();
}

// Preparar y ejecutar la inserción en la base de datos
// Asumimos que el nombre del nuevo documento será "Copia de [Nombre Plantilla]" o similar
$nombre_documento = "Copia de " . pathinfo($nombre_plantilla, PATHINFO_FILENAME);
$id_usuario = $_SESSION['id_usuario'] ?? NULL; // Obtener ID del usuario de la sesión (ajusta según tu auth)

$stmt = $conn->prepare("INSERT INTO documentos (nombre, ruta_archivo, fecha_subida, id_usuario) VALUES (?, ?, NOW(), ?)");

if ($stmt === false) {
    Logger::logError('Error al preparar la sentencia SQL de inserción (plantilla)', ['db_error' => $conn->error, 'ip' => $_SERVER['REMOTE_ADDR']]);
     if (file_exists($ruta_nuevo_archivo_fisica)) {
        unlink($ruta_nuevo_archivo_fisica);
    }
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Error interno del servidor al preparar la inserción.'];
    $conn->close();
    header("Location: plantillas.html");
    exit();
}

// 's' para string (nombre, ruta_archivo), 'i' para integer (id_usuario, si no es NULL)
$stmt->bind_param("ssi", $nombre_documento, $ruta_nuevo_archivo_bd, $id_usuario);

if (!$stmt->execute()) {
    Logger::logError('Error al ejecutar la sentencia SQL de inserción (plantilla)', ['db_error' => $stmt->error, 'nombre' => $nombre_documento, 'ruta' => $ruta_nuevo_archivo_bd, 'ip' => $_SERVER['REMOTE_ADDR']]);
    // Eliminar el archivo copiado si falla la inserción
    if (file_exists($ruta_nuevo_archivo_fisica)) {
        unlink($ruta_nuevo_archivo_fisica);
    }
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Error al guardar el nuevo documento en la base de datos.'];
    $stmt->close();
    $conn->close();
    header("Location: plantillas.html");
    exit();
}

// Obtener el ID del documento recién insertado
$nuevo_documento_id = $conn->insert_id;

$stmt->close();
$conn->close();

Logger::logOperation('Documento creado desde plantilla', [
    'nombre_plantilla' => $nombre_plantilla,
    'nuevo_documento_id' => $nuevo_documento_id,
    'id_usuario' => $id_usuario,
    'ip' => $_SERVER['REMOTE_ADDR']
]);

// Redirigir al usuario al editor con el ID del nuevo documento
header("Location: editor.php?id=" . $nuevo_documento_id);
exit();

?>
