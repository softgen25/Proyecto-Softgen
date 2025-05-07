<?php
// Iniciar buffer de salida para evitar errores con headers antes de cualquier salida
ob_start();

// Incluir archivo de configuración
require_once __DIR__ . '/config.php';
// Incluir archivo del logger
require_once __DIR__ . '/logger.php';

// Configuración de sesión segura (asegúrate de que session_start() se llame solo una vez)
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 3600, // 1 hora
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'],
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on', // true si es HTTPS
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    session_start();
}

// --- Lógica de manejo de subida de archivos ---
// Esta lógica se ha movido a upload_handler.php para separar responsabilidades.
// informes.php ahora solo muestra la tabla y el formulario de subida.
// El formulario en tablainformes.php apunta a upload_handler.php.

// Redirigir después de procesar la subida (si se envía un POST a este archivo, aunque el formulario apunta a upload_handler)
// Esto es más una medida de seguridad/limpieza si alguien intenta postear directamente aquí.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
     // Aquí podrías registrar un intento de POST directo si upload_handler es el destino esperado
    Logger::logOperation('INTENTO_POST_DIRECTO_INFORMES', ['ip' => $_SERVER['REMOTE_ADDR']]);
    header("Location: tablainformes.php");
    exit();
}

// --- Lógica para mostrar la página (el HTML se encuentra en tablainformes.php) ---
// Este archivo informes.php parece estar diseñado principalmente para manejar la lógica de POST
// y luego redirigir. El contenido HTML para mostrar la tabla y el formulario está en tablainformes.php.
// Mantendremos este archivo simple, solo para la lógica de POST si fuera necesario
// (aunque el formulario apunta a upload_handler.php).

// Limpiar buffer y redirigir a la página principal de la tabla de informes
ob_end_clean(); // Limpia el buffer de salida sin enviarlo
header("Location: tablainformes.php"); // Redirige al usuario a la página principal
exit(); // Asegura que el script se detenga después de la redirección
?>
