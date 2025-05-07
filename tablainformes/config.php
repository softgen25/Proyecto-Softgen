<?php
// Archivo de Configuración General

// --- Configuración de la Base de Datos ---
// Define tus credenciales de conexión a la base de datos db_softgen
define('DB_SERVERNAME', 'localhost'); // O la IP de tu servidor de base de datos
define('DB_USERNAME', 'root');       // Tu nombre de usuario de la base de datos
define('DB_PASSWORD', '98042662429');   // Tu contraseña de la base de datos (¡Cámbiala por una segura en producción!)
define('DB_NAME', 'db_softgen');     // El nombre de tu base de datos

// --- Configuración de Rutas y Archivos ---
// Define el directorio raíz de tu aplicación (asumiendo que config.php está en un subdirectorio como /tablainformes)
define('ROOT_DIR', realpath(__DIR__ . '/..'));

// Define el directorio donde se guardarán los documentos subidos y las plantillas
// Asegúrate de que este directorio tenga permisos de escritura para el servidor web
define('UPLOAD_DIR', ROOT_DIR . '/documentos');
define('TEMPLATES_DIR', ROOT_DIR . '/formatos'); // Directorio donde guardas tus archivos de plantilla

// URL base para acceder a los documentos desde el navegador
// Asegúrate de que esta URL coincida con la configuración de tu servidor web
// Ejemplo: si tus documentos están en C:\xampp\htdocs\softgen\documentos y tu sitio es http://localhost/softgen/
// define('DOCS_BASE_URL', 'http://localhost/softgen/documentos/');
// Basado en tu estructura de archivos, podría ser:
define('DOCS_BASE_URL', 'http://localhost/tablainformes/documentos/'); // Ajusta si tu estructura es diferente

// URL base para acceder a las plantillas desde el navegador (si es necesario para visualización)
// define('TEMPLATES_BASE_URL', 'http://localhost/softgen/formatos/');
define('TEMPLATES_BASE_URL', 'http://localhost/tablainformes/formatos/'); // Ajusta si tu estructura es diferente


// --- Configuración de Subida de Archivos ---
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // Tamaño máximo de archivo permitido (ej. 10MB)

// Tipos MIME permitidos para la subida de documentos
define('ALLOWED_MIME_TYPES', [
    'application/pdf',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',      // .xlsx
    'text/plain' // Permitir archivos de texto plano si tu editor los maneja
]);

// Extensiones de archivo permitidas
define('ALLOWED_EXTENSIONS', ['pdf', 'docx', 'xlsx', 'txt']);


// --- Configuración de Seguridad ---
// Tiempo de expiración para tokens CSRF (en segundos) - Opcional, pero buena práctica
define('CSRF_TOKEN_EXPIRY', 3600); // 1 hora

// --- Inicialización ---
// Crear el directorio de subida si no existe
if (!file_exists(UPLOAD_DIR)) {
    // Intentar crear el directorio con permisos de lectura y escritura para el propietario y lectura para otros
    if (!mkdir(UPLOAD_DIR, 0755, true)) {
        // Si falla la creación, registrar el error o lanzar una excepción
        error_log("Error: No se pudo crear el directorio de subida: " . UPLOAD_DIR);
        // Dependiendo de la severidad, podrías lanzar una excepción fatal:
        // die("Error fatal: El directorio de subida no puede ser creado.");
    }
}

// Crear el directorio de plantillas si no existe (puede que ya exista si subes plantillas manualmente)
if (!file_exists(TEMPLATES_DIR)) {
     if (!mkdir(TEMPLATES_DIR, 0755, true)) {
        error_log("Warning: No se pudo crear el directorio de plantillas: " . TEMPLATES_DIR);
     }
}

// Incluir el archivo del logger (asegúrate de que la ruta sea correcta)
require_once __DIR__ . '/logger.php';

?>
